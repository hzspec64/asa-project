CREATE DATABASE IF NOT EXISTS asa_project
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE asa_project;


-- =========================================
-- USERS
-- =========================================

CREATE TABLE users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    name VARCHAR(100) NOT NULL,

    email VARCHAR(100) NOT NULL UNIQUE,

    password VARCHAR(255) NOT NULL,

    role ENUM('admin', 'staff') NOT NULL DEFAULT 'staff',

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP
);


-- =========================================
-- ARTICLES
-- =========================================

CREATE TABLE articles (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    title VARCHAR(255) NOT NULL,

    slug VARCHAR(255) NOT NULL UNIQUE,

    content TEXT NOT NULL,

    image VARCHAR(255) NULL,

    status ENUM('draft', 'published')
        NOT NULL DEFAULT 'draft',

    user_id INT UNSIGNED NOT NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id)
        REFERENCES users(id)
);


-- =========================================
-- GALLERIES
-- =========================================

CREATE TABLE galleries (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    title VARCHAR(255) NOT NULL,

    description TEXT NULL,

    image VARCHAR(255) NOT NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


-- =========================================
-- CAMPAIGNS
-- =========================================

CREATE TABLE campaigns (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    title VARCHAR(255) NOT NULL,

    slug VARCHAR(255) NOT NULL UNIQUE,

    description TEXT NOT NULL,

    target_amount DECIMAL(15, 2) NOT NULL DEFAULT 0,

    start_date DATE NOT NULL,

    end_date DATE NULL,

    image VARCHAR(255) NULL,

    status ENUM('draft', 'active', 'completed', 'cancelled')
        NOT NULL DEFAULT 'draft',

    user_id INT UNSIGNED NOT NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id)
        REFERENCES users(id)
);


-- =========================================
-- DONATIONS
-- =========================================

CREATE TABLE donations (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    campaign_id INT UNSIGNED NOT NULL,

    donor_name VARCHAR(100) NOT NULL,

    donor_email VARCHAR(100) NULL,

    amount DECIMAL(15, 2) NOT NULL,

    donation_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    status ENUM('pending', 'paid', 'cancelled')
        NOT NULL DEFAULT 'pending',

    note TEXT NULL,

    FOREIGN KEY (campaign_id)
        REFERENCES campaigns(id)
);


-- =========================================
-- FUND USAGES
-- =========================================

CREATE TABLE fund_usages (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    campaign_id INT UNSIGNED NOT NULL,

    title VARCHAR(255) NOT NULL,

    description TEXT NULL,

    amount DECIMAL(15, 2) NOT NULL,

    usage_date DATE NOT NULL,

    image VARCHAR(255) NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (campaign_id)
        REFERENCES campaigns(id)
);
