-- ============================================================
--  agrify | schema.sql
--  Run this file once to bootstrap the full database.
--  Order matters — tables are created dependency-first.
-- ============================================================

CREATE DATABASE IF NOT EXISTS `agrify`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `agrify`;

-- ============================================================
--  Part 1 — admins
--  Platform-level superusers. No farm affiliation.
-- ============================================================
CREATE TABLE IF NOT EXISTS `admins` (
  `id`            INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `username`      VARCHAR(50)   NOT NULL,
  `email`         VARCHAR(150)  NOT NULL,
  `password`      VARCHAR(255)  NOT NULL        COMMENT 'bcrypt hash',
  `is_active`     TINYINT(1)    NOT NULL DEFAULT 1,
  `last_login_at` TIMESTAMP     NULL     DEFAULT NULL,
  `last_login_ip` VARCHAR(45)   NULL     DEFAULT NULL COMMENT 'supports IPv6',
  `created_at`    TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`    TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP
                                         ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_admin_email`    (`email`),
  UNIQUE KEY `uq_admin_username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
--  Part 2 — users
--  Standalone. Base auth table for farmers and workers.
-- ============================================================
CREATE TABLE IF NOT EXISTS `users` (
  `id`            INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `username`      VARCHAR(50)   NOT NULL,
  `email`         VARCHAR(150)  NOT NULL,
  `password`      VARCHAR(255)  NOT NULL        COMMENT 'bcrypt hash',
  `role`          ENUM('farmer','worker') NOT NULL DEFAULT 'farmer',
  `is_active`     TINYINT(1)    NOT NULL DEFAULT 1,
  `last_login_at` TIMESTAMP     NULL     DEFAULT NULL,
  `created_at`    TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`    TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP
                                         ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_user_email`    (`email`),
  UNIQUE KEY `uq_user_username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
--  Part 3 — farmers
--  Depends on: users.
--  Farmer profile — extends users. A farmer can own multiple farms.
-- ============================================================
CREATE TABLE IF NOT EXISTS `farmers` (
  `id`         INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `user_id`    INT UNSIGNED  NOT NULL COMMENT 'FK → users.id',
  `first_name` VARCHAR(100)  DEFAULT NULL,
  `last_name`  VARCHAR(100)  DEFAULT NULL,
  `phone`      VARCHAR(30)   DEFAULT NULL,
  `created_at` TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP
                                      ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_farmer_user` (`user_id`),
  CONSTRAINT `fk_farmers_user`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
--  Part 4 — farms
--  Depends on: farmers.
--  A farm belongs to a farmer. A farmer can own multiple farms.
-- ============================================================
CREATE TABLE IF NOT EXISTS `farms` (
  `id`               INT UNSIGNED   NOT NULL AUTO_INCREMENT,
  `farmer_id`        INT UNSIGNED   NOT NULL COMMENT 'FK → farmers.id',
  `name`             VARCHAR(100)   NOT NULL,
  `location`         VARCHAR(150)   NOT NULL,
  `total_area`       DECIMAL(10,2)  DEFAULT NULL,
  `area_unit`        ENUM('hectares','acres') NOT NULL DEFAULT 'hectares',
  `irrigation_type`  ENUM('Drip System','Sprinkler','Flood','Manual','None')
                                   NOT NULL DEFAULT 'Drip System',
  `owner_name`       VARCHAR(100)   DEFAULT NULL,
  `contact_email`    VARCHAR(150)   DEFAULT NULL,
  `contact_phone`    VARCHAR(30)    DEFAULT NULL,
  `latitude`         DECIMAL(10,7)  DEFAULT NULL,
  `longitude`        DECIMAL(10,7)  DEFAULT NULL,
  `is_active`        TINYINT(1)     NOT NULL DEFAULT 1,
  `created_at`       TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`       TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP
                                             ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  -- Scoped uniqueness: two farmers can share a farm name
  UNIQUE KEY `uq_farm_name_per_farmer` (`farmer_id`, `name`),
  CONSTRAINT `fk_farms_farmer`
    FOREIGN KEY (`farmer_id`) REFERENCES `farmers` (`id`)
    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
--  Part 5 — zones
--  Depends on: farms.
--  Static zone config. Live sensor readings stay in zones.json.
-- ============================================================
CREATE TABLE IF NOT EXISTS `zones` (
  `id`         VARCHAR(20)   NOT NULL COMMENT 'e.g. zone_A',
  `farm_id`    INT UNSIGNED  NOT NULL COMMENT 'FK → farms.id',
  `name`       VARCHAR(100)  NOT NULL COMMENT 'e.g. West Garden',
  `area`       DECIMAL(10,2) NOT NULL COMMENT 'Area in hectares',
  `crop`       VARCHAR(100)  DEFAULT NULL,
  `latitude`   DECIMAL(10,7) DEFAULT NULL,
  `longitude`  DECIMAL(10,7) DEFAULT NULL,
  `is_active`  TINYINT(1)    NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP
                                      ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_zones_farm`
    FOREIGN KEY (`farm_id`) REFERENCES `farms` (`id`)
    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
--  Part 6 — workers
--  Depends on: users, farms.
--  Worker profile — extends users. Created by a farmer, tied to a farm.
-- ============================================================
CREATE TABLE IF NOT EXISTS `workers` (
  `id`              INT UNSIGNED   NOT NULL AUTO_INCREMENT,
  `user_id`         INT UNSIGNED   NOT NULL COMMENT 'FK → users.id',
  `farm_id`         INT UNSIGNED   NOT NULL COMMENT 'FK → farms.id',
  `first_name`      VARCHAR(100)   DEFAULT NULL,
  `last_name`       VARCHAR(100)   DEFAULT NULL,
  `phone`           VARCHAR(30)    DEFAULT NULL,
  `job_title`       VARCHAR(100)   DEFAULT NULL COMMENT 'e.g. Field Operator',
  `salary`          DECIMAL(10,2)  DEFAULT NULL,
  `salary_currency` VARCHAR(10)    NOT NULL DEFAULT 'USD',
  `created_at`      TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`      TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP
                                            ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_worker_user` (`user_id`),
  CONSTRAINT `fk_workers_user`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_workers_farm`
    FOREIGN KEY (`farm_id`) REFERENCES `farms` (`id`)
    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
--  Part 7 — production
--  Depends on: farms, zones.
-- ============================================================
CREATE TABLE IF NOT EXISTS `production` (
  `id`               INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `farm_id`          INT UNSIGNED  NOT NULL COMMENT 'FK → farms.id',
  `zone_id`          VARCHAR(20)   DEFAULT NULL COMMENT 'FK → zones.id',
  `crop`             VARCHAR(100)  NOT NULL,
  `area`             DECIMAL(10,2) NOT NULL,
  `area_unit`        ENUM('hectares','acres') NOT NULL DEFAULT 'hectares',
  `season`           ENUM('winter','spring','summer','autumn') DEFAULT NULL,
  `planting_date`    DATE          NOT NULL,
  `expected_harvest` DATE          DEFAULT NULL,
  `actual_harvest`   DATE          DEFAULT NULL,
  `status`           ENUM('planned','growing','harvested','failed')
                                  NOT NULL DEFAULT 'planned',
  `yield`            DECIMAL(10,2) DEFAULT NULL,
  `yield_unit`       VARCHAR(20)   DEFAULT 'tons',
  `notes`            TEXT          DEFAULT NULL,
  `created_at`       TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`       TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP
                                            ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_production_farm`
    FOREIGN KEY (`farm_id`) REFERENCES `farms` (`id`)
    ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_production_zone`
    FOREIGN KEY (`zone_id`) REFERENCES `zones` (`id`)
    ON DELETE SET NULL ON UPDATE CASCADE,
  INDEX `idx_production_status` (`status`),
  INDEX `idx_production_season` (`season`),
  INDEX `idx_production_zone`   (`zone_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
--  Part 8 — stock
--  Depends on: farms.
-- ============================================================
CREATE TABLE IF NOT EXISTS `stock` (
  `id`            INT UNSIGNED   NOT NULL AUTO_INCREMENT,
  `farm_id`       INT UNSIGNED   NOT NULL COMMENT 'FK → farms.id',
  `name`          VARCHAR(150)   NOT NULL,
  `category`      ENUM('Seeds','Fertilizers','Pesticides','Equipment','Fuel','Other')
                                 NOT NULL DEFAULT 'Other',
  `quantity`      DECIMAL(10,2)  NOT NULL DEFAULT 0,
  `unit`          VARCHAR(30)    NOT NULL,
  `min_threshold` DECIMAL(10,2)  NOT NULL DEFAULT 0
                                 COMMENT 'Triggers low-stock alert',
  `location`      VARCHAR(100)   DEFAULT NULL,
  `supplier`      VARCHAR(100)   DEFAULT NULL,
  `expiry_date`   DATE           DEFAULT NULL,
  `last_updated`  DATE           DEFAULT NULL,
  `created_at`    TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`    TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP
                                          ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_stock_farm`
    FOREIGN KEY (`farm_id`) REFERENCES `farms` (`id`)
    ON DELETE RESTRICT ON UPDATE CASCADE,
  INDEX `idx_stock_category` (`category`),
  INDEX `idx_stock_location`  (`location`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;