import { useEffect } from 'react';
import { useRouter } from 'next/router';
import { useAuth } from '@/contexts/AuthContext';
import ProtectedRoute from '@/components/ProtectedRoute';
import Layout from '@/components/Layout';
import { createWill } from '@/lib/api';
import { useToast } from '@/contexts/ToastContext';
import LoadingSpinner from '@/components/LoadingSpinner';

// This is the entry point for will creation
export default function WillCreationStart() {
  const { user } = useAuth();
  const router = useRouter();
  const toast = useToast();
  
  useEffect(() => {
    // Auto-redirect to creation flow
    async function initializeWill() {
      if (user) {
        try {
          // Create a new will in the database
          const will = await createWill(user.id);
          
          if (will) {
            // Redirect to the first step of the will creation process
            router.push(`/will/${will.id}/personal-details`);
          } else {
            toast.error('Error creating will', 'Please try again later.');
          }
        } catch (error) {
          console.error('Error creating will:', error);
          toast.error('Error creating will', 'Please try again later.');
        }
      }
    }
    
    initializeWill();
  }, [user, router, toast]);
  
  return (
    <ProtectedRoute>
      <Layout title="Create Your Will | WillsX" authRequired={true}>
        <div className="flex items-center justify-center h-64">
          <LoadingSpinner size="lg" text="Setting up your will..." />
        </div>
      </Layout>
    </ProtectedRoute>
  );
}
