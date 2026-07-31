-- =========================================
-- PATCH 1
-- =========================================

-- USE asa_project;

-- =========================================
-- DONATIONS
-- =========================================

ALTER TABLE donations
ADD COLUMN donor_phone VARCHAR(20) NULL
AFTER donor_email;

ALTER TABLE donations
ADD COLUMN proof_image VARCHAR(255) NULL
AFTER note;


-- =========================================
-- RENAME TABLE
-- =========================================

RENAME TABLE fund_usages TO distributions;


-- =========================================
-- DISTRIBUTIONS
-- =========================================

ALTER TABLE distributions
CHANGE COLUMN usage_date distribution_date DATE NOT NULL;