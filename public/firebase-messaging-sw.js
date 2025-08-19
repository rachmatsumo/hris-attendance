importScripts('https://unpkg.com/idb@6.0.0/build/iife/index-min.js'); 
importScripts('https://www.gstatic.com/firebasejs/10.13.0/firebase-app-compat.js'); 
importScripts('https://www.gstatic.com/firebasejs/10.13.0/firebase-messaging-compat.js'); 

const cacheName = 'HRIS';
const toCache = [ 
    'web-manifest.json',  
    'logo.png', 
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
        apiKey: "AIzaSyBF58PzNmV6ME9Jllh61IlS2aZwTDbw0Lc",
        authDomain: "testing-74d37.firebaseapp.com",
        projectId: "testing-74d37",
        storageBucket: "testing-74d37.appspot.com",
        messagingSenderId: "874368827370",
        appId: "1:874368827370:web:d5a4e20ca7034898257c93",
        measurementId: "G-ZZ5NRGLVGR"
    });

const messaging = firebase.messaging();

messaging.onBackgroundMessage(function(payload) {
//   console.log('[firebase-messaging-sw.js] Received background message ', payload);

  const notificationTitle = payload.notification.title;
  const notificationBody = payload.notification.body; 
 
  const notificationURL = payload.data && payload.data.url ? payload.data.url : null;
  const clickAction = payload.data && payload.data.click_action ? payload.data.click_action : null;

  const notificationOptions = {
    title: notificationTitle,
    body: notificationBody,
    badge : "logo.png", 
      data: {
          url: notificationURL,
          click_action: clickAction
      }
  };

  self.registration.showNotification(notificationTitle, notificationOptions);
});

self.addEventListener('notificationclick', function(event) {
  event.notification.close();
 
  const urlToOpen = event.notification.data && event.notification.data.url ? event.notification.data.url : '/';

  event.waitUntil(
      clients.openWindow(urlToOpen)
  );
});