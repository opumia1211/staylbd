-- ======================================================
-- StayLBD: Products Table - Advanced Features Migration
-- Run this once in phpMyAdmin > SQL tab.
-- All ALTER TABLE statements are safe (skips if column already exists).
-- ======================================================

ALTER TABLE `products`

    -- === PRICING ===
    ADD COLUMN IF NOT EXISTS `original_price` DECIMAL(28,8) DEFAULT 0.00000000 COMMENT 'MSRP / Original price (shows as strike-through on frontend)' AFTER `price`,
    ADD COLUMN IF NOT EXISTS `profit_margin`  DECIMAL(28,8) DEFAULT 0.00000000 COMMENT 'Internal profit margin (not shown on frontend)',

    -- === INVENTORY ===
    ADD COLUMN IF NOT EXISTS `low_stock_alert`     INT(11)       DEFAULT 5    COMMENT 'Alert threshold for low stock notifications',
    ADD COLUMN IF NOT EXISTS `warehouse_location`  VARCHAR(255)  NULL         COMMENT 'e.g. Shelf A1-B2 (internal use only)',

    -- === SHIPPING ===
    ADD COLUMN IF NOT EXISTS `shipping_weight`  DECIMAL(10,2) DEFAULT 0.00 COMMENT 'Item weight in kg (shown in Shipping tab)',
    ADD COLUMN IF NOT EXISTS `shipping_class`   VARCHAR(100)  NULL         COMMENT 'e.g. standard, bulky, fragile',

    -- === PRODUCT TYPE & ATTRIBUTES ===
    ADD COLUMN IF NOT EXISTS `product_type`  VARCHAR(50)  DEFAULT 'physical' COMMENT 'physical, clothing, digital, service',
    ADD COLUMN IF NOT EXISTS `fabric_type`   VARCHAR(100) NULL COMMENT 'Fabric type for clothing products',
    ADD COLUMN IF NOT EXISTS `material`      VARCHAR(100) NULL COMMENT 'Primary material e.g. Cotton, Leather',
    ADD COLUMN IF NOT EXISTS `season`        VARCHAR(100) NULL COMMENT 'e.g. All, Summer, Winter',
    ADD COLUMN IF NOT EXISTS `color_variants` TEXT         NULL COMMENT 'JSON array of color options',
    ADD COLUMN IF NOT EXISTS `source_url`    VARCHAR(255) NULL COMMENT 'Original product source / resell URL (admin only)',

    -- === TARGETING ===
    ADD COLUMN IF NOT EXISTS `target_gender`  VARCHAR(50) NULL COMMENT 'male, female, kids, unisex',
    ADD COLUMN IF NOT EXISTS `target_age_min` INT(11)     NULL COMMENT 'Minimum target age',
    ADD COLUMN IF NOT EXISTS `target_age_max` INT(11)     NULL COMMENT 'Maximum target age';

-- Verify columns were added:
-- SELECT COLUMN_NAME, COLUMN_TYPE FROM information_schema.COLUMNS
-- WHERE TABLE_NAME = 'products' AND TABLE_SCHEMA = DATABASE()
-- ORDER BY ORDINAL_POSITION;

-- ======================================================
-- Performance indexes (safe: skips if index already exists)
-- Run after master schema import for high-traffic storefront/checkout.
-- ======================================================

-- Product listing & PDP slug lookups
CREATE INDEX IF NOT EXISTS `idx_products_slug_status` ON `products` (`slug`, `status`);
CREATE INDEX IF NOT EXISTS `idx_products_status_featured` ON `products` (`status`, `featured_product`);
CREATE INDEX IF NOT EXISTS `idx_products_category_status` ON `products` (`category_id`, `status`);

-- Variant stock on PDP / buy-now
CREATE INDEX IF NOT EXISTS `idx_product_variants_product_status` ON `product_variants` (`product_id`, `status`);

-- Cart & orders (logged-in users, checkout)
CREATE INDEX IF NOT EXISTS `idx_carts_user_product` ON `carts` (`user_id`, `product_id`);
CREATE INDEX IF NOT EXISTS `idx_orders_user_created` ON `orders` (`user_id`, `created_at`);
CREATE INDEX IF NOT EXISTS `idx_orders_guest_email` ON `orders` (`guest_email`);
CREATE INDEX IF NOT EXISTS `idx_order_details_order_product` ON `order_details` (`order_id`, `product_id`);
