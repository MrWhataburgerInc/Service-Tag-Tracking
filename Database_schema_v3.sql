-- Drop Tables / Temporary
DROP TABLE IF EXISTS unit_history;
DROP TABLE IF EXISTS unit;
DROP TABLE IF EXISTS technician;

-- Table creation
CREATE TABLE unit (
    service_tag VARCHAR(50),
    current_status ENUM('DIAGNOSED', 'REPAIRED', 'PARTIAL', 'BER', 'COMPLETED') NOT NULL,
    on_hold_status ENUM('AWAITING ADP REPAIR', 'AWAITING DELL WARRANTY_PARTS', 'AWAITING QA', 'AWAITING VENDOR DEPOT'),
    customer_name ENUM('RICHLAND 1', 'ANDERSON 5', 'FT. MILL', 'UNION', 'IREDELL'),
    assigned_technician VARCHAR(50),
    job_number INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Defining the Composite Primary Key here
    PRIMARY KEY (service_tag, job_number),
    
    -- Indexes for performance
    INDEX idx_current_status (current_status),
    INDEX idx_customer (customer_name),
    INDEX idx_technician (assigned_technician),
    INDEX idx_updated (updated_at)
);

CREATE TABLE work_history (
    history_id INT AUTO_INCREMENT PRIMARY KEY,
    service_tag VARCHAR(50) NOT NULL,
    technician_name VARCHAR(50) NOT NULL,
    work_date DATE NOT NULL,
    status_before VARCHAR(50),
    status_after VARCHAR(50) NOT NULL,
    on_hold_status ENUM('AWAITING ADP REPAIR', 'AWAITING DELL WARRANTY_PARTS', 'AWAITING QA', 'AWAITING VENDOR DEPOT'),
    notes TEXT,
    hours_worked CHAR(1),
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (service_tag) REFERENCES unit(service_tag) ON DELETE CASCADE,
    INDEX idx_service_tag (service_tag),
    INDEX idx_technician (technician_name),
    INDEX idx_date (work_date),
    INDEX idx_submitted (submitted_at)
);

CREATE TABLE technician (
    technician_name VARCHAR(20) PRIMARY KEY,
    kickbacks INT,
    part_addons INT,
    misdiags INT

);

-- Insert Values
INSERT INTO technician (technician_name) VALUES 
('ANDRE'),('ANTHONY'),('ASHE'),
('BRANDON'),
('CODY'),('CHRISTIAN'),('CRUZ'),
('DARIUS'),('DAVID'),('DEMETRUIS'),
('GOEFF'),
('JESS'),('JOSH'),
('KYLE'),
('PAM'),
('RYAN'),
('TRISTIN'),('TRINITY'),('TURNER'),('TYREK'),
('ZAC');


-- 1. Add the new column (nullable for now)
ALTER TABLE unit
  ADD COLUMN current_tech_id INT NULL,
  ADD INDEX idx_current_tech_id (current_tech_id);

-- 2. Populate from existing current_tech (if you have names matching tech_users.tech_name)
UPDATE unit u
JOIN technician t ON u.assigned_technician = t.tech_name
SET u.current_tech_id = t.tech_id;

-- 3. Make column NOT NULL if appropriate (only after verifying)
ALTER TABLE unit
  MODIFY COLUMN current_tech_id INT NOT NULL;

-- 4. Add foreign key constraint
ALTER TABLE unit
  ADD CONSTRAINT fk_units_tech FOREIGN KEY (current_tech_id) REFERENCES tech_users(tech_id) ON DELETE SET NULL ON UPDATE CASCADE;


-- SAMPLE DATA FOR TESTING
INSERT INTO unit (service_tag, current_status, on_hold_status, customer_name, assigned_technician, job_number)
VALUES ('FFH8VW3', 'DIAGNOSED', 'AWAITING PARTS', 'RICHLAND 1', 'GEOFF', 82071);

INSERT INTO work_history (service_tag, technician_name, work_date, status_before, status_after, on_hold_status, notes, hours_worked)
VALUES ('FFH8VW3', 'GEOFF', '2024-01-15', 'RECEIVED', 'DIAGNOSED', 'AWAITING PARTS', 'Diagnosed the unit, awaiting parts.', '2');

-- (Later, TRINITY) repairs it - this would be added when she submits)
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
