import { useEffect, useState } from 'react';
import { useRouter } from 'next/router';
import { z } from 'zod';
import { useAuth } from '@/contexts/AuthContext';
import { useWillForm, WillFormProvider } from '@/contexts/WillFormContext';
import ProtectedRoute from '@/components/ProtectedRoute';
import WillFormLayout from '@/components/will/WillFormLayout';
import { useFormValidation } from '@/hooks/useFormValidation';
import FormInput from '@/components/form/FormInput';
import FormSelect from '@/components/form/FormSelect';
import { PersonalDetails } from '@/types';

// Define validation schema
const personalDetailsSchema = z.object({
  firstName: z.string().min(1, 'First name is required'),
  middleName: z.string().optional(),
  lastName: z.string().min(1, 'Last name is required'),
  maidenName: z.string().optional(),
  dateOfBirth: z.string().regex(/^\d{4}-\d{2}-\d{2}$/, 'Please enter a valid date'),
  maritalStatus: z.enum(['single', 'married', 'divorced', 'widowed', 'civil-partnership', 'separated']),
  address: z.object({
    line1: z.string().min(1, 'Address line 1 is required'),
    line2: z.string().optional(),
    city: z.string().min(1, 'City is required'),
    county: z.string().optional(),
    postcode: z.string().min(1, 'Postcode is required').regex(/^[A-Z]{1,2}[0-9][A-Z0-9]? ?[0-9][A-Z]{2}$/i, 'Please enter a valid UK postcode'),
    country: z.string().min(1, 'Country is required'),
  }),
  contactDetails: z.object({
    email: z.string().email('Please enter a valid email address'),
    phone: z.string().min(1, 'Phone number is required'),
    alternativePhone: z.string().optional(),
  }),
});

type PersonalDetailsFormData = z.infer<typeof personalDetailsSchema>;

function PersonalDetailsForm() {
  const { willId, saveSection, getSectionData } = useWillForm();
  const [isInitialized, setIsInitialized] = useState(false);
  
  // Get existing data or use defaults
  const existingData = getSectionData<PersonalDetailsFormData>('personal-details');
  
  // Initialize form with user data or defaults
  const initialValues: PersonalDetailsFormData = {
    firstName: '',
    middleName: '',
    lastName: '',
    maidenName: '',
    dateOfBirth: '',
    maritalStatus: 'single',
    address: {
      line1: '',
      line2: '',
      city: '',
      county: '',
      postcode: '',
      country: 'United Kingdom',
    },
    contactDetails: {
      email: '',
      phone: '',
      alternativePhone: '',
    },
    ...existingData,
  };
  
  // Initialize form
  const {
    values,
    errors,
    touched,
    handleChange,
    handleBlur,
    isValid,
    setValues,
  } = useFormValidation<PersonalDetailsFormData>({
    schema: personalDetailsSchema,
    initialValues,
    onSubmit: () => {}, // We'll handle submission separately
  });
  
  // Prefill with user data if available
  const { user, userDetails } = useAuth();
  
  useEffect(() => {
    if (userDetails && !isInitialized && !existingData) {
      setValues({
        ...values,
        firstName: userDetails.first_name || '',
        lastName: userDetails.last_name || '',
        contactDetails: {
          ...values.contactDetails,
          email: user?.email || '',
          phone: userDetails.phone || '',
        },
      });
      setIsInitialized(true);
    }
  }, [userDetails, user, setValues, values, isInitialized, existingData]);
  
  // Save data when continuing
  const handleSave = async (): Promise<boolean> => {
    return await saveSection('personal-details', values);
  };
  
  const maritalStatusOptions = [
    { value: 'single', label: 'Single' },
    { value: 'married', label: 'Married' },
    { value: 'divorced', label: 'Divorced' },
    { value: 'widowed', label: 'Widowed' },
    { value: 'civil-partnership', label: 'Civil Partnership' },
    { value: 'separated', label: 'Separated' },
  ];
  
  return (
    <WillFormLayout
      title="Personal Details"
      description="Please provide your personal information for your will."
      onNext={handleSave}
      isValid={isValid}
    >
      <div className="grid grid-cols-1 gap-6">
        {/* Name fields */}
        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
          <FormInput
            label="First Name"
            id="firstName"
            name="firstName"
            value={values.firstName}
            onChange={handleChange}
            onBlur={handleBlur}
            error={touched.firstName ? errors.firstName : undefined}
            required
          />
          
          <FormInput
            label="Middle Name(s)"
            id="middleName"
            name="middleName"
            value={values.middleName}
            onChange={handleChange}
            onBlur={handleBlur}
            error={touched.middleName ? errors.middleName : undefined}
            helpText="Optional"
          />
        </div>
        
        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
          <FormInput
            label="Last Name"
            id="lastName"
            name="lastName"
            value={values.lastName}
            onChange={handleChange}
            onBlur={handleBlur}
            error={touched.lastName ? errors.lastName : undefined}
            required
          />
          
          <FormInput
            label="Maiden Name"
            id="maidenName"
            name="maidenName"
            value={values.maidenName}
            onChange={handleChange}
            onBlur={handleBlur}
            error={touched.maidenName ? errors.maidenName : undefined}
            helpText="If applicable"
          />
        </div>
        
        {/* Date of birth and Marital status */}
        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
          <FormInput
            label="Date of Birth"
            id="dateOfBirth"
            name="dateOfBirth"
            type="date"
            value={values.dateOfBirth}
            onChange={handleChange}
            onBlur={handleBlur}
            error={touched.dateOfBirth ? errors.dateOfBirth : undefined}
            required
          />
          
          <FormSelect
            label="Marital Status"
            id="maritalStatus"
            name="maritalStatus"
            value={values.maritalStatus}
            onChange={handleChange}
            onBlur={handleBlur}
            options={maritalStatusOptions}
            error={touched.maritalStatus ? errors.maritalStatus : undefined}
            required
          />
        </div>
        
        {/* Address section */}
        <div className="border-t border-gray-200 pt-6 mt-2">
          <h3 className="text-lg font-medium mb-4">Current Address</h3>
          
          <div className="space-y-4">
            <FormInput
              label="Address Line 1"
              id="address.line1"
              name="address.line1"
              value={values.address.line1}
              onChange={handleChange}
              onBlur={handleBlur}
              error={touched.address?.line1 ? errors.address?.line1 : undefined}
              required
            />
            
            <FormInput
              label="Address Line 2"
              id="address.line2"
              name="address.line2"
              value={values.address.line2}
              onChange={handleChange}
              onBlur={handleBlur}
              error={touched.address?.line2 ? errors.address?.line2 : undefined}
            />
            
            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
              <FormInput
                label="City"
                id="address.city"
                name="address.city"
                value={values.address.city}
                onChange={handleChange}
                onBlur={handleBlur}
                error={touched.address?.city ? errors.address?.city : undefined}
                required
              />
              
              <FormInput
                label="County"
                id="address.county"
                name="address.county"
                value={values.address.county}
                onChange={handleChange}
                onBlur={handleBlur}
                error={touched.address?.county ? errors.address?.county : undefined}
              />
            </div>
            
            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
              <FormInput
                label="Postcode"
                id="address.postcode"
                name="address.postcode"
                value={values.address.postcode}
                onChange={handleChange}
                onBlur={handleBlur}
                error={touched.address?.postcode ? errors.address?.postcode : undefined}
                required
              />
              
              <FormInput
                label="Country"
                id="address.country"
                name="address.country"
                value={values.address.country}
                onChange={handleChange}
                onBlur={handleBlur}
                error={touched.address?.country ? errors.address?.country : undefined}
                required
              />
            </div>
          </div>
        </div>
        
        {/* Contact details section */}
        <div className="border-t border-gray-200 pt-6">
          <h3 className="text-lg font-medium mb-4">Contact Details</h3>
          
          <div className="space-y-4">
            <FormInput
              label="Email Address"
              id="contactDetails.email"
              name="contactDetails.email"
              type="email"
              value={values.contactDetails.email}
              onChange={handleChange}
              onBlur={handleBlur}
              error={touched.contactDetails?.email ? errors.contactDetails?.email : undefined}
              required
            />
            
            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
              <FormInput
                label="Phone Number"
                id="contactDetails.phone"
                name="contactDetails.phone"
                type="tel"
                value={values.contactDetails.phone}
                onChange={handleChange}
                onBlur={handleBlur}
                error={touched.contactDetails?.phone ? errors.contactDetails?.phone : undefined}
                required
              />
              
              <FormInput
                label="Alternative Phone"
                id="contactDetails.alternativePhone"
                name="contactDetails.alternativePhone"
                type="tel"
                value={values.contactDetails.alternativePhone}
                onChange={handleChange}
                onBlur={handleBlur}
                error={touched.contactDetails?.alternativePhone ? errors.contactDetails?.alternativePhone : undefined}
                helpText="Optional"
              />
            </div>
          </div>
        </div>
      </div>
    </WillFormLayout>
  );
}

export default function PersonalDetailsPage() {
  const router = useRouter();
  const { id } = router.query;
  
  // Ensure we have a valid ID
  if (!id || typeof id !== 'string') {
    return null;
  }
  
  return (
    <ProtectedRoute>
      <WillFormProvider willId={id}>
        <PersonalDetailsForm />
      </WillFormProvider>
    </ProtectedRoute>
  );
}
