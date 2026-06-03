-- =============================================================================
-- StayLBD Database Update Guide (incremental patches)
-- Generated/synced: 2026-06-03 (admin menu restructure — no new tables required)
--
-- Admin panel (2026-06-03): Catalog / Products / Categories menus split;
-- Product Center & Category Center hubs added (routes only, no schema change).
-- Promotional ads moved under Home Layout. Run: cd core && php artisan migrate --force
--
-- Previous sync: 2026-05-26 (staylbd_wintersm — full export verified)
--
-- MASTER SCHEMA (full fresh install):
--   core/database/staylbd_wintersm.sql
--   Backup copy: core/database/staylbd_wintersm_backup.sql
--
-- Local workflow (XAMPP):
--   cd core
--   C:\xampp\php\php.exe artisan migrate --force
--   C:\xampp\php\php.exe artisan staylbd:health-check
--   C:\xampp\php\php.exe artisan staylbd:export-master-sql --mysqldump=C:\xampp\mysql\bin\mysqldump.exe
--
-- Status: All Laravel migrations applied. Health check: 11/11 OK.
-- =============================================================================

-- -----------------------------------------------------------------------------
-- 1) Products: advanced pricing & attributes (safe on MariaDB 10.4+)
-- -----------------------------------------------------------------------------
ALTER TABLE `products`
    ADD COLUMN IF NOT EXISTS `original_price` DECIMAL(28,8) DEFAULT 0.00000000 COMMENT 'MSRP / strike-through price' AFTER `price`,
    ADD COLUMN IF NOT EXISTS `profit_margin` DECIMAL(28,8) DEFAULT 0.00000000 COMMENT 'Internal profit margin' AFTER `original_price`,
    ADD COLUMN IF NOT EXISTS `low_stock_alert` INT(11) DEFAULT 5 AFTER `quantity`,
    ADD COLUMN IF NOT EXISTS `warehouse_location` VARCHAR(255) NULL AFTER `low_stock_alert`,
    ADD COLUMN IF NOT EXISTS `shipping_weight` DECIMAL(10,2) DEFAULT 0.00 AFTER `warehouse_location`,
    ADD COLUMN IF NOT EXISTS `shipping_class` VARCHAR(100) NULL AFTER `shipping_weight`,
    ADD COLUMN IF NOT EXISTS `product_type` VARCHAR(50) DEFAULT 'physical' AFTER `shipping_class`,
    ADD COLUMN IF NOT EXISTS `fabric_type` VARCHAR(100) NULL AFTER `product_type`,
    ADD COLUMN IF NOT EXISTS `material` VARCHAR(100) NULL AFTER `fabric_type`,
    ADD COLUMN IF NOT EXISTS `season` VARCHAR(100) NULL AFTER `material`,
    ADD COLUMN IF NOT EXISTS `color_variants` TEXT NULL AFTER `season`,
    ADD COLUMN IF NOT EXISTS `source_url` VARCHAR(255) NULL AFTER `color_variants`,
    ADD COLUMN IF NOT EXISTS `target_gender` VARCHAR(50) NULL AFTER `source_url`,
    ADD COLUMN IF NOT EXISTS `target_age_min` INT(11) NULL AFTER `target_gender`,
    ADD COLUMN IF NOT EXISTS `target_age_max` INT(11) NULL AFTER `target_age_min`,
    ADD COLUMN IF NOT EXISTS `delivery_type` VARCHAR(20) NOT NULL DEFAULT 'free' COMMENT 'free|paid' AFTER `target_age_max`,
    ADD COLUMN IF NOT EXISTS `delivery_charge` DECIMAL(28,8) DEFAULT 0.00000000 AFTER `delivery_type`,
    ADD COLUMN IF NOT EXISTS `trending_now` TINYINT(1) NOT NULL DEFAULT 0 AFTER `featured_product`,
    ADD COLUMN IF NOT EXISTS `today_deals` TINYINT(1) NOT NULL DEFAULT 0 AFTER `hot_deals`,
    ADD COLUMN IF NOT EXISTS `home_section_override` VARCHAR(50) NULL AFTER `today_deals`,
    ADD COLUMN IF NOT EXISTS `home_section_rank` INT(11) NOT NULL DEFAULT 0 AFTER `home_section_override`,
    ADD COLUMN IF NOT EXISTS `home_exclude_from_auto` TINYINT(1) NOT NULL DEFAULT 0 AFTER `home_section_rank`,
    ADD COLUMN IF NOT EXISTS `seller_id` BIGINT(20) UNSIGNED NULL AFTER `brand_id`;

-- -----------------------------------------------------------------------------
-- 2) Performance indexes (skip manually if index already exists)
-- -----------------------------------------------------------------------------
CREATE INDEX IF NOT EXISTS `idx_products_slug_status` ON `products` (`slug`, `status`);
CREATE INDEX IF NOT EXISTS `idx_products_status_featured` ON `products` (`status`, `featured_product`);
CREATE INDEX IF NOT EXISTS `idx_products_category_status` ON `products` (`category_id`, `status`);
CREATE INDEX IF NOT EXISTS `idx_product_variants_product_status` ON `product_variants` (`product_id`, `status`);
CREATE INDEX IF NOT EXISTS `idx_carts_user_product` ON `carts` (`user_id`, `product_id`);
CREATE INDEX IF NOT EXISTS `idx_orders_user_created` ON `orders` (`user_id`, `created_at`);
CREATE INDEX IF NOT EXISTS `idx_orders_guest_email` ON `orders` (`guest_email`);
CREATE INDEX IF NOT EXISTS `idx_order_details_order_product` ON `order_details` (`order_id`, `product_id`);

-- Migration 2026_05_15: admin/reporting indexes
CREATE INDEX IF NOT EXISTS `frontends_data_keys_index` ON `frontends` (`data_keys`);
CREATE INDEX IF NOT EXISTS `orders_order_status_index` ON `orders` (`order_status`);
CREATE INDEX IF NOT EXISTS `orders_payment_status_index` ON `orders` (`payment_status`);

-- -----------------------------------------------------------------------------
-- 3) Verify (optional)
-- -----------------------------------------------------------------------------
-- SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE();
-- SELECT migration FROM migrations ORDER BY id DESC LIMIT 5;
