/**
 * pwa-boot.js
 * ─────────────────────────────────────────────────────────────────
 * Include this ONE script in your admin.layouts.app blade layout.
 *
 *   <script type="module" src="{{ asset('pwa/pwa-boot.js') }}"></script>
 * ─────────────────────────────────────────────────────────────────
 */

import { pwaUI }       from './pwa-ui.js';
import { syncManager } from './sync-manager.js';

// ── Helper: wait until SW is fully active ────────────────────────
function waitForActivation(reg) {
  return new Promise((resolve) => {
    if (reg.active) { resolve(reg); return; }
    const sw = reg.installing || reg.waiting;
    if (!sw) { resolve(reg); return; }
    sw.addEventListener('statechange', function onState() {
      if (this.state === 'activated') {
        sw.removeEventListener('statechange', onState);
        resolve(reg);
      }
    });
  });
}

// ── Register Service Worker ───────────────────────────────────────
async function registerSW() {
  if (!('serviceWorker' in navigator)) {
    console.warn('[PWA] Service Workers not supported');
    return;
  }

  try {
    const reg = await navigator.serviceWorker.register('/sw.js', { scope: '/' });
    console.log('[PWA] Service Worker registered:', reg.scope);

    // Listen for SW messages
    navigator.serviceWorker.addEventListener('message', (event) => {
      if (event.data?.type === 'BACKGROUND_SYNC') {
        syncManager.sync();
      }
    });

    // Must wait for SW to be active before calling reg.sync.register()
    // otherwise throws InvalidStateError
    if ('sync' in reg) {
      await waitForActivation(reg);
      try {
        await reg.sync.register('mes-sync');
        console.log('[PWA] Background sync registered');
      } catch (e) {
        console.warn('[PWA] Background sync skipped:', e.message);
      }
    }

  } catch (err) {
    console.error('[PWA] SW registration failed:', err);
  }
}

// ── Boot ──────────────────────────────────────────────────────────
async function boot() {
  await registerSW();
  syncManager.init();

  window.addEventListener('mes:syncComplete', (e) => {
    if (e.detail.successCount > 0 && typeof window.loadData === 'function') {
      window.loadData();
    }
  });

  console.log('[PWA] Boot complete');
}

boot();