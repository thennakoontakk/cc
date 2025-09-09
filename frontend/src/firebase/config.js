// Firebase configuration - Only for Google OAuth
import { initializeApp } from 'firebase/app';
import { getAuth } from 'firebase/auth';

// Your web app's Firebase configuration
const firebaseConfig = {
  apiKey: "AIzaSyC3AJNm5zooV6XJObcZwhs0dOZQAb1-iWg",
  authDomain: "crafters-corner-4dee8.firebaseapp.com",
  projectId: "crafters-corner-4dee8",
  storageBucket: "crafters-corner-4dee8.firebasestorage.app",
  messagingSenderId: "1025982753706",
  appId: "1:1025982753706:web:4081166c49ca38da1499d3",
  measurementId: "G-NCEM3V9QE9"
};

// Initialize Firebase
const app = initializeApp(firebaseConfig);

// Initialize Firebase Authentication and get a reference to the service
export const auth = getAuth(app);

export default app;