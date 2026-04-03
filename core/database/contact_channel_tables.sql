-- Run this SQL in phpMyAdmin (or any MySQL client) on database: wintersm_tt
-- Fixes: Table 'contact_channel_integrations' doesn't exist

-- 1. Contact channel integrations
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

-- 2. Contact channel messages (depends on contact_channel_integrations)
CREATE TABLE IF NOT EXISTS `contact_channel_messages` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `contact_channel_integration_id` bigint unsigned DEFAULT NULL,
  `support_ticket_id` bigint unsigned DEFAULT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `channel` varchar(32) NOT NULL,
  `direction` varchar(16) NOT NULL DEFAULT 'inbound',
  `remote_chat_id` varchar(255) DEFAULT NULL,
  `remote_message_id` varchar(255) DEFAULT NULL,
  `sender_name` varchar(255) DEFAULT NULL,
  `sender_handle` varchar(255) DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `attachments` json DEFAULT NULL,
  `metadata` json DEFAULT NULL,
  `status` varchar(32) NOT NULL DEFAULT 'queued',
  `delivered_at` timestamp NULL DEFAULT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `contact_channel_messages_channel_remote_message_id_unique` (`channel`,`remote_message_id`),
  KEY `contact_channel_messages_contact_channel_integration_id_foreign` (`contact_channel_integration_id`),
  KEY `contact_channel_messages_support_ticket_id_foreign` (`support_ticket_id`),
  KEY `contact_channel_messages_user_id_foreign` (`user_id`),
  KEY `contact_channel_messages_channel_index` (`channel`),
  KEY `contact_channel_messages_direction_index` (`direction`),
  KEY `contact_channel_messages_remote_chat_id_index` (`remote_chat_id`),
  KEY `contact_channel_messages_remote_message_id_index` (`remote_message_id`),
  CONSTRAINT `contact_channel_messages_contact_channel_integration_id_foreign` FOREIGN KEY (`contact_channel_integration_id`) REFERENCES `contact_channel_integrations` (`id`) ON DELETE SET NULL,
  CONSTRAINT `contact_channel_messages_support_ticket_id_foreign` FOREIGN KEY (`support_ticket_id`) REFERENCES `support_tickets` (`id`) ON DELETE SET NULL,
  CONSTRAINT `contact_channel_messages_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
