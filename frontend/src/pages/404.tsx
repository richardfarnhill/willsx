import React from 'react';
import Link from 'next/link';
import Head from 'next/head';
import Layout from '@/components/Layout';

export default function NotFound() {
  return (
    <Layout title="Page Not Found | WillsX">
      <Head>
        <meta name="robots" content="noindex, nofollow" />
      </Head>
      
      <div className="min-h-[60vh] flex flex-col items-center justify-center text-center px-4">
        <div className="mb-8">
          <svg 
            className="mx-auto h-24 w-24 text-gray-400" 
            fill="none" 
            viewBox="0 0 24 24" 
            stroke="currentColor" 
            aria-hidden="true"
          >
            <path 
              strokeLinecap="round" 
              strokeLinejoin="round" 
              strokeWidth={1} 
              d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" 
            />
          </svg>
        </div>
        
        <h1 className="text-4xl font-bold mb-3">404 - Page Not Found</h1>
        
        <p className="text-xl text-gray-600 max-w-md mx-auto mb-8">
          Sorry, we couldn't find the page you're looking for. The page may have been moved or deleted.
        </p>
        
        <div className="flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4">
          <Link 
            href="/"
            className="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
          >
            Return Home
          </Link>
          
          <Link 
            href="/contact"
            className="inline-flex items-center justify-center px-6 py-3 border border-gray-300 shadow-sm text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
          >
            Contact Support
          </Link>
        </div>
        
        <div className="mt-12">
          <p className="text-gray-500">
            Looking for wills or LPA services?
          </p>
          <div className="mt-4 flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4 justify-center">
            <Link 
              href="/services/will-writing"
              className="text-blue-600 hover:text-blue-700 font-medium"
            >
              Will Writing 
              <span aria-hidden="true" className="ml-1">&rarr;</span>
            </Link>
            
            <Link 
              href="/services/lpa"
              className="text-blue-600 hover:text-blue-700 font-medium"
            >
              Lasting Power of Attorney
              <span aria-hidden="true" className="ml-1">&rarr;</span>
            </Link>
            
            <Link 
              href="/blog"
              className="text-blue-600 hover:text-blue-700 font-medium"
            >
              Resource Library
              <span aria-hidden="true" className="ml-1">&rarr;</span>
            </Link>
          </div>
        </div>
      </div>
    </Layout>
  );
}
