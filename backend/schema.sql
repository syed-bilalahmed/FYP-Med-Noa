-- Database Schema for Smart Healthcare System
-- --------------------------------------------------------
-- 1. Users Table (Core Authentication)
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin', 'doctor', 'receptionist', 'patient') NOT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;
-- 2. Patients Details (Profile)
CREATE TABLE IF NOT EXISTS `patients_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  -- Linked if patient has login
  `name` varchar(100) NOT NULL,
  `age` int(11) NOT NULL,
  `gender` enum('Male', 'Female', 'Other') NOT NULL,
  `blood_group` varchar(5) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;
-- 3. Hospitals & Clinics (Locations)
CREATE TABLE IF NOT EXISTS `hospitals` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `address` text NOT NULL,
  `contact_info` varchar(50) DEFAULT NULL,
  `type` enum('Hospital', 'Clinic') DEFAULT 'Hospital',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;
-- 4. Departments (Specialties)
CREATE TABLE IF NOT EXISTS `departments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL UNIQUE,
  `description` text,
  PRIMARY KEY (`id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;
-- 5. Doctors Details (Profile)
CREATE TABLE IF NOT EXISTS `doctors_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `specialization` varchar(100) NOT NULL,
  `qualification` varchar(150) DEFAULT NULL,
  `experience_years` int(11) DEFAULT 0,
  `bio` text,
  `contact_number` varchar(20) DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT 'default_doctor.png',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;
-- 6. Doctor-Hospital Affiliations (Where doctors work)
CREATE TABLE IF NOT EXISTS `doctor_hospital_affiliations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `doctor_id` int(11) NOT NULL,
  `hospital_id` int(11) NOT NULL,
  `department_id` int(11) DEFAULT NULL,
  `consultation_fee` decimal(10, 2) DEFAULT 0.00,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`doctor_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`hospital_id`) REFERENCES `hospitals`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`department_id`) REFERENCES `departments`(`id`) ON DELETE
  SET NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;
-- 7. Doctor Availability (Schedule)
CREATE TABLE IF NOT EXISTS `doctor_availability` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `doctor_id` int(11) NOT NULL,
  `hospital_id` int(11) NOT NULL,
  `day_of_week` enum(
    'Monday',
    'Tuesday',
    'Wednesday',
    'Thursday',
    'Friday',
    'Saturday',
    'Sunday'
  ) NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`doctor_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`hospital_id`) REFERENCES `hospitals`(`id`) ON DELETE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;
-- 8. Appointments
CREATE TABLE IF NOT EXISTS `appointments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `patient_id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `hospital_id` int(11) DEFAULT NULL,
  -- Optional if not location specific
  `token_number` int(11) DEFAULT 0,
  `date` date NOT NULL,
  `time_slot` varchar(50) DEFAULT NULL,
  -- E.g., "10:00 AM - 10:30 AM"
  `status` enum('pending', 'confirmed', 'completed', 'cancelled') DEFAULT 'pending',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`patient_id`) REFERENCES `patients_details`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`doctor_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`hospital_id`) REFERENCES `hospitals`(`id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;
-- 9. Prescriptions & Medical Records
CREATE TABLE IF NOT EXISTS `prescriptions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `appointment_id` int(11) NOT NULL,
  `symptoms` text,
  `diagnosis` text,
  `medicines` text,
  -- JSON or formatted string
  `tests_suggested` text,
  `advice` text,
  `next_visit_date` date DEFAULT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`appointment_id`) REFERENCES `appointments`(`id`) ON DELETE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;
-- 10. Medical Reports (File Uploads)
CREATE TABLE IF NOT EXISTS `medical_reports` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `patient_id` int(11) NOT NULL,
  `appointment_id` int(11) DEFAULT NULL,
  `title` varchar(150) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `uploaded_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`patient_id`) REFERENCES `patients_details`(`id`) ON DELETE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;
-- 11. Payments (Revenue Management)
CREATE TABLE IF NOT EXISTS `payments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `appointment_id` int(11) NOT NULL,
  `amount` decimal(10, 2) NOT NULL,
  `status` enum('pending', 'paid', 'failed') DEFAULT 'pending',
  `transaction_id` varchar(100) DEFAULT NULL,
  `payment_date` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`appointment_id`) REFERENCES `appointments`(`id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;
-- 12. Contact Messages
CREATE TABLE IF NOT EXISTS `contact_messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `subject` varchar(255),
  `message` TEXT NOT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;
-- --------------------------------------------------------
-- SEED DATA (For Testing)
-- --------------------------------------------------------
-- Admin
INSERT INTO `users` (`name`, `phone`, `password`, `role`)
VALUES (
    'Super Admin',
    '+1234567890',
    'admin123',
    'admin'
  ) ON DUPLICATE KEY
UPDATE name = 'Super Admin';
-- Departments
INSERT INTO `departments` (`name`, `description`)
VALUES (
    'Cardiology',
    'Heart and cardiovascular system details'
  ),
  ('Neurology', 'Brain and nervous system'),
  (
    'Pediatrics',
    'Medical care of infants, children, and adolescents'
  ),
  (
    'General Medicine',
    'Adult diseases and general health'
  ) ON DUPLICATE KEY
UPDATE description =
VALUES(description);
-- Hospitals
INSERT INTO `hospitals` (`name`, `address`, `type`)
VALUES (
    'City General Hospital',
    '123 Main St, New York',
    'Hospital'
  ),
  (
    'Sunrise Clinic',
    '456 East Ave, Brooklyn',
    'Clinic'
  ) ON DUPLICATE KEY
UPDATE address =
VALUES(address);