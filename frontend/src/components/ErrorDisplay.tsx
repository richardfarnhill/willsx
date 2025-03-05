import React from 'react';
import Link from 'next/link';

export enum ErrorType {
  VALIDATION = 'validation',
  SERVER = 'server',
  NETWORK = 'network',
  AUTH = 'auth',
  NOT_FOUND = 'not_found',
  PERMISSION = 'permission',
}

interface ErrorDisplayProps {
  type?: ErrorType;
  message?: string;
  showRetry?: boolean;
  showHome?: boolean;
  onRetry?: () => void;
}

const ErrorDisplay: React.FC<ErrorDisplayProps> = ({
  type = ErrorType.SERVER,
  message,
  showRetry = false,
  showHome = true,
  onRetry
}) => {
  const getErrorDetails = () => {
    switch (type) {
      case ErrorType.VALIDATION:
        return {
          title: 'Validation Error',
          description: message || 'There was an error with the data you provided. Please check your information and try again.',
          icon: (
            <svg className="h-12 w-12 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
          ),
          bgColor: 'bg-yellow-50',
          borderColor: 'border-yellow-400'
        };
      
      case ErrorType.NETWORK:
        return {
          title: 'Connection Error',
          description: message || 'Unable to connect to our servers. Please check your internet connection and try again.',
          icon: (
            <svg className="h-12 w-12 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 12l2-2m0 0l7-7 7 7m-7-7v14" />
            </svg>
          ),
          bgColor: 'bg-red-50',
          borderColor: 'border-red-400'
        };
      
      case ErrorType.AUTH:
        return {
          title: 'Authentication Required',
          description: message || 'You need to be logged in to access this feature. Please sign in and try again.',
          icon: (
            <svg className="h-12 w-12 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
            </svg>
          ),
          bgColor: 'bg-blue-50',
          borderColor: 'border-blue-400'
        };
      
      case ErrorType.NOT_FOUND:
        return {
          title: 'Not Found',
          description: message || 'The resource you are looking for could not be found.',
          icon: (
            <svg className="h-12 w-12 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          ),
          bgColor: 'bg-gray-50',
          borderColor: 'border-gray-400'
        };
      
      case ErrorType.PERMISSION:
        return {
          title: 'Access Denied',
          description: message || 'You do not have permission to access this resource.',
          icon: (
            <svg className="h-12 w-12 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
            </svg>
          ),
          bgColor: 'bg-red-50',
          borderColor: 'border-red-400'
        };

      // Default server error
      default:
        return {
          title: 'Server Error',
          description: message || 'Something went wrong on our end. Please try again later or contact support if the issue persists.',
          icon: (
            <svg className="h-12 w-12 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          ),
          bgColor: 'bg-red-50',
          borderColor: 'border-red-400'
        };
    }
  };

  const errorDetails = getErrorDetails();

  return (
    <div className={`rounded-md ${errorDetails.bgColor} border ${errorDetails.borderColor} p-6 shadow-sm`}>
      <div className="flex">
        <div className="flex-shrink-0">{errorDetails.icon}</div>
        <div className="ml-4">
          <h3 className="text-lg font-medium text-gray-900">{errorDetails.title}</h3>
          <div className="mt-2">
            <p className="text-sm text-gray-600">{errorDetails.description}</p>
          </div>
          <div className="mt-4 flex space-x-4">
            {showRetry && onRetry && (
              <button
                type="button"
                onClick={onRetry}
                className="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700"
              >
                Try Again
              </button>
            )}
            {showHome && (
              <Link
                href="/"
                className="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
              >
                Return Home
              </Link>
            )}
          </div>
        </div>
      </div>
    </div>
  );
};

export default ErrorDisplay;