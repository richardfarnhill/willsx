import React, { createContext, useContext, useState, ReactNode } from 'react';
import Notification, { NotificationType } from '@/components/Notification';

type NotificationItem = {
  id: string;
  type: NotificationType;
  message: string;
  description?: string;
  duration?: number;
};

interface NotificationContextType {
  showNotification: (
    type: NotificationType,
    message: string,
    description?: string,
    duration?: number
  ) => string;
  closeNotification: (id: string) => void;
  success: (message: string, description?: string, duration?: number) => string;
  error: (message: string, description?: string, duration?: number) => string;
  warning: (message: string, description?: string, duration?: number) => string;
  info: (message: string, description?: string, duration?: number) => string;
}

const NotificationContext = createContext<NotificationContextType | undefined>(undefined);

export const NotificationProvider: React.FC<{children: ReactNode}> = ({ children }) => {
  const [notifications, setNotifications] = useState<NotificationItem[]>([]);

  const showNotification = (
    type: NotificationType,
    message: string,
    description?: string,
    duration = 5000
  ): string => {
    const id = Math.random().toString(36).substr(2, 9);
    
    setNotifications((prev) => [
      ...prev,
      { id, type, message, description, duration },
    ]);
    
    return id;
  };

  const closeNotification = (id: string) => {
    setNotifications((prev) => prev.filter((notification) => notification.id !== id));
  };

  const success = (message: string, description?: string, duration?: number) => 
    showNotification('success', message, description, duration);
  
  const error = (message: string, description?: string, duration?: number) => 
    showNotification('error', message, description, duration);
  
  const warning = (message: string, description?: string, duration?: number) => 
    showNotification('warning', message, description, duration);
  
  const info = (message: string, description?: string, duration?: number) => 
    showNotification('info', message, description, duration);

  return (
    <NotificationContext.Provider
      value={{
        showNotification,
        closeNotification,
        success,
        error,
        warning,
        info,
      }}
    >
      {children}
      
      {/* Notification container */}
      <div className="fixed top-4 right-4 z-50 space-y-4 pointer-events-none">
        {notifications.map((notification) => (
          <Notification
            key={notification.id}
            type={notification.type}
            message={notification.message}
            description={notification.description}
            duration={notification.duration}
            onClose={() => closeNotification(notification.id)}
            isVisible={true}
          />
        ))}
      </div>
    </NotificationContext.Provider>
  );
};

export const useNotification = () => {
  const context = useContext(NotificationContext);
  if (!context) {
    throw new Error('useNotification must be used within a NotificationProvider');
  }
  return context;
};
