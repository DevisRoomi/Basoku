// Service Worker Minimalis BASOKU — Offline Shell Cache
const CACHE_NAME = 'basoku-shell-v2';
const ASSETS_TO_CACHE = [
  '/',
  '/manifest.json',
  '/favicon.svg'
];

const BYPASS_PREFIXES = [
  '/livewire',
  '/admin',
  '/masuk',
  '/daftar',
  '/keluar',
  '/racikan-saya',
  '/pesanan-saya',
  '/lacak-pesanan'
];

self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => {
      return cache.addAll(ASSETS_TO_CACHE);
    }).then(() => self.skipWaiting())
  );
});

self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((keys) => {
      return Promise.all(
        keys.map((key) => {
          if (key !== CACHE_NAME) {
            return caches.delete(key);
          }
        })
      );
    }).then(() => self.clients.claim())
  );
});

// Network-first with safe cache fallback for public/static GET requests
self.addEventListener('fetch', (event) => {
  if (event.request.method !== 'GET') return;

  const url = new URL(event.request.url);
  if (url.origin !== self.location.origin) return;

  if (BYPASS_PREFIXES.some((prefix) => url.pathname.startsWith(prefix))) {
    return;
  }

  event.respondWith(
    fetch(event.request)
      .then((response) => {
        if (response.status === 200 && response.type === 'basic') {
          const responseClone = response.clone();
          caches.open(CACHE_NAME).then((cache) => {
            cache.put(event.request, responseClone);
          });
        }
        return response;
      })
      .catch(() => {
        return caches.match(event.request).then((cachedResponse) => {
          if (cachedResponse) {
            return cachedResponse;
          }
          if (event.request.headers.get('accept')?.includes('text/html')) {
            return caches.match('/');
          }
        });
      })
  );
});
