-- =============================================
-- Parking Management System - Database Setup
-- Database: parking_db
-- Created for College Mini Project
-- ============================================
-- Create the database
CREATE DATABASE IF NOT EXISTS parking_db;
USE parking_db;

-- Drop tables in reverse order of foreign keys to avoid dependency issues
DROP TABLE IF EXISTS bookings;
DROP TABLE IF EXISTS slots;
DROP TABLE IF EXISTS admin;
DROP TABLE IF EXISTS users;

-- =============================================
-- USERS TABLE
-- Stores registered user information
-- =============================================
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fullname VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone VARCHAR(15) NOT NULL,
    student_id VARCHAR(20) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    security_question VARCHAR(255) NOT NULL,
    security_answer VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- =============================================
-- ADMIN TABLE
-- Stores admin login credentials
-- =============================================
CREATE TABLE IF NOT EXISTS admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
) ENGINE=InnoDB;

-- =============================================
-- SLOTS TABLE
-- Stores parking slot information
-- =============================================
CREATE TABLE IF NOT EXISTS slots (
    id INT AUTO_INCREMENT PRIMARY KEY,
    slot_number VARCHAR(10) NOT NULL UNIQUE,
    status ENUM('available', 'occupied') DEFAULT 'available'
) ENGINE=InnoDB;

-- =============================================
-- BOOKINGS TABLE
-- Stores booking records with foreign keys
-- =============================================
CREATE TABLE IF NOT EXISTS bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    slot_id INT NOT NULL,
    booking_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    status ENUM('active', 'cancelled', 'completed') DEFAULT 'active',
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (slot_id) REFERENCES slots(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- =============================================
-- SEED DATA
-- =============================================
-- Insert default admin (password: admin123)
INSERT INTO admin (username, password) VALUES 
('admin', '$2y$10$pUCxmYIY7rcB..u6tJ/TkOnfo2l9Oewq6CzWJTcqEQyf4By7vvmKu');

-- Insert default demo student driver (password: user123)
INSERT INTO users (fullname, email, phone, student_id, password, security_question, security_answer) VALUES
('Alex Morgan', 'user@test.com', '9876543210', 'STU-2026-001', '$2y$10$nQ7Bf/FoWB2d0tyIAC5MKe6K5wqsKx6Tosf4u/oSKjPD06ZanEgQK', 'What is your favorite color?', '$2y$10$nQ7Bf/FoWB2d0tyIAC5MKe6K5wqsKx6Tosf4u/oSKjPD06ZanEgQK');

-- Insert 20 parking slots (A1-A10 and B1-B10)
INSERT INTO slots (slot_number, status) VALUES
('A1', 'available'),
('A2', 'available'),
('A3', 'available'),
('A4', 'available'),
('A5', 'available'),
('A6', 'available'),
('A7', 'available'),
('A8', 'available'),
('A9', 'available'),
('A10', 'available'),
('B1', 'available'),
('B2', 'available'),
('B3', 'available'),
('B4', 'available'),
('B5', 'available'),
('B6', 'available'),
('B7', 'available'),
('B8', 'available'),
('B9', 'available'),
('B10', 'available');
