import { useEffect, useState } from 'react';
import { useRouter } from 'next/router';
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { z } from 'zod';
import { useAuth } from '@/contexts/AuthContext';
import { supabase } from '@/lib/supabase';
import { endpoints } from '@/lib/api';
import Head from 'next/head';
import Link from 'next/link';

// Define the steps in the will instruction process
const STEPS = [
  { id: 'intro', title: 'Introduction' },
  { id: 'personal', title: 'Personal Details' },
  { id: 'family', title: 'Family Information' },
  { id: 'assets', title: 'Your Assets' },
  { id: 'beneficiaries', title: 'Beneficiaries' },
  { id: 'executors', title: 'Executors' },
  { id: 'wishes', title: 'Special Wishes' },
  { id: 'review', title: 'Review & Submit' },
];

// Schema for the personal details step
const personalDetailsSchema = z.object({
  title: z.string().min(1, 'Title is required'),
  firstName: z.string().min(1, 'First name is required'),
  lastName: z.string().min(1, 'Last name is required'),
  dateOfBirth: z.string().regex(/^\d{4}-\d{2}-\d{2}$/, 'Valid date is required'),
  address: z.object({
    line1: z.string().min(1, 'Address line 1 is required'),
    line2: z.string().optional(),
    city: z.string().min(1, 'City is required'),
    county: z.string().min(1, 'County is required'),
    postcode: z.string().min(1, 'Postcode is required'),
  }),
  phone: z.string().min(1, 'Phone number is required'),
  maritalStatus: z.enum(['single', 'married', 'separated', 'divorced', 'widowed']),
});

// Schema for the family information step
const familyInformationSchema = z.object({
  hasSpouse: z.boolean(),
  spouseDetails: z.object({
    firstName: z.string().optional(),
    lastName: z.string().optional(),
    dateOfBirth: z.string().optional(),
  }).optional(),
  hasChildren: z.boolean(),
  children: z.array(
    z.object({
      firstName: z.string().min(1, 'First name is required'),
      lastName: z.string().min(1, 'Last name is required'),
      dateOfBirth: z.string().regex(/^\d{4}-\d{2}-\d{2}$/, 'Valid date is required'),
      relationship: z.enum(['biological', 'adopted', 'step']),
    })
  ).optional(),
  hasDependents: z.boolean(),
  dependents: z.array(
    z.object({
      firstName: z.string().min(1, 'First name is required'),
      lastName: z.string().min(1, 'Last name is required'),
      dateOfBirth: z.string().regex(/^\d{4}-\d{2}-\d{2}$/, 'Valid date is required'),
      relationship: z.string().min(1, 'Relationship is required'),
    })
  ).optional(),
});

// Export the will instructions component
export default function WillInstructions() {
  const { user, isLoading: authLoading } = useAuth();
  const router = useRouter();
  const { id: willId } = router.query;
  const [currentStep, setCurrentStep] = useState(0);
  const [currentWillId, setCurrentWillId] = useState<string | null>(null);
  const [saving, setSaving] = useState(false);
  const [isLoading, setIsLoading] = useState(true);

  // Form state for current step
  const {
    register,
    handleSubmit,
    formState: { errors },
    setValue,
    watch,
    reset,
  } = useForm({
    resolver: zodResolver(
      currentStep === 1 
        ? personalDetailsSchema 
        : currentStep === 2 
        ? familyInformationSchema 
        : z.any()
    ),
    defaultValues: {
      title: '',
      firstName: '',
      lastName: '',
      dateOfBirth: '',
      address: {
        line1: '',
        line2: '',
        city: '',
        county: '',
        postcode: '',
      },
      phone: '',
      maritalStatus: undefined,
      hasSpouse: false,
      spouseDetails: {
        firstName: '',
        lastName: '',
        dateOfBirth: '',
      },
      hasChildren: false,
      children: [],
      hasDependents: false,
      dependents: [],
    },
  });

  // Watch for change in hasChildren to show/hide children form
  const hasChildren = watch('hasChildren');
  const hasSpouse = watch('hasSpouse');
  const hasDependents = watch('hasDependents');

  // Redirect to login if not authenticated
  useEffect(() => {
    if (!authLoading && !user) {
      router.push('/login?redirect=/will-instructions');
    }
  }, [user, authLoading, router]);

  // Load existing will or create new one
  useEffect(() => {
    if (!user) return;

    const loadOrCreateWill = async () => {
      try {
        setIsLoading(true);
        
        // If willId is provided in URL, load that specific will
        if (willId) {
          const { data: existingWill, error: willError } = await supabase
            .from('wills')
            .select('*')
            .eq('id', willId)
            .eq('user_id', user.id)
            .single();

          if (willError) {
            throw willError;
          }

          if (existingWill) {
            setCurrentWillId(existingWill.id);
            loadWillData(existingWill.id);
            return;
          }
        }

        // Otherwise check if user has an existing draft will
        const { data: existingWills, error: willsError } = await supabase
          .from('wills')
          .select('id, status')
          .eq('user_id', user.id)
          .eq('status', 'draft')
          .order('created_at', { ascending: false })
          .limit(1);

        if (willsError) throw willsError;

        if (existingWills && existingWills.length > 0) {
          // Load existing will
          setCurrentWillId(existingWills[0].id);
          loadWillData(existingWills[0].id);
        } else {
          // Create new will
          const { data: newWill, error: createError } = await supabase
            .from('wills')
            .insert({
              user_id: user.id,
              status: 'draft',
              created_at: new Date().toISOString(),
              updated_at: new Date().toISOString(),
            })
            .select('id')
            .single();

          if (createError) throw createError;
          setCurrentWillId(newWill.id);
        }
        
      } catch (error) {
        console.error('Error loading/creating will:', error);
      } finally {
        setIsLoading(false);
      }
    };

    loadOrCreateWill();
  }, [user, willId]);

  // Load will data for a given will ID
  const loadWillData = async (willId: string) => {
    try {
      const { data, error } = await supabase
        .from('will_data')
        .select('section, data')
        .eq('will_id', willId);

      if (error) throw error;

      if (data && data.length > 0) {
        // Process each section's data
        data.forEach(section => {
          switch (section.section) {
            case 'personal':
              // Populate personal details form
              const personalData = section.data;
              setValue('title', personalData.title || '');
              setValue('firstName', personalData.firstName || '');
              setValue('lastName', personalData.lastName || '');
              setValue('dateOfBirth', personalData.dateOfBirth || '');
              setValue('address', personalData.address || {
                line1: '',
                line2: '',
                city: '',
                county: '',
                postcode: '',
              });
              setValue('phone', personalData.phone || '');
              setValue('maritalStatus', personalData.maritalStatus);
              break;
            case 'family':
              // Populate family information form
              const familyData = section