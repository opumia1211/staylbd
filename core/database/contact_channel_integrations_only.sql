-- Run this in phpMyAdmin on database: wintersm_tt
-- Fixes: Table 'contact_channel_integrations' doesn't exist
-- Use this if you only need to fix the error (no messages table).

CREATE TABLE IF NOT EXISTS `contact_channel_integrations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `channel` varchar(32) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_primary` tinyint(1) NOT NULL DEFAULT 0,
  `settings` json DEFAULT NULL,
  `auth_meta` json DEFAULT NULL,
  `last_synced_at` timestamp NULL DEFAULT NULL,
  `last_error_at` timestamp NULL DEFAULT NULL,
  `last_error_message` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `contact_channel_integrations_channel_index` (`channel`),
  KEY `contact_channel_integrations_is_active_index` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
