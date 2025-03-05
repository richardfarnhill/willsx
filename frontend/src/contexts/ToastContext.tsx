import React, { createContext, useState, useContext, ReactNode } from 'react';
import Toast, { ToastType, ToastPosition } from '@/components/ui/Toast';

type Toast = {
  id: string;
  type: ToastType;
  title: string;
  message?: string;
  duration?: number;
  position?: ToastPosition;
};

interface ToastContextType {
  toasts: Toast[];
  showToast: (type: ToastType, title: string, message?: string, options?: Partial<Toast>) => string;
  dismissToast: (id: string) => void;
  success: (title: string, message?: string, options?: Partial<Toast>) => string;
  error: (title: string, message?: string, options?: Partial<Toast>) => string;
  warning: (title: string, message?: string, options?: Partial<Toast>) => string;
  info: (title: string, message?: string, options?: Partial<Toast>) => string;
}

const ToastContext = createContext<ToastContextType | undefined>(undefined);

export const ToastProvider: React.FC<{ children: ReactNode }> = ({ children }) => {
  const [toasts, setToasts] = useState<Toast[]>([]);

  // Show a toast with the given type, title, and message
  const showToast = (
    type: ToastType,
    title: string,
    message?: string,
    options?: Partial<Toast>
  ): string => {
    const id = Math.random().toString(36).substring(2, 11);
    
    setToasts(prevToasts => [
      ...prevToasts,
      {
        id,
        type,
        title,
        message,
        duration: 5000,
        position: 'top-right',
        ...options,
      },
    ]);

    return id;
  };

  // Dismiss a toast by ID
  const dismissToast = (id: string) => {
    setToasts(prevToasts => prevToasts.filter(toast => toast.id !== id));
  };

  // Helper methods for different toast types
  const success = (title: string, message?: string, options?: Partial<Toast>) => 
    showToast('success', title, message, options);
  
  const error = (title: string, message?: string, options?: Partial<Toast>) => 
    showToast('error', title, message, options);
  
  const warning = (title: string, message?: string, options?: Partial<Toast>) => 
    showToast('warning', title, message, options);
  
  const info = (title: string, message?: string, options?: Partial<Toast>) => 
    showToast('info', title, message, options);

  return (
    <ToastContext.Provider
      value={{
        toasts,
        showToast,
        dismissToast,
        success,
        error,
        warning,
        info,
      }}
    >
      {children}
      
      {/* Render active toasts */}
      {toasts.map(toast => (
        <Toast
          key={toast.id}
          id={toast.id}
          type={toast.type}
          title={toast.title}
          message={toast.message}
          duration={toast.duration}
          position={toast.position}
          onDismiss={dismissToast}
        />
      ))}
    </ToastContext.Provider>
  );
};

// Custom hook to use the toast context
export const useToast = () => {
  const context = useContext(ToastContext);
  if (!context) {
    throw new Error('useToast must be used within a ToastProvider');
  }
  return context;
};
