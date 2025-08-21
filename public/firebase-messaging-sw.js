importScripts('https://unpkg.com/idb@6.0.0/build/iife/index-min.js'); 
importScripts('https://www.gstatic.com/firebasejs/10.13.0/firebase-app-compat.js'); 
importScripts('https://www.gstatic.com/firebasejs/10.13.0/firebase-messaging-compat.js'); 

const cacheName = 'HRIS';
const toCache = [ 
    'manifest.json',  
    'img/icons/icon-512x512.png', 
    'offline.html',
];  

// Variable untuk menyimpan status notifikasi
let notificationEnabled = true;

// Fungsi untuk mengecek status notifikasi dari IndexedDB
async function getNotificationStatusFromDB() {
    try {
        const db = await idb.openDB('NotificationDB', 1, {
            upgrade(db) {
                db.createObjectStore('settings');
            }
        });
        const status = await db.get('settings', 'notificationEnabled');
        return status !== undefined ? status : true;
    } catch (error) {
        console.log('Error reading notification status:', error);
        return true; // default enabled
    }
}

// Fungsi untuk menyimpan status notifikasi ke IndexedDB
async function setNotificationStatusToDB(enabled) {
    try {
        const db = await idb.openDB('NotificationDB', 1, {
            upgrade(db) {
                db.createObjectStore('settings');
            }
        });
        await db.put('settings', enabled, 'notificationEnabled');
        notificationEnabled = enabled;
    } catch (error) {
        console.log('Error saving notification status:', error);
    }
}

// Load status saat service worker dimulai
getNotificationStatusFromDB().then(status => {
    notificationEnabled = status;
});

// Listen untuk pesan dari main thread
self.addEventListener('message', async (event) => {
    const { type, enabled } = event.data;
    
    switch (type) {
        case 'GET_NOTIFICATION_STATUS':
            const currentStatus = await getNotificationStatusFromDB();
            if (event.ports && event.ports[0]) {
                event.ports[0].postMessage({
                    notificationEnabled: currentStatus
                });
            }
            break;
            
        case 'SET_NOTIFICATION_STATUS':
            await setNotificationStatusToDB(enabled);
            console.log('SW: Notification status updated:', enabled);
            break;
            
        case 'CLEAR_NOTIFICATIONS':
            // Tutup semua notifikasi yang sedang tampil
            const notifications = await self.registration.getNotifications();
            notifications.forEach(notification => notification.close());
            await setNotificationStatusToDB(false);
            console.log('SW: All notifications cleared');
            break;
    }
});

self.addEventListener('install', (e) => { 
    e.waitUntil((async () => {
        const cache = await caches.open(cacheName); 
        await cache.addAll(toCache);
    })());
});

self.addEventListener("activate", evt => self.clients.claim());
   
self.addEventListener('fetch', event => {
    event.respondWith(
        fetch(event.request)
            .catch(() => {
                return caches.match(event.request)
                    .then(response => {
                        return response || caches.match('offline.html');
                    });
            })
    );
});

firebase.initializeApp({ 
    apiKey: "AIzaSyBvzVzw7BVJvqJKQsOSjKEU4n0S7ZQOPIw",
    authDomain: "busogi-ee864.firebaseapp.com",
    projectId: "busogi-ee864",
    storageBucket: "busogi-ee864.firebasestorage.app",
    messagingSenderId: "558198634073",
    appId: "1:558198634073:web:41835adbd3bc59bc522348"
});

const messaging = firebase.messaging(); 

messaging.onBackgroundMessage(async function(payload) {
    console.log("[SW] Message received: ", payload);
    
    // Cek status notifikasi dari database
    const isNotificationEnabled = await getNotificationStatusFromDB();
    
    if (!isNotificationEnabled) {
        console.log('[SW] Notification blocked: disabled by user');
        return; // Jangan tampilkan notifikasi
    }

    const title = payload.data.title || "HRIS";
    const options = {
        body: payload.data.body || "",
        icon: payload.data.icon || "img/icons/icon-144x144.png",
        badge: "img/icons/icon-72x72.png",
        image: payload.data.image || null,
        tag: 'hris-notification', // Tag untuk grouping
        requireInteraction: false, // Auto close after beberapa detik
        data: { 
            url: payload.data.url || "/",
            timestamp: Date.now()
        }
    };

    self.registration.showNotification(title, options);
});

// Handle klik notifikasi
self.addEventListener('notificationclick', function(event) {
    console.log('[SW] Notification clicked:', event.notification);
    
    event.notification.close();
    
    const urlToOpen = event.notification.data && event.notification.data.url ? 
                     event.notification.data.url : '/';

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true })
            .then(clientList => {
                // Cek apakah ada window yang sudah terbuka
                for (let i = 0; i < clientList.length; i++) {
                    const client = clientList[i];
                    if (client.url === urlToOpen && 'focus' in client) {
                        return client.focus();
                    }
                }
                // Jika tidak ada, buka window baru
                if (clients.openWindow) {
                    return clients.openWindow(urlToOpen);
                }
            })
    );
});

// Handle tutup notifikasi
self.addEventListener('notificationclose', function(event) {
    console.log('[SW] Notification closed:', event.notification);
});