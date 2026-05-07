const CACHE_NAME = 'sia-campsite-v1';

self.addEventListener('install', event => {
    console.log('Service Worker installed');
    self.skipWaiting();
});

self.addEventListener('activate', event => {
    console.log('Service Worker activated');
});

self.addEventListener('fetch', event => {
    // Minimal fetch event to satisfy PWA requirements
    event.respondWith(fetch(event.request).catch(() => {
        return new Response('Offline mode not fully supported yet.');
    }));
});
