export type Json =
  | string
  | number
  | boolean
  | null
  | { [key: string]: Json | undefined }
  | Json[]

export interface Database {
  public: {
    Tables: {
      users: {
        Row: {
          id: string
          email: string
          first_name: string | null
          last_name: string | null
          phone: string | null
          created_at: string
          partner_id: number | null
        }
        Insert: {
          id?: string
          email: string
          first_name?: string | null
          last_name?: string | null
          phone?: string | null
          created_at?: string
          partner_id?: number | null
        }
        Update: {
          id?: string
          email?: string
          first_name?: string | null
          last_name?: string | null
          phone?: string | null
          created_at?: string
          partner_id?: number | null
        }
      }
      user_profiles: {
        Row: {
          id: string
          first_name: string
          last_name: string
          email: string
          phone: string | null
          partner_id: number | null
          role: string
          created_at: string
          updated_at: string
        }
        Insert: {
          id: string
          first_name: string
          last_name: string
          email: string
          phone?: string | null
          partner_id?: number | null
          role?: string
          created_at?: string
          updated_at?: string
        }
        Update: {
          id?: string
          first_name?: string
          last_name?: string
          email?: string
          phone?: string | null
          partner_id?: number | null
          role?: string
          created_at?: string
          updated_at?: string
        }
      }
      user_settings: {
        Row: {
          id: string
          user_id: string
          email_notifications: boolean
          marketing_emails: boolean
          sms_notifications: boolean
          document_sharing: boolean
          dark_mode: boolean
          created_at: string
          updated_at: string
        }
        Insert: {
          id?: string
          user_id: string
          email_notifications?: boolean
          marketing_emails?: boolean
          sms_notifications?: boolean
          document_sharing?: boolean
          dark_mode?: boolean
          created_at?: string
          updated_at?: string
        }
        Update: {
          id?: string
          user_id?: string
          email_notifications?: boolean
          marketing_emails?: boolean
          sms_notifications?: boolean
          document_sharing?: boolean
          dark_mode?: boolean
          created_at?: string
          updated_at?: string
        }
      }
      wills: {
        Row: {
          id: string
          user_id: string
          status: string
          created_at: string
          updated_at: string
          submitted_at: string | null
          completed_at: string | null
        }
        Insert: {
          id?: string
          user_id: string
          status?: string
          created_at?: string
          updated_at?: string
          submitted_at?: string | null
          completed_at?: string | null
        }
        Update: {
          id?: string
          user_id?: string
          status?: string
          created_at?: string
          updated_at?: string
          submitted_at?: string | null
          completed_at?: string | null
        }
      }
      will_data: {
        Row: {
          id: string
          will_id: string
          section: string
          data: Json
          updated_at: string
        }
        Insert: {
          id?: string
          will_id: string
          section: string
          data: Json
          updated_at?: string
        }
        Update: {
          id?: string
          will_id?: string
          section?: string
          data?: Json
          updated_at?: string
        }
      }
      bookings: {
        Row: {
          id: string
          user_id: string
          advisor_id: string
          date_time: string
          duration: number
          service_type: 'will_consultation' | 'lpa_consultation' | 'general_advice'
          status: 'scheduled' | 'completed' | 'cancelled'
          video_link: string | null
          notes: string | null
        }
        Insert: {
          id?: string
          user_id: string
          advisor_id: string
          date_time: string
          duration?: number
          service_type: 'will_consultation' | 'lpa_consultation' | 'general_advice'
          status?: 'scheduled' | 'completed' | 'cancelled'
          video_link?: string | null
          notes?: string | null
        }
        Update: {
          id?: string
          user_id?: string
          advisor_id?: string
          date_time?: string
          duration?: number
          service_type?: 'will_consultation' | 'lpa_consultation' | 'general_advice'
          status?: 'scheduled' | 'completed' | 'cancelled'
          video_link?: string | null
          notes?: string | null
        }
      }
      documents: {
        Row: {
          id: string
          user_id: string
          will_id: string | null
          type: 'will_draft' | 'will_final' | 'lpa_draft' | 'lpa_final' | 'id_verification'
          storage_path: string
          created_at: string
          status: 'draft' | 'final'
        }
        Insert: {
          id?: string
          user_id: string
          will_id?: string | null
          type: 'will_draft' | 'will_final' | 'lpa_draft' | 'lpa_final' | 'id_verification'
          storage_path: string
          created_at?: string
          status: 'draft' | 'final'
        }
        Update: {
          id?: string
          user_id?: string
          will_id?: string | null
          type?: 'will_draft' | 'will_final' | 'lpa_draft' | 'lpa_final' | 'id_verification'
          storage_path?: string
          created_at?: string
          status?: 'draft' | 'final'
        }
      }
    }
    Views: {
      [_ in never]: never
    }
    Functions: {
      [_ in never]: never
    }
    Enums: {
      [_ in never]: never
    }
  }
}
