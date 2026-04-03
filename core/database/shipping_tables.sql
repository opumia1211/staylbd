-- ============================================================
-- Shipping Module Tables - Run this SQL in your database
-- (e.g. wintersm_tt or your app database)
-- Run each section; if a table/column already exists, skip that part.
-- ============================================================

-- 1. Shipping Zones
CREATE TABLE IF NOT EXISTS `shipping_zones` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `type` varchar(20) NOT NULL DEFAULT 'national',
  `status` tinyint unsigned NOT NULL DEFAULT 1,
  `base_price` decimal(18,2) NOT NULL DEFAULT '0.00',
  `estimated_days` varchar(50) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `shipping_zones_status_type_index` (`status`,`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Shipping Zone Countries (for international zones)
CREATE TABLE IF NOT EXISTS `shipping_zone_countries` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `shipping_zone_id` bigint unsigned NOT NULL,
  `country_iso` varchar(5) NOT NULL,
  `country_name` varchar(100) DEFAULT NULL,
  `status` tinyint unsigned NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `shipping_zone_countries_zone_iso_unique` (`shipping_zone_id`,`country_iso`),
  KEY `shipping_zone_countries_country_iso_index` (`country_iso`),
  CONSTRAINT `shipping_zone_countries_zone_foreign` FOREIGN KEY (`shipping_zone_id`) REFERENCES `shipping_zones` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Shipping Zone Areas (for national zones - e.g. districts)
CREATE TABLE IF NOT EXISTS `shipping_zone_areas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `shipping_zone_id` bigint unsigned NOT NULL,
  `area_name` varchar(100) NOT NULL,
  `district_names` json DEFAULT NULL,
  `status` tinyint unsigned NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `shipping_zone_areas_zone_id_index` (`shipping_zone_id`),
  CONSTRAINT `shipping_zone_areas_zone_foreign` FOREIGN KEY (`shipping_zone_id`) REFERENCES `shipping_zones` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Shipping Rules (global settings)
CREATE TABLE IF NOT EXISTS `shipping_rules` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `free_shipping_min_amount` decimal(18,2) DEFAULT NULL,
  `cod_extra_charge` decimal(18,2) NOT NULL DEFAULT '0.00',
  `express_extra_charge` decimal(18,2) NOT NULL DEFAULT '0.00',
  `international_enabled` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default shipping rules (run once)
INSERT INTO `shipping_rules` (`free_shipping_min_amount`,`cod_extra_charge`,`express_extra_charge`,`international_enabled`,`created_at`,`updated_at`)
SELECT 5000, 0, 50, 1, NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `shipping_rules` LIMIT 1);

-- 5. Add columns to shipping_methods (run one by one; ignore "Duplicate column" errors)
ALTER TABLE `shipping_methods` ADD COLUMN `shipping_zone_id` bigint unsigned NULL;
ALTER TABLE `shipping_methods` ADD COLUMN `base_price` decimal(18,2) NOT NULL DEFAULT 0;
ALTER TABLE `shipping_methods` ADD COLUMN `price_per_kg` decimal(18,2) NULL;
ALTER TABLE `shipping_methods` ADD COLUMN `estimated_days` varchar(50) NULL;
ALTER TABLE `shipping_methods` ADD COLUMN `courier_name` varchar(100) NULL;
ALTER TABLE `shipping_methods` ADD COLUMN `is_express` tinyint(1) NOT NULL DEFAULT 0;
ALTER TABLE `shipping_methods` ADD COLUMN `weight_limit_kg` decimal(10,2) NULL;
ALTER TABLE `shipping_methods` ADD CONSTRAINT `shipping_methods_zone_foreign` FOREIGN KEY (`shipping_zone_id`) REFERENCES `shipping_zones` (`id`) ON DELETE SET NULL;

-- 6. Add columns to orders
ALTER TABLE `orders` ADD COLUMN `shipping_zone_id` bigint unsigned NULL;
ALTER TABLE `orders` ADD COLUMN `delivery_estimate` varchar(100) NULL;
ALTER TABLE `orders` ADD COLUMN `courier_name` varchar(100) NULL;
ALTER TABLE `orders` ADD CONSTRAINT `orders_shipping_zone_foreign` FOREIGN KEY (`shipping_zone_id`) REFERENCES `shipping_zones` (`id`) ON DELETE SET NULL;

-- 7. Seed default zones (run after table shipping_zones exists)
INSERT INTO `shipping_zones` (`name`,`type`,`status`,`base_price`,`estimated_days`,`created_at`,`updated_at`)
SELECT 'Inside Dhaka','national',1,60.00,'2-3 Days',NOW(),NOW() FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM shipping_zones WHERE name = 'Inside Dhaka' LIMIT 1);
INSERT INTO `shipping_zones` (`name`,`type`,`status`,`base_price`,`estimated_days`,`created_at`,`updated_at`)
SELECT 'Outside Dhaka','national',1,120.00,'3-5 Days',NOW(),NOW() FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM shipping_zones WHERE name = 'Outside Dhaka' LIMIT 1);
INSERT INTO `shipping_zones` (`name`,`type`,`status`,`base_price`,`estimated_days`,`created_at`,`updated_at`)
SELECT 'Remote Area','national',1,150.00,'5-7 Days',NOW(),NOW() FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM shipping_zones WHERE name = 'Remote Area' LIMIT 1);
INSERT INTO `shipping_zones` (`name`,`type`,`status`,`base_price`,`estimated_days`,`created_at`,`updated_at`)
SELECT 'International Standard','international',1,1200.00,'7-15 Days',NOW(),NOW() FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM shipping_zones WHERE name = 'International Standard' LIMIT 1);

-- 8. Seed default methods (one per zone; run after shipping_methods has shipping_zone_id column)
INSERT INTO `shipping_methods` (`name`,`price`,`status`,`shipping_zone_id`,`base_price`,`estimated_days`,`created_at`,`updated_at`)
SELECT 'Inside Dhaka',60,1,z.id,60.00,'2-3 Days',NOW(),NOW() FROM shipping_zones z WHERE z.name = 'Inside Dhaka' AND NOT EXISTS (SELECT 1 FROM shipping_methods m WHERE m.name = 'Inside Dhaka' LIMIT 1) LIMIT 1;
INSERT INTO `shipping_methods` (`name`,`price`,`status`,`shipping_zone_id`,`base_price`,`estimated_days`,`created_at`,`updated_at`)
SELECT 'Outside Dhaka',120,1,z.id,120.00,'3-5 Days',NOW(),NOW() FROM shipping_zones z WHERE z.name = 'Outside Dhaka' AND NOT EXISTS (SELECT 1 FROM shipping_methods m WHERE m.name = 'Outside Dhaka' LIMIT 1) LIMIT 1;
INSERT INTO `shipping_methods` (`name`,`price`,`status`,`shipping_zone_id`,`base_price`,`estimated_days`,`created_at`,`updated_at`)
SELECT 'Remote Area',150,1,z.id,150.00,'5-7 Days',NOW(),NOW() FROM shipping_zones z WHERE z.name = 'Remote Area' AND NOT EXISTS (SELECT 1 FROM shipping_methods m WHERE m.name = 'Remote Area' LIMIT 1) LIMIT 1;
INSERT INTO `shipping_methods` (`name`,`price`,`status`,`shipping_zone_id`,`base_price`,`estimated_days`,`created_at`,`updated_at`)
SELECT 'International Standard',1200,1,z.id,1200.00,'7-15 Days',NOW(),NOW() FROM shipping_zones z WHERE z.name = 'International Standard' AND NOT EXISTS (SELECT 1 FROM shipping_methods m WHERE m.name = 'International Standard' LIMIT 1) LIMIT 1;
