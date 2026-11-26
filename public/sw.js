// Service Worker Minimalis BASOKU — Static Asset Cache Only (Aman untuk Sesi Login)
const CACHE_NAME = 'basoku-static-v3';
const ASSETS_TO_CACHE = [
  '/manifest.json',
  '/favicon.svg'
];

const STATIC_PATH_PREFIXES = [
  '/build/',
  '/images/',
  '/manifest.json',
  '/favicon.svg'
];

const STATIC_EXTENSIONS = [
  '.css',
  '.js',
  '.svg',
  '.png',
  '.jpg',
  '.jpeg',
  '.webp',
  '.woff2',
  '.woff',
  '.ico'
];

function isCacheableStaticRequest(request, url) {
  if (request.method !== 'GET') return false;
  if (url.origin !== self.location.origin) return false;
  if (request.mode === 'navigate') return false;
  if (request.headers.get('accept')?.includes('text/html')) return false;

  if (STATIC_PATH_PREFIXES.some((prefix) => url.pathname.startsWith(prefix))) {
    return true;
  }

  return STATIC_EXTENSIONS.some((ext) => url.pathname.toLowerCase().endsWith(ext));
}

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

// Hanya cache aset statis publik agar halaman HTML terautentikasi tidak pernah tersimpan di CacheStorage
self.addEventListener('fetch', (event) => {
  const url = new URL(event.request.url);
  if (!isCacheableStaticRequest(event.request, url)) {
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
      .catch(() => caches.match(event.request))
  );
});
