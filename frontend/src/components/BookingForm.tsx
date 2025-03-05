import { useState, useEffect } from 'react';
import { useRouter } from 'next/router';
import { z } from 'zod';
import { useFormValidation } from '@/hooks/useFormValidation';
import { useAuth } from '@/contexts/AuthContext';
import { supabase } from '@/lib/supabase';
import { useNotification } from '@/contexts/NotificationContext';
import LoadingSpinner from './LoadingSpinner';

// Define booking form schema with Zod
const bookingSchema = z.object({
  service_type: z.enum(['initial_consultation', 'will_consultation', 'lpa_consultation', 'follow_up']),
  date: z.string().regex(/^\d{4}-\d{2}-\d{2}$/, 'Please select a valid date'),
  time_slot: z.string().min(1, 'Please select a time slot'),
  advisor_id: z.string().optional(),
  notes: z.string().max(500, 'Notes cannot exceed 500 characters').optional(),
});

type BookingFormData = z.infer<typeof bookingSchema>;

interface Advisor {
  id: string;
  name: string;
  expertise: string[];
  avatar_url: string;
}

interface TimeSlot {
  id: string;
  time: string;
  available: boolean;
}

// Mock data for advisors - in production, this would come from Supabase
const MOCK_ADVISORS: Advisor[] = [
  {
    id: 'advisor-1',
    name: 'Sarah Johnson',
    expertise: ['wills', 'probate'],
    avatar_url: '/images/advisors/advisor1.jpg'
  },
  {
    id: 'advisor-2',
    name: 'James Smith',
    expertise: ['lpa', 'estate planning'],
    avatar_url: '/images/advisors/advisor2.jpg'
  },
  {
    id: 'advisor-3',
    name: 'Emma Wilson',
    expertise: ['wills', 'lpa', 'trusts'],
    avatar_url: '/images/advisors/advisor3.jpg'
  }
];

// Component for booking form
const BookingForm = () => {
  const { user } = useAuth();
  const router = useRouter();
  const [loading, setLoading] = useState(false);
  const [availableSlots, setAvailableSlots] = useState<TimeSlot[]>([]);
  const [selectedAdvisor, setSelectedAdvisor] = useState<Advisor | null>(null);
  const notification = useNotification();
  
  // Initialize form
  const initialValues: BookingFormData = {
    service_type: 'initial_consultation',
    date: '',
    time_slot: '',
    advisor_id: '',
    notes: ''
  };
  
  const {
    values,
    errors,
    touched,
    handleChange,
    handleBlur,
    handleSubmit,
    setField
  } = useFormValidation<BookingFormData>({
    schema: bookingSchema,
    initialValues,
    onSubmit: createBooking
  });

  // Mock function to get available time slots
  const fetchAvailableTimeSlots = async (date: string, advisorId?: string) => {
    setLoading(true);
    
    try {
      // In a real app, this would call the Supabase API
      // const { data, error } = await supabase
      //   .rpc('get_available_slots', { 
      //     p_date: date,
      //     p_advisor_id: advisorId || null
      //   });
      
      // if (error) throw error;
      // setAvailableSlots(data);
      
      // Mock data for demo purposes
      // Simulate API call
      await new Promise(resolve => setTimeout(resolve, 800));
      
      // Generate slots from 9 AM to 5 PM
      const slots: TimeSlot[] = [];
      for (let i = 9; i < 17; i++) {
        const hour = i < 10 ? `0${i}` : `${i}`;
        // Randomize availability
        const available = Math.random() > 0.3;
        slots.push({ 
          id: `slot-${i}`, 
          time: `${hour}:00`, 
          available 
        });
        
        // Add 30-minute slots too
        const available30 = Math.random() > 0.3;
        slots.push({ 
          id: `slot-${i}-30`, 
          time: `${hour}:30`, 
          available: available30 
        });
      }
      
      setAvailableSlots(slots);
    } catch (error) {
      console.error('Error fetching time slots:', error);
      notification.error('Failed to load available appointment times');
    } finally {
      setLoading(false);
    }
  };
  
  // Update time slots when date changes
  useEffect(() => {
    if (values.date) {
      fetchAvailableTimeSlots(values.date, values.advisor_id);
    }
  }, [values.date, values.advisor_id]);
  
  // Handle advisor selection
  const handleAdvisorSelect = (advisor: Advisor) => {
    setSelectedAdvisor(advisor);
    setField('advisor_id', advisor.id);
    
    // Refetch time slots with new advisor
    if (values.date) {
      fetchAvailableTimeSlots(values.date, advisor.id);
    }
  };
  
  // Create booking in Supabase
  async function createBooking(data: BookingFormData) {
    if (!user) {
      notification.info('Please sign in to book an appointment',
        'You will be redirected to the login page');
      router.push('/login?redirect=/bookings/new');
      return;
    }
    
    setLoading(true);
    
    try {
      // Convert date and time to ISO string
      const dateTime = new Date(`${data.date}T${data.time_slot}`);
      
      // In a real app, this would insert into Supabase
      // const { data: booking, error } = await supabase
      //   .from('bookings')
      //   .insert({
      //     user_id: user.id,
      //     advisor_id: data.advisor_id,
      //     service_type: data.service_type,
      //     date_time: dateTime.toISOString(),
      //     notes: data.notes,
      //     status: 'scheduled',
      //     duration: 30, // Default duration in minutes
      //   })
      //   .select()
      //   .single();
      
      // if (error) throw error;
      
      // Mock successful booking
      await new Promise(resolve => setTimeout(resolve, 1200));
      
      notification.success(
        'Appointment booked successfully', 
        'You will receive a confirmation email shortly'
      );
      
      // Redirect to bookings page
      router.push('/dashboard');
    } catch (error) {
      console.error('Error creating booking:', error);
      notification.error('Failed to book appointment. Please try again.');
    } finally {
      setLoading(false);
    }
  }
  
  return (
    <div className="bg-white rounded-lg shadow-sm p-6">
      <h2 className="text-2xl font-bold mb-6">Schedule an Appointment</h2>
      
      <form onSubmit={handleSubmit} className="space-y-6">
        {/* Service Type */}
        <div>
          <label htmlFor="service_type" className="block text-sm font-medium text-gray-700 mb-1">
            Service Type
          </label>
          <select
            id="service_type"
            name="service_type"
            value={values.service_type}
            onChange={handleChange}
            onBlur={handleBlur}
            className="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md"
          >
            <option value="initial_consultation">Initial Consultation</option>
            <option value="will_consultation">Will Writing Consultation</option>
            <option value="lpa_consultation">Lasting Power of Attorney</option>
            <option value="follow_up">Follow-up Meeting</option>
          </select>
          {touched.service_type && errors.service_type && (
            <p className="mt-1 text-sm text-red-600">{errors.service_type}</p>
          )}
        </div>
        
        {/* Advisor Selection */}
        <div>
          <label className="block text-sm font-medium text-gray-700 mb-1">
            Select an Advisor (Optional)
          </label>
          <div className="grid grid-cols-1 md:grid-cols-3 gap-4 mt-2">
            {MOCK_ADVISORS.map((advisor) => (
              <div 
                key={advisor.id}
                className={`border rounded-lg p-4 cursor-pointer transition-colors ${
                  values.advisor_id === advisor.id 
                    ? 'border-blue-500 bg-blue-50' 
                    : 'border-gray-200 hover:bg-gray-50'
                }`}
                onClick={() => handleAdvisorSelect(advisor)}
              >
                <div className="flex items-center">
                  <div className="flex-shrink-0 h-10 w-10 bg-gray-200 rounded-full overflow-hidden">
                    {/* Placeholder for advisor avatar */}
                    <div className="h-full w-full flex items-center justify-center text-gray-500">
                      {advisor.name.charAt(0)}
                    </div>
                  </div>
                  <div className="ml-4">
                    <h4 className="text-sm font-medium text-gray-900">{advisor.name}</h4>
                    <p className="text-xs text-gray-500">
                      {advisor.expertise.join(', ')}
                    </p>
                  </div>
                </div>
              </div>
            ))}
          </div>
        </div>
        
        {/* Date Selection */}
        <div>
          <label htmlFor="date" className="block text-sm font-medium text-gray-700 mb-1">
            Date
          </label>
          <input
            type="date"
            id="date"
            name="date"
            value={values.date}
            onChange={handleChange}
            onBlur={handleBlur}
            min={new Date().toISOString().split('T')[0]} // Prevent past dates
            className="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
          />
          {touched.date && errors.date && (
            <p className="mt-1 text-sm text-red-600">{errors.date}</p>
          )}
        </div>
        
        {/* Time Slot Selection */}
        {values.date && (
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">
              Available Time Slots
            </label>
            
            {loading ? (
              <div className="flex justify-center py-4">
                <LoadingSpinner size="md" text="Loading available time slots..." />
              </div>
            ) : availableSlots.length > 0 ? (
              <div className="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2">
                {availableSlots.map((slot) => (
                  <button
                    key={slot.id}
                    type="button"
                    disabled={!slot.available}
                    onClick={() => setField('time_slot', slot.time)}
                    className={`py-2 px-4 text-sm font-medium rounded-md ${
                      values.time_slot === slot.time
                        ? 'bg-blue-500 text-white'
                        : slot.available
                        ? 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-50'
                        : 'bg-gray-100 text-gray-400 cursor-not-allowed border border-gray-200'
                    }`}
                  >
                    {slot.time}
                  </button>
                ))}
              </div>
            ) : (
              <p className="text-gray-500 text-sm italic">No slots available on this date. Please try another date.</p>
            )}
            
            {touched.time_slot && errors.time_slot && (
              <p className="mt-1 text-sm text-red-600">{errors.time_slot}</p>
            )}
          </div>
        )}
        
        {/* Notes */}
        <div>
          <label htmlFor="notes" className="block text-sm font-medium text-gray-700 mb-1">
            Additional Notes (Optional)
          </label>
          <textarea
            id="notes"
            name="notes"
            rows={3}
            value={values.notes || ''}
            onChange={handleChange}
            onBlur={handleBlur}
            className="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
            placeholder="Please let us know if you have any specific questions or requirements"
          ></textarea>
          {touched.notes && errors.notes && (
            <p className="mt-1 text-sm text-red-600">{errors.notes}</p>
          )}
        </div>
        
        {/* Submit Button */}
        <div className="flex justify-end">
          <button
            type="submit"
            disabled={loading}
            className="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:bg-blue-300"
          >
            {loading ? (
              <>
                <LoadingSpinner size="sm" color="white" className="mr-2" />
                Booking Appointment...
              </>
            ) : (
              'Book Appointment'
            )}
          </button>
        </div>
      </form>
    </div>
  );
};

export default BookingForm;
