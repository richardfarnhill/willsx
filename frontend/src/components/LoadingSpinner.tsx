import React from 'react';

interface LoadingSpinnerProps {
  size?: 'sm' | 'md' | 'lg';
  color?: 'primary' | 'white' | 'gray';
  className?: string;
  text?: string;
  fullPage?: boolean;
}

const LoadingSpinner: React.FC<LoadingSpinnerProps> = ({
  size = 'md',
  color = 'primary',
  className = '',
  text,
  fullPage = false,
}) => {
  // Determine spinner size
  const sizeClasses = {
    sm: 'h-4 w-4',
    md: 'h-8 w-8',
    lg: 'h-12 w-12',
  }[size];

  // Determine color
  const colorClasses = {
    primary: 'text-blue-600',
    white: 'text-white',
    gray: 'text-gray-500',
  }[color];

  // Base spinner component
  const spinnerElement = (
    <svg 
      className={`animate-spin ${sizeClasses} ${colorClasses} ${className}`}
      xmlns="http://www.w3.org/2000/svg" 
      fill="none" 
      viewBox="0 0 24 24"
      aria-hidden="true"
    >
      <circle 
        className="opacity-25" 
        cx="12" 
        cy="12" 
        r="10" 
        stroke="currentColor" 
        strokeWidth="4"
      />
      <path 
        className="opacity-75" 
        fill="currentColor" 
        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
      />
    </svg>
  );

  // If fullPage is true, center in the viewport
  if (fullPage) {
    return (
      <div className="fixed inset-0 flex items-center justify-center bg-white bg-opacity-75 z-50">
        <div className="text-center">
          {spinnerElement}
          {text && <p className="mt-3 text-sm text-gray-600">{text}</p>}
        </div>
      </div>
    );
  }

  // Otherwise, just return the spinner with optional text
  return text ? (
    <div className="flex flex-col items-center justify-center">
      {spinnerElement}
      <p className="mt-2 text-sm text-gray-600">{text}</p>
    </div>
  ) : (
    spinnerElement
  );
};

export default LoadingSpinner;
