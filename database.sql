-- Create database
CREATE DATABASE IF NOT EXISTS ju_exam_portal;
USE ju_exam_portal;

-- Users table (with bank account fields)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('student', 'chairman', 'provost', 'accountant', 'registrar', 'controller', 'admin') NOT NULL,
    student_id VARCHAR(50),
    session VARCHAR(20),
    department VARCHAR(100),
    hall VARCHAR(100),
    phone VARCHAR(20),
    bank_name VARCHAR(100),
    account_number VARCHAR(50),
    branch VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Exam forms table (with bank account fields)
CREATE TABLE IF NOT EXISTS exam_forms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    student_name VARCHAR(255) NOT NULL,
    father_name VARCHAR(255) NOT NULL,
    mother_name VARCHAR(255) NOT NULL,
    class_roll VARCHAR(50) NOT NULL,
    reg_number VARCHAR(50) NOT NULL,
    address TEXT,
    session VARCHAR(20) NOT NULL,
    department VARCHAR(100) NOT NULL,
    hall_name VARCHAR(100) NOT NULL,
    subject_list TEXT,
    failed_exams TEXT,
    semester VARCHAR(20) NOT NULL,
    photo_path VARCHAR(255),
    receipt_path VARCHAR(255),
    bank_name VARCHAR(100),
    account_number VARCHAR(50),
    branch VARCHAR(100),
    status ENUM('draft', 'submitted', 'approved_chairman', 'approved_provost', 'verified_accountant', 'verified_registrar', 'final_approved') DEFAULT 'draft',
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Approvals table
CREATE TABLE IF NOT EXISTS approvals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    form_id INT NOT NULL,
    approver_role ENUM('chairman', 'provost', 'accountant', 'registrar', 'controller') NOT NULL,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    comments TEXT,
    approved_at TIMESTAMP NULL,
    FOREIGN KEY (form_id) REFERENCES exam_forms(id) ON DELETE CASCADE
);

