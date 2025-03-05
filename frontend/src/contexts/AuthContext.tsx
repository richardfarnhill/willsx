import { createContext, useContext, useEffect, useState, ReactNode } from 'react';
import { supabase } from '@/lib/supabase';
import { User, Profile } from '@/types';
import { getUserProfile } from '@/lib/api';

interface AuthContextType {
  user: User | null;
  userDetails: Profile | null;
  isLoading: boolean;
  error: string | null;
  signUp: (email: string, password: string, userData: any) => Promise<boolean>;
  signIn: (email: string, password: string, rememberMe?: boolean) => Promise<boolean>;
  signOut: () => Promise<void>;
  resetPassword: (email: string) => Promise<boolean>;
  updateProfile: (data: Partial<Profile>) => Promise<boolean>;
}

const AuthContext = createContext<AuthContextType | undefined>(undefined);

export function AuthProvider({ children }: { children: ReactNode }) {
  const [user, setUser] = useState<User | null>(null);
  const [userDetails, setUserDetails] = useState<Profile | null>(null);
  const [isLoading, setIsLoading] = useState<boolean>(true);
  const [error, setError] = useState<string | null>(null);

  // Initial session check and user setup
  useEffect(() => {
    const initializeAuth = async () => {
      setIsLoading(true);
      
      try {
        // Get current session
        const { data: { session } } = await supabase.auth.getSession();

        // Set user if session exists
        if (session) {
          setUser(session.user);
          
          // Fetch user profile details
          if (session.user) {
            const profile = await getUserProfile(session.user.id);
            if (profile) {
              setUserDetails(profile);
            }
          }
        }
      } catch (err) {
        console.error('Error initializing auth:', err);
        setError('Failed to initialize authentication.');
      } finally {
        setIsLoading(false);
      }
    };

    initializeAuth();

    // Set up auth state change listener
    const { data: authListener } = supabase.auth.onAuthStateChange(
      async (event, session) => {
        if (session && session.user) {
          setUser(session.user);
          
          // Fetch profile on auth change
          const profile = await getUserProfile(session.user.id);
          if (profile) {
            setUserDetails(profile);
          }
        } else {
          setUser(null);
          setUserDetails(null);
        }
      }
    );

    // Clean up auth listener
    return () => {
      authListener.subscription.unsubscribe();
    };
  }, []);

  // Sign up function
  const signUp = async (email: string, password: string, userData: any): Promise<boolean> => {
    try {
      const { data, error } = await supabase.auth.signUp({
        email,
        password,
        options: {
          data: {
            first_name: userData.firstName,
            last_name: userData.lastName,
            phone: userData.phone || null,
            partner_id: userData.partnerId || null,
          },
        }
      });

      if (error) throw error;
      
      return true;
    } catch (error: any) {
      console.error('Error signing up:', error);
      throw new Error(error.message || 'Failed to create account. Please try again.');
    }
  };

  // Sign in function
  const signIn = async (email: string, password: string, rememberMe = false): Promise<boolean> => {
    try {
      const { data, error } = await supabase.auth.signInWithPassword({
        email,
        password,
      });

      if (error) throw error;
      
      return true;
    } catch (error: any) {
      console.error('Error signing in:', error);
      throw new Error(error.message || 'Invalid email or password. Please try again.');
    }
  };

  // Sign out function
  const signOut = async (): Promise<void> => {
    try {
      const { error } = await supabase.auth.signOut();
      if (error) throw error;
      
      setUser(null);
      setUserDetails(null);
    } catch (error: any) {
      console.error('Error signing out:', error);
      throw new Error(error.message || 'Failed to sign out. Please try again.');
    }
  };

  // Reset password function
  const resetPassword = async (email: string): Promise<boolean> => {
    try {
      const { data, error } = await supabase.auth.resetPasswordForEmail(email, {
        redirectTo: `${window.location.origin}/reset-password`,
      });

      if (error) throw error;
      
      return true;
    } catch (error: any) {
      console.error('Error resetting password:', error);
      throw new Error(error.message || 'Failed to send password reset email. Please try again.');
    }
  };

  // Update profile function
  const updateProfile = async (data: Partial<Profile>): Promise<boolean> => {
    if (!user) {
      throw new Error('User not authenticated');
    }

    try {
      // Update profile in database
      const { error } = await supabase
        .from('user_profiles')
        .update(data)
        .eq('id', user.id);

      if (error) throw error;
      
      // Update local state
      setUserDetails(prev => prev ? { ...prev, ...data } : null);
      
      return true;
    } catch (error: any) {
      console.error('Error updating profile:', error);
      throw new Error(error.message || 'Failed to update profile. Please try again.');
    }
  };

  const value = {
    user,
    userDetails,
    isLoading,
    error,
    signUp,
    signIn,
    signOut,
    resetPassword,
    updateProfile,
  };

  return <AuthContext.Provider value={value}>{children}</AuthContext.Provider>;
}

// Custom hook to use the auth context
export function useAuth() {
  const context = useContext(AuthContext);
  if (context === undefined) {
    throw new Error('useAuth must be used within an AuthProvider');
  }
  return context;
}
