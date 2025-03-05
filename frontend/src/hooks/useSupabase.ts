import { useState, useEffect } from 'react';
import { useAuth } from '@/contexts/AuthContext';
import { supabase } from '@/lib/supabase';
import { Document, Will, Booking } from '@/types';

// Hook to fetch user documents
export function useDocuments() {
  const { user } = useAuth();
  const [documents, setDocuments] = useState<Document[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<Error | null>(null);

  useEffect(() => {
    async function fetchDocuments() {
      if (!user) {
        setLoading(false);
        return;
      }

      try {
        setLoading(true);
        const { data, error } = await supabase
          .from('documents')
          .select('*')
          .eq('user_id', user.id)
          .order('created_at', { ascending: false });

        if (error) throw error;
        setDocuments(data || []);
      } catch (err) {
        console.error('Error fetching documents:', err);
        setError(err instanceof Error ? err : new Error('Failed to fetch documents'));
      } finally {
        setLoading(false);
      }
    }

    fetchDocuments();
  }, [user]);

  return { documents, loading, error };
}

// Hook to fetch user wills
export function useWills() {
  const { user } = useAuth();
  const [wills, setWills] = useState<Will[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<Error | null>(null);

  useEffect(() => {
    async function fetchWills() {
      if (!user) {
        setLoading(false);
        return;
      }

      try {
        setLoading(true);
        const { data, error } = await supabase
          .from('wills')
          .select('*')
          .eq('user_id', user.id)
          .order('updated_at', { ascending: false });

        if (error) throw error;
        setWills(data || []);
      } catch (err) {
        console.error('Error fetching wills:', err);
        setError(err instanceof Error ? err : new Error('Failed to fetch wills'));
      } finally {
        setLoading(false);
      }
    }

    fetchWills();
  }, [user]);

  return { wills, loading, error };
}

// Hook to fetch a specific will by ID
export function useWill(willId: string | null) {
  const { user } = useAuth();
  const [will, setWill] = useState<Will | null>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<Error | null>(null);

  useEffect(() => {
    async function fetchWill() {
      if (!user || !willId) {
        setLoading(false);
        return;
      }

      try {
        setLoading(true);
        const { data, error } = await supabase
          .from('wills')
          .select('*')
          .eq('id', willId)
          .eq('user_id', user.id)
          .single();

        if (error) throw error;
        setWill(data);
      } catch (err) {
        console.error('Error fetching will:', err);
        setError(err instanceof Error ? err : new Error('Failed to fetch will'));
      } finally {
        setLoading(false);
      }
    }

    fetchWill();
  }, [user, willId]);

  return { will, loading, error };
}

// Hook to fetch user bookings
export function useBookings() {
  const { user } = useAuth();
  const [bookings, setBookings] = useState<Booking[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<Error | null>(null);

  useEffect(() => {
    async function fetchBookings() {
      if (!user) {
        setLoading(false);
        return;
      }

      try {
        setLoading(true);
        const { data, error } = await supabase
          .from('bookings')
          .select('*')
          .eq('user_id', user.id)
          .order('date_time', { ascending: true });

        if (error) throw error;
        setBookings(data || []);
      } catch (err) {
        console.error('Error fetching bookings:', err);
        setError(err instanceof Error ? err : new Error('Failed to fetch bookings'));
      } finally {
        setLoading(false);
      }
    }

    fetchBookings();
  }, [user]);

  return { bookings, loading, error };
}

// Hook to fetch a single booking by ID
export function useBooking(bookingId: string | null) {
  const { user } = useAuth();
  const [booking, setBooking] = useState<Booking | null>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<Error | null>(null);

  useEffect(() => {
    async function fetchBooking() {
      if (!user || !bookingId) {
        setLoading(false);
        return;
      }

      try {
        setLoading(true);
        const { data, error } = await supabase
          .from('bookings')
          .select('*')
          .eq('id', bookingId)
          .eq('user_id', user.id)
          .single();

        if (error) throw error;
        setBooking(data);
      } catch (err) {
        console.error('Error fetching booking:', err);
        setError(err instanceof Error ? err : new Error('Failed to fetch booking'));
      } finally {
        setLoading(false);
      }
    }

    fetchBooking();
  }, [user, bookingId]);

  return { booking, loading, error };
}

// Hook to fetch will sections data
export function useWillSection(willId: string | null, section: string) {
  const { user } = useAuth();
  const [sectionData, setSectionData] = useState<any>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<Error | null>(null);

  useEffect(() => {
    async function fetchWillSection() {
      if (!user || !willId || !section) {
        setLoading(false);
        return;
      }

      try {
        setLoading(true);
        const { data, error } = await supabase
          .from('will_data')
          .select('data')
          .eq('will_id', willId)
          .eq('section', section)
          .single();

        if (error && error.code !== 'PGRST116') {
          // PGRST116 means no rows returned, which is fine for new sections
          throw error;
        }
        
        setSectionData(data?.data || null);
      } catch (err) {
        console.error(`Error fetching will section ${section}:`, err);
        setError(err instanceof Error ? err : new Error(`Failed to fetch will section ${section}`));
      } finally {
        setLoading(false);
      }
    }

    fetchWillSection();
  }, [user, willId, section]);

  // Function to save section data
  const saveSection = async (data: any): Promise<boolean> => {
    if (!user || !willId || !section) {
      return false;
    }

    try {
      setLoading(true);

      // Check if section exists
      const { data: existingData, error: checkError } = await supabase
        .from('will_data')
        .select('id')
        .eq('will_id', willId)
        .eq('section', section)
        .single();

      if (checkError && checkError.code !== 'PGRST116') {
        throw checkError;
      }

      let saveError;
      
      if (existingData) {
        // Update existing section
        const { error } = await supabase
          .from('will_data')
          .update({
            data,
            updated_at: new Date().toISOString()
          })
          .eq('id', existingData.id);
        
        saveError = error;
      } else {
        // Create new section
        const { error } = await supabase
          .from('will_data')
          .insert({
            will_id: willId,
            section,
            data,
          });
        
        saveError = error;
      }

      if (saveError) throw saveError;

      // Update the will's updated_at timestamp
      await supabase
        .from('wills')
        .update({ updated_at: new Date().toISOString() })
        .eq('id', willId);

      // Update local state
      setSectionData(data);
      return true;
    } catch (err) {
      console.error(`Error saving will section ${section}:`, err);
      setError(err instanceof Error ? err : new Error(`Failed to save will section ${section}`));
      return false;
    } finally {
      setLoading(false);
    }
  };

  return { sectionData, loading, error, saveSection };
}
