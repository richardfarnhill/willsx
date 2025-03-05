import React, { useState } from 'react';
import { Document } from '@/types';
import { getDocumentUrl } from '@/lib/api';
import Card from '@/components/ui/Card';
import Button from '@/components/ui/Button';
import { useToast } from '@/contexts/ToastContext';

interface DocumentCardProps {
  document: Document;
  className?: string;
}

const DocumentCard: React.FC<DocumentCardProps> = ({ document, className = '' }) => {
  const [isLoading, setIsLoading] = useState(false);
  const toast = useToast();

  // Format document type for display
  const formatType = (type: string): string => {
    return type
      .split('_')
      .map(word => word.charAt(0).toUpperCase() + word.slice(1))
      .join(' ');
  };

  // Get document icon based on type
  const getDocumentIcon = () => {
    if (document.type.includes('will')) {
      return (
        <svg className="h-10 w-10 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
      );
    } else if (document.type.includes('lpa')) {
      return (
        <svg className="h-10 w-10 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
        </svg>
      );
    } else {
      return (
        <svg className="h-10 w-10 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
        </svg>
      );
    }
  };

  // Get badge color based on document status
  const getStatusBadgeClass = () => {
    return document.status === 'draft'
      ? 'bg-yellow-100 text-yellow-800'
      : 'bg-green-100 text-green-800';
  };

  // Handle document viewing
  const handleViewDocument = async () => {
    setIsLoading(true);
    try {
      const url = await getDocumentUrl(document.storage_path);
      if (url) {
        window.open(url, '_blank');
      } else {
        toast.error('Error accessing document', 'The document could not be accessed at this time.');
      }
    } catch (error) {
      console.error('Error accessing document:', error);
      toast.error('Error accessing document', 'The document could not be accessed at this time.');
    } finally {
      setIsLoading(false);
    }
  };

  return (
    <Card className={`overflow-hidden ${className}`}>
      <div className="p-4 sm:p-6 flex flex-col h-full">
        <div className="flex items-start">
          <div className="flex-shrink-0">
            {getDocumentIcon()}
          </div>
          <div className="ml-4 flex-1">
            <div className="flex justify-between">
              <h3 className="text-lg font-medium text-gray-900">
                {formatType(document.type)}
              </h3>
              <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${getStatusBadgeClass()}`}>
                {document.status.charAt(0).toUpperCase() + document.status.slice(1)}
              </span>
            </div>
            <div className="mt-1 text-sm text-gray-600">
              Created: {new Date(document.created_at).toLocaleDateString()}
            </div>
          </div>
        </div>

        <div className="mt-4 flex justify-end">
          <Button
            size="sm"
            variant={document.status === 'final' ? 'primary' : 'outline'}
            onClick={handleViewDocument}
            isLoading={isLoading}
          >
            View Document
          </Button>
        </div>
      </div>
    </Card>
  );
};

export default DocumentCard;
