-- =====================================================
-- TECH ENTRY SYSTEM - DATABASE SCHEMA V2
-- Unit Tracking with Status History
-- =====================================================

-- Drop existing tables if they exist (be careful in production!)
DROP TABLE IF EXISTS work_history;
DROP TABLE IF EXISTS units;
DROP TABLE IF EXISTS tech_users;

-- =====================================================
-- TABLE 1: units
-- One record per unique device/service tag
-- Stores CURRENT status of each unit
-- =====================================================
CREATE TABLE units (
    service_tag VARCHAR(50) PRIMARY KEY,
    current_status ENUM('DIAGNOSED', 'REPAIRED', 'PARTIAL', 'BER', 'COMPLETED') NOT NULL,
    current_on_hold_status VARCHAR(200),
    customer VARCHAR(100),
    current_tech VARCHAR(50),
    job_number INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_status (current_status),
    INDEX idx_customer (customer),
    INDEX idx_tech (current_tech),
    INDEX idx_updated (last_updated)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- TABLE 2: work_history
-- Every time a tech works on a unit, add a row
-- Tracks complete history of who did what when
-- =====================================================
CREATE TABLE work_history (
    history_id INT AUTO_INCREMENT PRIMARY KEY,
    service_tag VARCHAR(50) NOT NULL,
    tech_name VARCHAR(50) NOT NULL,
    work_date DATE NOT NULL,
    status_before VARCHAR(50),
    status_after VARCHAR(50) NOT NULL,
    on_hold_status VARCHAR(200),
    notes TEXT,
    hours_worked DECIMAL(4,2),
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (service_tag) REFERENCES units(service_tag) ON DELETE CASCADE,
    INDEX idx_service_tag (service_tag),
    INDEX idx_tech (tech_name),
    INDEX idx_date (work_date),
    INDEX idx_submitted (submitted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- TABLE 3: tech_users
-- Login credentials for all technicians
-- =====================================================
CREATE TABLE tech_users (
    tech_id INT AUTO_INCREMENT PRIMARY KEY,
    tech_name VARCHAR(50) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- INSERT TECHNICIANS
-- Default PIN for all: 1234 (change after setup!)
-- =====================================================
INSERT INTO tech_users (tech_name, password_hash) VALUES
('ZAC', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('TYREK', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('TURNER', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('TRINITY', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('TRISTIN', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('KYLE', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('JOSH', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('JESS', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('GEOFF', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('DEMETRIUS', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('DARIUS', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('CHRISTIAN', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('CODY', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('BRANDON', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('ASHE', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('ANTHONY', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('ANDRE', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- =====================================================
-- SAMPLE DATA (Optional - for testing)
-- =====================================================

-- Sample unit: GEOFF diagnosed it
INSERT INTO units (service_tag, current_status, current_on_hold_status, customer, current_tech, job_number)
VALUES ('FFH8VW3', 'DIAGNOSED', 'AWAITING PARTS', 'RICHLAND 1', 'GEOFF', 82071);

-- History: GEOFF's diagnosis
INSERT INTO work_history (service_tag, tech_name, work_date, status_before, status_after, on_hold_status)
VALUES ('FFH8VW3', 'GEOFF', '2025-12-03', NULL, 'DIAGNOSED', 'AWAITING PARTS');

-- (Later, TRINITY repairs it - this would be added when she submits)
-- INSERT INTO work_history (service_tag, tech_name, work_date, status_before, status_after, on_hold_status, hours_worked)
-- VALUES ('FFH8VW3', 'TRINITY', '2025-12-05', 'DIAGNOSED', 'REPAIRED', 'AWAITING QA', 2.5);
-- UPDATE units SET current_status = 'REPAIRED', current_on_hold_status = 'AWAITING QA', current_tech = 'TRINITY' WHERE service_tag = 'FFH8VW3';

-- =====================================================
-- USEFUL QUERIES FOR QA TEAM
-- =====================================================

-- Get current status of all units
-- SELECT * FROM units ORDER BY last_updated DESC;

-- Get complete history of a specific unit
-- SELECT * FROM work_history WHERE service_tag = 'FFH8VW3' ORDER BY submitted_at;

-- Get all work done yesterday
-- SELECT * FROM work_history WHERE work_date = CURDATE() - INTERVAL 1 DAY;

-- Count units by current status
-- SELECT current_status, COUNT(*) as count FROM units GROUP BY current_status;

-- See which tech is currently working on each unit
-- SELECT service_tag, customer, current_status, current_tech FROM units WHERE current_status != 'COMPLETED';

-- Get units waiting for parts
-- SELECT * FROM units WHERE current_on_hold_status LIKE '%AWAITING%PARTS%';

-- =====================================================
-- NOTES
-- =====================================================
-- 1. service_tag is the PRIMARY KEY (unique identifier)
-- 2. units table = current state of each device
-- 3. work_history table = complete audit trail
-- 4. When tech updates a unit, both tables get updated
-- 5. Foreign key ensures history is tied to real units
