/**
 * sw.js  —  Service Worker
 * ─────────────────────────────────────────────────────────────────
 * Strategy:
 *   - App shell (HTML, CSS, JS, fonts) → Cache First
 *   - API requests                      → Network First, no cache
 *   - Static assets (images, icons)     → Cache First
 *   - Background Sync tag: 'mes-sync'
 * ─────────────────────────────────────────────────────────────────
 */

const CACHE_NAME = 'mes-app-v1';
const API_BASE   = '/api';

// Assets to pre-cache on install
const PRECACHE_URLS = [
  '/',
  '/offline.html',
  '/pwa/offline-db.js',
  '/pwa/data-service.js',
  '/pwa/sync-manager.js',
  '/pwa/pwa-ui.js',
  '/pwa/pwa-boot.js',
];

// ── Helper: only cache http/https requests ────────────────────────
function isCacheable(request) {
  const url = new URL(request.url);
  return url.protocol === 'http:' || url.protocol === 'https:';
}

// ── Install ───────────────────────────────────────────────────────
self.addEventListener('install', (event) => {
  console.log('[SW] Installing...');
  event.waitUntil(
    caches.open(CACHE_NAME).then(cache => {
      return Promise.allSettled(
        PRECACHE_URLS.map(url =>
          cache.add(url).catch(e => console.warn('[SW] Failed to cache:', url, e))
        )
      );
    }).then(() => self.skipWaiting())
  );
});

// ── Activate ──────────────────────────────────────────────────────
self.addEventListener('activate', (event) => {
  console.log('[SW] Activating...');
  event.waitUntil(
    caches.keys()
      .then(keys => Promise.all(
        keys.filter(k => k !== CACHE_NAME).map(k => caches.delete(k))
      ))
      .then(() => self.clients.claim())
  );
});

// ── Fetch ─────────────────────────────────────────────────────────
self.addEventListener('fetch', (event) => {
  const { request } = event;

  // ── Skip non-http(s) entirely (chrome-extension://, etc.) ────────
  if (!isCacheable(request)) return;

  const url = new URL(request.url);

  // ── Skip non-GET — let POST/PUT go straight to network ───────────
  if (request.method !== 'GET') return;

  // ── API requests — network only, never cache ─────────────────────
  if (url.pathname.startsWith(API_BASE)) {
    event.respondWith(
      fetch(request).catch(() =>
        new Response(
          JSON.stringify({ error: 'offline', message: 'No network connection' }),
          { status: 503, headers: { 'Content-Type': 'application/json' } }
        )
      )
    );
    return;
  }

  // ── Navigation (HTML pages) — network first, fallback to cache ───
  if (request.mode === 'navigate') {
    event.respondWith(
      fetch(request)
        .then(response => {
          if (response.ok) {
            const clone = response.clone();
            caches.open(CACHE_NAME).then(cache => cache.put(request, clone));
          }
          return response;
        })
        .catch(() =>
          caches.match(request)
            .then(cached => cached || caches.match('/offline.html'))
        )
    );
    return;
  }

  // ── Static assets — cache first, fallback to network ─────────────
  event.respondWith(
    caches.match(request).then(cached => {
      if (cached) return cached;

      return fetch(request).then(response => {
        if (response.ok && isCacheable(request)) {
          const clone = response.clone();
          caches.open(CACHE_NAME).then(cache => cache.put(request, clone));
        }
        return response;
      }).catch(() => new Response('', { status: 404 }));
    })
  );
});

// ── Background Sync ───────────────────────────────────────────────
self.addEventListener('sync', (event) => {
  if (event.tag === 'mes-sync') {
    console.log('[SW] Background sync triggered');
    event.waitUntil(
      self.clients.matchAll().then(clients => {
        clients.forEach(client => client.postMessage({ type: 'BACKGROUND_SYNC' }));
      })
    );
  }
});

// ── Push Notifications (future) ───────────────────────────────────
self.addEventListener('push', (event) => {
  if (!event.data) return;
  const data = event.data.json();
  event.waitUntil(
    self.registration.showNotification(data.title || 'MES Update', {
      body : data.body || '',
      icon : '/icons/icon-192.png',
      badge: '/icons/badge-72.png',
      data,
    })
  );
});

self.addEventListener('notificationclick', (event) => {
  event.notification.close();
  event.waitUntil(self.clients.openWindow(event.notification.data?.url || '/'));
});

console.log('[SW] Service Worker loaded');