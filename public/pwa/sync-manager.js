/**
 * sync-manager.js
 * ─────────────────────────────────────────────────────────────────
 * Listens for connectivity → flushes pending sync queue
 * Handles failures → marks as failed, notifies user
 * ─────────────────────────────────────────────────────────────────
 */

import { offlineDB, SYNC_STATUS } from './offline-db.js';
import { pwaUI } from './pwa-ui.js';

const API_BASE = '/api';

class SyncManager {
  constructor() {
    this.isSyncing   = false;
    this.listeners   = [];
  }

  // ── Init ─────────────────────────────────────────────────────────
  init() {
    // Sync when coming back online
    window.addEventListener('online',  () => this._onOnline());
    window.addEventListener('offline', () => this._onOffline());

    // Periodic sync check every 30s (in case online event missed)
    setInterval(() => {
      if (navigator.onLine && !this.isSyncing) this.sync();
    }, 30_000);

    // Initial sync attempt on page load
    if (navigator.onLine) {
      setTimeout(() => this.sync(), 2000);
    }

    console.log('[SyncManager] Initialized');
  }

  // ── Event handlers ────────────────────────────────────────────────
  _onOnline() {
    pwaUI.showOnlineBanner();
    this.sync();
  }

  _onOffline() {
    pwaUI.showOfflineBanner();
  }

  // ── Core sync loop ────────────────────────────────────────────────
  async sync() {
    if (this.isSyncing || !navigator.onLine) return;

    const queue = await offlineDB.getPendingQueue();
    if (queue.length === 0) return;

    this.isSyncing = true;
    pwaUI.showSyncingIndicator(queue.length);

    let successCount = 0;
    let failCount    = 0;
    const failedItems = [];

    for (const job of queue) {
      const result = await this._processJob(job);
      if (result.success) {
        successCount++;
      } else {
        failCount++;
        failedItems.push({ job, error: result.error });
      }
    }

    this.isSyncing = false;
    pwaUI.hideSyncingIndicator();

    // Notify results
    if (successCount > 0 && failCount === 0) {
      pwaUI.toast(
        `✓ ${successCount} record${successCount > 1 ? 's' : ''} synced successfully`,
        'success'
      );
    } else if (successCount > 0 && failCount > 0) {
      pwaUI.toast(
        `✓ ${successCount} synced · ✗ ${failCount} failed — check Sync Status`,
        'warning'
      );
      this._notifyFailures(failedItems);
    } else if (failCount > 0) {
      pwaUI.toast(
        `✗ ${failCount} record${failCount > 1 ? 's' : ''} failed to sync`,
        'error'
      );
      this._notifyFailures(failedItems);
    }

    // Emit event so index pages can refresh
    this._emit('syncComplete', { successCount, failCount });
  }

  // ── Process a single queue job ─────────────────────────────────────
  async _processJob(job) {
    // Mark as syncing
    await offlineDB.updateQueueJob(job.queue_id, {
      sync_status  : SYNC_STATUS.SYNCING,
      last_attempt : new Date().toISOString(),
      attempts     : (job.attempts || 0) + 1,
    });

    try {
      const token = localStorage.getItem('auth_token') || '';

      const res = await fetch(`${API_BASE}${job.endpoint.replace('/api','')}`, {
        method  : job.method,
        headers : {
          'Content-Type'  : 'application/json',
          'Accept'        : 'application/json',
          'Authorization' : `Bearer ${token}`,
        },
        body: JSON.stringify(job.payload),
      });

      if (res.ok) {
        const json = await res.json();
        const server_id = json.data?.id;

        // Remove from queue
        await offlineDB.removeQueueJob(job.queue_id);

        // Update record as synced
        await offlineDB.updateRecordSyncStatus(
          job.local_id,
          SYNC_STATUS.SYNCED,
          server_id
        );

        return { success: true, server_id };

      } else if (res.status === 422) {
        // Validation failed — keep in IndexedDB as failed, don't retry
        const json = await res.json();
        const errorMsg = json.message || 'Validation error from server';

        await offlineDB.updateQueueJob(job.queue_id, {
          sync_status : SYNC_STATUS.FAILED,
          error       : errorMsg,
        });

        await offlineDB.updateRecordSyncStatus(
          job.local_id,
          SYNC_STATUS.FAILED,
          null,
          errorMsg
        );

        return { success: false, error: errorMsg, validation: true };

      } else if ((job.attempts || 0) >= job.max_attempts) {
        // Max retries reached
        const errorMsg = `Server error ${res.status} after ${job.max_attempts} attempts`;

        await offlineDB.updateQueueJob(job.queue_id, {
          sync_status : SYNC_STATUS.FAILED,
          error       : errorMsg,
        });

        await offlineDB.updateRecordSyncStatus(
          job.local_id,
          SYNC_STATUS.FAILED,
          null,
          errorMsg
        );

        return { success: false, error: errorMsg };

      } else {
        // Will retry next sync cycle
        await offlineDB.updateQueueJob(job.queue_id, {
          sync_status : SYNC_STATUS.PENDING,
          error       : `Server error ${res.status}, will retry`,
        });

        return { success: false, error: `Server error ${res.status}`, retry: true };
      }

    } catch (networkError) {
      // Network error during sync
      await offlineDB.updateQueueJob(job.queue_id, {
        sync_status : SYNC_STATUS.PENDING,
        error       : networkError.message,
      });

      return { success: false, error: networkError.message, retry: true };
    }
  }

  // ── Notify user of failed records ─────────────────────────────────
  _notifyFailures(failedItems) {
    failedItems.forEach(({ job, error }) => {
      console.warn(`[SyncManager] Failed job for module=${job.module}:`, error);
    });

    // Dispatch event so pages can show failed record indicators
    this._emit('syncFailed', { items: failedItems });
  }

  // ── Event emitter ─────────────────────────────────────────────────
  on(event, fn) {
    this.listeners.push({ event, fn });
    return this; // chainable
  }

  _emit(event, data) {
    this.listeners
      .filter(l => l.event === event)
      .forEach(l => l.fn(data));

    // Also dispatch DOM event for blade pages
    window.dispatchEvent(new CustomEvent(`mes:${event}`, { detail: data }));
  }

  // ── Manual retry of failed records ───────────────────────────────
  async retryFailed() {
    const failed = await offlineDB.getFailedRecords();
    for (const record of failed) {
      await offlineDB.updateRecordSyncStatus(record.local_id, SYNC_STATUS.PENDING);
    }
    // Reset queue jobs too
    const queue = await offlineDB.getPendingQueue();
    for (const job of queue) {
      if (job.sync_status === SYNC_STATUS.FAILED) {
        await offlineDB.updateQueueJob(job.queue_id, {
          sync_status : SYNC_STATUS.PENDING,
          attempts    : 0,
          error       : null,
        });
      }
    }
    await this.sync();
  }

  // ── Stats for UI ──────────────────────────────────────────────────
  async getStats() {
    return offlineDB.getStats();
  }
}

const syncManager = new SyncManager();
export { syncManager };