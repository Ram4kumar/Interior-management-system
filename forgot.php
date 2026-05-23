-- Interior Management System - MySQL Schema
CREATE DATABASE IF NOT EXISTS interior_management CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE interior_management;

CREATE TABLE users (
  user_id INT PRIMARY KEY AUTO_INCREMENT,
  full_name VARCHAR(120) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  phone VARCHAR(20),
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('admin','employee') NOT NULL DEFAULT 'employee',
  avatar VARCHAR(255) DEFAULT NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE password_resets (
  id INT PRIMARY KEY AUTO_INCREMENT,
  user_id INT NOT NULL,
  token_hash VARCHAR(255) NOT NULL,
  expires_at DATETIME NOT NULL,
  used TINYINT(1) NOT NULL DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE clients (
  client_id INT PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(120) NOT NULL,
  phone VARCHAR(20),
  email VARCHAR(150),
  address TEXT,
  requirements TEXT,
  budget DECIMAL(12,2) DEFAULT 0,
  created_by INT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (created_by) REFERENCES users(user_id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE projects (
  project_id INT PRIMARY KEY AUTO_INCREMENT,
  title VARCHAR(150) NOT NULL,
  client_id INT NOT NULL,
  assigned_to INT,
  status ENUM('pending','ongoing','completed') NOT NULL DEFAULT 'pending',
  start_date DATE,
  end_date DATE,
  budget DECIMAL(12,2) DEFAULT 0,
  location VARCHAR(200),
  notes TEXT,
  cover_image VARCHAR(255),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (client_id) REFERENCES clients(client_id) ON DELETE CASCADE,
  FOREIGN KEY (assigned_to) REFERENCES users(user_id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE activity_logs (
  log_id INT PRIMARY KEY AUTO_INCREMENT,
  user_id INT,
  action VARCHAR(80),
  description VARCHAR(255),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE payments (
  payment_id INT PRIMARY KEY AUTO_INCREMENT,
  project_id INT NOT NULL,
  amount DECIMAL(12,2) NOT NULL,
  status ENUM('pending','paid') DEFAULT 'pending',
  paid_at DATE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (project_id) REFERENCES projects(project_id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Default admin: email=admin@ims.local  password=Admin@123
INSERT INTO users (full_name, email, password_hash, role) VALUES
('System Admin','admin@ims.local','$2b$10$xkYltx1wFQmWmElHRhqojeFs3I2HFmGP1LKw/6Jbu2J1B.FEmjHIq','admin'),
('Jane Designer','jane@ims.local','$2b$10$xkYltx1wFQmWmElHRhqojeFs3I2HFmGP1LKw/6Jbu2J1B.FEmjHIq','employee');
