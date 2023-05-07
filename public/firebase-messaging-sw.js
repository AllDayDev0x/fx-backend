// Scripts for firebase and firebase messaging
importScripts('https://www.gstatic.com/firebasejs/8.2.0/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/8.2.0/firebase-messaging.js');

// Initialize the Firebase app in the service worker by passing the generated config
const firebaseConfig = {
  apiKey: "AIzaSyBoHjrDF2ZYTIOoyuVyV4YgIgpx5W46PiM",
  authDomain: "fansclub-295709.firebaseapp.com",
  projectId: "fansclub-295709",
  storageBucket: "fansclub-295709.appspot.com",
  messagingSenderId: "36578671417",
  appId: "1:36578671417:web:9b3caa70fac5d876bdc6e8",
  measurementId: "G-9TEV8L7EW7"
};

firebase.initializeApp(firebaseConfig);

const messaging = firebase.messaging();

messaging.onBackgroundMessage(function(payload) {
  console.log('Received background message ', payload);
  // Customize notification here
  const notificationTitle = payload.notification.title;
  const notificationOptions = {
    body: payload.notification.body,
  };
  // self.registration.showNotification(notificationTitle,notificationOptions);
});
