import { initializeApp } from 'firebase/app';
import { getAuth, GoogleAuthProvider, signInWithPopup } from 'firebase/auth';

const firebaseConfig = {
    apiKey: import.meta.env.VITE_FIREBASE_API_KEY || '',
    authDomain: import.meta.env.VITE_FIREBASE_AUTH_DOMAIN || '',
    projectId: import.meta.env.VITE_FIREBASE_PROJECT_ID || 'nexamarket-56e7c',
    appId: import.meta.env.VITE_FIREBASE_APP_ID || '',
    messagingSenderId: import.meta.env.VITE_FIREBASE_MESSAGING_SENDER_ID || '',
};

let authInstance = null;

export function isFirebaseConfigured() {
    return Boolean(firebaseConfig.apiKey && firebaseConfig.projectId);
}

export function getFirebaseAuth() {
    if (!isFirebaseConfigured()) {
        return null;
    }

    if (!authInstance) {
        const app = initializeApp(firebaseConfig);
        authInstance = getAuth(app);
    }

    return authInstance;
}

export async function loginWithGoogle() {
    const auth = getFirebaseAuth();
    if (!auth) {
        throw new Error('Kredensial Firebase Web SDK belum dikonfigurasi di file .env lokal (VITE_FIREBASE_API_KEY).');
    }

    const provider = new GoogleAuthProvider();
    provider.addScope('email');
    provider.addScope('profile');
    provider.setCustomParameters({
        prompt: 'select_account',
    });

    const userCredential = await signInWithPopup(auth, provider);
    const idToken = await userCredential.user.getIdToken();

    return idToken;
}

// Attach to window for global Blade/Alpine accessibility
if (typeof window !== 'undefined') {
    window.NexaFirebaseAuth = {
        loginWithGoogle,
        isFirebaseConfigured,
    };
}
