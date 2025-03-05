import { motion } from 'framer-motion';

const Hero = () => {
  return (
    <section className="min-h-screen flex items-center justify-center bg-gradient-to-br from-primary/5 to-secondary/5">
      <div className="container mx-auto px-6 py-24">
        <div className="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
          <motion.div
            initial={{ opacity: 0, x: -50 }}
            animate={{ opacity: 1, x: 0 }}
            transition={{ duration: 0.5 }}
          >
            <h1 className="text-5xl md:text-7xl font-bold leading-tight">
              Transform Your
              <span className="text-primary"> Legacy</span>
            </h1>
            <p className="mt-6 text-xl text-gray-600">
              Secure your future. Protect your loved ones. 
              Digital will creation made simple and elegant.
            </p>
            <motion.div 
              className="mt-8 flex space-x-4"
              initial={{ opacity: 0 }}
              animate={{ opacity: 1 }}
              transition={{ delay: 0.2 }}
            >
              <button className="px-8 py-3 rounded-full bg-primary text-white hover:bg-primary/90 transition-colors">
                Get Started
              </button>
              <button className="px-8 py-3 rounded-full border border-gray-300 hover:border-primary transition-colors">
                Learn More
              </button>
            </motion.div>
          </motion.div>
          <motion.div
            initial={{ opacity: 0, y: 50 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.5, delay: 0.2 }}
            className="relative"
          >
            <div className="aspect-square rounded-2xl bg-gradient-to-br from-primary to-secondary opacity-20 absolute -inset-4 blur-3xl" />
            <div className="relative bg-white rounded-2xl p-8 shadow-xl">
              {/* Add your hero image or interactive element here */}
            </div>
          </motion.div>
        </div>
      </div>
    </section>
  );
};

export default Hero;
