-- Create database
CREATE DATABASE IF NOT EXISTS restaurant;
USE restaurant;

-- Create reservations table
CREATE TABLE IF NOT EXISTS reservations (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(100) NOT NULL,
  tables INT NOT NULL,
  date DATE NOT NULL,
  time VARCHAR(20) NOT NULL,
  phone_no VARCHAR(15) NOT NULL,
  email VARCHAR(100) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
