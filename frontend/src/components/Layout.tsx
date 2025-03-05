import { ReactNode, useState, useEffect } from 'react';
import Head from 'next/head';
import Header from './Header';
import Footer from './Footer';
import { useRouter } from 'next/router';
import LoadingSpinner from './LoadingSpinner';
import { useAuth } from '@/contexts/AuthContext';

interface LayoutProps {
  children: ReactNode;
  title?: string;
  description?: string;
  hideFooter?: boolean;
  hideHeader?: boolean;
  noContainer?: boolean;
  authRequired?: boolean;
  adminOnly?: boolean;
}

const Layout = ({ 
  children, 
  title = 'WillsX - Professional Will Writing',
  description = 'Create your will online with WillsX, expert will writing and lasting power of attorney services in England and Wales.',
  hideFooter = false,
  hideHeader = false,
  noContainer = false,
  authRequired = false,
  adminOnly = false
}: LayoutProps) => {
  const router = useRouter();
  const { user, userDetails, isLoading } = useAuth();
  const [pageReady, setPageReady] = useState(!authRequired);
  
  // Handle authentication requirements
  useEffect(() => {
    // If we don't need authentication, the page is always ready
    if (!authRequired) {
      setPageReady(true);
      return;
    }
    
    // Otherwise, wait for auth to complete
    if (!isLoading) {
      // If authentication is required and user is not logged in
      if (!user) {
        router.push(`/login?redirect=${encodeURIComponent(router.asPath)}`);
        return;
      }
      
      // If admin access is required and user is not admin
      if (adminOnly && userDetails?.role !== 'admin') {
        router.push('/dashboard');
        return;
      }
      
      // If all checks pass, mark page as ready
      setPageReady(true);
    }
  }, [authRequired, adminOnly, user, userDetails, isLoading, router]);
  
  // Track page views
  useEffect(() => {
    const handleRouteChange = (url: string) => {
      // Track page view in analytics
      if (window.gtag) {
        window.gtag('config', process.env.NEXT_PUBLIC_GA_MEASUREMENT_ID as string, {
          page_path: url,
        });
      }
    };

    router.events.on('routeChangeComplete', handleRouteChange);
    return () => {
      router.events.off('routeChangeComplete', handleRouteChange);
    };
  }, [router.events]);

  // If authentication is required but not yet loaded
  if (authRequired && !pageReady) {
    return (
      <div className="min-h-screen flex items-center justify-center">
        <LoadingSpinner size="lg" text="Loading your content..." />
      </div>
    );
  }

  return (
    <>
      <Head>
        <title>{title}</title>
        <meta name="description" content={description} />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <link rel="icon" href="/favicon.ico" />
        
        {/* Open Graph / Social Meta Tags */}
        <meta property="og:title" content={title} />
        <meta property="og:description" content={description} />
        <meta property="og:type" content="website" />
        <meta property="og:url" content={`https://willsx.co.uk${router.asPath}`} />
        <meta property="og:image" content="https://willsx.co.uk/images/og-image.jpg" />
        
        {/* Twitter Card */}
        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:title" content={title} />
        <meta name="twitter:description" content={description} />
        <meta name="twitter:image" content="https://willsx.co.uk/images/twitter-image.jpg" />
      </Head>

      {!hideHeader && <Header />}
      
      <main className={!hideHeader ? 'pt-20' : ''}>
        {noContainer ? (
          children
        ) : (
          <div className="container mx-auto px-4 py-8 min-h-[calc(100vh-200px)]">
            {children}
          </div>
        )}
      </main>
      
      {!hideFooter && <Footer />}
    </>
  );
};

export default Layout;
