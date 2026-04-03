-- Optional: run in phpMyAdmin if you cannot run artisan migrate.
-- If a column already exists, you will get "Duplicate column" - skip that line or run one by one.

ALTER TABLE `courierapis` ADD COLUMN `name` VARCHAR(255) NULL AFTER `type`;
ALTER TABLE `courierapis` ADD COLUMN `country_code` VARCHAR(10) NOT NULL DEFAULT 'BD' AFTER `name`;
ALTER TABLE `courierapis` ADD COLUMN `config` JSON NULL AFTER `token`;
ALTER TABLE `courierapis` ADD COLUMN `sort_order` SMALLINT UNSIGNED NOT NULL DEFAULT 0 AFTER `status`;

UPDATE `courierapis` SET `name` = 'Steadfast Courier', `country_code` = 'BD', `sort_order` = 1, `updated_at` = NOW() WHERE `type` = 'steadfast';
UPDATE `courierapis` SET `name` = 'Pathao', `country_code` = 'BD', `sort_order` = 2, `updated_at` = NOW() WHERE `type` = 'pathao';
