-- Drop Tables
DROP TABLE IF EXISTS work_history;
DROP TABLE IF EXISTS unit;
DROP TABLE IF EXISTS technician;

-- 1. Create Technician with an ID column
CREATE TABLE technician (
    tech_id INT AUTO_INCREMENT PRIMARY KEY, 
    technician_name VARCHAR(50) UNIQUE,
    kickbacks INT DEFAULT 0,
    part_addons INT DEFAULT 0,
    misdiags INT DEFAULT 0
);

-- 2. Create Unit table
CREATE TABLE unit (
    service_tag VARCHAR(50) NOT NULL,
    current_status ENUM('DIAGNOSED', 'REPAIRED', 'PARTIAL', 'BER', 'COMPLETED') NOT NULL,
    on_hold_status ENUM('AWAITING ADP REPAIR', 'AWAITING DELL WARRANTY_PARTS', 'AWAITING QA', 'AWAITING VENDOR DEPOT'),
    customer_name ENUM('RICHLAND 1', 'ANDERSON 5', 'FT. MILL', 'UNION', 'IREDELL' ),
    assigned_technician VARCHAR(50),
    job_number INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT pk_unit PRIMARY KEY (service_tag, job_number),
    INDEX idx_technician (assigned_technician)
);
-- Step 1: Drop the existing table to recreate it with partitioning
DROP TABLE IF EXISTS work_history;

-- Step 2: Recreate table WITHOUT the Foreign Key constraint
CREATE TABLE work_history (
    history_id INT AUTO_INCREMENT, -- Part of PK (must include partitioning key)
    service_tag VARCHAR(50) NOT NULL,
    technician_name VARCHAR(50) NOT NULL,
    work_date DATE NOT NULL,        -- The partitioning key
    status_before VARCHAR(50),
    status_after VARCHAR(50) NOT NULL,
    on_hold_status ENUM('AWAITING ADP REPAIR', 'AWAITING DELL WARRANTY_PARTS', 'AWAITING QA', 'AWAITING VENDOR DEPOT'),
    notes TEXT,
    hours_worked CHAR(1),
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    -- The PRIMARY KEY must include the partitioning column (work_date)
    PRIMARY KEY (history_id, work_date),
    INDEX idx_service_tag (service_tag),
    INDEX idx_technician (technician_name)
);

-- 3. Insert Values into technician (to generate IDs)
INSERT INTO technician (technician_name) VALUES 
('ANDRE'),('ANTHONY'),('ASHE'),('BRANDON'),('CODY'),('CHRISTIAN'),
('CRUZ'),('DARIUS'),('DAVID'),('DEMETRUIS'),('GEOFF'),('JESS'),
('JOSH'),('KYLE'),('PAM'),('RYAN'),('TRISTIN'),('TRINITY'),
('TURNER'),('TYREK'),('ZAC');

-- 4. Add the current_tech_id column to unit
ALTER TABLE unit
  ADD COLUMN current_tech_id INT NULL,
  ADD INDEX idx_current_tech_id (current_tech_id);

-- 5. Corrected UPDATE statement
-- Uses 'assigned_technician' (from unit) and 'technician_name' (from technician)
UPDATE unit u
  JOIN technician t ON u.assigned_technician = t.technician_name,
  SET u.current_tech_id = t.tech_id;

-- 6. Add foreign key constraint to the correct table (technician, not tech_users)
ALTER TABLE unit
  ADD CONSTRAINT fk_units_tech, 
  FOREIGN KEY (current_tech_id) REFERENCES technician(tech_id),
  ON DELETE SET NULL ON UPDATE CASCADE;

-- 7. Insert Sample Data
INSERT INTO unit (service_tag, current_status, on_hold_status, customer_name, assigned_technician, job_number)
VALUES ('FFH8VW3', 'DIAGNOSED', 'AWAITING DELL WARRANTY_PARTS', 'RICHLAND 1', 'GEOFF', 82071);
INSERT INTO unit (service_tag, current_status, on_hold_status, customer_name, assigned_technician, job_number)
VALUES ('a1b2c3d', 'REPAIRED', 'QA', 'ANDERSON 5', 'BRANDON', 12345);
