# React Frontend with Firebase Authentication

This is a React frontend application with Firebase authentication integration.

## Features

- User registration and login
- Google authentication
- Protected routes
- User dashboard
- Responsive design

## Setup Instructions

### 1. Firebase Configuration

1. Go to [Firebase Console](https://console.firebase.google.com/)
2. Create a new project or select an existing one
3. Enable Authentication:
   - Go to Authentication > Sign-in method
   - Enable Email/Password and Google sign-in methods
4. Get your Firebase config:
   - Go to Project Settings > General
   - Scroll down to "Your apps" section
   - Click on the web app icon or "Add app" if you haven't created one
   - Copy the Firebase configuration object

### 2. Update Firebase Configuration

Replace the placeholder values in `src/firebase/config.js` with your actual Firebase configuration:

```javascript
const firebaseConfig = {
  apiKey: "your-actual-api-key",
  authDomain: "your-project.firebaseapp.com",
  projectId: "your-actual-project-id",
  storageBucket: "your-project.appspot.com",
  messagingSenderId: "your-actual-sender-id",
  appId: "your-actual-app-id"
};
```

### 3. Install Dependencies

```bash
npm install
```

### 4. Run the Development Server

```bash
npm run dev
```

The application will be available at `http://localhost:5173`

## Available Scripts

- `npm run dev` - Start development server
- `npm run build` - Build for production
- `npm run preview` - Preview production build
- `npm run lint` - Run ESLint

## Project Structure

```
src/
├── components/
│   ├── Login.jsx          # Login component
│   ├── Signup.jsx         # Registration component
│   ├── Dashboard.jsx      # User dashboard
│   └── PrivateRoute.jsx   # Route protection
├── contexts/
│   └── AuthContext.jsx    # Authentication context
├── firebase/
│   └── config.js          # Firebase configuration
├── App.jsx                # Main app component
├── App.css                # Global styles
└── main.jsx               # App entry point
```

## Authentication Flow

1. Users can register with email/password or Google
2. After successful authentication, users are redirected to the dashboard
3. Protected routes require authentication
4. Users can logout from the dashboard

## Styling

The application uses custom CSS with:
- Responsive design
- Modern gradient backgrounds
- Clean form styling
- Dashboard layout with cards

## Next Steps

- Add user profile management
- Implement password reset functionality
- Add email verification
- Connect with Laravel backend API

## Expanding the ESLint configuration

If you are developing a production application, we recommend using TypeScript with type-aware lint rules enabled. Check out the [TS template](https://github.com/vitejs/vite/tree/main/packages/create-vite/template-react-ts) for information on how to integrate TypeScript and [`typescript-eslint`](https://typescript-eslint.io) in your project.
