const CACHE_NAME = 'bali-diving-bio-v2';
const STATIC_ASSETS = [
    './bio.php',
    '/_sdk/data_sdk.js', // If this exists
    'https://cdn.tailwindcss.com',
    'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css',
    'https://balidiving.com/logo-balidiving-250.jpg'
];

// Install: Cache Static Assets
self.addEventListener('install', (event) => {
    self.skipWaiting();
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            // Return true even if some fail, to ensure SW installs
            return Promise.all(
                STATIC_ASSETS.map(url => {
                    return cache.add(url).catch(err => console.log('Failed to cache:', url, err));
                })
            );
        })
    );
});

// Activate: Clean Old Caches
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) => {
            return Promise.all(
                keys.map((key) => {
                    if (key !== CACHE_NAME) return caches.delete(key);
                })
            );
        })
    );
    self.clients.claim();
});

// Fetch: Network First for HTML, Stale-While-Revalidate for others
self.addEventListener('fetch', (event) => {
    // Navigation requests (HTML) -> Network First, Fallback to Cache
    if (event.request.mode === 'navigate') {
        event.respondWith(
            fetch(event.request)
                .then((response) => {
                    const clone = response.clone();
                    caches.open(CACHE_NAME).then((cache) => cache.put(event.request, clone));
                    return response;
                })
                .catch(() => {
                    return caches.match(event.request).then(res => {
                        if (res) return res;
                        // Provide a fallback if bio.php is not cached? 
                        // Ideally bio.php is cached during install/first load.
                        return caches.match('./bio.php');
                    });
                })
        );
        return;
    }

    // Assets (Images, CSS, JS) -> Stale-While-Revalidate
    // Try Cache first, then Network (and update cache)
    event.respondWith(
        caches.match(event.request).then((cachedResponse) => {
            const fetchPromise = fetch(event.request).then((networkResponse) => {
                // Cache valid responses
                if (networkResponse && networkResponse.status === 200 && networkResponse.type === 'basic') {
                    const clone = networkResponse.clone();
                    caches.open(CACHE_NAME).then((cache) => cache.put(event.request, clone));
                }
                return networkResponse;
            }).catch(err => {
                // Network failed, nothing to do
                console.log('Network fetch failed for', event.request.url);
            });

            // Return cached response immediately if available, else wait for network
            return cachedResponse || fetchPromise;
        })
    );
});
