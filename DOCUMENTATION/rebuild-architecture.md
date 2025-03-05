# WillsX: Modern Architecture Blueprint

## Proposed Technical Stack

### Frontend
- **Framework**: Next.js 14+ with App Router
- **UI Library**: Tailwind CSS with custom components
- **State Management**: React Context + React Query
- **Form Management**: React Hook Form with Zod validation

### Backend
- **API Framework**: Next.js API routes or Express.js
- **Database**: PostgreSQL
- **ORM**: Prisma
- **Authentication**: NextAuth.js or Clerk
- **File Storage**: S3-compatible service

### DevOps
- **CI/CD**: GitHub Actions
- **Hosting**: Vercel or similar platform
- **Environment Management**: Docker for local development

## Core System Components

### 1. Partner Management System
```typescript
// Partner data model
interface Partner {
  id: string;
  name: string;
  logo: string;
  brandColors: {
    primary: string;
    secondary: string;
  };
  contactInfo: {
    email: string;
    phone: string;
    website: string;
  };
  referralSettings: {
    fee: number;
    paymentTerms: string;
  };
  landingPageSettings: {
    heading: string;
    subheading: string;
    features: Feature[];
  };
  createdAt: Date;
  updatedAt: Date;
}
```

### 2. Will Instruction Process
The will creation process would be implemented as a multi-step form with proper state management:

```typescript
// Form state management
interface WillFormState {
  currentStep: number;
  steps: StepStatus[];
  personalDetails: PersonalDetails;
  familyDetails: FamilyDetails;
  assets: Assets;
  beneficiaries: Beneficiary[];
  executors: Executor[];
  specialWishes: SpecialWishes;
  partnerId?: string; // For referral tracking
}
```

### 3. Booking System
```typescript
// Booking data model
interface Booking {
  id: string;
  clientId: string;
  advisorId: string;
  service: ServiceType;
  dateTime: Date;
  duration: number;
  status: BookingStatus;
  videoConferenceLink?: string;
  notes?: string;
  createdAt: Date;
  updatedAt: Date;
}
```

## API Architecture

### RESTful Endpoints
```
/api/v1/partners
/api/v1/partners/:id
/api/v1/partners/:id/landing-page

/api/v1/users
/api/v1/users/:id
/api/v1/users/:id/wills

/api/v1/wills
/api/v1/wills/:id
/api/v1/wills/:id/documents

/api/v1/bookings
/api/v1/bookings/:id
```

### Authentication Flow
1. User registration/login via credentials or OAuth providers
2. JWT token issuance with appropriate scopes
3. Token refresh mechanism
4. Role-based access control for different user types

## Database Schema

### Core Tables
- users
- partners
- wills
- will_documents
- bookings
- advisors
- beneficiaries
- executors
- assets

### Relationships
- Users to Wills: One-to-many
- Partners to Users: One-to-many (for referrals)
- Users to Bookings: One-to-many
- Advisors to Bookings: One-to-many

## Integration Strategy

### CRM Integration (HubSpot)
- API-based integration
- Webhook listeners for two-way sync
- Contact creation on user registration
- Deal creation on will completion

### Project Management (ClickUp)
- Task creation via API on key events
- Status updates on milestone completion
- Document linkage for completed wills

### Payment Processing
- Integration with payment provider APIs
- Webhook listeners for payment events
- Invoice generation and tracking

## Migration Strategy

### Data Migration
1. Export WordPress data to JSON format
2. Transform data to match new schema
3. Import to new database with validation
4. Verify data integrity

### Content Migration
1. Export WordPress content
2. Convert to Markdown/MDX format
3. Implement in new content system

### User Migration
1. Export user accounts (excluding passwords)
2. Email users with password reset instructions
3. Provide migration support

## Performance Considerations

- Server-side rendering for key pages
- Static generation for content pages
- Edge functions for global performance
- Optimized image delivery
- Proper caching strategy

## Security Implementation

- CSRF protection
- XSS prevention
- Rate limiting
- Input validation
- Proper authentication/authorization
- Data encryption
- Regular security audits

## Testing Strategy

- Unit tests for core business logic
- Integration tests for API endpoints
- E2E tests for critical user flows
- Visual regression testing
- Performance monitoring
