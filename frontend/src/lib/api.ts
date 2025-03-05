import axios from 'axios';
import { supabase } from './supabase';
import { User, Profile, Will, WillSection, Document, Booking } from '@/types';

const API_BASE_URL = process.env.NEXT_PUBLIC_WP_API_URL || 'https://api.willsx.co.uk/wp-json';

// Create axios instance for WordPress API
const wpApi = axios.create({
  baseURL: API_BASE_URL,
  headers: {
    'Content-Type': 'application/json',
  },
  withCredentials: true, // Handle cookies for WordPress sessions
});

// Request interceptor to add authentication
wpApi.interceptors.request.use(async (config) => {
  try {
    // If we're in the browser, get the JWT from Supabase
    if (typeof window !== 'undefined') {
      const { data } = await supabase.auth.getSession();
      const session = data?.session;
      
      if (session?.access_token) {
        config.headers['Authorization'] = `Bearer ${session.access_token}`;
      }
    }
    return config;
  } catch (error) {
    console.error('Error in request interceptor:', error);
    return config;
  }
});

// Define API endpoints
export const endpoints = {
  auth: {
    register: async (userData: any) => {
      return wpApi.post('/willsx/v1/register', userData);
    },
    login: async (email: string, password: string) => {
      return wpApi.post('/jwt-auth/v1/token', {
        username: email,
        password,
      });
    },
    logout: async () => {
      return wpApi.post('/willsx/v1/logout');
    },
    validateToken: async () => {
      return wpApi.post('/jwt-auth/v1/token/validate');
    },
    resetPassword: async (email: string) => {
      return wpApi.post('/willsx/v1/password/reset', { email });
    },
  },
  wills: {
    generateDocument: async (willId: string, documentType: 'draft' | 'final') => {
      return wpApi.post('/willsx/v1/documents/generate', {
        will_id: willId,
        type: documentType,
      });
    },
    submitForReview: async (willId: string) => {
      return wpApi.post(`/willsx/v1/wills/${willId}/submit`);
    },
  },
  bookings: {
    getAvailability: async (advisorId: string, date: string) => {
      return wpApi.get(`/willsx/v1/bookings/availability`, {
        params: { advisor_id: advisorId, date },
      });
    },
    createBooking: async (bookingData: any) => {
      return wpApi.post('/willsx/v1/bookings', bookingData);
    },
  },
  content: {
    getPages: async (slug: string) => {
      return wpApi.get(`/wp/v2/pages`, {
        params: { slug },
      });
    },
    getPosts: async (params: any = {}) => {
      return wpApi.get(`/wp/v2/posts`, { params });
    },
    getPost: async (slug: string) => {
      return wpApi.get(`/wp/v2/posts`, {
        params: { slug },
      });
    },
  },
  partners: {
    getPartnerBySlug: async (slug: string) => {
      return wpApi.get(`/willsx/v1/partners`, {
        params: { slug },
      });
    },
  },
};

// Helper for handling API errors
export const handleApiError = (error: any): { message: string, statusCode: number } => {
  if (error.response) {
    // The request was made and the server responded with an error status
    return {
      message: error.response.data.message || 'An error occurred with the API',
      statusCode: error.response.status,
    };
  } else if (error.request) {
    // The request was made but no response was received
    return {
      message: 'No response received from server. Please check your connection.',
      statusCode: 0,
    };
  } else {
    // Something happened in setting up the request
    return {
      message: error.message || 'An unexpected error occurred',
      statusCode: 500,
    };
  }
};

// User profiles API
export async function getUserProfile(userId: string): Promise<Profile | null> {
  try {
    const { data, error } = await supabase
      .from('user_profiles')
      .select('*')
      .eq('id', userId)
      .single();
    
    if (error) throw error;
    return data;
  } catch (error) {
    console.error('Error fetching user profile:', error);
    return null;
  }
}

export async function updateUserProfile(userId: string, profileData: Partial<Profile>): Promise<Profile | null> {
  try {
    const { data, error } = await supabase
      .from('user_profiles')
      .update(profileData)
      .eq('id', userId)
      .select()
      .single();
    
    if (error) throw error;
    return data;
  } catch (error) {
    console.error('Error updating user profile:', error);
    return null;
  }
}

// Wills API
export async function getWills(userId: string): Promise<Will[]> {
  try {
    const { data, error } = await supabase
      .from('wills')
      .select('*')
      .eq('user_id', userId)
      .order('updated_at', { ascending: false });
    
    if (error) throw error;
    return data || [];
  } catch (error) {
    console.error('Error fetching wills:', error);
    return [];
  }
}

export async function getWillById(willId: string): Promise<Will | null> {
  try {
    const { data, error } = await supabase
      .from('wills')
      .select('*')
      .eq('id', willId)
      .single();
    
    if (error) throw error;
    return data;
  } catch (error) {
    console.error('Error fetching will:', error);
    return null;
  }
}

export async function createWill(userId: string): Promise<Will | null> {
  try {
    const { data, error } = await supabase
      .from('wills')
      .insert({
        user_id: userId,
        status: 'draft',
      })
      .select()
      .single();
    
    if (error) throw error;
    return data;
  } catch (error) {
    console.error('Error creating will:', error);
    return null;
  }
}

export async function updateWillStatus(willId: string, status: string): Promise<boolean> {
  try {
    const { error } = await supabase
      .from('wills')
      .update({ status })
      .eq('id', willId);
    
    if (error) throw error;
    return true;
  } catch (error) {
    console.error('Error updating will status:', error);
    return false;
  }
}

// Will sections API
export async function getWillSection(willId: string, section: string): Promise<WillSection | null> {
  try {
    const { data, error } = await supabase
      .from('will_data')
      .select('*')
      .eq('will_id', willId)
      .eq('section', section)
      .single();
    
    if (error && error.code !== 'PGRST116') throw error; // PGRST116 is "no rows returned"
    return data;
  } catch (error) {
    console.error(`Error fetching will section ${section}:`, error);
    return null;
  }
}

export async function saveWillSection(willId: string, section: string, sectionData: any): Promise<boolean> {
  try {
    // Check if section exists
    const { data: existingSection } = await supabase
      .from('will_data')
      .select('id')
      .eq('will_id', willId)
      .eq('section', section)
      .single();
    
    if (existingSection) {
      // Update existing section
      const { error } = await supabase
        .from('will_data')
        .update({
          data: sectionData,
          updated_at: new Date().toISOString(),
        })
        .eq('id', existingSection.id);
      
      if (error) throw error;
    } else {
      // Create new section
      const { error } = await supabase
        .from('will_data')
        .insert({
          will_id: willId,
          section,
          data: sectionData,
        });
      
      if (error) throw error;
    }
    
    // Update will updated_at timestamp
    await supabase
      .from('wills')
      .update({ updated_at: new Date().toISOString() })
      .eq('id', willId);
    
    return true;
  } catch (error) {
    console.error(`Error saving will section ${section}:`, error);
    return false;
  }
}

// Documents API
export async function getDocuments(userId: string): Promise<Document[]> {
  try {
    const { data, error } = await supabase
      .from('documents')
      .select('*')
      .eq('user_id', userId)
      .order('created_at', { ascending: false });
    
    if (error) throw error;
    return data || [];
  } catch (error) {
    console.error('Error fetching documents:', error);
    return [];
  }
}

export async function getDocumentUrl(storagePath: string): Promise<string | null> {
  try {
    const { data, error } = await supabase.storage
      .from('documents')
      .createSignedUrl(storagePath, 60); // 60 seconds validity
    
    if (error) throw error;
    return data?.signedUrl || null;
  } catch (error) {
    console.error('Error generating document URL:', error);
    return null;
  }
}

// Bookings API
export async function getBookings(userId: string): Promise<Booking[]> {
  try {
    const { data, error } = await supabase
      .from('bookings')
      .select('*')
      .eq('user_id', userId)
      .order('date_time', { ascending: true });
    
    if (error) throw error;
    return data || [];
  } catch (error) {
    console.error('Error fetching bookings:', error);
    return [];
  }
}

export async function createBooking(bookingData: Partial<Booking>): Promise<Booking | null> {
  try {
    const { data, error } = await supabase
      .from('bookings')
      .insert(bookingData)
      .select()
      .single();
    
    if (error) throw error;
    return data;
  } catch (error) {
    console.error('Error creating booking:', error);
    return null;
  }
}

export async function updateBooking(bookingId: string, bookingData: Partial<Booking>): Promise<boolean> {
  try {
    const { error } = await supabase
      .from('bookings')
      .update(bookingData)
      .eq('id', bookingId);
    
    if (error) throw error;
    return true;
  } catch (error) {
    console.error('Error updating booking:', error);
    return false;
  }
}

export default wpApi;
