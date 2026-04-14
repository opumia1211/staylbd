-- Repair script for SQLSTATE[42S02] / 1932: ui_settings doesn't exist in engine
-- Source matched from core/database/wintersm_tt.sql

SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `ui_settings`;

CREATE TABLE `ui_settings` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `product_card_bg` varchar(30) NOT NULL DEFAULT '#ffffff',
  `product_button_color` varchar(30) NOT NULL DEFAULT '#1f2937',
  `product_buy_now_color` varchar(30) NOT NULL DEFAULT '#0e9f90',
  `product_buy_now_hover` varchar(30) NOT NULL DEFAULT '#0c8a7d',
  `product_price_color` varchar(30) DEFAULT NULL,
  `header_bg` varchar(30) DEFAULT NULL,
  `footer_bg` varchar(30) DEFAULT NULL,
  `rating_color` varchar(30) NOT NULL DEFAULT '#f59e0b',
  `discount_badge_color` varchar(30) NOT NULL DEFAULT '#dc2626',
  `stock_color` varchar(30) DEFAULT NULL,
  `shipping_badge_color` varchar(30) DEFAULT NULL,
  `theme_template` varchar(50) NOT NULL DEFAULT 'default',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `ui_settings`
(`id`,`product_card_bg`,`product_button_color`,`product_buy_now_color`,`product_buy_now_hover`,`product_price_color`,`header_bg`,`footer_bg`,`rating_color`,`discount_badge_color`,`stock_color`,`shipping_badge_color`,`theme_template`,`created_at`,`updated_at`)
VALUES
(1,'#ffffff','#1f2937','#0e9f90','#0c8a7d','#0e9f90',NULL,NULL,'#f59e0b','#dc2626','#16a34a','#2563eb','default',NOW(),NOW())
ON DUPLICATE KEY UPDATE
`product_card_bg`=VALUES(`product_card_bg`),
`product_button_color`=VALUES(`product_button_color`),
`product_buy_now_color`=VALUES(`product_buy_now_color`),
`product_buy_now_hover`=VALUES(`product_buy_now_hover`),
`product_price_color`=VALUES(`product_price_color`),
`header_bg`=VALUES(`header_bg`),
`footer_bg`=VALUES(`footer_bg`),
`rating_color`=VALUES(`rating_color`),
`discount_badge_color`=VALUES(`discount_badge_color`),
`stock_color`=VALUES(`stock_color`),
`shipping_badge_color`=VALUES(`shipping_badge_color`),
`theme_template`=VALUES(`theme_template`),
`updated_at`=VALUES(`updated_at`);

SET FOREIGN_KEY_CHECKS=1;
