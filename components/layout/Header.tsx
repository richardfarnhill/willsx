import { useState, useEffect } from 'react';
import Link from 'next/link';
import { motion } from 'framer-motion';

const Header = () => {
  const [scrolled, setScrolled] = useState(false);

  useEffect(() => {
    const handleScroll = () => {
      setScrolled(window.scrollY > 50);
    };
    window.addEventListener('scroll', handleScroll);
    return () => window.removeEventListener('scroll', handleScroll);
  }, []);

  return (
    <motion.header
      className={`fixed w-full z-50 transition-all duration-300 ${
        scrolled ? 'bg-white/90 backdrop-blur-md' : 'bg-transparent'
      }`}
      initial={{ y: -100 }}
      animate={{ y: 0 }}
      transition={{ duration: 0.5 }}
    >
      <nav className="container mx-auto px-6 py-4">
        <div className="flex items-center justify-between">
          <Link href="/" className="text-2xl font-bold">
            WillsX
          </Link>
          <div className="hidden md:flex space-x-8">
            <NavLink href="/features">Features</NavLink>
            <NavLink href="/pricing">Pricing</NavLink>
            <NavLink href="/about">About</NavLink>
          </div>
          <div className="flex items-center space-x-4">
            <button className="px-6 py-2 rounded-full bg-primary text-white hover:bg-primary/90 transition-colors">
              Get Started
            </button>
          </div>
        </div>
      </nav>
    </motion.header>
  );
};

const NavLink = ({ href, children }) => (
  <Link href={href} className="text-gray-600 hover:text-primary transition-colors">
    {children}
  </Link>
);

export default Header;
