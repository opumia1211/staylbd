-- Run this in phpMyAdmin / MySQL if `php artisan migrate` is not used.
-- Database: your app DB (e.g. wintersm_tt)

CREATE TABLE IF NOT EXISTS `homepage_custom_product_rows` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(191) NOT NULL,
  `subtitle` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` smallint unsigned NOT NULL DEFAULT 0,
  `source_type` varchar(20) NOT NULL,
  `category_id` bigint unsigned DEFAULT NULL,
  `product_ids` json DEFAULT NULL,
  `product_limit` tinyint unsigned NOT NULL DEFAULT 12,
  `interval_seconds` tinyint unsigned DEFAULT NULL,
  `view_all_url` varchar(512) DEFAULT NULL,
  `view_all_label` varchar(120) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `hp_custom_rows_active_sort` (`is_active`,`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- If MySQL says JSON unknown, replace `product_ids` json with:
-- `product_ids` longtext DEFAULT NULL,
