SET FOREIGN_KEY_CHECKS = 0;

-- 1. Hapus tabel lama jika ada
DROP TABLE IF EXISTS `history_logs`;
DROP TABLE IF EXISTS `stock_transactions`;
DROP TABLE IF EXISTS `part_boms`;
DROP TABLE IF EXISTS `master_parts`;
DROP TABLE IF EXISTS `master_lanes`;
DROP TABLE IF EXISTS `master_groups`;
DROP TABLE IF EXISTS `master_customers`;
DROP TABLE IF EXISTS `users`;
DROP TABLE IF EXISTS `roles`;

SET FOREIGN_KEY_CHECKS = 1;

-- 2. Table Roles & Users (NIK 5-Digit)
CREATE TABLE `roles` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(50) NOT NULL UNIQUE,
    `permissions` JSON DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `nik` CHAR(5) NOT NULL UNIQUE,
    `name` VARCHAR(100) NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `role_id` INT NOT NULL,
    `is_blocked` TINYINT(1) DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`role_id`) REFERENCES `roles`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 3. Table Master Groups (PP1-PP3, LA1-LA2, SMT, SUBCONT, FACTORY_2)
CREATE TABLE `master_groups` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `group_code` VARCHAR(20) NOT NULL UNIQUE,
    `group_name` VARCHAR(100) NOT NULL,
    `process_type` ENUM('INJECTION_ST', 'ASSEMBLY_2W', 'ASSEMBLY_4W', 'SMT', 'SUBCONT', 'FACTORY_2') NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 4. Table Master Lane Produksi
CREATE TABLE `master_lanes` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `group_id` INT NOT NULL,
    `lane_code` VARCHAR(50) NOT NULL UNIQUE,
    `lane_name` VARCHAR(100) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`group_id`) REFERENCES `master_groups`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 5. Table Master Customer
CREATE TABLE `master_customers` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `customer_code` VARCHAR(50) NOT NULL UNIQUE,
    `customer_name` VARCHAR(100) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 6. Table Master Parts (ICS Part Number)
CREATE TABLE `master_parts` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `part_number` VARCHAR(50) NOT NULL UNIQUE,
    `part_name` VARCHAR(120) NOT NULL,
    `type` VARCHAR(50) NOT NULL,
    `source` VARCHAR(100) NULL,               -- e.g. HASURA, MESIN-01, FACTORY 2
    `group_code` VARCHAR(20) NOT NULL,         -- e.g. PP1, LA1, SUBCONT
    `customer_id` INT NULL,
    `photo` VARCHAR(255) NULL,
    `min_stock` INT DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`customer_id`) REFERENCES `master_customers`(`id`) ON DELETE SET NULL,
    INDEX `idx_part_group` (`group_code`),
    INDEX `idx_part_number` (`part_number`)
) ENGINE=InnoDB;

-- 7. Table Bill of Materials (Single Part / Component Structure)
CREATE TABLE `part_boms` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `parent_part_id` INT NOT NULL,
    `child_part_id` INT NOT NULL,
    `quantity_required` INT DEFAULT 1,
    FOREIGN KEY (`parent_part_id`) REFERENCES `master_parts`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`child_part_id`) REFERENCES `master_parts`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 8. Table Stock Transactions (Cutoff 3 Shift)
CREATE TABLE `stock_transactions` (
    `id` BIGINT AUTO_INCREMENT PRIMARY KEY,
    `part_id` INT NOT NULL,
    `location` ENUM('PRODUCTION', 'WHP') NOT NULL,
    `group_code` VARCHAR(20) NOT NULL,
    `lane_id` INT NULL,
    `type` ENUM('IN', 'OUT', 'ADJUSTMENT') NOT NULL,
    `quantity` INT NOT NULL,
    `shift_code` ENUM('SHIFT_1', 'SHIFT_2', 'SHIFT_3') NOT NULL,
    `work_date` DATE NOT NULL,
    `user_id` INT NOT NULL,
    `notes` VARCHAR(255) NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`part_id`) REFERENCES `master_parts`(`id`),
    FOREIGN KEY (`lane_id`) REFERENCES `master_lanes`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`),
    INDEX `idx_shift_query` (`location`, `work_date`, `shift_code`),
    INDEX `idx_part_stock` (`part_id`, `location`)
) ENGINE=InnoDB;

-- 9. Table History / Audit Logs
CREATE TABLE `history_logs` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NULL,
    `action` VARCHAR(100) NOT NULL,
    `module` VARCHAR(50) NOT NULL,
    `description` TEXT NULL,
    `ip_address` VARCHAR(45) NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ========================================================
-- SEED DATA AWAL PT INDONESIA STANLEY ELECTRIC
-- ========================================================

-- Seed Roles
INSERT INTO `roles` (`id`, `name`, `permissions`) VALUES
(1, 'Admin', '["*"]'),
(2, 'Inventory', '["dashboard.view", "inventory.view", "inventory.manage", "parts.view", "parts.manage", "export.view"]'),
(3, 'Viewer', '["dashboard.view", "inventory.view", "parts.view", "export.view"]');

-- Seed Admin User (NIK: 12345 | Password: admin123password)
INSERT INTO `users` (`nik`, `name`, `password`, `role_id`, `is_blocked`) VALUES
('12345', 'Administrator Inventory', '$2y$10$eE4a9c11XbWdYz.J6K0b9O0a5sW8eF4gH3jK2l1m0n9p8q7r6s5t', 1, 0);

-- Seed Master Group Stanley
INSERT INTO `master_groups` (`group_code`, `group_name`, `process_type`) VALUES
('PP1', 'Plastic Press 1 (Injection & ST)', 'INJECTION_ST'),
('PP2', 'Plastic Press 2 (Injection & ST)', 'INJECTION_ST'),
('PP3', 'Plastic Press 3 (Injection & ST)', 'INJECTION_ST'),
('LA1', 'Lamp Assembly 1 (Roda 2 / 2W)', 'ASSEMBLY_2W'),
('LA2', 'Lamp Assembly 2 (Roda 4 / 4W)', 'ASSEMBLY_4W'),
('SMT', 'Surface Mount Technology (PWB)', 'SMT'),
('SUBCONT', 'Supplier / Subcont External', 'SUBCONT'),
('FACTORY_2', 'Internal Factory 2', 'FACTORY_2');

-- Seed Sample Customer
INSERT INTO `master_customers` (`customer_code`, `customer_name`) VALUES
('AHM', 'PT Astra Honda Motor'),
('ADM', 'PT Astra Daihatsu Motor'),
('YIMM', 'PT Yamaha Indonesia Motor Manufacturing'),
('MMKI', 'PT Mitsubishi Motors Krama Yudha Indonesia');