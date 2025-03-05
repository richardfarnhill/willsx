import React, { ButtonHTMLAttributes, ReactNode } from 'react';
import Link from 'next/link';
import LoadingSpinner from '../LoadingSpinner';

type ButtonVariant = 'primary' | 'secondary' | 'outline' | 'danger' | 'success' | 'ghost';
type ButtonSize = 'xs' | 'sm' | 'md' | 'lg' | 'xl';

interface ButtonBaseProps {
  variant?: ButtonVariant;
  size?: ButtonSize;
  isLoading?: boolean;
  leftIcon?: ReactNode;
  rightIcon?: ReactNode;
  fullWidth?: boolean;
  children: ReactNode;
}

// Button props when used as an actual button
interface ButtonAsButtonProps
  extends ButtonBaseProps,
    ButtonHTMLAttributes<HTMLButtonElement> {
  href?: never;
}

// Button props when used as a link
interface ButtonAsLinkProps extends ButtonBaseProps {
  href: string;
  onClick?: never;
  type?: never;
  disabled?: boolean;
  external?: boolean;
}

type ButtonProps = ButtonAsButtonProps | ButtonAsLinkProps;

// Helper function to determine if the props are for a link
const isLinkProps = (props: ButtonProps): props is ButtonAsLinkProps => {
  return 'href' in props && props.href !== undefined;
};

const Button = (props: ButtonProps) => {
  const {
    variant = 'primary',
    size = 'md',
    isLoading = false,
    leftIcon,
    rightIcon,
    fullWidth = false,
    children,
    disabled,
    className = '',
    ...rest
  } = props;

  // Determine base styles based on variant
  let variantClasses = '';
  switch (variant) {
    case 'primary':
      variantClasses = 'bg-blue-600 hover:bg-blue-700 text-white shadow-sm';
      break;
    case 'secondary':
      variantClasses = 'bg-gray-100 hover:bg-gray-200 text-gray-800 shadow-sm';
      break;
    case 'outline':
      variantClasses = 'bg-white hover:bg-gray-50 text-gray-700 border border-gray-300 shadow-sm';
      break;
    case 'danger':
      variantClasses = 'bg-red-600 hover:bg-red-700 text-white shadow-sm';
      break;
    case 'success':
      variantClasses = 'bg-green-600 hover:bg-green-700 text-white shadow-sm';
      break;
    case 'ghost':
      variantClasses = 'bg-transparent hover:bg-gray-100 text-gray-700';
      break;
  }

  // Determine size classes
  let sizeClasses = '';
  switch (size) {
    case 'xs':
      sizeClasses = 'px-2.5 py-1 text-xs';
      break;
    case 'sm':
      sizeClasses = 'px-3 py-1.5 text-sm';
      break;
    case 'md':
      sizeClasses = 'px-4 py-2 text-sm';
      break;
    case 'lg':
      sizeClasses = 'px-5 py-2.5 text-base';
      break;
    case 'xl':
      sizeClasses = 'px-6 py-3 text-base';
      break;
  }

  const buttonClasses = `
    inline-flex items-center justify-center 
    font-medium rounded-md 
    focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500
    transition-colors duration-200
    ${disabled || isLoading ? 'opacity-60 cursor-not-allowed' : 'cursor-pointer'}
    ${fullWidth ? 'w-full' : ''}
    ${variantClasses}
    ${sizeClasses}
    ${className}
  `;

  const content = (
    <>
      {isLoading && (
        <LoadingSpinner
          size={size === 'xs' || size === 'sm' ? 'sm' : 'md'}
          color={variant === 'primary' || variant === 'danger' || variant === 'success' ? 'white' : 'primary'}
          className="mr-2"
        />
      )}
      {!isLoading && leftIcon && <span className="mr-2">{leftIcon}</span>}
      {children}
      {!isLoading && rightIcon && <span className="ml-2">{rightIcon}</span>}
    </>
  );

  // Render as Link if href is provided
  if (isLinkProps(props)) {
    const { href, external, ...linkRest } = props;
    
    if (external) {
      return (
        <a
          href={href}
          className={buttonClasses}
          target="_blank"
          rel="noopener noreferrer"
          {...(linkRest as any)}
        >
          {content}
        </a>
      );
    }
    
    return (
      <Link href={href}>
        <span className={buttonClasses}>{content}</span>
      </Link>
    );
  }

  // Render as button
  return (
    <button className={buttonClasses} disabled={disabled || isLoading} {...rest}>
      {content}
    </button>
  );
};

export default Button;
