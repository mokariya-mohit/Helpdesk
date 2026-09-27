-- ==========================================================
-- Helpdesk Team Chat / Internal User Messaging Schema
-- Production Ready Migration for MySQL 5.7+ & 8.0+
-- ==========================================================

-- 1. Add last_seen_at column to users table if not exists
SET @dbname = DATABASE();
SET @tablename = "users";
SET @columnname = "last_seen_at";
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (column_name = @columnname)
  ) > 0,
  "SELECT 1",
  "ALTER TABLE users ADD COLUMN last_seen_at DATETIME NULL AFTER modified"
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- 2. Table: chat_requests
CREATE TABLE IF NOT EXISTS `chat_requests` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `sender_id` INT NOT NULL,
  `receiver_id` INT NOT NULL,
  `initial_message` TEXT NULL,
  `status` ENUM('pending', 'accepted', 'rejected') NOT NULL DEFAULT 'pending',
  `accepted_at` DATETIME NULL,
  `rejected_at` DATETIME NULL,
  `created` DATETIME NOT NULL,
  `modified` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_cr_sender_id` (`sender_id`),
  KEY `idx_cr_receiver_id` (`receiver_id`),
  KEY `idx_cr_status` (`status`),
  KEY `idx_cr_sender_receiver` (`sender_id`, `receiver_id`),
  CONSTRAINT `fk_chat_requests_sender` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_chat_requests_receiver` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Table: chat_conversations
CREATE TABLE IF NOT EXISTS `chat_conversations` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `created_by` INT NOT NULL,
  `type` ENUM('direct', 'group') NOT NULL DEFAULT 'direct',
  `title` VARCHAR(255) NULL,
  `description` TEXT NULL,
  `icon` VARCHAR(255) NULL,
  `status` VARCHAR(20) NOT NULL DEFAULT 'active',
  `created` DATETIME NOT NULL,
  `modified` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_cc_created_by` (`created_by`),
  KEY `idx_cc_modified` (`modified`),
  CONSTRAINT `fk_chat_conversations_creator` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Table: chat_conversation_users
CREATE TABLE IF NOT EXISTS `chat_conversation_users` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `conversation_id` INT NOT NULL,
  `user_id` INT NOT NULL,
  `role` ENUM('admin', 'member') NOT NULL DEFAULT 'member',
  `joined_at` DATETIME NOT NULL,
  `last_read_at` DATETIME NULL,
  `cleared_at` DATETIME NULL,
  `typing_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_ccu_conv_user` (`conversation_id`, `user_id`),
  KEY `idx_ccu_user_conv` (`user_id`, `conversation_id`),
  CONSTRAINT `fk_ccu_conversation` FOREIGN KEY (`conversation_id`) REFERENCES `chat_conversations` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_ccu_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Table: chat_messages
CREATE TABLE IF NOT EXISTS `chat_messages` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `conversation_id` INT NOT NULL,
  `sender_id` INT NOT NULL,
  `message` TEXT NULL,
  `message_type` VARCHAR(20) NOT NULL DEFAULT 'text',
  `attachment_path` VARCHAR(255) NULL,
  `attachment_name` VARCHAR(255) NULL,
  `attachment_size` INT NULL DEFAULT 0,
  `attachment_type` VARCHAR(50) NULL,
  `is_deleted` TINYINT(1) NOT NULL DEFAULT 0,
  `is_edited` TINYINT(1) NOT NULL DEFAULT 0,
  `edited_at` DATETIME NULL,
  `created` DATETIME NOT NULL,
  `modified` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_cm_conversation_created` (`conversation_id`, `created`),
  KEY `idx_cm_sender` (`sender_id`),
  CONSTRAINT `fk_chat_messages_conv` FOREIGN KEY (`conversation_id`) REFERENCES `chat_conversations` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_chat_messages_sender` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Table: chat_message_attachments (Supports up to 10 files per message)
CREATE TABLE IF NOT EXISTS `chat_message_attachments` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `message_id` INT NOT NULL,
  `file_path` VARCHAR(255) NOT NULL,
  `file_name` VARCHAR(255) NOT NULL,
  `file_size` INT NOT NULL DEFAULT 0,
  `file_type` VARCHAR(50) NOT NULL,
  `created` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_cma_message_id` (`message_id`),
  CONSTRAINT `fk_cma_message` FOREIGN KEY (`message_id`) REFERENCES `chat_messages` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. Table: chat_message_reactions (Microsoft Teams style emoji reactions)
CREATE TABLE IF NOT EXISTS `chat_message_reactions` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `message_id` INT NOT NULL,
  `user_id` INT NOT NULL,
  `reaction` VARCHAR(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `created` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_msg_user` (`message_id`, `user_id`),
  KEY `idx_cmr_message` (`message_id`),
  KEY `idx_cmr_user` (`user_id`),
  CONSTRAINT `fk_cmr_message` FOREIGN KEY (`message_id`) REFERENCES `chat_messages` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_cmr_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

