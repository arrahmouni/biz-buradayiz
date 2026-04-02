// Import Firebase scripts required for the service worker
importScripts('https://www.gstatic.com/firebasejs/8.10.0/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/8.10.0/firebase-messaging.js');

// Initialize the Firebase app in the service worker by passing in the messagingSenderId
firebase.initializeApp({
    apiKey              : "{{ config('notification.firebase_config.api_key') }}",
    authDomain          : "{{ config('notification.firebase_config.auth_domain') }}",
    projectId           : "{{ config('notification.firebase_config.project_id') }}",
    storageBucket       : "{{ config('notification.firebase_config.storage_bucket') }}",
    messagingSenderId   : "{{ config('notification.firebase_config.messaging_sender_id') }}",
    appId               : "{{ config('notification.firebase_config.app_id') }}",
    measurementId       : "{{ config('notification.firebase_config.measurement_id') }}"
});

// Retrieve an instance of Firebase Messaging so that it can handle background messages
const messaging = firebase.messaging();

// Handle background messages
messaging.onBackgroundMessage((payload) => {
    console.log('[firebase-messaging-sw.js] Received background message', payload);

    // Send a message to ALL active clients (tabs)
    self.clients.matchAll({ includeUncontrolled: true }).then((clients) => {
        clients.forEach((client) => {
            client.postMessage({
                type: 'UPDATE_NOTIFICATION_COUNT',
                increment: 1 // Tell clients to increment count
            });
        });
    });

    // Show the notification
    const { title, body, icon } = payload.notification;
    self.registration.showNotification(title, { body, icon });
});

