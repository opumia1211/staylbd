const CACHE_NAME = 'staylbd-offline-cache-v1';
const OFFLINE_URL = '/staylbd/offline.html'; // In a real app we'd cache an offline page

const ASSETS_TO_CACHE = [
  '/staylbd/',
  '/staylbd/assets/templates/basic/css/stayl-elite-core.css',
  '/staylbd/assets/images/logoIcon/favicon.png'
];

self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => {
      // Ignore failures for individual assets
      return Promise.allSettled(
        ASSETS_TO_CACHE.map(asset => cache.add(asset).catch(err => console.log('Failed to cache', asset)))
      );
    })
  );
  self.skipWaiting();
});

self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((cacheNames) => {
      return Promise.all(
        cacheNames.map((cacheName) => {
          if (cacheName !== CACHE_NAME) {
            return caches.delete(cacheName);
          }
        })
      );
    })
  );
  self.clients.claim();
});

self.addEventListener('fetch', (event) => {
  if (event.request.method !== 'GET') return;
  
  event.respondWith(
    fetch(event.request)
      .catch(() => {
        return caches.match(event.request)
          .then((response) => {
            if (response) {
              return response;
            }
            // If offline and request is a navigation, show offline fallback
            if (event.request.mode === 'navigate') {
              return caches.match(OFFLINE_URL);
            }
          });
      })
  );
});
