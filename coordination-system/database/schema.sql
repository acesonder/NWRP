-- Volunteer Coordination System Database Schema
-- Based on the volunteer coordination system proposal

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;

-- Volunteers table - Core volunteer information
CREATE TABLE volunteers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    phone VARCHAR(20),
    alt_phone VARCHAR(20),
    address TEXT,
    emergency_contact_name VARCHAR(200),
    emergency_contact_phone VARCHAR(20),
    preferred_contact_method ENUM('email', 'phone', 'text') DEFAULT 'email',
    status ENUM('active', 'inactive', 'pending', 'suspended') DEFAULT 'pending',
    training_completed BOOLEAN DEFAULT FALSE,
    training_date DATE,
    background_check_completed BOOLEAN DEFAULT FALSE,
    background_check_date DATE,
    availability_notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Volunteer skills and qualifications
CREATE TABLE volunteer_skills (
    id INT AUTO_INCREMENT PRIMARY KEY,
    volunteer_id INT NOT NULL,
    skill_type ENUM('first_aid', 'language', 'medical', 'mental_health', 'leadership', 'other') NOT NULL,
    skill_description VARCHAR(200) NOT NULL,
    certification_date DATE,
    expiration_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (volunteer_id) REFERENCES volunteers(id) ON DELETE CASCADE
);

-- Volunteer availability preferences
CREATE TABLE volunteer_availability (
    id INT AUTO_INCREMENT PRIMARY KEY,
    volunteer_id INT NOT NULL,
    day_of_week TINYINT NOT NULL, -- 0=Sunday, 1=Monday, etc.
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    is_preferred BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (volunteer_id) REFERENCES volunteers(id) ON DELETE CASCADE
);

-- Shift templates - Define standard shift patterns
CREATE TABLE shift_templates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    min_volunteers INT DEFAULT 2,
    max_volunteers INT DEFAULT 5,
    required_skills TEXT, -- JSON array of required skills
    description TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Activations - Warming room activation periods
CREATE TABLE activations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    location VARCHAR(200),
    coordinator_id INT,
    status ENUM('planned', 'active', 'completed', 'cancelled') DEFAULT 'planned',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (coordinator_id) REFERENCES volunteers(id)
);

-- Scheduled shifts - Actual shifts for activations
CREATE TABLE shifts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    activation_id INT NOT NULL,
    template_id INT NOT NULL,
    shift_date DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    min_volunteers INT DEFAULT 2,
    status ENUM('scheduled', 'in_progress', 'completed', 'cancelled') DEFAULT 'scheduled',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (activation_id) REFERENCES activations(id) ON DELETE CASCADE,
    FOREIGN KEY (template_id) REFERENCES shift_templates(id)
);

-- Volunteer shift assignments
CREATE TABLE shift_assignments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    shift_id INT NOT NULL,
    volunteer_id INT NOT NULL,
    role ENUM('volunteer', 'shift_lead', 'backup') DEFAULT 'volunteer',
    status ENUM('assigned', 'confirmed', 'no_show', 'completed', 'cancelled') DEFAULT 'assigned',
    assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    confirmed_at TIMESTAMP NULL,
    checked_in_at TIMESTAMP NULL,
    checked_out_at TIMESTAMP NULL,
    notes TEXT,
    FOREIGN KEY (shift_id) REFERENCES shifts(id) ON DELETE CASCADE,
    FOREIGN KEY (volunteer_id) REFERENCES volunteers(id) ON DELETE CASCADE,
    UNIQUE KEY unique_volunteer_shift (shift_id, volunteer_id)
);

-- Communication log
CREATE TABLE communications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type ENUM('reminder', 'alert', 'update', 'confirmation', 'incident') NOT NULL,
    method ENUM('email', 'sms', 'phone', 'app_notification') NOT NULL,
    recipient_type ENUM('volunteer', 'coordinator', 'all_volunteers', 'shift_volunteers') NOT NULL,
    recipient_id INT NULL, -- NULL for broadcast messages
    subject VARCHAR(200),
    message TEXT NOT NULL,
    sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    delivery_status ENUM('pending', 'sent', 'delivered', 'failed') DEFAULT 'pending',
    related_shift_id INT NULL,
    related_activation_id INT NULL,
    FOREIGN KEY (recipient_id) REFERENCES volunteers(id) ON DELETE SET NULL,
    FOREIGN KEY (related_shift_id) REFERENCES shifts(id) ON DELETE SET NULL,
    FOREIGN KEY (related_activation_id) REFERENCES activations(id) ON DELETE SET NULL
);

-- Incident reports
CREATE TABLE incidents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    shift_id INT NOT NULL,
    reporter_id INT NOT NULL,
    incident_type ENUM('medical', 'behavioral', 'facility', 'volunteer', 'other') NOT NULL,
    severity ENUM('low', 'medium', 'high', 'critical') NOT NULL,
    description TEXT NOT NULL,
    action_taken TEXT,
    follow_up_required BOOLEAN DEFAULT FALSE,
    follow_up_notes TEXT,
    incident_time TIMESTAMP NOT NULL,
    reported_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (shift_id) REFERENCES shifts(id),
    FOREIGN KEY (reporter_id) REFERENCES volunteers(id)
);

-- System settings and configuration
CREATE TABLE system_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    setting_type ENUM('string', 'number', 'boolean', 'json') DEFAULT 'string',
    description TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert default shift templates
INSERT INTO shift_templates (name, start_time, end_time, min_volunteers, max_volunteers, description) VALUES
('Evening Shift', '18:00:00', '22:00:00', 2, 4, 'Initial setup and guest check-in'),
('Night Shift', '22:00:00', '02:00:00', 2, 3, 'Overnight supervision'),
('Early Morning Shift', '02:00:00', '06:00:00', 1, 2, 'Light supervision and wake-up'),
('Morning Cleanup', '06:00:00', '09:00:00', 2, 3, 'Breakfast service and facility cleanup');

-- Insert default system settings
INSERT INTO system_settings (setting_key, setting_value, setting_type, description) VALUES
('site_name', 'Volunteer Coordination System', 'string', 'Application name'),
('notification_lead_time', '24', 'number', 'Hours before shift to send reminders'),
('backup_pool_size', '5', 'number', 'Minimum number of backup volunteers to maintain'),
('max_consecutive_shifts', '2', 'number', 'Maximum consecutive shifts a volunteer can work'),
('contact_methods', '["email","sms","phone"]', 'json', 'Available communication methods'),
('training_required', 'true', 'boolean', 'Whether training completion is required'),
('background_check_required', 'true', 'boolean', 'Whether background check is required');

COMMIT;