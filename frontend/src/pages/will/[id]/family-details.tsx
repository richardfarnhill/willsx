import { useState } from 'react';
import { useRouter } from 'next/router';
import { z } from 'zod';
import { v4 as uuidv4 } from 'uuid';
import { WillFormProvider, useWillForm } from '@/contexts/WillFormContext';
import ProtectedRoute from '@/components/ProtectedRoute';
import WillFormLayout from '@/components/will/WillFormLayout';
import { useFormValidation } from '@/hooks/useFormValidation';
import FormInput from '@/components/form/FormInput';
import FormSelect from '@/components/form/FormSelect';
import FormTextarea from '@/components/form/FormTextarea';
import Button from '@/components/ui/Button';
import Card from '@/components/ui/Card';
import { Child, Dependent, FamilyDetails, Spouse } from '@/types';

// Define schema for spouse
const spouseSchema = z.object({
  firstName: z.string().min(1, 'First name is required'),
  middleName: z.string().optional(),
  lastName: z.string().min(1, 'Last name is required'),
  dateOfBirth: z.string().regex(/^\d{4}-\d{2}-\d{2}$/, 'Please enter a valid date'),
}).optional();

// Define schema for child
const childSchema = z.object({
  id: z.string(),
  firstName: z.string().min(1, 'First name is required'),
  middleName: z.string().optional(),
  lastName: z.string().min(1, 'Last name is required'),
  dateOfBirth: z.string().regex(/^\d{4}-\d{2}-\d{2}$/, 'Please enter a valid date'),
  relationship: z.enum(['biological', 'adopted', 'step-child']),
});

// Define schema for dependent
const dependentSchema = z.object({
  id: z.string(),
  firstName: z.string().min(1, 'First name is required'),
  middleName: z.string().optional(),
  lastName: z.string().min(1, 'Last name is required'),
  dateOfBirth: z.string().regex(/^\d{4}-\d{2}-\d{2}$/, 'Please enter a valid date'),
  relationship: z.string().min(1, 'Relationship is required'),
  reasonForDependency: z.string().min(1, 'Reason for dependency is required'),
});

// Define schema for family details form
const familyDetailsSchema = z.object({
  spouse: spouseSchema,
  children: z.array(childSchema),
  dependents: z.array(dependentSchema),
});

type FamilyDetailsFormData = z.infer<typeof familyDetailsSchema>;

function FamilyDetailsForm() {
  const { willId, saveSection, getSectionData, currentStep } = useWillForm();
  
  // Get existing data or use defaults
  const existingData = getSectionData<FamilyDetailsFormData>('family-details');
  
  // Check if the user is married from personal details
  const personalDetails = getSectionData<any>('personal-details');
  const isMarried = personalDetails?.maritalStatus === 'married' || 
                  personalDetails?.maritalStatus === 'civil-partnership';
  
  // Initialize form with existing data or defaults
  const initialValues: FamilyDetailsFormData = {
    spouse: isMarried ? {
      firstName: '',
      middleName: '',
      lastName: '',
      dateOfBirth: '',
    } : undefined,
    children: [],
    dependents: [],
    ...existingData,
  };
  
  // Initialize form for validation
  const {
    values,
    errors,
    touched,
    handleChange,
    handleBlur,
    isValid,
    setValues,
    setValue,
  } = useFormValidation<FamilyDetailsFormData>({
    schema: familyDetailsSchema,
    initialValues,
    onSubmit: () => {}, // We'll handle submission separately
  });
  
  // State for the currently editing child/dependent
  const [editingChild, setEditingChild] = useState<Child | null>(null);
  const [editingDependent, setEditingDependent] = useState<Dependent | null>(null);
  const [showChildForm, setShowChildForm] = useState(false);
  const [showDependentForm, setShowDependentForm] = useState(false);
  
  // Save data when continuing
  const handleSave = async (): Promise<boolean> => {
    return await saveSection('family-details', values);
  };
  
  // Child management
  const addChild = (child: Omit<Child, 'id'>) => {
    const newChild = {
      ...child,
      id: editingChild?.id || uuidv4(),
    };
    
    if (editingChild) {
      // Update existing
      setValue(
        'children',
        values.children.map(c => (c.id === editingChild.id ? newChild : c))
      );
    } else {
      // Add new
      setValue('children', [...values.children, newChild]);
    }
    
    setEditingChild(null);
    setShowChildForm(false);
  };
  
  const removeChild = (id: string) => {
    setValue(
      'children',
      values.children.filter(child => child.id !== id)
    );
  };
  
  // Dependent management
  const addDependent = (dependent: Omit<Dependent, 'id'>) => {
    const newDependent = {
      ...dependent,
      id: editingDependent?.id || uuidv4(),
    };
    
    if (editingDependent) {
      // Update existing
      setValue(
        'dependents',
        values.dependents.map(d => (d.id === editingDependent.id ? newDependent : d))
      );
    } else {
      // Add new
      setValue('dependents', [...values.dependents, newDependent]);
    }
    
    setEditingDependent(null);
    setShowDependentForm(false);
  };
  
  const removeDependent = (id: string) => {
    setValue(
      'dependents',
      values.dependents.filter(dependent => dependent.id !== id)
    );
  };
  
  // Child form
  const renderChildForm = () => {
    const childValues = editingChild || {
      firstName: '',
      middleName: '',
      lastName: '',
      dateOfBirth: '',
      relationship: 'biological' as const,
    };
    
    const handleChildSubmit = (e: React.FormEvent) => {
      e.preventDefault();
      addChild(childValues);
    };
    
    return (
      <Card className="mb-6"></Card>
        <form onSubmit={handleChildSubmit}>
          <div className="p-6">
            <h3 className="text-lg font-medium mb-4">
              {editingChild ? 'Edit Child' : 'Add Child'}
            </h3>
            
            <div className="grid grid-cols-1 gap-4"></div>
              <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                <FormInput
                  label="First Name"
                  id="child-firstName"
                  value={childValues.firstName}
                  onChange={(e) => setEditingChild({ ...childValues, firstName: e.target.value })}
                  required
                />
                <FormInput
                  label="Middle Name"
                  id="child-middleName"
                  value={childValues.middleName || ''}
                  onChange={(e) => setEditingChild({ ...childValues, middleName: e.target.value })}
                />
              </div>
              
              <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                <FormInput
                  label="Last Name"
                  id="child-lastName"
                  value={childValues.lastName}
                  onChange={(e) => setEditingChild({ ...childValues, lastName: e.target.value })}
                  required
                />
                <FormInput
                  label="Date of Birth"
                  id="child-dateOfBirth"
                  type="date"
                  value={childValues.dateOfBirth}
                  onChange={(e) => setEditingChild({ ...childValues, dateOfBirth: e.target.value })}
                  required
                />
              </div>
              
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">Relationship</label>
                <div className="flex space-x-4">
                  {['biological', 'adopted', 'step-child'].map((rel) => (
                    <label key={rel} className="inline-flex items-center">
                      <input
                        type="radio"
                        className="form-radio"
                        name="relationship"
                        value={rel}
                        checked={childValues.relationship === rel}
                        onChange={() => setEditingChild({ ...childValues, relationship: rel as any })}
                      />
                      <span className="ml-2">
                        {rel === 'biological' ? 'Biological' : 
                         rel === 'adopted' ? 'Adopted' : 'Step Child'}
                      </span>
                    </label>
                  ))}
                </div>
              </div>
            </div>
            
            <div className="mt-6 flex justify-end space-x-3">
              <Button
                variant="outline"
                type="button"
                onClick={() => {
                  setEditingChild(null);
                  setShowChildForm(false);
                }}
              >
                Cancel
              </Button>
              <Button type="submit">
                {editingChild ? 'Update' : 'Add'} Child
              </Button>
            </div>
          </div>
        </form>
      </Card>
    );
  };
  
  // Dependent form
  const renderDependentForm = () => {
    const dependentValues = editingDependent || {
      firstName: '',
      middleName: '',
      lastName: '',
      dateOfBirth: '',
      relationship: '',
      reasonForDependency: '',
    };
    
    const handleDependentSubmit = (e: React.FormEvent) => {
      e.preventDefault();
      addDependent(dependentValues);
    };
    
    return (
      <Card className="mb-6">
        <form onSubmit={handleDependentSubmit}>
          <div className="p-6">
            <h3 className="text-lg font-medium mb-4">
              {editingDependent ? 'Edit Dependent' : 'Add Dependent'}
            </h3>
            
            <div className="grid grid-cols-1 gap-4">
              <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                <FormInput
                  label="First Name"
                  id="dependent-firstName"
                  value={dependentValues.firstName}
                  onChange={(e) => setEditingDependent({ ...dependentValues, firstName: e.target.value })}
                  required
                />
                <FormInput
                  label="Middle Name"
                  id="dependent-middleName"
                  value={dependentValues.middleName || ''}
                  onChange={(e) => setEditingDependent({ ...dependentValues, middleName: e.target.value })}
                />
              </div>
              
              <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                <FormInput
                  label="Last Name"
                  id="dependent-lastName"
                  value={dependentValues.lastName}
                  onChange={(e) => setEditingDependent({ ...dependentValues, lastName: e.target.value })}
                  required
                />
                <FormInput
                  label="Date of Birth"
                  id="dependent-dateOfBirth"
                  type="date"
                  value={dependentValues.dateOfBirth}
                  onChange={(e) => setEditingDependent({ ...dependentValues, dateOfBirth: e.target.value })}
                  required
                />
              </div>
              
              <FormInput
                label="Relationship"
                id="dependent-relationship"
                value={dependentValues.relationship}
                onChange={(e) => setEditingDependent({ ...dependentValues, relationship: e.target.value })}
                placeholder="e.g., Parent, Sibling, Friend"
                required
              />
              
              <FormTextarea
                label="Reason for Dependency"
                id="dependent-reasonForDependency"
                value={dependentValues.reasonForDependency}
                onChange={(e) => setEditingDependent({ ...dependentValues, reasonForDependency: e.target.value })}
                rows={3}
                placeholder="Please explain why this person is dependent on you"
                required
              />
            </div>
            
            <div className="mt-6 flex justify-end space-x-3"></div>
              <Button
                variant="outline"
                type="button"
                onClick={() => {
                  setEditingDependent(null);
                  setShowDependentForm(false);
                }}
              >
                Cancel
              </Button>
              <Button type="submit">
                {editingDependent ? 'Update' : 'Add'} Dependent
              </Button>
            </div>
          </div>
        </form>
      </Card>
    );
  };
  
  return (
    <WillFormLayout
      title="Family Details"
      description="Tell us about your family members so we can incorporate them into your will."
      onNext={handleSave}
      isValid={isValid}
    ></WillFormLayout>
      <div className="grid grid-cols-1 gap-6">
        {/* Spouse Section (if married) */}
        {isMarried && (
          <div className="border-b border-gray-200 pb-6"></div>
            <h3 className="text-lg font-medium mb-4">Spouse / Civil Partner</h3>
            
            <div className="grid grid-cols-1 gap-4">
              <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                <FormInput
                  label="First Name"
                  id="spouse.firstName"
                  name="spouse.firstName"
                  value={values.spouse?.firstName || ''}
                  onChange={handleChange}
                  onBlur={handleBlur}
                  error={touched.spouse?.firstName ? errors.spouse?.firstName : undefined}
                  required
                />
                
                <FormInput
                  label="Middle Name"
                  id="spouse.middleName"
                  name="spouse.middleName"
                  value={values.spouse?.middleName || ''}
                  onChange={handleChange}
                  onBlur={handleBlur}
                  error={touched.spouse?.middleName ? errors.spouse?.middleName : undefined}
                />
              </div>
              
              <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                <FormInput
                  label="Last Name"
                  id="spouse.lastName"
                  name="spouse.lastName"
                  value={values.spouse?.lastName || ''}
                  onChange={handleChange}
                  onBlur={handleBlur}
                  error={touched.spouse?.lastName ? errors.spouse?.lastName : undefined}
                  required
                />
                
                <FormInput
                  label="Date of Birth"
                  id="spouse.dateOfBirth"
                  name="spouse.dateOfBirth"
                  type="date"
                  value={values.spouse?.dateOfBirth || ''}
                  onChange={handleChange}
                  onBlur={handleBlur}
                  error={touched.spouse?.dateOfBirth ? errors.spouse?.dateOfBirth : undefined}
                  required
                />
              </div>
            </div>
          </div>
        )}
        
        {/* Children Section */}
        <div className="border-b border-gray-200 pb-6"></div>
          <div className="flex justify-between items-center mb-4">
            <h3 className="text-lg font-medium">Children</h3>
            <Button
              variant="outline"
              size="sm"
              onClick={() => {
                setEditingChild(null);
                setShowChildForm(true);
                setShowDependentForm(false);
              }}
            >
              Add Child
            </Button>
          </div>
          
          {showChildForm && renderChildForm()}
          
          {values.children.length > 0 ? (
            <div className="space-y-4">
              {values.children.map((child) => (
                <Card key={child.id} className="overflow-visible"></Card>
                  <div className="p-4 flex justify-between items-center">
                    <div>
                      <h4 className="font-medium">
                        {child.firstName} {child.middleName} {child.lastName}
                      </h4>
                      <div className="text-sm text-gray-500">
                        <span className="inline-block mr-3">
                          DOB: {new Date(child.dateOfBirth).toLocaleDateString()}
                        </span>
                        <span className="inline-block">
                          {child.relationship === 'biological' ? 'Biological Child' :
                           child.relationship === 'adopted' ? 'Adopted Child' : 'Step Child'}
                        </span>
                      </div>
                    </div>
                    <div className="flex gap-2">
                      <Button
                        size="sm"
                        variant="outline"
                        onClick={() => {
                          setEditingChild(child);
                          setShowChildForm(true);
                          setShowDependentForm(false);
                        }}
                      >
                        Edit
                      </Button>
                      <Button
                        size="sm"
                        variant="danger"
                        onClick={() => removeChild(child.id)}
                      >
                        Remove
                      </Button>
                    </div>
                  </div>
                </Card>
              ))}
            </div>
          ) : (
            <p className="text-gray-500 italic">No children added yet.</p>
          )}
        </div>
        
        {/* Dependents Section */}
        <div>
          <div className="flex justify-between items-center mb-4">
            <h3 className="text-lg font-medium">Other Dependents</h3>
            <Button
              variant="outline"
              size="sm"
              onClick={() => {
                setEditingDependent(null);
                setShowDependentForm(true);
                setShowChildForm(false);
              }}
            ></Button>
              Add Dependent
            </Button>
          </div>
          
          {showDependentForm && renderDependentForm()}
          
          {values.dependents.length > 0 ? (
            <div className="space-y-4"></div>
              {values.dependents.map((dependent) => (
                <Card key={dependent.id} className="overflow-visible"></Card>
                  <div className="p-4 flex justify-between items-center">
                    <div>
                      <h4 className="font-medium">
                        {dependent.firstName} {dependent.middleName} {dependent.lastName}
                      </h4>
                      <div className="text-sm text-gray-500">
                        <span className="inline-block mr-3">
                          DOB: {new Date(dependent.dateOfBirth).toLocaleDateString()}
                        </span>
                        <span className="inline-block">
                          Relationship: {dependent.relationship}
                        </span>
                      </div>
                      <p className="text-sm text-gray-600 mt-1">
                        {dependent.reasonForDependency}
                      </p>
                    </div>
                    <div className="flex gap-2">
                      <Button
                        size="sm"
                        variant="outline"
                        onClick={() => {
                          setEditingDependent(dependent);
                          setShowDependentForm(true);
                          setShowChildForm(false);
                        }}
                      ></Button>
                        Edit
                      </Button>
                      <Button
                        size="sm"
                        variant="danger"
                        onClick={() => removeDependent(dependent.id)}
                      >
                        Remove
                      </Button>
                    </div>
                  </div>
                </Card>
              ))}
            </div>
          ) : (
            <p className="text-gray-500 italic">No dependents added yet.</p>
          )}
        </div>
      </div>
    </WillFormLayout>
  );
}

export default function FamilyDetailsPage() {
  const router = useRouter();
  const { id } = router.query;
  
  // Ensure we have a valid ID
  if (!id || typeof id !== 'string') {
    return null;
  }
  
  return (
    <ProtectedRoute>
      <WillFormProvider willId={id}>
        <FamilyDetailsForm />
      </WillFormProvider>
    </ProtectedRoute>
  );
}