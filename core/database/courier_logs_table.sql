-- Courier Logs table: stores all courier API transactions (লেনদেন) for Steadfast and Pathao.
-- Run this if migration is not used (e.g. phpMyAdmin). Use your actual database name.

CREATE TABLE IF NOT EXISTS `courier_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint unsigned NOT NULL,
  `courier_type` varchar(50) NOT NULL,
  `courier_order_id` varchar(255) DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'pending',
  `request_data` text,
  `response_data` text,
  `error_message` text,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `courier_logs_order_id_index` (`order_id`),
  KEY `courier_logs_courier_type_index` (`courier_type`),
  KEY `courier_logs_status_index` (`status`),
  KEY `courier_logs_created_at_index` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
