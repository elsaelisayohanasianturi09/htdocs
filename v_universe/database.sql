-- Membuat database
CREATE DATABASE v_universe;

-- Menggunakan database
USE v_universe;

-- Membuat tabel messages
CREATE TABLE messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    country VARCHAR(100) NOT NULL,
    birthdate DATE NOT NULL,
    message TEXT NOT NULL,
    rating INT NOT NULL,
    browser VARCHAR(100) NOT NULL,
    ip_address VARCHAR(45) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

