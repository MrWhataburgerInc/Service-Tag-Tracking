-- Drop Tables (in correct order due to foreign keys)
DROP TABLE IF EXISTS work_history;
DROP TABLE IF EXISTS unit;
DROP TABLE IF EXISTS technician;

-- 1. Create Technician table with ID
CREATE TABLE technician (
    tech_id INT AUTO_INCREMENT PRIMARY KEY, 
    technician_name VARCHAR(50) UNIQUE NOT NULL,
    kickbacks INT DEFAULT 0,
    part_addons INT DEFAULT 0,
    misdiags INT DEFAULT 0
);

-- 2. Create Unit table
CREATE TABLE unit (
    service_tag VARCHAR(50) NOT NULL,
    job_number INT NOT NULL,
    current_status ENUM('DIAGNOSED', 'REPAIRED', 'PARTIAL', 'BER', 'COMPLETED') NOT NULL,
    on_hold_status ENUM('AWAITING ADP REPAIR', 'AWAITING DELL WARRANTY_PARTS', 'AWAITING QA', 'AWAITING VENDOR DEPOT'),
    customer_name ENUM('RICHLAND 1', 'ANDERSON 5', 'FT. MILL', 'UNION', 'IREDELL'),
    assigned_technician VARCHAR(50),
    current_tech_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    PRIMARY KEY (service_tag, job_number),
    INDEX idx_status (current_status),
    INDEX idx_customer (customer_name),
    INDEX idx_technician (assigned_technician),
    INDEX idx_current_tech_id (current_tech_id),
    INDEX idx_updated (updated_at),
    CONSTRAINT fk_unit_tech FOREIGN KEY (current_tech_id) REFERENCES technician(tech_id) ON DELETE SET NULL ON UPDATE CASCADE
);

-- 3. Create Work History table (no foreign key constraint to avoid partition issues)
CREATE TABLE work_history (
    history_id INT AUTO_INCREMENT PRIMARY KEY,
    service_tag VARCHAR(50) NOT NULL,
    job_number INT,
    technician_name VARCHAR(50) NOT NULL,
    work_date DATE NOT NULL,
    status_before VARCHAR(50),
    status_after VARCHAR(50) NOT NULL,
    on_hold_status ENUM('AWAITING ADP REPAIR', 'AWAITING DELL WARRANTY_PARTS', 'AWAITING QA', 'AWAITING VENDOR DEPOT'),
    notes TEXT,
    hours_worked DECIMAL(3,1),
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_service_tag (service_tag),
    INDEX idx_technician (technician_name),
    INDEX idx_work_date (work_date),
    INDEX idx_submitted (submitted_at)
);

-- 4. Insert technician data
INSERT INTO technician (technician_name) VALUES 
('ANDRE'),('ANTHONY'),('ASHE'),('BRANDON'),('CODY'),('CHRISTIAN'),
('CRUZ'),('DARIUS'),('DAVID'),('DEMETRUIS'),('GEOFF'),('JESS'),
('JOSH'),('KYLE'),('PAM'),('RYAN'),('TRISTIN'),('TRINITY'),
('TURNER'),('TYREK'),('ZAC');

-- 5. Insert sample units
INSERT INTO unit (service_tag, job_number, current_status, on_hold_status, customer_name, assigned_technician, current_tech_id)
VALUES ('FFH8VW3', 82071, 'DIAGNOSED', 'AWAITING DELL WARRANTY_PARTS', 'RICHLAND 1', 'GEOFF', (SELECT tech_id FROM technician WHERE technician_name = 'GEOFF'));

INSERT INTO unit (service_tag, job_number, current_status, on_hold_status, customer_name, assigned_technician, current_tech_id)
VALUES ('a1b2c3d', 12345, 'REPAIRED', 'AWAITING QA', 'ANDERSON 5', 'BRANDON', (SELECT tech_id FROM technician WHERE technician_name = 'BRANDON'));

INSERT INTO unit (service_tag, job_number, current_status, on_hold_status, customer_name, assigned_technician, current_tech_id)
VALUES ('d3b2a1c', 54321, 'REPAIRED', 'AWAITING QA', 'ANDERSON 5', 'BRANDON', (SELECT tech_id FROM technician WHERE technician_name = 'BRANDON'));

-- 6. Insert sample work history
INSERT INTO work_history (service_tag, job_number, technician_name, work_date, status_before, status_after, on_hold_status, notes, hours_worked)
VALUES ('FFH8VW3', 82071, 'GEOFF', '2025-01-15', NULL, 'DIAGNOSED', 'AWAITING DELL WARRANTY_PARTS', 'Diagnosed the unit, awaiting parts.', 2.0);

INSERT INTO work_history (service_tag, job_number, technician_name, work_date, status_before, status_after, on_hold_status, notes, hours_worked)
VALUES ('a1b2c3d', 12345, 'BRANDON', '2025-01-20', 'DIAGNOSED', 'REPAIRED', 'AWAITING QA', 'Repaired motherboard.', 3.5);

-- Verify data
SELECT 'Technicians' as label, COUNT(*) as count FROM technician
UNION ALL
SELECT 'Units', COUNT(*) FROM unit
UNION ALL
SELECT 'Work History', COUNT(*) FROM work_history;