-- Warming Room Proposal System Database Schema
-- This script creates the necessary database and tables for the warming room proposal system

-- Create database
CREATE DATABASE IF NOT EXISTS warming_room_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE warming_room_db;

-- Proposals table - stores all warming room proposals
CREATE TABLE IF NOT EXISTS proposals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    author VARCHAR(100) NOT NULL,
    status ENUM('Draft', 'Under Review', 'Approved', 'Implemented', 'Rejected') DEFAULT 'Draft',
    summary TEXT NOT NULL,
    problem_statement TEXT NOT NULL,
    proposed_solution TEXT NOT NULL,
    benefits TEXT,
    challenges TEXT,
    resources_required TEXT,
    timeline TEXT,
    success_metrics TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_status (status),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Comments table - stores comments on proposals
CREATE TABLE IF NOT EXISTS comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    proposal_id INT NOT NULL,
    author VARCHAR(100) NOT NULL,
    comment_text TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (proposal_id) REFERENCES proposals(id) ON DELETE CASCADE,
    INDEX idx_proposal (proposal_id),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Configuration table - stores system configuration
CREATE TABLE IF NOT EXISTS system_config (
    config_key VARCHAR(100) PRIMARY KEY,
    config_value TEXT,
    description VARCHAR(255),
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default configuration values
INSERT INTO system_config (config_key, config_value, description) VALUES
    ('site_name', 'Northumberland Warming Room Proposal System', 'Name of the website'),
    ('admin_email', 'admin@example.com', 'Administrator email address'),
    ('items_per_page', '10', 'Number of items to display per page'),
    ('allow_public_submissions', '1', 'Allow public proposal submissions (1=yes, 0=no)'),
    ('require_approval', '1', 'Require admin approval for new proposals (1=yes, 0=no)')
ON DUPLICATE KEY UPDATE config_value=VALUES(config_value);

-- Activity log table - tracks system activities
CREATE TABLE IF NOT EXISTS activity_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    action VARCHAR(100) NOT NULL,
    description TEXT,
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_action (action),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert sample data for testing
INSERT INTO proposals (title, author, status, summary, problem_statement, proposed_solution, benefits, challenges, resources_required, timeline, success_metrics) VALUES
(
    'Volunteer Coordination System',
    'Example Contributor',
    'Draft',
    'Implement a structured volunteer coordination system to ensure consistent staffing coverage during warming room operations, including scheduling, training, and communication protocols.',
    'During warming room activations, coordinating volunteers can be chaotic without a centralized system. This leads to gaps in coverage, confusion about shift responsibilities, inconsistent service delivery, and volunteer burnout.',
    'Create a three-tiered volunteer coordination system with: 1) Volunteer Database - maintain list of trained volunteers, track availability, record skills; 2) Scheduling System - create clear shift schedules, define minimum staffing, implement backup system; 3) Communication Protocol - establish primary contact methods and check-in systems.',
    'Reliability: Guaranteed coverage with backup volunteers. Efficiency: Clear expectations reduce confusion. Scalability: Easy to onboard new volunteers. Accountability: Track volunteer hours. Safety: Ensure proper staffing ratios.',
    'Technology Access: Not all volunteers may have smartphones. Last-Minute Cancellations: Emergencies happen. Training Consistency: Ensuring all volunteers receive proper training.',
    'Personnel: Volunteer Coordinator (10-15 hrs/week), Training Lead, Shift Leads. Materials: Management software, training materials, ID badges. Financial: $700-1,200/year.',
    'Phase 1 (Weeks 1-2): Planning and setup. Phase 2 (Weeks 3-6): Recruitment and training. Phase 3 (Weeks 7-10): Pilot program. Phase 4 (Week 11+): Full implementation.',
    'Coverage Rate: 95%+ shifts filled. No-Show Rate: Less than 10%. Volunteer Retention: 70%+ return rate. Response Time: All shifts filled within 24 hours.'
);

-- Log initial setup
INSERT INTO activity_log (action, description, ip_address) VALUES
('database_setup', 'Initial database schema created', 'localhost');
