CREATE DATABASE IF NOT EXISTS socialnet
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

CREATE USER IF NOT EXISTS 'socialnet_user'@'localhost'
IDENTIFIED BY 'SocialNetPass123!';

GRANT ALL PRIVILEGES ON socialnet.* TO 'socialnet_user'@'localhost';
FLUSH PRIVILEGES;

USE socialnet;

CREATE TABLE IF NOT EXISTS account (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    fullname VARCHAR(100) NOT NULL,
    password VARCHAR(255) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
