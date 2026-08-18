// ================================================
// TechniTrack Service Worker — PWA Push Notifications
// Gère les notifications Push Web même quand l'app est fermée
// ================================================

const CACHE_NAME = 'technitrack-v1';
const OFFLINE_URL = '/mobile';

// ── Installation du Service Worker ──
self.addEventListener('install', event => {
    console.log('[ServiceWorker] Install');
    self.skipWaiting();
});

// ── Activation ──
self.addEventListener('activate', event => {
    console.log('[ServiceWorker] Activate');
    event.waitUntil(clients.claim());
});

// ── Réception des Notifications Push ──
self.addEventListener('push', event => {
    let data = {
        title: 'TechniTrack',
        body: 'Vous avez une nouvelle notification.',
        url: '/mobile',
        icon: '/icon-192.png',
        badge: '/badge.png',
    };

    try {
        if (event.data) {
            const parsed = event.data.json();
            data = { ...data, ...parsed };
        }
    } catch (e) {
        if (event.data) {
            data.body = event.data.text();
        }
    }

    const notificationOptions = {
        body: data.body,
        icon: data.icon || '/icon-192.png',
        badge: data.badge || '/badge.png',
        tag: 'technitrack-notif-' + Date.now(),
        requireInteraction: true,
        vibrate: [200, 100, 200],
        data: {
            url: data.url || '/mobile',
            timestamp: data.timestamp || new Date().toISOString(),
        },
        actions: [
            {
                action: 'open',
                title: '📱 Ouvrir l\'application',
            },
            {
                action: 'dismiss',
                title: 'Ignorer',
            },
        ],
    };

    event.waitUntil(
        self.registration.showNotification(data.title, notificationOptions)
    );
});

// ── Clic sur la Notification ──
self.addEventListener('notificationclick', event => {
    const notification = event.notification;
    const action = event.action;
    const targetUrl = notification.data?.url || '/mobile';

    notification.close();

    if (action === 'dismiss') {
        return;
    }

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then(clientList => {
            // Si une fenêtre est déjà ouverte, on la focus
            for (const client of clientList) {
                if (client.url.includes('/mobile') && 'focus' in client) {
                    client.focus();
                    return client.navigate(targetUrl);
                }
            }
            // Sinon, ouvrir une nouvelle fenêtre
            if (clients.openWindow) {
                return clients.openWindow(targetUrl);
            }
        })
    );
});

// ── Fermeture silencieuse de la notification ──
self.addEventListener('notificationclose', event => {
    console.log('[ServiceWorker] Notification fermée :', event.notification.tag);
});
