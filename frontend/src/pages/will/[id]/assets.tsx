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
import { Asset } from '@/types';

// Define asset schema
const assetSchema = z.object({
  id: z.string(),
  type: z.enum(['property', 'bank-account', 'investment', 'vehicle', 'valuable', 'other']),
  description: z.string().min(1, 'Description is required'),
  value: z.number().min(0, 'Value must be 0 or higher'),
  ownership: z.enum(['sole', 'joint', 'tenants-in-common']),
  location: z.string().optional(),
  reference: z.string().optional(),
});

// Define assets form schema
const assetsFormSchema = z.object({
  assets: z.array(assetSchema),
  totalEstimatedValue: z.number(),
});

type AssetsFormData = z.infer<typeof assetsFormSchema>;

// Asset type metadata for UI
const ASSET_TYPES = [
  { value: 'property', label: 'Property', icon: '🏠' },
  { value: 'bank-account', label: 'Bank Account', icon: '🏦' },
  { value: 'investment', label: 'Investment', icon: '📈' },
  { value: 'vehicle', label: 'Vehicle', icon: '🚗' },
  { value: 'valuable', label: 'Valuable Item', icon: '💎' },
  { value: 'other', label: 'Other Asset', icon: '📦' },
];

// Ownership type options
const OWNERSHIP_TYPES = [
  { value: 'sole', label: 'Sole Ownership' },
  { value: 'joint', label: 'Joint Ownership' },
  { value: 'tenants-in-common', label: 'Tenants in Common' },
];

function AssetsForm() {
  const { saveSection, getSectionData } = useWillForm();
  
  // Get existing data or use defaults
  const existingData = getSectionData<AssetsFormData>('assets');
  
  // Initialize form with existing data or defaults
  const initialValues: AssetsFormData = {
    assets: [],
    totalEstimatedValue: 0,
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
  } = useFormValidation<AssetsFormData>({
    schema: assetsFormSchema,
    initialValues,
    onSubmit: () => {}, // We'll handle submission separately
  });
  
  // State for the currently editing asset
  const [editingAsset, setEditingAsset] = useState<Asset | null>(null);
  const [showAssetForm, setShowAssetForm] = useState(false);
  const [assetTypeFilter, setAssetTypeFilter] = useState<string | null>(null);
  
  // Calculate total estate value
  const updateTotalValue = (assets: Asset[]) => {
    const total = assets.reduce((sum, asset) => sum + asset.value, 0);
    setValue('totalEstimatedValue', total);
  };
  
  // Save data when continuing
  const handleSave = async (): Promise<boolean> => {
    return await saveSection('assets', values);
  };
  
  // Asset management
  const addAsset = (asset: Omit<Asset, 'id'>) => {
    const newAsset = {
      ...asset,
      id: editingAsset?.id || uuidv4(),
    };
    
    let updatedAssets: Asset[];
    
    if (editingAsset) {
      // Update existing
      updatedAssets = values.assets.map(a => (a.id === editingAsset.id ? newAsset : a));
    } else {
      // Add new
      updatedAssets = [...values.assets, newAsset];
    }
    
    setValue('assets', updatedAssets);
    updateTotalValue(updatedAssets);
    
    setEditingAsset(null);
    setShowAssetForm(false);
  };
  
  const removeAsset = (id: string) => {
    const updatedAssets = values.assets.filter(asset => asset.id !== id);
    setValue('assets', updatedAssets);
    updateTotalValue(updatedAssets);
  };
  
  // Asset form
  const renderAssetForm = () => {
    const [assetValues, setAssetValues] = useState<Partial<Asset>>(
      editingAsset || {
        type: 'property',
        description: '',
        value: 0,
        ownership: 'sole',
        location: '',
        reference: '',
      }
    );
    
    const handleAssetSubmit = (e: React.FormEvent) => {
      e.preventDefault();
      addAsset(assetValues as Asset);
    };
    
    const updateAssetValue = (key: keyof Asset, value: any) => {
      setAssetValues({ ...assetValues, [key]: value });
    };
    
    return (
      <Card className="mb-6">
        <form onSubmit={handleAssetSubmit}>
          <div className="p-6">
            <h3 className="text-lg font-medium mb-4">
              {editingAsset ? 'Edit Asset' : 'Add Asset'}
            </h3>
            
            <div className="grid grid-cols-1 gap-4">
              <FormSelect
                label="Asset Type"
                id="asset-type"
                value={assetValues.type}
                onChange={(e) => updateAssetValue('type', e.target.value)}
                options={ASSET_TYPES.map(type => ({ value: type.value, label: `${type.icon} ${type.label}` }))}
                required
              />
              
              <FormInput
                label="Description"
                id="asset-description"
                value={assetValues.description || ''}
                onChange={(e) => updateAssetValue('description', e.target.value)}
                placeholder={`e.g., ${
                  assetValues.type === 'property' ? '123 Main Street, Apartment' :
                  assetValues.type === 'bank-account' ? 'Savings Account at Barclays' :
                  assetValues.type === 'investment' ? 'Stocks in Apple Inc.' :
                  assetValues.type === 'vehicle' ? '2018 Toyota Camry' :
                  assetValues.type === 'valuable' ? 'Diamond Engagement Ring' :
                  'Premium Bonds'
                }`}
                required
              />
              
              <FormInput
                label="Estimated Value (£)"
                id="asset-value"
                type="number"
                value={assetValues.value?.toString() || '0'}
                onChange={(e) => updateAssetValue('value', parseFloat(e.target.value) || 0)}
                min="0"
                step="1"
                required
              />
              
              <FormSelect
                label="Ownership"
                id="asset-ownership"
                value={assetValues.ownership}
                onChange={(e) => updateAssetValue('ownership', e.target.value)}
                options={OWNERSHIP_TYPES}
                required
              />
              
              <FormInput
                label="Location"
                id="asset-location"
                value={assetValues.location || ''}
                onChange={(e) => updateAssetValue('location', e.target.value)}
                placeholder="Where the asset is located or stored"
              />
              
              <FormInput
                label="Reference"
                id="asset-reference"
                value={assetValues.reference || ''}
                onChange={(e) => updateAssetValue('reference', e.target.value)}
                placeholder="Account number, registration, serial number, etc."
              />
            </div>
            
            <div className="mt-6 flex justify-end space-x-3">
              <Button
                variant="outline"
                type="button"
                onClick={() => {
                  setEditingAsset(null);
                  setShowAssetForm(false);
                }}
              >
                Cancel
              </Button>
              <Button type="submit">
                {editingAsset ? 'Update' : 'Add'} Asset
              </Button>
            </div>
          </div>
        </form>
      </Card>
    );
  };
  
  const filteredAssets = assetTypeFilter
    ? values.assets.filter(asset => asset.type === assetTypeFilter)
    : values.assets;
  
  return (
    <WillFormLayout
      title="Your Assets"
      description="List the assets you'd like to include in your will."
      onNext={handleSave}
      isValid={isValid}
    >
      <div className="space-y-6">
        {/* Asset overview card */}
        <Card>
          <div className="p-6">
            <div className="flex justify-between items-center mb-4">
              <h3 className="text-lg font-medium">Estate Summary</h3>
              <div className="text-xl font-bold">
                £{values.totalEstimatedValue.toLocaleString()}
              </div>
            </div>
            
            <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
              {ASSET_TYPES.map(type => {
                const count = values.assets.filter(a => a.type === type.value).length;
                const total = values.assets
                  .filter(a => a.type === type.value)
                  .reduce((sum, asset) => sum + asset.value, 0);
                
                return (
                  <button
                    key={type.value}
                    onClick={() => setAssetTypeFilter(
                      assetTypeFilter === type.value ? null : type.value
                    )}
                    className={`p-3 rounded-lg border text-center transition ${
                      assetTypeFilter === type.value
                        ? 'bg-blue-50 border-blue-300'
                        : 'bg-white border-gray-200 hover:bg-gray-50'
                    }`}
                  >
                    <div className="text-2xl mb-1">{type.icon}</div>
                    <div className="text-sm font-medium">{type.label}</div>
                    <div className="text-xs text-gray-500">{count} items</div>
                    {count > 0 && (
                      <div className="text-xs font-medium mt-1">£{total.toLocaleString()}</div>
                    )}
                  </button>
                );
              })}
            </div>
          </div>
        </Card>
        
        {/* Asset list */}
        <div>
          <div className="flex justify-between items-center mb-4">
            <h3 className="text-lg font-medium">