import React, { forwardRef, InputHTMLAttributes } from 'react';

interface FormInputProps extends InputHTMLAttributes<HTMLInputElement> {
  label: string;
  error?: string;
  touched?: boolean;
  helpText?: string;
  labelClassName?: string;
}

const FormInput = forwardRef<HTMLInputElement, FormInputProps>(
  ({ label, error, touched, helpText, className, labelClassName, ...props }, ref) => {
    return (
      <div className="mb-4">
        <label 
          htmlFor={props.id || props.name} 
          className={`block text-sm font-medium text-gray-700 mb-1 ${labelClassName || ''}`}
        >
          {label}
          {props.required && <span className="text-red-500 ml-1">*</span>}
        </label>
        
        <input
          ref={ref}
          className={`appearance-none block w-full px-3 py-2 border ${
            touched && error 
              ? 'border-red-300 focus:ring-red-500 focus:border-red-500' 
              : 'border-gray-300 focus:ring-blue-500 focus:border-blue-500'
          } rounded-md shadow-sm placeholder-gray-400 focus:outline-none sm:text-sm ${
            className || ''
          }`}
          {...props}
        />
        
        {touched && error ? (
          <p className="mt-1 text-sm text-red-600">{error}</p>
        ) : helpText ? (
          <p className="mt-1 text-sm text-gray-500">{helpText}</p>
        ) : null}
      </div>
    );
  }
);

FormInput.displayName = 'FormInput';

export default FormInput;
