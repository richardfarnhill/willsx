import { useState, useEffect } from 'react';
import { useRouter } from 'next/router';
import Head from 'next/head';
import { useAuth } from '@/contexts/AuthContext';
import { useDocuments } from '@/hooks/useSupabase';
import { useWills } from '@/hooks/useSupabase';
import { useBookings } from '@/hooks/useSupabase';
import { useToast } from '@/contexts/ToastContext';
import Layout from '@/components/Layout';
import DocumentCard from '@/components/DocumentCard';
import LoadingSpinner from '@/components/LoadingSpinner';
import SidebarNav from '@/components/SidebarNav';
import Button from '@/components/ui/Button';
import Card from '@/components/ui/Card';

export default function Dashboard() {
  const { user, userDetails, isLoading: authLoading } = useAuth();
  const { documents, loading: documentsLoading } = useDocuments();
  const { wills, loading: willsLoading } = useWills();
  const { bookings, loading: bookingsLoading } = useBookings();
  const router = useRouter();
  const toast = useToast();
  const [activeWillId, setActiveWillId] = useState<string | null>(null);

  // Set active will when wills are loaded
  useEffect(() => {
    if (wills && wills.length > 0) {
      setActiveWillId(wills[0].id);
    }
  }, [wills]);

  // Determine if the page is loading
  const isLoading = authLoading || documentsLoading || willsLoading || bookingsLoading;

  // Determine if user has no data yet
  const isEmpty = !isLoading && (!wills || wills.length === 0) && (!documents || documents.length === 0) && (!bookings || bookings.length === 0);

  // Redirect if not authenticated
  useEffect(() => {
    if (!authLoading && !user) {
      router.push('/login?redirect=/dashboard');
    }
  }, [user, authLoading, router]);

  if (isLoading) {
    return (
      <Layout title="Dashboard | WillsX" authRequired={true}>
        <div className="flex justify-center items-center h-64">
          <LoadingSpinner size="lg" text="Loading your dashboard..." />
        </div>
      </Layout>
    );
  }

  const startNewWill = async () => {
    try {
      // In the real app, this would call to Supabase to create a new will
      toast.info('Starting new will process', 'You will be redirected to the will creation form.');
      router.push('/will-instructions/new');
    } catch (error) {
      toast.error('Error starting new will', 'Please try again later.');
    }
  };

  const continueWill = (willId: string) => {
    router.push(`/will-instructions/${willId}`);
  };

  const scheduleConsultation = () => {
    router.push('/bookings/new');
  };

  return (
    <Layout title="Dashboard | WillsX" authRequired={true}>
      <Head>
        <title>My Dashboard | WillsX</title>
        <meta name="description" content="Manage your wills and legal documents" />
      </Head>

      <div className="flex flex-col md:flex-row gap-8">
        {/* Sidebar */}
        <div className="w-full md:w-64 flex-shrink-0">
          <SidebarNav className="bg-white rounded-lg shadow-sm p-4" />
          
          <Card className="mt-6">
            <div className="p-4 flex flex-col items-center text-center">
              <div className="h-16 w-16 rounded-full bg-blue-100 flex items-center justify-center mb-3">
                <span className="text-xl font-semibold text-blue-700">
                  {userDetails?.first_name?.charAt(0) || ''}{userDetails?.last_name?.charAt(0) || ''}
                </span>
              </div>
              <h3 className="text-lg font-medium">
                {userDetails?.first_name} {userDetails?.last_name}
              </h3>
              <p className="text-sm text-gray-500 mb-4">
                {user?.email}
              </p>
              <Button
                variant="outline"
                size="sm"
                onClick={() => router.push('/profile')}
              >
                Edit Profile
              </Button>
            </div>
          </Card>
        </div>

        {/* Main Content */}
        <div className="flex-1">
          {/* Welcome Card */}
          <Card>
            <div className="p-6">
              <h1 className="text-2xl font-bold mb-2">Welcome back, {userDetails?.first_name || 'there'}!</h1>
              <p className="text-gray-600 mb-4">
                {isEmpty ? 'Get started by creating your will or booking a consultation.' : 'Here\'s an overview of your account.'}
              </p>
              <div className="flex flex-wrap gap-4">
                <Button onClick={startNewWill}>
                  Start New Will
                </Button>
                <Button variant="outline" onClick={scheduleConsultation}>
                  Book Consultation
                </Button>
              </div>
            </div>
          </Card>

          {/* Content grids */}
          {!isEmpty ? (
            <>
              {/* Wills Section */}
              {wills && wills.length > 0 && (
                <div className="mt-8">
                  <h2 className="text-xl font-semibold mb-4">Your Wills</h2>
                  <div className="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                    <div className="overflow-x-auto">
                      <table className="min-w-full divide-y divide-gray-200">
                        <thead className="bg-gray-50">
                          <tr>
                            <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                              Status
                            </th>
                            <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                              Created
                            </th>
                            <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                              Last Updated
                            </th>
                            <th scope="col" className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                              Actions
                            </th>
                          </tr>
                        </thead>
                        <tbody className="bg-white divide-y divide-gray-200">
                          {wills.map((will) => (
                            <tr key={will.id}>
                              <td className="px-6 py-4 whitespace-nowrap">
                                <span className={`px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full ${
                                  will.status === 'draft' 
                                    ? 'bg-yellow-100 text-yellow-800' 
                                    : will.status === 'submitted' 
                                    ? 'bg-blue-100 text-blue-800' 
                                    : 'bg-green-100 text-green-800'
                                }`}>
                                  {will.status.charAt(0).toUpperCase() + will.status.slice(1)}
                                </span>
                              </td>
                              <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {new Date(will.created_at).toLocaleDateString()}
                              </td>
                              <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {new Date(will.updated_at).toLocaleDateString()}
                              </td>
                              <td className="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <Button
                                  size="sm"
                                  onClick={() => continueWill(will.id)}
                                >
                                  {will.status === 'draft' ? 'Continue' : 'View'}
                                </Button>
                              </td>
                            </tr>
                          ))}
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              )}

              {/* Documents Section */}
              {documents && documents.length > 0 && (
                <div className="mt-8">
                  <h2 className="text-xl font-semibold mb-4">Recent Documents</h2>
                  <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {documents.slice(0, 4).map((doc) => (
                      <DocumentCard key={doc.id} document={doc} />
                    ))}
                  </div>
                  {documents.length > 4 && (
                    <div className="mt-4 text-right">
                      <Button 
                        variant="outline" 
                        size="sm"
                        onClick={() => router.push('/documents')}
                      >
                        View All Documents
                      </Button>
                    </div>
                  )}
                </div>
              )}

              {/* Bookings Section */}
              {bookings && bookings.length > 0 && (
                <div className="mt-8">
                  <h2 className="text-xl font-semibold mb-4">Upcoming Appointments</h2>
                  <div className="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                    <div className="overflow-x-auto">
                      <table className="min-w-full divide-y divide-gray-200">
                        <thead className="bg-gray-50">
                          <tr>
                            <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                              Service
                            </th>
                            <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                              Date & Time
                            </th>
                            <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                              Status
                            </th>
                            <th scope="col" className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                              Actions
                            </th>
                          </tr>
                        </thead>
                        <tbody className="bg-white divide-y divide-gray-200">
                          {bookings.map((booking) => (
                            <tr key={booking.id}>
                              <td className="px-6 py-4 whitespace-nowrap">
                                <div className="text-sm font-medium text-gray-900">
                                  {booking.service_type.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase())}
                                </div>
                              </td>
                              <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {new Date(booking.date_time).toLocaleString()}
                              </td>
                              <td className="px-6 py-4 whitespace-nowrap">
                                <span className={`px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full ${
                                  booking.status === 'scheduled' 
                                    ? 'bg-blue-100 text-blue-800' 
                                    : booking.status === 'completed'
                                    ? 'bg-green-100 text-green-800'
                                    : 'bg-red-100 text-red-800'
                                }`}>
                                  {booking.status.charAt(0).toUpperCase() + booking.status.slice(1)}
                                </span>
                              </td>
                              <td className="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                {booking.status === 'scheduled' && booking.video_link && (
                                  <Button
                                    size="sm"
                                    as="a"
                                    href={booking.video_link}
                                    target="_blank"
                                  >
                                    Join Call
                                  </Button>
                                )}
                              </td>
                            </tr>
                          ))}
                        </tbody>
                      </table>
                    </div>
                  </div>
                  <div className="mt-4 text-right">
                    <Button 
                      variant="outline" 
                      size="sm"
                      onClick={() => router.push('/bookings')}
                    >
                      View All Appointments
                    </Button>
                  </div>
                </div>
              )}
            </>
          ) : (
            <div className="mt-8 bg-white rounded-lg border border-gray-200 shadow-sm p-8 text-center">
              <div className="mx-auto w-24 h-24 bg-blue-50 rounded-full flex items-center justify-center mb-4">
                <svg className="h-12 w-12 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
              </div>
              <h2 className="text-xl font-semibold mb-2">Get Started with WillsX</h2>
              <p className="text-gray-600 mb-6 max-w-md mx-auto">
                You don't have any wills or appointments yet. Create your first will or schedule a consultation with one of our legal experts.
              </p>
              <div className="flex flex-col sm:flex-row gap-4 justify-center">
                <Button onClick={startNewWill}>
                  Create Your Will
                </Button>
                <Button variant="outline" onClick={scheduleConsultation}>
                  Schedule Consultation
                </Button>
              </div>
            </div>
          )}
        </div>
      </div>
    </Layout>
  );
}
