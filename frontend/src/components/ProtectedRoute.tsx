import { ReactNode, useEffect } from 'react';
import { useRouter } from 'next/router';
import { useAuth } from '@/contexts/AuthContext';
import LoadingSpinner from './LoadingSpinner';

interface ProtectedRouteProps {
  children: ReactNode;
  adminOnly?: boolean;
}

const ProtectedRoute: React.FC<ProtectedRouteProps> = ({ 
  children, 
  adminOnly = false 
}) => {
  const { user, userDetails, isLoading } = useAuth();
  const router = useRouter();
  
  useEffect(() => {
    if (!isLoading && !user) {
      // Redirect to login if not authenticated
      router.push(`/login?redirect=${encodeURIComponent(router.asPath)}`);
    } else if (
      !isLoading && 
      adminOnly && 
      userDetails && 
      userDetails.role !== 'admin'
    ) {
      // Redirect to dashboard if not an admin
      router.push('/dashboard');
    }
  }, [user, userDetails, isLoading, adminOnly, router]);

  // Show loading state while checking authentication
  if (isLoading) {
    return (
      <div className="min-h-screen flex items-center justify-center">
        <LoadingSpinner size="lg" text="Loading..." />
      </div>
    );
  }
  
  // If not authenticated or not authorized, don't render children
  if (!user || (adminOnly && userDetails?.role !== 'admin')) {
    return (
      <div className="min-h-screen flex items-center justify-center">
        <LoadingSpinner size="lg" text="Checking authorization..." />
      </div>
    );
  }

  // User is authenticated and authorized, render children
  return <>{children}</>;
};

export default ProtectedRoute;
