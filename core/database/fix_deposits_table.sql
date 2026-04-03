-- Fix: Create deposits table in wintersm_tt (or your app database)
-- Run this in phpMyAdmin or MySQL: select database wintersm_tt then run.
-- Kept in sync with: core/database/migrations/2026_02_16_160000_create_deposits_table.php

-- Remove broken/corrupt table if exists (fixes error 1932 "doesn't exist in engine")
DROP TABLE IF EXISTS `deposits`;

CREATE TABLE `deposits` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `order_id` bigint unsigned NOT NULL DEFAULT 0,
  `method_code` int unsigned NOT NULL,
  `method_currency` varchar(20) NOT NULL,
  `amount` decimal(18,8) NOT NULL DEFAULT 0.00000000,
  `charge` decimal(18,8) NOT NULL DEFAULT 0.00000000,
  `rate` decimal(18,8) NOT NULL DEFAULT 0.00000000,
  `final_amo` decimal(18,8) NOT NULL DEFAULT 0.00000000,
  `btc_amo` decimal(18,8) NOT NULL DEFAULT 0.00000000,
  `btc_wallet` varchar(255) DEFAULT NULL,
  `trx` varchar(100) NOT NULL,
  `status` tinyint unsigned NOT NULL DEFAULT 0,
  `detail` json DEFAULT NULL,
  `admin_feedback` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `deposits_trx_unique` (`trx`),
  KEY `deposits_user_id_foreign` (`user_id`),
  KEY `deposits_order_id_index` (`order_id`),
  KEY `deposits_method_code_index` (`method_code`),
  KEY `deposits_status_index` (`status`),
  KEY `deposits_created_at_index` (`created_at`),
  CONSTRAINT `deposits_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
