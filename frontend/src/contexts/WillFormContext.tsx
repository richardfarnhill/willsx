import React, { createContext, useContext, useState, useEffect, ReactNode } from 'react';
import { useRouter } from 'next/router';
import { useAuth } from '@/contexts/AuthContext';
import { useToast } from '@/contexts/ToastContext';
import { useWill, useWillSection } from '@/hooks/useSupabase';
import { Will } from '@/types';

// Step definitions for the will creation process
export const WILL_STEPS = [
  {
    id: 'personal-details',
    title: 'Personal Details',
    description: 'Your personal and contact information',
    path: (willId: string) => `/will/${willId}/personal-details`,
  },
  {
    id: 'family-details',
    title: 'Family Details',
    description: 'Information about your spouse and dependents',
    path: (willId: string) => `/will/${willId}/family-details`,
  },
  {
    id: 'assets',
    title: 'Assets',
    description: 'Your property, accounts, investments and other assets',
    path: (willId: string) => `/will/${willId}/assets`,
  },
  {
    id: 'beneficiaries',
    title: 'Beneficiaries',
    description: 'People you wish to leave your assets to',
    path: (willId: string) => `/will/${willId}/beneficiaries`,
  },
  {
    id: 'executors',
    title: 'Executors',
    description: 'People who will carry out your will',
    path: (willId: string) => `/will/${willId}/executors`,
  },
  {
    id: 'special-wishes',
    title: 'Special Wishes',
    description: 'Funeral arrangements and other specific requests',
    path: (willId: string) => `/will/${willId}/special-wishes`,
  },
  {
    id: 'review',
    title: 'Review',
    description: 'Review your will before finalizing',
    path: (willId: string) => `/will/${willId}/review`,
  },
];

export type WillStepId = typeof WILL_STEPS[number]['id'];

interface WillFormContextProps {
  currentStep: string;
  willId: string | null;
  will: Will | null;
  isLoading: boolean;
  progress: number;
  stepIndex: number;
  totalSteps: number;
  goToStep: (stepId: WillStepId) => void;
  goToNextStep: () => void;
  goToPrevStep: () => void;
  saveSection: <T>(sectionId: string, data: T) => Promise<boolean>;
  getSectionData: <T>(sectionId: string) => T | null;
  isSectionComplete: (sectionId: string) => boolean;
}

const WillFormContext = createContext<WillFormContextProps | undefined>(undefined);

interface WillFormProviderProps {
  children: ReactNode;
  willId: string;
}

export const WillFormProvider: React.FC<WillFormProviderProps> = ({ children, willId }) => {
  const { user } = useAuth();
  const router = useRouter();
  const toast = useToast();
  const { will, loading: willLoading } = useWill(willId);
  
  // Get current step from URL
  const currentPath = router.pathname;
  const currentStep = currentPath.split('/').pop() || '';
  
  // Track section completion
  const [completedSections, setCompletedSections] = useState<Record<string, boolean>>({});
  
  // Get current step index
  const stepIndex = WILL_STEPS.findIndex(step => step.id === currentStep);
  const totalSteps = WILL_STEPS.length;
  
  // Calculate progress
  const progress = Math.round((stepIndex / (totalSteps - 1)) * 100);
  
  // Section data cache
  const [sectionData, setSectionData] = useState<Record<string, any>>({});
  
  // Load completed sections
  useEffect(() => {
    if (will) {
      // In a real app, we'd load the completed sections from the backend
      // For now, just initialize with an empty object
      setCompletedSections({});
    }
  }, [will]);
  
  // Navigation functions
  const goToStep = (stepId: WillStepId) => {
    const step = WILL_STEPS.find(s => s.id === stepId);
    if (step && willId) {
      router.push(step.path(willId));
    }
  };
  
  const goToNextStep = () => {
    if (stepIndex < totalSteps - 1 && willId) {
      router.push(WILL_STEPS[stepIndex + 1].path(willId));
    }
  };
  
  const goToPrevStep = () => {
    if (stepIndex > 0 && willId) {
      router.push(WILL_STEPS[stepIndex - 1].path(willId));
    }
  };
  
  // Section data management
  const saveSection = async <T extends any>(sectionId: string, data: T): Promise<boolean> => {
    if (!willId || !user) return false;
    
    try {
      // Save to Supabase will_data table
      const { data: savedData, error } = await supabase
        .from('will_data')
        .upsert(
          {
            will_id: willId,
            section: sectionId,
            data: data,
            updated_at: new Date().toISOString(),
          },
          { onConflict: 'will_id,section' }
        )
        .select();
      
      if (error) throw error;
      
      // Update local cache
      setSectionData(prev => ({
        ...prev,
        [sectionId]: data,
      }));
      
      // Mark section as completed
      setCompletedSections(prev => ({
        ...prev,
        [sectionId]: true,
      }));
      
      return true;
    } catch (error) {
      console.error(`Error saving section ${sectionId}:`, error);
      toast.error('Failed to save your progress', 'Please try again.');
      return false;
    }
  };
  
  const getSectionData = <T extends any>(sectionId: string): T | null => {
    return sectionData[sectionId] as T || null;
  };
  
  const isSectionComplete = (sectionId: string): boolean => {
    return completedSections[sectionId] || false;
  };
  
  return (
    <WillFormContext.Provider
      value={{
        currentStep,
        willId,
        will,
        isLoading: willLoading,
        progress,
        stepIndex,
        totalSteps,
        goToStep,
        goToNextStep,
        goToPrevStep,
        saveSection,
        getSectionData,
        isSectionComplete,
      }}
    >
      {children}
    </WillFormContext.Provider>
  );
};

export const useWillForm = () => {
  const context = useContext(WillFormContext);
  if (!context) {
    throw new Error('useWillForm must be used within a WillFormProvider');
  }
  return context;
};
