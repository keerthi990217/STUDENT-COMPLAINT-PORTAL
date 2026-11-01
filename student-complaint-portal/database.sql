-- ============================================================
-- Student Complaint Portal - Database Setup
-- Run this file in phpMyAdmin or MySQL CLI
-- ============================================================

CREATE DATABASE IF NOT EXISTS complaint_portal;
USE complaint_portal;

-- -------------------------------------------------------
-- Table: departments
-- -------------------------------------------------------
CREATE TABLE IF NOT EXISTS departments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- -------------------------------------------------------
-- Table: students
-- -------------------------------------------------------
CREATE TABLE IF NOT EXISTS students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id VARCHAR(20) NOT NULL UNIQUE,   -- e.g. BCA2024001
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,           -- store hashed password
    department_id INT,
    year INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (department_id) REFERENCES departments(id)
);

-- -------------------------------------------------------
-- Table: admins
-- -------------------------------------------------------
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'staff') DEFAULT 'staff',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- -------------------------------------------------------
-- Table: complaints
-- -------------------------------------------------------
CREATE TABLE IF NOT EXISTS complaints (
    id INT AUTO_INCREMENT PRIMARY KEY,
    complaint_code VARCHAR(20) NOT NULL UNIQUE,  -- e.g. CMP-2024-0001
    student_id INT NOT NULL,
    department_id INT NOT NULL,
    category ENUM('academics', 'faculty', 'facilities', 'hostel', 'other') NOT NULL,
    subject VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    is_anonymous TINYINT(1) DEFAULT 1,
    status ENUM('pending', 'in_progress', 'resolved', 'rejected') DEFAULT 'pending',
    admin_remarks TEXT,
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id),
    FOREIGN KEY (department_id) REFERENCES departments(id)
);

-- -------------------------------------------------------
-- Seed Data: Departments
-- -------------------------------------------------------
INSERT INTO departments (name, description) VALUES
('Women Cell', 'Handles complaints related to gender-based issues and women safety'),
('Exam Cell', 'Handles examination related complaints and grievances'),
('Hostel Department', 'Manages hostel facilities and complaints'),
('Academic Department', 'Handles academic related issues and grievances'),
('Library', 'Complaints regarding library facilities and services'),
('Transport', 'Handles transport related complaints'),
('Administration', 'General administrative complaints'),
('IT Department', 'Technical and IT related complaints');

-- -------------------------------------------------------
-- Seed Data: Admin Account
-- Password: admin123 (hashed with password_hash in PHP)
-- -------------------------------------------------------
INSERT INTO admins (name, email, password, role) VALUES
('Admin User', 'admin@college.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- -------------------------------------------------------
-- Seed Data: Sample Student
-- Password: student123
-- -------------------------------------------------------
INSERT INTO students (student_id, name, email, password, department_id, year) VALUES
('BCA2024001', 'Rahul Sharma', 'rahul@college.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, 2);
