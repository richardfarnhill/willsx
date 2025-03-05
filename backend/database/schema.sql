-- WillsX Supabase Schema Definition

-- Enable necessary extensions
CREATE EXTENSION IF NOT EXISTS "uuid-ossp";
CREATE EXTENSION IF NOT EXISTS "pgcrypto";

-- User Profiles table
-- Maps to Supabase auth.users
CREATE TABLE user_profiles (
  id UUID PRIMARY KEY REFERENCES auth.users(id),
  first_name VARCHAR(255) NOT NULL,
  last_name VARCHAR(255) NOT NULL,
  email VARCHAR(255) NOT NULL UNIQUE,
  phone VARCHAR(20),
  partner_id INTEGER, -- References WordPress partner ID
  role VARCHAR(20) DEFAULT 'client',
  created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

-- Row level security for user_profiles
ALTER TABLE user_profiles ENABLE ROW LEVEL SECURITY;

-- Users can read their own profile
CREATE POLICY "Users can view own profile" ON user_profiles
  FOR SELECT USING (auth.uid() = id);

-- Users can update their own profile
CREATE POLICY "Users can update own profile" ON user_profiles
  FOR UPDATE USING (auth.uid() = id);

-- Admins can read all profiles
CREATE POLICY "Admins can view all profiles" ON user_profiles
  FOR SELECT USING (
    EXISTS (
      SELECT 1 FROM user_profiles
      WHERE id = auth.uid() AND role = 'admin'
    )
  );

-- User settings table
CREATE TABLE user_settings (
  id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
  user_id UUID NOT NULL REFERENCES user_profiles(id) ON DELETE CASCADE,
  email_notifications BOOLEAN DEFAULT TRUE,
  marketing_emails BOOLEAN DEFAULT TRUE,
  sms_notifications BOOLEAN DEFAULT FALSE,
  document_sharing BOOLEAN DEFAULT TRUE,
  dark_mode BOOLEAN DEFAULT FALSE,
  created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
  UNIQUE(user_id)
);

-- Row level security for user_settings
ALTER TABLE user_settings ENABLE ROW LEVEL SECURITY;

-- Users can read and update their own settings
CREATE POLICY "Users can view own settings" ON user_settings
  FOR SELECT USING (auth.uid() = user_id);

CREATE POLICY "Users can update own settings" ON user_settings
  FOR UPDATE USING (auth.uid() = user_id);

-- Wills table
CREATE TABLE wills (
  id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
  user_id UUID NOT NULL REFERENCES user_profiles(id) ON DELETE CASCADE,
  status VARCHAR(20) NOT NULL DEFAULT 'draft', -- draft, submitted, in_review, completed
  created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
  submitted_at TIMESTAMP WITH TIME ZONE,
  completed_at TIMESTAMP WITH TIME ZONE
);

-- Row level security for wills
ALTER TABLE wills ENABLE ROW LEVEL SECURITY;

-- Users can read their own wills
CREATE POLICY "Users can view own wills" ON wills
  FOR SELECT USING (auth.uid() = user_id);

-- Users can create wills
CREATE POLICY "Users can create wills" ON wills
  FOR INSERT WITH CHECK (auth.uid() = user_id);

-- Users can update their own wills
CREATE POLICY "Users can update own wills" ON wills
  FOR UPDATE USING (auth.uid() = user_id);

-- Will data table (stores data for each section of the will)
CREATE TABLE will_data (
  id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
  will_id UUID NOT NULL REFERENCES wills(id) ON DELETE CASCADE,
  section VARCHAR(50) NOT NULL, -- personal, family, assets, beneficiaries, executors, wishes
  data JSONB NOT NULL,
  updated_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
  UNIQUE(will_id, section)
);

-- Row level security for will_data
ALTER TABLE will_data ENABLE ROW LEVEL SECURITY;

-- Users can read their own will data
CREATE POLICY "Users can view own will data" ON will_data
  FOR SELECT USING (
    EXISTS (
      SELECT 1 FROM wills
      WHERE wills.id = will_data.will_id AND wills.user_id = auth.uid()
    )
  );

-- Users can insert will data for their wills
CREATE POLICY "Users can insert will data" ON will_data
  FOR INSERT WITH CHECK (
    EXISTS (
      SELECT 1 FROM wills
      WHERE wills.id = will_data.will_id AND wills.user_id = auth.uid()
    )
  );

-- Users can update will data for their wills
CREATE POLICY "Users can update will data" ON will_data
  FOR UPDATE USING (
    EXISTS (
      SELECT 1 FROM wills
      WHERE wills.id = will_data.will_id AND wills.user_id = auth.uid()
    )
  );

-- Bookings table
CREATE TABLE bookings (
  id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
  user_id UUID NOT NULL REFERENCES user_profiles(id) ON DELETE CASCADE,
  advisor_id VARCHAR(50) NOT NULL, -- WordPress advisor ID
  service_type VARCHAR(50) NOT NULL, -- will_consultation, lpa_consultation, general_advice
  date_time TIMESTAMP WITH TIME ZONE NOT NULL,
  duration INTEGER NOT NULL DEFAULT 30, -- in minutes
  status VARCHAR(20) NOT NULL DEFAULT 'scheduled', -- scheduled, completed, cancelled
  video_link VARCHAR(255),
  notes TEXT,
  created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

-- Row level security for bookings
ALTER TABLE bookings ENABLE ROW LEVEL SECURITY;

-- Users can read their own bookings
CREATE POLICY "Users can view own bookings" ON bookings
  FOR SELECT USING (auth.uid() = user_id);

-- Users can create bookings
CREATE POLICY "Users can create bookings" ON bookings
  FOR INSERT WITH CHECK (auth.uid() = user_id);

-- Users can update their own bookings
CREATE POLICY "Users can update own bookings" ON bookings
  FOR UPDATE USING (auth.uid() = user_id);

-- Documents table
CREATE TABLE documents (
  id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
  user_id UUID NOT NULL REFERENCES user_profiles(id) ON DELETE CASCADE,
  will_id UUID REFERENCES wills(id) ON DELETE SET NULL,
  type VARCHAR(50) NOT NULL, -- will_draft, will_final, lpa_draft, lpa_final, id_verification
  storage_path VARCHAR(255) NOT NULL, -- Path in storage bucket
  created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
  status VARCHAR(20) NOT NULL DEFAULT 'draft' -- draft, final
);

-- Row level security for documents
ALTER TABLE documents ENABLE ROW LEVEL SECURITY;

-- Users can read their own documents
CREATE POLICY "Users can view own documents" ON documents
  FOR SELECT USING (auth.uid() = user_id);

-- Admins can create documents for users
CREATE POLICY "Admins can create documents" ON documents
  FOR INSERT WITH CHECK (
    EXISTS (
      SELECT 1 FROM user_profiles
      WHERE id = auth.uid() AND role = 'admin'
    )
  );

-- Create triggers for updated_at timestamps
CREATE OR REPLACE FUNCTION update_timestamp()
RETURNS TRIGGER AS $$
BEGIN
  NEW.updated_at = CURRENT_TIMESTAMP;
  RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER update_user_profiles_timestamp
BEFORE UPDATE ON user_profiles
FOR EACH ROW EXECUTE PROCEDURE update_timestamp();

CREATE TRIGGER update_wills_timestamp
BEFORE UPDATE ON wills
FOR EACH ROW EXECUTE PROCEDURE update_timestamp();

CREATE TRIGGER update_user_settings_timestamp
BEFORE UPDATE ON user_settings
FOR EACH ROW EXECUTE PROCEDURE update_timestamp();

-- Create function for wills status updates
CREATE OR REPLACE FUNCTION update_will_status_timestamps()
RETURNS TRIGGER AS $$
BEGIN
  IF OLD.status != NEW.status THEN
    IF NEW.status = 'submitted' THEN
      NEW.submitted_at = CURRENT_TIMESTAMP;
    ELSIF NEW.status = 'completed' THEN
      NEW.completed_at = CURRENT_TIMESTAMP;
    END IF;
  END IF;
  RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER update_will_status_timestamps
BEFORE UPDATE ON wills
FOR EACH ROW EXECUTE PROCEDURE update_will_status_timestamps();

-- Create initial admin user (comment out in production)
-- INSERT INTO user_profiles (id, first_name, last_name, email, role)
-- VALUES ('00000000-0000-0000-0000-000000000000', 'Admin', 'User', 'admin@willsx.co.uk', 'admin');
