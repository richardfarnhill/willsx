import { useState } from 'react';
import { useRouter } from 'next/router';
import Head from 'next/head';
import { useAuth } from '@/contexts/AuthContext';
import { useNotification } from '@/contexts/NotificationContext';
import ProtectedRoute from '@/components/ProtectedRoute';
import LoadingSpinner from '@/components/LoadingSpinner';
import { z } from 'zod';
import { useFormValidation } from '@/hooks/useFormValidation';

const settingsSchema = z.object({
  email_notifications: z.boolean(),
  sms_notifications: z.boolean(),
  marketing_emails: z.boolean(),
  document_sharing: z.boolean(),
  dark_mode: z.boolean(),
});

type SettingsFormData = z.infer<typeof settingsSchema>;

export default function Settings() {
  const { user, isLoading } = useAuth();
  const router = useRouter();
  const notification = useNotification();
  const [isSaving, setIsSaving] = useState(false);
  
  // Mock initial settings - in production these would come from Supabase
  const initialSettings: SettingsFormData = {
    email_notifications: true,
    sms_notifications: false,
    marketing_emails: true,
    document_sharing: true,
    dark_mode: false,
  };

  const {
    values,
    handleChange,
    handleSubmit,
  } = useFormValidation<SettingsFormData>({
    schema: settingsSchema,
    initialValues: initialSettings,
    onSubmit: saveSettings,
  });

  async function saveSettings(data: SettingsFormData) {
    setIsSaving(true);
    
    try {
      // In a real app, we'd save this to Supabase
      // await supabase.from('user_settings').upsert({ 
      //   user_id: user.id, 
      //   ...data,
      //   updated_at: new Date().toISOString()
      // });
      
      // Simulate API call
      await new Promise(resolve => setTimeout(resolve, 800));
      
      notification.success('Settings updated successfully');
    } catch (error) {
      console.error('Error saving settings:', error);
      notification.error('Failed to update settings. Please try again.');
    } finally {
      setIsSaving(false);
    }
  }

  if (isLoading) {
    return <LoadingSpinner fullPage text="Loading settings..." />;
  }

  return (
    <ProtectedRoute>
      <Head>
        <title>Account Settings | WillsX</title>
        <meta name="description" content="Manage your WillsX account settings" />
      </Head>
      
      <div className="max-w-4xl mx-auto">
        <h1 className="text-3xl font-bold mb-6">Account Settings</h1>
        
        <div className="bg-white rounded-lg shadow overflow-hidden">
          <div className="p-6">
            <form onSubmit={handleSubmit}>
              <div className="space-y-6">
                <h2 className="text-xl font-semibold mb-4">Notifications</h2>
                
                <div className="flex items-center justify-between py-2">
                  <div>
                    <h3 className="text-base font-medium text-gray-900">Email notifications</h3>
                    <p className="text-sm text-gray-500">Receive email updates about your will progress</p>
                  </div>
                  <label className="relative inline-flex items-center cursor-pointer">
                    <input
                      type="checkbox"
                      name="email_notifications"
                      checked={values.email_notifications}
                      onChange={handleChange}
                      className="sr-only peer"
                    />
                    <div className="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:bg-blue-600 peer-focus:ring-4 peer-focus:ring-blue-300 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all"></div>
                  </label>
                </div>
                
                <div className="flex items-center justify-between py-2">
                  <div>
                    <h3 className="text-base font-medium text-gray-900">SMS notifications</h3>
                    <p className="text-sm text-gray-500">Receive text message reminders for appointments</p>
                  </div>
                  <label className="relative inline-flex items-center cursor-pointer">
                    <input
                      type="checkbox"
                      name="sms_notifications"
                      checked={values.sms_notifications}
                      onChange={handleChange}
                      className="sr-only peer"
                    />
                    <div className="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:bg-blue-600 peer-focus:ring-4 peer-focus:ring-blue-300 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all"></div>
                  </label>
                </div>
                
                <div className="flex items-center justify-between py-2">
                  <div>
                    <h3 className="text-base font-medium text-gray-900">Marketing emails</h3>
                    <p className="text-sm text-gray-500">Receive marketing and promotional emails</p>
                  </div>
                  <label className="relative inline-flex items-center cursor-pointer">
                    <input
                      type="checkbox"
                      name="marketing_emails"
                      checked={values.marketing_emails}
                      onChange={handleChange}
                      className="sr-only peer"
                    />
                    <div className="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:bg-blue-600 peer-focus:ring-4 peer-focus:ring-blue-300 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all"></div>
                  </label>
                </div>
                
                <hr className="my-6" />
                
                <h2 className="text-xl font-semibold mb-4">Privacy & Sharing</h2>
                
                <div className="flex items-center justify-between py-2">
                  <div>
                    <h3 className="text-base font-medium text-gray-900">Document sharing</h3>
                    <p className="text-sm text-gray-500">Allow sharing documents with permitted users</p>
                  </div>
                  <label className="relative inline-flex items-center cursor-pointer">
                    <input
                      type="checkbox"
                      name="document_sharing"
                      checked={values.document_sharing}
                      onChange={handleChange}
                      className="sr-only peer"
                    />
                    <div className="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:bg-blue-600 peer-focus:ring-4 peer-focus:ring-blue-300 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all"></div>
                  </label>
                </div>
                
                <hr className="my-6" />
                
                <h2 className="text-xl font-semibold mb-4">Appearance</h2>
                
                <div className="flex items-center justify-between py-2">
                  <div>
                    <h3 className="text-base font-medium text-gray-900">Dark mode</h3>
                    <p className="text-sm text-gray-500">Use dark theme for the interface</p>
                  </div>
                  <label className="relative inline-flex items-center cursor-pointer">
                    <input
                      type="checkbox"
                      name="dark_mode"
                      checked={values.dark_mode}
                      onChange={handleChange}
                      className="sr-only peer"
                    />
                    <div className="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:bg-blue-600 peer-focus:ring-4 peer-focus:ring-blue-300 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all"></div>
                  </label>
                </div>
                
                <div className="mt-8">
                  <button
                    type="submit"
                    disabled={isSaving}
                    className="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-70"
                  >
                    {isSaving ? (
                      <>
                        <LoadingSpinner size="sm" color="white" className="mr-2" />
                        Saving...
                      </>
                    ) : (
                      'Save Settings'
                    )}
                  </button>
                </div>
              </div>
            </form>
          </div>
        </div>
        
        <div className="mt-8 bg-white rounded-lg shadow overflow-hidden">
          <div className="p-6">
            <h2 className="text-xl font-semibold text-red-600 mb-4">Danger Zone</h2>
            <p className="text-gray-600 mb-4">
              These actions are irreversible. Please proceed with caution.
            </p>
            
            <div className="flex flex-col space-y-4 sm:flex-row sm:space-y-0 sm:space-x-4">
              <button
                type="button"
                className="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                onClick={() => {
                  notification.info(
                    'This feature is not yet implemented',
                    'Account deletion would be handled here in the production version.'
                  );
                }}
              >
                Delete Account
              </button>
              <button
                type="button"
                className="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                onClick={() => router.push('/reset-password')}
              >
                Change Password
              </button>
            </div>
          </div>
        </div>
      </div>
    </ProtectedRoute>
  );
}
