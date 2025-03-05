import { ReactNode } from 'react';
import Header from '@/components/Header';
import Footer from '@/components/Footer';
import { useAuth } from '@/contexts/AuthContext';

interface MainLayoutProps {
  children: ReactNode;
  hideHeader?: boolean;
  hideFooter?: boolean;
}

export default function MainLayout({ 
  children, 
  hideHeader = false, 
  hideFooter = false,
}: MainLayoutProps) {
  const { user, userDetails } = useAuth();

  return (
    <div className="flex flex-col min-h-screen">
      {!hideHeader && <Header user={user} userDetails={userDetails} />}
      
      <main className="flex-grow">
        {children}
      </main>
      
      {!hideFooter && <Footer />}
    </div>
  );
}
