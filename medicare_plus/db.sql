-- Create Database
CREATE DATABASE IF NOT EXISTS medicare_plus CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE medicare_plus;

-- 1. USERS Table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    role ENUM('admin','doctor','patient') NOT NULL DEFAULT 'patient',
    fullname VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    specialization VARCHAR(255),
    experience VARCHAR(50)
);

-- Insert Sample Users
INSERT INTO users (role, fullname, email, password_hash, specialization, experience) VALUES
('admin', 'Admin User', 'admin@medicare.com', '$2y$10$KIX6wQx.Cn7ZQ8P4EVZPMuF3t3Z3D56a8dK2zRj9RZZPz6Qk./aAa', NULL, NULL),
('doctor', 'Dr. John Doe', 'john@medicare.com', '$2y$10$KIX6wQx.Cn7ZQ8P4EVZPMuF3t3Z3D56a8dK2zRj9RZZPz6Qk./aAa', 'Cardiology', '10 years'),
('doctor', 'Dr. Jane Smith', 'jane@medicare.com', '$2y$10$KIX6wQx.Cn7ZQ8P4EVZPMuF3t3Z3D56a8dK2zRj9RZZPz6Qk./aAa', 'Pediatrics', '8 years'),
('patient', 'Alice Brown', 'alice@medicare.com', '$2y$10$KIX6wQx.Cn7ZQ8P4EVZPMuF3t3Z3D56a8dK2zRj9RZZPz6Qk./aAa', NULL, NULL),
('patient', 'Bob Johnson', 'bob@medicare.com', '$2y$10$KIX6wQx.Cn7ZQ8P4EVZPMuF3t3Z3D56a8dK2zRj9RZZPz6Qk./aAa', NULL, NULL);

-- 2. SERVICES Table
CREATE TABLE IF NOT EXISTS services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT
);

-- Sample Services
INSERT INTO services (name, description) VALUES
('Cardiology', 'Heart-related consultations and treatments'),
('Pediatrics', 'Child health care and treatments'),
('Radiology', 'Diagnostic imaging services'),
('Dermatology', 'Skin care treatments and consultations');

-- 3. APPOINTMENTS Table
CREATE TABLE IF NOT EXISTS appointments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT NOT NULL,
    doctor_id INT NOT NULL,
    service_id INT NOT NULL,
    appointment_date DATETIME NOT NULL,
    status ENUM('pending','confirmed','completed','cancelled') DEFAULT 'pending',
    FOREIGN KEY (patient_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (doctor_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE
);

-- Sample Appointments
INSERT INTO appointments (patient_id, doctor_id, service_id, appointment_date, status) VALUES
(4, 2, 1, '2025-11-25 10:00:00', 'pending'),
(5, 3, 2, '2025-11-26 14:30:00', 'confirmed');

-- 4. MESSAGES Table
CREATE TABLE IF NOT EXISTS messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT NOT NULL,
    receiver_id INT NOT NULL,
    message TEXT NOT NULL,
    sent_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Sample Messages
INSERT INTO messages (sender_id, receiver_id, message) VALUES
(4, 2, 'Hello Doctor, I have a question about my heart.'),
(2, 4, 'Sure Alice, please explain your symptoms.');

-- 5. REPORTS Table
CREATE TABLE IF NOT EXISTS reports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT NOT NULL,
    doctor_id INT NOT NULL,
    file_name VARCHAR(255) NOT NULL,
    uploaded_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (doctor_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS patient_profile (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL UNIQUE,
    address VARCHAR(255),
    age INT,
    weight FLOAT,
    height FLOAT,
    profile_pic VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Sample Reports (files to be uploaded manually in uploads/)
INSERT INTO reports (patient_id, doctor_id, file_name) VALUES
(4, 2, 'heart_report_alice.pdf'),
(5, 3, 'pediatric_checkup_bob.pdf');

-- 6. FEEDBACK Table
CREATE TABLE IF NOT EXISTS feedback (
    id INT AUTO_INCREMENT PRIMARY KEY,
    doctor_id INT NOT NULL,
    patient_id INT NOT NULL,
    comment TEXT NOT NULL,
    rating INT NOT NULL,
    FOREIGN KEY (doctor_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (patient_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Sample Feedback
INSERT INTO feedback (doctor_id, patient_id, comment, rating) VALUES
(2, 4, 'Excellent consultation. Very professional.', 5),
(3, 5, 'Friendly and knowledgeable.', 4);

ALTER TABLE messages ADD COLUMN is_read TINYINT(1) NOT NULL DEFAULT 0;
UPDATE messages SET is_read = 0;

CREATE TABLE IF NOT EXISTS bills (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT NOT NULL, 
    description VARCHAR(255) NOT NULL DEFAULT 'Doctor Consultation Fee', 
    amount DECIMAL(10,2) NOT NULL,   
    status ENUM('Unpaid','Pending','Paid') DEFAULT 'Unpaid', 
    receipt VARCHAR(255) DEFAULT NULL,  
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP, 
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, 
    FOREIGN KEY (patient_id) REFERENCES users(id) ON DELETE CASCADE
);
ALTER TABLE bills
ADD COLUMN receipt VARCHAR(255) NULL;

ALTER TABLE users ADD profile_pic VARCHAR(255) NULL;

ALTER TABLE users ADD COLUMN rating FLOAT DEFAULT 0;

CREATE TABLE IF NOT EXISTS patient_reports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT NOT NULL,
    doctor_id INT NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    uploaded_at DATETIME NOT NULL,
    FOREIGN KEY (patient_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (doctor_id) REFERENCES users(id) ON DELETE CASCADE
);

DROP TABLE IF EXISTS bills;


CREATE TABLE IF NOT EXISTS bills (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT NOT NULL,
    doctor_id INT NOT NULL,
    receipt VARCHAR(255), -- file path
    status ENUM('Pending','Confirmed') DEFAULT 'Pending',
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (doctor_id) REFERENCES users(id) ON DELETE CASCADE
);
