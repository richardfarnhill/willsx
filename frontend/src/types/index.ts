// Core types for the WillsX application
import { Database } from './supabase';
import { User as SupabaseUser } from '@supabase/supabase-js';

// Auth
export interface User extends SupabaseUser {
  // Extended user type if needed
}

// User profile
export interface Profile {
  id: string;
  first_name: string;
  last_name: string;
  email: string;
  phone?: string | null;
  partner_id?: number | null;
  role: string;
  created_at: string;
  updated_at: string;
}

// User settings
export interface UserSettings {
  id: string;
  user_id: string;
  email_notifications: boolean;
  marketing_emails: boolean;
  sms_notifications: boolean;
  document_sharing: boolean;
  dark_mode: boolean;
  created_at: string;
  updated_at: string;
}

// Will
export interface Will {
  id: string;
  user_id: string;
  status: 'draft' | 'submitted' | 'in_review' | 'completed';
  created_at: string;
  updated_at: string;
  submitted_at?: string | null;
  completed_at?: string | null;
}

// Will section
export interface WillSection {
  id: string;
  will_id: string;
  section: string;
  data: any;
  updated_at: string;
}

// Document
export interface Document {
  id: string;
  user_id: string;
  will_id?: string | null;
  type: 'will_draft' | 'will_final' | 'lpa_draft' | 'lpa_final' | 'id_verification';
  storage_path: string;
  created_at: string;
  status: 'draft' | 'final';
}

// Booking
export interface Booking {
  id: string;
  user_id: string;
  advisor_id: string;
  service_type: 'will_consultation' | 'lpa_consultation' | 'general_advice';
  date_time: string;
  duration: number;
  status: 'scheduled' | 'completed' | 'cancelled';
  video_link?: string | null;
  notes?: string | null;
  created_at: string;
  updated_at: string;
}

// Partner
export interface Partner {
  id: number;
  name: string;
  slug: string;
  logo_url?: string | null;
  website_url?: string | null;
  contact_email?: string | null;
  contact_phone?: string | null;
  referral_code: string;
  fee_structure: any;
  created_at: string;
  updated_at: string;
}

// For form state
export interface PersonalDetails {
  firstName: string;
  middleName?: string;
  lastName: string;
  maidenName?: string;
  dateOfBirth: string;
  address: Address;
  previousAddresses?: Address[];
  contactDetails: ContactDetails;
  maritalStatus: 'single' | 'married' | 'divorced' | 'widowed' | 'civil-partnership' | 'separated';
}

export interface Address {
  line1: string;
  line2?: string;
  city: string;
  county?: string;
  postcode: string;
  country: string;
  dateFrom?: string;
  dateTo?: string;
}

export interface ContactDetails {
  phone: string;
  email: string;
  alternativePhone?: string;
}

export interface FamilyDetails {
  spouse?: Spouse;
  children: Child[];
  dependents: Dependent[];
}

export interface Spouse {
  firstName: string;
  middleName?: string;
  lastName: string;
  dateOfBirth: string;
}

export interface Child {
  id: string;
  firstName: string;
  middleName?: string;
  lastName: string;
  dateOfBirth: string;
  relationship: 'biological' | 'adopted' | 'step-child';
  address?: Address;
}

export interface Dependent {
  id: string;
  firstName: string;
  middleName?: string;
  lastName: string;
  dateOfBirth: string;
  relationship: string;
  address?: Address;
  reasonForDependency: string;
}

export interface Asset {
  id: string;
  type: 'property' | 'bank-account' | 'investment' | 'vehicle' | 'valuable' | 'other';
  description: string;
  value: number;
  ownership: 'sole' | 'joint' | 'tenants-in-common';
  coOwners?: Person[];
  location?: string;
  reference?: string;
}

export interface Person {
  id: string;
  firstName: string;
  middleName?: string;
  lastName: string;
  relationship?: string;
  address?: Address;
  contactDetails?: ContactDetails;
}

export interface Beneficiary extends Person {
  type: 'person' | 'charity' | 'other-organization';
  charityNumber?: string;
  gifts: Gift[];
}

export interface Gift {
  id: string;
  type: 'specific' | 'pecuniary' | 'residuary';
  description: string;
  value?: number;
  percentage?: number;
  conditions?: string;
}

export interface Executor extends Person {
  isReplacement: boolean;
  professionalDetails?: {
    company: string;
    profession: string;
  };
}

export interface SpecialWishes {
  funeralInstructions?: string;
  letterOfWishes?: string;
  digitalAssets?: string;
  pets?: string;
  other?: string;
}

// UI related types
export type ButtonVariant = 'primary' | 'secondary' | 'outline' | 'danger' | 'success' | 'ghost';
export type ButtonSize = 'xs' | 'sm' | 'md' | 'lg' | 'xl';
export type AlertType = 'info' | 'success' | 'warning' | 'error';
