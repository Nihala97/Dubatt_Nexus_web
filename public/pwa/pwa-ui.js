/**
 * pwa-ui.js
 * ─────────────────────────────────────────────────────────────────
 * All UI feedback for offline/sync state:
 *   - Offline banner
 *   - Online restored banner
 *   - Syncing indicator (top bar)
 *   - Toast notifications
 *   - Pending badge on nav items
 * ─────────────────────────────────────────────────────────────────
 */

import { offlineDB } from './offline-db.js';

class PwaUI {
  constructor() {
    this._injected = false;
  }

  // ── Inject base styles + containers ──────────────────────────────
  _inject() {
    if (this._injected) return;
    this._injected = true;

    const style = document.createElement('style');
    style.textContent = `
      /* ── Offline Banner ── */
      #pwa-offline-banner {
        position: fixed; top: 0; left: 0; right: 0; z-index: 9999;
        background: #1e293b; color: #f1f5f9;
        padding: 10px 20px;
        display: flex; align-items: center; justify-content: space-between; gap: 12px;
        font-family: 'Outfit', sans-serif; font-size: 13.5px; font-weight: 500;
        transform: translateY(-100%);
        transition: transform 0.35s cubic-bezier(0.4,0,0.2,1);
        box-shadow: 0 4px 20px rgba(0,0,0,0.3);
      }
      #pwa-offline-banner.visible { transform: translateY(0); }
      #pwa-offline-banner .banner-left { display:flex; align-items:center; gap:10px; }
      #pwa-offline-banner .banner-dot {
        width:8px; height:8px; border-radius:50%; background:#f87171;
        animation: pulse-red 1.5s infinite;
      }
      @keyframes pulse-red {
        0%,100% { box-shadow: 0 0 0 0 rgba(248,113,113,0.4); }
        50%      { box-shadow: 0 0 0 6px rgba(248,113,113,0); }
      }

      /* ── Online Restored Banner ── */
      #pwa-online-banner {
        position: fixed; top: 0; left: 0; right: 0; z-index: 9999;
        background: #064e3b; color: #d1fae5;
        padding: 10px 20px;
        display: flex; align-items: center; gap: 10px;
        font-family: 'Outfit', sans-serif; font-size: 13.5px; font-weight: 500;
        transform: translateY(-100%);
        transition: transform 0.35s cubic-bezier(0.4,0,0.2,1);
      }
      #pwa-online-banner.visible { transform: translateY(0); }
      #pwa-online-banner .banner-dot {
        width:8px; height:8px; border-radius:50%; background:#34d399;
        animation: pulse-green 1.5s infinite;
      }
      @keyframes pulse-green {
        0%,100% { box-shadow: 0 0 0 0 rgba(52,211,153,0.4); }
        50%      { box-shadow: 0 0 0 6px rgba(52,211,153,0); }
      }

      /* ── Sync Progress Bar ── */
      #pwa-sync-bar {
        position: fixed; top: 0; left: 0; right: 0; z-index: 9998;
        height: 3px; background: #bbf7d0;
        transform: scaleX(0); transform-origin: left;
        transition: transform 0.4s ease;
      }
      #pwa-sync-bar.active { animation: sync-progress 1.8s ease-in-out infinite; }
      @keyframes sync-progress {
        0%   { transform: scaleX(0);   transform-origin: left; }
        50%  { transform: scaleX(0.7); transform-origin: left; }
        100% { transform: scaleX(1);   transform-origin: left; opacity: 0; }
      }

      /* ── Syncing Pill ── */
      #pwa-sync-pill {
        position: fixed; bottom: 80px; right: 20px; z-index: 9997;
        background: #1a7a3a; color: #fff;
        padding: 8px 14px; border-radius: 20px;
        font-family: 'Outfit', sans-serif; font-size: 12.5px; font-weight: 600;
        display: flex; align-items: center; gap: 8px;
        box-shadow: 0 4px 16px rgba(26,122,58,0.35);
        opacity: 0; transform: translateY(10px);
        transition: opacity 0.25s, transform 0.25s;
        pointer-events: none;
      }
      #pwa-sync-pill.visible { opacity: 1; transform: translateY(0); }
      #pwa-sync-pill .spin {
        width: 13px; height: 13px;
        border: 2px solid rgba(255,255,255,0.3);
        border-top-color: #fff;
        border-radius: 50%;
        animation: spin 0.7s linear infinite;
      }
      @keyframes spin { to { transform: rotate(360deg); } }

      /* ── Toast Notifications ── */
      #pwa-toast-container {
        position: fixed; bottom: 24px; left: 50%; transform: translateX(-50%);
        z-index: 10000;
        display: flex; flex-direction: column; gap: 8px; align-items: center;
        pointer-events: none;
      }
      .pwa-toast {
        padding: 11px 18px; border-radius: 10px;
        font-family: 'Outfit', sans-serif; font-size: 13.5px; font-weight: 500;
        box-shadow: 0 6px 24px rgba(0,0,0,0.15);
        animation: toast-in 0.3s ease-out forwards;
        max-width: 380px; text-align: center; line-height: 1.4;
        white-space: nowrap;
      }
      .pwa-toast.success { background: #065f46; color: #d1fae5; }
      .pwa-toast.error   { background: #991b1b; color: #fee2e2; }
      .pwa-toast.warning { background: #92400e; color: #fef3c7; }
      .pwa-toast.info    { background: #1e3a5f; color: #dbeafe; }
      .pwa-toast.out     { animation: toast-out 0.3s ease-in forwards; }
      @keyframes toast-in  { from { opacity:0; transform:translateY(16px); } to { opacity:1; transform:translateY(0); } }
      @keyframes toast-out { from { opacity:1; transform:translateY(0); }  to { opacity:0; transform:translateY(8px); } }

      /* ── Pending Badge ── */
      .pwa-pending-badge {
        display: inline-flex; align-items: center; justify-content: center;
        background: #f59e0b; color: #fff;
        font-size: 10px; font-weight: 700;
        width: 18px; height: 18px; border-radius: 50%;
        margin-left: 6px; vertical-align: middle;
        animation: badge-pop 0.3s cubic-bezier(0.34,1.56,0.64,1);
      }
      @keyframes badge-pop { from { transform: scale(0); } to { transform: scale(1); } }

      /* ── Sync Status Panel ── */
      #pwa-status-panel {
        position: fixed; bottom: 80px; right: 20px; z-index: 9996;
        background: #fff; border: 1px solid #dde8e2; border-radius: 14px;
        width: 300px; box-shadow: 0 8px 32px rgba(0,0,0,0.12);
        font-family: 'Outfit', sans-serif;
        transform: translateY(10px) scale(0.97);
        opacity: 0; pointer-events: none;
        transition: all 0.2s cubic-bezier(0.4,0,0.2,1);
      }
      #pwa-status-panel.open { transform: translateY(0) scale(1); opacity: 1; pointer-events: auto; }
      .sp-head { padding: 14px 16px; background: #e8f5ed; border-radius: 14px 14px 0 0;
                 border-bottom: 1px solid #dde8e2; display:flex; align-items:center; justify-content:space-between; }
      .sp-head h4 { font-size: 13px; font-weight: 700; color: #1a7a3a; }
      .sp-close { background: none; border: none; cursor: pointer; color: #6b8a78; font-size: 18px; line-height:1; }
      .sp-body { padding: 14px 16px; }
      .sp-row { display:flex; justify-content:space-between; align-items:center;
                font-size: 13px; color: #3d5449; padding: 5px 0; border-bottom: 1px solid #f0f4f2; }
      .sp-row:last-child { border-bottom: none; }
      .sp-count { font-weight: 700; }
      .sp-count.pending { color: #d97706; }
      .sp-count.failed  { color: #dc2626; }
      .sp-count.synced  { color: #1a7a3a; }
      .sp-actions { padding: 12px 16px; border-top: 1px solid #dde8e2; display:flex; gap:8px; }
      .sp-btn { flex:1; padding: 8px 12px; border-radius: 8px; font-family:'Outfit',sans-serif;
                font-size: 12.5px; font-weight: 600; cursor: pointer; border: none; transition: all 0.2s; }
      .sp-btn.primary { background: #1a7a3a; color: #fff; }
      .sp-btn.primary:hover { background: #145f2d; }
      .sp-btn.outline { background: #fff; color: #3d5449; border: 1.5px solid #dde8e2; }
      .sp-btn.outline:hover { border-color: #1a7a3a; color: #1a7a3a; }

      /* ── FAB Sync Button ── */
      #pwa-fab {
        position: fixed; bottom: 20px; right: 20px; z-index: 9995;
        width: 52px; height: 52px; border-radius: 50%;
        background: #1a7a3a; color: #fff; border: none; cursor: pointer;
        display: flex; align-items: center; justify-content: center;
        box-shadow: 0 4px 16px rgba(26,122,58,0.4);
        transition: all 0.2s;
      }
      #pwa-fab:hover { background: #145f2d; transform: scale(1.08); }
      #pwa-fab svg { width: 22px; height: 22px; stroke: currentColor; fill: none; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; }
      #pwa-fab .fab-badge {
        position: absolute; top: -4px; right: -4px;
        background: #f59e0b; color: #fff;
        font-size: 10px; font-weight: 700;
        min-width: 18px; height: 18px; border-radius: 9px; padding: 0 4px;
        display: flex; align-items: center; justify-content: center;
        border: 2px solid #fff;
      }

      /* push body down when banner visible */
      body.pwa-offline-push { padding-top: 42px !important; }
    `;
    document.head.appendChild(style);

    // Offline banner
    const offlineBanner = document.createElement('div');
    offlineBanner.id = 'pwa-offline-banner';
    offlineBanner.innerHTML = `
      <div class="banner-left">
        <div class="banner-dot"></div>
        <span>You're offline — changes are saved locally and will sync when reconnected</span>
      </div>
    `;
    document.body.prepend(offlineBanner);

    // Online banner
    const onlineBanner = document.createElement('div');
    onlineBanner.id = 'pwa-online-banner';
    onlineBanner.innerHTML = `
      <div class="banner-dot"></div>
      <span>Back online — syncing your data...</span>
    `;
    document.body.prepend(onlineBanner);

    // Sync bar
    const syncBar = document.createElement('div');
    syncBar.id = 'pwa-sync-bar';
    document.body.prepend(syncBar);

    // Toast container
    const toastContainer = document.createElement('div');
    toastContainer.id = 'pwa-toast-container';
    document.body.appendChild(toastContainer);

    // Sync pill
    const pill = document.createElement('div');
    pill.id = 'pwa-sync-pill';
    pill.innerHTML = `<div class="spin"></div><span id="pwa-sync-pill-text">Syncing...</span>`;
    document.body.appendChild(pill);

    // FAB
    const fab = document.createElement('button');
    fab.id = 'pwa-fab';
    fab.title = 'Sync Status';
    fab.innerHTML = `
      <svg viewBox="0 0 24 24"><polyline points="1 4 1 10 7 10"/><polyline points="23 20 23 14 17 14"/>
      <path d="M20.49 9A9 9 0 0 0 5.64 5.64L1 10M23 14l-4.64 4.36A9 9 0 0 1 3.51 15"/></svg>
      <div class="fab-badge" id="pwa-fab-badge" style="display:none">0</div>
    `;
    fab.addEventListener('click', () => this.toggleStatusPanel());
    document.body.appendChild(fab);

    // Status panel
    const panel = document.createElement('div');
    panel.id = 'pwa-status-panel';
    panel.innerHTML = `
      <div class="sp-head">
        <h4>⚡ Sync Status</h4>
        <button class="sp-close" onclick="pwaUI.toggleStatusPanel()">×</button>
      </div>
      <div class="sp-body">
        <div class="sp-row"><span>Pending sync</span><span class="sp-count pending" id="sp-pending">—</span></div>
        <div class="sp-row"><span>Failed</span><span class="sp-count failed" id="sp-failed">—</span></div>
        <div class="sp-row"><span>Synced</span><span class="sp-count synced" id="sp-synced">—</span></div>
        <div class="sp-row"><span>Total local</span><span class="sp-count" id="sp-total">—</span></div>
      </div>
      <div class="sp-actions">
        <button class="sp-btn primary" onclick="pwaUI.triggerSync()">Sync Now</button>
        <button class="sp-btn outline" onclick="pwaUI.retryFailed()">Retry Failed</button>
      </div>
    `;
    document.body.appendChild(panel);

    // Initial network state
    if (!navigator.onLine) this.showOfflineBanner();

    // Refresh stats every 10s
    setInterval(() => this.refreshStats(), 10_000);
    this.refreshStats();
  }

  // ── Offline / Online banners ──────────────────────────────────────
  showOfflineBanner() {
    document.getElementById('pwa-offline-banner')?.classList.add('visible');
    document.getElementById('pwa-online-banner')?.classList.remove('visible');
    document.body.classList.add('pwa-offline-push');
  }

  showOnlineBanner() {
    document.getElementById('pwa-offline-banner')?.classList.remove('visible');
    document.body.classList.remove('pwa-offline-push');
    const ob = document.getElementById('pwa-online-banner');
    ob?.classList.add('visible');
    setTimeout(() => ob?.classList.remove('visible'), 4000);
  }

  // ── Syncing indicator ─────────────────────────────────────────────
  showSyncingIndicator(count) {
    document.getElementById('pwa-sync-bar')?.classList.add('active');
    const pill = document.getElementById('pwa-sync-pill');
    const text = document.getElementById('pwa-sync-pill-text');
    if (text) text.textContent = `Syncing ${count} record${count > 1 ? 's' : ''}...`;
    pill?.classList.add('visible');
  }

  hideSyncingIndicator() {
    document.getElementById('pwa-sync-bar')?.classList.remove('active');
    document.getElementById('pwa-sync-pill')?.classList.remove('visible');
    this.refreshStats();
  }

  // ── Toast ─────────────────────────────────────────────────────────
  toast(message, type = 'info', duration = 4000) {
    const container = document.getElementById('pwa-toast-container');
    if (!container) return;

    const el = document.createElement('div');
    el.className = `pwa-toast ${type}`;
    el.textContent = message;
    container.appendChild(el);

    setTimeout(() => {
      el.classList.add('out');
      setTimeout(() => el.remove(), 300);
    }, duration);
  }

  // ── Status panel ──────────────────────────────────────────────────
  toggleStatusPanel() {
    document.getElementById('pwa-status-panel')?.classList.toggle('open');
  }

  async refreshStats() {
    const stats = await offlineDB.getStats();
    const set = (id, val) => {
      const el = document.getElementById(id);
      if (el) el.textContent = val;
    };
    set('sp-pending', stats.pending);
    set('sp-failed',  stats.failed);
    set('sp-synced',  stats.synced);
    set('sp-total',   stats.total);

    const badge = document.getElementById('pwa-fab-badge');
    if (badge) {
      const count = stats.pending + stats.failed;
      badge.textContent = count;
      badge.style.display = count > 0 ? 'flex' : 'none';
    }
  }

  // ── Trigger sync from panel ───────────────────────────────────────
  async triggerSync() {
    const { syncManager } = await import('./sync-manager.js');
    await syncManager.sync();
    this.toggleStatusPanel();
  }

  async retryFailed() {
    const { syncManager } = await import('./sync-manager.js');
    await syncManager.retryFailed();
    this.toggleStatusPanel();
  }
}

const pwaUI = new PwaUI();

// Auto-inject on DOM ready
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', () => pwaUI._inject());
} else {
  pwaUI._inject();
}

// Expose globally so blade templates can access it
window.pwaUI = pwaUI;

export { pwaUI };