// Service Worker for ISO Digital PWA
const CACHE_NAME = 'iso-digital-v1.0.1';
const BASE_PATH = '';

const STATIC_ASSETS = [
    `${BASE_PATH}/`,
    `${BASE_PATH}/admin`,
    `${BASE_PATH}/offline.html`,
    `${BASE_PATH}/logo.png`,
    `${BASE_PATH}/logo.jpg`,
    `${BASE_PATH}/manifest.json`,
    `${BASE_PATH}/css/mobile-enhancements.css`,
];

// Install event - cache static assets
self.addEventListener('install', (event) => {
    console.log('[SW] Installing service worker...');
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then((cache) => {
                console.log('[SW] Caching static assets');
                return cache.addAll(STATIC_ASSETS.map(url => new Request(url, { cache: 'no-cache' })))
                    .catch(err => {
                        console.warn('[SW] Some assets failed to cache:', err);
                        // Don't fail installation if some assets can't be cached
                        return Promise.resolve();
                    });
            })
            .then(() => {
                console.log('[SW] Service worker installed successfully');
                return self.skipWaiting();
            })
    );
});

// Activate event - clean up old caches
self.addEventListener('activate', (event) => {
    console.log('[SW] Activating service worker...');
    event.waitUntil(
        caches.keys()
            .then((cacheNames) => {
                return Promise.all(
                    cacheNames.map((cacheName) => {
                        if (cacheName !== CACHE_NAME) {
                            console.log('[SW] Deleting old cache:', cacheName);
                            return caches.delete(cacheName);
                        }
                    })
                );
            })
            .then(() => {
                console.log('[SW] Service worker activated');
                return self.clients.claim();
            })
    );
});

// Fetch event - implement caching strategies
self.addEventListener('fetch', (event) => {
    const { request } = event;
    const url = new URL(request.url);

    // Skip non-GET requests
    if (request.method !== 'GET') {
        return;
    }

    // Skip Chrome extensions and other protocols
    if (!url.protocol.startsWith('http')) {
        return;
    }

    // Skip CSRF token requests
    if (url.pathname.includes('/sanctum/csrf-cookie')) {
        return;
    }

    event.respondWith(
        caches.match(request)
            .then((cachedResponse) => {
                // Strategy: Network first, fallback to cache
                return fetch(request)
                    .then((networkResponse) => {
                        // Cache successful responses
                        if (networkResponse && networkResponse.status === 200) {
                            const responseToCache = networkResponse.clone();
                            caches.open(CACHE_NAME).then((cache) => {
                                cache.put(request, responseToCache);
                            });
                        }
                        return networkResponse;
                    })
                    .catch((error) => {
                        console.log('[SW] Network request failed, serving from cache:', request.url);

                        // Return cached response if available
                        if (cachedResponse) {
                            return cachedResponse;
                        }

                        // For navigation requests, show offline page
                        if (request.mode === 'navigate') {
                            return caches.match(`${BASE_PATH}/offline.html`);
                        }

                        // For other requests, reject
                        return Promise.reject(error);
                    });
            })
    );
});

// Background sync event (for offline form submissions)
self.addEventListener('sync', (event) => {
    console.log('[SW] Background sync:', event.tag);
    if (event.tag === 'sync-forms') {
        event.waitUntil(syncForms());
    }
});

async function syncForms() {
    console.log('[SW] Syncing offline form submissions...');
    // This will be used to sync forms submitted while offline
    // Implementation depends on your specific form handling
}

// Push notification event (optional, for future use)
self.addEventListener('push', (event) => {
    console.log('[SW] Push notification received');
    const options = {
        body: event.data ? event.data.text() : 'New update available',
        icon: `${BASE_PATH}/icons/icon-192x192.png`,
        badge: `${BASE_PATH}/icons/icon-72x72.png`,
        vibrate: [200, 100, 200],
    };

    event.waitUntil(
        self.registration.showNotification('ISO Digital', options)
    );
});

console.log('[SW] Service Worker loaded');
