-- ============================================================
-- PHP Security Labs – Database Schema (complete, updated)
-- ============================================================
--
--   DATABASE 1: php_security_labs_app
--     • users, sessions, progress, system_logs (application runtime)
--   DATABASE 2: php_security_labs_challenges
--     • agents, units, leaders, technologies, cases, communications,
--       reports, secrets, notifications, audit_trail (military simulation)
--
-- All tables use InnoDB. Foreign keys enforce referential integrity
-- within the simulation database. No cross-database relations.
-- ============================================================

-- ------------------------------------------------------------
-- 1. APPLICATION DATABASE (secure core)
-- ------------------------------------------------------------
CREATE DATABASE IF NOT EXISTS php_security_labs_app
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE php_security_labs_app;

CREATE TABLE IF NOT EXISTS users (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username   VARCHAR(50)  NOT NULL UNIQUE,
    email      VARCHAR(100) NOT NULL UNIQUE,
    password   VARCHAR(255) NOT NULL,
    role       VARCHAR(20)  NOT NULL DEFAULT 'user',
    is_admin   TINYINT(1)   NOT NULL DEFAULT 0,           -- replaces internal_admins
    created_at DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_role (role)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS sessions_lab (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id       INT UNSIGNED NOT NULL,
    session_token VARCHAR(255) NOT NULL UNIQUE,
    is_admin      TINYINT(1)   NOT NULL DEFAULT 0,
    created_at    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_sessions_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user (user_id),
    INDEX idx_token (session_token)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS lab_progress (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id      INT UNSIGNED NOT NULL,
    lab_name     VARCHAR(50)  NOT NULL,
    challenge    VARCHAR(50)  NOT NULL,
    completed    TINYINT(1)   NOT NULL DEFAULT 0,
    completed_at DATETIME     NULL DEFAULT NULL,
    CONSTRAINT fk_progress_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY uq_user_lab_challenge (user_id, lab_name, challenge),
    INDEX idx_user_lab (user_id, lab_name)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS system_logs (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id    INT UNSIGNED NULL,
    action     VARCHAR(255) NOT NULL,
    created_at DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_user (user_id),
    INDEX idx_created (created_at)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- 2. MILITARY SIMULATION DATABASE (vulnerability playground)
-- ------------------------------------------------------------
CREATE DATABASE IF NOT EXISTS php_security_labs_challenges
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE php_security_labs_challenges;

-- --- Base look‑up tables -----------------------------------
CREATE TABLE IF NOT EXISTS leaders (
    id       INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name     VARCHAR(100) NOT NULL,
    position VARCHAR(50)  NOT NULL,
    division VARCHAR(100) NOT NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS units (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name         VARCHAR(100) NOT NULL,
    type         VARCHAR(50)  NOT NULL,                   -- infantry, cyber, intelligence
    commander_id INT UNSIGNED NULL,
    CONSTRAINT fk_units_commander FOREIGN KEY (commander_id) REFERENCES leaders(id) ON DELETE SET NULL,
    INDEX idx_type (type)
) ENGINE=InnoDB;

-- --- Core simulation tables --------------------------------
CREATE TABLE IF NOT EXISTS agents (
    id               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    codename         VARCHAR(50)  NOT NULL,
    real_name        VARCHAR(100) NOT NULL,
    clearance_level  INT          NOT NULL DEFAULT 1,
    unit_id          INT UNSIGNED NULL,
    status           VARCHAR(20)  NOT NULL DEFAULT 'active',
    CONSTRAINT fk_agents_unit FOREIGN KEY (unit_id) REFERENCES units(id) ON DELETE SET NULL,
    INDEX idx_codename (codename),
    INDEX idx_status (status)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS cases (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title         VARCHAR(150) NOT NULL,
    description   TEXT         NOT NULL,
    status        VARCHAR(50)  NOT NULL DEFAULT 'open',
    assigned_unit INT UNSIGNED NULL,
    priority      VARCHAR(20)  NOT NULL DEFAULT 'normal',
    created_at    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_cases_unit FOREIGN KEY (assigned_unit) REFERENCES units(id) ON DELETE SET NULL,
    INDEX idx_status (status),
    INDEX idx_priority (priority)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS agent_case (
    id        INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    agent_id  INT UNSIGNED NOT NULL,
    case_id   INT UNSIGNED NOT NULL,
    role      VARCHAR(50)  NOT NULL DEFAULT 'operative',
    CONSTRAINT fk_ac_agent FOREIGN KEY (agent_id) REFERENCES agents(id) ON DELETE CASCADE,
    CONSTRAINT fk_ac_case  FOREIGN KEY (case_id)  REFERENCES cases(id)  ON DELETE CASCADE,
    UNIQUE KEY uq_agent_case (agent_id, case_id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS communications (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    sender_id    INT UNSIGNED NULL,
    recipient_id INT UNSIGNED NULL,                       -- added for realism
    message      TEXT         NOT NULL,
    is_encrypted TINYINT(1)   NOT NULL DEFAULT 0,
    created_at   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_comm_sender FOREIGN KEY (sender_id) REFERENCES agents(id) ON DELETE SET NULL,
    CONSTRAINT fk_comm_recip   FOREIGN KEY (recipient_id) REFERENCES agents(id) ON DELETE SET NULL,
    INDEX idx_sender (sender_id),
    INDEX idx_recipient (recipient_id),
    INDEX idx_created (created_at)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS reports (
    id                INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    agent_id          INT UNSIGNED NULL,
    report            TEXT         NOT NULL,
    reviewed_by_admin TINYINT(1)   NOT NULL DEFAULT 0,
    created_at        DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_reports_agent FOREIGN KEY (agent_id) REFERENCES agents(id) ON DELETE SET NULL,
    INDEX idx_reviewed (reviewed_by_admin),
    INDEX idx_agent (agent_id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS secrets (
    id                   INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    secret_key           VARCHAR(255) NOT NULL UNIQUE,
    description          TEXT         NOT NULL,
    classification_level INT          NOT NULL DEFAULT 10,
    source               VARCHAR(100) NULL,               -- where the secret came from
    leaked               TINYINT(1)   NOT NULL DEFAULT 0  -- has it been stolen?
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS technologies (
    id                   INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name                 VARCHAR(100) NOT NULL,
    description          TEXT         NOT NULL,
    classification_level INT          NOT NULL DEFAULT 1,
    status               VARCHAR(50)  NOT NULL DEFAULT 'active',
    INDEX idx_classification (classification_level)
) ENGINE=InnoDB;

-- Extra tables that enrich attack scenarios ---------------
CREATE TABLE IF NOT EXISTS notifications (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id    INT UNSIGNED NULL,                          -- references users.id in app DB, no FK
    title      VARCHAR(255) NOT NULL,
    message    TEXT         NOT NULL,
    is_read    TINYINT(1)   NOT NULL DEFAULT 0,
    created_at DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_user (user_id),
    INDEX idx_read (is_read)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS audit_trail (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id    INT UNSIGNED NULL,
    action     VARCHAR(255) NOT NULL,
    ip_address VARCHAR(45)  NULL,
    user_agent TEXT         NULL,
    created_at DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_user (user_id),
    INDEX idx_created (created_at)
) ENGINE=InnoDB;

ALTER TABLE communications 
ADD COLUMN read_status TINYINT(1) DEFAULT 0,
ADD COLUMN attachment_url VARCHAR(255) NULL;

ALTER TABLE secrets 
ADD COLUMN owner_agent_id INT UNSIGNED NULL,
ADD COLUMN case_id INT UNSIGNED NULL,
ADD CONSTRAINT fk_secrets_agent FOREIGN KEY (owner_agent_id) REFERENCES agents(id) ON DELETE SET NULL,
ADD CONSTRAINT fk_secrets_case FOREIGN KEY (case_id) REFERENCES cases(id) ON DELETE SET NULL;

ALTER TABLE notifications 
ADD COLUMN source_type VARCHAR(50) DEFAULT 'app',
ADD COLUMN source_id INT UNSIGNED NULL;

ALTER TABLE audit_trail 
ADD COLUMN action_type VARCHAR(50) DEFAULT 'system';
-- ------------------------------------------------------------
-- 3. LAB SETTINGS TABLE (for LabEngine persistence)
-- ------------------------------------------------------------
USE php_security_labs_app;

CREATE TABLE IF NOT EXISTS lab_settings (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id         INT UNSIGNED NOT NULL,
    vulnerabilities JSON         NOT NULL,
    created_at      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_lab_settings_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY uq_user_lab_settings (user_id),
    INDEX idx_user (user_id)
) ENGINE=InnoDB;
