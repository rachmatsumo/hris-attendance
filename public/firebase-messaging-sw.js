importScripts('https://unpkg.com/idb@6.0.0/build/iife/index-min.js'); 
importScripts('https://www.gstatic.com/firebasejs/10.13.0/firebase-app-compat.js'); 
importScripts('https://www.gstatic.com/firebasejs/10.13.0/firebase-messaging-compat.js'); 

const cacheName = 'HRIS';
const toCache = [ 
    'manifest.json',  
    'img/icons/icon-512x512.png', 
    'offline.html',
];  

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

// messaging.onBackgroundMessage(function(payload) {
//   const notificationTitle = payload.notification.title;
//   const notificationBody  = payload.notification.body;
//   const notificationImage = payload.notification.image || null;

//   const notificationOptions = {
//     body: notificationBody,
//     icon: "img/icons/icon-144x144.png",
//     badge: "img/icons/icon-144x144.png",
//     image: notificationImage, // kalau ada gambar akan ditampilkan
//     data: {
//       url: payload.data && payload.data.url ? payload.data.url : '/',
//     }
//   };

//   self.registration.showNotification(notificationTitle, notificationOptions);
// });

messaging.onBackgroundMessage(function(payload) {
  console.log("[SW] Message received: ", payload);

  const title = payload.data.title || "HRIS";
  const options = {
    body: payload.data.body || "",
    icon: "img/icons/icon-144x144.png",
    badge: "img/icons/icon-144x144.png",
    image: payload.data.image || null,
    data: { url: payload.data.url || "/" }
  };

  self.registration.showNotification(title, options);
});


self.addEventListener('notificationclick', function(event) {
  event.notification.close();
 
  const urlToOpen = event.notification.data && event.notification.data.url ? event.notification.data.url : '/';

  event.waitUntil(
      clients.openWindow(urlToOpen)
  );
});