import React, { forwardRef, SelectHTMLAttributes } from 'react';

interface Option {
  value: string;
  label: string;
}

interface FormSelectProps extends SelectHTMLAttributes<HTMLSelectElement> {
  label: string;
  options: Option[];
  error?: string;
  touched?: boolean;
  helpText?: string;
  labelClassName?: string;
}

const FormSelect = forwardRef<HTMLSelectElement, FormSelectProps>(
  ({ label, options, error, touched, helpText, className, labelClassName, ...props }, ref) => {
    return (
      <div className="mb-4">
        <label 
          htmlFor={props.id || props.name} 
          className={`block text-sm font-medium text-gray-700 mb-1 ${labelClassName || ''}`}
        >
          {label}
          {props.required && <span className="text-red-500 ml-1">*</span>}
        </label>
        
        <select
          ref={ref}
          className={`block w-full pl-3 pr-10 py-2 text-base ${
            touched && error 
              ? 'border-red-300 focus:ring-red-500 focus:border-red-500' 
              : 'border-gray-300 focus:ring-blue-500 focus:border-blue-500'
          } border rounded-md shadow-sm focus:outline-none sm:text-sm ${
            className || ''
          }`}
          {...props}
        >
          {props.placeholder && (
            <option value="" disabled>
              {props.placeholder}
            </option>
          )}
          
          {options.map((option) => (
            <option key={option.value} value={option.value}>
              {option.label}
            </option>
          ))}
        </select>
        
        {touched && error ? (
          <p className="mt-1 text-sm text-red-600">{error}</p>
        ) : helpText ? (
          <p className="mt-1 text-sm text-gray-500">{helpText}</p>
        ) : null}
      </div>
    );
  }
);

FormSelect.displayName = 'FormSelect';

export default FormSelect;
