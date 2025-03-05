# Supabase Integration for WillsX

## Overview

This document outlines how Supabase is integrated into the WillsX headless architecture to provide modern database functionality, authentication, and storage capabilities.

## Architecture with Supabase

```
┌─────────────────┐      ┌─────────────────┐      ┌─────────────────┐
│                 │      │                 │      │                 │
│    WordPress    │◄────►│    Next.js      │◄────►│    Supabase     │
│    Backend      │      │    Frontend     │      │    Backend      │
│                 │      │                 │      │                 │
└─────────────────┘      └─────────────────┘      └─────────────────┘
        ▲                                                 ▲
        │                                                 │
        ▼                                                 ▼
┌─────────────────┐                              ┌─────────────────┐
│                 │                              │                 │
│    WP MySQL     │                              │   Storage       │
│    Database     │                              │   (Files)       │
│                 │                              │                 │
└─────────────────┘                              └─────────────────┘
```

## Responsibility Split

### WordPress
- Content management (blog posts, static pages)
- Admin interface for partners and content
- Basic API endpoints for content delivery

### Supabase
- User authentication and management
- Will instruction data storage
- Booking system data
- Document storage
- Real-time updates
- Row-level security for data access control

### Next.js
- Frontend UI/UX
- Form state management
- Integration with both WordPress and Supabase
- Client-side rendering and interaction

## Supabase Database Schema

### Tables

1. **users**
   - id (UUID, primary key)
   - email (string, unique)
   - first_name (string)
   - last_name (string)
   - phone (string)
   - created_at (timestamp)
   - partner_id (integer, foreign key to WordPress partners)

2. **wills**
   - id (UUID, primary key)
   - user_id (UUID, foreign key to users)
   - status (string: draft, submitted, in_review, completed)
   - created_at (timestamp)
   - updated_at (timestamp)
   - submitted_at (timestamp)
   - completed_at (timestamp)

3. **will_data**
   - id (UUID, primary key)
   - will_id (UUID, foreign key to wills)
   - section (string: personal, family, assets, beneficiaries, executors, wishes)
   - data (JSONB)
   - updated_at (timestamp)

4. **bookings**
   - id (UUID, primary key)
   - user_id (UUID, foreign key to users)
   - advisor_id (string, reference to WordPress advisor ID)
   - date_time (timestamp)
   - duration (integer, minutes)
   - service_type (string: will_consultation, lpa_consultation, general_advice)
   - status (string: scheduled, completed, cancelled)
   - video_link (string)
   - notes (text)

5. **documents**
   - id (UUID, primary key)
   - user_id (UUID, foreign key to users)
   - will_id (UUID, foreign key to wills, nullable)
   - type (string: will_draft, will_final, lpa_draft, lpa_final, id_verification)
   - storage_path (string)
   - created_at (timestamp)
   - status (string: draft, final)

## Authentication Flow

1. **Registration**
   - User signs up through Next.js interface
   - Supabase creates user account
   - WordPress receives user data for CRM syncing

2. **Login**
   - User logs in via Supabase Auth
   - JWT passed to Next.js client
   - Authenticated requests to both Supabase and WordPress API

3. **Partner-referred Registration**
   - Partner slug captured from URL
   - User registers as normal
   - Partner relationship recorded in user metadata

## Data Flow Examples

### Will Creation Process
1. User authenticates via Supabase Auth
2. Each step of will form saved to will_data table in real-time
3. Form state managed by Next.js, persisted in Supabase
4. Completed will triggers WordPress API call for admin processing

### Document Generation
1. User completes will form
2. API request to WordPress to generate document
3. Document stored in Supabase storage
4. Document metadata saved to documents table
5. User notified of completion

## Security Implementation

1. **Row Level Security (RLS)**
   - Users can only access their own data
   - Admin roles for staff access
   - Partner-specific access policies

2. **API Security**
   - Supabase JWT validation
   - Rate limiting through Supabase
   - Input validation on both client and server

3. **Data Encryption**
   - Sensitive data encrypted at rest
   - Secure transit via HTTPS
   - Document access control via signed URLs

## Implementation Strategy

1. **Set up Supabase project**
   - Create tables and relationships
   - Configure authentication providers
   - Establish RLS policies
   - Set up storage buckets

2. **Implement Next.js integration**
   - Supabase client configuration
   - Auth context provider
   - API hooks for data access
   - Form state persistence

3. **Connect WordPress with Supabase**
   - User synchronization
   - Webhook triggers for key events
   - Document processing workflow
