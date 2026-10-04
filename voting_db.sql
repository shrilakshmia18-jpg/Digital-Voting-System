-- Voting DB with columns for photos, reasons, symbol images
CREATE DATABASE IF NOT EXISTS voting_db;
USE voting_db;

CREATE TABLE IF NOT EXISTS nomination (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100),
  dob DATE,
  father VARCHAR(100),
  mother VARCHAR(100),
  address TEXT,
  phone VARCHAR(15),
  aadhaar VARCHAR(20),
  photo VARCHAR(255),
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS date_dashboard (
  id INT AUTO_INCREMENT PRIMARY KEY,
  date DATE,
  description TEXT
);

CREATE TABLE IF NOT EXISTS verification (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nomination_id INT,
  name VARCHAR(100),
  aadhaar VARCHAR(20),
  status VARCHAR(20),
  reason TEXT,
  verified_at DATETIME,
  FOREIGN KEY (nomination_id) REFERENCES nomination(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS withdrawn (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nomination_id INT,
  name VARCHAR(100),
  aadhaar VARCHAR(20),
  withdrawn_at DATETIME,
  FOREIGN KEY (nomination_id) REFERENCES nomination(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS distribution (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nomination_id INT,
  name VARCHAR(100),
  aadhaar VARCHAR(20),
  symbol VARCHAR(100),
  symbol_image VARCHAR(255),
  assigned_at DATETIME,
  FOREIGN KEY (nomination_id) REFERENCES nomination(id) ON DELETE SET NULL
);
