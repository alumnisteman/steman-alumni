const CACHE_NAME = 'steman-alumni-v1';
const ASSETS_TO_CACHE = [
    '/',
    '/offline.html',
    '/build/assets/app.css', // This will need to be dynamic in production, but we cache basic assets
    '/images/logo.jpg'
];

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then((cache) => {
                // Try caching, but don't fail installation if some assets are missing
                return cache.addAll(ASSETS_TO_CACHE.map(url => new Request(url, { cache: 'reload' })))
                    .catch(err => console.warn('PWA: Failed to cache some assets during install', err));
            })
            .then(() => self.skipWaiting())
    );
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
    // Only cache GET requests
    if (event.request.method !== 'GET') return;

    // Skip API, Admin, and Livewire requests
    if (event.request.url.includes('/api/') || 
        event.request.url.includes('/admin') || 
        event.request.url.includes('livewire')) {
        return;
    }

    event.respondWith(
        fetch(event.request)
            .catch(() => {
                return caches.match(event.request)
                    .then((response) => {
                        if (response) {
                            return response;
                        }
                        // If it's an HTML page, show offline page
                        if (event.request.headers.get('accept').includes('text/html')) {
                            return caches.match('/offline.html');
                        }
                    });
            })
    );
});
