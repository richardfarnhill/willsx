import { createClient } from '@supabase/supabase-js';
import { Database } from '@/types/supabase';

// Get Supabase URL and anon key from environment variables
const supabaseUrl = process.env.NEXT_PUBLIC_SUPABASE_URL as string;
const supabaseAnonKey = process.env.NEXT_PUBLIC_SUPABASE_ANON_KEY as string;

// Check if the required environment variables are defined
if (!supabaseUrl || !supabaseAnonKey) {
  console.error('Missing Supabase environment variables');
  
  // Only throw in non-production environments to avoid breaking the app
  if (process.env.NODE_ENV !== 'production') {
    throw new Error(
      'Missing required environment variables NEXT_PUBLIC_SUPABASE_URL or NEXT_PUBLIC_SUPABASE_ANON_KEY'
    );
  }
}

// Create Supabase client
export const supabase = createClient<Database>(
  supabaseUrl,
  supabaseAnonKey,
  {
    auth: {
      persistSession: true,
      storageKey: 'willsx-auth-storage',
      autoRefreshToken: true,
    },
  }
);

/**
 * Get the current authenticated user
 * Useful for server-side rendering or API routes
 */
export async function getAuthenticatedUser() {
  try {
    const { data: { session }, error } = await supabase.auth.getSession();
    
    if (error) {
      throw error;
    }
    
    return session?.user || null;
  } catch (error) {
    console.error('Error getting authenticated user:', error);
    return null;
  }
}

/**
 * Check if the user has admin access
 * @param userId The user ID to check
 * @returns Promise resolving to boolean
 */
export async function isUserAdmin(userId: string): Promise<boolean> {
  try {
    const { data, error } = await supabase
      .from('user_roles')
      .select('role')
      .eq('user_id', userId)
      .eq('role', 'admin')
      .maybeSingle();
    
    if (error) throw error;
    
    return !!data;
  } catch (error) {
    console.error('Error checking admin status:', error);
    return false;
  }
}

/**
 * Helper function to get file URL from storage
 */
export async function getFileUrl(bucket: string, path: string, expiresIn = 3600): Promise<string | null> {
  try {
    const { data, error } = await supabase.storage
      .from(bucket)
      .createSignedUrl(path, expiresIn);
    
    if (error) throw error;
    return data.signedUrl;
  } catch (error) {
    console.error('Error getting file URL:', error);
    return null;
  }
}

// Helper functions for common Supabase operations
export async function uploadFile(
  bucket: string,
  filePath: string,
  file: File
): Promise<string | null> {
  try {
    const { data, error } = await supabase.storage.from(bucket).upload(filePath, file, {
      cacheControl: '3600',
      upsert: false,
    });

    if (error) throw error;
    return data.path;
  } catch (error) {
    console.error('Error uploading file:', error);
    return null;
  }
}

export async function downloadFile(bucket: string, filePath: string): Promise<string | null> {
  try {
    const { data, error } = await supabase.storage.from(bucket).download(filePath);
    
    if (error) throw error;
    
    const url = URL.createObjectURL(data);
    return url;
  } catch (error) {
    console.error('Error downloading file:', error);
    return null;
  }
}

export async function getFileUrl(bucket: string, filePath: string): Promise<string | null> {
  try {
    const { data, error } = await supabase.storage.from(bucket).getPublicUrl(filePath);
    
    if (error) throw error;
    
    return data.publicUrl;
  } catch (error) {
    console.error('Error getting file URL:', error);
    return null;
  }
}

export async function deleteFile(bucket: string, filePath: string): Promise<boolean> {
  try {
    const { error } = await supabase.storage.from(bucket).remove([filePath]);
    
    if (error) throw error;
    
    return true;
  } catch (error) {
    console.error('Error deleting file:', error);
    return false;
  }
}

export default supabase;
