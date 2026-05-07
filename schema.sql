-- 1. Create the database
CREATE DATABASE IF NOT EXISTS sia_campsite CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE sia_campsite;

-- 2. Table for Sites (Allows you to easily add/remove Tapaks)
CREATE TABLE sites (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tapak_name VARCHAR(50) NOT NULL, -- e.g., "Tapak 1", "Tapak 2"
    status ENUM('active', 'maintenance') DEFAULT 'active',
    remark TEXT
);

-- 3. Table for Users (Admin vs Booking Staff)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('admin', 'staff') DEFAULT 'staff'
);

-- 4. Table for Bookings (The core data)
CREATE TABLE bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    site_id INT NOT NULL,
    booking_name VARCHAR(100) NOT NULL,
    booking_contact VARCHAR(20) NOT NULL,
    check_in DATE NOT NULL,
    check_out DATE NOT NULL,
    booking_remark TEXT,
    admin_remark TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (site_id) REFERENCES sites(id) ON DELETE CASCADE
);
