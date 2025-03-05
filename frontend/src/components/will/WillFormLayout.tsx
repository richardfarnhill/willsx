import React, { ReactNode } from 'react';
import { useRouter } from 'next/router';
import Layout from '@/components/Layout';
import { WILL_STEPS, useWillForm } from '@/contexts/WillFormContext';
import Button from '@/components/ui/Button';
import Card from '@/components/ui/Card';
import LoadingSpinner from '@/components/LoadingSpinner';

interface WillFormLayoutProps {
  children: ReactNode;
  title: string;
  description?: string;
  showProgress?: boolean;
  backButtonLabel?: string;
  nextButtonLabel?: string;
  onNext?: () => Promise<boolean> | boolean;
  onBack?: () => void;
  isValid?: boolean;
}

const WillFormLayout: React.FC<WillFormLayoutProps> = ({
  children,
  title,
  description,
  showProgress = true,
  backButtonLabel = 'Previous',
  nextButtonLabel = 'Continue',
  onNext,
  onBack,
  isValid = true,
}) => {
  const { 
    willId, 
    progress, 
    stepIndex, 
    totalSteps, 
    goToPrevStep,
    goToNextStep,
    isLoading 
  } = useWillForm();
  const router = useRouter();
  const [isSubmitting, setIsSubmitting] = React.useState(false);
  
  const handleNext = async () => {
    if (!isValid) return;
    
    setIsSubmitting(true);
    try {
      const canProceed = onNext ? await onNext() : true;
      if (canProceed) {
        goToNextStep();
      }
    } finally {
      setIsSubmitting(false);
    }
  };
  
  const handleBack = () => {
    if (onBack) {
      onBack();
    } else {
      goToPrevStep();
    }
  };
  
  // Determine if this is the first or last step
  const isFirstStep = stepIndex === 0;
  const isLastStep = stepIndex === totalSteps - 1;
  
  if (isLoading) {
    return (
      <Layout title={`${title} | WillsX`} authRequired={true}>
        <div className="flex items-center justify-center h-64">
          <LoadingSpinner size="lg" text="Loading..." />
        </div>
      </Layout>
    );
  }
  
  return (
    <Layout title={`${title} | WillsX`} authRequired={true}>
      <div className="max-w-4xl mx-auto">
        {/* Progress indicator */}
        {showProgress && (
          <div className="mb-8">
            <div className="flex justify-between items-center mb-2">
              <div className="text-sm font-medium text-gray-500">
                Step {stepIndex + 1} of {totalSteps}
              </div>
              <div className="text-sm font-medium text-gray-500">
                {progress}% Complete
              </div>
            </div>
            <div className="w-full bg-gray-200 rounded-full h-2.5">
              <div 
                className="bg-blue-600 h-2.5 rounded-full transition-all duration-300 ease-in-out" 
                style={{ width: `${progress}%` }}
              ></div>
            </div>
          </div>
        )}
        
        {/* Step navigation tabs */}
        <div className="hidden lg:flex mb-6 border-b border-gray-200">
          {WILL_STEPS.map((step, index) => (
            <button
              key={step.id}
              className={`px-4 py-2 border-b-2 text-sm font-medium ${
                index === stepIndex
                  ? 'border-blue-500 text-blue-600'
                  : index < stepIndex
                  ? 'border-transparent text-gray-500 hover:text-gray-700'
                  : 'border-transparent text-gray-400 cursor-not-allowed'
              }`}
              onClick={() => {
                // Only allow navigation to completed steps or current step
                if (index <= stepIndex && willId) {
                  router.push(step.path(willId));
                }
              }}
              disabled={index > stepIndex}
            >
              {index + 1}. {step.title}
            </button>
          ))}
        </div>
        
        {/* Main content */}
        <Card className="mb-8">
          <div className="p-6">
            <h1 className="text-2xl font-bold text-gray-900 mb-2">{title}</h1>
            {description && <p className="text-gray-600 mb-6">{description}</p>}
            
            <div className="space-y-6">
              {children}
            </div>
          </div>
        </Card>
        
        {/* Navigation buttons */}
        <div className="flex justify-between mb-12">
          <Button
            variant="outline"
            onClick={handleBack}
            disabled={isFirstStep || isSubmitting}
          >
            {backButtonLabel}
          </Button>
          
          <Button
            onClick={handleNext}
            disabled={!isValid || isSubmitting}
            isLoading={isSubmitting}
          >
            {isLastStep ? 'Submit' : nextButtonLabel}
          </Button>
        </div>
      </div>
    </Layout>
  );
};

export default WillFormLayout;
