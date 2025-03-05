import '@/styles/globals.css';
import type { AppProps } from 'next/app';
import { useRouter } from 'next/router';
import { useState, useEffect } from 'react';
import { AuthProvider } from '@/contexts/AuthContext';
import { NotificationProvider } from '@/contexts/NotificationContext';
import { ToastProvider } from '@/contexts/ToastContext';
import Layout from '@/components/Layout';

// Loading indicator
function LoadingIndicator() {
  return (
    <div className="fixed top-0 left-0 w-full h-1 bg-blue-100 z-50">
      <div className="h-full bg-blue-600 animate-loading-bar"></div>
    </div>
  );
}

export default function App({ Component, pageProps }: AppProps) {
  const router = useRouter();
  const [loading, setLoading] = useState(false);

  // Set up loading indicator for route changes
  useEffect(() => {
    const handleStart = () => setLoading(true);
    const handleComplete = () => setLoading(false);

    router.events.on('routeChangeStart', handleStart);
    router.events.on('routeChangeComplete', handleComplete);
    router.events.on('routeChangeError', handleComplete);

    return () => {
      router.events.off('routeChangeStart', handleStart);
      router.events.off('routeChangeComplete', handleComplete);
      router.events.off('routeChangeError', handleComplete);
    };
  }, [router]);

  return (
    <AuthProvider>
      <NotificationProvider>
        <ToastProvider>
          {loading && <LoadingIndicator />}
          <Layout>
            <Component {...pageProps} />
          </Layout>
        </ToastProvider>
      </NotificationProvider>
    </AuthProvider>
  );
}
