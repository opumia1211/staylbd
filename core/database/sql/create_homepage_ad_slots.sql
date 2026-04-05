-- Run in phpMyAdmin / MySQL if the table is missing or ERROR 1932 (ghost InnoDB table).
-- Database: your app DB (e.g. wintersm_tt)
--
-- If you get ERROR 1813 (tablespace exists): stop MySQL, delete
--   mysql/data/<db>/homepage_ad_slots.ibd
-- then start MySQL and run this script again.

DROP TABLE IF EXISTS `homepage_ad_slots`;

CREATE TABLE `homepage_ad_slots` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `admin_title` varchar(191) NOT NULL,
  `advertiser_name` varchar(191) DEFAULT NULL,
  `image` varchar(512) DEFAULT NULL,
  `source_type` varchar(24) NOT NULL DEFAULT 'upload',
  `external_url` text DEFAULT NULL,
  `link_url` varchar(512) DEFAULT NULL,
  `open_new_tab` tinyint(1) NOT NULL DEFAULT 1,
  `frame_style` varchar(32) NOT NULL DEFAULT 'thin',
  `width_mode` varchar(16) NOT NULL DEFAULT 'full',
  `position` varchar(32) NOT NULL DEFAULT 'custom',
  `display_pages` varchar(24) NOT NULL DEFAULT 'all',
  `custom_path` varchar(255) DEFAULT NULL,
  `side` varchar(32) DEFAULT NULL,
  `top` int DEFAULT NULL,
  `bottom` int DEFAULT NULL,
  `left` int DEFAULT NULL,
  `right` int DEFAULT NULL,
  `max_height_px` smallint unsigned DEFAULT NULL,
  `size_type` varchar(16) NOT NULL DEFAULT 'auto',
  `custom_width` varchar(16) DEFAULT NULL,
  `custom_height` varchar(16) DEFAULT NULL,
  `z_index` int NOT NULL DEFAULT 1100,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int unsigned NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `homepage_ad_slots_is_active_sort_order_index` (`is_active`,`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Optional: restore rows from backup N/wintersm_tt.sql (edit paths/images if needed)
INSERT INTO `homepage_ad_slots` (`id`, `admin_title`, `advertiser_name`, `image`, `source_type`, `external_url`, `link_url`, `open_new_tab`, `frame_style`, `width_mode`, `position`, `display_pages`, `custom_path`, `side`, `top`, `bottom`, `left`, `right`, `max_height_px`, `size_type`, `custom_width`, `custom_height`, `z_index`, `is_active`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 'vb', NULL, '69ba6fa765e181773825959.jpg', 'upload', NULL, NULL, 1, 'card', 'half', 'custom', 'all', NULL, 'bottom', 103, 164, 80, 81, 80, 'custom', '305px', '93px', 1100, 1, 1, '2026-03-18 03:25:59', '2026-04-02 10:55:17'),
(2, 'hrfh', 'fhrhj', '69c659ae5160b1774606766.jpg', 'upload', NULL, NULL, 1, 'thin', 'full', 'custom', 'all', NULL, 'bottom', 282, 12, 23, 23, 400, 'auto', NULL, NULL, 1100, 1, 2, '2026-03-27 04:19:26', '2026-04-02 11:10:52');
