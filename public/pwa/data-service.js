/**
 * data-service.js
 * ─────────────────────────────────────────────────────────────────
 * The single source of truth for all form saves.
 * 
 * Flow:
 *   1. Always write to IndexedDB first
 *   2. If online  → immediately POST/PUT to API → mark synced
 *   3. If offline → mark pending, enqueue for later sync
 * 
 * Usage (in your blade form JS):
 *   import { DataService } from './data-service.js';
 *   const ds = new DataService('receiving');
 *   await ds.save(formData);
 * ─────────────────────────────────────────────────────────────────
 */

import { offlineDB, MODULES, SYNC_STATUS } from './offline-db.js';

// API endpoint map per module
const MODULE_ENDPOINTS = {
  [MODULES.RECEIVING]    : '/api/receivings',
  [MODULES.ACID_TESTING] : '/api/acid-testings',
  [MODULES.BBSU]         : '/api/bbsu-logs',
};

// ── API fetch with auth token ──────────────────────────────────────
async function apiFetch(endpoint, options = {}) {
  const token = document.querySelector('meta[name="api-token"]')?.content
             || localStorage.getItem('auth_token')
             || '';

  const res = await fetch(endpoint, {
    ...options,
    headers: {
      'Content-Type' : 'application/json',
      'Accept'       : 'application/json',
      'Authorization': `Bearer ${token}`,
      ...(options.headers || {}),
    },
  });
  return res;
}

class DataService {
  /**
   * @param {string} module - One of MODULES.*
   */
  constructor(module) {
    if (!Object.values(MODULES).includes(module)) {
      throw new Error(`Unknown module: ${module}`);
    }
    this.module   = module;
    this.endpoint = MODULE_ENDPOINTS[module];
  }

  // ── Save (Create or Update) ──────────────────────────────────────
  /**
   * @param {object} formData  - Raw form fields
   * @param {string} local_id  - If editing an existing local record
   * @param {string} server_id - If editing a server-synced record
   */
  async save(formData, local_id = null, server_id = null) {
    const isUpdate = !!(local_id && server_id);

    // 1. Write to IndexedDB immediately
    const record = await offlineDB.saveRecord(
      this.module,
      { ...formData, local_id: local_id || undefined },
      server_id || null
    );

    // 2. Try to sync with server if online
    if (navigator.onLine) {
      try {
        const method   = isUpdate ? 'PUT' : 'POST';
        const url      = isUpdate ? `${this.endpoint}/${server_id}` : this.endpoint;
        const response = await apiFetch(url, {
          method,
          body: JSON.stringify(formData),
        });

        if (response.ok) {
          const json      = await response.json();
          const newServerId = json.data?.id || server_id;

          // Mark as synced in IndexedDB
          await offlineDB.updateRecordSyncStatus(
            record.local_id,
            SYNC_STATUS.SYNCED,
            newServerId
          );

          return {
            success   : true,
            synced    : true,
            local_id  : record.local_id,
            server_id : newServerId,
            data      : json.data,
          };

        } else if (response.status === 422) {
          // Validation error from server - keep in IndexedDB as failed
          const json = await response.json();
          await offlineDB.updateRecordSyncStatus(
            record.local_id,
            SYNC_STATUS.FAILED,
            null,
            json.message || 'Validation failed'
          );

          return {
            success          : false,
            synced           : false,
            validation_error : true,
            errors           : json.errors || {},
            message          : json.message || 'Validation failed',
            local_id         : record.local_id,
          };

        } else {
          // Server error - mark pending, will retry
          await offlineDB.updateRecordSyncStatus(
            record.local_id,
            SYNC_STATUS.PENDING
          );
          await offlineDB.enqueue(
            this.module,
            record.local_id,
            isUpdate ? 'PUT' : 'POST',
            isUpdate ? `${this.endpoint}/${server_id}` : this.endpoint,
            formData
          );

          return {
            success  : true,
            synced   : false,
            offline  : false,
            queued   : true,
            local_id : record.local_id,
            message  : 'Saved locally. Will sync when server is available.',
          };
        }

      } catch (networkError) {
        // Network failure even though navigator.onLine was true
        await offlineDB.updateRecordSyncStatus(record.local_id, SYNC_STATUS.PENDING);
        await offlineDB.enqueue(
          this.module,
          record.local_id,
          isUpdate ? 'PUT' : 'POST',
          isUpdate ? `${this.endpoint}/${server_id}` : this.endpoint,
          formData
        );

        return {
          success  : true,
          synced   : false,
          offline  : true,
          queued   : true,
          local_id : record.local_id,
          message  : 'Saved offline. Will sync when connected.',
        };
      }

    } else {
      // Offline - enqueue for background sync
      await offlineDB.enqueue(
        this.module,
        record.local_id,
        isUpdate ? 'PUT' : 'POST',
        isUpdate ? `${this.endpoint}/${server_id}` : this.endpoint,
        formData
      );

      return {
        success  : true,
        synced   : false,
        offline  : true,
        queued   : true,
        local_id : record.local_id,
        message  : 'Saved offline. Will sync when connected.',
      };
    }
  }

  // ── Load List (API first, fallback to IndexedDB) ─────────────────
  async getList(filters = {}) {
    if (navigator.onLine) {
      try {
        let url = this.endpoint + '?per_page=100';
        Object.entries(filters).forEach(([k, v]) => {
          if (v !== '' && v !== null && v !== undefined) {
            url += `&${k}=${encodeURIComponent(v)}`;
          }
        });

        const res = await apiFetch(url);
        if (res.ok) {
          const json = await res.json();
          const items = json.data?.data || json.data || [];

          // Merge with local pending records
          const localPending = await offlineDB.getPendingRecords();
          const modulePending = localPending.filter(r => r.module === this.module);

          return {
            source  : 'api',
            items,
            pending : modulePending,
            total   : json.data?.total || items.length,
          };
        }
      } catch (e) {
        console.warn('[DataService] API failed, falling back to IndexedDB', e);
      }
    }

    // Fallback: IndexedDB
    const localItems = await offlineDB.getRecordsByModule(this.module);
    return {
      source  : 'local',
      items   : localItems.filter(r => r.sync_status === SYNC_STATUS.SYNCED),
      pending : localItems.filter(r => r.sync_status === SYNC_STATUS.PENDING),
      failed  : localItems.filter(r => r.sync_status === SYNC_STATUS.FAILED),
      total   : localItems.length,
    };
  }

  // ── Get Single Record ─────────────────────────────────────────────
  async getOne(server_id, local_id = null) {
    if (navigator.onLine && server_id) {
      try {
        const res = await apiFetch(`${this.endpoint}/${server_id}`);
        if (res.ok) {
          const json = await res.json();
          return { source: 'api', data: json.data };
        }
      } catch (e) {
        console.warn('[DataService] API failed, falling back to IndexedDB');
      }
    }

    if (local_id) {
      const record = await offlineDB.getRecord(local_id);
      return record ? { source: 'local', data: record } : null;
    }

    return null;
  }

  // ── Cache dropdowns / prefill data ───────────────────────────────
  async getCachedOrFetch(cacheKey, apiUrl, ttl_minutes = 120) {
    // Try cache first
    const cached = await offlineDB.getCachedList(cacheKey);
    if (cached) return { source: 'cache', data: cached };

    if (navigator.onLine) {
      try {
        const res = await apiFetch(apiUrl);
        if (res.ok) {
          const json = await res.json();
          const data = json.data?.data || json.data || json;
          await offlineDB.cacheList(cacheKey, data, ttl_minutes);
          return { source: 'api', data };
        }
      } catch (e) {
        console.warn('[DataService] Could not fetch:', apiUrl);
      }
    }

    return { source: 'none', data: [] };
  }
}

export { DataService, apiFetch };