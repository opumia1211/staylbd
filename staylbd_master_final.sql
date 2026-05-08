-- StayLBD Master DB - Import only this file in cPanel. No migrations/patches required.
CREATE DATABASE IF NOT EXISTS `staylbd_wintersm` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `staylbd_wintersm`;

-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: 127.0.0.1    Database: staylbd_wintersm
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `abandoned_carts`
--

DROP TABLE IF EXISTS `abandoned_carts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `abandoned_carts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `session_id` varchar(191) DEFAULT NULL,
  `cookie_id` varchar(191) DEFAULT NULL,
  `local_storage_id` varchar(191) DEFAULT NULL,
  `cart_snapshot` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Full cart items for recovery' CHECK (json_valid(`cart_snapshot`)),
  `cart_value` decimal(18,2) NOT NULL DEFAULT 0.00,
  `checkout_started_at` timestamp NULL DEFAULT NULL,
  `last_activity_at` timestamp NULL DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `device_type` varchar(50) DEFAULT NULL,
  `email` varchar(191) DEFAULT NULL,
  `mobile` varchar(50) DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'pending' COMMENT 'pending, abandoned, recovered',
  `reminder_sent_at` timestamp NULL DEFAULT NULL,
  `recovery_token` varchar(64) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `abandoned_carts_recovery_token_unique` (`recovery_token`),
  KEY `abandoned_carts_user_id_index` (`user_id`),
  KEY `abandoned_carts_session_id_index` (`session_id`),
  KEY `abandoned_carts_cookie_id_index` (`cookie_id`),
  KEY `abandoned_carts_local_storage_id_index` (`local_storage_id`),
  KEY `abandoned_carts_last_activity_at_index` (`last_activity_at`),
  KEY `abandoned_carts_email_index` (`email`),
  KEY `abandoned_carts_status_index` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `abandoned_carts`
--

LOCK TABLES `abandoned_carts` WRITE;
/*!40000 ALTER TABLE `abandoned_carts` DISABLE KEYS */;
INSERT INTO `abandoned_carts` VALUES (1,NULL,'KdPlybFVNQ95Elf1DguFPx0wM6fRXXVEQeysTCE0','xUYXaNip7YvriCGIljOyc10VznyuZlzPgZfWGoha',NULL,'[{\"product_id\":2,\"product_name\":\"WinTerSMM\",\"product_price\":\"100.00000000\",\"quantity\":1,\"variant_id\":null,\"variant_details\":null,\"image\":\"69b05568ee4e71773163880.png\"}]',100.00,NULL,'2026-03-11 14:31:30','::1','desktop',NULL,NULL,'pending',NULL,'Rb8UrVesdBJTmf0g9PNYMrZugo2NEZsQEqOO7oOqUWbHaIIb','2026-03-11 14:31:30','2026-03-11 14:31:30'),(2,NULL,'mQMhZEE5owugQrUwJcW2jAAOQUsfuRUlAJvQmMeS','qNo2WC894ps98QFPx3fQ7PSYlwT6bh8orKGpopyq',NULL,'[{\"product_id\":9,\"product_name\":\"T-shirt, \\u099f\\u09bf-\\u09b6\\u09be\\u09b0\\u09cd\\u099f\",\"product_price\":\"500.00000000\",\"quantity\":1,\"variant_id\":null,\"variant_details\":null,\"image\":\"69b026df72d591773151967.png\"},{\"product_id\":7,\"product_name\":\"T-shirt, \\u099f\\u09bf-\\u09b6\\u09be\\u09b0\\u09cd\\u099f\",\"product_price\":\"500.00000000\",\"quantity\":1,\"variant_id\":null,\"variant_details\":null,\"image\":\"69b0222139ffb1773150753.png\"}]',1000.00,NULL,'2026-03-13 05:16:18','::1','desktop',NULL,NULL,'pending',NULL,'efWPG8p7Ss2vSntSLs76hewCWSbw0MyMmqviEuHDzjynpPKE','2026-03-12 22:30:35','2026-03-13 05:16:18'),(3,NULL,'FjIqqr6DgSNMFLuatcz0l9kumM57if57uGDUJGjH','hSC1AVCNjozPsppWghbIC0RGu7hOSwZhXbLOoWLo',NULL,'[{\"product_id\":10,\"product_name\":\"T-shirt, \\u099f\\u09bf-\\u09b6\\u09be\\u09b0\\u09cd\\u099f\",\"product_price\":\"600.00000000\",\"quantity\":1,\"variant_id\":null,\"variant_details\":null,\"image\":\"69b02961eab081773152609.png\"}]',600.00,NULL,'2026-03-13 06:42:57','192.168.0.8','mobile',NULL,NULL,'pending',NULL,'KOxAy5DTdH3UnJEyCGHHe5DAxylfdE2Yctsbh7RCLbh595ZX','2026-03-13 05:25:30','2026-03-13 06:42:57'),(4,NULL,'m0yy7tbWPKGjI2qA8xamd6GAWtB0hpVN7YfOKewz','9fnU7Vqaoo3TWwVoo9yFZH1ayfzfAiTvuVQ7E36p',NULL,'[{\"product_id\":7,\"product_name\":\"T-shirt, \\u099f\\u09bf-\\u09b6\\u09be\\u09b0\\u09cd\\u099f\",\"product_price\":\"500.00000000\",\"quantity\":2,\"variant_id\":null,\"variant_details\":null,\"image\":\"69b0222139ffb1773150753.png\"},{\"product_id\":5,\"product_name\":\"Affordable Custom Cricket Jersey With Sublimation Printing\",\"product_price\":\"6000.00000000\",\"quantity\":1,\"variant_id\":null,\"variant_details\":null,\"image\":\"69b054fda55e21773163773.png\"},{\"product_id\":4,\"product_name\":\"\\u09b8\\u09cd\\u0995\\u09cd\\u09b0\\u09bf\\u09a8\\u09b6\\u099f \\u09a6\\u09bf\\u09af\\u09bc\\u09c7\\u099b\\u09bf  Affordable Custom Cricket Jersey With Sublimation Printing\",\"product_price\":\"4500.00000000\",\"quantity\":1,\"variant_id\":null,\"variant_details\":null,\"image\":\"69b055206c0dc1773163808.png\"},{\"product_id\":8,\"product_name\":\"T-shirt, \\u099f\\u09bf-\\u09b6\\u09be\\u09b0\\u09cd\\u099f\",\"product_price\":\"500.00000000\",\"quantity\":1,\"variant_id\":null,\"variant_details\":null,\"image\":\"69b02379aeb431773151097.png\"},{\"product_id\":9,\"product_name\":\"T-shirt, \\u099f\\u09bf-\\u09b6\\u09be\\u09b0\\u09cd\\u099f\",\"product_price\":\"500.00000000\",\"quantity\":1,\"variant_id\":null,\"variant_details\":null,\"image\":\"69b026df72d591773151967.png\"},{\"product_id\":10,\"product_name\":\"T-shirt, \\u099f\\u09bf-\\u09b6\\u09be\\u09b0\\u09cd\\u099f\",\"product_price\":\"600.00000000\",\"quantity\":1,\"variant_id\":null,\"variant_details\":null,\"image\":\"69b02961eab081773152609.png\"}]',13100.00,NULL,'2026-03-13 08:50:08','::1','desktop',NULL,NULL,'pending',NULL,'vYWtk0ZU26wcuLW8PsMcewpoviEHxgNGVlOFn0Lc2BLdyjaG','2026-03-13 05:31:02','2026-03-13 08:50:08'),(5,NULL,'jk9QyWa3c134IZsF2KgaXCMUpq3ghbV8ItFSgGzm','dvd8Jajo0wwZWbJLzzWlpGJqEzIptvlQRXPbOMB8',NULL,'[{\"product_id\":8,\"product_name\":\"T-shirt, \\u099f\\u09bf-\\u09b6\\u09be\\u09b0\\u09cd\\u099f\",\"product_price\":\"500.00000000\",\"quantity\":1,\"variant_id\":null,\"variant_details\":null,\"image\":\"69b02379aeb431773151097.png\"},{\"product_id\":5,\"product_name\":\"Affordable Custom Cricket Jersey With Sublimation Printing\",\"product_price\":\"6000.00000000\",\"quantity\":2,\"variant_id\":null,\"variant_details\":null,\"image\":\"69b054fda55e21773163773.png\"},{\"product_id\":4,\"product_name\":\"\\u09b8\\u09cd\\u0995\\u09cd\\u09b0\\u09bf\\u09a8\\u09b6\\u099f \\u09a6\\u09bf\\u09af\\u09bc\\u09c7\\u099b\\u09bf  Affordable Custom Cricket Jersey With Sublimation Printing\",\"product_price\":\"4500.00000000\",\"quantity\":1,\"variant_id\":null,\"variant_details\":null,\"image\":\"69b055206c0dc1773163808.png\"}]',17000.00,NULL,'2026-03-13 05:43:16','::1','desktop',NULL,NULL,'pending',NULL,'Am1RjL2sH5IqAJzmohBHTiG0dTyLcfHfAjD1aQ4MxQfP1NEb','2026-03-13 05:34:45','2026-03-13 05:43:16'),(6,NULL,'hktait89zrO2A2udioTjywpeKMCi1GUwQOE6xAkr','Ozqu6u5VVy3hfkHOkOyInk9V9ESLYO74MCUcds4u',NULL,'[{\"product_id\":5,\"product_name\":\"Affordable Custom Cricket Jersey With Sublimation Printing\",\"product_price\":\"6000.00000000\",\"quantity\":1,\"variant_id\":null,\"variant_details\":null,\"image\":\"69b054fda55e21773163773.png\"}]',6000.00,NULL,'2026-03-13 10:46:43','::1','mobile',NULL,NULL,'pending',NULL,'yQtwRCbfbA73kMw5RE26lIJU86NzXTLjmKCZDRd6NDhhWdQp','2026-03-13 08:59:20','2026-03-13 10:46:43'),(7,NULL,'RDmxyY7rCGTdYB30g67paEAxxe9V9ko0brdsuTb0','m4uHb7bRcTqqu7PZDhg0qQ4K6xs1wbqXaSg3Z6b3',NULL,'[{\"product_id\":5,\"product_name\":\"Affordable Custom Cricket Jersey With Sublimation Printing\",\"product_price\":\"6000.00000000\",\"quantity\":1,\"variant_id\":null,\"variant_details\":null,\"image\":\"69b054fda55e21773163773.png\"},{\"product_id\":4,\"product_name\":\"\\u09b8\\u09cd\\u0995\\u09cd\\u09b0\\u09bf\\u09a8\\u09b6\\u099f \\u09a6\\u09bf\\u09af\\u09bc\\u09c7\\u099b\\u09bf  Affordable Custom Cricket Jersey With Sublimation Printing\",\"product_price\":\"4500.00000000\",\"quantity\":1,\"variant_id\":null,\"variant_details\":null,\"image\":\"69b055206c0dc1773163808.png\"},{\"product_id\":3,\"product_name\":\"Cricket Jersey || Affordable Custom Cricket Jersey With Sublimation Printing\",\"product_price\":\"4099.00000000\",\"quantity\":1,\"variant_id\":null,\"variant_details\":null,\"image\":\"69b055417e0c51773163841.png\"}]',14599.00,NULL,'2026-03-13 13:06:24','::1','desktop',NULL,NULL,'pending',NULL,'olE4NWgC01YMH1XlraS9Y4XwDoBU9v2mWIPuTtYNkLglwE2I','2026-03-13 09:03:31','2026-03-13 13:06:24'),(8,NULL,'M2rW0sQ6c0mbE8Ieet11Rh3nwibgEaYKa65gZDjZ','hRvYORLR3ip7maDYQFWCz6Pt0griW3vt8bPBn63t',NULL,'[{\"product_id\":7,\"product_name\":\"T-shirt, \\u099f\\u09bf-\\u09b6\\u09be\\u09b0\\u09cd\\u099f\",\"product_price\":\"500.00000000\",\"quantity\":12,\"variant_id\":null,\"variant_details\":null,\"image\":\"69b0222139ffb1773150753.png\"},{\"product_id\":5,\"product_name\":\"Affordable Custom Cricket Jersey With Sublimation Printing\",\"product_price\":\"6000.00000000\",\"quantity\":12,\"variant_id\":null,\"variant_details\":null,\"image\":\"69b054fda55e21773163773.png\"},{\"product_id\":4,\"product_name\":\"\\u09b8\\u09cd\\u0995\\u09cd\\u09b0\\u09bf\\u09a8\\u09b6\\u099f \\u09a6\\u09bf\\u09af\\u09bc\\u09c7\\u099b\\u09bf  Affordable Custom Cricket Jersey With Sublimation Printing\",\"product_price\":\"4500.00000000\",\"quantity\":12,\"variant_id\":null,\"variant_details\":null,\"image\":\"69b055206c0dc1773163808.png\"},{\"product_id\":3,\"product_name\":\"Cricket Jersey || Affordable Custom Cricket Jersey With Sublimation Printing\",\"product_price\":\"4099.00000000\",\"quantity\":12,\"variant_id\":null,\"variant_details\":null,\"image\":\"69b055417e0c51773163841.png\"},{\"product_id\":8,\"product_name\":\"T-shirt, \\u099f\\u09bf-\\u09b6\\u09be\\u09b0\\u09cd\\u099f\",\"product_price\":\"500.00000000\",\"quantity\":12,\"variant_id\":null,\"variant_details\":null,\"image\":\"69b02379aeb431773151097.png\"},{\"product_id\":9,\"product_name\":\"T-shirt, \\u099f\\u09bf-\\u09b6\\u09be\\u09b0\\u09cd\\u099f\",\"product_price\":\"500.00000000\",\"quantity\":12,\"variant_id\":null,\"variant_details\":null,\"image\":\"69b026df72d591773151967.png\"}]',193188.00,NULL,'2026-03-14 12:20:38','::1','desktop',NULL,NULL,'pending',NULL,'v5reZrMkAlMJo38sDw3hxZgMCSCEl1zQjGyiFxzHVU1i0njA','2026-03-14 11:12:43','2026-03-14 12:20:38'),(9,NULL,'G5R4u6SGxNkTFZrk3Wz7qpBeep9dAVDXpXABWtMh','nyrHL3PXRf8I7J1G8f7FwMw3Hwp6KvzhnabTsSK5',NULL,'[{\"product_id\":7,\"product_name\":\"T-shirt, \\u099f\\u09bf-\\u09b6\\u09be\\u09b0\\u09cd\\u099f\",\"product_price\":\"500.00000000\",\"quantity\":999,\"variant_id\":null,\"variant_details\":null,\"image\":\"69b0222139ffb1773150753.png\"},{\"product_id\":5,\"product_name\":\"Affordable Custom Cricket Jersey With Sublimation Printing\",\"product_price\":\"6000.00000000\",\"quantity\":999,\"variant_id\":null,\"variant_details\":null,\"image\":\"69b054fda55e21773163773.png\"},{\"product_id\":4,\"product_name\":\"\\u09b8\\u09cd\\u0995\\u09cd\\u09b0\\u09bf\\u09a8\\u09b6\\u099f \\u09a6\\u09bf\\u09af\\u09bc\\u09c7\\u099b\\u09bf  Affordable Custom Cricket Jersey With Sublimation Printing\",\"product_price\":\"4500.00000000\",\"quantity\":999,\"variant_id\":null,\"variant_details\":null,\"image\":\"69b055206c0dc1773163808.png\"},{\"product_id\":3,\"product_name\":\"Cricket Jersey || Affordable Custom Cricket Jersey With Sublimation Printing\",\"product_price\":\"4099.00000000\",\"quantity\":999,\"variant_id\":null,\"variant_details\":null,\"image\":\"69b055417e0c51773163841.png\"},{\"product_id\":8,\"product_name\":\"T-shirt, \\u099f\\u09bf-\\u09b6\\u09be\\u09b0\\u09cd\\u099f\",\"product_price\":\"500.00000000\",\"quantity\":999,\"variant_id\":null,\"variant_details\":null,\"image\":\"69b02379aeb431773151097.png\"},{\"product_id\":9,\"product_name\":\"T-shirt, \\u099f\\u09bf-\\u09b6\\u09be\\u09b0\\u09cd\\u099f\",\"product_price\":\"500.00000000\",\"quantity\":999,\"variant_id\":null,\"variant_details\":null,\"image\":\"69b026df72d591773151967.png\"}]',16082901.00,NULL,'2026-03-15 09:09:06','::1','desktop',NULL,NULL,'pending',NULL,'YuedeZC2WHdIMyAS5vZX3mOKiBvaiNg13alqBHPJVY2PeEHL','2026-03-15 07:44:43','2026-03-15 09:09:06'),(10,NULL,'iCp4w8RsUpcis6HBVOFOsVMtSAxd1Ny7nf0cVU7L','12BT4TYvPEnQmjwP0GGAdMPI7IC66ZiLINCSngdQ',NULL,'[{\"product_id\":7,\"product_name\":\"T-shirt, \\u099f\\u09bf-\\u09b6\\u09be\\u09b0\\u09cd\\u099f\",\"product_price\":\"500.00000000\",\"quantity\":999,\"variant_id\":null,\"variant_details\":null,\"image\":\"69b0222139ffb1773150753.png\"},{\"product_id\":5,\"product_name\":\"Affordable Custom Cricket Jersey With Sublimation Printing\",\"product_price\":\"6000.00000000\",\"quantity\":999,\"variant_id\":null,\"variant_details\":null,\"image\":\"69b054fda55e21773163773.png\"},{\"product_id\":4,\"product_name\":\"\\u09b8\\u09cd\\u0995\\u09cd\\u09b0\\u09bf\\u09a8\\u09b6\\u099f \\u09a6\\u09bf\\u09af\\u09bc\\u09c7\\u099b\\u09bf  Affordable Custom Cricket Jersey With Sublimation Printing\",\"product_price\":\"4500.00000000\",\"quantity\":999,\"variant_id\":null,\"variant_details\":null,\"image\":\"69b055206c0dc1773163808.png\"},{\"product_id\":3,\"product_name\":\"Cricket Jersey || Affordable Custom Cricket Jersey With Sublimation Printing\",\"product_price\":\"4099.00000000\",\"quantity\":999,\"variant_id\":null,\"variant_details\":null,\"image\":\"69b055417e0c51773163841.png\"},{\"product_id\":8,\"product_name\":\"T-shirt, \\u099f\\u09bf-\\u09b6\\u09be\\u09b0\\u09cd\\u099f\",\"product_price\":\"500.00000000\",\"quantity\":999,\"variant_id\":null,\"variant_details\":null,\"image\":\"69b02379aeb431773151097.png\"},{\"product_id\":9,\"product_name\":\"T-shirt, \\u099f\\u09bf-\\u09b6\\u09be\\u09b0\\u09cd\\u099f\",\"product_price\":\"500.00000000\",\"quantity\":999,\"variant_id\":null,\"variant_details\":null,\"image\":\"69b026df72d591773151967.png\"}]',16082901.00,NULL,'2026-03-15 12:31:45','::1','desktop',NULL,NULL,'pending',NULL,'VVutvluNG0ex7fS3kcaTaQ9MyEdtTMmlKc0OfrjtexwukqVJ','2026-03-15 09:21:51','2026-03-15 12:31:45'),(11,NULL,'DYVISFKi0edCgfIuRbVn2AvU24W9myKxAjFzwbFh','wLLCgnVRj6WPvt7HgoUbshRC6HcqDwkhaOLF5z2M',NULL,'[{\"product_id\":7,\"product_name\":\"T-shirt, \\u099f\\u09bf-\\u09b6\\u09be\\u09b0\\u09cd\\u099f\",\"product_price\":\"500.00000000\",\"quantity\":999,\"variant_id\":null,\"variant_details\":null,\"image\":\"69b0222139ffb1773150753.png\"},{\"product_id\":5,\"product_name\":\"Affordable Custom Cricket Jersey With Sublimation Printing\",\"product_price\":\"6000.00000000\",\"quantity\":999,\"variant_id\":null,\"variant_details\":null,\"image\":\"69b054fda55e21773163773.png\"},{\"product_id\":4,\"product_name\":\"\\u09b8\\u09cd\\u0995\\u09cd\\u09b0\\u09bf\\u09a8\\u09b6\\u099f \\u09a6\\u09bf\\u09af\\u09bc\\u09c7\\u099b\\u09bf  Affordable Custom Cricket Jersey With Sublimation Printing\",\"product_price\":\"4500.00000000\",\"quantity\":999,\"variant_id\":null,\"variant_details\":null,\"image\":\"69b055206c0dc1773163808.png\"},{\"product_id\":3,\"product_name\":\"Cricket Jersey || Affordable Custom Cricket Jersey With Sublimation Printing\",\"product_price\":\"4099.00000000\",\"quantity\":999,\"variant_id\":null,\"variant_details\":null,\"image\":\"69b055417e0c51773163841.png\"},{\"product_id\":8,\"product_name\":\"T-shirt, \\u099f\\u09bf-\\u09b6\\u09be\\u09b0\\u09cd\\u099f\",\"product_price\":\"500.00000000\",\"quantity\":999,\"variant_id\":null,\"variant_details\":null,\"image\":\"69b02379aeb431773151097.png\"},{\"product_id\":9,\"product_name\":\"T-shirt, \\u099f\\u09bf-\\u09b6\\u09be\\u09b0\\u09cd\\u099f\",\"product_price\":\"500.00000000\",\"quantity\":999,\"variant_id\":null,\"variant_details\":null,\"image\":\"69b026df72d591773151967.png\"}]',16082901.00,NULL,'2026-03-16 12:11:17','::1','desktop',NULL,NULL,'pending',NULL,'1ynYD0uteMTa4k69rhUjSfLPI3v3JkSacH7kUerRKjrBQVm1','2026-03-16 07:38:55','2026-03-16 12:11:17'),(12,NULL,'XArDHqBxdsH29AHC8SskzrGTrzeEX2u4Gt5mTfrD','YrIwU3JIpBRXaqmzNoVjKyk0KYMdnRZFV4VGpU7m',NULL,'[{\"product_id\":7,\"product_name\":\"T-shirt, \\u099f\\u09bf-\\u09b6\\u09be\\u09b0\\u09cd\\u099f\",\"product_price\":\"500.00000000\",\"quantity\":999,\"variant_id\":null,\"variant_details\":null,\"image\":\"69b0222139ffb1773150753.png\"},{\"product_id\":5,\"product_name\":\"Affordable Custom Cricket Jersey With Sublimation Printing\",\"product_price\":\"6000.00000000\",\"quantity\":999,\"variant_id\":null,\"variant_details\":null,\"image\":\"69b054fda55e21773163773.png\"},{\"product_id\":4,\"product_name\":\"\\u09b8\\u09cd\\u0995\\u09cd\\u09b0\\u09bf\\u09a8\\u09b6\\u099f \\u09a6\\u09bf\\u09af\\u09bc\\u09c7\\u099b\\u09bf  Affordable Custom Cricket Jersey With Sublimation Printing\",\"product_price\":\"4500.00000000\",\"quantity\":999,\"variant_id\":null,\"variant_details\":null,\"image\":\"69b055206c0dc1773163808.png\"},{\"product_id\":3,\"product_name\":\"Cricket Jersey || Affordable Custom Cricket Jersey With Sublimation Printing\",\"product_price\":\"4099.00000000\",\"quantity\":999,\"variant_id\":null,\"variant_details\":null,\"image\":\"69b055417e0c51773163841.png\"},{\"product_id\":8,\"product_name\":\"T-shirt, \\u099f\\u09bf-\\u09b6\\u09be\\u09b0\\u09cd\\u099f\",\"product_price\":\"500.00000000\",\"quantity\":999,\"variant_id\":null,\"variant_details\":null,\"image\":\"69b02379aeb431773151097.png\"},{\"product_id\":9,\"product_name\":\"T-shirt, \\u099f\\u09bf-\\u09b6\\u09be\\u09b0\\u09cd\\u099f\",\"product_price\":\"500.00000000\",\"quantity\":999,\"variant_id\":null,\"variant_details\":null,\"image\":\"69b026df72d591773151967.png\"}]',16082901.00,NULL,'2026-03-17 08:52:10','::1','desktop',NULL,NULL,'pending',NULL,'H3zDhS6kRaeSty2ARNjp0pGFzZ8Nb7BWivlvii7vShEgFggN','2026-03-17 05:37:40','2026-03-17 08:52:10'),(13,NULL,'rgsMLgtvX2k7Vgpl34lkzrGoNxzwzVVevdLlANKp','jSltcQWV0CX4UlG51Bpym4qfO9qRfnr0L3iQkNQ0',NULL,'[{\"product_id\":7,\"product_name\":\"T-shirt, \\u099f\\u09bf-\\u09b6\\u09be\\u09b0\\u09cd\\u099f\",\"product_price\":\"500.00000000\",\"quantity\":999,\"variant_id\":null,\"variant_details\":null,\"image\":\"69b0222139ffb1773150753.png\"},{\"product_id\":5,\"product_name\":\"Affordable Custom Cricket Jersey With Sublimation Printing\",\"product_price\":\"6000.00000000\",\"quantity\":999,\"variant_id\":null,\"variant_details\":null,\"image\":\"69b054fda55e21773163773.png\"},{\"product_id\":4,\"product_name\":\"\\u09b8\\u09cd\\u0995\\u09cd\\u09b0\\u09bf\\u09a8\\u09b6\\u099f \\u09a6\\u09bf\\u09af\\u09bc\\u09c7\\u099b\\u09bf  Affordable Custom Cricket Jersey With Sublimation Printing\",\"product_price\":\"4500.00000000\",\"quantity\":999,\"variant_id\":null,\"variant_details\":null,\"image\":\"69b055206c0dc1773163808.png\"},{\"product_id\":3,\"product_name\":\"Cricket Jersey || Affordable Custom Cricket Jersey With Sublimation Printing\",\"product_price\":\"4099.00000000\",\"quantity\":999,\"variant_id\":null,\"variant_details\":null,\"image\":\"69b055417e0c51773163841.png\"},{\"product_id\":9,\"product_name\":\"T-shirt, \\u099f\\u09bf-\\u09b6\\u09be\\u09b0\\u09cd\\u099f\",\"product_price\":\"500.00000000\",\"quantity\":999,\"variant_id\":null,\"variant_details\":null,\"image\":\"69b026df72d591773151967.png\"}]',15583401.00,NULL,'2026-03-17 22:58:48','::1','desktop',NULL,NULL,'pending',NULL,'gFxt5QOCOcC33J5yuj48McJvruLRlHxTNHHr9a63VpDyZYmu','2026-03-17 22:24:21','2026-03-17 22:58:48'),(14,NULL,'q7KfO2uAwbY2bdX1en7aNF9PCv0PoCXk4MLq0XqW','FuwQSam0ZE62JQw08MA7J4jqs3eWtzzo4PaRaOVn',NULL,'[{\"product_id\":7,\"product_name\":\"T-shirt, \\u099f\\u09bf-\\u09b6\\u09be\\u09b0\\u09cd\\u099f\",\"product_price\":\"500.00000000\",\"quantity\":999,\"variant_id\":null,\"variant_details\":null,\"image\":\"69b0222139ffb1773150753.png\"},{\"product_id\":5,\"product_name\":\"Affordable Custom Cricket Jersey With Sublimation Printing\",\"product_price\":\"6000.00000000\",\"quantity\":999,\"variant_id\":null,\"variant_details\":null,\"image\":\"69b054fda55e21773163773.png\"},{\"product_id\":4,\"product_name\":\"\\u09b8\\u09cd\\u0995\\u09cd\\u09b0\\u09bf\\u09a8\\u09b6\\u099f \\u09a6\\u09bf\\u09af\\u09bc\\u09c7\\u099b\\u09bf  Affordable Custom Cricket Jersey With Sublimation Printing\",\"product_price\":\"4500.00000000\",\"quantity\":999,\"variant_id\":null,\"variant_details\":null,\"image\":\"69b055206c0dc1773163808.png\"},{\"product_id\":3,\"product_name\":\"Cricket Jersey || Affordable Custom Cricket Jersey With Sublimation Printing\",\"product_price\":\"4099.00000000\",\"quantity\":999,\"variant_id\":null,\"variant_details\":null,\"image\":\"69b055417e0c51773163841.png\"},{\"product_id\":9,\"product_name\":\"T-shirt, \\u099f\\u09bf-\\u09b6\\u09be\\u09b0\\u09cd\\u099f\",\"product_price\":\"500.00000000\",\"quantity\":999,\"variant_id\":null,\"variant_details\":null,\"image\":\"69b026df72d591773151967.png\"}]',15583401.00,NULL,'2026-03-18 03:39:17','::1','desktop',NULL,NULL,'pending',NULL,'lYhfupPbSBG7GBnpvYenTCnRzvsYCOgSJJsjfFpe0mlkwQFx','2026-03-18 03:09:32','2026-03-18 03:39:17'),(15,NULL,'15Hi6EOyRLKOh6rt9TgTPbaQrB2P0WiabGnsnZcB','DZiTYhM8FfBMUux33MpnuhTTf3Ol1rMqGEd4Jtd5',NULL,'[{\"product_id\":1,\"product_name\":\"RIAZUL ISLAM SHOJOL\",\"product_price\":\"100.00000000\",\"quantity\":999,\"variant_id\":null,\"variant_details\":null,\"image\":\"69a5cd7b28f141772473723.jpg\"},{\"product_id\":8,\"product_name\":\"T-shirt, \\u099f\\u09bf-\\u09b6\\u09be\\u09b0\\u09cd\\u099f\",\"product_price\":\"500.00000000\",\"quantity\":999,\"variant_id\":null,\"variant_details\":null,\"image\":\"69b02379aeb431773151097.png\"},{\"product_id\":7,\"product_name\":\"T-shirt, \\u099f\\u09bf-\\u09b6\\u09be\\u09b0\\u09cd\\u099f\",\"product_price\":\"500.00000000\",\"quantity\":48,\"variant_id\":null,\"variant_details\":null,\"image\":\"69b0222139ffb1773150753.png\"},{\"product_id\":6,\"product_name\":\"\\u09b8\\u09cd\\u0995\\u09cd\\u09b0\\u09bf\\u09a8\\u09b6\\u099f \\u09a6\\u09bf\\u09af\\u09bc\\u09c7\\u099b\\u09bf \\u09ad\\u09be\\u09b2\\u09cb\\u09ad\\u09be\\u09ac\\u09c7 \\u098f\\u09a8\\u09be\\u09b2\\u09be\\u0987\\u09b8\\u09bf\\u09b8 \\u0995\\u09b0\\u09c7 \\u09a6\\u09c7\\u0996\\u09c1\\u09a8 \\u09b8\\u09ac\\u0995\\u09bf\\u099b\\u09c1 \\u09a0\\u09bf\\u0995 \\u09b0\\u09af\\u09bc\\u09c7\\u099b\\u09c7 \\u0995\\u09bf\\u09a8\\u09be \\u098f\\u09ac\\u0982 \\u09aa\\u09cd\\u09b0\\u09ab\\u09c7\\u09b6\\u09a8\\u09be\\u09b2 \\u09ad\\u09be\\u09ac\\u09c7 \\u09b8\\u09ac\\u0995\\u09bf\\u099b\\u09c1 \\u09a6\\u09c7\\u0996\\u09be\\u099a\\u09cd\\u099b\\u09c7 \\u0995\\u09bf\\u09a8\\u09be\",\"product_price\":\"500.00000000\",\"quantity\":3,\"variant_id\":null,\"variant_details\":null,\"image\":\"69b021db50f4e1773150683.png\"}]',624900.00,NULL,'2026-03-27 11:55:17','::1','desktop',NULL,NULL,'pending',NULL,'sCK8NtJOAcmQuLy14SaEGA1gm0NgPnn1L6oqCzc3i6z7NAHq','2026-03-27 04:31:04','2026-03-27 11:55:17'),(16,NULL,'EyGMqyKCZC5SsVLfHhYowlq7GYsqwd0FZGMmaasQ','9s9KA4iRodSE5BRR3D5ICZva4MAqsw2MihNNPeEv',NULL,'[{\"product_id\":7,\"product_name\":\"T-shirt, \\u099f\\u09bf-\\u09b6\\u09be\\u09b0\\u09cd\\u099f\",\"product_price\":\"500.00000000\",\"quantity\":1,\"variant_id\":null,\"variant_details\":null,\"image\":\"69b0222139ffb1773150753.png\"},{\"product_id\":4,\"product_name\":\"\\u09b8\\u09cd\\u0995\\u09cd\\u09b0\\u09bf\\u09a8\\u09b6\\u099f \\u09a6\\u09bf\\u09af\\u09bc\\u09c7\\u099b\\u09bf  Affordable Custom Cricket Jersey With Sublimation Printing\",\"product_price\":\"4500.00000000\",\"quantity\":1,\"variant_id\":null,\"variant_details\":null,\"image\":\"69b055206c0dc1773163808.png\"},{\"product_id\":3,\"product_name\":\"Cricket Jersey || Affordable Custom Cricket Jersey With Sublimation Printing\",\"product_price\":\"4099.00000000\",\"quantity\":1,\"variant_id\":null,\"variant_details\":null,\"image\":\"69b055417e0c51773163841.png\"},{\"product_id\":8,\"product_name\":\"T-shirt, \\u099f\\u09bf-\\u09b6\\u09be\\u09b0\\u09cd\\u099f\",\"product_price\":\"500.00000000\",\"quantity\":1,\"variant_id\":null,\"variant_details\":null,\"image\":\"69b02379aeb431773151097.png\"}]',9599.00,NULL,'2026-04-01 14:57:25','::1','desktop',NULL,NULL,'pending',NULL,'068IJIpGEh3ncw6pQMTbQp5pIgxI55aKGKwFo0K4LdRGggqp','2026-04-01 14:56:55','2026-04-01 14:57:25'),(17,NULL,'C8wye4ZRr0oTaOlcWqWke94A2axndMxSDp2oL4FQ','6gODILLoyQnzmo0gP6vARHka0Mt3Yyk8i6zIq8rK',NULL,'[{\"product_id\":7,\"product_name\":\"T-shirt, \\u099f\\u09bf-\\u09b6\\u09be\\u09b0\\u09cd\\u099f\",\"product_price\":\"500.00000000\",\"quantity\":2,\"variant_id\":null,\"variant_details\":null,\"image\":\"69b0222139ffb1773150753.png\"},{\"product_id\":5,\"product_name\":\"Affordable Custom Cricket Jersey With Sublimation Printing\",\"product_price\":\"6000.00000000\",\"quantity\":6,\"variant_id\":null,\"variant_details\":null,\"image\":\"69b054fda55e21773163773.png\"},{\"product_id\":9,\"product_name\":\"T-shirt, \\u099f\\u09bf-\\u09b6\\u09be\\u09b0\\u09cd\\u099f\",\"product_price\":\"500.00000000\",\"quantity\":1,\"variant_id\":null,\"variant_details\":null,\"image\":\"69b026df72d591773151967.png\"},{\"product_id\":4,\"product_name\":\"\\u09b8\\u09cd\\u0995\\u09cd\\u09b0\\u09bf\\u09a8\\u09b6\\u099f \\u09a6\\u09bf\\u09af\\u09bc\\u09c7\\u099b\\u09bf  Affordable Custom Cricket Jersey With Sublimation Printing\",\"product_price\":\"4500.00000000\",\"quantity\":3,\"variant_id\":null,\"variant_details\":null,\"image\":\"69b055206c0dc1773163808.png\"}]',51000.00,NULL,'2026-04-02 10:42:47','::1','desktop',NULL,NULL,'pending',NULL,'1vV9kDLxCOVBUCKxzgsvlTrHotgO8u2CDFgv960VjNBga4BO','2026-04-02 08:49:42','2026-04-02 10:42:47'),(18,NULL,'UCWNtHbwgXud0Nl4pLLUSMkRZyxhfqPFpPB4fT9W','Gnr0JDstwFMGig9GdotRvZqQpcBCdap64JEBS9cH',NULL,'[{\"product_id\":2,\"product_name\":\"WinTerSMM\",\"product_price\":\"100.00000000\",\"quantity\":1,\"variant_id\":null,\"variant_details\":null,\"image\":\"69b05568ee4e71773163880.png\"}]',100.00,NULL,'2026-04-03 00:51:54','::1','desktop',NULL,NULL,'pending',NULL,'JVuDFFie5nYnRsj9zjZgLKzLahu2GheuLMjlU9ONSE642wKE','2026-04-03 00:51:51','2026-04-03 00:51:54');
/*!40000 ALTER TABLE `abandoned_carts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `admin_activity_logs`
--

DROP TABLE IF EXISTS `admin_activity_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admin_activity_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `admin_id` bigint(20) unsigned NOT NULL,
  `action` varchar(255) NOT NULL,
  `model` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `model_type` varchar(255) DEFAULT NULL,
  `model_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(500) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `admin_activity_logs_admin_id_index` (`admin_id`),
  KEY `admin_activity_logs_action_index` (`action`),
  KEY `admin_activity_logs_created_at_index` (`created_at`),
  KEY `admin_activity_logs_model_index` (`model`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin_activity_logs`
--

LOCK TABLES `admin_activity_logs` WRITE;
/*!40000 ALTER TABLE `admin_activity_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `admin_activity_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `admin_ip_whitelist`
--

DROP TABLE IF EXISTS `admin_ip_whitelist`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admin_ip_whitelist` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(45) NOT NULL,
  `label` varchar(100) DEFAULT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT 1,
  `admin_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `admin_ip_whitelist_ip_address_unique` (`ip_address`),
  KEY `admin_ip_whitelist_ip_address_index` (`ip_address`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin_ip_whitelist`
--

LOCK TABLES `admin_ip_whitelist` WRITE;
/*!40000 ALTER TABLE `admin_ip_whitelist` DISABLE KEYS */;
/*!40000 ALTER TABLE `admin_ip_whitelist` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `admin_lockouts`
--

DROP TABLE IF EXISTS `admin_lockouts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admin_lockouts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(45) NOT NULL,
  `identifier` varchar(191) DEFAULT NULL,
  `failed_attempts` int(10) unsigned NOT NULL DEFAULT 0,
  `lock_count` int(10) unsigned NOT NULL DEFAULT 0,
  `locked_at` timestamp NULL DEFAULT NULL,
  `unlocked_at` timestamp NULL DEFAULT NULL,
  `unlocked_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `admin_lockouts_ip_address_index` (`ip_address`),
  KEY `admin_lockouts_identifier_index` (`identifier`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin_lockouts`
--

LOCK TABLES `admin_lockouts` WRITE;
/*!40000 ALTER TABLE `admin_lockouts` DISABLE KEYS */;
INSERT INTO `admin_lockouts` VALUES (1,'::1','digitalzero.com@gmail.com',0,0,NULL,NULL,NULL,'2026-03-04 11:06:55','2026-04-01 06:34:55');
/*!40000 ALTER TABLE `admin_lockouts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `admin_notifications`
--

DROP TABLE IF EXISTS `admin_notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admin_notifications` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `title` varchar(255) DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `click_url` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin_notifications`
--

LOCK TABLES `admin_notifications` WRITE;
/*!40000 ALTER TABLE `admin_notifications` DISABLE KEYS */;
INSERT INTO `admin_notifications` VALUES (1,1,'New member registered',0,'/sajaladminopu/users/detail/1','2025-10-10 11:24:15','2025-10-10 11:24:15'),(2,2,'New member registered',0,'/sajaladminopu/users/detail/2','2025-10-10 12:18:08','2025-10-10 12:18:08'),(3,2,'Deposit request from hhuhuhu',0,'/sajaladminopu/deposit/details/1','2025-10-10 12:27:55','2025-10-10 12:27:55'),(4,2,'Order successfully placed.',1,'/sajaladminopu/order/details/1','2025-10-10 12:27:55','2025-10-10 12:35:38'),(5,3,'New member registered',0,'/sajaladminopu/users/detail/3','2025-10-11 13:19:39','2025-10-11 13:19:39'),(6,4,'New member registered',0,'/sajaladminopu/users/detail/4','2025-10-12 19:33:24','2025-10-12 19:33:24'),(7,5,'New member registered',0,'/sajaladminopu/users/detail/5','2025-10-15 11:42:04','2025-10-15 11:42:04'),(8,6,'New member registered',1,'/sajaladminopu/users/detail/6','2025-10-15 11:48:36','2025-10-16 02:09:40'),(9,0,'A new contact message has been submitted',0,'/sajaladminopu/messages/view/1','2025-10-22 06:39:58','2025-10-22 06:39:58'),(10,7,'New member registered',0,'/sajaladminopu/users/detail/7','2025-11-03 03:52:29','2025-11-03 03:52:29'),(11,8,'New member registered',0,'/sajaladminopu/users/detail/8','2026-02-28 12:51:00','2026-02-28 12:51:00'),(12,8,'New message from opumiaxb: hi',0,'/sajaladminopu/ticket/view-user/8','2026-02-28 12:52:53','2026-02-28 12:52:53'),(13,9,'New member registered',0,'/sajaladminopu/users/detail/9','2026-03-01 17:39:12','2026-03-01 17:39:12'),(14,10,'New member registered',0,'/sajaladminopu/users/detail/10','2026-03-06 07:24:43','2026-03-06 07:24:43'),(15,11,'New member registered',0,'/sajaladminopu/users/detail/11','2026-03-06 08:15:11','2026-03-06 08:15:11'),(16,12,'New member registered',0,'/sajaladminopu/users/detail/12','2026-03-06 08:27:21','2026-03-06 08:27:21');
/*!40000 ALTER TABLE `admin_notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `admin_password_resets`
--

DROP TABLE IF EXISTS `admin_password_resets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admin_password_resets` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(40) DEFAULT NULL,
  `token` varchar(40) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin_password_resets`
--

LOCK TABLES `admin_password_resets` WRITE;
/*!40000 ALTER TABLE `admin_password_resets` DISABLE KEYS */;
/*!40000 ALTER TABLE `admin_password_resets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `admin_permissions`
--

DROP TABLE IF EXISTS `admin_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admin_permissions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `admin_id` bigint(20) unsigned NOT NULL,
  `permission_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `admin_permissions_admin_id_permission_id_unique` (`admin_id`,`permission_id`),
  KEY `admin_permissions_permission_id_foreign` (`permission_id`),
  CONSTRAINT `admin_permissions_admin_id_foreign` FOREIGN KEY (`admin_id`) REFERENCES `admins` (`id`) ON DELETE CASCADE,
  CONSTRAINT `admin_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin_permissions`
--

LOCK TABLES `admin_permissions` WRITE;
/*!40000 ALTER TABLE `admin_permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `admin_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `admin_reports`
--

DROP TABLE IF EXISTS `admin_reports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admin_reports` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `type` enum('bug','feature') NOT NULL DEFAULT 'bug',
  `message` text NOT NULL,
  `status` enum('pending','read','resolved') NOT NULL DEFAULT 'pending',
  `admin_id` bigint(20) unsigned DEFAULT NULL,
  `admin_name` varchar(100) DEFAULT NULL,
  `page_url` varchar(500) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `admin_reports_type_index` (`type`),
  KEY `admin_reports_status_index` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin_reports`
--

LOCK TABLES `admin_reports` WRITE;
/*!40000 ALTER TABLE `admin_reports` DISABLE KEYS */;
/*!40000 ALTER TABLE `admin_reports` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `admin_sessions`
--

DROP TABLE IF EXISTS `admin_sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admin_sessions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `admin_id` bigint(20) unsigned NOT NULL,
  `session_id` varchar(255) NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(500) DEFAULT NULL,
  `last_activity_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `admin_sessions_admin_id_index` (`admin_id`),
  KEY `admin_sessions_session_id_index` (`session_id`),
  CONSTRAINT `admin_sessions_admin_id_foreign` FOREIGN KEY (`admin_id`) REFERENCES `admins` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=76 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin_sessions`
--

LOCK TABLES `admin_sessions` WRITE;
/*!40000 ALTER TABLE `admin_sessions` DISABLE KEYS */;
INSERT INTO `admin_sessions` VALUES (71,1,'smqwthdyQmuEBbMGIEfMs0WMt8oaZbgUzdsx7pF0','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-04-01 08:13:17','2026-04-01 06:35:27','2026-04-01 08:13:17'),(72,1,'EyGMqyKCZC5SsVLfHhYowlq7GYsqwd0FZGMmaasQ','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-04-01 15:23:47','2026-04-01 10:14:47','2026-04-01 15:23:47'),(73,1,'C8wye4ZRr0oTaOlcWqWke94A2axndMxSDp2oL4FQ','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-04-02 11:18:23','2026-04-02 07:32:13','2026-04-02 11:18:23'),(74,1,'DKpLnzfEGrBQBm5tSLlu5keIT2WOWXrwQE4zJoJ0','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-04-03 02:52:36','2026-04-02 20:15:49','2026-04-03 02:52:36'),(75,1,'y2W7f0olyCg79ktMZjJ6kZxvpAfw0FVCav0Y9Tsi','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','2026-05-04 12:00:03','2026-05-04 11:59:03','2026-05-04 12:00:03');
/*!40000 ALTER TABLE `admin_sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `admins`
--

DROP TABLE IF EXISTS `admins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admins` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(40) DEFAULT NULL,
  `email` varchar(40) DEFAULT NULL,
  `mobile` varchar(20) DEFAULT NULL,
  `admin_notes` text DEFAULT NULL,
  `username` varchar(40) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(32) NOT NULL DEFAULT 'admin',
  `allowed_sections` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `remember_token` varchar(255) DEFAULT NULL,
  `two_factor_secret` text DEFAULT NULL,
  `two_factor_recovery_codes` text DEFAULT NULL,
  `two_factor_confirmed_at` timestamp NULL DEFAULT NULL,
  `force_password_change` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`,`username`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admins`
--

LOCK TABLES `admins` WRITE;
/*!40000 ALTER TABLE `admins` DISABLE KEYS */;
INSERT INTO `admins` VALUES (1,'Owner Opu','digitalzero.com@gmail.com','+8801996522333',NULL,'Owner',NULL,'68eb0a278e3421760234023.png','$2y$10$lXymqU3OT5HMCxo1n1nlJeOt5l3CXF0mr.duHexukQpHVRjtAeFpW','owner',NULL,'RMBF4PSEMj43jJTDLkRrr1CkcAGrqRxewGSwd7sFvaoMLh3dInbMo920FaS8','eyJpdiI6IkQ0V1Jkd0hIbnAxTTZUTVBJbmtpMVE9PSIsInZhbHVlIjoiYWRobHlOYmZ0OWMyMlpBZHM2Wlp3YVhGczBiZXNlTTdiR29IQXhYYzRqaz0iLCJtYWMiOiJmNjUwNzUwYzk0MDNmOTRlODk4ZWNmNjQ4YzQ5YWM5YzQzYmVlY2I5ZDk3OGRmNGM2YmVjN2ZkOGFlMDdjYTQ2IiwidGFnIjoiIn0=','[\"E23837F7\",\"KHE7JBUN\",\"83RLC5QH\",\"38FLNF9S\",\"4L7XCSYN\",\"33GH4TCQ\",\"3K9SEBKZ\",\"PQRJXZ6N\"]','2026-04-01 06:35:26',0,NULL,'2026-04-01 06:35:26');
/*!40000 ALTER TABLE `admins` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `audit_logs`
--

DROP TABLE IF EXISTS `audit_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `audit_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `previous_log_id` bigint(20) unsigned DEFAULT NULL,
  `previous_hash` varchar(64) DEFAULT NULL,
  `current_hash` varchar(64) NOT NULL,
  `event_type` varchar(64) NOT NULL,
  `actor_type` varchar(64) DEFAULT NULL,
  `actor_id` bigint(20) unsigned DEFAULT NULL,
  `target_type` varchar(64) DEFAULT NULL,
  `target_id` bigint(20) unsigned DEFAULT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`payload`)),
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `audit_logs_current_hash_index` (`current_hash`),
  KEY `audit_logs_event_type_index` (`event_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `audit_logs`
--

LOCK TABLES `audit_logs` WRITE;
/*!40000 ALTER TABLE `audit_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `audit_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `auto_responses`
--

DROP TABLE IF EXISTS `auto_responses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `auto_responses` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `trigger_type` enum('keyword','welcome','offline') NOT NULL DEFAULT 'keyword',
  `keyword` varchar(255) DEFAULT NULL,
  `keywords` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`keywords`)),
  `message` text NOT NULL,
  `channel` varchar(50) DEFAULT 'all',
  `config` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`config`)),
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_public` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `auto_responses_trigger_type_index` (`trigger_type`),
  KEY `auto_responses_is_active_index` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `auto_responses`
--

LOCK TABLES `auto_responses` WRITE;
/*!40000 ALTER TABLE `auto_responses` DISABLE KEYS */;
/*!40000 ALTER TABLE `auto_responses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `autopay_messages`
--

DROP TABLE IF EXISTS `autopay_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `autopay_messages` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `method_code` int(10) unsigned NOT NULL,
  `deposit_id` bigint(20) unsigned DEFAULT NULL,
  `sender` varchar(100) DEFAULT NULL,
  `raw_message` text DEFAULT NULL,
  `amount` decimal(18,8) DEFAULT NULL,
  `trx_id` varchar(100) DEFAULT NULL,
  `matched` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `autopay_messages_method_code_index` (`method_code`),
  KEY `autopay_messages_deposit_id_index` (`deposit_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `autopay_messages`
--

LOCK TABLES `autopay_messages` WRITE;
/*!40000 ALTER TABLE `autopay_messages` DISABLE KEYS */;
/*!40000 ALTER TABLE `autopay_messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `banner_analytics`
--

DROP TABLE IF EXISTS `banner_analytics`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `banner_analytics` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `frontend_id` bigint(20) unsigned NOT NULL,
  `event` enum('impression','click') NOT NULL DEFAULT 'impression',
  `device` varchar(20) DEFAULT 'desktop',
  `campaign_source` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `banner_analytics_frontend_id_index` (`frontend_id`),
  KEY `banner_analytics_event_index` (`event`),
  KEY `banner_analytics_created_at_index` (`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=1459 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `banner_analytics`
--

LOCK TABLES `banner_analytics` WRITE;
/*!40000 ALTER TABLE `banner_analytics` DISABLE KEYS */;
INSERT INTO `banner_analytics` VALUES (1,106,'impression','desktop',NULL,'2026-02-11 07:58:27'),(2,102,'impression','desktop',NULL,'2026-02-11 07:58:32'),(3,106,'impression','desktop',NULL,'2026-02-11 07:58:37'),(4,102,'impression','desktop',NULL,'2026-02-11 07:58:42'),(5,106,'impression','desktop',NULL,'2026-02-11 07:58:47'),(6,102,'impression','desktop',NULL,'2026-02-11 07:58:52'),(7,106,'impression','desktop',NULL,'2026-02-11 07:58:57'),(8,102,'impression','desktop',NULL,'2026-02-11 07:59:02'),(9,106,'impression','desktop',NULL,'2026-02-11 07:59:07'),(10,102,'impression','desktop',NULL,'2026-02-11 07:59:12'),(11,106,'impression','desktop',NULL,'2026-02-11 07:59:17'),(12,102,'impression','desktop',NULL,'2026-02-11 07:59:22'),(13,106,'impression','desktop',NULL,'2026-02-11 07:59:27'),(14,102,'impression','desktop',NULL,'2026-02-11 07:59:32'),(15,106,'impression','desktop',NULL,'2026-02-11 07:59:37'),(16,102,'impression','desktop',NULL,'2026-02-11 07:59:42'),(17,106,'impression','desktop',NULL,'2026-02-11 07:59:47'),(18,102,'impression','desktop',NULL,'2026-02-11 07:59:52'),(19,106,'impression','desktop',NULL,'2026-02-11 07:59:57'),(20,102,'impression','desktop',NULL,'2026-02-11 08:00:02'),(21,106,'impression','desktop',NULL,'2026-02-11 08:00:07'),(22,102,'impression','desktop',NULL,'2026-02-11 08:00:12'),(23,102,'impression','desktop',NULL,'2026-02-11 08:00:18'),(24,106,'impression','desktop',NULL,'2026-02-11 08:00:23'),(25,102,'impression','desktop',NULL,'2026-02-27 05:50:13'),(26,102,'impression','desktop',NULL,'2026-02-27 05:50:20'),(27,106,'impression','desktop',NULL,'2026-02-27 05:50:25'),(28,102,'impression','desktop',NULL,'2026-02-28 18:52:08'),(29,106,'impression','desktop',NULL,'2026-02-28 18:52:13'),(30,102,'impression','desktop',NULL,'2026-02-28 18:52:18'),(31,106,'impression','desktop',NULL,'2026-02-28 18:52:23'),(32,102,'impression','desktop',NULL,'2026-02-28 19:26:00'),(33,106,'impression','desktop',NULL,'2026-02-28 19:26:05'),(34,120,'impression','desktop',NULL,'2026-02-28 19:26:10'),(35,102,'impression','desktop',NULL,'2026-02-28 19:26:15'),(36,106,'impression','desktop',NULL,'2026-02-28 19:26:20'),(37,102,'impression','desktop',NULL,'2026-03-01 13:03:36'),(38,106,'impression','desktop',NULL,'2026-03-01 13:03:41'),(39,120,'impression','desktop',NULL,'2026-03-01 13:03:46'),(40,102,'impression','desktop',NULL,'2026-03-01 13:03:51'),(41,106,'impression','desktop',NULL,'2026-03-01 13:03:56'),(42,120,'impression','desktop',NULL,'2026-03-01 13:04:01'),(43,102,'impression','desktop',NULL,'2026-03-01 13:04:06'),(44,106,'impression','desktop',NULL,'2026-03-01 13:04:11'),(45,120,'impression','desktop',NULL,'2026-03-01 13:04:16'),(46,102,'impression','desktop',NULL,'2026-03-01 13:04:21'),(47,106,'impression','desktop',NULL,'2026-03-01 13:04:26'),(48,120,'impression','desktop',NULL,'2026-03-01 13:04:31'),(49,102,'impression','desktop',NULL,'2026-03-01 13:04:36'),(50,106,'impression','desktop',NULL,'2026-03-01 13:04:41'),(51,120,'impression','desktop',NULL,'2026-03-01 13:04:46'),(52,102,'impression','desktop',NULL,'2026-03-01 13:04:51'),(53,106,'impression','desktop',NULL,'2026-03-01 13:04:56'),(54,120,'impression','desktop',NULL,'2026-03-01 13:05:01'),(55,102,'impression','desktop',NULL,'2026-03-01 13:05:06'),(56,106,'impression','desktop',NULL,'2026-03-01 13:05:11'),(57,120,'impression','desktop',NULL,'2026-03-01 13:05:16'),(58,102,'impression','desktop',NULL,'2026-03-01 13:05:21'),(59,106,'impression','desktop',NULL,'2026-03-01 13:05:26'),(60,120,'impression','desktop',NULL,'2026-03-01 13:05:31'),(61,102,'impression','desktop',NULL,'2026-03-01 13:05:36'),(62,106,'impression','desktop',NULL,'2026-03-01 13:05:41'),(63,120,'impression','desktop',NULL,'2026-03-01 13:05:46'),(64,102,'impression','desktop',NULL,'2026-03-01 13:05:51'),(65,106,'impression','desktop',NULL,'2026-03-01 13:05:56'),(66,120,'impression','desktop',NULL,'2026-03-01 13:06:01'),(67,102,'impression','desktop',NULL,'2026-03-01 13:06:06'),(68,102,'impression','desktop',NULL,'2026-03-01 13:10:11'),(69,102,'impression','desktop',NULL,'2026-03-01 14:53:50'),(70,102,'impression','desktop',NULL,'2026-03-01 15:29:07'),(71,102,'impression','desktop',NULL,'2026-03-01 15:29:13'),(72,106,'impression','desktop',NULL,'2026-03-01 15:29:18'),(73,120,'impression','desktop',NULL,'2026-03-01 15:29:23'),(74,102,'impression','desktop',NULL,'2026-03-01 15:29:28'),(75,106,'impression','desktop',NULL,'2026-03-01 15:29:33'),(76,120,'impression','desktop',NULL,'2026-03-01 15:29:38'),(77,102,'impression','desktop',NULL,'2026-03-01 15:29:43'),(78,106,'impression','desktop',NULL,'2026-03-01 15:29:48'),(79,120,'impression','desktop',NULL,'2026-03-01 15:29:53'),(80,102,'impression','desktop',NULL,'2026-03-01 15:29:58'),(81,106,'impression','desktop',NULL,'2026-03-01 15:30:03'),(82,120,'impression','desktop',NULL,'2026-03-01 15:30:08'),(83,102,'impression','desktop',NULL,'2026-03-01 15:30:13'),(84,106,'impression','desktop',NULL,'2026-03-01 15:30:18'),(85,120,'impression','desktop',NULL,'2026-03-01 15:30:23'),(86,102,'impression','desktop',NULL,'2026-03-01 15:30:28'),(87,102,'impression','desktop',NULL,'2026-03-01 15:32:41'),(88,106,'impression','desktop',NULL,'2026-03-01 15:32:46'),(89,120,'impression','desktop',NULL,'2026-03-01 15:32:51'),(90,102,'impression','desktop',NULL,'2026-03-01 15:32:56'),(91,106,'impression','desktop',NULL,'2026-03-01 15:33:01'),(92,120,'impression','desktop',NULL,'2026-03-01 15:33:06'),(93,102,'impression','desktop',NULL,'2026-03-01 15:33:11'),(94,102,'impression','desktop',NULL,'2026-03-01 15:33:27'),(95,106,'impression','desktop',NULL,'2026-03-01 15:33:32'),(96,120,'impression','desktop',NULL,'2026-03-01 15:33:37'),(97,102,'impression','desktop',NULL,'2026-03-01 15:33:42'),(98,106,'impression','desktop',NULL,'2026-03-01 15:33:47'),(99,120,'impression','desktop',NULL,'2026-03-01 15:33:52'),(100,102,'impression','desktop',NULL,'2026-03-01 15:33:57'),(101,106,'impression','desktop',NULL,'2026-03-01 15:34:02'),(102,120,'impression','desktop',NULL,'2026-03-01 15:34:07'),(103,102,'impression','desktop',NULL,'2026-03-01 15:34:12'),(104,106,'impression','desktop',NULL,'2026-03-01 15:34:17'),(105,120,'impression','desktop',NULL,'2026-03-01 15:34:22'),(106,102,'impression','desktop',NULL,'2026-03-01 15:34:27'),(107,106,'impression','desktop',NULL,'2026-03-01 15:34:32'),(108,120,'impression','desktop',NULL,'2026-03-01 15:34:37'),(109,102,'impression','desktop',NULL,'2026-03-01 15:34:42'),(110,106,'impression','desktop',NULL,'2026-03-01 15:34:47'),(111,120,'impression','desktop',NULL,'2026-03-01 15:34:52'),(112,102,'impression','desktop',NULL,'2026-03-01 15:34:57'),(113,106,'impression','desktop',NULL,'2026-03-01 15:35:02'),(114,120,'impression','desktop',NULL,'2026-03-01 15:35:07'),(115,102,'impression','desktop',NULL,'2026-03-01 15:35:12'),(116,106,'impression','desktop',NULL,'2026-03-01 15:35:17'),(117,120,'impression','desktop',NULL,'2026-03-01 15:35:22'),(118,102,'impression','desktop',NULL,'2026-03-01 15:35:27'),(119,106,'impression','desktop',NULL,'2026-03-01 15:35:32'),(120,120,'impression','desktop',NULL,'2026-03-01 15:35:37'),(121,102,'impression','desktop',NULL,'2026-03-01 15:35:42'),(122,106,'impression','desktop',NULL,'2026-03-01 15:35:47'),(123,120,'impression','desktop',NULL,'2026-03-01 15:35:52'),(124,102,'impression','desktop',NULL,'2026-03-01 15:35:57'),(125,106,'impression','desktop',NULL,'2026-03-01 15:36:02'),(126,120,'impression','desktop',NULL,'2026-03-01 15:36:07'),(127,102,'impression','desktop',NULL,'2026-03-01 15:36:12'),(128,106,'impression','desktop',NULL,'2026-03-01 15:36:17'),(129,120,'impression','desktop',NULL,'2026-03-01 15:36:22'),(130,102,'impression','desktop',NULL,'2026-03-01 15:36:27'),(131,102,'impression','desktop',NULL,'2026-03-01 15:36:39'),(132,106,'impression','desktop',NULL,'2026-03-01 15:36:44'),(133,120,'impression','desktop',NULL,'2026-03-01 15:36:49'),(134,102,'impression','desktop',NULL,'2026-03-01 15:36:54'),(135,106,'impression','desktop',NULL,'2026-03-01 15:36:59'),(136,120,'impression','desktop',NULL,'2026-03-01 15:37:04'),(137,102,'impression','desktop',NULL,'2026-03-01 15:37:09'),(138,106,'impression','desktop',NULL,'2026-03-01 15:37:14'),(139,120,'impression','desktop',NULL,'2026-03-01 15:37:19'),(140,102,'impression','desktop',NULL,'2026-03-01 15:37:24'),(141,106,'impression','desktop',NULL,'2026-03-01 15:37:29'),(142,102,'impression','desktop',NULL,'2026-03-01 15:44:10'),(143,106,'impression','desktop',NULL,'2026-03-01 15:44:15'),(144,120,'impression','desktop',NULL,'2026-03-01 15:44:20'),(145,102,'impression','desktop',NULL,'2026-03-01 15:44:25'),(146,106,'impression','desktop',NULL,'2026-03-01 15:44:30'),(147,120,'impression','desktop',NULL,'2026-03-01 15:44:35'),(148,102,'impression','desktop',NULL,'2026-03-01 15:44:40'),(149,106,'impression','desktop',NULL,'2026-03-01 15:44:45'),(150,120,'impression','desktop',NULL,'2026-03-01 15:44:50'),(151,102,'impression','desktop',NULL,'2026-03-01 15:44:55'),(152,106,'impression','desktop',NULL,'2026-03-01 15:45:00'),(153,120,'impression','desktop',NULL,'2026-03-01 15:45:05'),(154,102,'impression','desktop',NULL,'2026-03-01 15:45:10'),(155,106,'impression','desktop',NULL,'2026-03-01 15:45:15'),(156,120,'impression','desktop',NULL,'2026-03-01 15:45:20'),(157,102,'impression','desktop',NULL,'2026-03-01 15:45:25'),(158,106,'impression','desktop',NULL,'2026-03-01 15:45:30'),(159,120,'impression','desktop',NULL,'2026-03-01 15:45:35'),(160,102,'impression','desktop',NULL,'2026-03-01 15:45:40'),(161,102,'impression','desktop',NULL,'2026-03-01 15:59:05'),(162,106,'impression','desktop',NULL,'2026-03-01 15:59:10'),(163,120,'impression','desktop',NULL,'2026-03-01 15:59:15'),(164,102,'impression','desktop',NULL,'2026-03-01 15:59:20'),(165,102,'impression','desktop',NULL,'2026-03-01 15:59:28'),(166,106,'impression','desktop',NULL,'2026-03-01 15:59:33'),(167,120,'impression','desktop',NULL,'2026-03-01 15:59:38'),(168,102,'impression','desktop',NULL,'2026-03-01 15:59:43'),(169,106,'impression','desktop',NULL,'2026-03-01 15:59:48'),(170,120,'impression','desktop',NULL,'2026-03-01 15:59:53'),(171,102,'impression','desktop',NULL,'2026-03-01 15:59:58'),(172,106,'impression','desktop',NULL,'2026-03-01 16:00:03'),(173,120,'impression','desktop',NULL,'2026-03-01 16:00:08'),(174,102,'impression','desktop',NULL,'2026-03-01 16:00:13'),(175,106,'impression','desktop',NULL,'2026-03-01 16:00:18'),(176,120,'impression','desktop',NULL,'2026-03-01 16:00:23'),(177,102,'impression','desktop',NULL,'2026-03-01 16:00:28'),(178,106,'impression','desktop',NULL,'2026-03-01 16:00:33'),(179,120,'impression','desktop',NULL,'2026-03-01 16:00:38'),(180,102,'impression','desktop',NULL,'2026-03-01 16:00:43'),(181,106,'impression','desktop',NULL,'2026-03-01 16:00:48'),(182,120,'impression','desktop',NULL,'2026-03-01 16:00:53'),(183,102,'impression','desktop',NULL,'2026-03-01 16:00:58'),(184,106,'impression','desktop',NULL,'2026-03-01 16:01:03'),(185,120,'impression','desktop',NULL,'2026-03-01 16:01:08'),(186,102,'impression','desktop',NULL,'2026-03-01 16:05:11'),(187,106,'impression','desktop',NULL,'2026-03-01 16:05:16'),(188,120,'impression','desktop',NULL,'2026-03-01 16:05:22'),(189,102,'impression','desktop',NULL,'2026-03-01 16:05:27'),(190,106,'impression','desktop',NULL,'2026-03-01 16:05:32'),(191,120,'impression','desktop',NULL,'2026-03-01 16:05:37'),(192,102,'impression','desktop',NULL,'2026-03-01 16:05:42'),(193,106,'impression','desktop',NULL,'2026-03-01 16:05:47'),(194,120,'impression','desktop',NULL,'2026-03-01 16:05:52'),(195,102,'impression','desktop',NULL,'2026-03-01 16:05:57'),(196,106,'impression','desktop',NULL,'2026-03-01 16:06:02'),(197,120,'impression','desktop',NULL,'2026-03-01 16:06:07'),(198,102,'impression','desktop',NULL,'2026-03-01 16:06:12'),(199,106,'impression','desktop',NULL,'2026-03-01 16:06:17'),(200,120,'impression','desktop',NULL,'2026-03-01 16:06:22'),(201,102,'impression','desktop',NULL,'2026-03-01 16:06:27'),(202,106,'impression','desktop',NULL,'2026-03-01 16:06:32'),(203,120,'impression','desktop',NULL,'2026-03-01 16:06:35'),(204,120,'impression','desktop',NULL,'2026-03-01 16:06:37'),(205,102,'impression','desktop',NULL,'2026-03-01 16:06:38'),(206,102,'impression','desktop',NULL,'2026-03-01 16:06:42'),(207,106,'impression','desktop',NULL,'2026-03-01 16:06:43'),(208,106,'impression','desktop',NULL,'2026-03-01 16:06:45'),(209,106,'impression','desktop',NULL,'2026-03-01 16:06:47'),(210,120,'impression','desktop',NULL,'2026-03-01 16:06:52'),(211,102,'impression','desktop',NULL,'2026-03-01 16:06:57'),(212,102,'impression','desktop',NULL,'2026-03-01 16:11:19'),(213,106,'impression','desktop',NULL,'2026-03-01 16:11:24'),(214,120,'impression','desktop',NULL,'2026-03-01 16:11:29'),(215,102,'impression','desktop',NULL,'2026-03-01 16:11:34'),(216,106,'impression','desktop',NULL,'2026-03-01 16:11:39'),(217,120,'impression','desktop',NULL,'2026-03-01 16:11:44'),(218,102,'impression','desktop',NULL,'2026-03-01 16:11:49'),(219,106,'impression','desktop',NULL,'2026-03-01 16:11:54'),(220,120,'impression','desktop',NULL,'2026-03-01 16:11:59'),(221,102,'impression','desktop',NULL,'2026-03-01 16:12:04'),(222,106,'impression','desktop',NULL,'2026-03-01 16:12:09'),(223,120,'impression','desktop',NULL,'2026-03-01 16:12:14'),(224,102,'impression','desktop',NULL,'2026-03-01 16:12:19'),(225,106,'impression','desktop',NULL,'2026-03-01 16:12:24'),(226,120,'impression','desktop',NULL,'2026-03-01 16:12:29'),(227,102,'impression','desktop',NULL,'2026-03-01 16:12:34'),(228,106,'impression','desktop',NULL,'2026-03-01 16:12:39'),(229,120,'impression','desktop',NULL,'2026-03-01 16:12:44'),(230,102,'impression','desktop',NULL,'2026-03-01 16:12:49'),(231,102,'impression','desktop',NULL,'2026-03-01 16:17:21'),(232,106,'impression','desktop',NULL,'2026-03-01 16:17:26'),(233,120,'impression','desktop',NULL,'2026-03-01 16:17:31'),(234,102,'impression','desktop',NULL,'2026-03-01 16:17:36'),(235,106,'impression','desktop',NULL,'2026-03-01 16:17:41'),(236,120,'impression','desktop',NULL,'2026-03-01 16:17:46'),(237,102,'impression','desktop',NULL,'2026-03-01 16:17:51'),(238,106,'impression','desktop',NULL,'2026-03-01 16:17:56'),(239,120,'impression','desktop',NULL,'2026-03-01 16:18:01'),(240,102,'impression','desktop',NULL,'2026-03-01 16:18:06'),(241,106,'impression','desktop',NULL,'2026-03-01 16:18:11'),(242,120,'impression','desktop',NULL,'2026-03-01 16:18:16'),(243,102,'impression','desktop',NULL,'2026-03-01 16:18:21'),(244,106,'impression','desktop',NULL,'2026-03-01 16:18:26'),(245,120,'impression','desktop',NULL,'2026-03-01 16:18:31'),(246,102,'impression','desktop',NULL,'2026-03-01 16:18:36'),(247,106,'impression','desktop',NULL,'2026-03-01 16:18:41'),(248,120,'impression','desktop',NULL,'2026-03-01 16:18:46'),(249,102,'impression','desktop',NULL,'2026-03-01 16:18:51'),(250,102,'impression','desktop',NULL,'2026-03-01 16:22:57'),(251,106,'impression','desktop',NULL,'2026-03-01 16:23:02'),(252,120,'impression','desktop',NULL,'2026-03-01 16:23:07'),(253,102,'impression','desktop',NULL,'2026-03-01 16:23:12'),(254,106,'impression','desktop',NULL,'2026-03-01 16:23:17'),(255,120,'impression','desktop',NULL,'2026-03-01 16:23:22'),(256,102,'impression','desktop',NULL,'2026-03-01 16:23:27'),(257,106,'impression','desktop',NULL,'2026-03-01 16:23:32'),(258,120,'impression','desktop',NULL,'2026-03-01 16:23:37'),(259,102,'impression','desktop',NULL,'2026-03-01 16:23:42'),(260,106,'impression','desktop',NULL,'2026-03-01 16:23:47'),(261,120,'impression','desktop',NULL,'2026-03-01 16:23:52'),(262,102,'impression','desktop',NULL,'2026-03-01 16:23:57'),(263,106,'impression','desktop',NULL,'2026-03-01 16:24:02'),(264,120,'impression','desktop',NULL,'2026-03-01 16:24:07'),(265,102,'impression','desktop',NULL,'2026-03-01 16:24:12'),(266,106,'impression','desktop',NULL,'2026-03-01 16:24:17'),(267,120,'impression','desktop',NULL,'2026-03-01 16:24:22'),(268,102,'impression','desktop',NULL,'2026-03-01 16:24:23'),(269,102,'impression','desktop',NULL,'2026-03-01 16:24:27'),(270,106,'impression','desktop',NULL,'2026-03-01 16:24:32'),(271,120,'impression','desktop',NULL,'2026-03-01 16:24:37'),(272,102,'impression','desktop',NULL,'2026-03-01 16:24:42'),(273,106,'impression','desktop',NULL,'2026-03-01 16:24:47'),(274,120,'impression','desktop',NULL,'2026-03-01 16:24:49'),(275,120,'impression','desktop',NULL,'2026-03-01 16:24:52'),(276,102,'impression','desktop',NULL,'2026-03-01 16:24:53'),(277,102,'impression','desktop',NULL,'2026-03-01 16:24:56'),(278,102,'impression','desktop',NULL,'2026-03-01 16:24:57'),(279,106,'impression','desktop',NULL,'2026-03-01 16:24:58'),(280,106,'impression','desktop',NULL,'2026-03-01 16:25:02'),(281,120,'impression','desktop',NULL,'2026-03-01 16:25:03'),(282,120,'impression','desktop',NULL,'2026-03-01 16:25:07'),(283,102,'impression','desktop',NULL,'2026-03-01 16:25:12'),(284,106,'impression','desktop',NULL,'2026-03-01 16:25:17'),(285,120,'impression','desktop',NULL,'2026-03-01 16:25:22'),(286,102,'impression','desktop',NULL,'2026-03-01 16:25:27'),(287,106,'impression','desktop',NULL,'2026-03-01 16:25:32'),(288,120,'impression','desktop',NULL,'2026-03-01 16:25:37'),(289,102,'impression','desktop',NULL,'2026-03-01 16:29:09'),(290,102,'impression','desktop',NULL,'2026-03-01 16:29:15'),(291,106,'impression','desktop',NULL,'2026-03-01 16:29:20'),(292,120,'impression','desktop',NULL,'2026-03-01 16:29:25'),(293,102,'impression','desktop',NULL,'2026-03-01 16:29:30'),(294,106,'impression','desktop',NULL,'2026-03-01 16:29:32'),(295,106,'impression','desktop',NULL,'2026-03-01 16:29:35'),(296,120,'impression','desktop',NULL,'2026-03-01 16:29:40'),(297,102,'impression','desktop',NULL,'2026-03-01 16:29:45'),(298,106,'impression','desktop',NULL,'2026-03-01 16:29:50'),(299,120,'impression','desktop',NULL,'2026-03-01 16:29:53'),(300,120,'impression','desktop',NULL,'2026-03-01 16:29:55'),(301,102,'impression','desktop',NULL,'2026-03-01 16:30:00'),(302,106,'impression','desktop',NULL,'2026-03-01 16:30:05'),(303,120,'impression','desktop',NULL,'2026-03-01 16:30:06'),(304,120,'impression','desktop',NULL,'2026-03-01 16:30:10'),(305,102,'impression','desktop',NULL,'2026-03-01 16:30:15'),(306,106,'impression','desktop',NULL,'2026-03-01 16:30:20'),(307,120,'impression','desktop',NULL,'2026-03-01 16:30:25'),(308,102,'impression','desktop',NULL,'2026-03-01 16:30:30'),(309,106,'impression','desktop',NULL,'2026-03-01 16:30:35'),(310,120,'impression','desktop',NULL,'2026-03-01 16:30:40'),(311,102,'impression','desktop',NULL,'2026-03-01 16:30:45'),(312,102,'impression','desktop',NULL,'2026-03-01 16:34:11'),(313,106,'impression','desktop',NULL,'2026-03-01 16:34:16'),(314,120,'impression','desktop',NULL,'2026-03-01 16:34:21'),(315,102,'impression','desktop',NULL,'2026-03-01 16:34:25'),(316,102,'impression','desktop',NULL,'2026-03-01 16:34:26'),(317,106,'impression','desktop',NULL,'2026-03-01 16:34:31'),(318,120,'impression','desktop',NULL,'2026-03-01 16:34:36'),(319,102,'impression','desktop',NULL,'2026-03-01 16:34:41'),(320,106,'impression','desktop',NULL,'2026-03-01 16:34:44'),(321,106,'impression','desktop',NULL,'2026-03-01 16:34:46'),(322,120,'impression','desktop',NULL,'2026-03-01 16:34:51'),(323,102,'impression','desktop',NULL,'2026-03-01 16:34:56'),(324,106,'impression','desktop',NULL,'2026-03-01 16:35:01'),(325,120,'impression','desktop',NULL,'2026-03-01 16:35:06'),(326,102,'impression','desktop',NULL,'2026-03-01 16:35:11'),(327,106,'impression','desktop',NULL,'2026-03-01 16:35:16'),(328,120,'impression','desktop',NULL,'2026-03-01 16:35:21'),(329,102,'impression','desktop',NULL,'2026-03-01 16:35:26'),(330,106,'impression','desktop',NULL,'2026-03-01 16:35:31'),(331,120,'impression','desktop',NULL,'2026-03-01 16:35:36'),(332,102,'impression','desktop',NULL,'2026-03-01 16:35:41'),(333,106,'impression','desktop',NULL,'2026-03-01 16:35:46'),(334,120,'impression','desktop',NULL,'2026-03-01 16:35:51'),(335,102,'impression','desktop',NULL,'2026-03-01 16:35:56'),(336,106,'impression','desktop',NULL,'2026-03-01 16:36:01'),(337,120,'impression','desktop',NULL,'2026-03-01 16:36:06'),(338,102,'impression','desktop',NULL,'2026-03-01 16:36:11'),(339,106,'impression','desktop',NULL,'2026-03-01 16:36:16'),(340,120,'impression','desktop',NULL,'2026-03-01 16:36:21'),(341,102,'impression','desktop',NULL,'2026-03-01 16:36:26'),(342,106,'impression','desktop',NULL,'2026-03-01 16:36:31'),(343,120,'impression','desktop',NULL,'2026-03-01 16:36:36'),(344,102,'impression','desktop',NULL,'2026-03-01 16:36:41'),(345,106,'impression','desktop',NULL,'2026-03-01 16:36:46'),(346,120,'impression','desktop',NULL,'2026-03-01 16:36:51'),(347,102,'impression','desktop',NULL,'2026-03-01 16:36:56'),(348,106,'impression','desktop',NULL,'2026-03-01 16:37:01'),(349,120,'impression','desktop',NULL,'2026-03-01 16:37:06'),(350,102,'impression','desktop',NULL,'2026-03-01 16:37:11'),(351,102,'impression','desktop',NULL,'2026-03-01 16:37:20'),(352,106,'impression','desktop',NULL,'2026-03-01 16:37:21'),(353,106,'impression','desktop',NULL,'2026-03-01 16:37:25'),(354,120,'impression','desktop',NULL,'2026-03-01 16:37:30'),(355,102,'impression','desktop',NULL,'2026-03-01 16:37:33'),(356,102,'impression','desktop',NULL,'2026-03-01 16:37:35'),(357,106,'impression','desktop',NULL,'2026-03-01 16:37:40'),(358,120,'impression','desktop',NULL,'2026-03-01 16:37:45'),(359,102,'impression','desktop',NULL,'2026-03-01 16:37:50'),(360,106,'impression','desktop',NULL,'2026-03-01 16:37:55'),(361,120,'impression','desktop',NULL,'2026-03-01 16:38:00'),(362,102,'impression','desktop',NULL,'2026-03-01 16:38:05'),(363,106,'impression','desktop',NULL,'2026-03-01 16:38:10'),(364,120,'impression','desktop',NULL,'2026-03-01 16:38:15'),(365,102,'impression','desktop',NULL,'2026-03-01 16:38:20'),(366,106,'impression','desktop',NULL,'2026-03-01 16:38:25'),(367,120,'impression','desktop',NULL,'2026-03-01 16:38:30'),(368,102,'impression','desktop',NULL,'2026-03-01 16:38:35'),(369,106,'impression','desktop',NULL,'2026-03-01 16:38:40'),(370,102,'impression','desktop',NULL,'2026-03-01 16:42:41'),(371,106,'impression','desktop',NULL,'2026-03-01 16:42:46'),(372,120,'impression','desktop',NULL,'2026-03-01 16:42:51'),(373,102,'impression','desktop',NULL,'2026-03-01 16:42:56'),(374,106,'impression','desktop',NULL,'2026-03-01 16:43:01'),(375,120,'impression','desktop',NULL,'2026-03-01 16:43:06'),(376,102,'impression','desktop',NULL,'2026-03-01 16:43:11'),(377,106,'impression','desktop',NULL,'2026-03-01 16:43:16'),(378,120,'impression','desktop',NULL,'2026-03-01 16:43:21'),(379,102,'impression','desktop',NULL,'2026-03-01 16:43:26'),(380,106,'impression','desktop',NULL,'2026-03-01 16:43:31'),(381,120,'impression','desktop',NULL,'2026-03-01 16:43:36'),(382,102,'impression','desktop',NULL,'2026-03-01 16:43:41'),(383,106,'impression','desktop',NULL,'2026-03-01 16:43:46'),(384,120,'impression','desktop',NULL,'2026-03-01 16:43:51'),(385,102,'impression','desktop',NULL,'2026-03-01 16:43:56'),(386,106,'impression','desktop',NULL,'2026-03-01 16:44:01'),(387,120,'impression','desktop',NULL,'2026-03-01 16:44:06'),(388,102,'impression','desktop',NULL,'2026-03-01 16:50:30'),(389,106,'impression','desktop',NULL,'2026-03-01 16:50:35'),(390,120,'impression','desktop',NULL,'2026-03-01 16:50:40'),(391,102,'impression','desktop',NULL,'2026-03-01 16:50:45'),(392,106,'impression','desktop',NULL,'2026-03-01 16:50:50'),(393,120,'impression','desktop',NULL,'2026-03-01 16:50:55'),(394,102,'impression','desktop',NULL,'2026-03-01 16:51:00'),(395,106,'impression','desktop',NULL,'2026-03-01 16:51:05'),(396,120,'impression','desktop',NULL,'2026-03-01 16:51:10'),(397,102,'impression','desktop',NULL,'2026-03-01 16:51:15'),(398,106,'impression','desktop',NULL,'2026-03-01 16:51:20'),(399,120,'impression','desktop',NULL,'2026-03-01 16:51:25'),(400,102,'impression','desktop',NULL,'2026-03-01 16:51:30'),(401,106,'impression','desktop',NULL,'2026-03-01 16:51:35'),(402,120,'impression','desktop',NULL,'2026-03-01 16:51:40'),(403,102,'impression','desktop',NULL,'2026-03-01 16:51:45'),(404,106,'impression','desktop',NULL,'2026-03-01 16:51:50'),(405,120,'impression','desktop',NULL,'2026-03-01 16:51:55'),(406,102,'impression','desktop',NULL,'2026-03-01 16:52:00'),(407,106,'impression','desktop',NULL,'2026-03-01 16:52:05'),(408,120,'impression','desktop',NULL,'2026-03-01 16:52:10'),(409,102,'impression','desktop',NULL,'2026-03-01 16:52:15'),(410,106,'impression','desktop',NULL,'2026-03-01 16:52:20'),(411,120,'impression','desktop',NULL,'2026-03-01 16:52:25'),(412,102,'impression','desktop',NULL,'2026-03-01 16:52:30'),(413,106,'impression','desktop',NULL,'2026-03-01 16:52:35'),(414,120,'impression','desktop',NULL,'2026-03-01 16:52:40'),(415,102,'impression','desktop',NULL,'2026-03-01 16:52:45'),(416,106,'impression','desktop',NULL,'2026-03-01 16:52:50'),(417,120,'impression','desktop',NULL,'2026-03-01 16:52:52'),(418,120,'impression','desktop',NULL,'2026-03-01 16:52:55'),(419,102,'impression','desktop',NULL,'2026-03-01 16:53:00'),(420,106,'impression','desktop',NULL,'2026-03-01 16:53:05'),(421,120,'impression','desktop',NULL,'2026-03-01 16:53:10'),(422,102,'impression','desktop',NULL,'2026-03-01 16:53:15'),(423,102,'impression','desktop',NULL,'2026-03-01 16:57:59'),(424,106,'impression','desktop',NULL,'2026-03-01 16:58:04'),(425,120,'impression','desktop',NULL,'2026-03-01 16:58:09'),(426,102,'impression','desktop',NULL,'2026-03-01 16:58:09'),(427,102,'impression','desktop',NULL,'2026-03-01 16:58:14'),(428,106,'impression','desktop',NULL,'2026-03-01 16:58:19'),(429,120,'impression','desktop',NULL,'2026-03-01 16:58:24'),(430,102,'impression','desktop',NULL,'2026-03-01 16:58:29'),(431,102,'impression','mobile',NULL,'2026-03-01 17:00:02'),(432,106,'impression','mobile',NULL,'2026-03-01 17:00:07'),(433,120,'impression','mobile',NULL,'2026-03-01 17:00:12'),(434,102,'impression','mobile',NULL,'2026-03-01 17:00:17'),(435,106,'impression','mobile',NULL,'2026-03-01 17:00:22'),(436,120,'impression','mobile',NULL,'2026-03-01 17:00:27'),(437,102,'impression','mobile',NULL,'2026-03-01 17:00:32'),(438,106,'impression','mobile',NULL,'2026-03-01 17:00:37'),(439,120,'impression','mobile',NULL,'2026-03-01 17:00:42'),(440,102,'impression','mobile',NULL,'2026-03-01 17:00:47'),(441,106,'impression','mobile',NULL,'2026-03-01 17:00:52'),(442,120,'impression','mobile',NULL,'2026-03-01 17:00:57'),(443,102,'impression','mobile',NULL,'2026-03-01 17:01:02'),(444,106,'impression','mobile',NULL,'2026-03-01 17:01:07'),(445,120,'impression','mobile',NULL,'2026-03-01 17:01:12'),(446,102,'impression','mobile',NULL,'2026-03-01 17:01:17'),(447,106,'impression','mobile',NULL,'2026-03-01 17:01:22'),(448,120,'impression','mobile',NULL,'2026-03-01 17:01:27'),(449,102,'impression','mobile',NULL,'2026-03-01 17:01:32'),(450,106,'impression','mobile',NULL,'2026-03-01 17:01:37'),(451,120,'impression','mobile',NULL,'2026-03-01 17:01:42'),(452,102,'impression','mobile',NULL,'2026-03-01 17:01:47'),(453,106,'impression','mobile',NULL,'2026-03-01 17:01:52'),(454,120,'impression','mobile',NULL,'2026-03-01 17:01:57'),(455,102,'impression','mobile',NULL,'2026-03-01 17:02:02'),(456,106,'impression','mobile',NULL,'2026-03-01 17:02:07'),(457,120,'impression','mobile',NULL,'2026-03-01 17:02:12'),(458,102,'impression','desktop',NULL,'2026-03-01 17:05:47'),(459,106,'impression','desktop',NULL,'2026-03-01 17:05:52'),(460,120,'impression','desktop',NULL,'2026-03-01 17:05:57'),(461,102,'impression','mobile',NULL,'2026-03-01 17:06:00'),(462,102,'impression','desktop',NULL,'2026-03-01 17:06:02'),(463,106,'impression','mobile',NULL,'2026-03-01 17:06:05'),(464,106,'impression','desktop',NULL,'2026-03-01 17:06:07'),(465,120,'impression','mobile',NULL,'2026-03-01 17:06:10'),(466,120,'impression','desktop',NULL,'2026-03-01 17:06:12'),(467,102,'impression','mobile',NULL,'2026-03-01 17:06:15'),(468,102,'impression','desktop',NULL,'2026-03-01 17:06:17'),(469,106,'impression','mobile',NULL,'2026-03-01 17:06:20'),(470,106,'impression','desktop',NULL,'2026-03-01 17:06:22'),(471,120,'impression','mobile',NULL,'2026-03-01 17:06:25'),(472,120,'impression','desktop',NULL,'2026-03-01 17:06:27'),(473,102,'impression','mobile',NULL,'2026-03-01 17:06:30'),(474,102,'impression','desktop',NULL,'2026-03-01 17:06:32'),(475,106,'impression','mobile',NULL,'2026-03-01 17:06:35'),(476,106,'impression','desktop',NULL,'2026-03-01 17:06:37'),(477,120,'impression','mobile',NULL,'2026-03-01 17:06:40'),(478,120,'impression','desktop',NULL,'2026-03-01 17:06:42'),(479,102,'impression','mobile',NULL,'2026-03-01 17:06:45'),(480,102,'impression','desktop',NULL,'2026-03-01 17:06:47'),(481,106,'impression','mobile',NULL,'2026-03-01 17:06:50'),(482,106,'impression','desktop',NULL,'2026-03-01 17:06:52'),(483,120,'impression','mobile',NULL,'2026-03-01 17:06:55'),(484,102,'impression','mobile',NULL,'2026-03-01 17:07:00'),(485,106,'impression','mobile',NULL,'2026-03-01 17:07:05'),(486,120,'impression','mobile',NULL,'2026-03-01 17:07:10'),(487,102,'impression','mobile',NULL,'2026-03-01 17:07:15'),(488,106,'impression','mobile',NULL,'2026-03-01 17:07:20'),(489,120,'impression','mobile',NULL,'2026-03-01 17:07:25'),(490,102,'impression','mobile',NULL,'2026-03-01 17:07:30'),(491,106,'impression','mobile',NULL,'2026-03-01 17:07:35'),(492,120,'impression','mobile',NULL,'2026-03-01 17:07:40'),(493,102,'impression','mobile',NULL,'2026-03-01 17:07:46'),(494,106,'impression','mobile',NULL,'2026-03-01 17:07:50'),(495,120,'impression','mobile',NULL,'2026-03-01 17:07:56'),(496,102,'impression','mobile',NULL,'2026-03-01 17:08:01'),(497,106,'impression','mobile',NULL,'2026-03-01 17:08:05'),(498,120,'impression','mobile',NULL,'2026-03-01 17:08:10'),(499,102,'impression','mobile',NULL,'2026-03-01 17:08:15'),(500,106,'impression','mobile',NULL,'2026-03-01 17:08:21'),(501,120,'impression','mobile',NULL,'2026-03-01 17:08:26'),(502,102,'impression','mobile',NULL,'2026-03-01 17:08:30'),(503,106,'impression','mobile',NULL,'2026-03-01 17:08:35'),(504,120,'impression','mobile',NULL,'2026-03-01 17:08:40'),(505,102,'impression','mobile',NULL,'2026-03-01 17:08:45'),(506,106,'impression','mobile',NULL,'2026-03-01 17:08:51'),(507,120,'impression','mobile',NULL,'2026-03-01 17:08:56'),(508,102,'impression','mobile',NULL,'2026-03-01 17:09:01'),(509,106,'impression','mobile',NULL,'2026-03-01 17:09:05'),(510,120,'impression','mobile',NULL,'2026-03-01 17:09:11'),(511,102,'impression','mobile',NULL,'2026-03-01 17:09:16'),(512,106,'impression','mobile',NULL,'2026-03-01 17:09:21'),(513,120,'impression','mobile',NULL,'2026-03-01 17:09:26'),(514,102,'impression','mobile',NULL,'2026-03-01 17:09:31'),(515,106,'impression','mobile',NULL,'2026-03-01 17:09:36'),(516,120,'impression','mobile',NULL,'2026-03-01 17:09:41'),(517,102,'impression','mobile',NULL,'2026-03-01 17:09:46'),(518,106,'impression','mobile',NULL,'2026-03-01 17:09:51'),(519,120,'impression','mobile',NULL,'2026-03-01 17:09:56'),(520,102,'impression','mobile',NULL,'2026-03-01 17:10:01'),(521,106,'impression','mobile',NULL,'2026-03-01 17:10:06'),(522,120,'impression','mobile',NULL,'2026-03-01 17:10:11'),(523,102,'impression','mobile',NULL,'2026-03-01 17:10:16'),(524,106,'impression','mobile',NULL,'2026-03-01 17:10:21'),(525,120,'impression','mobile',NULL,'2026-03-01 17:10:26'),(526,102,'impression','mobile',NULL,'2026-03-01 17:10:31'),(527,106,'impression','mobile',NULL,'2026-03-01 17:10:36'),(528,120,'impression','mobile',NULL,'2026-03-01 17:10:41'),(529,102,'impression','mobile',NULL,'2026-03-01 17:10:46'),(530,106,'impression','mobile',NULL,'2026-03-01 17:10:51'),(531,102,'impression','desktop',NULL,'2026-03-01 17:10:53'),(532,120,'impression','mobile',NULL,'2026-03-01 17:10:56'),(533,106,'impression','desktop',NULL,'2026-03-01 17:10:58'),(534,102,'impression','mobile',NULL,'2026-03-01 17:11:01'),(535,120,'impression','desktop',NULL,'2026-03-01 17:11:03'),(536,106,'impression','mobile',NULL,'2026-03-01 17:11:06'),(537,102,'impression','desktop',NULL,'2026-03-01 17:11:08'),(538,120,'impression','mobile',NULL,'2026-03-01 17:11:11'),(539,106,'impression','desktop',NULL,'2026-03-01 17:11:13'),(540,102,'impression','mobile',NULL,'2026-03-01 17:11:16'),(541,120,'impression','desktop',NULL,'2026-03-01 17:11:18'),(542,106,'impression','mobile',NULL,'2026-03-01 17:11:21'),(543,102,'impression','desktop',NULL,'2026-03-01 17:11:23'),(544,120,'impression','mobile',NULL,'2026-03-01 17:11:26'),(545,106,'impression','desktop',NULL,'2026-03-01 17:11:28'),(546,102,'impression','mobile',NULL,'2026-03-01 17:11:31'),(547,106,'impression','mobile',NULL,'2026-03-01 17:11:36'),(548,102,'impression','desktop',NULL,'2026-03-01 17:11:36'),(549,120,'impression','mobile',NULL,'2026-03-01 17:11:41'),(550,106,'impression','desktop',NULL,'2026-03-01 17:11:41'),(551,102,'impression','mobile',NULL,'2026-03-01 17:11:46'),(552,120,'impression','desktop',NULL,'2026-03-01 17:11:46'),(553,106,'impression','mobile',NULL,'2026-03-01 17:11:51'),(554,102,'impression','desktop',NULL,'2026-03-01 17:11:51'),(555,120,'impression','mobile',NULL,'2026-03-01 17:11:56'),(556,106,'impression','desktop',NULL,'2026-03-01 17:11:56'),(557,102,'impression','mobile',NULL,'2026-03-01 17:12:01'),(558,120,'impression','desktop',NULL,'2026-03-01 17:12:01'),(559,106,'impression','mobile',NULL,'2026-03-01 17:12:06'),(560,102,'impression','desktop',NULL,'2026-03-01 17:12:06'),(561,120,'impression','mobile',NULL,'2026-03-01 17:12:11'),(562,106,'impression','desktop',NULL,'2026-03-01 17:12:11'),(563,120,'impression','desktop',NULL,'2026-03-01 17:12:16'),(564,102,'impression','desktop',NULL,'2026-03-01 17:12:21'),(565,106,'impression','desktop',NULL,'2026-03-01 17:12:26'),(566,120,'impression','desktop',NULL,'2026-03-01 17:12:31'),(567,102,'impression','desktop',NULL,'2026-03-01 17:12:36'),(568,106,'impression','desktop',NULL,'2026-03-01 17:12:41'),(569,120,'impression','desktop',NULL,'2026-03-01 17:12:46'),(570,102,'impression','desktop',NULL,'2026-03-01 17:12:52'),(571,106,'impression','desktop',NULL,'2026-03-01 17:12:56'),(572,120,'impression','desktop',NULL,'2026-03-01 17:13:01'),(573,102,'impression','desktop',NULL,'2026-03-01 17:13:06'),(574,106,'impression','desktop',NULL,'2026-03-01 17:13:12'),(575,120,'impression','desktop',NULL,'2026-03-01 17:13:17'),(576,102,'impression','desktop',NULL,'2026-03-01 17:13:22'),(577,106,'impression','desktop',NULL,'2026-03-01 17:13:27'),(578,120,'impression','desktop',NULL,'2026-03-01 17:13:32'),(579,102,'impression','desktop',NULL,'2026-03-01 17:13:37'),(580,106,'impression','desktop',NULL,'2026-03-01 17:13:42'),(581,120,'impression','desktop',NULL,'2026-03-01 17:13:47'),(582,102,'impression','desktop',NULL,'2026-03-01 17:13:52'),(583,106,'impression','desktop',NULL,'2026-03-01 17:13:57'),(584,120,'impression','desktop',NULL,'2026-03-01 17:14:02'),(585,102,'impression','desktop',NULL,'2026-03-01 17:14:07'),(586,106,'impression','desktop',NULL,'2026-03-01 17:14:12'),(587,120,'impression','desktop',NULL,'2026-03-01 17:14:17'),(588,102,'impression','desktop',NULL,'2026-03-01 17:14:22'),(589,106,'impression','desktop',NULL,'2026-03-01 17:14:27'),(590,120,'impression','desktop',NULL,'2026-03-01 17:14:32'),(591,102,'impression','desktop',NULL,'2026-03-01 17:14:37'),(592,106,'impression','desktop',NULL,'2026-03-01 17:14:42'),(593,120,'impression','desktop',NULL,'2026-03-01 17:14:47'),(594,102,'impression','desktop',NULL,'2026-03-01 17:14:52'),(595,106,'impression','desktop',NULL,'2026-03-01 17:14:57'),(596,120,'impression','desktop',NULL,'2026-03-01 17:15:02'),(597,102,'impression','desktop',NULL,'2026-03-01 17:15:07'),(598,106,'impression','desktop',NULL,'2026-03-01 17:15:12'),(599,120,'impression','desktop',NULL,'2026-03-01 17:15:17'),(600,102,'impression','desktop',NULL,'2026-03-01 17:15:22'),(601,106,'impression','desktop',NULL,'2026-03-01 17:15:27'),(602,120,'impression','desktop',NULL,'2026-03-01 17:15:32'),(603,102,'impression','desktop',NULL,'2026-03-01 17:15:37'),(604,106,'impression','desktop',NULL,'2026-03-01 17:15:42'),(605,120,'impression','desktop',NULL,'2026-03-01 17:15:47'),(606,102,'impression','desktop',NULL,'2026-03-01 17:15:52'),(607,106,'impression','desktop',NULL,'2026-03-01 17:15:57'),(608,120,'impression','desktop',NULL,'2026-03-01 17:16:02'),(609,102,'impression','desktop',NULL,'2026-03-01 17:16:07'),(610,106,'impression','desktop',NULL,'2026-03-01 17:16:12'),(611,120,'impression','desktop',NULL,'2026-03-01 17:16:17'),(612,102,'impression','desktop',NULL,'2026-03-01 17:16:22'),(613,106,'impression','desktop',NULL,'2026-03-01 17:16:27'),(614,120,'impression','desktop',NULL,'2026-03-01 17:16:32'),(615,102,'impression','desktop',NULL,'2026-03-01 17:16:37'),(616,106,'impression','desktop',NULL,'2026-03-01 17:16:42'),(617,120,'impression','desktop',NULL,'2026-03-01 17:16:47'),(618,102,'impression','desktop',NULL,'2026-03-01 17:16:52'),(619,106,'impression','desktop',NULL,'2026-03-01 17:16:57'),(620,120,'impression','desktop',NULL,'2026-03-01 17:17:02'),(621,102,'impression','desktop',NULL,'2026-03-01 17:17:07'),(622,106,'impression','desktop',NULL,'2026-03-01 17:17:12'),(623,120,'impression','desktop',NULL,'2026-03-01 17:17:17'),(624,102,'impression','desktop',NULL,'2026-03-01 17:17:22'),(625,106,'impression','desktop',NULL,'2026-03-01 17:17:27'),(626,120,'impression','desktop',NULL,'2026-03-01 17:17:32'),(627,102,'impression','desktop',NULL,'2026-03-01 17:17:37'),(628,106,'impression','desktop',NULL,'2026-03-01 17:17:42'),(629,120,'impression','desktop',NULL,'2026-03-01 17:17:47'),(630,102,'impression','desktop',NULL,'2026-03-01 17:17:52'),(631,106,'impression','desktop',NULL,'2026-03-01 17:17:57'),(632,120,'impression','desktop',NULL,'2026-03-01 17:18:02'),(633,102,'impression','desktop',NULL,'2026-03-01 17:18:07'),(634,106,'impression','desktop',NULL,'2026-03-01 17:18:12'),(635,120,'impression','desktop',NULL,'2026-03-01 17:18:17'),(636,102,'impression','desktop',NULL,'2026-03-01 17:18:22'),(637,106,'impression','desktop',NULL,'2026-03-01 17:18:27'),(638,120,'impression','desktop',NULL,'2026-03-01 17:18:32'),(639,102,'impression','desktop',NULL,'2026-03-01 17:18:37'),(640,106,'impression','desktop',NULL,'2026-03-01 17:18:42'),(641,120,'impression','desktop',NULL,'2026-03-01 17:18:47'),(642,102,'impression','desktop',NULL,'2026-03-01 17:18:52'),(643,106,'impression','desktop',NULL,'2026-03-01 17:18:57'),(644,120,'impression','desktop',NULL,'2026-03-01 17:19:02'),(645,102,'impression','desktop',NULL,'2026-03-01 17:19:07'),(646,106,'impression','desktop',NULL,'2026-03-01 17:19:12'),(647,120,'impression','desktop',NULL,'2026-03-01 17:19:17'),(648,102,'impression','desktop',NULL,'2026-03-01 17:19:22'),(649,106,'impression','desktop',NULL,'2026-03-01 17:19:27'),(650,120,'impression','desktop',NULL,'2026-03-01 17:19:32'),(651,102,'impression','desktop',NULL,'2026-03-01 17:19:37'),(652,106,'impression','desktop',NULL,'2026-03-01 17:19:42'),(653,120,'impression','desktop',NULL,'2026-03-01 17:19:47'),(654,102,'impression','desktop',NULL,'2026-03-01 17:19:52'),(655,106,'impression','desktop',NULL,'2026-03-01 17:19:57'),(656,120,'impression','desktop',NULL,'2026-03-01 17:20:02'),(657,102,'impression','desktop',NULL,'2026-03-01 17:20:07'),(658,106,'impression','desktop',NULL,'2026-03-01 17:20:12'),(659,120,'impression','desktop',NULL,'2026-03-01 17:20:17'),(660,102,'impression','desktop',NULL,'2026-03-01 17:20:22'),(661,106,'impression','desktop',NULL,'2026-03-01 17:20:27'),(662,120,'impression','desktop',NULL,'2026-03-01 17:20:32'),(663,102,'impression','desktop',NULL,'2026-03-01 17:20:37'),(664,106,'impression','desktop',NULL,'2026-03-01 17:20:42'),(665,120,'impression','desktop',NULL,'2026-03-01 17:20:47'),(666,102,'impression','desktop',NULL,'2026-03-01 17:20:52'),(667,106,'impression','desktop',NULL,'2026-03-01 17:20:57'),(668,120,'impression','desktop',NULL,'2026-03-01 17:21:02'),(669,102,'impression','desktop',NULL,'2026-03-01 17:21:07'),(670,106,'impression','desktop',NULL,'2026-03-01 17:21:12'),(671,120,'impression','desktop',NULL,'2026-03-01 17:21:17'),(672,102,'impression','desktop',NULL,'2026-03-01 17:21:22'),(673,106,'impression','desktop',NULL,'2026-03-01 17:21:27'),(674,120,'impression','desktop',NULL,'2026-03-01 17:21:32'),(675,102,'impression','desktop',NULL,'2026-03-01 17:21:37'),(676,106,'impression','desktop',NULL,'2026-03-01 17:21:42'),(677,120,'impression','desktop',NULL,'2026-03-01 17:21:47'),(678,102,'impression','desktop',NULL,'2026-03-01 17:21:52'),(679,106,'impression','desktop',NULL,'2026-03-01 17:21:57'),(680,120,'impression','desktop',NULL,'2026-03-01 17:22:02'),(681,102,'impression','desktop',NULL,'2026-03-01 17:22:07'),(682,106,'impression','desktop',NULL,'2026-03-01 17:22:12'),(683,120,'impression','desktop',NULL,'2026-03-01 17:22:17'),(684,102,'impression','desktop',NULL,'2026-03-01 17:22:22'),(685,106,'impression','desktop',NULL,'2026-03-01 17:22:27'),(686,120,'impression','desktop',NULL,'2026-03-01 17:22:32'),(687,102,'impression','desktop',NULL,'2026-03-01 17:22:37'),(688,106,'impression','desktop',NULL,'2026-03-01 17:22:42'),(689,120,'impression','desktop',NULL,'2026-03-01 17:22:47'),(690,102,'impression','desktop',NULL,'2026-03-01 17:22:52'),(691,106,'impression','desktop',NULL,'2026-03-01 17:22:57'),(692,120,'impression','desktop',NULL,'2026-03-01 17:23:02'),(693,102,'impression','desktop',NULL,'2026-03-01 17:23:07'),(694,106,'impression','desktop',NULL,'2026-03-01 17:23:12'),(695,120,'impression','desktop',NULL,'2026-03-01 17:23:17'),(696,102,'impression','desktop',NULL,'2026-03-01 17:23:22'),(697,106,'impression','desktop',NULL,'2026-03-01 17:23:27'),(698,120,'impression','desktop',NULL,'2026-03-01 17:23:32'),(699,102,'impression','desktop',NULL,'2026-03-01 17:23:37'),(700,106,'impression','desktop',NULL,'2026-03-01 17:23:42'),(701,120,'impression','desktop',NULL,'2026-03-01 17:23:47'),(702,102,'impression','desktop',NULL,'2026-03-01 17:23:52'),(703,106,'impression','desktop',NULL,'2026-03-01 17:23:57'),(704,120,'impression','desktop',NULL,'2026-03-01 17:24:02'),(705,102,'impression','desktop',NULL,'2026-03-01 17:24:07'),(706,106,'impression','desktop',NULL,'2026-03-01 17:24:12'),(707,120,'impression','desktop',NULL,'2026-03-01 17:24:17'),(708,102,'impression','desktop',NULL,'2026-03-01 17:24:22'),(709,106,'impression','desktop',NULL,'2026-03-01 17:24:27'),(710,120,'impression','desktop',NULL,'2026-03-01 17:24:32'),(711,102,'impression','desktop',NULL,'2026-03-01 17:24:37'),(712,106,'impression','desktop',NULL,'2026-03-01 17:24:42'),(713,120,'impression','desktop',NULL,'2026-03-01 17:24:47'),(714,102,'impression','desktop',NULL,'2026-03-01 17:24:52'),(715,106,'impression','desktop',NULL,'2026-03-01 17:24:57'),(716,120,'impression','desktop',NULL,'2026-03-01 17:25:02'),(717,102,'impression','desktop',NULL,'2026-03-01 17:25:07'),(718,106,'impression','desktop',NULL,'2026-03-01 17:25:12'),(719,120,'impression','desktop',NULL,'2026-03-01 17:25:17'),(720,102,'impression','desktop',NULL,'2026-03-01 17:25:22'),(721,106,'impression','desktop',NULL,'2026-03-01 17:25:27'),(722,120,'impression','desktop',NULL,'2026-03-01 17:25:32'),(723,102,'impression','desktop',NULL,'2026-03-01 17:25:38'),(724,106,'impression','desktop',NULL,'2026-03-01 17:25:42'),(725,120,'impression','desktop',NULL,'2026-03-01 17:25:47'),(726,102,'impression','desktop',NULL,'2026-03-01 17:25:52'),(727,106,'impression','desktop',NULL,'2026-03-01 17:25:57'),(728,120,'impression','desktop',NULL,'2026-03-01 17:26:02'),(729,102,'impression','desktop',NULL,'2026-03-01 17:26:07'),(730,106,'impression','desktop',NULL,'2026-03-01 17:26:12'),(731,120,'impression','desktop',NULL,'2026-03-01 17:26:17'),(732,102,'impression','desktop',NULL,'2026-03-01 17:26:23'),(733,106,'impression','desktop',NULL,'2026-03-01 17:26:28'),(734,120,'impression','desktop',NULL,'2026-03-01 17:26:33'),(735,102,'impression','desktop',NULL,'2026-03-01 17:26:38'),(736,106,'impression','desktop',NULL,'2026-03-01 17:26:43'),(737,120,'impression','desktop',NULL,'2026-03-01 17:26:48'),(738,102,'impression','desktop',NULL,'2026-03-01 17:26:53'),(739,106,'impression','desktop',NULL,'2026-03-01 17:26:58'),(740,120,'impression','desktop',NULL,'2026-03-01 17:27:03'),(741,102,'impression','desktop',NULL,'2026-03-01 17:27:08'),(742,106,'impression','desktop',NULL,'2026-03-01 17:27:13'),(743,120,'impression','desktop',NULL,'2026-03-01 17:27:18'),(744,102,'impression','desktop',NULL,'2026-03-01 17:27:23'),(745,106,'impression','desktop',NULL,'2026-03-01 17:27:28'),(746,120,'impression','desktop',NULL,'2026-03-01 17:27:33'),(747,102,'impression','desktop',NULL,'2026-03-01 17:27:38'),(748,106,'impression','desktop',NULL,'2026-03-01 17:27:43'),(749,120,'impression','desktop',NULL,'2026-03-01 17:27:48'),(750,102,'impression','desktop',NULL,'2026-03-01 17:27:53'),(751,106,'impression','desktop',NULL,'2026-03-01 17:27:58'),(752,120,'impression','desktop',NULL,'2026-03-01 17:28:03'),(753,102,'impression','desktop',NULL,'2026-03-01 17:28:08'),(754,106,'impression','desktop',NULL,'2026-03-01 17:28:13'),(755,120,'impression','desktop',NULL,'2026-03-01 17:28:18'),(756,102,'impression','desktop',NULL,'2026-03-01 17:28:23'),(757,106,'impression','desktop',NULL,'2026-03-01 17:28:28'),(758,120,'impression','desktop',NULL,'2026-03-01 17:28:33'),(759,102,'impression','desktop',NULL,'2026-03-01 17:28:38'),(760,106,'impression','desktop',NULL,'2026-03-01 17:28:43'),(761,120,'impression','desktop',NULL,'2026-03-01 17:28:48'),(762,102,'impression','desktop',NULL,'2026-03-01 17:28:53'),(763,106,'impression','desktop',NULL,'2026-03-01 17:28:58'),(764,120,'impression','desktop',NULL,'2026-03-01 17:29:03'),(765,102,'impression','desktop',NULL,'2026-03-01 17:29:08'),(766,106,'impression','desktop',NULL,'2026-03-01 17:29:13'),(767,120,'impression','desktop',NULL,'2026-03-01 17:29:18'),(768,102,'impression','desktop',NULL,'2026-03-01 17:29:23'),(769,106,'impression','desktop',NULL,'2026-03-01 17:29:28'),(770,120,'impression','desktop',NULL,'2026-03-01 17:29:33'),(771,102,'impression','desktop',NULL,'2026-03-01 17:29:38'),(772,106,'impression','desktop',NULL,'2026-03-01 17:29:43'),(773,120,'impression','desktop',NULL,'2026-03-01 17:29:48'),(774,102,'impression','desktop',NULL,'2026-03-01 17:29:53'),(775,106,'impression','desktop',NULL,'2026-03-01 17:29:58'),(776,120,'impression','desktop',NULL,'2026-03-01 17:30:03'),(777,102,'impression','desktop',NULL,'2026-03-01 17:30:08'),(778,106,'impression','desktop',NULL,'2026-03-01 17:30:13'),(779,120,'impression','desktop',NULL,'2026-03-01 17:30:18'),(780,102,'impression','desktop',NULL,'2026-03-01 17:30:23'),(781,106,'impression','desktop',NULL,'2026-03-01 17:30:28'),(782,120,'impression','desktop',NULL,'2026-03-01 17:30:33'),(783,102,'impression','desktop',NULL,'2026-03-01 17:30:38'),(784,106,'impression','desktop',NULL,'2026-03-01 17:30:43'),(785,120,'impression','desktop',NULL,'2026-03-01 17:30:48'),(786,102,'impression','desktop',NULL,'2026-03-01 17:30:53'),(787,106,'impression','desktop',NULL,'2026-03-01 17:30:58'),(788,120,'impression','desktop',NULL,'2026-03-01 17:31:03'),(789,102,'impression','desktop',NULL,'2026-03-01 17:31:08'),(790,106,'impression','desktop',NULL,'2026-03-01 17:31:13'),(791,120,'impression','desktop',NULL,'2026-03-01 17:31:18'),(792,102,'impression','desktop',NULL,'2026-03-01 17:31:23'),(793,106,'impression','desktop',NULL,'2026-03-01 17:31:28'),(794,120,'impression','desktop',NULL,'2026-03-01 17:31:33'),(795,102,'impression','desktop',NULL,'2026-03-01 17:31:38'),(796,106,'impression','desktop',NULL,'2026-03-01 17:31:43'),(797,120,'impression','desktop',NULL,'2026-03-01 17:31:48'),(798,102,'impression','desktop',NULL,'2026-03-01 17:31:53'),(799,106,'impression','desktop',NULL,'2026-03-01 17:31:58'),(800,120,'impression','desktop',NULL,'2026-03-01 17:32:03'),(801,102,'impression','desktop',NULL,'2026-03-01 17:32:08'),(802,106,'impression','desktop',NULL,'2026-03-01 17:32:13'),(803,120,'impression','desktop',NULL,'2026-03-01 17:32:18'),(804,102,'impression','desktop',NULL,'2026-03-01 17:32:23'),(805,106,'impression','desktop',NULL,'2026-03-01 17:32:28'),(806,120,'impression','desktop',NULL,'2026-03-01 17:32:33'),(807,102,'impression','mobile',NULL,'2026-03-01 17:32:35'),(808,102,'impression','desktop',NULL,'2026-03-01 17:32:38'),(809,106,'impression','mobile',NULL,'2026-03-01 17:32:40'),(810,106,'impression','desktop',NULL,'2026-03-01 17:32:43'),(811,120,'impression','mobile',NULL,'2026-03-01 17:32:45'),(812,120,'impression','desktop',NULL,'2026-03-01 17:32:48'),(813,102,'impression','mobile',NULL,'2026-03-01 17:32:50'),(814,102,'impression','desktop',NULL,'2026-03-01 17:32:53'),(815,106,'impression','mobile',NULL,'2026-03-01 17:32:55'),(816,106,'impression','desktop',NULL,'2026-03-01 17:32:58'),(817,120,'impression','mobile',NULL,'2026-03-01 17:33:00'),(818,120,'impression','desktop',NULL,'2026-03-01 17:33:03'),(819,102,'impression','mobile',NULL,'2026-03-01 17:33:05'),(820,102,'impression','desktop',NULL,'2026-03-01 17:33:08'),(821,106,'impression','mobile',NULL,'2026-03-01 17:33:10'),(822,106,'impression','desktop',NULL,'2026-03-01 17:33:13'),(823,120,'impression','mobile',NULL,'2026-03-01 17:33:15'),(824,120,'impression','desktop',NULL,'2026-03-01 17:33:18'),(825,102,'impression','mobile',NULL,'2026-03-01 17:33:20'),(826,102,'impression','desktop',NULL,'2026-03-01 17:33:23'),(827,106,'impression','mobile',NULL,'2026-03-01 17:33:25'),(828,106,'impression','desktop',NULL,'2026-03-01 17:33:28'),(829,120,'impression','mobile',NULL,'2026-03-01 17:33:30'),(830,102,'impression','mobile',NULL,'2026-03-01 17:33:35'),(831,106,'impression','mobile',NULL,'2026-03-01 17:33:40'),(832,120,'impression','mobile',NULL,'2026-03-01 17:33:45'),(833,102,'impression','mobile',NULL,'2026-03-01 17:33:50'),(834,106,'impression','mobile',NULL,'2026-03-01 17:33:55'),(835,120,'impression','mobile',NULL,'2026-03-01 17:34:00'),(836,102,'impression','mobile',NULL,'2026-03-01 17:34:05'),(837,106,'impression','mobile',NULL,'2026-03-01 17:34:10'),(838,120,'impression','mobile',NULL,'2026-03-01 17:34:15'),(839,102,'impression','mobile',NULL,'2026-03-01 17:34:20'),(840,106,'impression','mobile',NULL,'2026-03-01 17:34:25'),(841,120,'impression','mobile',NULL,'2026-03-01 17:34:30'),(842,102,'impression','mobile',NULL,'2026-03-01 17:34:35'),(843,106,'impression','mobile',NULL,'2026-03-01 17:34:40'),(844,120,'impression','mobile',NULL,'2026-03-01 17:34:45'),(845,102,'impression','mobile',NULL,'2026-03-01 17:34:50'),(846,106,'impression','mobile',NULL,'2026-03-01 17:34:55'),(847,120,'impression','mobile',NULL,'2026-03-01 17:35:00'),(848,102,'impression','mobile',NULL,'2026-03-01 17:35:05'),(849,106,'impression','mobile',NULL,'2026-03-01 17:35:11'),(850,120,'impression','mobile',NULL,'2026-03-01 17:35:15'),(851,102,'impression','mobile',NULL,'2026-03-01 17:35:21'),(852,106,'impression','mobile',NULL,'2026-03-01 17:35:25'),(853,120,'impression','mobile',NULL,'2026-03-01 17:35:30'),(854,102,'impression','mobile',NULL,'2026-03-01 17:35:36'),(855,106,'impression','mobile',NULL,'2026-03-01 17:35:40'),(856,120,'impression','mobile',NULL,'2026-03-01 17:35:46'),(857,102,'impression','mobile',NULL,'2026-03-01 17:35:50'),(858,106,'impression','mobile',NULL,'2026-03-01 17:35:56'),(859,120,'impression','mobile',NULL,'2026-03-01 17:36:01'),(860,102,'impression','mobile',NULL,'2026-03-01 17:36:06'),(861,106,'impression','mobile',NULL,'2026-03-01 17:36:11'),(862,120,'impression','mobile',NULL,'2026-03-01 17:36:16'),(863,102,'impression','mobile',NULL,'2026-03-01 17:36:21'),(864,106,'impression','mobile',NULL,'2026-03-01 17:36:26'),(865,120,'impression','mobile',NULL,'2026-03-01 17:36:31'),(866,102,'impression','mobile',NULL,'2026-03-01 17:36:36'),(867,102,'impression','desktop',NULL,'2026-03-01 17:36:54'),(868,106,'impression','desktop',NULL,'2026-03-01 17:36:59'),(869,120,'impression','desktop',NULL,'2026-03-01 17:37:02'),(870,120,'impression','desktop',NULL,'2026-03-01 17:37:04'),(871,102,'impression','desktop',NULL,'2026-03-01 17:37:12'),(872,106,'impression','desktop',NULL,'2026-03-01 17:37:17'),(873,120,'impression','desktop',NULL,'2026-03-01 17:37:20'),(874,120,'impression','desktop',NULL,'2026-03-01 17:37:22'),(875,102,'impression','desktop',NULL,'2026-03-01 17:37:27'),(876,106,'impression','desktop',NULL,'2026-03-01 17:37:32'),(877,120,'impression','desktop',NULL,'2026-03-01 17:37:37'),(878,102,'impression','desktop',NULL,'2026-03-01 17:37:42'),(879,106,'impression','desktop',NULL,'2026-03-01 17:37:47'),(880,120,'impression','desktop',NULL,'2026-03-01 17:37:52'),(881,102,'impression','desktop',NULL,'2026-03-01 17:37:57'),(882,106,'impression','desktop',NULL,'2026-03-01 17:38:02'),(883,120,'impression','desktop',NULL,'2026-03-01 17:38:07'),(884,102,'impression','desktop',NULL,'2026-03-01 17:41:11'),(885,106,'impression','desktop',NULL,'2026-03-01 17:41:16'),(886,120,'impression','desktop',NULL,'2026-03-01 17:41:21'),(887,102,'impression','desktop',NULL,'2026-03-01 17:41:25'),(888,102,'impression','desktop',NULL,'2026-03-01 17:41:26'),(889,102,'impression','desktop',NULL,'2026-03-01 17:41:32'),(890,102,'impression','desktop',NULL,'2026-03-01 17:41:33'),(891,106,'impression','desktop',NULL,'2026-03-01 17:41:38'),(892,102,'impression','desktop',NULL,'2026-03-01 17:42:27'),(893,106,'impression','desktop',NULL,'2026-03-01 17:42:32'),(894,120,'impression','desktop',NULL,'2026-03-01 17:42:37'),(895,102,'impression','mobile',NULL,'2026-03-01 17:42:41'),(896,102,'impression','desktop',NULL,'2026-03-01 17:42:42'),(897,106,'impression','mobile',NULL,'2026-03-01 17:42:46'),(898,106,'impression','desktop',NULL,'2026-03-01 17:42:47'),(899,120,'impression','mobile',NULL,'2026-03-01 17:42:51'),(900,102,'impression','mobile',NULL,'2026-03-01 17:42:56'),(901,106,'impression','mobile',NULL,'2026-03-01 17:43:01'),(902,120,'impression','mobile',NULL,'2026-03-01 17:43:06'),(903,102,'impression','mobile',NULL,'2026-03-01 17:43:11'),(904,106,'impression','mobile',NULL,'2026-03-01 17:43:16'),(905,120,'impression','mobile',NULL,'2026-03-01 17:43:21'),(906,102,'impression','mobile',NULL,'2026-03-01 17:43:26'),(907,106,'impression','mobile',NULL,'2026-03-01 17:43:31'),(908,120,'impression','mobile',NULL,'2026-03-01 17:43:36'),(909,102,'impression','mobile',NULL,'2026-03-01 17:43:41'),(910,106,'impression','mobile',NULL,'2026-03-01 17:43:46'),(911,120,'impression','mobile',NULL,'2026-03-01 17:43:51'),(912,102,'impression','mobile',NULL,'2026-03-01 17:43:56'),(913,106,'impression','mobile',NULL,'2026-03-01 17:44:01'),(914,120,'impression','mobile',NULL,'2026-03-01 17:44:06'),(915,102,'impression','mobile',NULL,'2026-03-01 17:44:11'),(916,106,'impression','mobile',NULL,'2026-03-01 17:44:16'),(917,120,'impression','mobile',NULL,'2026-03-01 17:44:21'),(918,102,'impression','mobile',NULL,'2026-03-01 17:44:26'),(919,106,'impression','mobile',NULL,'2026-03-01 17:44:31'),(920,120,'impression','mobile',NULL,'2026-03-01 17:44:36'),(921,102,'impression','mobile',NULL,'2026-03-01 17:44:41'),(922,106,'impression','mobile',NULL,'2026-03-01 17:44:46'),(923,120,'impression','mobile',NULL,'2026-03-01 17:44:51'),(924,102,'impression','mobile',NULL,'2026-03-01 17:44:56'),(925,106,'impression','mobile',NULL,'2026-03-01 17:45:01'),(926,120,'impression','mobile',NULL,'2026-03-01 17:45:06'),(927,102,'impression','mobile',NULL,'2026-03-01 17:45:12'),(928,106,'impression','mobile',NULL,'2026-03-01 17:45:16'),(929,120,'impression','mobile',NULL,'2026-03-01 17:45:21'),(930,102,'impression','mobile',NULL,'2026-03-01 17:45:26'),(931,106,'impression','mobile',NULL,'2026-03-01 17:45:31'),(932,120,'impression','mobile',NULL,'2026-03-01 17:45:36'),(933,102,'impression','mobile',NULL,'2026-03-01 17:45:41'),(934,106,'impression','mobile',NULL,'2026-03-01 17:45:47'),(935,120,'impression','mobile',NULL,'2026-03-01 17:45:51'),(936,102,'impression','mobile',NULL,'2026-03-01 17:45:56'),(937,106,'impression','mobile',NULL,'2026-03-01 17:46:01'),(938,120,'impression','mobile',NULL,'2026-03-01 17:46:07'),(939,102,'impression','mobile',NULL,'2026-03-01 17:46:11'),(940,106,'impression','mobile',NULL,'2026-03-01 17:46:17'),(941,102,'impression','desktop',NULL,'2026-03-01 17:48:02'),(942,106,'impression','desktop',NULL,'2026-03-01 17:48:07'),(943,120,'impression','desktop',NULL,'2026-03-01 17:48:12'),(944,102,'impression','desktop',NULL,'2026-03-01 17:48:17'),(945,102,'impression','mobile',NULL,'2026-03-01 17:48:18'),(946,106,'impression','desktop',NULL,'2026-03-01 17:48:22'),(947,106,'impression','mobile',NULL,'2026-03-01 17:48:23'),(948,120,'impression','desktop',NULL,'2026-03-01 17:48:27'),(949,120,'impression','mobile',NULL,'2026-03-01 17:48:28'),(950,102,'impression','desktop',NULL,'2026-03-01 17:48:32'),(951,102,'impression','mobile',NULL,'2026-03-01 17:48:33'),(952,106,'impression','desktop',NULL,'2026-03-01 17:48:37'),(953,106,'impression','mobile',NULL,'2026-03-01 17:48:38'),(954,120,'impression','desktop',NULL,'2026-03-01 17:48:42'),(955,120,'impression','mobile',NULL,'2026-03-01 17:48:43'),(956,102,'impression','mobile',NULL,'2026-03-01 17:48:48'),(957,106,'impression','mobile',NULL,'2026-03-01 17:48:53'),(958,120,'impression','mobile',NULL,'2026-03-01 17:48:58'),(959,102,'impression','mobile',NULL,'2026-03-01 17:49:03'),(960,106,'impression','mobile',NULL,'2026-03-01 17:49:08'),(961,120,'impression','mobile',NULL,'2026-03-01 17:49:13'),(962,102,'impression','mobile',NULL,'2026-03-01 17:49:18'),(963,106,'impression','mobile',NULL,'2026-03-01 17:49:23'),(964,120,'impression','mobile',NULL,'2026-03-01 17:49:28'),(965,102,'impression','mobile',NULL,'2026-03-01 17:49:33'),(966,106,'impression','mobile',NULL,'2026-03-01 17:49:38'),(967,120,'impression','mobile',NULL,'2026-03-01 17:49:43'),(968,102,'impression','mobile',NULL,'2026-03-01 17:49:48'),(969,106,'impression','mobile',NULL,'2026-03-01 17:49:53'),(970,120,'impression','mobile',NULL,'2026-03-01 17:49:58'),(971,102,'impression','mobile',NULL,'2026-03-01 17:50:03'),(972,106,'impression','mobile',NULL,'2026-03-01 17:50:08'),(973,120,'impression','mobile',NULL,'2026-03-01 17:50:13'),(974,102,'impression','mobile',NULL,'2026-03-01 17:50:18'),(975,106,'impression','mobile',NULL,'2026-03-01 17:50:23'),(976,120,'impression','mobile',NULL,'2026-03-01 17:50:28'),(977,102,'impression','desktop',NULL,'2026-03-01 17:59:08'),(978,106,'impression','desktop',NULL,'2026-03-01 17:59:13'),(979,102,'impression','desktop',NULL,'2026-03-01 18:06:04'),(980,102,'impression','desktop',NULL,'2026-03-01 18:06:41'),(981,106,'impression','desktop',NULL,'2026-03-01 18:06:46'),(982,120,'impression','desktop',NULL,'2026-03-01 18:06:51'),(983,102,'impression','desktop',NULL,'2026-03-01 18:06:56'),(984,106,'impression','desktop',NULL,'2026-03-01 18:07:01'),(985,120,'impression','desktop',NULL,'2026-03-01 18:07:06'),(986,102,'impression','desktop',NULL,'2026-03-01 18:22:52'),(987,102,'impression','desktop',NULL,'2026-03-01 18:38:14'),(988,102,'impression','desktop',NULL,'2026-03-01 23:11:12'),(989,106,'impression','desktop',NULL,'2026-03-01 23:11:17'),(990,120,'impression','desktop',NULL,'2026-03-01 23:11:22'),(991,102,'impression','desktop',NULL,'2026-03-01 23:11:38'),(992,102,'impression','desktop',NULL,'2026-03-01 23:11:50'),(993,106,'impression','desktop',NULL,'2026-03-01 23:11:55'),(994,120,'impression','desktop',NULL,'2026-03-01 23:12:00'),(995,102,'impression','desktop',NULL,'2026-03-01 23:12:09'),(996,106,'impression','desktop',NULL,'2026-03-01 23:12:14'),(997,120,'impression','desktop',NULL,'2026-03-01 23:12:19'),(998,102,'impression','desktop',NULL,'2026-03-01 23:12:24'),(999,102,'impression','desktop',NULL,'2026-03-01 23:37:11'),(1000,102,'impression','desktop',NULL,'2026-03-01 23:37:18'),(1001,106,'impression','desktop',NULL,'2026-03-01 23:37:23'),(1002,120,'impression','desktop',NULL,'2026-03-01 23:37:28'),(1003,102,'impression','desktop',NULL,'2026-03-01 23:37:33'),(1004,106,'impression','desktop',NULL,'2026-03-01 23:37:38'),(1005,102,'impression','mobile',NULL,'2026-03-01 23:44:42'),(1006,106,'impression','mobile',NULL,'2026-03-01 23:44:47'),(1007,102,'impression','mobile',NULL,'2026-03-01 23:44:55'),(1008,106,'impression','mobile',NULL,'2026-03-01 23:45:00'),(1009,120,'impression','mobile',NULL,'2026-03-01 23:45:05'),(1010,102,'impression','mobile',NULL,'2026-03-01 23:45:10'),(1011,106,'impression','mobile',NULL,'2026-03-01 23:45:15'),(1012,120,'impression','mobile',NULL,'2026-03-01 23:45:20'),(1013,102,'impression','mobile',NULL,'2026-03-01 23:45:25'),(1014,106,'impression','mobile',NULL,'2026-03-01 23:45:30'),(1015,120,'impression','mobile',NULL,'2026-03-01 23:45:35'),(1016,102,'impression','mobile',NULL,'2026-03-01 23:45:40'),(1017,106,'impression','mobile',NULL,'2026-03-01 23:45:45'),(1018,120,'impression','mobile',NULL,'2026-03-01 23:45:50'),(1019,102,'impression','mobile',NULL,'2026-03-01 23:45:55'),(1020,106,'impression','mobile',NULL,'2026-03-01 23:46:00'),(1021,120,'impression','mobile',NULL,'2026-03-01 23:46:05'),(1022,102,'impression','mobile',NULL,'2026-03-01 23:46:10'),(1023,106,'impression','mobile',NULL,'2026-03-01 23:46:15'),(1024,120,'impression','mobile',NULL,'2026-03-01 23:46:20'),(1025,102,'impression','mobile',NULL,'2026-03-01 23:46:25'),(1026,106,'impression','mobile',NULL,'2026-03-01 23:46:30'),(1027,120,'impression','mobile',NULL,'2026-03-01 23:46:35'),(1028,102,'impression','mobile',NULL,'2026-03-01 23:46:40'),(1029,106,'impression','mobile',NULL,'2026-03-01 23:46:45'),(1030,120,'impression','mobile',NULL,'2026-03-01 23:46:50'),(1031,102,'impression','mobile',NULL,'2026-03-01 23:46:55'),(1032,102,'impression','mobile',NULL,'2026-03-01 23:55:55'),(1033,106,'impression','mobile',NULL,'2026-03-01 23:56:00'),(1034,120,'impression','mobile',NULL,'2026-03-01 23:56:05'),(1035,102,'impression','mobile',NULL,'2026-03-01 23:56:10'),(1036,106,'impression','mobile',NULL,'2026-03-01 23:56:15'),(1037,120,'impression','mobile',NULL,'2026-03-01 23:56:20'),(1038,102,'impression','mobile',NULL,'2026-03-01 23:56:25'),(1039,106,'impression','mobile',NULL,'2026-03-01 23:56:30'),(1040,120,'impression','mobile',NULL,'2026-03-01 23:56:35'),(1041,102,'impression','mobile',NULL,'2026-03-01 23:56:45'),(1042,106,'impression','mobile',NULL,'2026-03-01 23:56:50'),(1043,120,'impression','mobile',NULL,'2026-03-01 23:56:55'),(1044,102,'impression','mobile',NULL,'2026-03-01 23:57:00'),(1045,102,'impression','mobile',NULL,'2026-03-01 23:57:06'),(1046,106,'impression','mobile',NULL,'2026-03-01 23:57:11'),(1047,120,'impression','mobile',NULL,'2026-03-01 23:57:16'),(1048,102,'impression','mobile',NULL,'2026-03-01 23:57:21'),(1049,106,'impression','mobile',NULL,'2026-03-01 23:57:26'),(1050,120,'impression','mobile',NULL,'2026-03-01 23:57:31'),(1051,102,'impression','mobile',NULL,'2026-03-01 23:57:36'),(1052,106,'impression','mobile',NULL,'2026-03-01 23:57:42'),(1053,120,'impression','mobile',NULL,'2026-03-01 23:57:46'),(1054,102,'impression','mobile',NULL,'2026-03-01 23:57:51'),(1055,106,'impression','mobile',NULL,'2026-03-01 23:57:56'),(1056,120,'impression','mobile',NULL,'2026-03-01 23:58:01'),(1057,102,'impression','mobile',NULL,'2026-03-01 23:58:07'),(1058,106,'impression','mobile',NULL,'2026-03-01 23:58:11'),(1059,120,'impression','mobile',NULL,'2026-03-01 23:58:16'),(1060,102,'impression','mobile',NULL,'2026-03-01 23:58:22'),(1061,106,'impression','mobile',NULL,'2026-03-01 23:58:27'),(1062,120,'impression','mobile',NULL,'2026-03-01 23:58:31'),(1063,102,'impression','mobile',NULL,'2026-03-01 23:58:37'),(1064,106,'impression','mobile',NULL,'2026-03-01 23:58:42'),(1065,120,'impression','mobile',NULL,'2026-03-01 23:58:46'),(1066,102,'impression','mobile',NULL,'2026-03-01 23:58:52'),(1067,106,'impression','mobile',NULL,'2026-03-01 23:58:57'),(1068,102,'impression','desktop',NULL,'2026-03-02 11:49:27'),(1069,106,'impression','desktop',NULL,'2026-03-02 11:49:32'),(1070,102,'impression','desktop',NULL,'2026-03-02 11:49:42'),(1071,102,'impression','desktop',NULL,'2026-03-02 13:10:45'),(1072,106,'impression','desktop',NULL,'2026-03-02 13:10:50'),(1073,120,'impression','desktop',NULL,'2026-03-02 13:10:55'),(1074,102,'impression','desktop',NULL,'2026-03-02 16:40:01'),(1075,106,'impression','desktop',NULL,'2026-03-02 16:40:06'),(1076,120,'impression','desktop',NULL,'2026-03-02 16:40:11'),(1077,102,'impression','desktop',NULL,'2026-03-02 16:40:16'),(1078,106,'impression','desktop',NULL,'2026-03-02 16:40:21'),(1079,120,'impression','desktop',NULL,'2026-03-02 16:40:26'),(1080,102,'impression','desktop',NULL,'2026-03-02 16:50:22'),(1081,106,'impression','desktop',NULL,'2026-03-02 16:50:27'),(1082,120,'impression','desktop',NULL,'2026-03-02 16:50:32'),(1083,102,'impression','desktop',NULL,'2026-03-02 16:53:01'),(1084,106,'impression','desktop',NULL,'2026-03-02 16:53:06'),(1085,120,'impression','desktop',NULL,'2026-03-02 16:53:11'),(1086,102,'impression','desktop',NULL,'2026-03-02 16:53:16'),(1087,106,'impression','desktop',NULL,'2026-03-02 16:53:21'),(1088,120,'impression','desktop',NULL,'2026-03-02 16:53:26'),(1089,102,'impression','desktop',NULL,'2026-03-02 16:53:31'),(1090,102,'impression','desktop',NULL,'2026-03-02 16:59:36'),(1091,106,'impression','desktop',NULL,'2026-03-02 16:59:41'),(1092,102,'impression','desktop',NULL,'2026-03-02 16:59:57'),(1093,106,'impression','desktop',NULL,'2026-03-02 17:00:02'),(1094,120,'impression','desktop',NULL,'2026-03-02 17:00:07'),(1095,102,'impression','desktop',NULL,'2026-03-02 17:00:12'),(1096,106,'impression','desktop',NULL,'2026-03-02 17:00:17'),(1097,120,'impression','desktop',NULL,'2026-03-02 17:00:22'),(1098,102,'impression','desktop',NULL,'2026-03-02 17:00:27'),(1099,102,'impression','desktop',NULL,'2026-03-02 17:03:25'),(1100,106,'impression','desktop',NULL,'2026-03-02 17:03:30'),(1101,120,'impression','desktop',NULL,'2026-03-02 17:03:35'),(1102,102,'impression','desktop',NULL,'2026-03-02 17:03:40'),(1103,106,'impression','desktop',NULL,'2026-03-02 17:03:45'),(1104,120,'impression','desktop',NULL,'2026-03-02 17:03:50'),(1105,102,'impression','desktop',NULL,'2026-03-02 17:03:55'),(1106,106,'impression','desktop',NULL,'2026-03-02 17:04:00'),(1107,120,'impression','desktop',NULL,'2026-03-02 17:04:05'),(1108,102,'impression','desktop',NULL,'2026-03-02 17:08:03'),(1109,106,'impression','desktop',NULL,'2026-03-02 17:08:09'),(1110,120,'impression','desktop',NULL,'2026-03-02 17:08:14'),(1111,102,'impression','desktop',NULL,'2026-03-02 17:08:19'),(1112,102,'impression','desktop',NULL,'2026-03-02 17:11:40'),(1113,106,'impression','desktop',NULL,'2026-03-02 17:11:45'),(1114,120,'impression','desktop',NULL,'2026-03-02 17:11:50'),(1115,102,'impression','desktop',NULL,'2026-03-02 17:11:55'),(1116,106,'impression','desktop',NULL,'2026-03-02 17:12:00'),(1117,120,'impression','desktop',NULL,'2026-03-02 17:12:05'),(1118,102,'impression','desktop',NULL,'2026-03-02 17:12:10'),(1119,106,'impression','desktop',NULL,'2026-03-02 17:12:15'),(1120,120,'impression','desktop',NULL,'2026-03-02 17:12:20'),(1121,102,'impression','desktop',NULL,'2026-03-02 17:12:25'),(1122,106,'impression','desktop',NULL,'2026-03-02 17:12:30'),(1123,120,'impression','desktop',NULL,'2026-03-02 17:12:35'),(1124,102,'impression','desktop',NULL,'2026-03-02 17:12:44'),(1125,106,'impression','desktop',NULL,'2026-03-02 17:12:49'),(1126,120,'impression','desktop',NULL,'2026-03-02 17:12:54'),(1127,102,'impression','desktop',NULL,'2026-03-02 17:13:09'),(1128,102,'impression','desktop',NULL,'2026-03-02 17:15:58'),(1129,106,'impression','desktop',NULL,'2026-03-02 17:16:03'),(1130,120,'impression','desktop',NULL,'2026-03-02 17:16:08'),(1131,102,'impression','desktop',NULL,'2026-03-02 17:16:13'),(1132,106,'impression','desktop',NULL,'2026-03-02 17:16:18'),(1133,120,'impression','desktop',NULL,'2026-03-02 17:16:23'),(1134,102,'impression','desktop',NULL,'2026-03-02 17:16:28'),(1135,106,'impression','desktop',NULL,'2026-03-02 17:16:33'),(1136,120,'impression','desktop',NULL,'2026-03-02 17:16:38'),(1137,102,'impression','desktop',NULL,'2026-03-02 17:16:43'),(1138,106,'impression','desktop',NULL,'2026-03-02 17:16:48'),(1139,120,'impression','desktop',NULL,'2026-03-02 17:16:53'),(1140,102,'impression','desktop',NULL,'2026-03-02 17:18:29'),(1141,106,'impression','desktop',NULL,'2026-03-02 17:18:34'),(1142,120,'impression','desktop',NULL,'2026-03-02 17:18:39'),(1143,102,'impression','desktop',NULL,'2026-03-02 17:18:44'),(1144,102,'impression','desktop',NULL,'2026-03-02 17:22:06'),(1145,106,'impression','desktop',NULL,'2026-03-02 17:22:11'),(1146,120,'impression','desktop',NULL,'2026-03-02 17:22:16'),(1147,102,'impression','desktop',NULL,'2026-03-02 17:22:21'),(1148,106,'impression','desktop',NULL,'2026-03-02 17:22:26'),(1149,120,'impression','desktop',NULL,'2026-03-02 17:22:31'),(1150,102,'impression','desktop',NULL,'2026-03-02 17:22:36'),(1151,106,'impression','desktop',NULL,'2026-03-02 17:22:41'),(1152,102,'impression','desktop',NULL,'2026-03-02 17:27:13'),(1153,106,'impression','desktop',NULL,'2026-03-02 17:27:18'),(1154,120,'impression','desktop',NULL,'2026-03-02 17:27:23'),(1155,102,'impression','desktop',NULL,'2026-03-02 17:27:28'),(1156,106,'impression','desktop',NULL,'2026-03-02 17:27:33'),(1157,120,'impression','desktop',NULL,'2026-03-02 17:27:38'),(1158,102,'impression','desktop',NULL,'2026-03-02 17:27:43'),(1159,106,'impression','desktop',NULL,'2026-03-02 17:27:48'),(1160,120,'impression','desktop',NULL,'2026-03-02 17:27:53'),(1161,102,'impression','desktop',NULL,'2026-03-02 17:27:58'),(1162,106,'impression','desktop',NULL,'2026-03-02 17:28:03'),(1163,120,'impression','desktop',NULL,'2026-03-02 17:28:08'),(1164,102,'impression','desktop',NULL,'2026-03-02 17:28:13'),(1165,106,'impression','desktop',NULL,'2026-03-02 17:28:19'),(1166,120,'impression','desktop',NULL,'2026-03-02 17:28:23'),(1167,102,'impression','desktop',NULL,'2026-03-02 17:28:29'),(1168,106,'impression','desktop',NULL,'2026-03-02 17:28:34'),(1169,120,'impression','desktop',NULL,'2026-03-02 17:28:39'),(1170,102,'impression','desktop',NULL,'2026-03-02 17:28:44'),(1171,106,'impression','desktop',NULL,'2026-03-02 17:28:49'),(1172,120,'impression','desktop',NULL,'2026-03-02 17:28:54'),(1173,102,'impression','desktop',NULL,'2026-03-02 17:28:59'),(1174,106,'impression','desktop',NULL,'2026-03-02 17:29:04'),(1175,102,'impression','desktop',NULL,'2026-03-02 17:33:10'),(1176,106,'impression','desktop',NULL,'2026-03-02 17:33:15'),(1177,120,'impression','desktop',NULL,'2026-03-02 17:33:20'),(1178,102,'impression','desktop',NULL,'2026-03-02 17:33:25'),(1179,106,'impression','desktop',NULL,'2026-03-02 17:33:30'),(1180,120,'impression','desktop',NULL,'2026-03-02 17:33:35'),(1181,102,'impression','desktop',NULL,'2026-03-02 17:37:55'),(1182,102,'impression','desktop',NULL,'2026-03-02 17:40:47'),(1183,106,'impression','desktop',NULL,'2026-03-02 17:40:52'),(1184,120,'impression','desktop',NULL,'2026-03-02 17:40:57'),(1185,102,'impression','desktop',NULL,'2026-03-02 17:41:02'),(1186,106,'impression','desktop',NULL,'2026-03-02 17:41:07'),(1187,120,'impression','desktop',NULL,'2026-03-02 17:41:12'),(1188,102,'impression','desktop',NULL,'2026-03-02 17:41:17'),(1189,106,'impression','desktop',NULL,'2026-03-02 17:41:22'),(1190,120,'impression','desktop',NULL,'2026-03-02 17:41:27'),(1191,102,'impression','desktop',NULL,'2026-03-02 17:41:32'),(1192,106,'impression','desktop',NULL,'2026-03-02 17:41:37'),(1193,120,'impression','desktop',NULL,'2026-03-02 17:41:42'),(1194,102,'impression','desktop',NULL,'2026-03-02 17:41:47'),(1195,106,'impression','desktop',NULL,'2026-03-02 17:41:52'),(1196,120,'impression','desktop',NULL,'2026-03-02 17:41:57'),(1197,102,'impression','desktop',NULL,'2026-03-02 17:42:02'),(1198,106,'impression','desktop',NULL,'2026-03-02 17:42:07'),(1199,120,'impression','desktop',NULL,'2026-03-02 17:42:12'),(1200,102,'impression','desktop',NULL,'2026-03-02 17:42:17'),(1201,106,'impression','desktop',NULL,'2026-03-02 17:42:22'),(1202,102,'impression','desktop',NULL,'2026-03-02 17:42:28'),(1203,106,'impression','desktop',NULL,'2026-03-02 17:42:33'),(1204,120,'impression','desktop',NULL,'2026-03-02 17:42:38'),(1205,102,'impression','desktop',NULL,'2026-03-02 17:42:43'),(1206,106,'impression','desktop',NULL,'2026-03-02 17:42:48'),(1207,120,'impression','desktop',NULL,'2026-03-02 17:42:53'),(1208,102,'impression','desktop',NULL,'2026-03-02 17:42:58'),(1209,106,'impression','desktop',NULL,'2026-03-02 17:43:03'),(1210,102,'impression','desktop',NULL,'2026-03-02 17:46:00'),(1211,106,'impression','desktop',NULL,'2026-03-02 17:46:05'),(1212,102,'impression','desktop',NULL,'2026-03-02 17:48:56'),(1213,106,'impression','desktop',NULL,'2026-03-02 17:49:01'),(1214,120,'impression','desktop',NULL,'2026-03-02 17:49:06'),(1215,102,'impression','desktop',NULL,'2026-03-02 17:49:11'),(1216,102,'impression','desktop',NULL,'2026-03-02 19:00:12'),(1217,106,'impression','desktop',NULL,'2026-03-02 19:00:17'),(1218,120,'impression','desktop',NULL,'2026-03-02 19:00:22'),(1219,102,'impression','desktop',NULL,'2026-03-02 19:00:27'),(1220,106,'impression','desktop',NULL,'2026-03-02 19:00:32'),(1221,120,'impression','desktop',NULL,'2026-03-02 19:00:37'),(1222,102,'impression','desktop',NULL,'2026-03-02 19:00:42'),(1223,106,'impression','desktop',NULL,'2026-03-02 19:00:47'),(1224,120,'impression','desktop',NULL,'2026-03-02 19:00:52'),(1225,102,'impression','desktop',NULL,'2026-03-02 19:00:57'),(1226,106,'impression','desktop',NULL,'2026-03-02 19:01:02'),(1227,120,'impression','desktop',NULL,'2026-03-02 19:01:07'),(1228,102,'impression','desktop',NULL,'2026-03-02 19:01:12'),(1229,106,'impression','desktop',NULL,'2026-03-02 19:01:17'),(1230,120,'impression','desktop',NULL,'2026-03-02 19:01:22'),(1231,102,'impression','desktop',NULL,'2026-03-02 19:01:27'),(1232,106,'impression','desktop',NULL,'2026-03-02 19:01:32'),(1233,120,'impression','desktop',NULL,'2026-03-02 19:01:37'),(1234,102,'impression','desktop',NULL,'2026-03-02 19:01:42'),(1235,106,'impression','desktop',NULL,'2026-03-02 19:01:47'),(1236,120,'impression','desktop',NULL,'2026-03-02 19:01:52'),(1237,102,'impression','desktop',NULL,'2026-03-02 19:01:57'),(1238,106,'impression','desktop',NULL,'2026-03-02 19:02:02'),(1239,120,'impression','desktop',NULL,'2026-03-02 19:02:07'),(1240,102,'impression','desktop',NULL,'2026-03-02 19:02:12'),(1241,106,'impression','desktop',NULL,'2026-03-02 19:02:17'),(1242,102,'impression','desktop',NULL,'2026-03-02 19:07:44'),(1243,106,'impression','desktop',NULL,'2026-03-02 19:07:49'),(1244,120,'impression','desktop',NULL,'2026-03-02 19:07:54'),(1245,102,'impression','desktop',NULL,'2026-03-02 19:07:59'),(1246,102,'impression','desktop',NULL,'2026-03-02 19:08:06'),(1247,106,'impression','desktop',NULL,'2026-03-02 19:08:11'),(1248,102,'impression','desktop',NULL,'2026-03-02 19:08:16'),(1249,102,'impression','desktop',NULL,'2026-03-02 19:08:37'),(1250,106,'impression','desktop',NULL,'2026-03-02 19:08:42'),(1251,120,'impression','desktop',NULL,'2026-03-02 19:08:47'),(1252,102,'impression','desktop',NULL,'2026-03-02 19:08:52'),(1253,106,'impression','desktop',NULL,'2026-03-02 19:08:57'),(1254,120,'impression','desktop',NULL,'2026-03-02 19:09:02'),(1255,102,'impression','desktop',NULL,'2026-03-02 19:09:07'),(1256,106,'impression','desktop',NULL,'2026-03-02 19:09:12'),(1257,120,'impression','desktop',NULL,'2026-03-02 19:09:17'),(1258,102,'impression','desktop',NULL,'2026-03-02 19:09:22'),(1259,102,'impression','desktop',NULL,'2026-03-03 14:29:36'),(1260,106,'impression','desktop',NULL,'2026-03-03 14:29:41'),(1261,120,'impression','desktop',NULL,'2026-03-03 14:29:46'),(1262,102,'impression','desktop',NULL,'2026-03-03 14:29:51'),(1263,106,'impression','desktop',NULL,'2026-03-03 14:29:56'),(1264,120,'impression','desktop',NULL,'2026-03-03 14:30:01'),(1265,102,'impression','desktop',NULL,'2026-03-03 14:30:06'),(1266,106,'impression','desktop',NULL,'2026-03-03 14:30:11'),(1267,120,'impression','desktop',NULL,'2026-03-03 14:30:16'),(1268,102,'impression','desktop',NULL,'2026-03-03 14:30:21'),(1269,106,'impression','desktop',NULL,'2026-03-03 14:30:26'),(1270,120,'impression','desktop',NULL,'2026-03-03 14:30:31'),(1271,102,'impression','desktop',NULL,'2026-03-03 14:30:36'),(1272,106,'impression','desktop',NULL,'2026-03-03 14:30:41'),(1273,120,'impression','desktop',NULL,'2026-03-03 14:30:46'),(1274,102,'impression','desktop',NULL,'2026-03-03 14:30:51'),(1275,106,'impression','desktop',NULL,'2026-03-03 14:30:56'),(1276,120,'impression','desktop',NULL,'2026-03-03 14:31:01'),(1277,102,'impression','desktop',NULL,'2026-03-03 14:31:06'),(1278,106,'impression','desktop',NULL,'2026-03-03 14:31:11'),(1279,120,'impression','desktop',NULL,'2026-03-03 14:31:16'),(1280,102,'impression','desktop',NULL,'2026-03-03 14:31:21'),(1281,106,'impression','desktop',NULL,'2026-03-03 14:31:26'),(1282,120,'impression','desktop',NULL,'2026-03-03 14:31:31'),(1283,102,'impression','desktop',NULL,'2026-03-03 14:31:36'),(1284,106,'impression','desktop',NULL,'2026-03-03 14:31:41'),(1285,120,'impression','desktop',NULL,'2026-03-03 14:31:46'),(1286,102,'impression','desktop',NULL,'2026-03-03 14:31:51'),(1287,106,'impression','desktop',NULL,'2026-03-03 14:31:56'),(1288,120,'impression','desktop',NULL,'2026-03-03 14:32:01'),(1289,102,'impression','desktop',NULL,'2026-03-03 14:32:06'),(1290,106,'impression','desktop',NULL,'2026-03-03 14:32:11'),(1291,120,'impression','desktop',NULL,'2026-03-03 14:32:16'),(1292,102,'impression','desktop',NULL,'2026-03-03 14:32:22'),(1293,106,'impression','desktop',NULL,'2026-03-03 14:32:27'),(1294,120,'impression','desktop',NULL,'2026-03-03 14:32:32'),(1295,102,'impression','desktop',NULL,'2026-03-03 14:32:37'),(1296,106,'impression','desktop',NULL,'2026-03-03 14:32:42'),(1297,120,'impression','desktop',NULL,'2026-03-03 14:32:47'),(1298,102,'impression','desktop',NULL,'2026-03-03 14:32:52'),(1299,106,'impression','desktop',NULL,'2026-03-03 14:32:57'),(1300,120,'impression','desktop',NULL,'2026-03-03 14:33:02'),(1301,102,'impression','desktop',NULL,'2026-03-03 14:33:07'),(1302,102,'impression','desktop',NULL,'2026-03-03 15:27:04'),(1303,106,'impression','desktop',NULL,'2026-03-03 15:27:09'),(1304,102,'impression','desktop',NULL,'2026-03-03 15:47:33'),(1305,106,'impression','desktop',NULL,'2026-03-03 15:47:38'),(1306,120,'impression','desktop',NULL,'2026-03-03 15:47:43'),(1307,102,'impression','desktop',NULL,'2026-03-03 15:49:39'),(1308,106,'impression','desktop',NULL,'2026-03-03 15:49:44'),(1309,102,'impression','desktop',NULL,'2026-03-04 15:29:23'),(1310,102,'impression','desktop',NULL,'2026-03-04 15:30:53'),(1311,102,'impression','desktop',NULL,'2026-03-04 15:39:41'),(1312,106,'impression','desktop',NULL,'2026-03-04 15:39:46'),(1313,120,'impression','desktop',NULL,'2026-03-04 15:39:51'),(1314,102,'impression','desktop',NULL,'2026-03-04 15:39:56'),(1315,106,'impression','desktop',NULL,'2026-03-04 15:40:01'),(1316,120,'impression','desktop',NULL,'2026-03-04 15:40:06'),(1317,102,'impression','desktop',NULL,'2026-03-04 15:40:11'),(1318,106,'impression','desktop',NULL,'2026-03-04 15:40:16'),(1319,120,'impression','desktop',NULL,'2026-03-04 15:40:21'),(1320,102,'impression','desktop',NULL,'2026-03-04 15:40:26'),(1321,106,'impression','desktop',NULL,'2026-03-04 15:40:31'),(1322,120,'impression','desktop',NULL,'2026-03-04 15:40:36'),(1323,102,'impression','desktop',NULL,'2026-03-04 15:40:41'),(1324,106,'impression','desktop',NULL,'2026-03-04 15:40:46'),(1325,120,'impression','desktop',NULL,'2026-03-04 15:40:51'),(1326,102,'impression','desktop',NULL,'2026-03-04 15:40:56'),(1327,106,'impression','desktop',NULL,'2026-03-04 15:41:01'),(1328,120,'impression','desktop',NULL,'2026-03-04 15:41:06'),(1329,102,'impression','desktop',NULL,'2026-03-04 15:41:11'),(1330,106,'impression','desktop',NULL,'2026-03-04 15:41:16'),(1331,120,'impression','desktop',NULL,'2026-03-04 15:41:21'),(1332,102,'impression','desktop',NULL,'2026-03-04 15:41:26'),(1333,106,'impression','desktop',NULL,'2026-03-04 15:41:31'),(1334,120,'impression','desktop',NULL,'2026-03-04 15:41:36'),(1335,102,'impression','desktop',NULL,'2026-03-04 15:41:41'),(1336,106,'impression','desktop',NULL,'2026-03-04 15:41:46'),(1337,102,'impression','desktop',NULL,'2026-03-04 15:42:03'),(1338,106,'impression','desktop',NULL,'2026-03-04 15:42:08'),(1339,120,'impression','desktop',NULL,'2026-03-04 15:42:13'),(1340,102,'impression','desktop',NULL,'2026-03-04 15:42:18'),(1341,106,'impression','desktop',NULL,'2026-03-04 15:42:23'),(1342,120,'impression','desktop',NULL,'2026-03-04 15:42:29'),(1343,102,'impression','desktop',NULL,'2026-03-04 15:42:34'),(1344,106,'impression','desktop',NULL,'2026-03-04 15:42:39'),(1345,120,'impression','desktop',NULL,'2026-03-04 15:42:44'),(1346,102,'impression','desktop',NULL,'2026-03-04 15:42:49'),(1347,106,'impression','desktop',NULL,'2026-03-04 15:42:54'),(1348,120,'impression','desktop',NULL,'2026-03-04 15:42:59'),(1349,102,'impression','desktop',NULL,'2026-03-04 15:43:04'),(1350,106,'impression','desktop',NULL,'2026-03-04 15:43:09'),(1351,120,'impression','desktop',NULL,'2026-03-04 15:43:14'),(1352,102,'impression','desktop',NULL,'2026-03-04 15:43:19'),(1353,106,'impression','desktop',NULL,'2026-03-04 15:43:24'),(1354,120,'impression','desktop',NULL,'2026-03-04 15:43:29'),(1355,102,'impression','desktop',NULL,'2026-03-04 15:43:34'),(1356,106,'impression','desktop',NULL,'2026-03-04 15:43:39'),(1357,120,'impression','desktop',NULL,'2026-03-04 15:43:44'),(1358,102,'impression','desktop',NULL,'2026-03-04 15:43:49'),(1359,106,'impression','desktop',NULL,'2026-03-04 15:43:54'),(1360,120,'impression','desktop',NULL,'2026-03-04 15:43:59'),(1361,102,'impression','desktop',NULL,'2026-03-04 15:44:04'),(1362,106,'impression','desktop',NULL,'2026-03-04 15:44:09'),(1363,120,'impression','desktop',NULL,'2026-03-04 15:44:14'),(1364,102,'impression','desktop',NULL,'2026-03-04 15:44:19'),(1365,106,'impression','desktop',NULL,'2026-03-04 15:44:24'),(1366,120,'impression','desktop',NULL,'2026-03-04 15:44:29'),(1367,102,'impression','desktop',NULL,'2026-03-04 15:44:34'),(1368,102,'impression','desktop',NULL,'2026-03-04 15:47:24'),(1369,106,'impression','desktop',NULL,'2026-03-04 15:47:29'),(1370,120,'impression','desktop',NULL,'2026-03-04 15:47:34'),(1371,102,'impression','desktop',NULL,'2026-03-04 15:50:46'),(1372,106,'impression','desktop',NULL,'2026-03-04 15:50:51'),(1373,120,'impression','desktop',NULL,'2026-03-04 15:50:56'),(1374,102,'impression','desktop',NULL,'2026-03-04 15:51:01'),(1375,106,'impression','desktop',NULL,'2026-03-04 15:51:06'),(1376,120,'impression','desktop',NULL,'2026-03-04 15:51:11'),(1377,102,'impression','desktop',NULL,'2026-03-04 15:55:39'),(1378,102,'impression','desktop',NULL,'2026-03-04 15:56:07'),(1379,106,'impression','desktop',NULL,'2026-03-04 15:56:12'),(1380,120,'impression','desktop',NULL,'2026-03-04 15:56:17'),(1381,102,'impression','desktop',NULL,'2026-03-04 15:56:22'),(1382,102,'impression','desktop',NULL,'2026-03-04 16:04:13'),(1383,102,'impression','desktop',NULL,'2026-03-04 16:04:33'),(1384,106,'impression','desktop',NULL,'2026-03-04 16:04:38'),(1385,120,'impression','desktop',NULL,'2026-03-04 16:04:43'),(1386,102,'impression','desktop',NULL,'2026-03-04 16:04:48'),(1387,106,'impression','desktop',NULL,'2026-03-04 16:04:53'),(1388,120,'impression','desktop',NULL,'2026-03-04 16:04:58'),(1389,102,'impression','desktop',NULL,'2026-03-04 16:05:03'),(1390,102,'impression','desktop',NULL,'2026-03-04 16:11:15'),(1391,106,'impression','desktop',NULL,'2026-03-04 16:11:20'),(1392,120,'impression','desktop',NULL,'2026-03-04 16:11:25'),(1393,102,'impression','desktop',NULL,'2026-03-04 16:11:30'),(1394,106,'impression','desktop',NULL,'2026-03-04 16:11:35'),(1395,120,'impression','desktop',NULL,'2026-03-04 16:11:40'),(1396,102,'impression','desktop',NULL,'2026-03-04 16:11:45'),(1397,106,'impression','desktop',NULL,'2026-03-04 16:11:50'),(1398,120,'impression','desktop',NULL,'2026-03-04 16:11:55'),(1399,102,'impression','desktop',NULL,'2026-03-04 16:12:00'),(1400,106,'impression','desktop',NULL,'2026-03-04 16:12:05'),(1401,120,'impression','desktop',NULL,'2026-03-04 16:12:10'),(1402,102,'impression','desktop',NULL,'2026-03-04 16:12:15'),(1403,106,'impression','desktop',NULL,'2026-03-04 16:12:20'),(1404,120,'impression','desktop',NULL,'2026-03-04 16:12:25'),(1405,102,'impression','desktop',NULL,'2026-03-04 16:12:30'),(1406,106,'impression','desktop',NULL,'2026-03-04 16:12:35'),(1407,120,'impression','desktop',NULL,'2026-03-04 16:12:40'),(1408,102,'impression','desktop',NULL,'2026-03-04 16:12:45'),(1409,106,'impression','desktop',NULL,'2026-03-04 16:12:50'),(1410,120,'impression','desktop',NULL,'2026-03-04 16:12:55'),(1411,102,'impression','desktop',NULL,'2026-03-04 16:13:00'),(1412,106,'impression','desktop',NULL,'2026-03-04 16:13:05'),(1413,120,'impression','desktop',NULL,'2026-03-04 16:13:10'),(1414,102,'impression','desktop',NULL,'2026-03-04 16:13:15'),(1415,106,'impression','desktop',NULL,'2026-03-04 16:13:20'),(1416,120,'impression','desktop',NULL,'2026-03-04 16:13:25'),(1417,102,'impression','desktop',NULL,'2026-03-04 16:13:30'),(1418,106,'impression','desktop',NULL,'2026-03-04 16:13:35'),(1419,102,'impression','desktop',NULL,'2026-03-04 16:19:12'),(1420,106,'impression','desktop',NULL,'2026-03-04 16:19:17'),(1421,120,'impression','desktop',NULL,'2026-03-04 16:19:22'),(1422,102,'impression','desktop',NULL,'2026-03-04 16:19:27'),(1423,106,'impression','desktop',NULL,'2026-03-04 16:19:32'),(1424,120,'impression','desktop',NULL,'2026-03-04 16:19:37'),(1425,102,'impression','desktop',NULL,'2026-03-04 16:19:42'),(1426,106,'impression','desktop',NULL,'2026-03-04 16:19:47'),(1427,120,'impression','desktop',NULL,'2026-03-04 16:19:52'),(1428,102,'impression','desktop',NULL,'2026-03-04 16:19:57'),(1429,106,'impression','desktop',NULL,'2026-03-04 16:20:02'),(1430,120,'impression','desktop',NULL,'2026-03-04 16:20:07'),(1431,102,'impression','desktop',NULL,'2026-03-04 16:20:12'),(1432,102,'impression','desktop',NULL,'2026-03-04 16:26:55'),(1433,106,'impression','desktop',NULL,'2026-03-04 16:27:00'),(1434,120,'impression','desktop',NULL,'2026-03-04 16:27:05'),(1435,102,'impression','desktop',NULL,'2026-03-04 16:27:10'),(1436,106,'impression','desktop',NULL,'2026-03-04 16:27:15'),(1437,120,'impression','desktop',NULL,'2026-03-04 16:27:20'),(1438,102,'impression','desktop',NULL,'2026-03-04 16:27:25'),(1439,106,'impression','desktop',NULL,'2026-03-04 16:27:30'),(1440,102,'impression','desktop',NULL,'2026-03-04 16:31:15'),(1441,106,'impression','desktop',NULL,'2026-03-04 16:31:20'),(1442,120,'impression','desktop',NULL,'2026-03-04 16:31:25'),(1443,102,'impression','desktop',NULL,'2026-03-04 16:31:30'),(1444,106,'impression','desktop',NULL,'2026-03-04 16:31:35'),(1445,120,'impression','desktop',NULL,'2026-03-04 16:31:40'),(1446,102,'impression','desktop',NULL,'2026-03-04 16:31:45'),(1447,106,'impression','desktop',NULL,'2026-03-04 16:31:50'),(1448,120,'impression','desktop',NULL,'2026-03-04 16:31:55'),(1449,102,'impression','desktop',NULL,'2026-03-04 16:32:00'),(1450,102,'impression','desktop',NULL,'2026-03-04 16:37:12'),(1451,106,'impression','desktop',NULL,'2026-03-04 16:37:17'),(1452,120,'impression','desktop',NULL,'2026-03-04 16:37:22'),(1453,121,'impression','desktop',NULL,'2026-03-06 05:32:09'),(1454,122,'impression','desktop',NULL,'2026-03-06 05:32:14'),(1455,121,'impression','desktop',NULL,'2026-03-06 05:32:48'),(1456,121,'impression','desktop',NULL,'2026-03-06 05:39:35'),(1457,121,'impression','desktop',NULL,'2026-03-06 05:39:51'),(1458,121,'impression','desktop',NULL,'2026-03-06 05:42:39');
/*!40000 ALTER TABLE `banner_analytics` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `brands`
--

DROP TABLE IF EXISTS `brands`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `brands` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `featured` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `status` tinyint(1) unsigned NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `brands`
--

LOCK TABLES `brands` WRITE;
/*!40000 ALTER TABLE `brands` DISABLE KEYS */;
INSERT INTO `brands` VALUES (1,'WinTerSMM','68e9423035ff61760117296.png',0,1,'2025-10-10 11:28:16','2025-10-10 11:28:16'),(2,'টি-শার্ট','69a5cc95d05161772473493.jpg',0,1,'2026-03-02 11:44:53','2026-03-02 11:44:53'),(3,'TS Opu','69a5ccba4c98c1772473530.png',0,1,'2026-03-02 11:45:30','2026-03-02 11:45:30'),(4,'RI','69a5cccc97fc31772473548.png',0,1,'2026-03-02 11:45:48','2026-03-02 11:45:48');
/*!40000 ALTER TABLE `brands` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `business_settings`
--

DROP TABLE IF EXISTS `business_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `business_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(30) NOT NULL,
  `value` longtext DEFAULT NULL,
  `lang` varchar(30) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `type` (`type`)
) ENGINE=InnoDB AUTO_INCREMENT=55 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `business_settings`
--

LOCK TABLES `business_settings` WRITE;
/*!40000 ALTER TABLE `business_settings` DISABLE KEYS */;
INSERT INTO `business_settings` VALUES (1,'bkash','1',NULL,'2025-10-13 18:48:26','2025-10-13 18:48:26'),(2,'bkash_sandbox','1',NULL,'2025-10-13 18:48:26','2025-10-13 18:48:26'),(3,'aamarpay','1',NULL,'2025-10-13 18:48:26','2025-10-13 18:48:26'),(4,'aamarpay_sandbox','1',NULL,'2025-10-13 18:48:26','2025-10-13 18:48:26'),(5,'nagad','1',NULL,'2025-10-13 18:48:26','2025-10-13 18:48:26'),(6,'nagad_sandbox','1',NULL,'2025-10-13 18:48:26','2025-10-13 18:48:26'),(7,'bkash_activation','Bkash Activation','en','2025-10-13 18:48:26','2025-10-13 18:48:26'),(8,'you_need_to_configure_bkash_co','You need to configure bkash correctly to enable this feature','en','2025-10-13 18:48:26','2025-10-13 18:48:26'),(9,'aamarpay_activation','Aamarpay Activation','en','2025-10-13 18:48:26','2025-10-13 18:48:26'),(10,'you_need_to_configure_aamarpay','You need to configure aamarpay correctly to enable this feature','en','2025-10-13 18:48:26','2025-10-13 18:48:26'),(11,'nagad_activation','Nagad Activation','en','2025-10-13 18:48:26','2025-10-13 18:48:26'),(12,'you_need_to_configure_nagad_co','You need to configure nagad correctly to enable this feature','en','2025-10-13 18:48:26','2025-10-13 18:48:26'),(13,'bkash','1',NULL,'2025-10-13 19:59:29','2025-10-13 19:59:29'),(14,'bkash_sandbox','1',NULL,'2025-10-13 19:59:29','2025-10-13 19:59:29'),(15,'bkash','1',NULL,'2025-10-13 20:00:24','2025-10-13 20:00:24'),(16,'bkash_sandbox','1',NULL,'2025-10-13 20:00:24','2025-10-13 20:00:24'),(17,'bkash','1',NULL,'2025-10-14 17:50:05','2025-10-14 17:50:05'),(18,'bkash_sandbox','1',NULL,'2025-10-14 17:50:05','2025-10-14 17:50:05'),(19,'bkash','1',NULL,'2025-10-14 17:51:43','2025-10-14 17:51:43'),(20,'bkash_sandbox','1',NULL,'2025-10-14 17:51:43','2025-10-14 17:51:43'),(21,'bkash','1',NULL,'2025-10-14 17:56:27','2025-10-14 17:56:27'),(22,'bkash_sandbox','1',NULL,'2025-10-14 17:56:27','2025-10-14 17:56:27'),(23,'bkash','1',NULL,'2025-10-14 17:58:01','2025-10-14 17:58:01'),(24,'bkash_sandbox','1',NULL,'2025-10-14 17:58:01','2025-10-14 17:58:01'),(25,'bkash','1',NULL,'2025-10-14 17:58:05','2025-10-14 17:58:05'),(26,'bkash_sandbox','1',NULL,'2025-10-14 17:58:05','2025-10-14 17:58:05'),(27,'bkash','1',NULL,'2025-10-14 17:58:06','2025-10-14 17:58:06'),(28,'bkash_sandbox','1',NULL,'2025-10-14 17:58:06','2025-10-14 17:58:06'),(29,'bkash','1',NULL,'2025-10-14 17:58:07','2025-10-14 17:58:07'),(30,'bkash_sandbox','1',NULL,'2025-10-14 17:58:07','2025-10-14 17:58:07'),(31,'bkash','1',NULL,'2025-10-14 17:58:08','2025-10-14 17:58:08'),(32,'bkash_sandbox','1',NULL,'2025-10-14 17:58:08','2025-10-14 17:58:08'),(33,'bkash','1',NULL,'2025-10-14 17:58:08','2025-10-14 17:58:08'),(34,'bkash_sandbox','1',NULL,'2025-10-14 17:58:08','2025-10-14 17:58:08'),(35,'bkash','1',NULL,'2025-10-14 17:58:23','2025-10-14 17:58:23'),(36,'bkash_sandbox','1',NULL,'2025-10-14 17:58:23','2025-10-14 17:58:23'),(37,'bkash_sandbox','1',NULL,'2025-10-14 19:13:40','2025-10-14 19:13:40'),(38,'bkash_app_key','4f6o0cjiki2rfm34kfdadl1eqq',NULL,'2025-10-14 19:13:40','2025-10-14 19:13:40'),(39,'bkash_app_secret','2is7hdktrekvrbljjh44ll3d9l1dtjo4pasmjvs5vl5qr3fug4b',NULL,'2025-10-14 19:13:40','2025-10-14 19:13:40'),(40,'bkash_username','sandboxTokenizedUser02',NULL,'2025-10-14 19:13:40','2025-10-14 19:13:40'),(41,'bkash_password','sandboxTokenizedUser02@12345',NULL,'2025-10-14 19:13:40','2025-10-14 19:13:40'),(42,'bkash_base_url','https://tokenized.sandbox.bka.sh/v1.2.0-beta',NULL,'2025-10-14 19:13:40','2025-10-14 19:13:40'),(43,'bkash_sandbox','1',NULL,'2025-10-14 19:31:02','2025-10-14 19:31:02'),(44,'bkash_app_key','4f6o0cjiki2rfm34kfdadl1eqq',NULL,'2025-10-14 19:31:02','2025-10-14 19:31:02'),(45,'bkash_app_secret','2is7hdktrekvrbljjh44ll3d9l1dtjo4pasmjvs5vl5qr3fug4b',NULL,'2025-10-14 19:31:02','2025-10-14 19:31:02'),(46,'bkash_username','sandboxTokenizedUser02',NULL,'2025-10-14 19:31:02','2025-10-14 19:31:02'),(47,'bkash_password','sandboxTokenizedUser02@12345',NULL,'2025-10-14 19:31:02','2025-10-14 19:31:02'),(48,'bkash_base_url','https://tokenized.sandbox.bka.sh/v1.2.0-beta',NULL,'2025-10-14 19:31:02','2025-10-14 19:31:02'),(49,'bkash_sandbox','1',NULL,'2025-10-14 19:32:45','2025-10-14 19:32:45'),(50,'bkash_app_key','4f6o0cjiki2rfm34kfdadl1eqq',NULL,'2025-10-14 19:32:45','2025-10-14 19:32:45'),(51,'bkash_app_secret','2is7hdktrekvrbljjh44ll3d9l1dtjo4pasmjvs5vl5qr3fug4b',NULL,'2025-10-14 19:32:45','2025-10-14 19:32:45'),(52,'bkash_username','sandboxTokenizedUser02',NULL,'2025-10-14 19:32:45','2025-10-14 19:32:45'),(53,'bkash_password','sandboxTokenizedUser02@12345',NULL,'2025-10-14 19:32:45','2025-10-14 19:32:45'),(54,'bkash_base_url','https://tokenized.sandbox.bka.sh/v1.2.0-beta',NULL,'2025-10-14 19:32:45','2025-10-14 19:32:45');
/*!40000 ALTER TABLE `business_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_clear_logs`
--

DROP TABLE IF EXISTS `cache_clear_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache_clear_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `admin_id` bigint(20) unsigned DEFAULT NULL,
  `admin_name` varchar(100) DEFAULT NULL,
  `action` varchar(50) NOT NULL DEFAULT 'cache_clear',
  `ip` varchar(45) DEFAULT NULL,
  `user_agent` varchar(500) DEFAULT NULL,
  `success` tinyint(1) NOT NULL DEFAULT 1,
  `error_message` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cache_clear_logs_admin_id_index` (`admin_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_clear_logs`
--

LOCK TABLES `cache_clear_logs` WRITE;
/*!40000 ALTER TABLE `cache_clear_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_clear_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `carts`
--

DROP TABLE IF EXISTS `carts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `carts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `variant_id` bigint(20) unsigned DEFAULT NULL,
  `variant_details` text DEFAULT NULL COMMENT 'JSON for display',
  `quantity` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_cart_variant` (`variant_id`),
  CONSTRAINT `carts_variant_id_foreign` FOREIGN KEY (`variant_id`) REFERENCES `product_variants` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_cart_variant` FOREIGN KEY (`variant_id`) REFERENCES `product_variants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `carts`
--

LOCK TABLES `carts` WRITE;
/*!40000 ALTER TABLE `carts` DISABLE KEYS */;
INSERT INTO `carts` VALUES (1,1,1,NULL,NULL,5,'2025-10-10 11:53:10','2025-10-10 11:53:42'),(3,3,1,NULL,NULL,2,'2025-10-11 13:19:39','2025-10-11 13:20:29'),(4,4,1,NULL,NULL,2,'2025-10-12 19:33:24','2025-10-15 10:45:46'),(5,5,1,NULL,NULL,1,'2025-10-15 11:42:04','2025-10-15 11:42:04'),(10,6,2,NULL,NULL,1,'2025-10-16 04:46:14','2025-10-16 04:46:14'),(11,6,1,NULL,NULL,4,'2025-10-16 07:54:31','2025-10-16 11:07:51'),(12,7,1,NULL,NULL,2,'2025-11-03 03:52:29','2025-11-03 03:52:29'),(14,8,2,NULL,NULL,2,'2026-02-28 12:52:22','2026-02-28 12:53:02'),(16,9,1,NULL,NULL,1,'2026-03-02 12:51:22','2026-03-02 12:51:22'),(17,9,2,NULL,NULL,3,'2026-03-02 13:07:45','2026-03-06 05:55:37'),(20,9,5,NULL,NULL,1,'2026-03-02 13:07:53','2026-03-09 08:12:47'),(22,9,3,NULL,NULL,4,'2026-03-04 08:37:40','2026-03-06 02:09:28'),(23,9,4,NULL,NULL,1,'2026-03-04 08:40:13','2026-03-04 08:40:13');
/*!40000 ALTER TABLE `carts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `image` varchar(255) NOT NULL,
  `featured` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `home_line` tinyint(3) unsigned NOT NULL DEFAULT 1,
  `home_order` smallint(5) unsigned NOT NULL DEFAULT 0,
  `status` tinyint(1) unsigned NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `publish_status` varchar(20) NOT NULL DEFAULT 'public',
  `scheduled_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (1,'opu','69a5bed1e548c1772469969.png',0,1,0,1,'2025-10-10 11:27:15','2026-03-02 10:46:19','public',NULL),(2,'md','69a5beeee02c81772469998.png',0,1,0,1,'2026-03-02 10:46:38','2026-03-02 10:46:38','public',NULL),(3,'pc','69a5bf067414a1772470022.png',0,1,0,1,'2026-03-02 10:47:02','2026-03-02 10:47:02','public',NULL),(4,'mobail','69a5bf1a47c3f1772470042.png',0,1,0,1,'2026-03-02 10:47:22','2026-03-02 10:47:22','public',NULL),(5,'a','69b3d85eced291773394014.svg',0,1,0,1,'2026-03-13 03:26:54','2026-03-13 03:26:54','public',NULL),(6,'b','69b3d86b628351773394027.svg',0,1,0,1,'2026-03-13 03:27:07','2026-03-13 03:27:07','public',NULL),(7,'c','69b3d8747b4c11773394036.svg',0,1,0,1,'2026-03-13 03:27:16','2026-03-13 03:27:16','public',NULL),(8,'d','69b3d884d75be1773394052.svg',0,1,0,1,'2026-03-13 03:27:32','2026-03-13 03:27:32','public',NULL),(9,'e','69b3d890930d01773394064.svg',0,1,0,1,'2026-03-13 03:27:44','2026-03-13 03:27:44','public',NULL),(10,'f','69b3d89b0c1d61773394075.svg',0,1,0,1,'2026-03-13 03:27:55','2026-03-13 03:27:55','public',NULL),(11,'g','69b3d8a5d093d1773394085.svg',0,1,0,1,'2026-03-13 03:28:05','2026-03-13 03:28:05','public',NULL),(12,'h','69b3d8b019ee51773394096.svg',0,1,0,1,'2026-03-13 03:28:16','2026-03-13 03:28:16','public',NULL),(13,'i','69b3d8da31ae91773394138.svg',0,1,0,1,'2026-03-13 03:28:58','2026-03-13 03:28:58','public',NULL),(14,'j','69b3d8ef27da51773394159.svg',0,1,0,1,'2026-03-13 03:29:19','2026-03-13 03:29:19','public',NULL),(15,'k','69b3d9070bd231773394183.svg',0,1,0,1,'2026-03-13 03:29:43','2026-03-13 03:29:43','public',NULL),(16,'ab','69b3f134872031773400372.svg',0,1,1,1,'2026-03-13 05:12:52','2026-03-13 05:48:46','public',NULL);
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `category_attributes`
--

DROP TABLE IF EXISTS `category_attributes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `category_attributes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `category_id` bigint(20) unsigned NOT NULL,
  `attribute_id` bigint(20) unsigned NOT NULL,
  `is_required` tinyint(4) NOT NULL DEFAULT 0,
  `is_variant` tinyint(4) NOT NULL DEFAULT 1 COMMENT 'If 1, creates product variants',
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cat_attr_unique` (`category_id`,`attribute_id`),
  KEY `idx_is_variant` (`is_variant`),
  KEY `fk_cat_attr_attribute` (`attribute_id`),
  CONSTRAINT `fk_cat_attr_attribute` FOREIGN KEY (`attribute_id`) REFERENCES `product_attributes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_cat_attr_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `category_attributes`
--

LOCK TABLES `category_attributes` WRITE;
/*!40000 ALTER TABLE `category_attributes` DISABLE KEYS */;
/*!40000 ALTER TABLE `category_attributes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `chat_assignments`
--

DROP TABLE IF EXISTS `chat_assignments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `chat_assignments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `conversation_id` bigint(20) unsigned NOT NULL,
  `admin_id` bigint(20) unsigned NOT NULL,
  `assigned_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `chat_assignments_conversation_id_foreign` (`conversation_id`),
  KEY `chat_assignments_admin_id_index` (`admin_id`),
  CONSTRAINT `chat_assignments_conversation_id_foreign` FOREIGN KEY (`conversation_id`) REFERENCES `conversations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `chat_assignments`
--

LOCK TABLES `chat_assignments` WRITE;
/*!40000 ALTER TABLE `chat_assignments` DISABLE KEYS */;
/*!40000 ALTER TABLE `chat_assignments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cod_blacklists`
--

DROP TABLE IF EXISTS `cod_blacklists`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cod_blacklists` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(20) NOT NULL COMMENT 'mobile, address, ip',
  `value` varchar(255) NOT NULL COMMENT 'phone, address hash, or IP',
  `reason` varchar(500) DEFAULT NULL,
  `admin_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cod_blacklists_type_value_index` (`type`,`value`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cod_blacklists`
--

LOCK TABLES `cod_blacklists` WRITE;
/*!40000 ALTER TABLE `cod_blacklists` DISABLE KEYS */;
/*!40000 ALTER TABLE `cod_blacklists` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cod_otp_verifications`
--

DROP TABLE IF EXISTS `cod_otp_verifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cod_otp_verifications` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `mobile` varchar(50) NOT NULL,
  `otp` varchar(10) NOT NULL,
  `session_id` varchar(100) DEFAULT NULL,
  `expires_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `verified_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cod_otp_verifications_mobile_expires_at_index` (`mobile`,`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cod_otp_verifications`
--

LOCK TABLES `cod_otp_verifications` WRITE;
/*!40000 ALTER TABLE `cod_otp_verifications` DISABLE KEYS */;
/*!40000 ALTER TABLE `cod_otp_verifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cod_settings`
--

DROP TABLE IF EXISTS `cod_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cod_settings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `cod_enabled` tinyint(1) NOT NULL DEFAULT 1,
  `cod_min_order` decimal(18,2) NOT NULL DEFAULT 0.00,
  `cod_max_order` decimal(18,2) NOT NULL DEFAULT 0.00 COMMENT '0 = no max limit',
  `cod_charge_type` tinyint(4) NOT NULL DEFAULT 1 COMMENT '1=flat, 2=percent',
  `cod_charge_value` decimal(18,2) NOT NULL DEFAULT 0.00,
  `cod_free_above` decimal(18,2) NOT NULL DEFAULT 0.00 COMMENT 'Free COD above this order amount; 0=disabled',
  `cod_otp_required` tinyint(1) NOT NULL DEFAULT 0,
  `cod_otp_expire_minutes` smallint(5) unsigned NOT NULL DEFAULT 10,
  `cod_auto_cancel_hours` smallint(5) unsigned NOT NULL DEFAULT 24 COMMENT 'Cancel unverified COD order after N hours',
  `cod_failed_disable_count` tinyint(3) unsigned NOT NULL DEFAULT 2 COMMENT 'Disable COD after N failed deliveries',
  `cod_new_customer_max` decimal(18,2) NOT NULL DEFAULT 0.00 COMMENT 'Max order for new customer COD; 0=use cod_max_order',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cod_settings`
--

LOCK TABLES `cod_settings` WRITE;
/*!40000 ALTER TABLE `cod_settings` DISABLE KEYS */;
/*!40000 ALTER TABLE `cod_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contact_channel_integrations`
--

DROP TABLE IF EXISTS `contact_channel_integrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contact_channel_integrations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `channel` varchar(32) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_primary` tinyint(1) NOT NULL DEFAULT 0,
  `settings` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`settings`)),
  `auth_meta` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`auth_meta`)),
  `last_synced_at` timestamp NULL DEFAULT NULL,
  `last_error_at` timestamp NULL DEFAULT NULL,
  `last_error_message` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `contact_channel_integrations_channel_index` (`channel`),
  KEY `contact_channel_integrations_is_active_index` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contact_channel_integrations`
--

LOCK TABLES `contact_channel_integrations` WRITE;
/*!40000 ALTER TABLE `contact_channel_integrations` DISABLE KEYS */;
/*!40000 ALTER TABLE `contact_channel_integrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contact_channel_messages`
--

DROP TABLE IF EXISTS `contact_channel_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contact_channel_messages` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `contact_channel_integration_id` bigint(20) unsigned DEFAULT NULL,
  `support_ticket_id` bigint(20) unsigned DEFAULT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `channel` varchar(32) NOT NULL,
  `direction` varchar(16) NOT NULL DEFAULT 'inbound',
  `remote_chat_id` varchar(255) DEFAULT NULL,
  `remote_message_id` varchar(255) DEFAULT NULL,
  `sender_name` varchar(255) DEFAULT NULL,
  `sender_handle` varchar(255) DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `attachments` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`attachments`)),
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contact_channel_messages`
--

LOCK TABLES `contact_channel_messages` WRITE;
/*!40000 ALTER TABLE `contact_channel_messages` DISABLE KEYS */;
/*!40000 ALTER TABLE `contact_channel_messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `conversations`
--

DROP TABLE IF EXISTS `conversations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `conversations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `guest_email` varchar(255) DEFAULT NULL,
  `guest_name` varchar(255) DEFAULT NULL,
  `channel` varchar(50) DEFAULT 'web',
  `status` enum('open','pending','closed') NOT NULL DEFAULT 'open',
  `subject` varchar(500) DEFAULT NULL,
  `last_message_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `conversations_user_id_index` (`user_id`),
  KEY `conversations_status_index` (`status`),
  KEY `conversations_channel_index` (`channel`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `conversations`
--

LOCK TABLES `conversations` WRITE;
/*!40000 ALTER TABLE `conversations` DISABLE KEYS */;
/*!40000 ALTER TABLE `conversations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `coupons`
--

DROP TABLE IF EXISTS `coupons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `coupons` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `discount` decimal(28,8) unsigned NOT NULL DEFAULT 0.00000000,
  `discount_type` tinyint(1) unsigned DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `min_order` decimal(28,8) unsigned NOT NULL DEFAULT 0.00000000,
  `status` tinyint(1) unsigned NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `usage_limit` int(10) unsigned DEFAULT NULL,
  `max_discount` decimal(10,2) DEFAULT NULL,
  `per_user_limit` int(10) unsigned DEFAULT NULL,
  `description` text DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `is_first_order_only` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `coupons`
--

LOCK TABLES `coupons` WRITE;
/*!40000 ALTER TABLE `coupons` DISABLE KEYS */;
/*!40000 ALTER TABLE `coupons` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `courier_backups`
--

DROP TABLE IF EXISTS `courier_backups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `courier_backups` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `courier_type` varchar(50) NOT NULL,
  `backup_data` longtext NOT NULL,
  `backup_type` varchar(50) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `courier_backups_courier_type_index` (`courier_type`),
  KEY `courier_backups_backup_type_index` (`backup_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `courier_backups`
--

LOCK TABLES `courier_backups` WRITE;
/*!40000 ALTER TABLE `courier_backups` DISABLE KEYS */;
/*!40000 ALTER TABLE `courier_backups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `courier_cache`
--

DROP TABLE IF EXISTS `courier_cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `courier_cache` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `courier_type` varchar(50) NOT NULL,
  `cache_key` varchar(255) NOT NULL,
  `cache_data` longtext NOT NULL,
  `expires_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `courier_cache_type_key_unique` (`courier_type`,`cache_key`),
  KEY `courier_cache_expires_at_index` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `courier_cache`
--

LOCK TABLES `courier_cache` WRITE;
/*!40000 ALTER TABLE `courier_cache` DISABLE KEYS */;
/*!40000 ALTER TABLE `courier_cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary table structure for view `courier_daily_stats`
--

DROP TABLE IF EXISTS `courier_daily_stats`;
/*!50001 DROP VIEW IF EXISTS `courier_daily_stats`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `courier_daily_stats` AS SELECT
 1 AS `courier_type`,
  1 AS `date`,
  1 AS `total_orders`,
  1 AS `successful_orders`,
  1 AS `failed_orders` */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `courier_error_codes`
--

DROP TABLE IF EXISTS `courier_error_codes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `courier_error_codes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `courier_type` varchar(50) NOT NULL,
  `error_code` varchar(50) NOT NULL,
  `error_message` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `courier_error_codes_type_code_unique` (`courier_type`,`error_code`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `courier_error_codes`
--

LOCK TABLES `courier_error_codes` WRITE;
/*!40000 ALTER TABLE `courier_error_codes` DISABLE KEYS */;
INSERT INTO `courier_error_codes` VALUES (1,'pathao','400','Bad Request','Invalid request parameters',1,'2025-10-12 16:07:20','2025-10-12 16:07:20'),(2,'pathao','401','Unauthorized','Invalid API credentials',1,'2025-10-12 16:07:20','2025-10-12 16:07:20'),(3,'pathao','403','Forbidden','Access denied',1,'2025-10-12 16:07:20','2025-10-12 16:07:20'),(4,'pathao','404','Not Found','Resource not found',1,'2025-10-12 16:07:20','2025-10-12 16:07:20'),(5,'pathao','500','Internal Server Error','Server error',1,'2025-10-12 16:07:20','2025-10-12 16:07:20'),(6,'steadfast','400','Bad Request','Invalid request parameters',1,'2025-10-12 16:07:20','2025-10-12 16:07:20'),(7,'steadfast','401','Unauthorized','Invalid API credentials',1,'2025-10-12 16:07:20','2025-10-12 16:07:20'),(8,'steadfast','403','Forbidden','Access denied',1,'2025-10-12 16:07:20','2025-10-12 16:07:20'),(9,'steadfast','404','Not Found','Resource not found',1,'2025-10-12 16:07:20','2025-10-12 16:07:20'),(10,'steadfast','500','Internal Server Error','Server error',1,'2025-10-12 16:07:20','2025-10-12 16:07:20');
/*!40000 ALTER TABLE `courier_error_codes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `courier_logs`
--

DROP TABLE IF EXISTS `courier_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `courier_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint(20) unsigned NOT NULL,
  `courier_type` varchar(50) NOT NULL,
  `courier_order_id` varchar(255) DEFAULT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'pending',
  `return_status` varchar(20) DEFAULT 'none',
  `request_data` longtext DEFAULT NULL,
  `response_data` longtext DEFAULT NULL,
  `error_message` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `courier_logs_order_id_foreign` (`order_id`),
  KEY `courier_logs_courier_type_index` (`courier_type`),
  KEY `courier_logs_status_index` (`status`),
  KEY `courier_logs_created_at_index` (`created_at`),
  KEY `courier_logs_courier_type_created_at_index` (`courier_type`,`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `courier_logs`
--

LOCK TABLES `courier_logs` WRITE;
/*!40000 ALTER TABLE `courier_logs` DISABLE KEYS */;
INSERT INTO `courier_logs` VALUES (3,8,'steadfast',NULL,'failed','none','{\"consignment_type\":\"2\",\"delivery_type\":\"2\",\"city\":\"jnnj\",\"area\":\"njjn\",\"order_id\":8,\"order_no\":\"DGG5URG39KS7\",\"customer_name\":\"adminkys\",\"customer_phone\":\"8801388888888\",\"customer_address\":\"N\\/A\",\"amount\":\"181.00000000\",\"weight\":1,\"notes\":\"Order from website\"}',NULL,'cURL error 6: Could not resolve host: api (see https://curl.haxx.se/libcurl/c/libcurl-errors.html) for api/consignment','2025-10-12 19:23:12','2025-10-12 19:23:12');
/*!40000 ALTER TABLE `courier_logs` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `update_courier_statistics` AFTER INSERT ON `courier_logs` FOR EACH ROW INSERT INTO courier_statistics (courier_type, date, total_orders, successful_orders, failed_orders)
            VALUES (
                NEW.courier_type,
                DATE(NEW.created_at),
                1,
                CASE WHEN NEW.status = 'success' THEN 1 ELSE 0 END,
                CASE WHEN NEW.status = 'failed' THEN 1 ELSE 0 END
            )
            ON DUPLICATE KEY UPDATE
                total_orders = total_orders + 1,
                successful_orders = successful_orders + (CASE WHEN NEW.status = 'success' THEN 1 ELSE 0 END),
                failed_orders = failed_orders + (CASE WHEN NEW.status = 'failed' THEN 1 ELSE 0 END) */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `courier_notifications`
--

DROP TABLE IF EXISTS `courier_notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `courier_notifications` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint(20) unsigned NOT NULL,
  `courier_type` varchar(50) NOT NULL,
  `notification_type` varchar(50) NOT NULL,
  `message` text NOT NULL,
  `is_sent` tinyint(1) NOT NULL DEFAULT 0,
  `sent_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `courier_notifications_order_id_foreign` (`order_id`),
  KEY `courier_notifications_courier_type_index` (`courier_type`),
  KEY `courier_notifications_notification_type_index` (`notification_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `courier_notifications`
--

LOCK TABLES `courier_notifications` WRITE;
/*!40000 ALTER TABLE `courier_notifications` DISABLE KEYS */;
/*!40000 ALTER TABLE `courier_notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `courier_rate_limits`
--

DROP TABLE IF EXISTS `courier_rate_limits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `courier_rate_limits` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `courier_type` varchar(50) NOT NULL,
  `api_endpoint` varchar(255) NOT NULL,
  `requests_per_minute` int(11) NOT NULL DEFAULT 60,
  `requests_per_hour` int(11) NOT NULL DEFAULT 1000,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `courier_rate_limits_type_endpoint_unique` (`courier_type`,`api_endpoint`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `courier_rate_limits`
--

LOCK TABLES `courier_rate_limits` WRITE;
/*!40000 ALTER TABLE `courier_rate_limits` DISABLE KEYS */;
INSERT INTO `courier_rate_limits` VALUES (1,'pathao','orders',30,500,'2025-10-12 16:07:20','2025-10-12 16:07:20'),(2,'pathao','cities',60,1000,'2025-10-12 16:07:20','2025-10-12 16:07:20'),(3,'pathao','zones',60,1000,'2025-10-12 16:07:20','2025-10-12 16:07:20'),(4,'steadfast','orders',30,500,'2025-10-12 16:07:20','2025-10-12 16:07:20');
/*!40000 ALTER TABLE `courier_rate_limits` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `courier_settings`
--

DROP TABLE IF EXISTS `courier_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `courier_settings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `courier_type` varchar(50) NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `courier_settings_type_key_unique` (`courier_type`,`setting_key`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `courier_settings`
--

LOCK TABLES `courier_settings` WRITE;
/*!40000 ALTER TABLE `courier_settings` DISABLE KEYS */;
INSERT INTO `courier_settings` VALUES (1,'pathao','default_store_id','','2025-10-12 16:07:20','2025-10-12 16:07:20'),(2,'pathao','default_city_id','','2025-10-12 16:07:20','2025-10-12 16:07:20'),(3,'pathao','default_zone_id','','2025-10-12 16:07:20','2025-10-12 16:07:20'),(4,'steadfast','default_branch_id','','2025-10-12 16:07:20','2025-10-12 16:07:20'),(5,'steadfast','default_service_type','48','2025-10-12 16:07:20','2025-10-12 16:07:20'),(6,'pathao','api_version','v1','2025-10-12 16:07:20','2025-10-12 16:07:20'),(7,'pathao','timeout','30','2025-10-12 16:07:20','2025-10-12 16:07:20'),(8,'pathao','retry_attempts','3','2025-10-12 16:07:20','2025-10-12 16:07:20'),(9,'steadfast','api_version','v1','2025-10-12 16:07:20','2025-10-12 16:07:20'),(10,'steadfast','timeout','30','2025-10-12 16:07:20','2025-10-12 16:07:20'),(11,'steadfast','retry_attempts','3','2025-10-12 16:07:20','2025-10-12 16:07:20');
/*!40000 ALTER TABLE `courier_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `courier_statistics`
--

DROP TABLE IF EXISTS `courier_statistics`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `courier_statistics` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `courier_type` varchar(50) NOT NULL,
  `date` date NOT NULL,
  `total_orders` int(11) NOT NULL DEFAULT 0,
  `successful_orders` int(11) NOT NULL DEFAULT 0,
  `failed_orders` int(11) NOT NULL DEFAULT 0,
  `total_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `courier_statistics_type_date_unique` (`courier_type`,`date`),
  KEY `courier_statistics_date_index` (`date`),
  KEY `courier_statistics_courier_type_date_index` (`courier_type`,`date`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `courier_statistics`
--

LOCK TABLES `courier_statistics` WRITE;
/*!40000 ALTER TABLE `courier_statistics` DISABLE KEYS */;
INSERT INTO `courier_statistics` VALUES (1,'pathao','2025-10-12',1,1,0,0.00,NULL,NULL),(2,'steadfast','2025-10-12',1,1,0,0.00,NULL,NULL),(3,'steadfast','2025-10-13',1,0,1,0.00,NULL,NULL);
/*!40000 ALTER TABLE `courier_statistics` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `courier_status_types`
--

DROP TABLE IF EXISTS `courier_status_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `courier_status_types` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `courier_type` varchar(50) NOT NULL,
  `status_code` varchar(50) NOT NULL,
  `status_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `courier_status_types_type_code_unique` (`courier_type`,`status_code`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `courier_status_types`
--

LOCK TABLES `courier_status_types` WRITE;
/*!40000 ALTER TABLE `courier_status_types` DISABLE KEYS */;
INSERT INTO `courier_status_types` VALUES (1,'pathao','pending','Pending','Order is pending',1,'2025-10-12 16:07:20','2025-10-12 16:07:20'),(2,'pathao','confirmed','Confirmed','Order is confirmed',1,'2025-10-12 16:07:20','2025-10-12 16:07:20'),(3,'pathao','picked_up','Picked Up','Order is picked up',1,'2025-10-12 16:07:20','2025-10-12 16:07:20'),(4,'pathao','delivered','Delivered','Order is delivered',1,'2025-10-12 16:07:20','2025-10-12 16:07:20'),(5,'pathao','cancelled','Cancelled','Order is cancelled',1,'2025-10-12 16:07:20','2025-10-12 16:07:20'),(6,'steadfast','pending','Pending','Order is pending',1,'2025-10-12 16:07:20','2025-10-12 16:07:20'),(7,'steadfast','confirmed','Confirmed','Order is confirmed',1,'2025-10-12 16:07:20','2025-10-12 16:07:20'),(8,'steadfast','picked_up','Picked Up','Order is picked up',1,'2025-10-12 16:07:20','2025-10-12 16:07:20'),(9,'steadfast','delivered','Delivered','Order is delivered',1,'2025-10-12 16:07:20','2025-10-12 16:07:20'),(10,'steadfast','cancelled','Cancelled','Order is cancelled',1,'2025-10-12 16:07:20','2025-10-12 16:07:20');
/*!40000 ALTER TABLE `courier_status_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `courier_webhooks`
--

DROP TABLE IF EXISTS `courier_webhooks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `courier_webhooks` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `courier_type` varchar(50) NOT NULL,
  `webhook_url` varchar(255) NOT NULL,
  `secret_key` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `courier_webhooks`
--

LOCK TABLES `courier_webhooks` WRITE;
/*!40000 ALTER TABLE `courier_webhooks` DISABLE KEYS */;
/*!40000 ALTER TABLE `courier_webhooks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `courierapis`
--

DROP TABLE IF EXISTS `courierapis`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `courierapis` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(255) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `country_code` varchar(10) NOT NULL DEFAULT 'BD',
  `region` varchar(20) DEFAULT NULL,
  `api_key` varchar(255) DEFAULT NULL,
  `secret_key` varchar(255) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `token` varchar(255) DEFAULT NULL,
  `config` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`config`)),
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `show_to_user` tinyint(1) NOT NULL DEFAULT 0,
  `sort_order` smallint(5) unsigned NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `courierapis_type_unique` (`type`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `courierapis`
--

LOCK TABLES `courierapis` WRITE;
/*!40000 ALTER TABLE `courierapis` DISABLE KEYS */;
INSERT INTO `courierapis` VALUES (1,'steadfast','Steadfast Courier','BD',NULL,'uzTfzUOQDwUtwB4lctHIchfiGN12qBvCDFakwJeu5oxdiU4kKX','nillislam03','','',NULL,1,0,1,'2025-10-12 16:07:20','2026-02-26 23:40:32'),(2,'pathao','Pathao','BD',NULL,'','','https://api-hermes.pathao.com','',NULL,0,0,2,'2025-10-12 16:07:20','2026-02-26 23:40:32');
/*!40000 ALTER TABLE `courierapis` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `delivery_zones`
--

DROP TABLE IF EXISTS `delivery_zones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `delivery_zones` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `thana_id` bigint(20) unsigned NOT NULL,
  `delivery_charge` decimal(14,2) NOT NULL DEFAULT 0.00,
  `estimated_days` varchar(50) DEFAULT NULL,
  `status` tinyint(3) unsigned NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `delivery_zones_thana_id_index` (`thana_id`),
  CONSTRAINT `delivery_zones_thana_id_foreign` FOREIGN KEY (`thana_id`) REFERENCES `thanas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `delivery_zones`
--

LOCK TABLES `delivery_zones` WRITE;
/*!40000 ALTER TABLE `delivery_zones` DISABLE KEYS */;
/*!40000 ALTER TABLE `delivery_zones` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `deposits`
--

DROP TABLE IF EXISTS `deposits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `deposits` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `order_id` bigint(20) unsigned NOT NULL DEFAULT 0,
  `method_code` int(10) unsigned NOT NULL,
  `method_currency` varchar(20) NOT NULL,
  `amount` decimal(18,8) NOT NULL DEFAULT 0.00000000,
  `charge` decimal(18,8) NOT NULL DEFAULT 0.00000000,
  `rate` decimal(18,8) NOT NULL DEFAULT 0.00000000,
  `final_amo` decimal(18,8) NOT NULL DEFAULT 0.00000000,
  `btc_amo` decimal(18,8) NOT NULL DEFAULT 0.00000000,
  `btc_wallet` varchar(255) DEFAULT NULL,
  `trx` varchar(100) NOT NULL,
  `status` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `detail` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`detail`)),
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `deposits`
--

LOCK TABLES `deposits` WRITE;
/*!40000 ALTER TABLE `deposits` DISABLE KEYS */;
/*!40000 ALTER TABLE `deposits` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `districts`
--

DROP TABLE IF EXISTS `districts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `districts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `division_id` bigint(20) unsigned NOT NULL,
  `name_en` varchar(100) NOT NULL,
  `name_bn` varchar(100) NOT NULL,
  `sort_order` tinyint(3) unsigned DEFAULT 0,
  `status` tinyint(3) unsigned NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `districts_division_id_index` (`division_id`),
  CONSTRAINT `districts_ibfk_1` FOREIGN KEY (`division_id`) REFERENCES `divisions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=129 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `districts`
--

LOCK TABLES `districts` WRITE;
/*!40000 ALTER TABLE `districts` DISABLE KEYS */;
INSERT INTO `districts` VALUES (1,1,'Barguna','বরগুনা',1,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(2,1,'Barisal','বরিশাল',2,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(3,1,'Bhola','ভোলা',3,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(4,1,'Jhalokati','ঝালকাঠি',4,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(5,1,'Patuakhali','পটুয়াখালী',5,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(6,1,'Pirojpur','পিরোজপুর',6,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(7,2,'Bandarban','বান্দরবান',7,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(8,2,'Brahmanbaria','ব্রাহ্মণবাড়িয়া',8,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(9,2,'Chandpur','চাঁদপুর',9,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(10,2,'Chittagong','চট্টগ্রাম',10,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(11,2,'Comilla','কুমিল্লা',11,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(12,2,'Cox\'s Bazar','কক্সবাজার',12,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(13,2,'Feni','ফেনী',13,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(14,2,'Khagrachhari','খাগড়াছড়ি',14,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(15,2,'Lakshmipur','লক্ষ্মীপুর',15,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(16,2,'Noakhali','নোয়াখালী',16,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(17,2,'Rangamati','রাঙ্গামাটি',17,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(18,3,'Dhaka','ঢাকা',18,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(19,3,'Faridpur','ফরিদপুর',19,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(20,3,'Gazipur','গাজীপুর',20,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(21,3,'Gopalganj','গোপালগঞ্জ',21,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(22,3,'Kishoreganj','কিশোরগঞ্জ',22,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(23,3,'Madaripur','মাদারীপুর',23,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(24,3,'Manikganj','মানিকগঞ্জ',24,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(25,3,'Munshiganj','মুন্সিগঞ্জ',25,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(26,3,'Narayanganj','নারায়ণগঞ্জ',26,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(27,3,'Narsingdi','নরসিংদী',27,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(28,3,'Rajbari','রাজবাড়ী',28,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(29,3,'Shariatpur','শরীয়তপুর',29,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(30,3,'Tangail','টাঙ্গাইল',30,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(31,4,'Bagerhat','বাগেরহাট',31,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(32,4,'Chuadanga','চুয়াডাঙ্গা',32,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(33,4,'Jessore','যশোর',33,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(34,4,'Jhenaidah','ঝিনাইদহ',34,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(35,4,'Khulna','খুলনা',35,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(36,4,'Kushtia','কুষ্টিয়া',36,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(37,4,'Magura','মাগুরা',37,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(38,4,'Meherpur','মেহেরপুর',38,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(39,4,'Narail','নড়াইল',39,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(40,4,'Satkhira','সাতক্ষীরা',40,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(41,5,'Jamalpur','জামালপুর',41,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(42,5,'Mymensingh','ময়মনসিংহ',42,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(43,5,'Netrokona','নেত্রকোণা',43,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(44,5,'Sherpur','শেরপুর',44,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(45,6,'Bogra','বগুড়া',45,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(46,6,'Joypurhat','জয়পুরহাট',46,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(47,6,'Naogaon','নওগাঁ',47,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(48,6,'Natore','নাটোর',48,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(49,6,'Chapainawabganj','চাঁপাইনবাবগঞ্জ',49,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(50,6,'Pabna','পাবনা',50,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(51,6,'Rajshahi','রাজশাহী',51,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(52,6,'Sirajganj','সিরাজগঞ্জ',52,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(53,7,'Dinajpur','দিনাজপুর',53,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(54,7,'Gaibandha','গাইবান্ধা',54,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(55,7,'Kurigram','কুড়িগ্রাম',55,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(56,7,'Lalmonirhat','লালমনিরহাট',56,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(57,7,'Nilphamari','নীলফামারী',57,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(58,7,'Panchagarh','পঞ্চগড়',58,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(59,7,'Rangpur','রংপুর',59,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(60,7,'Thakurgaon','ঠাকুরগাঁও',60,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(61,8,'Habiganj','হবিগঞ্জ',61,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(62,8,'Moulvibazar','মৌলভীবাজার',62,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(63,8,'Sunamganj','সুনামগঞ্জ',63,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(64,8,'Sylhet','সিলেট',64,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(65,1,'Barguna','Óª¼Óª░ÓªùÓºüÓª¿Óª¥',1,1,'2026-05-04 17:54:02','2026-05-04 17:54:02'),(66,1,'Barisal','Óª¼Óª░Óª┐ÓªÂÓª¥Óª▓',2,1,'2026-05-04 17:54:02','2026-05-04 17:54:02'),(67,1,'Bhola','Óª¡ÓºïÓª▓Óª¥',3,1,'2026-05-04 17:54:02','2026-05-04 17:54:02'),(68,1,'Jhalokati','ÓªØÓª¥Óª▓ÓªòÓª¥ÓªáÓª┐',4,1,'2026-05-04 17:54:02','2026-05-04 17:54:02'),(69,1,'Patuakhali','Óª¬ÓªƒÓºüÓª»Óª╝Óª¥ÓªûÓª¥Óª▓ÓºÇ',5,1,'2026-05-04 17:54:02','2026-05-04 17:54:02'),(70,1,'Pirojpur','Óª¬Óª┐Óª░ÓºïÓª£Óª¬ÓºüÓª░',6,1,'2026-05-04 17:54:02','2026-05-04 17:54:02'),(71,2,'Bandarban','Óª¼Óª¥Óª¿ÓºìÓªªÓª░Óª¼Óª¥Óª¿',7,1,'2026-05-04 17:54:02','2026-05-04 17:54:02'),(72,2,'Brahmanbaria','Óª¼ÓºìÓª░Óª¥Óª╣ÓºìÓª«ÓªúÓª¼Óª¥ÓªíÓª╝Óª┐Óª»Óª╝Óª¥',8,1,'2026-05-04 17:54:02','2026-05-04 17:54:02'),(73,2,'Chandpur','ÓªÜÓª¥ÓªüÓªªÓª¬ÓºüÓª░',9,1,'2026-05-04 17:54:02','2026-05-04 17:54:02'),(74,2,'Chittagong','ÓªÜÓªƒÓºìÓªƒÓªùÓºìÓª░Óª¥Óª«',10,1,'2026-05-04 17:54:02','2026-05-04 17:54:02'),(75,2,'Comilla','ÓªòÓºüÓª«Óª┐Óª▓ÓºìÓª▓Óª¥',11,1,'2026-05-04 17:54:02','2026-05-04 17:54:02'),(76,2,'Cox\'s Bazar','ÓªòÓªòÓºìÓª©Óª¼Óª¥Óª£Óª¥Óª░',12,1,'2026-05-04 17:54:02','2026-05-04 17:54:02'),(77,2,'Feni','Óª½ÓºçÓª¿ÓºÇ',13,1,'2026-05-04 17:54:02','2026-05-04 17:54:02'),(78,2,'Khagrachhari','ÓªûÓª¥ÓªùÓªíÓª╝Óª¥ÓªøÓªíÓª╝Óª┐',14,1,'2026-05-04 17:54:02','2026-05-04 17:54:02'),(79,2,'Lakshmipur','Óª▓ÓªòÓºìÓªÀÓºìÓª«ÓºÇÓª¬ÓºüÓª░',15,1,'2026-05-04 17:54:02','2026-05-04 17:54:02'),(80,2,'Noakhali','Óª¿ÓºïÓª»Óª╝Óª¥ÓªûÓª¥Óª▓ÓºÇ',16,1,'2026-05-04 17:54:02','2026-05-04 17:54:02'),(81,2,'Rangamati','Óª░Óª¥ÓªÖÓºìÓªùÓª¥Óª«Óª¥ÓªƒÓª┐',17,1,'2026-05-04 17:54:02','2026-05-04 17:54:02'),(82,3,'Dhaka','ÓªóÓª¥ÓªòÓª¥',18,1,'2026-05-04 17:54:02','2026-05-04 17:54:02'),(83,3,'Faridpur','Óª½Óª░Óª┐ÓªªÓª¬ÓºüÓª░',19,1,'2026-05-04 17:54:02','2026-05-04 17:54:02'),(84,3,'Gazipur','ÓªùÓª¥Óª£ÓºÇÓª¬ÓºüÓª░',20,1,'2026-05-04 17:54:02','2026-05-04 17:54:02'),(85,3,'Gopalganj','ÓªùÓºïÓª¬Óª¥Óª▓ÓªùÓª×ÓºìÓª£',21,1,'2026-05-04 17:54:02','2026-05-04 17:54:02'),(86,3,'Kishoreganj','ÓªòÓª┐ÓªÂÓºïÓª░ÓªùÓª×ÓºìÓª£',22,1,'2026-05-04 17:54:02','2026-05-04 17:54:02'),(87,3,'Madaripur','Óª«Óª¥ÓªªÓª¥Óª░ÓºÇÓª¬ÓºüÓª░',23,1,'2026-05-04 17:54:02','2026-05-04 17:54:02'),(88,3,'Manikganj','Óª«Óª¥Óª¿Óª┐ÓªòÓªùÓª×ÓºìÓª£',24,1,'2026-05-04 17:54:02','2026-05-04 17:54:02'),(89,3,'Munshiganj','Óª«ÓºüÓª¿ÓºìÓª©Óª┐ÓªùÓª×ÓºìÓª£',25,1,'2026-05-04 17:54:02','2026-05-04 17:54:02'),(90,3,'Narayanganj','Óª¿Óª¥Óª░Óª¥Óª»Óª╝ÓªúÓªùÓª×ÓºìÓª£',26,1,'2026-05-04 17:54:02','2026-05-04 17:54:02'),(91,3,'Narsingdi','Óª¿Óª░Óª©Óª┐ÓªéÓªªÓºÇ',27,1,'2026-05-04 17:54:02','2026-05-04 17:54:02'),(92,3,'Rajbari','Óª░Óª¥Óª£Óª¼Óª¥ÓªíÓª╝ÓºÇ',28,1,'2026-05-04 17:54:02','2026-05-04 17:54:02'),(93,3,'Shariatpur','ÓªÂÓª░ÓºÇÓª»Óª╝ÓªñÓª¬ÓºüÓª░',29,1,'2026-05-04 17:54:02','2026-05-04 17:54:02'),(94,3,'Tangail','ÓªƒÓª¥ÓªÖÓºìÓªùÓª¥ÓªçÓª▓',30,1,'2026-05-04 17:54:02','2026-05-04 17:54:02'),(95,4,'Bagerhat','Óª¼Óª¥ÓªùÓºçÓª░Óª╣Óª¥Óªƒ',31,1,'2026-05-04 17:54:02','2026-05-04 17:54:02'),(96,4,'Chuadanga','ÓªÜÓºüÓª»Óª╝Óª¥ÓªíÓª¥ÓªÖÓºìÓªùÓª¥',32,1,'2026-05-04 17:54:02','2026-05-04 17:54:02'),(97,4,'Jessore','Óª»ÓªÂÓºïÓª░',33,1,'2026-05-04 17:54:02','2026-05-04 17:54:02'),(98,4,'Jhenaidah','ÓªØÓª┐Óª¿Óª¥ÓªçÓªªÓª╣',34,1,'2026-05-04 17:54:02','2026-05-04 17:54:02'),(99,4,'Khulna','ÓªûÓºüÓª▓Óª¿Óª¥',35,1,'2026-05-04 17:54:02','2026-05-04 17:54:02'),(100,4,'Kushtia','ÓªòÓºüÓªÀÓºìÓªƒÓª┐Óª»Óª╝Óª¥',36,1,'2026-05-04 17:54:02','2026-05-04 17:54:02'),(101,4,'Magura','Óª«Óª¥ÓªùÓºüÓª░Óª¥',37,1,'2026-05-04 17:54:02','2026-05-04 17:54:02'),(102,4,'Meherpur','Óª«ÓºçÓª╣ÓºçÓª░Óª¬ÓºüÓª░',38,1,'2026-05-04 17:54:02','2026-05-04 17:54:02'),(103,4,'Narail','Óª¿ÓªíÓª╝Óª¥ÓªçÓª▓',39,1,'2026-05-04 17:54:02','2026-05-04 17:54:02'),(104,4,'Satkhira','Óª©Óª¥ÓªñÓªòÓºìÓªÀÓºÇÓª░Óª¥',40,1,'2026-05-04 17:54:02','2026-05-04 17:54:02'),(105,5,'Jamalpur','Óª£Óª¥Óª«Óª¥Óª▓Óª¬ÓºüÓª░',41,1,'2026-05-04 17:54:02','2026-05-04 17:54:02'),(106,5,'Mymensingh','Óª«Óª»Óª╝Óª«Óª¿Óª©Óª┐ÓªéÓª╣',42,1,'2026-05-04 17:54:02','2026-05-04 17:54:02'),(107,5,'Netrokona','Óª¿ÓºçÓªñÓºìÓª░ÓªòÓºïÓªúÓª¥',43,1,'2026-05-04 17:54:02','2026-05-04 17:54:02'),(108,5,'Sherpur','ÓªÂÓºçÓª░Óª¬ÓºüÓª░',44,1,'2026-05-04 17:54:02','2026-05-04 17:54:02'),(109,6,'Bogra','Óª¼ÓªùÓºüÓªíÓª╝Óª¥',45,1,'2026-05-04 17:54:02','2026-05-04 17:54:02'),(110,6,'Joypurhat','Óª£Óª»Óª╝Óª¬ÓºüÓª░Óª╣Óª¥Óªƒ',46,1,'2026-05-04 17:54:02','2026-05-04 17:54:02'),(111,6,'Naogaon','Óª¿ÓªôÓªùÓª¥Óªü',47,1,'2026-05-04 17:54:02','2026-05-04 17:54:02'),(112,6,'Natore','Óª¿Óª¥ÓªƒÓºïÓª░',48,1,'2026-05-04 17:54:02','2026-05-04 17:54:02'),(113,6,'Chapainawabganj','ÓªÜÓª¥ÓªüÓª¬Óª¥ÓªçÓª¿Óª¼Óª¥Óª¼ÓªùÓª×ÓºìÓª£',49,1,'2026-05-04 17:54:02','2026-05-04 17:54:02'),(114,6,'Pabna','Óª¬Óª¥Óª¼Óª¿Óª¥',50,1,'2026-05-04 17:54:02','2026-05-04 17:54:02'),(115,6,'Rajshahi','Óª░Óª¥Óª£ÓªÂÓª¥Óª╣ÓºÇ',51,1,'2026-05-04 17:54:02','2026-05-04 17:54:02'),(116,6,'Sirajganj','Óª©Óª┐Óª░Óª¥Óª£ÓªùÓª×ÓºìÓª£',52,1,'2026-05-04 17:54:02','2026-05-04 17:54:02'),(117,7,'Dinajpur','ÓªªÓª┐Óª¿Óª¥Óª£Óª¬ÓºüÓª░',53,1,'2026-05-04 17:54:02','2026-05-04 17:54:02'),(118,7,'Gaibandha','ÓªùÓª¥ÓªçÓª¼Óª¥Óª¿ÓºìÓªºÓª¥',54,1,'2026-05-04 17:54:02','2026-05-04 17:54:02'),(119,7,'Kurigram','ÓªòÓºüÓªíÓª╝Óª┐ÓªùÓºìÓª░Óª¥Óª«',55,1,'2026-05-04 17:54:02','2026-05-04 17:54:02'),(120,7,'Lalmonirhat','Óª▓Óª¥Óª▓Óª«Óª¿Óª┐Óª░Óª╣Óª¥Óªƒ',56,1,'2026-05-04 17:54:02','2026-05-04 17:54:02'),(121,7,'Nilphamari','Óª¿ÓºÇÓª▓Óª½Óª¥Óª«Óª¥Óª░ÓºÇ',57,1,'2026-05-04 17:54:02','2026-05-04 17:54:02'),(122,7,'Panchagarh','Óª¬Óª×ÓºìÓªÜÓªùÓªíÓª╝',58,1,'2026-05-04 17:54:02','2026-05-04 17:54:02'),(123,7,'Rangpur','Óª░ÓªéÓª¬ÓºüÓª░',59,1,'2026-05-04 17:54:02','2026-05-04 17:54:02'),(124,7,'Thakurgaon','ÓªáÓª¥ÓªòÓºüÓª░ÓªùÓª¥ÓªüÓªô',60,1,'2026-05-04 17:54:02','2026-05-04 17:54:02'),(125,8,'Habiganj','Óª╣Óª¼Óª┐ÓªùÓª×ÓºìÓª£',61,1,'2026-05-04 17:54:02','2026-05-04 17:54:02'),(126,8,'Moulvibazar','Óª«ÓºîÓª▓Óª¡ÓºÇÓª¼Óª¥Óª£Óª¥Óª░',62,1,'2026-05-04 17:54:02','2026-05-04 17:54:02'),(127,8,'Sunamganj','Óª©ÓºüÓª¿Óª¥Óª«ÓªùÓª×ÓºìÓª£',63,1,'2026-05-04 17:54:02','2026-05-04 17:54:02'),(128,8,'Sylhet','Óª©Óª┐Óª▓ÓºçÓªƒ',64,1,'2026-05-04 17:54:02','2026-05-04 17:54:02');
/*!40000 ALTER TABLE `districts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `divisions`
--

DROP TABLE IF EXISTS `divisions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `divisions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name_en` varchar(100) NOT NULL,
  `name_bn` varchar(100) NOT NULL,
  `sort_order` tinyint(3) unsigned DEFAULT 0,
  `status` tinyint(3) unsigned NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `divisions`
--

LOCK TABLES `divisions` WRITE;
/*!40000 ALTER TABLE `divisions` DISABLE KEYS */;
INSERT INTO `divisions` VALUES (1,'Barishal','বরিশাল',1,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(2,'Chattogram','চট্টগ্রাম',2,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(3,'Dhaka','ঢাকা',3,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(4,'Khulna','খুলনা',4,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(5,'Mymensingh','ময়মনসিংহ',5,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(6,'Rajshahi','রাজশাহী',6,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(7,'Rangpur','রংপুর',7,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(8,'Sylhet','সিলেট',8,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(9,'Barishal','Óª¼Óª░Óª┐ÓªÂÓª¥Óª▓',1,1,'2026-05-04 17:54:02','2026-05-04 17:54:02'),(10,'Chattogram','ÓªÜÓªƒÓºìÓªƒÓªùÓºìÓª░Óª¥Óª«',2,1,'2026-05-04 17:54:02','2026-05-04 17:54:02'),(11,'Dhaka','ÓªóÓª¥ÓªòÓª¥',3,1,'2026-05-04 17:54:02','2026-05-04 17:54:02'),(12,'Khulna','ÓªûÓºüÓª▓Óª¿Óª¥',4,1,'2026-05-04 17:54:02','2026-05-04 17:54:02'),(13,'Mymensingh','Óª«Óª»Óª╝Óª«Óª¿Óª©Óª┐ÓªéÓª╣',5,1,'2026-05-04 17:54:02','2026-05-04 17:54:02'),(14,'Rajshahi','Óª░Óª¥Óª£ÓªÂÓª¥Óª╣ÓºÇ',6,1,'2026-05-04 17:54:02','2026-05-04 17:54:02'),(15,'Rangpur','Óª░ÓªéÓª¬ÓºüÓª░',7,1,'2026-05-04 17:54:02','2026-05-04 17:54:02'),(16,'Sylhet','Óª©Óª┐Óª▓ÓºçÓªƒ',8,1,'2026-05-04 17:54:02','2026-05-04 17:54:02');
/*!40000 ALTER TABLE `divisions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `extensions`
--

DROP TABLE IF EXISTS `extensions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `extensions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `act` varchar(40) DEFAULT NULL,
  `name` varchar(40) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `script` text DEFAULT NULL,
  `shortcode` text DEFAULT NULL COMMENT 'object',
  `support` varchar(50) DEFAULT NULL COMMENT 'help section',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1=>enable, 2=>disable',
  `version` varchar(32) DEFAULT NULL,
  `dependency` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Required extensions/versions' CHECK (json_valid(`dependency`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `last_updated` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `extensions`
--

LOCK TABLES `extensions` WRITE;
/*!40000 ALTER TABLE `extensions` DISABLE KEYS */;
INSERT INTO `extensions` VALUES (1,'tawk-chat','Tawk.to','Key location is shown bellow','tawky_big.png','<script>\r\n                        var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();\r\n                        (function(){\r\n                        var s1=document.createElement(\"script\"),s0=document.getElementsByTagName(\"script\")[0];\r\n                        s1.async=true;\r\n                        s1.src=\"https://embed.tawk.to/{{app_key}}\";\r\n                        s1.charset=\"UTF-8\";\r\n                        s1.setAttribute(\"crossorigin\",\"*\");\r\n                        s0.parentNode.insertBefore(s1,s0);\r\n                        })();\r\n                    </script>','{\"app_key\":{\"title\":\"App Key\",\"value\":\"7bc9bba6630077ec6c046af143c1c7fd6d6e9462\"}}','twak.png',1,NULL,NULL,'2019-10-18 23:16:05','2025-10-16 02:18:44',NULL),(2,'google-recaptcha2','Google Recaptcha 2','Key location is shown bellow','recaptcha3.png','\n<script src=\"https://www.google.com/recaptcha/api.js\"></script>\n<div class=\"g-recaptcha\" data-sitekey=\"{{site_key}}\" data-callback=\"verifyCaptcha\"></div>\n<div id=\"g-recaptcha-error\"></div>','{\"site_key\":{\"title\":\"Site Key\",\"value\":\"\"},\"secret_key\":{\"title\":\"Secret Key\",\"value\":\"\"}}','recaptcha.png',0,NULL,NULL,'2019-10-18 23:16:05','2023-05-30 07:34:50',NULL),(3,'custom-captcha','Custom Captcha','Just put any random string','customcaptcha.png',NULL,'{\"random_key\":{\"title\":\"Random String\",\"value\":\"SecureString\"}}','na',0,NULL,NULL,'2019-10-18 23:16:05','2023-05-29 08:25:25',NULL),(4,'google-analytics','Google Analytics','Key location is shown bellow','google_analytics.png','<script async src=\"https://www.googletagmanager.com/gtag/js?id={{app_key}}\"></script>\r\n                <script>\r\n                  window.dataLayer = window.dataLayer || [];\r\n                  function gtag(){dataLayer.push(arguments);}\r\n                  gtag(\"js\", new Date());\r\n                \r\n                  gtag(\"config\", \"{{app_key}}\");\r\n                </script>','{\"app_key\":{\"title\":\"App Key\",\"value\":\"------\"}}','ganalytics.png',0,NULL,NULL,NULL,'2021-05-04 10:19:12',NULL),(7,'facebook-pixel','Facebook Pixel','Track conversions, build audiences and get detailed analytics. Paste your Pixel ID from Facebook Events Manager.','Facebook.png','<!-- Facebook Pixel -->\n<script>\n!function(f,b,e,v,n,t,s)\n{if(f.fbq)return;n=f.fbq=function(){n.callMethod?n.callMethod.apply(n,arguments):n.queue.push(arguments)};\nif(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version=\'2.0\';\nn.queue=[];t=b.createElement(e);t.async=!0;\nt.src=v;s=b.getElementsByTagName(e)[0];\ns.parentNode.insertBefore(t,s)}(window, document,\'script\',\n\'https://connect.facebook.net/en_US/fbevents.js\');\nfbq(\'init\', \'{{pixel_id}}\');\nfbq(\'track\', \'PageView\');\n</script>\n<noscript><img height=\"1\" width=\"1\" style=\"display:none\" src=\"https://www.facebook.com/tr?id={{pixel_id}}&ev=PageView&noscript=1\"/></noscript>','{\"pixel_id\":{\"title\":\"Pixel ID\",\"value\":\"\"}}','na',0,NULL,NULL,'2026-02-11 07:37:07','2026-02-11 07:37:07',NULL),(8,'gtag-manager','Google Tag Manager','Manage all your tracking and marketing tags from one place. Enter your GTM container ID (e.g. GTM-XXXXXXX).','google_analytics.png','<!-- Google Tag Manager -->\n<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({\'gtm.start\':new Date().getTime(),event:\'gtm.js\'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!=\'dataLayer\'?\'&l=\'+l:\'\';j.async=true;j.src=\'https://www.googletagmanager.com/gtm.js?id=\'+i+dl;f.parentNode.insertBefore(j,f);})(window,document,\'script\',\'dataLayer\',\'{{container_id}}\');</script>','{\"container_id\":{\"title\":\"GTM Container ID (e.g. GTM-XXXXXXX)\",\"value\":\"\"}}','na',0,NULL,NULL,'2026-02-11 07:37:07','2026-02-11 07:37:07',NULL),(9,'recaptcha3','Google reCAPTCHA v3','Invisible reCAPTCHA that scores user interaction. Get keys from Google reCAPTCHA admin (v3).','recaptcha3.png','<script src=\"https://www.google.com/recaptcha/api.js?render={{site_key}}\"></script>\n<script>window.recaptchaSiteKeyV3=\"{{site_key}}\";</script>','{\"site_key\":{\"title\":\"Site Key (reCAPTCHA v3)\",\"value\":\"\"},\"secret_key\":{\"title\":\"Secret Key (for server-side verify)\",\"value\":\"\"}}','recaptcha.png',0,NULL,NULL,'2026-02-11 07:37:07','2026-02-11 07:37:07',NULL),(10,'custom-code','Custom Code (HTML/JS)','Paste any custom HTML or JavaScript (e.g. chat widgets, tracking scripts). For advanced users only.','ganalytics.png','{{custom_script}}','{\"custom_script\":{\"title\":\"Custom HTML/JS\",\"value\":\"\"}}','na',0,NULL,NULL,'2026-02-11 07:37:07','2026-02-11 07:37:07',NULL);
/*!40000 ALTER TABLE `extensions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `forms`
--

DROP TABLE IF EXISTS `forms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `forms` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `act` varchar(40) DEFAULT NULL,
  `form_data` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `forms`
--

LOCK TABLES `forms` WRITE;
/*!40000 ALTER TABLE `forms` DISABLE KEYS */;
INSERT INTO `forms` VALUES (1,'manual_deposit','[]','2025-10-10 12:27:26','2025-10-10 12:27:26');
/*!40000 ALTER TABLE `forms` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fraud_blocks`
--

DROP TABLE IF EXISTS `fraud_blocks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fraud_blocks` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(20) NOT NULL,
  `value` varchar(100) NOT NULL,
  `reason` varchar(500) DEFAULT NULL,
  `blocked_by_admin_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `fraud_blocks_type_value_unique` (`type`,`value`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fraud_blocks`
--

LOCK TABLES `fraud_blocks` WRITE;
/*!40000 ALTER TABLE `fraud_blocks` DISABLE KEYS */;
/*!40000 ALTER TABLE `fraud_blocks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fraud_complaints`
--

DROP TABLE IF EXISTS `fraud_complaints`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fraud_complaints` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `reported_by_admin_id` bigint(20) unsigned DEFAULT NULL,
  `reason` varchar(500) DEFAULT NULL,
  `status` varchar(30) NOT NULL DEFAULT 'open',
  `admin_note` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fraud_complaints_order_id_status_index` (`order_id`,`status`),
  CONSTRAINT `fraud_complaints_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fraud_complaints`
--

LOCK TABLES `fraud_complaints` WRITE;
/*!40000 ALTER TABLE `fraud_complaints` DISABLE KEYS */;
/*!40000 ALTER TABLE `fraud_complaints` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `frontends`
--

DROP TABLE IF EXISTS `frontends`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `frontends` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `data_keys` varchar(40) DEFAULT NULL,
  `data_values` longtext DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=163 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `frontends`
--

LOCK TABLES `frontends` WRITE;
/*!40000 ALTER TABLE `frontends` DISABLE KEYS */;
INSERT INTO `frontends` VALUES (1,'seo.data','{\"seo_image\":\"1\",\"keywords\":[\"dealshop\",\"e-commerce\",\"online shopping platfrom\",\"product\",\"discount\"],\"description\":\"Discover a world of endless possibilities at our eCommerce store. Shop the latest trends, find exclusive deals, and indulge in a seamless shopping experience. Explore a wide range of products, from fashion and electronics to home decor and more. With fast shipping and secure transactions, we\'re here to make your online shopping dreams come true. Start browsing now and unlock a world of convenience at our DealShop.\",\"social_title\":\"DealShop - Online E-commerce Shopping Platform\",\"social_description\":\"Discover a world of endless possibilities at our eCommerce store. Shop the latest trends, find exclusive deals, and indulge in a seamless shopping experience. Explore a wide range of products, from fashion and electronics to home decor and more. With fast shipping and secure transactions, we\'re here to make your online shopping dreams come true. Start browsing now and unlock a world of convenience at our DealShop.\",\"image\":\"64759c7b3443b1685429371.png\"}','2020-07-04 23:42:52','2023-05-30 05:39:52'),(27,'contact_us.content','{\"has_image\": \"1\", \"title\": \"Get in Touch Us\", \"subtitle\": \"Lorem ipsum dolor sit amet consectetur adipisicing elit. Sit eveniet soluta, nihil est\", \"contact_number\": \"01307644289\", \"contact_email\": \"contact@dealshop.com\", \"address\": \"4901 Seminary Rd #120,Alexandria,Vermont USA\", \"whatsapp_number\": \"\", \"telegram_username\": \"\", \"image\": \"63fb2824d83781677404196.png\"}','2020-10-28 00:59:19','2025-10-16 02:47:27'),(33,'feature.content','{\"heading\":\"asdf\",\"sub_heading\":\"asdf\"}','2021-01-03 23:40:54','2021-01-03 23:40:55'),(34,'feature.element','{\"title\":\"asdf\",\"description\":\"asdf\",\"feature_icon\":\"asdf\"}','2021-01-03 23:41:02','2021-01-03 23:41:02'),(36,'service.content','{\"trx_type\":\"deposit\",\"heading\":\"asdf fffff\",\"subheading\":\"555\"}','2021-03-06 01:27:34','2022-03-30 08:07:06'),(39,'banner.content','{\"slide_interval_seconds\":3,\"autoplay\":1,\"banner_width\":\"2560\",\"banner_height\":\"400\",\"heading\":\"Latest News\",\"sub_heading\":\"Lorem ipsum dolor sit, amet consectetur adipisicing elit. Esse voluptatum eaque earum quos quia? Id aspernatur ratione, voluptas nulla rerum laudantium neque ipsam eaque\"}','2021-05-02 06:09:30','2026-03-06 00:04:17'),(41,'cookie.data','{\"short_desc\":\"We may use cookies or any other tracking technologies when you visit our website, including any other media form, mobile website, or mobile application related or connected to help customize the Site and improve your experience.\",\"description\":\"<div class=\\\"mb-5\\\" style=\\\"color: rgb(111, 111, 111); font-family: Nunito, sans-serif; margin-bottom: 3rem !important;\\\" bis_skin_checked=\\\"1\\\"><h3 class=\\\"mb-3\\\" style=\\\"font-weight: 600; line-height: 1.3; font-size: 24px; font-family: Exo, sans-serif; color: rgb(54, 54, 54);\\\">What information do we collect?<\\/h3><p class=\\\"font-18\\\" style=\\\"margin-right: 0px; margin-left: 0px; font-size: 18px !important;\\\">We gather data from you when you register on our site, submit a request, buy any services, react to an overview, or round out a structure. At the point when requesting any assistance or enrolling on our site, as suitable, you might be approached to enter your: name, email address, or telephone number. You may, nonetheless, visit our site anonymously.<\\/p><\\/div><div class=\\\"mb-5\\\" style=\\\"color: rgb(111, 111, 111); font-family: Nunito, sans-serif; margin-bottom: 3rem !important;\\\" bis_skin_checked=\\\"1\\\"><h3 class=\\\"mb-3\\\" style=\\\"font-weight: 600; line-height: 1.3; font-size: 24px; font-family: Exo, sans-serif; color: rgb(54, 54, 54);\\\">How do we protect your information?<\\/h3><p class=\\\"font-18\\\" style=\\\"margin-right: 0px; margin-left: 0px; font-size: 18px !important;\\\">All provided delicate\\/credit data is sent through Stripe.<br>After an exchange, your private data (credit cards, social security numbers, financials, and so on) won\'t be put away on our workers.<\\/p><\\/div><div class=\\\"mb-5\\\" style=\\\"color: rgb(111, 111, 111); font-family: Nunito, sans-serif; margin-bottom: 3rem !important;\\\" bis_skin_checked=\\\"1\\\"><h3 class=\\\"mb-3\\\" style=\\\"font-weight: 600; line-height: 1.3; font-size: 24px; font-family: Exo, sans-serif; color: rgb(54, 54, 54);\\\">Do we disclose any information to outside parties?<\\/h3><p class=\\\"font-18\\\" style=\\\"margin-right: 0px; margin-left: 0px; font-size: 18px !important;\\\">We don\'t sell, exchange, or in any case move to outside gatherings by and by recognizable data. This does exclude confided in outsiders who help us in working our site, leading our business, or adjusting you, since those gatherings consent to keep this data private. We may likewise deliver your data when we accept discharge is suitable to follow the law, implement our site strategies, or ensure our own or others\' rights, property, or wellbeing.<\\/p><\\/div><div class=\\\"mb-5\\\" style=\\\"color: rgb(111, 111, 111); font-family: Nunito, sans-serif; margin-bottom: 3rem !important;\\\" bis_skin_checked=\\\"1\\\"><h3 class=\\\"mb-3\\\" style=\\\"font-weight: 600; line-height: 1.3; font-size: 24px; font-family: Exo, sans-serif; color: rgb(54, 54, 54);\\\">Children\'s Online Privacy Protection Act Compliance<\\/h3><p class=\\\"font-18\\\" style=\\\"margin-right: 0px; margin-left: 0px; font-size: 18px !important;\\\">We are consistent with the prerequisites of COPPA (Children\'s Online Privacy Protection Act), we don\'t gather any data from anybody under 13 years old. Our site, items, and administrations are completely coordinated to individuals who are in any event 13 years of age or more established.<\\/p><\\/div><div class=\\\"mb-5\\\" style=\\\"color: rgb(111, 111, 111); font-family: Nunito, sans-serif; margin-bottom: 3rem !important;\\\" bis_skin_checked=\\\"1\\\"><h3 class=\\\"mb-3\\\" style=\\\"font-weight: 600; line-height: 1.3; font-size: 24px; font-family: Exo, sans-serif; color: rgb(54, 54, 54);\\\">Changes to our Privacy Policy<\\/h3><p class=\\\"font-18\\\" style=\\\"margin-right: 0px; margin-left: 0px; font-size: 18px !important;\\\">If we decide to change our privacy policy, we will post those changes on this page.<\\/p><\\/div><div class=\\\"mb-5\\\" style=\\\"color: rgb(111, 111, 111); font-family: Nunito, sans-serif; margin-bottom: 3rem !important;\\\" bis_skin_checked=\\\"1\\\"><h3 class=\\\"mb-3\\\" style=\\\"font-weight: 600; line-height: 1.3; font-size: 24px; font-family: Exo, sans-serif; color: rgb(54, 54, 54);\\\">How long we retain your information?<\\/h3><p class=\\\"font-18\\\" style=\\\"margin-right: 0px; margin-left: 0px; font-size: 18px !important;\\\">At the point when you register for our site, we cycle and keep your information we have about you however long you don\'t erase the record or withdraw yourself (subject to laws and guidelines).<\\/p><\\/div><div class=\\\"mb-5\\\" style=\\\"color: rgb(111, 111, 111); font-family: Nunito, sans-serif; margin-bottom: 3rem !important;\\\" bis_skin_checked=\\\"1\\\"><h3 class=\\\"mb-3\\\" style=\\\"font-weight: 600; line-height: 1.3; font-size: 24px; font-family: Exo, sans-serif; color: rgb(54, 54, 54);\\\">What we don\\u2019t do with your data<\\/h3><p class=\\\"font-18\\\" style=\\\"margin-right: 0px; margin-left: 0px; font-size: 18px !important;\\\">We don\'t and will never share, unveil, sell, or in any case give your information to different organizations for the promoting of their items or administrations.<\\/p><\\/div>\",\"status\":1}','2020-07-04 23:42:52','2025-10-10 11:13:47'),(42,'policy_pages.element','{\"title\":\"Privacy Policy\",\"details\":\"<div class=\\\"mb-5\\\" style=\\\"color:rgb(111,111,111);font-family:Nunito, sans-serif;margin-bottom:3rem;\\\"><h3 class=\\\"mb-3\\\" style=\\\"font-weight:600;line-height:1.3;font-size:24px;font-family:Exo, sans-serif;color:rgb(54,54,54);\\\">What information do we collect?<\\/h3><p class=\\\"font-18\\\" style=\\\"margin-right:0px;margin-left:0px;font-size:18px;\\\">We gather data from you when you register on our site, submit a request, buy any services, react to an overview, or round out a structure. At the point when requesting any assistance or enrolling on our site, as suitable, you might be approached to enter your: name, email address, or telephone number. You may, nonetheless, visit our site anonymously.<\\/p><\\/div><div class=\\\"mb-5\\\" style=\\\"color:rgb(111,111,111);font-family:Nunito, sans-serif;margin-bottom:3rem;\\\"><h3 class=\\\"mb-3\\\" style=\\\"font-weight:600;line-height:1.3;font-size:24px;font-family:Exo, sans-serif;color:rgb(54,54,54);\\\">How do we protect your information?<\\/h3><p class=\\\"font-18\\\" style=\\\"margin-right:0px;margin-left:0px;font-size:18px;\\\">All provided delicate\\/credit data is sent through Stripe.<br \\/>After an exchange, your private data (credit cards, social security numbers, financials, and so on) won\'t be put away on our workers.<\\/p><\\/div><div class=\\\"mb-5\\\" style=\\\"color:rgb(111,111,111);font-family:Nunito, sans-serif;margin-bottom:3rem;\\\"><h3 class=\\\"mb-3\\\" style=\\\"font-weight:600;line-height:1.3;font-size:24px;font-family:Exo, sans-serif;color:rgb(54,54,54);\\\">Do we disclose any information to outside parties?<\\/h3><p class=\\\"font-18\\\" style=\\\"margin-right:0px;margin-left:0px;font-size:18px;\\\">We don\'t sell, exchange, or in any case move to outside gatherings by and by recognizable data. This does exclude confided in outsiders who help us in working our site, leading our business, or adjusting you, since those gatherings consent to keep this data private. We may likewise deliver your data when we accept discharge is suitable to follow the law, implement our site strategies, or ensure our own or others\' rights, property, or wellbeing.<\\/p><\\/div><div class=\\\"mb-5\\\" style=\\\"color:rgb(111,111,111);font-family:Nunito, sans-serif;margin-bottom:3rem;\\\"><h3 class=\\\"mb-3\\\" style=\\\"font-weight:600;line-height:1.3;font-size:24px;font-family:Exo, sans-serif;color:rgb(54,54,54);\\\">Children\'s Online Privacy Protection Act Compliance<\\/h3><p class=\\\"font-18\\\" style=\\\"margin-right:0px;margin-left:0px;font-size:18px;\\\">We are consistent with the prerequisites of COPPA (Children\'s Online Privacy Protection Act), we don\'t gather any data from anybody under 13 years old. Our site, items, and administrations are completely coordinated to individuals who are in any event 13 years of age or more established.<\\/p><\\/div><div class=\\\"mb-5\\\" style=\\\"color:rgb(111,111,111);font-family:Nunito, sans-serif;margin-bottom:3rem;\\\"><h3 class=\\\"mb-3\\\" style=\\\"font-weight:600;line-height:1.3;font-size:24px;font-family:Exo, sans-serif;color:rgb(54,54,54);\\\">Changes to our Privacy Policy<\\/h3><p class=\\\"font-18\\\" style=\\\"margin-right:0px;margin-left:0px;font-size:18px;\\\">If we decide to change our privacy policy, we will post those changes on this page.<\\/p><\\/div><div class=\\\"mb-5\\\" style=\\\"color:rgb(111,111,111);font-family:Nunito, sans-serif;margin-bottom:3rem;\\\"><h3 class=\\\"mb-3\\\" style=\\\"font-weight:600;line-height:1.3;font-size:24px;font-family:Exo, sans-serif;color:rgb(54,54,54);\\\">How long we retain your information?<\\/h3><p class=\\\"font-18\\\" style=\\\"margin-right:0px;margin-left:0px;font-size:18px;\\\">At the point when you register for our site, we cycle and keep your information we have about you however long you don\'t erase the record or withdraw yourself (subject to laws and guidelines).<\\/p><\\/div><div class=\\\"mb-5\\\" style=\\\"color:rgb(111,111,111);font-family:Nunito, sans-serif;margin-bottom:3rem;\\\"><h3 class=\\\"mb-3\\\" style=\\\"font-weight:600;line-height:1.3;font-size:24px;font-family:Exo, sans-serif;color:rgb(54,54,54);\\\">What we don\\u2019t do with your data<\\/h3><p class=\\\"font-18\\\" style=\\\"margin-right:0px;margin-left:0px;font-size:18px;\\\">We don\'t and will never share, unveil, sell, or in any case give your information to different organizations for the promoting of their items or administrations.<\\/p><\\/div>\"}','2021-06-09 08:50:42','2021-06-09 08:50:42'),(43,'policy_pages.element','{\"title\":\"Terms of Service\",\"details\":\"<div class=\\\"mb-5\\\" style=\\\"color:rgb(111,111,111);font-family:Nunito, sans-serif;margin-bottom:3rem;\\\"><p class=\\\"font-18\\\" style=\\\"margin-right:0px;margin-left:0px;font-size:18px;\\\">We claim all authority to dismiss, end, or handicap any help with or without cause per administrator discretion. This is a Complete independent facilitating, on the off chance that you misuse our ticket or Livechat or emotionally supportive network by submitting solicitations or protests we will impair your record. The solitary time you should reach us about the seaward facilitating is if there is an issue with the worker. We have not many substance limitations and everything is as per laws and guidelines. Try not to join on the off chance that you intend to do anything contrary to the guidelines, we do check these things and we will know, don\'t burn through our own and your time by joining on the off chance that you figure you will have the option to sneak by us and break the terms.<\\/p><\\/div><div class=\\\"mb-5\\\" style=\\\"color:rgb(111,111,111);font-family:Nunito, sans-serif;margin-bottom:3rem;\\\"><ul class=\\\"font-18\\\" style=\\\"padding-left:15px;list-style-type:disc;font-size:18px;\\\"><li style=\\\"margin-top:0px;margin-right:0px;margin-left:0px;\\\">Configuration requests - If you have a fully managed dedicated server with us then we offer custom PHP\\/MySQL configurations, firewalls for dedicated IPs, DNS, and httpd configurations.<\\/li><li style=\\\"margin-top:0px;margin-right:0px;margin-left:0px;\\\">Software requests - Cpanel Extension Installation will be granted as long as it does not interfere with the security, stability, and performance of other users on the server.<\\/li><li style=\\\"margin-top:0px;margin-right:0px;margin-left:0px;\\\">Emergency Support - We do not provide emergency support \\/ Phone Support \\/ LiveChat Support. Support may take some hours sometimes.<\\/li><li style=\\\"margin-top:0px;margin-right:0px;margin-left:0px;\\\">Webmaster help - We do not offer any support for webmaster related issues and difficulty including coding, &amp; installs, Error solving. if there is an issue where a library or configuration of the server then we can help you if it\'s possible from our end.<\\/li><li style=\\\"margin-top:0px;margin-right:0px;margin-left:0px;\\\">Backups - We keep backups but we are not responsible for data loss, you are fully responsible for all backups.<\\/li><li style=\\\"margin-top:0px;margin-right:0px;margin-left:0px;\\\">We Don\'t support any child porn or such material.<\\/li><li style=\\\"margin-top:0px;margin-right:0px;margin-left:0px;\\\">No spam-related sites or material, such as email lists, mass mail programs, and scripts, etc.<\\/li><li style=\\\"margin-top:0px;margin-right:0px;margin-left:0px;\\\">No harassing material that may cause people to retaliate against you.<\\/li><li style=\\\"margin-top:0px;margin-right:0px;margin-left:0px;\\\">No phishing pages.<\\/li><li style=\\\"margin-top:0px;margin-right:0px;margin-left:0px;\\\">You may not run any exploitation script from the server. reason can be terminated immediately.<\\/li><li style=\\\"margin-top:0px;margin-right:0px;margin-left:0px;\\\">If Anyone attempting to hack or exploit the server by using your script or hosting, we will terminate your account to keep safe other users.<\\/li><li style=\\\"margin-top:0px;margin-right:0px;margin-left:0px;\\\">Malicious Botnets are strictly forbidden.<\\/li><li style=\\\"margin-top:0px;margin-right:0px;margin-left:0px;\\\">Spam, mass mailing, or email marketing in any way are strictly forbidden here.<\\/li><li style=\\\"margin-top:0px;margin-right:0px;margin-left:0px;\\\">Malicious hacking materials, trojans, viruses, &amp; malicious bots running or for download are forbidden.<\\/li><li style=\\\"margin-top:0px;margin-right:0px;margin-left:0px;\\\">Resource and cronjob abuse is forbidden and will result in suspension or termination.<\\/li><li style=\\\"margin-top:0px;margin-right:0px;margin-left:0px;\\\">Php\\/CGI proxies are strictly forbidden.<\\/li><li style=\\\"margin-top:0px;margin-right:0px;margin-left:0px;\\\">CGI-IRC is strictly forbidden.<\\/li><li style=\\\"margin-top:0px;margin-right:0px;margin-left:0px;\\\">No fake or disposal mailers, mass mailing, mail bombers, SMS bombers, etc.<\\/li><li style=\\\"margin-top:0px;margin-right:0px;margin-left:0px;\\\">NO CREDIT OR REFUND will be granted for interruptions of service, due to User Agreement violations.<\\/li><\\/ul><\\/div><div class=\\\"mb-5\\\" style=\\\"color:rgb(111,111,111);font-family:Nunito, sans-serif;margin-bottom:3rem;\\\"><h3 class=\\\"mb-3\\\" style=\\\"font-weight:600;line-height:1.3;font-size:24px;font-family:Exo, sans-serif;color:rgb(54,54,54);\\\">Terms &amp; Conditions for Users<\\/h3><p class=\\\"font-18\\\" style=\\\"margin-right:0px;margin-left:0px;font-size:18px;\\\">Before getting to this site, you are consenting to be limited by these site Terms and Conditions of Use, every single appropriate law, and guidelines, and concur that you are answerable for consistency with any material neighborhood laws. If you disagree with any of these terms, you are restricted from utilizing or getting to this site.<\\/p><\\/div><div class=\\\"mb-5\\\" style=\\\"color:rgb(111,111,111);font-family:Nunito, sans-serif;margin-bottom:3rem;\\\"><h3 class=\\\"mb-3\\\" style=\\\"font-weight:600;line-height:1.3;font-size:24px;font-family:Exo, sans-serif;color:rgb(54,54,54);\\\">Support<\\/h3><p class=\\\"font-18\\\" style=\\\"margin-right:0px;margin-left:0px;font-size:18px;\\\">Whenever you have downloaded our item, you may get in touch with us for help through email and we will give a valiant effort to determine your issue. We will attempt to answer using the Email for more modest bug fixes, after which we will refresh the center bundle. Content help is offered to confirmed clients by Tickets as it were. Backing demands made by email and Livechat.<\\/p><p class=\\\"my-3 font-18 font-weight-bold\\\" style=\\\"margin-right:0px;margin-left:0px;font-size:18px;\\\">On the off chance that your help requires extra adjustment of the System, at that point, you have two alternatives:<\\/p><ul class=\\\"font-18\\\" style=\\\"padding-left:15px;list-style-type:disc;font-size:18px;\\\"><li style=\\\"margin-top:0px;margin-right:0px;margin-left:0px;\\\">Hang tight for additional update discharge.<\\/li><li style=\\\"margin-top:0px;margin-right:0px;margin-left:0px;\\\">Or on the other hand, enlist a specialist (We offer customization for extra charges).<\\/li><\\/ul><\\/div><div class=\\\"mb-5\\\" style=\\\"color:rgb(111,111,111);font-family:Nunito, sans-serif;margin-bottom:3rem;\\\"><h3 class=\\\"mb-3\\\" style=\\\"font-weight:600;line-height:1.3;font-size:24px;font-family:Exo, sans-serif;color:rgb(54,54,54);\\\">Ownership<\\/h3><p class=\\\"font-18\\\" style=\\\"margin-right:0px;margin-left:0px;font-size:18px;\\\">You may not guarantee scholarly or selective possession of any of our items, altered or unmodified. All items are property, we created them. Our items are given \\\"with no guarantees\\\" without guarantee of any sort, either communicated or suggested. On no occasion will our juridical individual be subject to any harms including, however not restricted to, immediate, roundabout, extraordinary, accidental, or significant harms or different misfortunes emerging out of the utilization of or powerlessness to utilize our items.<\\/p><\\/div><div class=\\\"mb-5\\\" style=\\\"color:rgb(111,111,111);font-family:Nunito, sans-serif;margin-bottom:3rem;\\\"><h3 class=\\\"mb-3\\\" style=\\\"font-weight:600;line-height:1.3;font-size:24px;font-family:Exo, sans-serif;color:rgb(54,54,54);\\\">Warranty<\\/h3><p class=\\\"font-18\\\" style=\\\"margin-right:0px;margin-left:0px;font-size:18px;\\\">We don\'t offer any guarantee or assurance of these Services in any way. When our Services have been modified we can\'t ensure they will work with all outsider plugins, modules, or internet browsers. Program similarity ought to be tried against the show formats on the demo worker. If you don\'t mind guarantee that the programs you use will work with the component, as we can not ensure that our systems will work with all program mixes.<\\/p><\\/div><div class=\\\"mb-5\\\" style=\\\"color:rgb(111,111,111);font-family:Nunito, sans-serif;margin-bottom:3rem;\\\"><h3 class=\\\"mb-3\\\" style=\\\"font-weight:600;line-height:1.3;font-size:24px;font-family:Exo, sans-serif;color:rgb(54,54,54);\\\">Unauthorized\\/Illegal Usage<\\/h3><p class=\\\"font-18\\\" style=\\\"margin-right:0px;margin-left:0px;font-size:18px;\\\">You may not utilize our things for any illicit or unapproved reason or may you, in the utilization of the stage, disregard any laws in your locale (counting yet not restricted to copyright laws) just as the laws of your nation and International law. Specifically, it is disallowed to utilize the things on our foundation for pages that advance: brutality, illegal intimidation, hard sexual entertainment, bigotry, obscenity content or warez programming joins.<br \\/><br \\/>You can\'t imitate, copy, duplicate, sell, exchange or adventure any of our segment, utilization of the offered on our things, or admittance to the administration without the express composed consent by us or item proprietor.<br \\/><br \\/>Our Members are liable for all substance posted on the discussion and demo and movement that happens under your record.<br \\/><br \\/>We hold the chance of hindering your participation account quickly if we will think about a particularly not allowed conduct.<br \\/><br \\/>If you make a record on our site, you are liable for keeping up the security of your record, and you are completely answerable for all exercises that happen under the record and some other activities taken regarding the record. You should quickly inform us, of any unapproved employments of your record or some other penetrates of security.<\\/p><\\/div><div class=\\\"mb-5\\\" style=\\\"color:rgb(111,111,111);font-family:Nunito, sans-serif;margin-bottom:3rem;\\\"><h3 class=\\\"mb-3\\\" style=\\\"font-weight:600;line-height:1.3;font-size:24px;font-family:Exo, sans-serif;color:rgb(54,54,54);\\\">Fiverr, Seoclerks Sellers Or Affiliates<\\/h3><p class=\\\"font-18\\\" style=\\\"margin-right:0px;margin-left:0px;font-size:18px;\\\">We do NOT ensure full SEO campaign conveyance within 24 hours. We make no assurance for conveyance time by any means. We give our best assessment to orders during the putting in of requests, anyway, these are gauges. We won\'t be considered liable for loss of assets, negative surveys or you being prohibited for late conveyance. If you are selling on a site that requires time touchy outcomes, utilize Our SEO Services at your own risk.<\\/p><\\/div><div class=\\\"mb-5\\\" style=\\\"color:rgb(111,111,111);font-family:Nunito, sans-serif;margin-bottom:3rem;\\\"><h3 class=\\\"mb-3\\\" style=\\\"font-weight:600;line-height:1.3;font-size:24px;font-family:Exo, sans-serif;color:rgb(54,54,54);\\\">Payment\\/Refund Policy<\\/h3><p class=\\\"font-18\\\" style=\\\"margin-right:0px;margin-left:0px;font-size:18px;\\\">No refund or cash back will be made. After a deposit has been finished, it is extremely unlikely to invert it. You should utilize your equilibrium on requests our administrations, Hosting, SEO campaign. You concur that once you complete a deposit, you won\'t document a debate or a chargeback against us in any way, shape, or form.<br \\/><br \\/>If you document a debate or chargeback against us after a deposit, we claim all authority to end every single future request, prohibit you from our site. False action, for example, utilizing unapproved or taken charge cards will prompt the end of your record. There are no special cases.<\\/p><\\/div><div class=\\\"mb-5\\\" style=\\\"color:rgb(111,111,111);font-family:Nunito, sans-serif;margin-bottom:3rem;\\\"><h3 class=\\\"mb-3\\\" style=\\\"font-weight:600;line-height:1.3;font-size:24px;font-family:Exo, sans-serif;color:rgb(54,54,54);\\\">Free Balance \\/ Coupon Policy<\\/h3><p class=\\\"font-18\\\" style=\\\"margin-right:0px;margin-left:0px;font-size:18px;\\\">We offer numerous approaches to get FREE Balance, Coupons and Deposit offers yet we generally reserve the privilege to audit it and deduct it from your record offset with any explanation we may it is a sort of misuse. If we choose to deduct a few or all of free Balance from your record balance, and your record balance becomes negative, at that point the record will naturally be suspended. If your record is suspended because of a negative Balance you can request to make a custom payment to settle your equilibrium to actuate your record.<\\/p><\\/div>\"}','2021-06-09 08:51:18','2021-06-09 08:51:18'),(44,'maintenance.data','{\"description\":\"<div class=\\\"mb-5\\\" style=\\\"color: rgb(111, 111, 111); font-family: Nunito, sans-serif; margin-bottom: 3rem !important;\\\"><h3 class=\\\"mb-3\\\" style=\\\"text-align: center; font-weight: 600; line-height: 1.3; font-size: 24px; font-family: Exo, sans-serif; color: rgb(54, 54, 54);\\\">What information do we collect?<\\/h3><p class=\\\"font-18\\\" style=\\\"text-align: center; margin-right: 0px; margin-left: 0px; font-size: 18px !important;\\\">We gather data from you when you register on our site, submit a request, buy any services, react to an overview, or round out a structure. At the point when requesting any assistance or enrolling on our site, as suitable, you might be approached to enter your: name, email address, or telephone number. You may, nonetheless, visit our site anonymously.<\\/p><\\/div>\"}','2020-07-04 23:42:52','2022-05-11 03:57:17'),(48,'footer.content','{\"subscribe_title\":\"Subscribe for new Offers and updates\",\"connect_title\":\"To get updates follow us on Facebook, Twitters etc.\",\"subscribe_subtitle\":\"Subscribe for new Offers and updates\",\"copyright_text\":null,\"seller_account_enabled\":1,\"seller_account_url\":null}','2023-02-26 07:07:04','2026-04-02 21:25:01'),(49,'footer.element','{\"has_image\":\"1\",\"image\":\"63fb28510697a1677404241.jpg\"}','2023-02-26 07:07:21','2023-02-26 07:07:21'),(50,'footer.element','{\"has_image\":\"1\",\"image\":\"63fb285c75e3c1677404252.jpg\"}','2023-02-26 07:07:32','2023-02-26 07:07:32'),(51,'footer.element','{\"has_image\":\"1\",\"image\":\"63fb286b77ef01677404267.jpg\"}','2023-02-26 07:07:47','2023-02-26 07:07:47'),(52,'footer.element','{\"has_image\":\"1\",\"image\":\"63fb28781d5ac1677404280.jpg\"}','2023-02-26 07:08:00','2023-02-26 07:08:00'),(53,'footer.element','{\"has_image\":\"1\",\"image\":\"63fb2885195a61677404293.jpg\"}','2023-02-26 07:08:13','2023-02-26 07:08:13'),(54,'footer.element','{\"has_image\":\"1\",\"image\":\"63fb28952a9311677404309.jpg\"}','2023-02-26 07:08:29','2023-02-26 07:08:29'),(55,'footer.element','{\"has_image\":\"1\",\"image\":\"63fb28a13a3c71677404321.jpg\"}','2023-02-26 07:08:41','2023-02-26 07:08:41'),(56,'footer.element','{\"has_image\":\"1\",\"image\":\"63fb28b0c801a1677404336.jpg\"}','2023-02-26 07:08:56','2023-02-26 07:08:56'),(57,'login.content','{\"heading\":\"Login Account\",\"subheading\":\"\",\"login_fields\":{\"username\":1,\"email\":1,\"mobile\":1},\"captcha_enabled\":1,\"social_login_buttons\":{\"google\":1,\"facebook\":1,\"twitter\":0,\"apple\":0,\"github\":0},\"image\":\"63fb28dd2c0851677404381.jpg\"}','2023-02-26 07:09:41','2026-03-14 10:01:58'),(58,'policy_pages.element','{\"title\":\"Shipping and Delivery\",\"details\":\"<div><div><div><span style=\\\"font-size:1rem;\\\">Lorem ipsum dolor sit amet consectetur adipisicing elit. Cupiditate quae illo soluta sapiente minus voluptatibus molestias voluptates maiores repudiandae, velit quaerat error! Dolor alias voluptates rerum vitae illum officiis laboriosam, eos fugiat necessitatibus iste quasi vero porro at asperiores atque numquam adipisci esse perferendis hic dolore dolores facere quidem? Voluptatum, nemo voluptates. Qui, animi odit voluptatem velit nostrum rem maiores. Qui esse magnam enim natus numquam ab adipisci nihil mollitia odio ducimus architecto unde harum saepe illum, ipsa hic dicta alias cumque et minus veritatis assumenda a quo. Possimus, vitae est! Fuga quidem minima sunt modi. Officia natus quaerat nobis ut ab nulla. Tempora, corrupti? Animi excepturi voluptatem quod consectetur culpa autem aliquid? Inventore adipisci officia error dolore provident omnis sint perferendis, consequuntur, sapiente magni sequi quo quis nesciunt molestiae vero iure cum laboriosam fugit. Numquam sed expedita alias non? Sequi, harum cupiditate! Quasi non laboriosam optio ex fugit delectus minus incidunt excepturi! Nisi iure ex, nulla perspiciatis similique est, libero sapiente hic error amet, quisquam vel obcaecati fugit. Maxime cupiditate voluptatibus, nisi ullam error voluptas culpa at animi sequi eius suscipit ad ipsum qui illum provident dolores facere necessitatibus commodi vel in, laborum quidem aliquam ipsa quibusdam? Eius, alias voluptatem, laboriosam perferendis itaque, sapiente nisi beatae necessitatibus reprehenderit nam corrupti magnam qui omnis eveniet! Optio at expedita temporibus fugiat debitis eum? Dolore excepturi quod doloribus quam rem placeat at odit dicta amet expedita illo laboriosam minus ut minima, tenetur suscipit soluta assumenda. Nisi laboriosam adipisci animi consequuntur, ad illum repellat consequatur odit, laudantium velit non nobis labore illo omnis quod suscipit voluptates quaerat consectetur temporibus et, laborum quam ducimus earum! Repellat, fugit? Repudiandae repellendus maiores doloribus deleniti asperiores distinctio suscipit fugiat omnis culpa itaque? Harum et, velit ratione corrupti error asperiores optio, recusandae mollitia necessitatibus cumque vero voluptatem ullam porro aut eum earum! Consectetur voluptatum ratione dolor in earum molestiae ipsam quisquam, eum vitae suscipit voluptates recusandae. Cum eaque officiis ea et atque eveniet similique sequi illo!<\\/span><br \\/><\\/div><\\/div><\\/div>\"}','2023-02-26 07:11:24','2023-02-26 07:12:05'),(59,'register.content','{\"heading\":\"Create Account\",\"subheading\":\"\",\"login_captcha_enabled\":1,\"registration_fields\":{\"referBy\":1,\"firstname\":1,\"lastname\":1,\"username\":1,\"email\":1,\"country\":0,\"mobile\":1,\"age\":0,\"gender\":0,\"password\":1,\"captcha\":1,\"agree\":1,\"address\":0,\"city\":0,\"state\":0,\"zip\":0,\"division\":0,\"district\":0,\"thana\":0,\"date_of_birth\":0,\"occupation\":0,\"company_name\":0,\"website\":0,\"telegram\":0,\"whatsapp\":1,\"newsletter_subscribe\":0,\"how_heard\":0,\"profile_photo\":0,\"nid_number\":0,\"alternate_phone\":0,\"preferred_language\":0,\"tax_id\":0},\"has_image\":\"1\",\"image\":\"63fb29940bae41677404564.jpg\",\"profile_fields\":{\"referBy\":0,\"firstname\":1,\"lastname\":1,\"username\":1,\"email\":1,\"country\":1,\"mobile\":1,\"age\":1,\"gender\":1,\"password\":0,\"captcha\":0,\"agree\":0,\"address\":1,\"city\":1,\"state\":0,\"zip\":1,\"division\":1,\"district\":1,\"thana\":0,\"date_of_birth\":0,\"occupation\":0,\"company_name\":0,\"website\":0,\"telegram\":0,\"whatsapp\":0,\"newsletter_subscribe\":1,\"how_heard\":1,\"profile_photo\":1,\"nid_number\":0,\"alternate_phone\":1,\"preferred_language\":0,\"tax_id\":0}}','2023-02-26 07:12:44','2026-03-14 07:23:25'),(60,'service.element','{\"has_image\":[\"1\"],\"title\":\"Gift Voucher\",\"short_detail\":\"Aliquam eleifend in elit congue\",\"image\":\"63fb29c5c36081677404613.png\"}','2023-02-26 07:13:33','2023-02-26 07:13:33'),(61,'service.element','{\"has_image\":[\"1\"],\"title\":\"Online Support 24\\/7\",\"short_detail\":\"Aliquam eleifend in elit congue\",\"image\":\"63fb2a1aa312d1677404698.png\"}','2023-02-26 07:14:58','2023-02-26 07:14:58'),(62,'service.element','{\"has_image\":[\"1\"],\"title\":\"Money Back Guarantee\",\"short_detail\":\"Aliquam eleifend in elit congue\",\"image\":\"63fb2a32b7b551677404722.png\"}','2023-02-26 07:15:22','2023-02-26 07:15:22'),(63,'service.element','{\"has_image\":[\"1\"],\"title\":\"Free Shipping\",\"short_detail\":\"Aliquam eleifend in elit congue\",\"image\":\"63fb2af3cf4ff1677404915.png\"}','2023-02-26 07:18:35','2023-02-26 07:18:35'),(64,'social_icon.element','{\"title\":\"Facebook\",\"icon\":\"<i class=\\\"fab fa-facebook-f\\\"><\\/i>\",\"url\":\"https:\\/\\/www.facebook.com\\/\"}','2023-02-26 07:20:07','2023-02-26 07:20:07'),(65,'social_icon.element','{\"title\":\"Twitter\",\"icon\":\"<i class=\\\"fab fa-twitter\\\"><\\/i>\",\"url\":\"https:\\/\\/www.twitter.com\\/\"}','2023-02-26 07:20:50','2023-02-26 07:20:50'),(66,'social_icon.element','{\"title\":\"Instagram\",\"icon\":\"<i class=\\\"fab fa-instagram\\\"><\\/i>\",\"url\":\"https:\\/\\/www.instagram.com\\/\"}','2023-02-26 07:21:30','2023-02-26 07:21:30'),(67,'social_icon.element','{\"title\":\"opu\",\"icon\":\"<i class=\\\"far fa-address-card\\\"><\\/i>\",\"url\":\"https:\\/\\/www.ryans.com\\/opu\"}','2023-02-26 07:21:58','2026-04-02 22:22:08'),(108,'scrollbar.element','{\"title\":\"Home Banner Ticker\",\"display_order\":50,\"position\":\"banner_below\",\"template\":\"offer\",\"status\":1,\"visibility\":\"private\",\"visibility_users\":\"all\",\"visibility_pages\":\"all\",\"custom_urls\":\"\",\"custom_url_mode\":\"contains\",\"schedule_start\":null,\"schedule_end\":null,\"scroll_speed\":50,\"scroll_direction\":\"rtl\",\"loop_mode\":\"infinite\",\"pause_on_hover\":1,\"gap_between_items\":8,\"animation_type\":\"linear\",\"bar_height\":28,\"bar_size\":\"medium\",\"bar_thickness\":\"thin\",\"default_text_size\":\"small\",\"default_text_weight\":\"medium\",\"bar_padding\":null,\"width_type\":\"full\",\"width_value\":\"\",\"max_width\":\"\",\"bar_background_type\":\"solid\",\"bar_background_value\":\"#ffffff\",\"bar_border\":null,\"bar_shadow\":null,\"hide_on_mobile\":0,\"hide_on_desktop\":0,\"container_mode\":\"full\",\"align\":\"center\",\"z_index\":10,\"sticky\":0,\"offset_top\":\"0px\",\"custom_x_percent\":0,\"custom_y_px\":0,\"custom_width_percent\":100,\"loop_delay\":0,\"item_animation\":\"none\",\"icon_animation\":\"none\",\"hover_effect\":\"pause\",\"items\":[{\"type\":\"text\",\"content\":\"\\ud83d\\udce3 Live Offer: Welcome! Get 20% OFF on first order. Free shipping over 50 USD. \\ud83d\\uded2 http:\\/\\/localhost\\/staylbd\\/sajaladminopu\\/frontend\\/scrollbar\\/edit\\/108\\ud83d\\udc51 \\u098f\\u0987 \\u09b2\\u09bf\\u0982\\u0995\\u09c7 \\u09a6\\u09c7\\u0996\\u09be \\u09af\\u09be\\u099a\\u09cd\\u099b\\u09c7 \\u098f\\u0995\\u0987 \\u09ab\\u09bf\\u099a\\u09be\\u09b0 \\ud83d\\ude80 \\u09a6\\u09c1\\u0987 \\u099c\\u09be\\u09af\\u09bc\\u0997\\u09be\\u09af\\u09bc \\u09b8\\u09c7\\u0997\\u09c1\\u09b2\\u09cb \\u09b0\\u09bf\\u09ae\\u09c1\\u09ad \\u0995\\u09b0\\u09c1\\u09a8 \\u09a1\\u09ac\\u09b2 \\u09a1\\u09ac\\u09b2 \\u098f\\u0995\\u0987 \\u09ab\\u09bf\\u099a\\u09be\\u09b0 \\u098f\\u09ac\\u0982 \\u0995\\u09a4 \\u09b8\\u09cd\\u09aa\\u09bf\\u09a1\\u09c7 \\u09b8\\u09cd\\u0995\\u09cd\\u09b0\\u09b2 \\u099a\\u09b2\\u09ac\\u09c7 \\u09a4\\u09be \\ud83d\\udef5 \\u0995\\u09a8\\u09cd\\u099f\\u09cd\\u09b0\\u09cb\\u09b2 \\u0995\\u09b0\\u09be\\u09b0 \\u099c\\u09a8\\u09cd\\u09af \\u09ab\\u09bf\\u099a\\u09be\\u09b0 \\u09a6\\u09c7\\u0996\\u09be \\u09af\\u09be\\u099a\\u09cd\\u099b\\u09c7 \\u09a8\\u09be \\u0986\\u09aa\\u09a1\\u09c7\\u099f \\u0995\\u09b0\\u09c1\\u09a8 \\u09af\\u09a4 \\u2705 \\u09ac\\u09a1\\u09bc\\u0987 \\u09b2\\u09c7\\u0996\\u09be \\u09b9\\u09cb\\u0995 \\u09a8\\u09be \\u0995\\u09c7\\u09a8 \\u09ac\\u09be \\u09af\\u09a4 \\u099b\\u09cb\\u099f \\u09b2\\u09c7\\u0996\\u0995 \\u09a8\\u09be \\u0995\\u09c7\\u09a8 \\ud83d\\udccc \\u09af\\u09a4 \\u0995\\u09cd\\u09af\\u09be\\u09b0\\u09c7\\u0995\\u09cd\\u099f\\u09be\\u09b0 \\u09b2\\u09c7\\u0996\\u09be \\u09b9\\u09cb\\u0995 \\u09a8\\u09be \\u0995\\u09c7\\u09a8 \\u09b8\\u09cd\\u09aa\\u09cd\\u09b0\\u09bf\\u099f \\ud83c\\udf80 \\u0995\\u09a8\\u09cd\\u099f\\u09cd\\u09b0\\u09cb\\u09b2 \\u0995\\u09b0\\u09be \\u09af\\u09be\\u09ac\\u09c7 \\u09b8\\u09c7\\u0987 \\u09ad\\u09be\\u09ac\\u09c7 \\u0986\\u09aa\\u09a1\\u09c7\\u099f \\u0995\\u09b0\\u09c1\\u09a8 \\u098f\\u0987 \\u09b2\\u09bf\\u0982\\u0995\\u09c7 \\u26a1 \\u09a6\\u09c7\\u0996\\u09be \\u09af\\u09be\\u099a\\u09cd\\u099b\\u09c7 \\u098f\\u0996\\u09a8\\u09cb \\u0985\\u09aa\\u09cd\\u09b0\\u09af\\u09bc\\u09cb\\u099c\\u09a8\\u09c0\\u09af\\u09bc \\u09ab\\u09bf\\u099a\\u09be\\u09b0 \\u09af\\u09c7\\u0997\\u09c1\\u09b2\\u09cb \\ud83d\\uded2\",\"content_text\":\"\\ud83d\\udce3 Live Offer: Welcome! Get 20% OFF on first order. Free shipping over 50 USD. \\ud83d\\uded2 http:\\/\\/localhost\\/staylbd\\/sajaladminopu\\/frontend\\/scrollbar\\/edit\\/108\\ud83d\\udc51 \\u098f\\u0987 \\u09b2\\u09bf\\u0982\\u0995\\u09c7 \\u09a6\\u09c7\\u0996\\u09be \\u09af\\u09be\\u099a\\u09cd\\u099b\\u09c7 \\u098f\\u0995\\u0987 \\u09ab\\u09bf\\u099a\\u09be\\u09b0 \\ud83d\\ude80 \\u09a6\\u09c1\\u0987 \\u099c\\u09be\\u09af\\u09bc\\u0997\\u09be\\u09af\\u09bc \\u09b8\\u09c7\\u0997\\u09c1\\u09b2\\u09cb \\u09b0\\u09bf\\u09ae\\u09c1\\u09ad \\u0995\\u09b0\\u09c1\\u09a8 \\u09a1\\u09ac\\u09b2 \\u09a1\\u09ac\\u09b2 \\u098f\\u0995\\u0987 \\u09ab\\u09bf\\u099a\\u09be\\u09b0 \\u098f\\u09ac\\u0982 \\u0995\\u09a4 \\u09b8\\u09cd\\u09aa\\u09bf\\u09a1\\u09c7 \\u09b8\\u09cd\\u0995\\u09cd\\u09b0\\u09b2 \\u099a\\u09b2\\u09ac\\u09c7 \\u09a4\\u09be \\ud83d\\udef5 \\u0995\\u09a8\\u09cd\\u099f\\u09cd\\u09b0\\u09cb\\u09b2 \\u0995\\u09b0\\u09be\\u09b0 \\u099c\\u09a8\\u09cd\\u09af \\u09ab\\u09bf\\u099a\\u09be\\u09b0 \\u09a6\\u09c7\\u0996\\u09be \\u09af\\u09be\\u099a\\u09cd\\u099b\\u09c7 \\u09a8\\u09be \\u0986\\u09aa\\u09a1\\u09c7\\u099f \\u0995\\u09b0\\u09c1\\u09a8 \\u09af\\u09a4 \\u2705 \\u09ac\\u09a1\\u09bc\\u0987 \\u09b2\\u09c7\\u0996\\u09be \\u09b9\\u09cb\\u0995 \\u09a8\\u09be \\u0995\\u09c7\\u09a8 \\u09ac\\u09be \\u09af\\u09a4 \\u099b\\u09cb\\u099f \\u09b2\\u09c7\\u0996\\u0995 \\u09a8\\u09be \\u0995\\u09c7\\u09a8 \\ud83d\\udccc \\u09af\\u09a4 \\u0995\\u09cd\\u09af\\u09be\\u09b0\\u09c7\\u0995\\u09cd\\u099f\\u09be\\u09b0 \\u09b2\\u09c7\\u0996\\u09be \\u09b9\\u09cb\\u0995 \\u09a8\\u09be \\u0995\\u09c7\\u09a8 \\u09b8\\u09cd\\u09aa\\u09cd\\u09b0\\u09bf\\u099f \\ud83c\\udf80 \\u0995\\u09a8\\u09cd\\u099f\\u09cd\\u09b0\\u09cb\\u09b2 \\u0995\\u09b0\\u09be \\u09af\\u09be\\u09ac\\u09c7 \\u09b8\\u09c7\\u0987 \\u09ad\\u09be\\u09ac\\u09c7 \\u0986\\u09aa\\u09a1\\u09c7\\u099f \\u0995\\u09b0\\u09c1\\u09a8 \\u098f\\u0987 \\u09b2\\u09bf\\u0982\\u0995\\u09c7 \\u26a1 \\u09a6\\u09c7\\u0996\\u09be \\u09af\\u09be\\u099a\\u09cd\\u099b\\u09c7 \\u098f\\u0996\\u09a8\\u09cb \\u0985\\u09aa\\u09cd\\u09b0\\u09af\\u09bc\\u09cb\\u099c\\u09a8\\u09c0\\u09af\\u09bc \\u09ab\\u09bf\\u099a\\u09be\\u09b0 \\u09af\\u09c7\\u0997\\u09c1\\u09b2\\u09cb \\ud83d\\uded2\",\"color\":\"#333333\",\"font_size\":\"\",\"font_weight\":\"400\",\"font_family\":\"inherit\",\"font_style\":\"normal\",\"letter_spacing\":\"\",\"text_transform\":\"none\",\"is_active\":1,\"segments\":[{\"text\":\"\\ud83d\\udce3\",\"color\":\"#5b6e88\",\"weight\":\"700\",\"font_family\":\"Poppins, sans-serif\",\"font_size\":\"\"},{\"text\":\"Live Offer: Welcome! Get 20% OFF on first order. Free shipping over 50 USD.\",\"color\":\"#ff0000\",\"weight\":\"700\",\"font_family\":\"Poppins, sans-serif\",\"font_size\":\"\"},{\"text\":\"\\ud83d\\uded2\",\"color\":\"#22c55e\",\"weight\":\"700\",\"font_family\":\"Poppins, sans-serif\",\"font_size\":\"\"},{\"text\":\"http:\\/\\/localhost\\/staylbd\\/sajaladminopu\\/frontend\\/scrollbar\\/edit\\/108\\ud83d\\udc51\",\"color\":\"#22c55e\",\"weight\":\"700\",\"font_family\":\"Poppins, sans-serif\",\"font_size\":\"\"},{\"text\":\"\\u098f\\u0987 \\u09b2\\u09bf\\u0982\\u0995\\u09c7 \\u09a6\\u09c7\\u0996\\u09be \\u09af\\u09be\\u099a\\u09cd\\u099b\\u09c7 \\u098f\\u0995\\u0987 \\u09ab\\u09bf\\u099a\\u09be\\u09b0\",\"color\":\"#3b82f6\",\"weight\":\"400\",\"font_family\":\"Poppins, sans-serif\",\"font_size\":\"\"},{\"text\":\"\\ud83d\\ude80\",\"color\":\"#3b82f6\",\"weight\":\"400\",\"font_family\":\"Poppins, sans-serif\",\"font_size\":\"\"},{\"text\":\"\\u09a6\\u09c1\\u0987 \\u099c\\u09be\\u09af\\u09bc\\u0997\\u09be\\u09af\\u09bc \\u09b8\\u09c7\\u0997\\u09c1\\u09b2\\u09cb \\u09b0\\u09bf\\u09ae\\u09c1\\u09ad \\u0995\\u09b0\\u09c1\\u09a8 \\u09a1\\u09ac\\u09b2\",\"color\":\"#000000\",\"weight\":\"400\",\"font_family\":\"Poppins, sans-serif\",\"font_size\":\"\"},{\"text\":\"\\u09a1\\u09ac\\u09b2 \\u098f\\u0995\\u0987 \\u09ab\\u09bf\\u099a\\u09be\\u09b0 \\u098f\\u09ac\\u0982 \\u0995\\u09a4 \\u09b8\\u09cd\\u09aa\\u09bf\\u09a1\\u09c7 \\u09b8\\u09cd\\u0995\\u09cd\\u09b0\\u09b2 \\u099a\\u09b2\\u09ac\\u09c7 \\u09a4\\u09be\",\"color\":\"#ff00ea\",\"weight\":\"400\",\"font_family\":\"Poppins, sans-serif\",\"font_size\":\"\"},{\"text\":\"\\ud83d\\udef5\",\"color\":\"#ff00ea\",\"weight\":\"400\",\"font_family\":\"Poppins, sans-serif\",\"font_size\":\"\"},{\"text\":\"\\u0995\\u09a8\\u09cd\\u099f\\u09cd\\u09b0\\u09cb\\u09b2 \\u0995\\u09b0\\u09be\\u09b0 \\u099c\\u09a8\\u09cd\\u09af \\u09ab\\u09bf\\u099a\\u09be\\u09b0 \\u09a6\\u09c7\\u0996\\u09be \\u09af\\u09be\\u099a\\u09cd\\u099b\\u09c7 \\u09a8\\u09be \\u0986\\u09aa\\u09a1\\u09c7\\u099f \\u0995\\u09b0\\u09c1\\u09a8 \\u09af\\u09a4\",\"color\":\"#000000\",\"weight\":\"400\",\"font_family\":\"Poppins, sans-serif\",\"font_size\":\"\"},{\"text\":\"\\u2705\",\"color\":\"#000000\",\"weight\":\"400\",\"font_family\":\"Poppins, sans-serif\",\"font_size\":\"\"},{\"text\":\"\\u09ac\\u09a1\\u09bc\\u0987 \\u09b2\\u09c7\\u0996\\u09be \\u09b9\\u09cb\\u0995 \\u09a8\\u09be \\u0995\\u09c7\\u09a8 \\u09ac\\u09be \\u09af\\u09a4 \\u099b\\u09cb\\u099f \\u09b2\\u09c7\\u0996\\u0995 \\u09a8\\u09be \\u0995\\u09c7\\u09a8\",\"color\":\"#0ea5e9\",\"weight\":\"400\",\"font_family\":\"Poppins, sans-serif\",\"font_size\":\"\"},{\"text\":\"\\ud83d\\udccc\",\"color\":\"#0ea5e9\",\"weight\":\"400\",\"font_family\":\"Poppins, sans-serif\",\"font_size\":\"\"},{\"text\":\"\\u09af\\u09a4 \\u0995\\u09cd\\u09af\\u09be\\u09b0\\u09c7\\u0995\\u09cd\\u099f\\u09be\\u09b0 \\u09b2\\u09c7\\u0996\\u09be \\u09b9\\u09cb\\u0995 \\u09a8\\u09be \\u0995\\u09c7\\u09a8 \\u09b8\\u09cd\\u09aa\\u09cd\\u09b0\\u09bf\\u099f\",\"color\":\"#000000\",\"weight\":\"400\",\"font_family\":\"Poppins, sans-serif\",\"font_size\":\"\"},{\"text\":\"\\ud83c\\udf80\",\"color\":\"#000000\",\"weight\":\"400\",\"font_family\":\"Poppins, sans-serif\",\"font_size\":\"\"},{\"text\":\"\\u0995\\u09a8\\u09cd\\u099f\\u09cd\\u09b0\\u09cb\\u09b2 \\u0995\\u09b0\\u09be \\u09af\\u09be\\u09ac\\u09c7 \\u09b8\\u09c7\\u0987 \\u09ad\\u09be\\u09ac\\u09c7 \\u0986\\u09aa\\u09a1\\u09c7\\u099f \\u0995\\u09b0\\u09c1\\u09a8 \\u098f\\u0987 \\u09b2\\u09bf\\u0982\\u0995\\u09c7\",\"color\":\"#ef4444\",\"weight\":\"400\",\"font_family\":\"Poppins, sans-serif\",\"font_size\":\"\"},{\"text\":\"\\u26a1\",\"color\":\"#ef4444\",\"weight\":\"400\",\"font_family\":\"Poppins, sans-serif\",\"font_size\":\"\"},{\"text\":\"\\u09a6\\u09c7\\u0996\\u09be \\u09af\\u09be\\u099a\\u09cd\\u099b\\u09c7 \\u098f\\u0996\\u09a8\\u09cb \\u0985\\u09aa\\u09cd\\u09b0\\u09af\\u09bc\\u09cb\\u099c\\u09a8\\u09c0\\u09af\\u09bc \\u09ab\\u09bf\\u099a\\u09be\\u09b0 \\u09af\\u09c7\\u0997\\u09c1\\u09b2\\u09cb\",\"color\":\"#000000\",\"weight\":\"400\",\"font_family\":\"Poppins, sans-serif\",\"font_size\":\"\"},{\"text\":\"\\ud83d\\uded2\",\"color\":\"#000000\",\"weight\":\"400\",\"font_family\":\"Poppins, sans-serif\",\"font_size\":\"\"}]}],\"page_speeds\":[]}','2026-02-27 05:37:46','2026-04-02 10:59:00'),(109,'home_section.settings','{\"power_zone_enabled\":1,\"show_category_icons\":1,\"show_flash_deals\":1,\"show_trending\":1,\"show_quick_services\":1,\"show_promo_blocks\":1,\"show_quick_category_boxes\":1,\"flash_sale_end_date\":\"2026-02-28T23:59\",\"flash_sale_title\":\"Flash Sale\",\"trust_section_enabled\":1,\"social_proof_enabled\":1,\"live_purchase_enabled\":0,\"reviews_slider_enabled\":1,\"top_rated_enabled\":1,\"recommendation_enabled\":1,\"recently_viewed_enabled\":1,\"similar_products_enabled\":0,\"sticky_cart_enabled\":1,\"quick_view_enabled\":1,\"wishlist_popup_enabled\":1,\"compare_enabled\":1,\"floating_cart_enabled\":1,\"conversion_enabled\":1,\"limited_stock_enabled\":1,\"only_x_left_enabled\":1,\"people_viewing_enabled\":0,\"recently_sold_enabled\":0,\"flash_deals_limit\":8,\"trending_limit\":8,\"top_rated_limit\":8,\"reviews_slider_limit\":6}','2026-02-28 13:17:44','2026-03-17 22:17:23'),(110,'home_section.quick_category','{\"title\":\"Hot Deals\",\"icon\":\"las la-bolt\",\"link_type\":\"hot_deal\",\"display_order\":1}','2026-02-28 13:17:44','2026-02-28 13:17:44'),(111,'home_section.quick_category','{\"title\":\"Top Selling\",\"icon\":\"las la-chart-line\",\"link_type\":\"best_selling\",\"display_order\":2}','2026-02-28 13:17:44','2026-02-28 13:17:44'),(112,'home_section.quick_category','{\"title\":\"New Arrival\",\"icon\":\"las la-star\",\"link_type\":\"new_arrival\",\"display_order\":3}','2026-02-28 13:17:44','2026-02-28 13:17:44'),(113,'home_section.quick_category','{\"title\":\"Featured\",\"icon\":\"las la-gem\",\"link_type\":\"featured\",\"display_order\":4}','2026-02-28 13:17:44','2026-02-28 13:17:44'),(114,'home_section.quick_category','{\"title\":\"Discount\",\"icon\":\"las la-tag\",\"link_type\":\"discount\",\"display_order\":5}','2026-02-28 13:17:44','2026-02-28 13:17:44'),(115,'home_section.trust','{\"title\":\"Secure Payment\",\"icon\":\"las la-lock\",\"short_detail\":\"100% secure payment\",\"url\":\"#\",\"display_order\":1}','2026-02-28 13:17:44','2026-02-28 13:17:44'),(116,'home_section.trust','{\"title\":\"Fast Delivery\",\"icon\":\"las la-shipping-fast\",\"short_detail\":\"Quick delivery\",\"url\":\"#\",\"display_order\":2}','2026-02-28 13:17:44','2026-02-28 13:17:44'),(117,'home_section.trust','{\"title\":\"Easy Return\",\"icon\":\"las la-undo\",\"short_detail\":\"Easy return policy\",\"url\":\"#\",\"display_order\":3}','2026-02-28 13:17:44','2026-02-28 13:17:44'),(118,'home_section.trust','{\"title\":\"Customer Satisfaction\",\"icon\":\"las la-smile\",\"short_detail\":\"Satisfaction guaranteed\",\"url\":\"#\",\"display_order\":4}','2026-02-28 13:17:44','2026-02-28 13:17:44'),(119,'home_section.trust','{\"title\":\"Authentic Product\",\"icon\":\"las la-certificate\",\"short_detail\":\"100% authentic\",\"url\":\"#\",\"display_order\":5}','2026-02-28 13:17:44','2026-02-28 13:17:44'),(126,'custom_message.element','{\"message\":\"jgrgrhsfiehbr\",\"link_url\":\"http:\\/\\/localhost\\/staylbd\\/product\\/\",\"link_text\":\"Read more\",\"show_on\":\"all\",\"position\":\"bottom_bar\",\"route_filter\":\"\",\"status\":1,\"display_order\":1}','2026-03-06 09:55:13','2026-03-06 09:55:13'),(127,'product_slider.settings','{\"auto_scroll_enabled\":1,\"scroll_interval_seconds\":4,\"scroll_animation_speed_ms\":600,\"products_per_row_desktop\":6,\"products_per_row_tablet\":4,\"products_per_row_mobile\":2}','2026-03-10 10:36:58','2026-03-10 10:36:58'),(128,'footer.company_info','{\"show\":1,\"about_text\":\"CEO: Mohammad Tariqul Islam\",\"mission_text\":\"\\u0993\\u09af\\u09bc\\u09c7\\u09b2\\u0995\\u09be\\u09ae staylbd \\u0995\\u09c7\\u09a8\\u09be\\u0995\\u09be\\u099f\\u09be \\u09a8\\u09bf\\u09b6\\u09cd\\u099a\\u09bf\\u09a8\\u09cd\\u09a4\\u09c7\",\"registration_info\":\"staylbd\",\"business_license\":\"\\u0995\\u09c7\\u09a8\\u09be\\u0995\\u09be\\u099f\\u09be \\u09a8\\u09bf\\u09b6\\u09cd\\u099a\\u09bf\\u09a8\\u09cd\\u09a4\\u09c7\"}','2026-03-15 09:14:31','2026-03-15 09:14:31'),(129,'footer.quick_links','{\"title\":\"\\u0993\\u09af\\u09bc\\u09c7\\u09b2\\u0995\\u09be\\u09ae staylbd \\u0995\\u09c7\\u09a8\\u09be\\u0995\\u09be\\u099f\\u09be \\u09a8\\u09bf\\u09b6\\u09cd\\u099a\\u09bf\\u09a8\\u09cd\\u09a4\\u09c7\",\"url\":\"https:\\/\\/www.google.com\\/\",\"display_order\":0}','2026-03-15 09:15:45','2026-03-15 09:15:45'),(130,'footer.quick_links','{\"title\":\"\\u0993\\u09af\\u09bc\\u09c7\\u09b2\\u0995\\u09be\\u09ae staylbd \\u0995\\u09c7\\u09a8\\u09be\\u0995\\u09be\\u099f\\u09be \\u09a8\\u09bf\\u09b6\\u09cd\\u099a\\u09bf\\u09a8\\u09cd\\u09a4\\u09c7\",\"url\":\"https:\\/\\/www.google.com\\/\",\"display_order\":0}','2026-03-15 09:15:53','2026-03-15 09:15:53'),(131,'footer.app_promotion','{\"enabled\":1}','2026-03-15 09:16:10','2026-03-15 09:16:25'),(132,'footer.app_promotion_item','{\"platform\":\"Android\",\"name\":\"\",\"link\":\"\",\"display_order\":0}','2026-03-15 09:17:56','2026-03-15 09:17:56'),(133,'footer.app_promotion_item','{\"platform\":\"Desktop\",\"name\":\"\",\"link\":\"\",\"display_order\":0}','2026-03-15 09:18:02','2026-03-15 09:18:02'),(134,'footer.app_promotion_item','{\"platform\":\"Mac\",\"name\":\"\",\"link\":\"\",\"display_order\":0}','2026-03-15 09:18:05','2026-03-15 09:18:05'),(135,'footer.app_promotion_item','{\"platform\":\"Windows\",\"name\":\"\",\"link\":\"\",\"display_order\":0}','2026-03-15 09:18:07','2026-03-15 09:18:07'),(136,'footer.support_center','{\"enabled\":1,\"help_center_url\":\"https:\\/\\/www.google.com\\/\",\"return_policy_url\":\"https:\\/\\/www.google.com\\/\",\"refund_policy_url\":\"https:\\/\\/www.google.com\\/\",\"track_order_url\":\"https:\\/\\/www.google.com\\/\",\"live_chat_enabled\":1,\"support_ticket_enabled\":1,\"support_email\":\"shtejnjrhrbb@gmail\"}','2026-03-15 09:18:41','2026-03-15 09:18:41'),(137,'footer.custom_ads','{\"image\":\"69b6ce063fe561773587974.jpg\",\"title\":\"\\u0993\\u09af\\u09bc\\u09c7\\u09b2\\u0995\\u09be\\u09ae staylbd \\u0995\\u09c7\\u09a8\\u09be\\u0995\\u09be\\u099f\\u09be \\u09a8\\u09bf\\u09b6\\u09cd\\u099a\\u09bf\\u09a8\\u09cd\\u09a4\\u09c7\",\"url\":\"https:\\/\\/www.google.com\\/\",\"display_order\":0}','2026-03-15 09:19:34','2026-03-15 09:19:34'),(138,'footer.custom_ads','{\"image\":\"69b6ce142b30b1773587988.png\",\"title\":\"\\u0993\\u09af\\u09bc\\u09c7\\u09b2\\u0995\\u09be\\u09ae staylbd \\u0995\\u09c7\\u09a8\\u09be\\u0995\\u09be\\u099f\\u09be \\u09a8\\u09bf\\u09b6\\u09cd\\u099a\\u09bf\\u09a8\\u09cd\\u09a4\\u09c7\",\"url\":\"https:\\/\\/www.google.com\\/\",\"display_order\":0}','2026-03-15 09:19:48','2026-03-15 09:19:48'),(139,'footer.security_badges','{\"image\":\"69b6ce2d5db7f1773588013.jpg\",\"title\":\"\\u0993\\u09af\\u09bc\\u09c7\\u09b2\\u0995\\u09be\\u09ae staylbd \\u0995\\u09c7\\u09a8\\u09be\\u0995\\u09be\\u099f\\u09be \\u09a8\\u09bf\\u09b6\\u09cd\\u099a\\u09bf\\u09a8\\u09cd\\u09a4\\u09c7\",\"url\":\"https:\\/\\/www.google.com\\/\",\"display_order\":0}','2026-03-15 09:20:13','2026-03-15 09:20:13'),(140,'footer.return_policy','{\"show_form\":1,\"form_title\":\"Product Return Request\",\"success_message\":\"We have received your return request. Our team will contact you shortly.\"}','2026-03-15 09:20:44','2026-03-15 09:20:44'),(141,'quick_order.fields','{\"fields\":[\"guest_phone\",\"guest_name\",\"guest_email\",\"guest_alternate_phone\",\"guest_preferred_contact_time\",\"guest_address\",\"guest_area_city\",\"guest_landmark\",\"postal_code\",\"guest_delivery_note\",\"guest_preferred_delivery_time\",\"guest_order_note\"]}','2026-03-17 06:41:01','2026-03-17 06:41:01'),(142,'homepage.layout_order','{\"sections\":[{\"id\":\"scrollbar\",\"enabled\":true,\"label\":\"Scrollbar\"},{\"id\":\"home_category\",\"enabled\":true,\"label\":\"Category row\"},{\"id\":\"quick_deals\",\"enabled\":true,\"label\":\"Quick Deals\"},{\"id\":\"power_zone\",\"enabled\":true,\"label\":\"Power zone \\/ banner below\"},{\"id\":\"ad_slot_1\",\"enabled\":true,\"label\":\"vb\"},{\"id\":\"hot_deal\",\"enabled\":true,\"label\":\"Hot Deals\"},{\"id\":\"featured\",\"enabled\":true,\"label\":\"Featured Products\"},{\"id\":\"new_arrivals\",\"enabled\":true,\"label\":\"New Arrivals\"},{\"id\":\"trending\",\"enabled\":true,\"label\":\"Trending Now\"},{\"id\":\"best_selling\",\"enabled\":true,\"label\":\"Best Selling\"},{\"id\":\"ad_slot_2\",\"enabled\":true,\"label\":\"hrfh\"},{\"id\":\"social_proof\",\"enabled\":true,\"label\":\"Social proof\"},{\"id\":\"recommendations\",\"enabled\":true,\"label\":\"Recommended For You\"},{\"id\":\"custom_row_1\",\"enabled\":true,\"label\":\"md\"}]}','2026-03-17 22:57:47','2026-03-27 04:34:42'),(143,'scrollbar.settings','{\"enabled\": 1}','2026-03-27 12:43:56','2026-03-27 12:43:56'),(144,'scrollbar.custom.element','{\"title\":\"[Custom] 2026-03-27 16:17\",\"display_order\":1,\"position\":\"custom\",\"template\":\"glass\",\"status\":1,\"visibility\":\"public\",\"visibility_users\":\"all\",\"visibility_pages\":\"custom_urls\",\"custom_urls\":\"\\/product\\/tshirt-tsrt-8\",\"custom_url_mode\":\"contains\",\"schedule_start\":null,\"schedule_end\":null,\"scroll_speed\":45,\"page_speeds\":[],\"scroll_direction\":\"ltr\",\"loop_mode\":\"infinite\",\"pause_on_hover\":1,\"gap_between_items\":8,\"animation_type\":\"linear\",\"bar_height\":52,\"bar_size\":\"medium\",\"bar_thickness\":\"normal\",\"default_text_size\":\"normal\",\"default_text_weight\":\"normal\",\"bar_padding\":null,\"width_type\":\"full\",\"width_value\":\"\",\"max_width\":\"\",\"bar_background_type\":\"solid\",\"bar_background_value\":\"#ffffff\",\"bar_border\":null,\"bar_shadow\":null,\"hide_on_mobile\":0,\"hide_on_desktop\":0,\"container_mode\":\"full\",\"align\":\"center\",\"z_index\":10,\"sticky\":0,\"offset_top\":\"0px\",\"custom_x_percent\":0,\"custom_y_px\":0,\"custom_width_percent\":100,\"loop_delay\":0,\"item_animation\":\"none\",\"icon_animation\":\"none\",\"hover_effect\":\"pause\",\"items\":[{\"type\":\"text\",\"content\":\"\\u098f\\u0987 \\u09b2\\u09bf\\u0982\\u0995\\u09c7 \\u09a5\\u09be\\u0995\\u09be \\u09ab\\u09bf\\u099a\\u09be\\u09b0\\u0997\\u09c1\\u09b2\\u09cb \\u0986\\u09aa\\u09a1\\u09c7\\u099f \\u0995\\u09b0\\u09a4\\u09c7 \\u09b9\\u09ac\\u09c7  \\u098f\\u0996\\u09be\\u09a8\\u09c7 \\u0995\\u09be\\u09b8\\u09cd\\u099f\\u09ae \\u09b2\\u09bf\\u0999\\u09cd\\u0995 \\u098f \\u09ac\\u09be \\u0995\\u09be\\u09b8\\u09cd\\u099f\\u09ae \\u09aa\\u09c7\\u099c\\u09c7 \\u0986\\u09ae\\u09bf Scroll Bar  \\u09ac\\u09bf\\u09ad\\u09bf\\u09a8\\u09cd\\u09a8 \\u09aa\\u09c7\\u099c\\u09c7 \\u09aa\\u09be\\u09ac\\u09b2\\u09bf\\u0995 \\u09aa\\u09c7\\u099c\\u09c7 \\u09b6\\u09cb \\u0995\\u09b0\\u09be\\u09ac\\u09cb \\u09b8\\u09c7\\u0987 \\u09ad\\u09be\\u09ac\\u09c7\\u0987 \\u09b8\\u09ae\\u09b8\\u09cd\\u09a4 \\u09ac\\u09bf\\u099a\\u09be\\u09b0 \\u0986\\u09aa\\u09a1\\u09c7\\u099f \\u0995\\u09b0\\u09c1\\u09a8 \\u098f\\u09ac\\u0982 \\u09aa\\u09cd\\u09b0\\u09bf\\u09ad\\u09bf\\u0989 \\u09b8\\u09c7\\u0995\\u09b6\\u09a8 \\u09b8\\u09a0\\u09bf\\u0995\\u09ad\\u09be\\u09ac\\u09c7 \\u09b2\\u09c7\\u0996\\u09be\\u0997\\u09c1\\u09b2\\u09cb \\u09b8\\u09cd\\u09aa\\u09b7\\u09cd\\u099f\\u09ad\\u09be\\u09ac\\u09c7 \\u09a6\\u09c7\\u0996\\u09be \\u09af\\u09be\\u099a\\u09cd\\u099b\\u09c7 \\u09a8\\u09be \\u098f\\u09ac\\u0982 \\u09b8\\u09a0\\u09bf\\u0995 \\u09aa\\u099c\\u09bf\\u09b6\\u09a8\\u09c7 \\u09a6\\u09c7\\u0996\\u09be \\u09af\\u09be\\u099a\\u09cd\\u099b\\u09c7 \\u09a8\\u09be \\u09aa\\u09cd\\u09b0\\u09bf\\u09ad\\u09bf\\u0989 \\u09ac\\u09be Scroll Bar \\u09ae\\u09be\\u099d\\u0996\\u09be\\u09a8\\u09c7 \\u09b2\\u09c7\\u0996\\u09be\\u0997\\u09c1\\u09b2\\u09cb \\u09a6\\u09c7\\u0996\\u09be \\u09af\\u09be\\u099a\\u09cd\\u099b\\u09c7 \\u09a8\\u09be  \\u09aa\\u09cd\\u09b0\\u09bf\\u09ad\\u09bf\\u0989 \\u09b8\\u09c7\\u0995\\u09b6\\u09a8\\u09c7 \\u09b8\\u09c7\\u0987 \\u09ad\\u09be\\u09ac\\u09c7 \\u0986\\u09aa\\u09a1\\u09c7\\u099f \\u0995\\u09b0\\u09c1\\u09a8\",\"content_text\":\"\\u098f\\u0987 \\u09b2\\u09bf\\u0982\\u0995\\u09c7 \\u09a5\\u09be\\u0995\\u09be \\u09ab\\u09bf\\u099a\\u09be\\u09b0\\u0997\\u09c1\\u09b2\\u09cb \\u0986\\u09aa\\u09a1\\u09c7\\u099f \\u0995\\u09b0\\u09a4\\u09c7 \\u09b9\\u09ac\\u09c7  \\u098f\\u0996\\u09be\\u09a8\\u09c7 \\u0995\\u09be\\u09b8\\u09cd\\u099f\\u09ae \\u09b2\\u09bf\\u0999\\u09cd\\u0995 \\u098f \\u09ac\\u09be \\u0995\\u09be\\u09b8\\u09cd\\u099f\\u09ae \\u09aa\\u09c7\\u099c\\u09c7 \\u0986\\u09ae\\u09bf Scroll Bar  \\u09ac\\u09bf\\u09ad\\u09bf\\u09a8\\u09cd\\u09a8 \\u09aa\\u09c7\\u099c\\u09c7 \\u09aa\\u09be\\u09ac\\u09b2\\u09bf\\u0995 \\u09aa\\u09c7\\u099c\\u09c7 \\u09b6\\u09cb \\u0995\\u09b0\\u09be\\u09ac\\u09cb \\u09b8\\u09c7\\u0987 \\u09ad\\u09be\\u09ac\\u09c7\\u0987 \\u09b8\\u09ae\\u09b8\\u09cd\\u09a4 \\u09ac\\u09bf\\u099a\\u09be\\u09b0 \\u0986\\u09aa\\u09a1\\u09c7\\u099f \\u0995\\u09b0\\u09c1\\u09a8 \\u098f\\u09ac\\u0982 \\u09aa\\u09cd\\u09b0\\u09bf\\u09ad\\u09bf\\u0989 \\u09b8\\u09c7\\u0995\\u09b6\\u09a8 \\u09b8\\u09a0\\u09bf\\u0995\\u09ad\\u09be\\u09ac\\u09c7 \\u09b2\\u09c7\\u0996\\u09be\\u0997\\u09c1\\u09b2\\u09cb \\u09b8\\u09cd\\u09aa\\u09b7\\u09cd\\u099f\\u09ad\\u09be\\u09ac\\u09c7 \\u09a6\\u09c7\\u0996\\u09be \\u09af\\u09be\\u099a\\u09cd\\u099b\\u09c7 \\u09a8\\u09be \\u098f\\u09ac\\u0982 \\u09b8\\u09a0\\u09bf\\u0995 \\u09aa\\u099c\\u09bf\\u09b6\\u09a8\\u09c7 \\u09a6\\u09c7\\u0996\\u09be \\u09af\\u09be\\u099a\\u09cd\\u099b\\u09c7 \\u09a8\\u09be \\u09aa\\u09cd\\u09b0\\u09bf\\u09ad\\u09bf\\u0989 \\u09ac\\u09be Scroll Bar \\u09ae\\u09be\\u099d\\u0996\\u09be\\u09a8\\u09c7 \\u09b2\\u09c7\\u0996\\u09be\\u0997\\u09c1\\u09b2\\u09cb \\u09a6\\u09c7\\u0996\\u09be \\u09af\\u09be\\u099a\\u09cd\\u099b\\u09c7 \\u09a8\\u09be  \\u09aa\\u09cd\\u09b0\\u09bf\\u09ad\\u09bf\\u0989 \\u09b8\\u09c7\\u0995\\u09b6\\u09a8\\u09c7 \\u09b8\\u09c7\\u0987 \\u09ad\\u09be\\u09ac\\u09c7 \\u0986\\u09aa\\u09a1\\u09c7\\u099f \\u0995\\u09b0\\u09c1\\u09a8\",\"color\":\"#333333\",\"font_size\":\"\",\"font_weight\":\"400\",\"font_family\":\"inherit\",\"font_style\":\"normal\",\"letter_spacing\":\"\",\"text_transform\":\"none\",\"is_active\":1,\"segments\":[{\"text\":\"\\u098f\\u0987 \\u09b2\\u09bf\\u0982\\u0995\\u09c7 \\u09a5\\u09be\\u0995\\u09be \\u09ab\\u09bf\\u099a\\u09be\\u09b0\\u0997\\u09c1\\u09b2\\u09cb \\u0986\\u09aa\\u09a1\\u09c7\\u099f \\u0995\\u09b0\\u09a4\\u09c7 \\u09b9\\u09ac\\u09c7\",\"color\":\"#e11d48\",\"weight\":\"\",\"font_family\":\"Arial, sans-serif\",\"font_size\":\"11pt\"},{\"text\":\" \\u098f\\u0996\\u09be\\u09a8\\u09c7 \\u0995\\u09be\\u09b8\\u09cd\\u099f\\u09ae \\u09b2\\u09bf\\u0999\\u09cd\\u0995 \\u098f \\u09ac\\u09be \\u0995\\u09be\\u09b8\\u09cd\\u099f\\u09ae \\u09aa\\u09c7\\u099c\\u09c7 \\u0986\\u09ae\\u09bf Scroll Bar  \\u09ac\\u09bf\\u09ad\\u09bf\\u09a8\\u09cd\\u09a8 \\u09aa\\u09c7\\u099c\\u09c7 \\u09aa\\u09be\\u09ac\\u09b2\\u09bf\\u0995 \\u09aa\\u09c7\\u099c\\u09c7 \\u09b6\\u09cb \\u0995\\u09b0\\u09be\\u09ac\\u09cb \\u09b8\\u09c7\\u0987 \\u09ad\\u09be\\u09ac\\u09c7\\u0987 \\u09b8\\u09ae\\u09b8\\u09cd\\u09a4 \\u09ac\\u09bf\\u099a\\u09be\\u09b0 \\u0986\\u09aa\\u09a1\\u09c7\\u099f \\u0995\\u09b0\\u09c1\\u09a8 \\u098f\\u09ac\\u0982 \\u09aa\\u09cd\\u09b0\\u09bf\\u09ad\\u09bf\\u0989 \\u09b8\\u09c7\\u0995\\u09b6\\u09a8 \\u09b8\\u09a0\\u09bf\\u0995\\u09ad\\u09be\\u09ac\\u09c7 \\u09b2\\u09c7\\u0996\\u09be\\u0997\\u09c1\\u09b2\\u09cb \\u09b8\\u09cd\\u09aa\\u09b7\\u09cd\\u099f\\u09ad\\u09be\\u09ac\\u09c7 \\u09a6\\u09c7\\u0996\\u09be \\u09af\\u09be\\u099a\\u09cd\\u099b\\u09c7 \\u09a8\\u09be \\u098f\\u09ac\\u0982 \\u09b8\\u09a0\\u09bf\\u0995 \\u09aa\\u099c\\u09bf\\u09b6\\u09a8\\u09c7 \\u09a6\\u09c7\\u0996\\u09be \\u09af\\u09be\\u099a\\u09cd\\u099b\\u09c7 \\u09a8\\u09be \\u09aa\\u09cd\\u09b0\\u09bf\\u09ad\\u09bf\\u0989 \\u09ac\\u09be Scroll Bar \\u09ae\\u09be\\u099d\\u0996\\u09be\\u09a8\\u09c7 \\u09b2\\u09c7\\u0996\\u09be\\u0997\\u09c1\\u09b2\\u09cb \\u09a6\\u09c7\\u0996\\u09be \\u09af\\u09be\\u099a\\u09cd\\u099b\\u09c7 \\u09a8\\u09be  \\u09aa\\u09cd\\u09b0\\u09bf\\u09ad\\u09bf\\u0989 \\u09b8\\u09c7\\u0995\\u09b6\\u09a8\\u09c7 \\u09b8\\u09c7\\u0987 \\u09ad\\u09be\\u09ac\\u09c7 \\u0986\\u09aa\\u09a1\\u09c7\\u099f \\u0995\\u09b0\\u09c1\\u09a8 \",\"color\":\"#000000\",\"weight\":\"\",\"font_family\":\"Arial, sans-serif\",\"font_size\":\"11pt\"}]}]}','2026-03-27 10:17:23','2026-03-27 10:17:23'),(145,'header_icons.content','{\"products_icon\":\"box\",\"contact_icon\":\"phone\",\"track_order_icon\":\"shipping-fast\",\"language_icon\":\"language\",\"notification_icon\":\"bell\",\"wishlist_icon\":\"heart\",\"compare_icon\":\"exchange-alt\",\"cart_icon\":\"shopping-cart\",\"orders_icon\":\"list-alt\",\"login_icon\":\"user\"}','2026-03-31 12:49:43','2026-03-31 12:49:43'),(153,'banner.element','{\"display_order\":\"1\",\"animation_type\":\"none\",\"image\":\"banner_69cc2d8d1adcf1774988685.jpg\",\"layout_type\":\"hero_full_width\",\"visibility\":\"public\",\"is_active\":1,\"banner_content\":{\"title\":\"\",\"subtitle\":\"\",\"description\":\"\",\"badge\":\"\",\"button_text\":\"\",\"button_url\":\"\",\"icon\":\"\",\"overlay_color\":\"rgba(0,0,0,0.3)\",\"overlay_opacity\":\"0.3\",\"title_font_size\":\"\",\"title_font_weight\":\"700\",\"title_align\":\"center\",\"text_color\":\"#ffffff\"}}','2026-03-31 14:24:22','2026-03-31 14:24:45'),(154,'banner.element','{\"display_order\":\"2\",\"animation_type\":\"none\",\"image\":\"banner_69cc2db252de61774988722.jpg\",\"layout_type\":\"hero_full_width\",\"visibility\":\"public\",\"is_active\":1,\"banner_content\":{\"title\":\"\",\"subtitle\":\"\",\"description\":\"\",\"badge\":\"\",\"button_text\":\"\",\"button_url\":\"\",\"icon\":\"\",\"overlay_color\":\"rgba(0,0,0,0.3)\",\"overlay_opacity\":\"0.3\",\"title_font_size\":\"\",\"title_font_weight\":\"700\",\"title_align\":\"center\",\"text_color\":\"#ffffff\"}}','2026-03-31 14:25:15','2026-03-31 14:25:22'),(155,'banner.element','{\"display_order\":\"3\",\"animation_type\":\"none\",\"image\":\"banner_69cc2dbf9e70a1774988735.jpg\",\"layout_type\":\"hero_full_width\",\"visibility\":\"public\",\"is_active\":1,\"banner_content\":{\"title\":\"\",\"subtitle\":\"\",\"description\":\"\",\"badge\":\"\",\"button_text\":\"\",\"button_url\":\"\",\"icon\":\"\",\"overlay_color\":\"rgba(0,0,0,0.3)\",\"overlay_opacity\":\"0.3\",\"title_font_size\":\"\",\"title_font_weight\":\"700\",\"title_align\":\"center\",\"text_color\":\"#ffffff\"}}','2026-03-31 14:25:26','2026-03-31 14:25:35'),(157,'banner.element','{\"display_order\":\"4\",\"animation_type\":\"none\",\"image\":\"banner_69cc2e3d296201774988861.jpg\",\"layout_type\":\"hero_full_width\",\"visibility\":\"public\",\"is_active\":1,\"banner_content\":{\"title\":\"\",\"subtitle\":\"\",\"description\":\"\",\"badge\":\"\",\"button_text\":\"\",\"button_url\":\"\",\"icon\":\"\",\"overlay_color\":\"rgba(0,0,0,0.3)\",\"overlay_opacity\":\"0.3\",\"title_font_size\":\"\",\"title_font_weight\":\"700\",\"title_align\":\"center\",\"text_color\":\"#ffffff\"}}','2026-03-31 14:27:34','2026-03-31 14:27:41'),(158,'scrollbar.element','{\"title\":\"[Below Banner (home)] 2026-04-01 12:39\",\"display_order\":1,\"position\":\"banner_above\",\"template\":\"glass\",\"status\":1,\"visibility\":\"private\",\"visibility_users\":\"all\",\"visibility_pages\":\"all\",\"custom_urls\":\"\",\"custom_url_mode\":\"contains\",\"schedule_start\":null,\"schedule_end\":null,\"scroll_speed\":45,\"page_speeds\":[],\"scroll_direction\":\"ltr\",\"loop_mode\":\"infinite\",\"pause_on_hover\":1,\"gap_between_items\":8,\"animation_type\":\"linear\",\"bar_height\":20,\"bar_size\":\"medium\",\"bar_thickness\":\"thin\",\"default_text_size\":\"normal\",\"default_text_weight\":\"extrabold\",\"bar_padding\":null,\"width_type\":\"full\",\"width_value\":\"\",\"max_width\":\"\",\"bar_background_type\":\"solid\",\"bar_background_value\":\"#0dcaf0\",\"bar_border\":null,\"bar_shadow\":null,\"hide_on_mobile\":0,\"hide_on_desktop\":0,\"container_mode\":\"full\",\"align\":\"center\",\"z_index\":10,\"sticky\":0,\"offset_top\":\"0px\",\"custom_x_percent\":0,\"custom_y_px\":0,\"custom_width_percent\":100,\"loop_delay\":0,\"item_animation\":\"none\",\"icon_animation\":\"none\",\"hover_effect\":\"pause\",\"items\":[{\"type\":\"text\",\"content\":\"\\ud83d\\ude9a Type and insert emoji together in one editor. Emoji click keeps cursor typing flow active. \\ud83d\\ude9a\",\"content_text\":\"\\ud83d\\ude9a Type and insert emoji together in one editor. Emoji click keeps cursor typing flow active. \\ud83d\\ude9a\",\"color\":\"#333333\",\"font_size\":\"\",\"font_weight\":\"400\",\"font_family\":\"inherit\",\"font_style\":\"normal\",\"letter_spacing\":\"\",\"text_transform\":\"none\",\"is_active\":1,\"segments\":[{\"text\":\"\\ud83d\\ude9a\",\"color\":\"#6c757d\",\"weight\":\"\",\"font_family\":\"Poppins, sans-serif\",\"font_size\":\"\"},{\"text\":\"Type and insert emoji together in one editor. Emoji click keeps cursor typing flow active.\",\"color\":\"#6c757d\",\"weight\":\"\",\"font_family\":\"Poppins, sans-serif\",\"font_size\":\"\"},{\"text\":\"\\ud83d\\ude9a\",\"color\":\"#6c757d\",\"weight\":\"\",\"font_family\":\"Poppins, sans-serif\",\"font_size\":\"\"}]}]}','2026-04-01 06:39:33','2026-04-02 10:58:56'),(159,'scrollbar.custom.element','{\"title\":\"[Above Every Product Line (Home)] 2026-04-01 12:55\",\"display_order\":1,\"position\":\"product_line\",\"template\":\"glass\",\"status\":1,\"visibility\":\"public\",\"visibility_users\":\"all\",\"visibility_pages\":\"custom_urls\",\"custom_urls\":\"\",\"custom_url_mode\":\"contains\",\"schedule_start\":null,\"schedule_end\":null,\"scroll_speed\":45,\"page_speeds\":[],\"scroll_direction\":\"ltr\",\"loop_mode\":\"infinite\",\"pause_on_hover\":1,\"gap_between_items\":8,\"animation_type\":\"linear\",\"bar_height\":50,\"bar_size\":\"medium\",\"bar_thickness\":\"thin\",\"default_text_size\":\"normal\",\"default_text_weight\":\"medium\",\"bar_padding\":null,\"width_type\":\"full\",\"width_value\":\"\",\"max_width\":\"\",\"bar_background_type\":\"solid\",\"bar_background_value\":\"#ffffff\",\"bar_border\":null,\"bar_shadow\":null,\"hide_on_mobile\":0,\"hide_on_desktop\":0,\"container_mode\":\"full\",\"align\":\"center\",\"z_index\":10,\"sticky\":0,\"offset_top\":\"0px\",\"custom_x_percent\":0,\"custom_y_px\":0,\"custom_width_percent\":100,\"loop_delay\":0,\"item_animation\":\"none\",\"icon_animation\":\"none\",\"hover_effect\":\"pause\",\"items\":[{\"type\":\"text\",\"content\":\"\\ud83d\\udcab \\u098f\\u0995\\u099f\\u09bf \\u09b8\\u09cd\\u0995\\u09cd\\u09b0\\u09b2 \\u09ac\\u09be\\u09b0  \\u09a4\\u09c8 \\u09b0\\u09bf \\u0995\\u09b0\\u09be\\u09b0 \\u099c\\u09a8\\u09cd\\u09af \\u09b8\\u09ae\\u09b8\\u09cd\\u09a4 \\u09ab\\u09bf \\u099a\\u09be\\u09b0 \\u09af\\u09c1\\u0995\\u09cd\\u09a4 \\u0995\\u09b0\\u09c1\\u09a8 \\u098f\\u09ac\\u0982 \\u09aa\\u09cd\\u09b0\\u09a4\\u09bf\\u099f\\u09be \\u09ab\\u09bf\\u099a\\u09be\\u09b0\\u0987 \\u0995\\u09cd\\u09af\\u09be\\u099f\\u09be\\u0997\\u09b0\\u09bf\\u099c \\ud83d\\udce6 \\u09a5\\u09be\\u0995\\u09ac\\u09c7 \\u09af\\u09be\\u09a4\\u09c7 \\u09b8\\u09b9\\u099c\\u09c7\\u0987 \\u098f\\u09a1\\u09ae\\u09bf\\u09a8 \\u09ac\\u09c1\\u099d\\u09a4\\u09c7 \\u09aa\\u09be\\u09b0\\u09c7 \\ud83d\\udcb8\",\"content_text\":\"\\ud83d\\udcab \\u098f\\u0995\\u099f\\u09bf \\u09b8\\u09cd\\u0995\\u09cd\\u09b0\\u09b2 \\u09ac\\u09be\\u09b0  \\u09a4\\u09c8 \\u09b0\\u09bf \\u0995\\u09b0\\u09be\\u09b0 \\u099c\\u09a8\\u09cd\\u09af \\u09b8\\u09ae\\u09b8\\u09cd\\u09a4 \\u09ab\\u09bf \\u099a\\u09be\\u09b0 \\u09af\\u09c1\\u0995\\u09cd\\u09a4 \\u0995\\u09b0\\u09c1\\u09a8 \\u098f\\u09ac\\u0982 \\u09aa\\u09cd\\u09b0\\u09a4\\u09bf\\u099f\\u09be \\u09ab\\u09bf\\u099a\\u09be\\u09b0\\u0987 \\u0995\\u09cd\\u09af\\u09be\\u099f\\u09be\\u0997\\u09b0\\u09bf\\u099c \\ud83d\\udce6 \\u09a5\\u09be\\u0995\\u09ac\\u09c7 \\u09af\\u09be\\u09a4\\u09c7 \\u09b8\\u09b9\\u099c\\u09c7\\u0987 \\u098f\\u09a1\\u09ae\\u09bf\\u09a8 \\u09ac\\u09c1\\u099d\\u09a4\\u09c7 \\u09aa\\u09be\\u09b0\\u09c7 \\ud83d\\udcb8\",\"color\":\"#333333\",\"font_size\":\"\",\"font_weight\":\"400\",\"font_family\":\"inherit\",\"font_style\":\"normal\",\"letter_spacing\":\"\",\"text_transform\":\"none\",\"is_active\":1,\"segments\":[{\"text\":\"\\ud83d\\udcab\",\"color\":\"#000000\",\"weight\":\"\",\"font_family\":\"Poppins, sans-serif\",\"font_size\":\"\"},{\"text\":\"\\u098f\\u0995\\u099f\\u09bf \\u09b8\\u09cd\\u0995\\u09cd\\u09b0\\u09b2 \\u09ac\\u09be\\u09b0  \\u09a4\\u09c8\",\"color\":\"#000000\",\"weight\":\"\",\"font_family\":\"Poppins, sans-serif\",\"font_size\":\"\"},{\"text\":\"\\u09b0\\u09bf \\u0995\\u09b0\\u09be\\u09b0 \\u099c\\u09a8\\u09cd\\u09af \\u09b8\\u09ae\\u09b8\\u09cd\\u09a4 \\u09ab\\u09bf\",\"color\":\"#22c55e\",\"weight\":\"\",\"font_family\":\"Poppins, sans-serif\",\"font_size\":\"\"},{\"text\":\"\\u099a\\u09be\\u09b0 \\u09af\\u09c1\\u0995\\u09cd\\u09a4 \\u0995\\u09b0\\u09c1\\u09a8 \\u098f\\u09ac\\u0982 \\u09aa\\u09cd\\u09b0\\u09a4\\u09bf\\u099f\\u09be \\u09ab\\u09bf\\u099a\\u09be\\u09b0\\u0987 \\u0995\\u09cd\\u09af\\u09be\\u099f\\u09be\\u0997\\u09b0\\u09bf\\u099c\",\"color\":\"#000000\",\"weight\":\"\",\"font_family\":\"Poppins, sans-serif\",\"font_size\":\"\"},{\"text\":\"\\ud83d\\udce6\",\"color\":\"#000000\",\"weight\":\"\",\"font_family\":\"Poppins, sans-serif\",\"font_size\":\"\"},{\"text\":\"\\u09a5\\u09be\\u0995\\u09ac\\u09c7 \\u09af\\u09be\\u09a4\\u09c7 \\u09b8\\u09b9\\u099c\\u09c7\\u0987\",\"color\":\"#000000\",\"weight\":\"\",\"font_family\":\"Poppins, sans-serif\",\"font_size\":\"\"},{\"text\":\"\\u098f\\u09a1\\u09ae\\u09bf\\u09a8 \\u09ac\\u09c1\\u099d\\u09a4\\u09c7\",\"color\":\"#14b8a6\",\"weight\":\"\",\"font_family\":\"Poppins, sans-serif\",\"font_size\":\"\"},{\"text\":\"\\u09aa\\u09be\\u09b0\\u09c7\",\"color\":\"#000000\",\"weight\":\"\",\"font_family\":\"Poppins, sans-serif\",\"font_size\":\"\"},{\"text\":\"\\ud83d\\udcb8\",\"color\":\"#000000\",\"weight\":\"\",\"font_family\":\"Poppins, sans-serif\",\"font_size\":\"\"}]}]}','2026-04-01 06:55:39','2026-04-01 06:56:32'),(160,'scrollbar.element','{\"title\":\"[Above Every Product Line (Home)] 2026-04-01 12:59\",\"display_order\":8,\"position\":\"header_below\",\"template\":\"glass\",\"status\":1,\"visibility\":\"private\",\"visibility_users\":\"all\",\"visibility_pages\":\"all\",\"custom_urls\":\"\",\"custom_url_mode\":\"contains\",\"schedule_start\":null,\"schedule_end\":null,\"scroll_speed\":45,\"page_speeds\":[],\"scroll_direction\":\"ltr\",\"loop_mode\":\"infinite\",\"pause_on_hover\":1,\"gap_between_items\":8,\"animation_type\":\"linear\",\"bar_height\":52,\"bar_size\":\"medium\",\"bar_thickness\":\"thin\",\"default_text_size\":\"normal\",\"default_text_weight\":\"normal\",\"bar_padding\":null,\"width_type\":\"full\",\"width_value\":\"\",\"max_width\":\"\",\"bar_background_type\":null,\"bar_background_value\":null,\"bar_border\":null,\"bar_shadow\":null,\"hide_on_mobile\":0,\"hide_on_desktop\":0,\"container_mode\":\"full\",\"align\":\"center\",\"z_index\":10,\"sticky\":0,\"offset_top\":\"0px\",\"custom_x_percent\":0,\"custom_y_px\":0,\"custom_width_percent\":100,\"loop_delay\":0,\"item_animation\":\"none\",\"icon_animation\":\"none\",\"hover_effect\":\"pause\",\"items\":[{\"type\":\"text\",\"content\":\"\\ud83d\\udce3 Live Offer: Welcome! Get 20% OFF on first order. Free shipping over 50 USD. \\ud83d\\uded2 http:\\/\\/localhost\\/staylbd\\/sajaladminopu\\/frontend\\/scrollbar\\/edit\\/108\\ud83d\\udc51 \\u098f\\u0987 \\u09b2\\u09bf\\u0982\\u0995\\u09c7 \\u09a6\\u09c7\\u0996\\u09be \\u09af\\u09be\\u099a\\u09cd\\u099b\\u09c7 \\u098f\\u0995\\u0987 \\u09ab\\u09bf\\u099a\\u09be\\u09b0 \\ud83d\\ude80 \\u09a6\\u09c1\\u0987 \\u099c\\u09be\\u09af\\u09bc\\u0997\\u09be\\u09af\\u09bc \\u09b8\\u09c7\\u0997\\u09c1\\u09b2\\u09cb \\u09b0\\u09bf\\u09ae\\u09c1\\u09ad \\u0995\\u09b0\\u09c1\\u09a8 \\u09a1\\u09ac\\u09b2 \\u09a1\\u09ac\\u09b2 \\u098f\\u0995\\u0987 \\u09ab\\u09bf\\u099a\\u09be\\u09b0 \\u098f\\u09ac\\u0982 \\u0995\\u09a4 \\u09b8\\u09cd\\u09aa\\u09bf\\u09a1\\u09c7 \\u09b8\\u09cd\\u0995\\u09cd\\u09b0\\u09b2 \\u099a\\u09b2\\u09ac\\u09c7 \\u09a4\\u09be \\ud83d\\udef5 \\u0995\\u09a8\\u09cd\\u099f\\u09cd\\u09b0\\u09cb\\u09b2 \\u0995\\u09b0\\u09be\\u09b0 \\u099c\\u09a8\\u09cd\\u09af \\u09ab\\u09bf\\u099a\\u09be\\u09b0 \\u09a6\\u09c7\\u0996\\u09be \\u09af\\u09be\\u099a\\u09cd\\u099b\\u09c7 \\u09a8\\u09be \\u0986\\u09aa\\u09a1\\u09c7\\u099f \\u0995\\u09b0\\u09c1\\u09a8 \\u09af\\u09a4 \\u2705 \\u09ac\\u09a1\\u09bc\\u0987 \\u09b2\\u09c7\\u0996\\u09be \\u09b9\\u09cb\\u0995 \\u09a8\\u09be \\u0995\\u09c7\\u09a8 \\u09ac\\u09be \\u09af\\u09a4 \\u099b\\u09cb\\u099f \\u09b2\\u09c7\\u0996\\u0995 \\u09a8\\u09be \\u0995\\u09c7\\u09a8 \\ud83d\\udccc \\u09af\\u09a4 \\u0995\\u09cd\\u09af\\u09be\\u09b0\\u09c7\\u0995\\u09cd\\u099f\\u09be\\u09b0 \\u09b2\\u09c7\\u0996\\u09be \\u09b9\\u09cb\\u0995 \\u09a8\\u09be \\u0995\\u09c7\\u09a8 \\u09b8\\u09cd\\u09aa\\u09cd\\u09b0\\u09bf\\u099f \\ud83c\\udf80 \\u0995\\u09a8\\u09cd\\u099f\\u09cd\\u09b0\\u09cb\\u09b2 \\u0995\\u09b0\\u09be \\u09af\\u09be\\u09ac\\u09c7 \\u09b8\\u09c7\\u0987 \\u09ad\\u09be\\u09ac\\u09c7 \\u0986\\u09aa\\u09a1\\u09c7\\u099f \\u0995\\u09b0\\u09c1\\u09a8 \\u098f\\u0987 \\u09b2\\u09bf\\u0982\\u0995\\u09c7 \\u26a1 \\u09a6\\u09c7\\u0996\\u09be \\u09af\\u09be\\u099a\\u09cd\\u099b\\u09c7 \\u098f\\u0996\\u09a8\\u09cb \\u0985\\u09aa\\u09cd\\u09b0\\u09af\\u09bc\\u09cb\\u099c\\u09a8\\u09c0\\u09af\\u09bc \\u09ab\\u09bf\\u099a\\u09be\\u09b0 \\u09af\\u09c7\\u0997\\u09c1\\u09b2\\u09cb \\ud83d\\uded2\",\"content_text\":\"\\ud83d\\udce3 Live Offer: Welcome! Get 20% OFF on first order. Free shipping over 50 USD. \\ud83d\\uded2 http:\\/\\/localhost\\/staylbd\\/sajaladminopu\\/frontend\\/scrollbar\\/edit\\/108\\ud83d\\udc51 \\u098f\\u0987 \\u09b2\\u09bf\\u0982\\u0995\\u09c7 \\u09a6\\u09c7\\u0996\\u09be \\u09af\\u09be\\u099a\\u09cd\\u099b\\u09c7 \\u098f\\u0995\\u0987 \\u09ab\\u09bf\\u099a\\u09be\\u09b0 \\ud83d\\ude80 \\u09a6\\u09c1\\u0987 \\u099c\\u09be\\u09af\\u09bc\\u0997\\u09be\\u09af\\u09bc \\u09b8\\u09c7\\u0997\\u09c1\\u09b2\\u09cb \\u09b0\\u09bf\\u09ae\\u09c1\\u09ad \\u0995\\u09b0\\u09c1\\u09a8 \\u09a1\\u09ac\\u09b2 \\u09a1\\u09ac\\u09b2 \\u098f\\u0995\\u0987 \\u09ab\\u09bf\\u099a\\u09be\\u09b0 \\u098f\\u09ac\\u0982 \\u0995\\u09a4 \\u09b8\\u09cd\\u09aa\\u09bf\\u09a1\\u09c7 \\u09b8\\u09cd\\u0995\\u09cd\\u09b0\\u09b2 \\u099a\\u09b2\\u09ac\\u09c7 \\u09a4\\u09be \\ud83d\\udef5 \\u0995\\u09a8\\u09cd\\u099f\\u09cd\\u09b0\\u09cb\\u09b2 \\u0995\\u09b0\\u09be\\u09b0 \\u099c\\u09a8\\u09cd\\u09af \\u09ab\\u09bf\\u099a\\u09be\\u09b0 \\u09a6\\u09c7\\u0996\\u09be \\u09af\\u09be\\u099a\\u09cd\\u099b\\u09c7 \\u09a8\\u09be \\u0986\\u09aa\\u09a1\\u09c7\\u099f \\u0995\\u09b0\\u09c1\\u09a8 \\u09af\\u09a4 \\u2705 \\u09ac\\u09a1\\u09bc\\u0987 \\u09b2\\u09c7\\u0996\\u09be \\u09b9\\u09cb\\u0995 \\u09a8\\u09be \\u0995\\u09c7\\u09a8 \\u09ac\\u09be \\u09af\\u09a4 \\u099b\\u09cb\\u099f \\u09b2\\u09c7\\u0996\\u0995 \\u09a8\\u09be \\u0995\\u09c7\\u09a8 \\ud83d\\udccc \\u09af\\u09a4 \\u0995\\u09cd\\u09af\\u09be\\u09b0\\u09c7\\u0995\\u09cd\\u099f\\u09be\\u09b0 \\u09b2\\u09c7\\u0996\\u09be \\u09b9\\u09cb\\u0995 \\u09a8\\u09be \\u0995\\u09c7\\u09a8 \\u09b8\\u09cd\\u09aa\\u09cd\\u09b0\\u09bf\\u099f \\ud83c\\udf80 \\u0995\\u09a8\\u09cd\\u099f\\u09cd\\u09b0\\u09cb\\u09b2 \\u0995\\u09b0\\u09be \\u09af\\u09be\\u09ac\\u09c7 \\u09b8\\u09c7\\u0987 \\u09ad\\u09be\\u09ac\\u09c7 \\u0986\\u09aa\\u09a1\\u09c7\\u099f \\u0995\\u09b0\\u09c1\\u09a8 \\u098f\\u0987 \\u09b2\\u09bf\\u0982\\u0995\\u09c7 \\u26a1 \\u09a6\\u09c7\\u0996\\u09be \\u09af\\u09be\\u099a\\u09cd\\u099b\\u09c7 \\u098f\\u0996\\u09a8\\u09cb \\u0985\\u09aa\\u09cd\\u09b0\\u09af\\u09bc\\u09cb\\u099c\\u09a8\\u09c0\\u09af\\u09bc \\u09ab\\u09bf\\u099a\\u09be\\u09b0 \\u09af\\u09c7\\u0997\\u09c1\\u09b2\\u09cb \\ud83d\\uded2\",\"color\":\"#333333\",\"font_size\":\"\",\"font_weight\":\"400\",\"font_family\":\"inherit\",\"font_style\":\"normal\",\"letter_spacing\":\"\",\"text_transform\":\"none\",\"is_active\":1,\"segments\":[{\"text\":\"\\ud83d\\udce3\",\"color\":\"#5b6e88\",\"weight\":\"700\",\"font_family\":\"Poppins, sans-serif\",\"font_size\":\"\"},{\"text\":\"Live Offer: Welcome! Get 20% OFF on first order. Free shipping over 50 USD.\",\"color\":\"#ff0000\",\"weight\":\"700\",\"font_family\":\"Poppins, sans-serif\",\"font_size\":\"\"},{\"text\":\"\\ud83d\\uded2\",\"color\":\"#22c55e\",\"weight\":\"700\",\"font_family\":\"Poppins, sans-serif\",\"font_size\":\"\"},{\"text\":\"http:\\/\\/localhost\\/staylbd\\/sajaladminopu\\/frontend\\/scrollbar\\/edit\\/108\\ud83d\\udc51\",\"color\":\"#22c55e\",\"weight\":\"700\",\"font_family\":\"Poppins, sans-serif\",\"font_size\":\"\"},{\"text\":\"\\u098f\\u0987 \\u09b2\\u09bf\\u0982\\u0995\\u09c7 \\u09a6\\u09c7\\u0996\\u09be \\u09af\\u09be\\u099a\\u09cd\\u099b\\u09c7 \\u098f\\u0995\\u0987 \\u09ab\\u09bf\\u099a\\u09be\\u09b0\",\"color\":\"#3b82f6\",\"weight\":\"\",\"font_family\":\"Poppins, sans-serif\",\"font_size\":\"\"},{\"text\":\"\\ud83d\\ude80\",\"color\":\"#3b82f6\",\"weight\":\"\",\"font_family\":\"Poppins, sans-serif\",\"font_size\":\"\"},{\"text\":\"\\u09a6\\u09c1\\u0987 \\u099c\\u09be\\u09af\\u09bc\\u0997\\u09be\\u09af\\u09bc \\u09b8\\u09c7\\u0997\\u09c1\\u09b2\\u09cb \\u09b0\\u09bf\\u09ae\\u09c1\\u09ad \\u0995\\u09b0\\u09c1\\u09a8 \\u09a1\\u09ac\\u09b2\",\"color\":\"#000000\",\"weight\":\"\",\"font_family\":\"Poppins, sans-serif\",\"font_size\":\"\"},{\"text\":\"\\u09a1\\u09ac\\u09b2 \\u098f\\u0995\\u0987 \\u09ab\\u09bf\\u099a\\u09be\\u09b0 \\u098f\\u09ac\\u0982 \\u0995\\u09a4 \\u09b8\\u09cd\\u09aa\\u09bf\\u09a1\\u09c7 \\u09b8\\u09cd\\u0995\\u09cd\\u09b0\\u09b2 \\u099a\\u09b2\\u09ac\\u09c7 \\u09a4\\u09be\",\"color\":\"#ff00ea\",\"weight\":\"\",\"font_family\":\"Poppins, sans-serif\",\"font_size\":\"\"},{\"text\":\"\\ud83d\\udef5\",\"color\":\"#ff00ea\",\"weight\":\"\",\"font_family\":\"Poppins, sans-serif\",\"font_size\":\"\"},{\"text\":\"\\u0995\\u09a8\\u09cd\\u099f\\u09cd\\u09b0\\u09cb\\u09b2 \\u0995\\u09b0\\u09be\\u09b0 \\u099c\\u09a8\\u09cd\\u09af \\u09ab\\u09bf\\u099a\\u09be\\u09b0 \\u09a6\\u09c7\\u0996\\u09be \\u09af\\u09be\\u099a\\u09cd\\u099b\\u09c7 \\u09a8\\u09be \\u0986\\u09aa\\u09a1\\u09c7\\u099f \\u0995\\u09b0\\u09c1\\u09a8 \\u09af\\u09a4\",\"color\":\"#000000\",\"weight\":\"\",\"font_family\":\"Poppins, sans-serif\",\"font_size\":\"\"},{\"text\":\"\\u2705\",\"color\":\"#000000\",\"weight\":\"\",\"font_family\":\"Poppins, sans-serif\",\"font_size\":\"\"},{\"text\":\"\\u09ac\\u09a1\\u09bc\\u0987 \\u09b2\\u09c7\\u0996\\u09be \\u09b9\\u09cb\\u0995 \\u09a8\\u09be \\u0995\\u09c7\\u09a8 \\u09ac\\u09be \\u09af\\u09a4 \\u099b\\u09cb\\u099f \\u09b2\\u09c7\\u0996\\u0995 \\u09a8\\u09be \\u0995\\u09c7\\u09a8\",\"color\":\"#0ea5e9\",\"weight\":\"\",\"font_family\":\"Poppins, sans-serif\",\"font_size\":\"\"},{\"text\":\"\\ud83d\\udccc\",\"color\":\"#0ea5e9\",\"weight\":\"\",\"font_family\":\"Poppins, sans-serif\",\"font_size\":\"\"},{\"text\":\"\\u09af\\u09a4 \\u0995\\u09cd\\u09af\\u09be\\u09b0\\u09c7\\u0995\\u09cd\\u099f\\u09be\\u09b0 \\u09b2\\u09c7\\u0996\\u09be \\u09b9\\u09cb\\u0995 \\u09a8\\u09be \\u0995\\u09c7\\u09a8 \\u09b8\\u09cd\\u09aa\\u09cd\\u09b0\\u09bf\\u099f\",\"color\":\"#000000\",\"weight\":\"\",\"font_family\":\"Poppins, sans-serif\",\"font_size\":\"\"},{\"text\":\"\\ud83c\\udf80\",\"color\":\"#000000\",\"weight\":\"\",\"font_family\":\"Poppins, sans-serif\",\"font_size\":\"\"},{\"text\":\"\\u0995\\u09a8\\u09cd\\u099f\\u09cd\\u09b0\\u09cb\\u09b2 \\u0995\\u09b0\\u09be \\u09af\\u09be\\u09ac\\u09c7 \\u09b8\\u09c7\\u0987 \\u09ad\\u09be\\u09ac\\u09c7 \\u0986\\u09aa\\u09a1\\u09c7\\u099f \\u0995\\u09b0\\u09c1\\u09a8 \\u098f\\u0987 \\u09b2\\u09bf\\u0982\\u0995\\u09c7\",\"color\":\"#ef4444\",\"weight\":\"\",\"font_family\":\"Poppins, sans-serif\",\"font_size\":\"\"},{\"text\":\"\\u26a1\",\"color\":\"#ef4444\",\"weight\":\"\",\"font_family\":\"Poppins, sans-serif\",\"font_size\":\"\"},{\"text\":\"\\u09a6\\u09c7\\u0996\\u09be \\u09af\\u09be\\u099a\\u09cd\\u099b\\u09c7 \\u098f\\u0996\\u09a8\\u09cb \\u0985\\u09aa\\u09cd\\u09b0\\u09af\\u09bc\\u09cb\\u099c\\u09a8\\u09c0\\u09af\\u09bc \\u09ab\\u09bf\\u099a\\u09be\\u09b0 \\u09af\\u09c7\\u0997\\u09c1\\u09b2\\u09cb\",\"color\":\"#000000\",\"weight\":\"\",\"font_family\":\"Poppins, sans-serif\",\"font_size\":\"\"},{\"text\":\"\\ud83d\\uded2\",\"color\":\"#000000\",\"weight\":\"\",\"font_family\":\"Poppins, sans-serif\",\"font_size\":\"\"}]}]}','2026-04-01 06:59:39','2026-04-02 10:58:39'),(161,'social_icon.element','{\"title\":\"hi\",\"icon\":\"<i class=\\\"fas fa-asterisk\\\"><\\/i>\",\"url\":\"https:\\/\\/www.ryans.com\\/opuhi\"}','2026-04-02 22:25:22','2026-04-02 22:25:22'),(162,'social_icon.element','{\"title\":\"uo\",\"icon\":\"fas fa-archway\",\"show_on_public\":1,\"custom_icon_svg\":\"\",\"has_image\":\"1\",\"url\":\"https:\\/\\/www.ryans.com\\/opuhiwe\"}','2026-04-02 23:03:26','2026-04-02 23:59:37');
/*!40000 ALTER TABLE `frontends` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `gateway_currencies`
--

DROP TABLE IF EXISTS `gateway_currencies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `gateway_currencies` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(40) DEFAULT NULL,
  `currency` varchar(40) DEFAULT NULL,
  `symbol` varchar(40) DEFAULT NULL,
  `method_code` int(11) DEFAULT NULL,
  `gateway_alias` varchar(40) DEFAULT NULL,
  `min_amount` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `max_amount` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `percent_charge` decimal(5,2) NOT NULL DEFAULT 0.00,
  `fixed_charge` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `rate` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `image` varchar(255) DEFAULT NULL,
  `gateway_parameter` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2026 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `gateway_currencies`
--

LOCK TABLES `gateway_currencies` WRITE;
/*!40000 ALTER TABLE `gateway_currencies` DISABLE KEYS */;
INSERT INTO `gateway_currencies` VALUES (1007,'bKash BDT','BDT','৳',902,'Bkash',1.00000000,1000000.00000000,0.00,0.00000000,1.00000000,NULL,'{\"app_key\":\"0vWQuCRGiUX7EPVjQDr0EUAYtc\",\"app_secret\":\"jcUNPBgbcqEDedNKdvE4G1cAK7D3hCjmJccNPZZBq96QIxxwAMEx\",\"username\":\"01770618567\",\"password\":\"D7DaC<*E*eG\",\"base_url\":\"https:\\/\\/tokenized.sandbox.bka.sh\\/v1.2.0-beta\"}','2025-10-14 14:31:15','2026-02-27 05:38:29'),(2019,'TZSMMPAY BDT','BDT','৳',906,'TZSMMPAY',1.00000000,1000000.00000000,0.00,0.00000000,1.00000000,NULL,'{\"api_key\":\"xB4mEUSZVic4qloJuIzZJzusqZIOOuOodwgVIPwhrc6IIHwcDj\",\"create_url\":\"https:\\/\\/cdn.wintersmm.com\\/api\\/payment\\/create\"}','2025-10-16 00:15:07','2025-10-16 00:15:07'),(2023,'WINTERSMM','BDT','৳',907,'WINTERSMM',10.00000000,100000000.00000000,0.00,0.00000000,1.00000000,NULL,'{\"brand_key\":\"gZ3Oheox6TJSMmOwsO4RRmjem3zS0LJ5htd5YTaqBFOjiIJJJG\",\"create_url\":\"https:\\/\\/cdn.wintersmm.com\\/api\\/payment\\/create\",\"verify_url\":\"https:\\/\\/cdn.wintersmm.com\\/api\\/payment\\/verify\"}','2025-10-16 00:45:58','2025-10-16 00:45:58'),(2024,'Aamarpay BDT','BDT','৳',903,'Aamarpay',1.00000000,1000000.00000000,0.00,0.00000000,1.00000000,NULL,'{\"instruction\":\"Pay with Aamarpay\"}','2026-02-11 07:38:42','2026-02-27 05:38:29'),(2025,'Nagad BDT','BDT','৳',904,'Nagad',1.00000000,1000000.00000000,0.00,0.00000000,1.00000000,NULL,'{\"instruction\":\"Pay with Nagad\"}','2026-02-11 07:38:42','2026-02-27 05:38:29');
/*!40000 ALTER TABLE `gateway_currencies` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `gateways`
--

DROP TABLE IF EXISTS `gateways`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `gateways` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `form_id` int(10) unsigned NOT NULL DEFAULT 0,
  `code` int(11) DEFAULT NULL,
  `name` varchar(40) DEFAULT NULL,
  `alias` varchar(40) NOT NULL DEFAULT 'NULL',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1=>enable, 2=>disable',
  `sort_order` smallint(5) unsigned NOT NULL DEFAULT 0,
  `gateway_parameters` text DEFAULT NULL,
  `supported_currencies` text DEFAULT NULL,
  `crypto` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0: fiat currency, 1: crypto currency',
  `extra` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `logo` varchar(500) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1008 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `gateways`
--

LOCK TABLES `gateways` WRITE;
/*!40000 ALTER TABLE `gateways` DISABLE KEYS */;
INSERT INTO `gateways` VALUES (1,0,101,'Paypal','Paypal',1,0,'{\"paypal_email\":{\"title\":\"PayPal Email\",\"global\":true,\"value\":\"sb-owud61543012@business.example.com\"}}','{\"AUD\":\"AUD\",\"BRL\":\"BRL\",\"CAD\":\"CAD\",\"CZK\":\"CZK\",\"DKK\":\"DKK\",\"EUR\":\"EUR\",\"HKD\":\"HKD\",\"HUF\":\"HUF\",\"INR\":\"INR\",\"ILS\":\"ILS\",\"JPY\":\"JPY\",\"MYR\":\"MYR\",\"MXN\":\"MXN\",\"TWD\":\"TWD\",\"NZD\":\"NZD\",\"NOK\":\"NOK\",\"PHP\":\"PHP\",\"PLN\":\"PLN\",\"GBP\":\"GBP\",\"RUB\":\"RUB\",\"SGD\":\"SGD\",\"SEK\":\"SEK\",\"CHF\":\"CHF\",\"THB\":\"THB\",\"USD\":\"$\"}',0,NULL,NULL,NULL,'2019-09-14 13:14:22','2021-05-21 00:04:38'),(2,0,102,'Perfect Money','PerfectMoney',1,0,'{\"passphrase\":{\"title\":\"ALTERNATE PASSPHRASE\",\"global\":true,\"value\":\"hR26aw02Q1eEeUPSIfuwNypXX\"},\"wallet_id\":{\"title\":\"PM Wallet\",\"global\":false,\"value\":\"\"}}','{\"USD\":\"$\",\"EUR\":\"\\u20ac\"}',0,NULL,NULL,NULL,'2019-09-14 13:14:22','2021-05-21 01:35:33'),(3,0,103,'Stripe Hosted','Stripe',1,0,'{\"secret_key\":{\"title\":\"Secret Key\",\"global\":true,\"value\":\"sk_test_51I6GGiCGv1sRiQlEi5v1or9eR0HVbuzdMd2rW4n3DxC8UKfz66R4X6n4yYkzvI2LeAIuRU9H99ZpY7XCNFC9xMs500vBjZGkKG\"},\"publishable_key\":{\"title\":\"PUBLISHABLE KEY\",\"global\":true,\"value\":\"pk_test_51I6GGiCGv1sRiQlEOisPKrjBqQqqcFsw8mXNaZ2H2baN6R01NulFS7dKFji1NRRxuchoUTEDdB7ujKcyKYSVc0z500eth7otOM\"}}','{\"USD\":\"USD\",\"AUD\":\"AUD\",\"BRL\":\"BRL\",\"CAD\":\"CAD\",\"CHF\":\"CHF\",\"DKK\":\"DKK\",\"EUR\":\"EUR\",\"GBP\":\"GBP\",\"HKD\":\"HKD\",\"INR\":\"INR\",\"JPY\":\"JPY\",\"MXN\":\"MXN\",\"MYR\":\"MYR\",\"NOK\":\"NOK\",\"NZD\":\"NZD\",\"PLN\":\"PLN\",\"SEK\":\"SEK\",\"SGD\":\"SGD\"}',0,NULL,NULL,NULL,'2019-09-14 13:14:22','2021-05-21 00:48:36'),(4,0,104,'Skrill','Skrill',1,0,'{\"pay_to_email\":{\"title\":\"Skrill Email\",\"global\":true,\"value\":\"merchant@skrill.com\"},\"secret_key\":{\"title\":\"Secret Key\",\"global\":true,\"value\":\"---\"}}','{\"AED\":\"AED\",\"AUD\":\"AUD\",\"BGN\":\"BGN\",\"BHD\":\"BHD\",\"CAD\":\"CAD\",\"CHF\":\"CHF\",\"CZK\":\"CZK\",\"DKK\":\"DKK\",\"EUR\":\"EUR\",\"GBP\":\"GBP\",\"HKD\":\"HKD\",\"HRK\":\"HRK\",\"HUF\":\"HUF\",\"ILS\":\"ILS\",\"INR\":\"INR\",\"ISK\":\"ISK\",\"JOD\":\"JOD\",\"JPY\":\"JPY\",\"KRW\":\"KRW\",\"KWD\":\"KWD\",\"MAD\":\"MAD\",\"MYR\":\"MYR\",\"NOK\":\"NOK\",\"NZD\":\"NZD\",\"OMR\":\"OMR\",\"PLN\":\"PLN\",\"QAR\":\"QAR\",\"RON\":\"RON\",\"RSD\":\"RSD\",\"SAR\":\"SAR\",\"SEK\":\"SEK\",\"SGD\":\"SGD\",\"THB\":\"THB\",\"TND\":\"TND\",\"TRY\":\"TRY\",\"TWD\":\"TWD\",\"USD\":\"USD\",\"ZAR\":\"ZAR\",\"COP\":\"COP\"}',0,NULL,NULL,NULL,'2019-09-14 13:14:22','2021-05-21 01:30:16'),(5,0,105,'PayTM','Paytm',1,0,'{\"MID\":{\"title\":\"Merchant ID\",\"global\":true,\"value\":\"DIY12386817555501617\"},\"merchant_key\":{\"title\":\"Merchant Key\",\"global\":true,\"value\":\"bKMfNxPPf_QdZppa\"},\"WEBSITE\":{\"title\":\"Paytm Website\",\"global\":true,\"value\":\"DIYtestingweb\"},\"INDUSTRY_TYPE_ID\":{\"title\":\"Industry Type\",\"global\":true,\"value\":\"Retail\"},\"CHANNEL_ID\":{\"title\":\"CHANNEL ID\",\"global\":true,\"value\":\"WEB\"},\"transaction_url\":{\"title\":\"Transaction URL\",\"global\":true,\"value\":\"https:\\/\\/pguat.paytm.com\\/oltp-web\\/processTransaction\"},\"transaction_status_url\":{\"title\":\"Transaction STATUS URL\",\"global\":true,\"value\":\"https:\\/\\/pguat.paytm.com\\/paytmchecksum\\/paytmCallback.jsp\"}}','{\"AUD\":\"AUD\",\"ARS\":\"ARS\",\"BDT\":\"BDT\",\"BRL\":\"BRL\",\"BGN\":\"BGN\",\"CAD\":\"CAD\",\"CLP\":\"CLP\",\"CNY\":\"CNY\",\"COP\":\"COP\",\"HRK\":\"HRK\",\"CZK\":\"CZK\",\"DKK\":\"DKK\",\"EGP\":\"EGP\",\"EUR\":\"EUR\",\"GEL\":\"GEL\",\"GHS\":\"GHS\",\"HKD\":\"HKD\",\"HUF\":\"HUF\",\"INR\":\"INR\",\"IDR\":\"IDR\",\"ILS\":\"ILS\",\"JPY\":\"JPY\",\"KES\":\"KES\",\"MYR\":\"MYR\",\"MXN\":\"MXN\",\"MAD\":\"MAD\",\"NPR\":\"NPR\",\"NZD\":\"NZD\",\"NGN\":\"NGN\",\"NOK\":\"NOK\",\"PKR\":\"PKR\",\"PEN\":\"PEN\",\"PHP\":\"PHP\",\"PLN\":\"PLN\",\"RON\":\"RON\",\"RUB\":\"RUB\",\"SGD\":\"SGD\",\"ZAR\":\"ZAR\",\"KRW\":\"KRW\",\"LKR\":\"LKR\",\"SEK\":\"SEK\",\"CHF\":\"CHF\",\"THB\":\"THB\",\"TRY\":\"TRY\",\"UGX\":\"UGX\",\"UAH\":\"UAH\",\"AED\":\"AED\",\"GBP\":\"GBP\",\"USD\":\"USD\",\"VND\":\"VND\",\"XOF\":\"XOF\"}',0,NULL,NULL,NULL,'2019-09-14 13:14:22','2021-05-21 03:00:44'),(6,0,106,'Payeer','Payeer',1,0,'{\"merchant_id\":{\"title\":\"Merchant ID\",\"global\":true,\"value\":\"866989763\"},\"secret_key\":{\"title\":\"Secret key\",\"global\":true,\"value\":\"7575\"}}','{\"USD\":\"USD\",\"EUR\":\"EUR\",\"RUB\":\"RUB\"}',0,'{\"status\":{\"title\": \"Status URL\",\"value\":\"ipn.Payeer\"}}',NULL,NULL,'2019-09-14 13:14:22','2022-08-28 10:11:14'),(7,0,107,'PayStack','Paystack',1,0,'{\"public_key\":{\"title\":\"Public key\",\"global\":true,\"value\":\"pk_test_cd330608eb47970889bca397ced55c1dd5ad3783\"},\"secret_key\":{\"title\":\"Secret key\",\"global\":true,\"value\":\"sk_test_8a0b1f199362d7acc9c390bff72c4e81f74e2ac3\"}}','{\"USD\":\"USD\",\"NGN\":\"NGN\"}',0,'{\"callback\":{\"title\": \"Callback URL\",\"value\":\"ipn.Paystack\"},\"webhook\":{\"title\": \"Webhook URL\",\"value\":\"ipn.Paystack\"}}\r\n',NULL,NULL,'2019-09-14 13:14:22','2021-05-21 01:49:51'),(8,0,108,'VoguePay','Voguepay',1,0,'{\"merchant_id\":{\"title\":\"MERCHANT ID\",\"global\":true,\"value\":\"demo\"}}','{\"USD\":\"USD\",\"GBP\":\"GBP\",\"EUR\":\"EUR\",\"GHS\":\"GHS\",\"NGN\":\"NGN\",\"ZAR\":\"ZAR\"}',0,NULL,NULL,NULL,'2019-09-14 13:14:22','2021-05-21 01:22:38'),(9,0,109,'Flutterwave','Flutterwave',1,0,'{\"public_key\":{\"title\":\"Public Key\",\"global\":true,\"value\":\"----------------\"},\"secret_key\":{\"title\":\"Secret Key\",\"global\":true,\"value\":\"-----------------------\"},\"encryption_key\":{\"title\":\"Encryption Key\",\"global\":true,\"value\":\"------------------\"}}','{\"BIF\":\"BIF\",\"CAD\":\"CAD\",\"CDF\":\"CDF\",\"CVE\":\"CVE\",\"EUR\":\"EUR\",\"GBP\":\"GBP\",\"GHS\":\"GHS\",\"GMD\":\"GMD\",\"GNF\":\"GNF\",\"KES\":\"KES\",\"LRD\":\"LRD\",\"MWK\":\"MWK\",\"MZN\":\"MZN\",\"NGN\":\"NGN\",\"RWF\":\"RWF\",\"SLL\":\"SLL\",\"STD\":\"STD\",\"TZS\":\"TZS\",\"UGX\":\"UGX\",\"USD\":\"USD\",\"XAF\":\"XAF\",\"XOF\":\"XOF\",\"ZMK\":\"ZMK\",\"ZMW\":\"ZMW\",\"ZWD\":\"ZWD\"}',0,NULL,NULL,NULL,'2019-09-14 13:14:22','2021-06-05 11:37:45'),(10,0,110,'RazorPay','Razorpay',1,0,'{\"key_id\":{\"title\":\"Key Id\",\"global\":true,\"value\":\"rzp_test_kiOtejPbRZU90E\"},\"key_secret\":{\"title\":\"Key Secret \",\"global\":true,\"value\":\"osRDebzEqbsE1kbyQJ4y0re7\"}}','{\"INR\":\"INR\"}',0,NULL,NULL,NULL,'2019-09-14 13:14:22','2021-05-21 02:51:32'),(11,0,111,'Stripe Storefront','StripeJs',1,0,'{\"secret_key\":{\"title\":\"Secret Key\",\"global\":true,\"value\":\"sk_test_51I6GGiCGv1sRiQlEi5v1or9eR0HVbuzdMd2rW4n3DxC8UKfz66R4X6n4yYkzvI2LeAIuRU9H99ZpY7XCNFC9xMs500vBjZGkKG\"},\"publishable_key\":{\"title\":\"PUBLISHABLE KEY\",\"global\":true,\"value\":\"pk_test_51I6GGiCGv1sRiQlEOisPKrjBqQqqcFsw8mXNaZ2H2baN6R01NulFS7dKFji1NRRxuchoUTEDdB7ujKcyKYSVc0z500eth7otOM\"}}','{\"USD\":\"USD\",\"AUD\":\"AUD\",\"BRL\":\"BRL\",\"CAD\":\"CAD\",\"CHF\":\"CHF\",\"DKK\":\"DKK\",\"EUR\":\"EUR\",\"GBP\":\"GBP\",\"HKD\":\"HKD\",\"INR\":\"INR\",\"JPY\":\"JPY\",\"MXN\":\"MXN\",\"MYR\":\"MYR\",\"NOK\":\"NOK\",\"NZD\":\"NZD\",\"PLN\":\"PLN\",\"SEK\":\"SEK\",\"SGD\":\"SGD\"}',0,NULL,NULL,NULL,'2019-09-14 13:14:22','2021-05-21 00:53:10'),(12,0,112,'Instamojo','Instamojo',1,0,'{\"api_key\":{\"title\":\"API KEY\",\"global\":true,\"value\":\"test_2241633c3bc44a3de84a3b33969\"},\"auth_token\":{\"title\":\"Auth Token\",\"global\":true,\"value\":\"test_279f083f7bebefd35217feef22d\"},\"salt\":{\"title\":\"Salt\",\"global\":true,\"value\":\"19d38908eeff4f58b2ddda2c6d86ca25\"}}','{\"INR\":\"INR\"}',0,NULL,NULL,NULL,'2019-09-14 13:14:22','2021-05-21 02:56:20'),(13,0,501,'Blockchain','Blockchain',1,0,'{\"api_key\":{\"title\":\"API Key\",\"global\":true,\"value\":\"55529946-05ca-48ff-8710-f279d86b1cc5\"},\"xpub_code\":{\"title\":\"XPUB CODE\",\"global\":true,\"value\":\"xpub6CKQ3xxWyBoFAF83izZCSFUorptEU9AF8TezhtWeMU5oefjX3sFSBw62Lr9iHXPkXmDQJJiHZeTRtD9Vzt8grAYRhvbz4nEvBu3QKELVzFK\"}}','{\"BTC\":\"BTC\"}',1,NULL,NULL,NULL,'2019-09-14 13:14:22','2022-03-21 07:41:56'),(15,0,503,'CoinPayments','Coinpayments',1,0,'{\"public_key\":{\"title\":\"Public Key\",\"global\":true,\"value\":\"---------------\"},\"private_key\":{\"title\":\"Private Key\",\"global\":true,\"value\":\"------------\"},\"merchant_id\":{\"title\":\"Merchant ID\",\"global\":true,\"value\":\"93a1e014c4ad60a7980b4a7239673cb4\"}}','{\"BTC\":\"Bitcoin\",\"BTC.LN\":\"Bitcoin (Lightning Network)\",\"LTC\":\"Litecoin\",\"CPS\":\"CPS Coin\",\"VLX\":\"Velas\",\"APL\":\"Apollo\",\"AYA\":\"Aryacoin\",\"BAD\":\"Badcoin\",\"BCD\":\"Bitcoin Diamond\",\"BCH\":\"Bitcoin Cash\",\"BCN\":\"Bytecoin\",\"BEAM\":\"BEAM\",\"BITB\":\"Bean Cash\",\"BLK\":\"BlackCoin\",\"BSV\":\"Bitcoin SV\",\"BTAD\":\"Bitcoin Adult\",\"BTG\":\"Bitcoin Gold\",\"BTT\":\"BitTorrent\",\"CLOAK\":\"CloakCoin\",\"CLUB\":\"ClubCoin\",\"CRW\":\"Crown\",\"CRYP\":\"CrypticCoin\",\"CRYT\":\"CryTrExCoin\",\"CURE\":\"CureCoin\",\"DASH\":\"DASH\",\"DCR\":\"Decred\",\"DEV\":\"DeviantCoin\",\"DGB\":\"DigiByte\",\"DOGE\":\"Dogecoin\",\"EBST\":\"eBoost\",\"EOS\":\"EOS\",\"ETC\":\"Ether Classic\",\"ETH\":\"Ethereum\",\"ETN\":\"Electroneum\",\"EUNO\":\"EUNO\",\"EXP\":\"EXP\",\"Expanse\":\"Expanse\",\"FLASH\":\"FLASH\",\"GAME\":\"GameCredits\",\"GLC\":\"Goldcoin\",\"GRS\":\"Groestlcoin\",\"KMD\":\"Komodo\",\"LOKI\":\"LOKI\",\"LSK\":\"LSK\",\"MAID\":\"MaidSafeCoin\",\"MUE\":\"MonetaryUnit\",\"NAV\":\"NAV Coin\",\"NEO\":\"NEO\",\"NMC\":\"Namecoin\",\"NVST\":\"NVO Token\",\"NXT\":\"NXT\",\"OMNI\":\"OMNI\",\"PINK\":\"PinkCoin\",\"PIVX\":\"PIVX\",\"POT\":\"PotCoin\",\"PPC\":\"Peercoin\",\"PROC\":\"ProCurrency\",\"PURA\":\"PURA\",\"QTUM\":\"QTUM\",\"RES\":\"Resistance\",\"RVN\":\"Ravencoin\",\"RVR\":\"RevolutionVR\",\"SBD\":\"Steem Dollars\",\"SMART\":\"SmartCash\",\"SOXAX\":\"SOXAX\",\"STEEM\":\"STEEM\",\"STRAT\":\"STRAT\",\"SYS\":\"Syscoin\",\"TPAY\":\"TokenPay\",\"TRIGGERS\":\"Triggers\",\"TRX\":\" TRON\",\"UBQ\":\"Ubiq\",\"UNIT\":\"UniversalCurrency\",\"USDT\":\"Tether USD (Omni Layer)\",\"USDT.BEP20\":\"Tether USD (BSC Chain)\",\"USDT.ERC20\":\"Tether USD (ERC20)\",\"USDT.TRC20\":\"Tether USD (Tron/TRC20)\",\"VTC\":\"Vertcoin\",\"WAVES\":\"Waves\",\"XCP\":\"Counterparty\",\"XEM\":\"NEM\",\"XMR\":\"Monero\",\"XSN\":\"Stakenet\",\"XSR\":\"SucreCoin\",\"XVG\":\"VERGE\",\"XZC\":\"ZCoin\",\"ZEC\":\"ZCash\",\"ZEN\":\"Horizen\"}',1,NULL,NULL,NULL,'2019-09-14 13:14:22','2021-05-21 02:07:14'),(16,0,504,'CoinPayments Fiat','CoinpaymentsFiat',1,0,'{\"merchant_id\":{\"title\":\"Merchant ID\",\"global\":true,\"value\":\"6515561\"}}','{\"USD\":\"USD\",\"AUD\":\"AUD\",\"BRL\":\"BRL\",\"CAD\":\"CAD\",\"CHF\":\"CHF\",\"CLP\":\"CLP\",\"CNY\":\"CNY\",\"DKK\":\"DKK\",\"EUR\":\"EUR\",\"GBP\":\"GBP\",\"HKD\":\"HKD\",\"INR\":\"INR\",\"ISK\":\"ISK\",\"JPY\":\"JPY\",\"KRW\":\"KRW\",\"NZD\":\"NZD\",\"PLN\":\"PLN\",\"RUB\":\"RUB\",\"SEK\":\"SEK\",\"SGD\":\"SGD\",\"THB\":\"THB\",\"TWD\":\"TWD\"}',0,NULL,NULL,NULL,'2019-09-14 13:14:22','2021-05-21 02:07:44'),(17,0,505,'Coingate','Coingate',1,0,'{\"api_key\":{\"title\":\"API Key\",\"global\":true,\"value\":\"6354mwVCEw5kHzRJ6thbGo-N\"}}','{\"USD\":\"USD\",\"EUR\":\"EUR\"}',0,NULL,NULL,NULL,'2019-09-14 13:14:22','2022-03-30 09:24:57'),(18,0,506,'Coinbase Commerce','CoinbaseCommerce',1,0,'{\"api_key\":{\"title\":\"API Key\",\"global\":true,\"value\":\"c47cd7df-d8e8-424b-a20a\"},\"secret\":{\"title\":\"Webhook Shared Secret\",\"global\":true,\"value\":\"55871878-2c32-4f64-ab66\"}}','{\"USD\":\"USD\",\"EUR\":\"EUR\",\"JPY\":\"JPY\",\"GBP\":\"GBP\",\"AUD\":\"AUD\",\"CAD\":\"CAD\",\"CHF\":\"CHF\",\"CNY\":\"CNY\",\"SEK\":\"SEK\",\"NZD\":\"NZD\",\"MXN\":\"MXN\",\"SGD\":\"SGD\",\"HKD\":\"HKD\",\"NOK\":\"NOK\",\"KRW\":\"KRW\",\"TRY\":\"TRY\",\"RUB\":\"RUB\",\"INR\":\"INR\",\"BRL\":\"BRL\",\"ZAR\":\"ZAR\",\"AED\":\"AED\",\"AFN\":\"AFN\",\"ALL\":\"ALL\",\"AMD\":\"AMD\",\"ANG\":\"ANG\",\"AOA\":\"AOA\",\"ARS\":\"ARS\",\"AWG\":\"AWG\",\"AZN\":\"AZN\",\"BAM\":\"BAM\",\"BBD\":\"BBD\",\"BDT\":\"BDT\",\"BGN\":\"BGN\",\"BHD\":\"BHD\",\"BIF\":\"BIF\",\"BMD\":\"BMD\",\"BND\":\"BND\",\"BOB\":\"BOB\",\"BSD\":\"BSD\",\"BTN\":\"BTN\",\"BWP\":\"BWP\",\"BYN\":\"BYN\",\"BZD\":\"BZD\",\"CDF\":\"CDF\",\"CLF\":\"CLF\",\"CLP\":\"CLP\",\"COP\":\"COP\",\"CRC\":\"CRC\",\"CUC\":\"CUC\",\"CUP\":\"CUP\",\"CVE\":\"CVE\",\"CZK\":\"CZK\",\"DJF\":\"DJF\",\"DKK\":\"DKK\",\"DOP\":\"DOP\",\"DZD\":\"DZD\",\"EGP\":\"EGP\",\"ERN\":\"ERN\",\"ETB\":\"ETB\",\"FJD\":\"FJD\",\"FKP\":\"FKP\",\"GEL\":\"GEL\",\"GGP\":\"GGP\",\"GHS\":\"GHS\",\"GIP\":\"GIP\",\"GMD\":\"GMD\",\"GNF\":\"GNF\",\"GTQ\":\"GTQ\",\"GYD\":\"GYD\",\"HNL\":\"HNL\",\"HRK\":\"HRK\",\"HTG\":\"HTG\",\"HUF\":\"HUF\",\"IDR\":\"IDR\",\"ILS\":\"ILS\",\"IMP\":\"IMP\",\"IQD\":\"IQD\",\"IRR\":\"IRR\",\"ISK\":\"ISK\",\"JEP\":\"JEP\",\"JMD\":\"JMD\",\"JOD\":\"JOD\",\"KES\":\"KES\",\"KGS\":\"KGS\",\"KHR\":\"KHR\",\"KMF\":\"KMF\",\"KPW\":\"KPW\",\"KWD\":\"KWD\",\"KYD\":\"KYD\",\"KZT\":\"KZT\",\"LAK\":\"LAK\",\"LBP\":\"LBP\",\"LKR\":\"LKR\",\"LRD\":\"LRD\",\"LSL\":\"LSL\",\"LYD\":\"LYD\",\"MAD\":\"MAD\",\"MDL\":\"MDL\",\"MGA\":\"MGA\",\"MKD\":\"MKD\",\"MMK\":\"MMK\",\"MNT\":\"MNT\",\"MOP\":\"MOP\",\"MRO\":\"MRO\",\"MUR\":\"MUR\",\"MVR\":\"MVR\",\"MWK\":\"MWK\",\"MYR\":\"MYR\",\"MZN\":\"MZN\",\"NAD\":\"NAD\",\"NGN\":\"NGN\",\"NIO\":\"NIO\",\"NPR\":\"NPR\",\"OMR\":\"OMR\",\"PAB\":\"PAB\",\"PEN\":\"PEN\",\"PGK\":\"PGK\",\"PHP\":\"PHP\",\"PKR\":\"PKR\",\"PLN\":\"PLN\",\"PYG\":\"PYG\",\"QAR\":\"QAR\",\"RON\":\"RON\",\"RSD\":\"RSD\",\"RWF\":\"RWF\",\"SAR\":\"SAR\",\"SBD\":\"SBD\",\"SCR\":\"SCR\",\"SDG\":\"SDG\",\"SHP\":\"SHP\",\"SLL\":\"SLL\",\"SOS\":\"SOS\",\"SRD\":\"SRD\",\"SSP\":\"SSP\",\"STD\":\"STD\",\"SVC\":\"SVC\",\"SYP\":\"SYP\",\"SZL\":\"SZL\",\"THB\":\"THB\",\"TJS\":\"TJS\",\"TMT\":\"TMT\",\"TND\":\"TND\",\"TOP\":\"TOP\",\"TTD\":\"TTD\",\"TWD\":\"TWD\",\"TZS\":\"TZS\",\"UAH\":\"UAH\",\"UGX\":\"UGX\",\"UYU\":\"UYU\",\"UZS\":\"UZS\",\"VEF\":\"VEF\",\"VND\":\"VND\",\"VUV\":\"VUV\",\"WST\":\"WST\",\"XAF\":\"XAF\",\"XAG\":\"XAG\",\"XAU\":\"XAU\",\"XCD\":\"XCD\",\"XDR\":\"XDR\",\"XOF\":\"XOF\",\"XPD\":\"XPD\",\"XPF\":\"XPF\",\"XPT\":\"XPT\",\"YER\":\"YER\",\"ZMW\":\"ZMW\",\"ZWL\":\"ZWL\"}\r\n\r\n',0,'{\"endpoint\":{\"title\": \"Webhook Endpoint\",\"value\":\"ipn.CoinbaseCommerce\"}}',NULL,NULL,'2019-09-14 13:14:22','2021-05-21 02:02:47'),(24,0,113,'Paypal Express','PaypalSdk',1,0,'{\"clientId\":{\"title\":\"Paypal Client ID\",\"global\":true,\"value\":\"Ae0-tixtSV7DvLwIh3Bmu7JvHrjh5EfGdXr_cEklKAVjjezRZ747BxKILiBdzlKKyp-W8W_T7CKH1Ken\"},\"clientSecret\":{\"title\":\"Client Secret\",\"global\":true,\"value\":\"EOhbvHZgFNO21soQJT1L9Q00M3rK6PIEsdiTgXRBt2gtGtxwRer5JvKnVUGNU5oE63fFnjnYY7hq3HBA\"}}','{\"AUD\":\"AUD\",\"BRL\":\"BRL\",\"CAD\":\"CAD\",\"CZK\":\"CZK\",\"DKK\":\"DKK\",\"EUR\":\"EUR\",\"HKD\":\"HKD\",\"HUF\":\"HUF\",\"INR\":\"INR\",\"ILS\":\"ILS\",\"JPY\":\"JPY\",\"MYR\":\"MYR\",\"MXN\":\"MXN\",\"TWD\":\"TWD\",\"NZD\":\"NZD\",\"NOK\":\"NOK\",\"PHP\":\"PHP\",\"PLN\":\"PLN\",\"GBP\":\"GBP\",\"RUB\":\"RUB\",\"SGD\":\"SGD\",\"SEK\":\"SEK\",\"CHF\":\"CHF\",\"THB\":\"THB\",\"USD\":\"$\"}',0,NULL,NULL,NULL,'2019-09-14 13:14:22','2021-05-20 23:01:08'),(25,0,114,'Stripe Checkout','StripeV3',1,0,'{\"secret_key\":{\"title\":\"Secret Key\",\"global\":true,\"value\":\"sk_test_51I6GGiCGv1sRiQlEi5v1or9eR0HVbuzdMd2rW4n3DxC8UKfz66R4X6n4yYkzvI2LeAIuRU9H99ZpY7XCNFC9xMs500vBjZGkKG\"},\"publishable_key\":{\"title\":\"PUBLISHABLE KEY\",\"global\":true,\"value\":\"pk_test_51I6GGiCGv1sRiQlEOisPKrjBqQqqcFsw8mXNaZ2H2baN6R01NulFS7dKFji1NRRxuchoUTEDdB7ujKcyKYSVc0z500eth7otOM\"},\"end_point\":{\"title\":\"End Point Secret\",\"global\":true,\"value\":\"whsec_lUmit1gtxwKTveLnSe88xCSDdnPOt8g5\"}}','{\"USD\":\"USD\",\"AUD\":\"AUD\",\"BRL\":\"BRL\",\"CAD\":\"CAD\",\"CHF\":\"CHF\",\"DKK\":\"DKK\",\"EUR\":\"EUR\",\"GBP\":\"GBP\",\"HKD\":\"HKD\",\"INR\":\"INR\",\"JPY\":\"JPY\",\"MXN\":\"MXN\",\"MYR\":\"MYR\",\"NOK\":\"NOK\",\"NZD\":\"NZD\",\"PLN\":\"PLN\",\"SEK\":\"SEK\",\"SGD\":\"SGD\"}',0,'{\"webhook\":{\"title\": \"Webhook Endpoint\",\"value\":\"ipn.StripeV3\"}}',NULL,NULL,'2019-09-14 13:14:22','2021-05-21 00:58:38'),(27,0,115,'Mollie','Mollie',1,0,'{\"mollie_email\":{\"title\":\"Mollie Email \",\"global\":true,\"value\":\"vi@gmail.com\"},\"api_key\":{\"title\":\"API KEY\",\"global\":true,\"value\":\"test_cucfwKTWfft9s337qsVfn5CC4vNkrn\"}}','{\"AED\":\"AED\",\"AUD\":\"AUD\",\"BGN\":\"BGN\",\"BRL\":\"BRL\",\"CAD\":\"CAD\",\"CHF\":\"CHF\",\"CZK\":\"CZK\",\"DKK\":\"DKK\",\"EUR\":\"EUR\",\"GBP\":\"GBP\",\"HKD\":\"HKD\",\"HRK\":\"HRK\",\"HUF\":\"HUF\",\"ILS\":\"ILS\",\"ISK\":\"ISK\",\"JPY\":\"JPY\",\"MXN\":\"MXN\",\"MYR\":\"MYR\",\"NOK\":\"NOK\",\"NZD\":\"NZD\",\"PHP\":\"PHP\",\"PLN\":\"PLN\",\"RON\":\"RON\",\"RUB\":\"RUB\",\"SEK\":\"SEK\",\"SGD\":\"SGD\",\"THB\":\"THB\",\"TWD\":\"TWD\",\"USD\":\"USD\",\"ZAR\":\"ZAR\"}',0,NULL,NULL,NULL,'2019-09-14 13:14:22','2021-05-21 02:44:45'),(30,0,116,'Cashmaal','Cashmaal',1,0,'{\"web_id\":{\"title\":\"Web Id\",\"global\":true,\"value\":\"3748\"},\"ipn_key\":{\"title\":\"IPN Key\",\"global\":true,\"value\":\"546254628759524554647987\"}}','{\"PKR\":\"PKR\",\"USD\":\"USD\"}',0,'{\"webhook\":{\"title\": \"IPN URL\",\"value\":\"ipn.Cashmaal\"}}',NULL,NULL,NULL,'2021-06-22 08:05:04'),(36,0,119,'Mercado Pago','MercadoPago',1,0,'{\"access_token\":{\"title\":\"Access Token\",\"global\":true,\"value\":\"APP_USR-7924565816849832-082312-21941521997fab717db925cf1ea2c190-1071840315\"}}','{\"USD\":\"USD\",\"CAD\":\"CAD\",\"CHF\":\"CHF\",\"DKK\":\"DKK\",\"EUR\":\"EUR\",\"GBP\":\"GBP\",\"NOK\":\"NOK\",\"PLN\":\"PLN\",\"SEK\":\"SEK\",\"AUD\":\"AUD\",\"NZD\":\"NZD\"}',0,NULL,NULL,NULL,NULL,'2022-09-14 07:41:14'),(37,0,120,'Authorize.net','Authorize',1,0,'{\"login_id\":{\"title\":\"Login ID\",\"global\":true,\"value\":\"59e4P9DBcZv\"},\"transaction_key\":{\"title\":\"Transaction Key\",\"global\":true,\"value\":\"47x47TJyLw2E7DbR\"}}','{\"USD\":\"USD\",\"CAD\":\"CAD\",\"CHF\":\"CHF\",\"DKK\":\"DKK\",\"EUR\":\"EUR\",\"GBP\":\"GBP\",\"NOK\":\"NOK\",\"PLN\":\"PLN\",\"SEK\":\"SEK\",\"AUD\":\"AUD\",\"NZD\":\"NZD\"}',0,NULL,NULL,NULL,NULL,'2025-10-11 13:18:25'),(46,0,121,'NMI','NMI',1,0,'{\"api_key\":{\"title\":\"API Key\",\"global\":true,\"value\":\"2F822Rw39fx762MaV7Yy86jXGTC7sCDy\"}}','{\"AED\":\"AED\",\"ARS\":\"ARS\",\"AUD\":\"AUD\",\"BOB\":\"BOB\",\"BRL\":\"BRL\",\"CAD\":\"CAD\",\"CHF\":\"CHF\",\"CLP\":\"CLP\",\"CNY\":\"CNY\",\"COP\":\"COP\",\"DKK\":\"DKK\",\"EUR\":\"EUR\",\"GBP\":\"GBP\",\"HKD\":\"HKD\",\"IDR\":\"IDR\",\"ILS\":\"ILS\",\"INR\":\"INR\",\"JPY\":\"JPY\",\"KRW\":\"KRW\",\"MXN\":\"MXN\",\"MYR\":\"MYR\",\"NOK\":\"NOK\",\"NZD\":\"NZD\",\"PEN\":\"PEN\",\"PHP\":\"PHP\",\"PLN\":\"PLN\",\"PYG\":\"PYG\",\"RUB\":\"RUB\",\"SEC\":\"SEC\",\"SGD\":\"SGD\",\"THB\":\"THB\",\"TRY\":\"TRY\",\"TWD\":\"TWD\",\"USD\":\"USD\",\"ZAR\":\"ZAR\"}',0,NULL,NULL,NULL,NULL,'2022-08-28 10:32:31'),(50,0,507,'BTCPay','BTCPay',1,0,'{\"store_id\":{\"title\":\"Store Id\",\"global\":true,\"value\":\"HsqFVTXSeUFJu7caoYZc3CTnP8g5LErVdHhEXPVTheHf\"},\"api_key\":{\"title\":\"Api Key\",\"global\":true,\"value\":\"4436bd706f99efae69305e7c4eff4780de1335ce\"},\"server_name\":{\"title\":\"Server Name\",\"global\":true,\"value\":\"https:\\/\\/testnet.demo.btcpayserver.org\"},\"secret_code\":{\"title\":\"Secret Code\",\"global\":true,\"value\":\"SUCdqPn9CDkY7RmJHfpQVHP2Lf2\"}}','{\"BTC\":\"Bitcoin\",\"LTC\":\"Litecoin\"}',1,'{\"webhook\":{\"title\": \"IPN URL\",\"value\":\"ipn.BTCPay\"}}',NULL,NULL,NULL,'2023-02-14 04:42:09'),(51,0,508,'Now payments hosted','NowPaymentsHosted',1,0,'{\"api_key\":{\"title\":\"API Key\",\"global\":true,\"value\":\"--------\"},\"secret_key\":{\"title\":\"Secret Key\",\"global\":true,\"value\":\"------------\"}}','{\"BTG\":\"BTG\",\"ETH\":\"ETH\",\"XMR\":\"XMR\",\"ZEC\":\"ZEC\",\"XVG\":\"XVG\",\"ADA\":\"ADA\",\"LTC\":\"LTC\",\"BCH\":\"BCH\",\"QTUM\":\"QTUM\",\"DASH\":\"DASH\",\"XLM\":\"XLM\",\"XRP\":\"XRP\",\"XEM\":\"XEM\",\"DGB\":\"DGB\",\"LSK\":\"LSK\",\"DOGE\":\"DOGE\",\"TRX\":\"TRX\",\"KMD\":\"KMD\",\"REP\":\"REP\",\"BAT\":\"BAT\",\"ARK\":\"ARK\",\"WAVES\":\"WAVES\",\"BNB\":\"BNB\",\"XZC\":\"XZC\",\"NANO\":\"NANO\",\"TUSD\":\"TUSD\",\"VET\":\"VET\",\"ZEN\":\"ZEN\",\"GRS\":\"GRS\",\"FUN\":\"FUN\",\"NEO\":\"NEO\",\"GAS\":\"GAS\",\"PAX\":\"PAX\",\"USDC\":\"USDC\",\"ONT\":\"ONT\",\"XTZ\":\"XTZ\",\"LINK\":\"LINK\",\"RVN\":\"RVN\",\"BNBMAINNET\":\"BNBMAINNET\",\"ZIL\":\"ZIL\",\"BCD\":\"BCD\",\"USDT\":\"USDT\",\"USDTERC20\":\"USDTERC20\",\"CRO\":\"CRO\",\"DAI\":\"DAI\",\"HT\":\"HT\",\"WABI\":\"WABI\",\"BUSD\":\"BUSD\",\"ALGO\":\"ALGO\",\"USDTTRC20\":\"USDTTRC20\",\"GT\":\"GT\",\"STPT\":\"STPT\",\"AVA\":\"AVA\",\"SXP\":\"SXP\",\"UNI\":\"UNI\",\"OKB\":\"OKB\",\"BTC\":\"BTC\"}',1,'',NULL,NULL,NULL,'2023-02-14 05:08:23'),(65,0,122,'TwoCheckout','TwoCheckout',1,0,'{\"merchant_code\":{\"title\":\"Merchant Code\",\"global\":true,\"value\":\"---------\"},\"secret_key\":{\"title\":\"Secret Key\",\"global\":true,\"value\":\"--------\"}}','{\"AFN\": \"AFN\",\"ALL\": \"ALL\",\"DZD\": \"DZD\",\"ARS\": \"ARS\",\"AUD\": \"AUD\",\"AZN\": \"AZN\",\"BSD\": \"BSD\",\"BDT\": \"BDT\",\"BBD\": \"BBD\",\"BZD\": \"BZD\",\"BMD\": \"BMD\",\"BOB\": \"BOB\",\"BWP\": \"BWP\",\"BRL\": \"BRL\",\"GBP\": \"GBP\",\"BND\": \"BND\",\"BGN\": \"BGN\",\"CAD\": \"CAD\",\"CLP\": \"CLP\",\"CNY\": \"CNY\",\"COP\": \"COP\",\"CRC\": \"CRC\",\"HRK\": \"HRK\",\"CZK\": \"CZK\",\"DKK\": \"DKK\",\"DOP\": \"DOP\",\"XCD\": \"XCD\",\"EGP\": \"EGP\",\"EUR\": \"EUR\",\"FJD\": \"FJD\",\"GTQ\": \"GTQ\",\"HKD\": \"HKD\",\"HNL\": \"HNL\",\"HUF\": \"HUF\",\"INR\": \"INR\",\"IDR\": \"IDR\",\"ILS\": \"ILS\",\"JMD\": \"JMD\",\"JPY\": \"JPY\",\"KZT\": \"KZT\",\"KES\": \"KES\",\"LAK\": \"LAK\",\"MMK\": \"MMK\",\"LBP\": \"LBP\",\"LRD\": \"LRD\",\"MOP\": \"MOP\",\"MYR\": \"MYR\",\"MVR\": \"MVR\",\"MRO\": \"MRO\",\"MUR\": \"MUR\",\"MXN\": \"MXN\",\"MAD\": \"MAD\",\"NPR\": \"NPR\",\"TWD\": \"TWD\",\"NZD\": \"NZD\",\"NIO\": \"NIO\",\"NOK\": \"NOK\",\"PKR\": \"PKR\",\"PGK\": \"PGK\",\"PEN\": \"PEN\",\"PHP\": \"PHP\",\"PLN\": \"PLN\",\"QAR\": \"QAR\",\"RON\": \"RON\",\"RUB\": \"RUB\",\"WST\": \"WST\",\"SAR\": \"SAR\",\"SCR\": \"SCR\",\"SGD\": \"SGD\",\"SBD\": \"SBD\",\"ZAR\": \"ZAR\",\"KRW\": \"KRW\",\"LKR\": \"LKR\",\"SEK\": \"SEK\",\"CHF\": \"CHF\",\"SYP\": \"SYP\",\"THB\": \"THB\",\"TOP\": \"TOP\",\"TTD\": \"TTD\",\"TRY\": \"TRY\",\"UAH\": \"UAH\",\"AED\": \"AED\",\"USD\": \"USD\",\"VUV\": \"VUV\",\"VND\": \"VND\",\"XOF\": \"XOF\",\"YER\": \"YER\"}',1,'{\"approved_url\":{\"title\": \"Approved URL\",\"value\":\"ipn.TwoCheckout\"}}',NULL,NULL,NULL,'2023-05-25 04:17:21'),(66,0,123,'Checkout','Checkout',1,0,'{\"secret_key\":{\"title\":\"Secret Key\",\"global\":true,\"value\":\"sk_f7f9a069-dcc5-45d8-aa72-e60f605c9514\"},\"public_key\":{\"title\":\"PUBLIC KEY\",\"global\":true,\"value\":\"pk_66e19b3f-a431-44ff-823f-d773d960f6b9\"},\"processing_channel_id\":{\"title\":\"PROCESSING CHANNEL\",\"global\":true,\"value\":\"---\"}}','{\"USD\":\"USD\",\"EUR\":\"EUR\",\"GBP\":\"GBP\",\"HKD\":\"HKD\",\"AUD\":\"AUD\",\"CAN\":\"CAN\",\"CHF\":\"CHF\",\"SGD\":\"SGD\",\"JPY\":\"JPY\",\"NZD\":\"NZD\"}',0,NULL,NULL,NULL,NULL,NULL),(67,1,1000,'WinTerSMM','wintersmm',1,0,'[]','[]',0,NULL,'<br>',NULL,'2025-10-10 12:27:26','2025-10-10 12:27:26'),(69,0,903,'Aamarpay','Aamarpay',1,0,'{\"store_id\":{\"title\":\"Store ID\",\"global\":true,\"value\":\"\"},\"signature_key\":{\"title\":\"Signature Key\",\"global\":true,\"value\":\"\"},\"sandbox\":{\"title\":\"Sandbox Mode\",\"global\":true,\"value\":\"1\"},\"callback_url\":{\"title\":\"Callback URL\",\"global\":true,\"value\":\"\"}}','{\"BDT\":\"৳\"}',0,NULL,'Aamarpay Payment Gateway (Working Implementation)',NULL,'2025-10-13 18:33:31','2026-02-27 05:38:29'),(70,0,904,'Nagad','Nagad',1,0,'{\"merchant_id\":{\"title\":\"Merchant ID\",\"global\":true,\"value\":\"\"},\"merchant_number\":{\"title\":\"Merchant Number\",\"global\":true,\"value\":\"\"},\"private_key\":{\"title\":\"Private Key\",\"global\":true,\"value\":\"\"},\"public_key\":{\"title\":\"Public Key\",\"global\":true,\"value\":\"\"},\"sandbox\":{\"title\":\"Sandbox Mode\",\"global\":true,\"value\":\"1\"},\"callback_url\":{\"title\":\"Callback URL\",\"global\":true,\"value\":\"\"}}','{\"BDT\":\"৳\"}',0,NULL,'Nagad Payment Gateway (Working Implementation)',NULL,'2025-10-13 18:33:31','2026-02-27 05:38:29'),(1005,0,902,'bKash','Bkash',1,0,'{\"sandbox\":{\"title\":\"Sandbox Mode\",\"global\":true,\"value\":\"1\"},\"app_key\":{\"title\":\"App Key\",\"global\":true,\"value\":\"\"},\"app_secret\":{\"title\":\"App Secret\",\"global\":true,\"value\":\"\"},\"username\":{\"title\":\"Username\",\"global\":true,\"value\":\"\"},\"password\":{\"title\":\"Password\",\"global\":true,\"value\":\"\"},\"callback_url\":{\"title\":\"Callback URL\",\"global\":true,\"value\":\"\"}}','{\"BDT\":\"৳\"}',0,NULL,'bKash Tokenized Checkout (Working Implementation)',NULL,'2025-10-14 20:27:58','2026-02-27 05:38:29'),(1006,0,906,'TZSMMPAY','TZSMMPAY',1,0,'{\"api_key\":{\"title\":\"API Key\",\"global\":true,\"value\":\"xB4mEUSZVic4qloJuIzZJzusqZIOOuOodwgVIPwhrc6IIHwcDj\"},\"create_url\":{\"title\":\"Create Payment URL\",\"global\":true,\"value\":\"https:\\/\\/cdn.wintersmm.com\\/api\\/payment\\/create\"}}','{\"USD\":\"$\",\"BDT\":\"৳\",\"EUR\":\"€\",\"GBP\":\"£\"}',0,'{\"callback\":{\"title\":\"Callback Route\",\"value\":\"ipn.TZSMMPAY\"}}','TZSMMPAY Payment Gateway (Configurable from Admin Panel)',NULL,'2025-01-15 06:00:00','2025-10-16 00:15:07'),(1007,0,907,'WINTERSMM','WINTERSMM',1,0,'{\"brand_key\":{\"title\":\"Brand Key\",\"global\":true,\"value\":\"gZ3Oheox6TJSMmOwsO4RRmjem3zS0LJ5htd5YTaqBFOjiIJJJG\"},\"create_url\":{\"title\":\"Create Payment URL\",\"global\":true,\"value\":\"https:\\/\\/cdn.wintersmm.com\\/api\\/payment\\/create\"},\"verify_url\":{\"title\":\"Verify Payment URL\",\"global\":true,\"value\":\"https:\\/\\/cdn.wintersmm.com\\/api\\/payment\\/verify\"}}','{\"USD\":\"$\",\"BDT\":\"৳\",\"EUR\":\"€\",\"GBP\":\"£\"}',0,'{\"callback\":{\"title\":\"Callback Route\",\"value\":\"ipn.WINTERSMM\"}}','WintersMM Payment Gateway',NULL,'2025-01-15 06:00:00','2025-10-16 00:44:56');
/*!40000 ALTER TABLE `gateways` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `general_settings`
--

DROP TABLE IF EXISTS `general_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `general_settings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `stock_out_user_message` text DEFAULT NULL COMMENT 'Message shown to user when order fails due to stock out',
  `stock_out_admin_message` varchar(500) DEFAULT NULL COMMENT 'Admin notification title when customers try to order out-of-stock product',
  `restock_notify_enable` tinyint(4) NOT NULL DEFAULT 1 COMMENT '1=notify users (cart/wishlist/compare) when product back in stock',
  `restock_message_template` text DEFAULT NULL COMMENT 'In-app message for restock notification. Use {product_name}, {product_url}',
  `restock_whatsapp_enable` tinyint(4) NOT NULL DEFAULT 0 COMMENT '1=send restock alert via WhatsApp when configured',
  `restock_whatsapp_message` text DEFAULT NULL COMMENT 'Template for WhatsApp. Use {product_name}, {product_url}',
  `restock_telegram_enable` tinyint(4) NOT NULL DEFAULT 0 COMMENT '1=send restock alert via Telegram when configured',
  `restock_telegram_message` text DEFAULT NULL COMMENT 'Template for Telegram. Use {product_name}, {product_url}',
  `abandoned_cart_inactivity_minutes` smallint(5) unsigned NOT NULL DEFAULT 60,
  `abandoned_cart_reminder_email` tinyint(1) NOT NULL DEFAULT 1,
  `abandoned_cart_reminder_sms` tinyint(1) NOT NULL DEFAULT 0,
  `abandoned_cart_cleanup_days` smallint(5) unsigned NOT NULL DEFAULT 30,
  `site_name` varchar(40) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `logo_dark` varchar(255) DEFAULT NULL,
  `favicon` varchar(255) DEFAULT NULL,
  `notification_logo` varchar(191) DEFAULT NULL,
  `invoice_logo` varchar(191) DEFAULT NULL,
  `invoice_signature` varchar(191) DEFAULT NULL,
  `invoice_authorized_name` varchar(191) DEFAULT NULL,
  `invoice_qr_caption_en` text DEFAULT NULL,
  `invoice_qr_caption_bn` text DEFAULT NULL,
  `logo_effects_enabled` tinyint(1) NOT NULL DEFAULT 0,
  `logo_hover_effect` varchar(50) DEFAULT 'none',
  `logo_animation` varchar(50) DEFAULT 'none',
  `logo_animation_speed` varchar(20) DEFAULT 'normal',
  `logo_opacity` decimal(3,2) NOT NULL DEFAULT 1.00,
  `logo_max_width` int(11) NOT NULL DEFAULT 200,
  `logo_max_height` int(11) NOT NULL DEFAULT 60,
  `footer_logo_height` int(11) NOT NULL DEFAULT 35,
  `cur_text` varchar(40) DEFAULT NULL COMMENT 'currency text',
  `discount` decimal(28,8) unsigned NOT NULL DEFAULT 0.00000000,
  `discount_type` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `quick_order_fields` text DEFAULT NULL,
  `cur_sym` varchar(40) DEFAULT NULL COMMENT 'currency symbol',
  `email_from` varchar(40) DEFAULT NULL,
  `email_template` text DEFAULT NULL,
  `sms_body` varchar(255) DEFAULT NULL,
  `sms_from` varchar(255) DEFAULT NULL,
  `base_color` varchar(40) DEFAULT NULL,
  `secondary_color` varchar(40) DEFAULT NULL,
  `mail_config` text DEFAULT NULL COMMENT 'email configuration',
  `sms_config` text DEFAULT NULL,
  `global_shortcodes` text DEFAULT NULL,
  `kv` tinyint(1) NOT NULL DEFAULT 0,
  `ev` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'email verification, 0 - dont check, 1 - check',
  `en` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'email notification, 0 - dont send, 1 - send',
  `sv` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'mobile verication, 0 - dont check, 1 - check',
  `sn` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'sms notification, 0 - dont send, 1 - send',
  `force_ssl` tinyint(1) NOT NULL DEFAULT 0,
  `maintenance_mode` tinyint(1) NOT NULL DEFAULT 0,
  `secure_password` tinyint(1) NOT NULL DEFAULT 0,
  `agree` tinyint(1) NOT NULL DEFAULT 0,
  `display_stock` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `display_view_count` tinyint(4) NOT NULL DEFAULT 1 COMMENT '1=show view count on product page, 0=hide',
  `multi_language` tinyint(1) NOT NULL DEFAULT 1,
  `floating_login` tinyint(1) NOT NULL DEFAULT 1,
  `floating_register` tinyint(1) NOT NULL DEFAULT 1,
  `registration` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0: Off	, 1: On',
  `active_template` varchar(40) DEFAULT NULL,
  `system_info` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `loyalty_points_per_currency` decimal(10,2) NOT NULL DEFAULT 1.00,
  `loyalty_points_status` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0: Disable, 1: Enable',
  `admin_online_status` tinyint(4) NOT NULL DEFAULT 0 COMMENT '1=Online/Green, 0=Offline/Red',
  `meta_pixel_id` varchar(100) DEFAULT NULL COMMENT 'Meta/Facebook Pixel ID',
  `facebook_access_token` varchar(500) DEFAULT NULL COMMENT 'Facebook Access Token',
  `google_ads_id` varchar(100) DEFAULT NULL COMMENT 'Google Ads / gtag ID',
  `tiktok_pixel_id` varchar(100) DEFAULT NULL COMMENT 'TikTok Pixel ID',
  `product_card_color` varchar(30) DEFAULT '#ffffff',
  `button_color` varchar(30) DEFAULT '#1f2937',
  `button_hover_color` varchar(30) DEFAULT '#374151',
  `rating_star_color` varchar(30) DEFAULT '#f59e0b',
  `discount_badge_color` varchar(30) DEFAULT '#dc2626',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `general_settings`
--

LOCK TABLES `general_settings` WRITE;
/*!40000 ALTER TABLE `general_settings` DISABLE KEYS */;
INSERT INTO `general_settings` VALUES (1,NULL,NULL,1,NULL,0,NULL,0,NULL,60,1,0,30,'demo','logo_b1b17b552207019a4be1410764771251.svg','logo_dark_2eb6b47e909f1d6dea71fe975484ec02.svg','favicon_4dbfe81324d307ef06a2b0fb4545c478.png',NULL,NULL,NULL,NULL,NULL,NULL,0,'none','none','normal',1.00,200,60,35,'BDT',0.00000000,2,NULL,'৳','info@viserlab.com','<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\">\r\n  <!--[if !mso]><!-->\r\n  <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\">\r\n  <!--<![endif]-->\r\n  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\r\n  <title></title>\r\n  <style type=\"text/css\">\r\n.ReadMsgBody { width: 100%; background-color: #ffffff; }\r\n.ExternalClass { width: 100%; background-color: #ffffff; }\r\n.ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td, .ExternalClass div { line-height: 100%; }\r\nhtml { width: 100%; }\r\nbody { -webkit-text-size-adjust: none; -ms-text-size-adjust: none; margin: 0; padding: 0; }\r\ntable { border-spacing: 0; table-layout: fixed; margin: 0 auto;border-collapse: collapse; }\r\ntable table table { table-layout: auto; }\r\n.yshortcuts a { border-bottom: none !important; }\r\nimg:hover { opacity: 0.9 !important; }\r\na { color: #0087ff; text-decoration: none; }\r\n.textbutton a { font-family: \'open sans\', arial, sans-serif !important;}\r\n.btn-link a { color:#FFFFFF !important;}\r\n\r\n@media only screen and (max-width: 480px) {\r\nbody { width: auto !important; }\r\n*[class=\"table-inner\"] { width: 90% !important; text-align: center !important; }\r\n*[class=\"table-full\"] { width: 100% !important; text-align: center !important; }\r\n/* image */\r\nimg[class=\"img1\"] { width: 100% !important; height: auto !important; }\r\n}\r\n</style>\r\n\r\n\r\n\r\n  <table bgcolor=\"#414a51\" width=\"100%\" border=\"0\" align=\"center\" cellpadding=\"0\" cellspacing=\"0\">\r\n    <tbody><tr>\r\n      <td height=\"50\"></td>\r\n    </tr>\r\n    <tr>\r\n      <td align=\"center\" style=\"text-align:center;vertical-align:top;font-size:0;\">\r\n        <table align=\"center\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">\r\n          <tbody><tr>\r\n            <td align=\"center\" width=\"600\">\r\n              <!--header-->\r\n              <table class=\"table-inner\" width=\"95%\" border=\"0\" align=\"center\" cellpadding=\"0\" cellspacing=\"0\">\r\n                <tbody><tr>\r\n                  <td bgcolor=\"#0087ff\" style=\"border-top-left-radius:6px; border-top-right-radius:6px;text-align:center;vertical-align:top;font-size:0;\" align=\"center\">\r\n                    <table width=\"90%\" border=\"0\" align=\"center\" cellpadding=\"0\" cellspacing=\"0\">\r\n                      <tbody><tr>\r\n                        <td height=\"20\"></td>\r\n                      </tr>\r\n                      <tr>\r\n                        <td align=\"center\" style=\"font-family: \'Open sans\', Arial, sans-serif; color:#FFFFFF; font-size:16px; font-weight: bold;\">This is a System Generated Email</td>\r\n                      </tr>\r\n                      <tr>\r\n                        <td height=\"20\"></td>\r\n                      </tr>\r\n                    </tbody></table>\r\n                  </td>\r\n                </tr>\r\n              </tbody></table>\r\n              <!--end header-->\r\n              <table class=\"table-inner\" width=\"95%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\r\n                <tbody><tr>\r\n                  <td bgcolor=\"#FFFFFF\" align=\"center\" style=\"text-align:center;vertical-align:top;font-size:0;\">\r\n                    <table align=\"center\" width=\"90%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\r\n                      <tbody><tr>\r\n                        <td height=\"35\"></td>\r\n                      </tr>\r\n                      <!--logo-->\r\n                      <tr>\r\n                        <td align=\"center\" style=\"vertical-align:top;font-size:0;\">\r\n                          <a href=\"#\">\r\n                            <img style=\"display:block; line-height:0px; font-size:0px; border:0px;\" src=\"https://i.imgur.com/Z1qtvtV.png\" alt=\"img\">\r\n                          </a>\r\n                        </td>\r\n                      </tr>\r\n                      <!--end logo-->\r\n                      <tr>\r\n                        <td height=\"40\"></td>\r\n                      </tr>\r\n                      <!--headline-->\r\n                      <tr>\r\n                        <td align=\"center\" style=\"font-family: \'Open Sans\', Arial, sans-serif; font-size: 22px;color:#414a51;font-weight: bold;\">Hello {{fullname}} ({{username}})</td>\r\n                      </tr>\r\n                      <!--end headline-->\r\n                      <tr>\r\n                        <td align=\"center\" style=\"text-align:center;vertical-align:top;font-size:0;\">\r\n                          <table width=\"40\" border=\"0\" align=\"center\" cellpadding=\"0\" cellspacing=\"0\">\r\n                            <tbody><tr>\r\n                              <td height=\"20\" style=\" border-bottom:3px solid #0087ff;\"></td>\r\n                            </tr>\r\n                          </tbody></table>\r\n                        </td>\r\n                      </tr>\r\n                      <tr>\r\n                        <td height=\"20\"></td>\r\n                      </tr>\r\n                      <!--content-->\r\n                      <tr>\r\n                        <td align=\"left\" style=\"font-family: \'Open sans\', Arial, sans-serif; color:#7f8c8d; font-size:16px; line-height: 28px;\">{{message}}</td>\r\n                      </tr>\r\n                      <!--end content-->\r\n                      <tr>\r\n                        <td height=\"40\"></td>\r\n                      </tr>\r\n              \r\n                    </tbody></table>\r\n                  </td>\r\n                </tr>\r\n                <tr>\r\n                  <td height=\"45\" align=\"center\" bgcolor=\"#f4f4f4\" style=\"border-bottom-left-radius:6px;border-bottom-right-radius:6px;\">\r\n                    <table align=\"center\" width=\"90%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\r\n                      <tbody><tr>\r\n                        <td height=\"10\"></td>\r\n                      </tr>\r\n                      <!--preference-->\r\n                      <tr>\r\n                        <td class=\"preference-link\" align=\"center\" style=\"font-family: \'Open sans\', Arial, sans-serif; color:#95a5a6; font-size:14px;\">\r\n                          Â© 2021 <a href=\"#\">{{site_name}}</a>&nbsp;. All Rights Reserved. \r\n                        </td>\r\n                      </tr>\r\n                      <!--end preference-->\r\n                      <tr>\r\n                        <td height=\"10\"></td>\r\n                      </tr>\r\n                    </tbody></table>\r\n                  </td>\r\n                </tr>\r\n              </tbody></table>\r\n            </td>\r\n          </tr>\r\n        </tbody></table>\r\n      </td>\r\n    </tr>\r\n    <tr>\r\n      <td height=\"60\"></td>\r\n    </tr>\r\n  </tbody></table>','hi {{fullname}} ({{username}}), {{message}}','ViserAdmin','3e8804','060662','{\"name\":\"php\"}','{\"name\":\"nexmo\",\"clickatell\":{\"api_key\":\"----------------\"},\"infobip\":{\"username\":\"------------8888888\",\"password\":\"-----------------\"},\"message_bird\":{\"api_key\":\"-------------------\"},\"nexmo\":{\"api_key\":\"----------------------\",\"api_secret\":\"----------------------\"},\"sms_broadcast\":{\"username\":\"----------------------\",\"password\":\"-----------------------------\"},\"twilio\":{\"account_sid\":\"-----------------------\",\"auth_token\":\"---------------------------\",\"from\":\"----------------------\"},\"text_magic\":{\"username\":\"-----------------------\",\"apiv2_key\":\"-------------------------------\"},\"custom\":{\"method\":\"get\",\"url\":\"https:\\/\\/hostname\\/demo-api-v1\",\"headers\":{\"name\":[\"api_key\"],\"value\":[\"test_api 555\"]},\"body\":{\"name\":[\"from_number\"],\"value\":[\"5657545757\"]}}}','{\n    \"site_name\":\"Name of your site\",\n    \"site_currency\":\"Currency of your site\",\n    \"currency_symbol\":\"Symbol of currency\"\n}',0,0,1,0,0,0,0,0,1,1,1,1,1,1,1,'basic','[]',NULL,'2026-04-01 06:34:54',1.00,0,0,NULL,NULL,NULL,NULL,'#ffffff','#1f2937','#374151','#f59e0b','#dc2626');
/*!40000 ALTER TABLE `general_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `homepage_ad_slots`
--

DROP TABLE IF EXISTS `homepage_ad_slots`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `homepage_ad_slots` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
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
  `top` int(11) DEFAULT NULL,
  `bottom` int(11) DEFAULT NULL,
  `left` int(11) DEFAULT NULL,
  `right` int(11) DEFAULT NULL,
  `max_height_px` smallint(5) unsigned DEFAULT NULL,
  `size_type` varchar(16) NOT NULL DEFAULT 'auto',
  `custom_width` varchar(16) DEFAULT NULL,
  `custom_height` varchar(16) DEFAULT NULL,
  `z_index` int(11) NOT NULL DEFAULT 1100,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(10) unsigned NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `homepage_ad_slots`
--

LOCK TABLES `homepage_ad_slots` WRITE;
/*!40000 ALTER TABLE `homepage_ad_slots` DISABLE KEYS */;
INSERT INTO `homepage_ad_slots` VALUES (1,'vb',NULL,'69ba6fa765e181773825959.jpg','upload',NULL,NULL,1,'card','half','custom','all',NULL,'bottom',103,164,80,81,80,'custom','305px','93px',1100,1,1,'2026-03-18 03:25:59','2026-04-02 10:55:17'),(2,'hrfh','fhrhj','69c659ae5160b1774606766.jpg','upload',NULL,NULL,1,'thin','full','custom','all',NULL,'bottom',282,12,23,23,400,'auto',NULL,NULL,1100,1,2,'2026-03-27 04:19:26','2026-04-02 11:10:52');
/*!40000 ALTER TABLE `homepage_ad_slots` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `homepage_custom_product_rows`
--

DROP TABLE IF EXISTS `homepage_custom_product_rows`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `homepage_custom_product_rows` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `subtitle` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` smallint(5) unsigned NOT NULL DEFAULT 0,
  `source_type` varchar(20) NOT NULL,
  `category_id` bigint(20) unsigned DEFAULT NULL,
  `product_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`product_ids`)),
  `product_limit` tinyint(3) unsigned NOT NULL DEFAULT 12,
  `interval_seconds` tinyint(3) unsigned DEFAULT NULL,
  `view_all_url` varchar(512) DEFAULT NULL,
  `view_all_label` varchar(120) DEFAULT NULL,
  `split_banner_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`split_banner_json`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `homepage_custom_product_rows_is_active_sort_order_index` (`is_active`,`sort_order`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `homepage_custom_product_rows`
--

LOCK TABLES `homepage_custom_product_rows` WRITE;
/*!40000 ALTER TABLE `homepage_custom_product_rows` DISABLE KEYS */;
INSERT INTO `homepage_custom_product_rows` VALUES (1,'md','opu',1,1,'manual',NULL,'[101]',12,3,NULL,'show all',NULL,'2026-03-17 22:57:47','2026-03-17 23:17:18');
/*!40000 ALTER TABLE `homepage_custom_product_rows` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `homepage_top_features`
--

DROP TABLE IF EXISTS `homepage_top_features`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `homepage_top_features` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `icon_image` varchar(255) DEFAULT NULL,
  `background_style` varchar(255) DEFAULT NULL,
  `product_id` bigint(20) unsigned DEFAULT NULL,
  `category_id` bigint(20) unsigned DEFAULT NULL,
  `offer_price` decimal(18,2) DEFAULT NULL,
  `discount_percentage` decimal(5,2) DEFAULT NULL,
  `offer_start` datetime DEFAULT NULL,
  `offer_end` datetime DEFAULT NULL,
  `redirect_url` varchar(500) DEFAULT NULL,
  `sort_order` int(10) unsigned NOT NULL DEFAULT 0,
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '1=Active, 0=Hidden',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `homepage_top_features_status_offer_end_index` (`status`,`offer_end`),
  KEY `homepage_top_features_product_id_foreign` (`product_id`),
  KEY `homepage_top_features_category_id_foreign` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `homepage_top_features`
--

LOCK TABLES `homepage_top_features` WRITE;
/*!40000 ALTER TABLE `homepage_top_features` DISABLE KEYS */;
/*!40000 ALTER TABLE `homepage_top_features` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `internal_notes`
--

DROP TABLE IF EXISTS `internal_notes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `internal_notes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `conversation_id` bigint(20) unsigned NOT NULL,
  `admin_id` bigint(20) unsigned NOT NULL,
  `note` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `internal_notes_conversation_id_index` (`conversation_id`),
  CONSTRAINT `internal_notes_conversation_id_foreign` FOREIGN KEY (`conversation_id`) REFERENCES `conversations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `internal_notes`
--

LOCK TABLES `internal_notes` WRITE;
/*!40000 ALTER TABLE `internal_notes` DISABLE KEYS */;
/*!40000 ALTER TABLE `internal_notes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `languages`
--

DROP TABLE IF EXISTS `languages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `languages` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(40) DEFAULT NULL,
  `code` varchar(40) DEFAULT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0: not default language, 1: default language',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `languages`
--

LOCK TABLES `languages` WRITE;
/*!40000 ALTER TABLE `languages` DISABLE KEYS */;
INSERT INTO `languages` VALUES (1,'English','en',1,'2020-07-06 03:47:55','2022-04-09 03:47:04'),(5,'Hindi','hn',0,'2020-12-29 02:20:07','2022-04-09 03:47:04'),(9,'Bangla','bn',0,'2021-03-14 04:37:41','2022-03-30 12:31:55');
/*!40000 ALTER TABLE `languages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `loyalty_transactions`
--

DROP TABLE IF EXISTS `loyalty_transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `loyalty_transactions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `points` int(11) NOT NULL DEFAULT 0,
  `type` enum('earned','redeemed','expired','adjusted') NOT NULL DEFAULT 'earned',
  `source` varchar(100) DEFAULT NULL,
  `reference_type` varchar(255) DEFAULT NULL,
  `reference_id` bigint(20) unsigned DEFAULT NULL,
  `description` varchar(500) DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `loyalty_transactions_user_id_index` (`user_id`),
  KEY `loyalty_transactions_type_index` (`type`),
  KEY `loyalty_transactions_expires_at_index` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `loyalty_transactions`
--

LOCK TABLES `loyalty_transactions` WRITE;
/*!40000 ALTER TABLE `loyalty_transactions` DISABLE KEYS */;
/*!40000 ALTER TABLE `loyalty_transactions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `message_channels`
--

DROP TABLE IF EXISTS `message_channels`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `message_channels` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `slug` varchar(32) NOT NULL COMMENT 'web, telegram, whatsapp, facebook, instagram, email',
  `config` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'API keys, webhook URL, etc.' CHECK (json_valid(`config`)),
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `message_channels_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `message_channels`
--

LOCK TABLES `message_channels` WRITE;
/*!40000 ALTER TABLE `message_channels` DISABLE KEYS */;
/*!40000 ALTER TABLE `message_channels` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `message_status_logs`
--

DROP TABLE IF EXISTS `message_status_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `message_status_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `message_id` bigint(20) unsigned NOT NULL,
  `status` varchar(32) NOT NULL COMMENT 'sent, delivered, read',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `message_status_logs_message_id_status_index` (`message_id`,`status`),
  CONSTRAINT `message_status_logs_message_id_foreign` FOREIGN KEY (`message_id`) REFERENCES `omnichannel_messages` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `message_status_logs`
--

LOCK TABLES `message_status_logs` WRITE;
/*!40000 ALTER TABLE `message_status_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `message_status_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `message_templates`
--

DROP TABLE IF EXISTS `message_templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `message_templates` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `body` text NOT NULL,
  `category` varchar(64) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `message_templates`
--

LOCK TABLES `message_templates` WRITE;
/*!40000 ALTER TABLE `message_templates` DISABLE KEYS */;
/*!40000 ALTER TABLE `message_templates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=240 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'2014_10_12_000000_create_users_table',1),(2,'2014_10_12_100000_create_password_resets_table',1),(3,'2019_08_19_000000_create_failed_jobs_table',1),(4,'2019_12_14_000001_create_personal_access_tokens_table',1),(5,'2022_02_26_061836_create_forms_table',1),(6,'2023_02_22_095338_create_user_tokens_table',1),(7,'2023_02_22_101032_create_tokens_table',1),(8,'2023_02_23_144521_create_brands_table',1),(9,'2023_02_23_162048_create_categories_table',1),(10,'2023_02_25_092916_create_subcategories_table',1),(11,'2023_02_25_104148_create_coupons_table',1),(12,'2023_02_25_134428_create_products_table',1),(13,'2023_02_25_140858_create_product_galleries_table',1),(14,'2023_02_26_140953_create_reviews_table',1),(15,'2023_02_26_160717_create_orders_table',1),(16,'2023_02_27_094248_create_wishlists_table',1),(17,'2023_02_27_121428_create_carts_table',1),(18,'2023_02_27_135749_create_shipping_methods_table',1),(19,'2023_02_28_132511_create_order_details_table',1),(20,'2024_01_01_000000_create_courierapis_table',1),(21,'2025_01_31_000000_add_logo_effect_columns_to_general_settings',1),(22,'2025_02_04_000000_create_admin_activity_logs_table',1),(23,'2025_02_04_000001_add_channel_to_support_tickets_table',1),(24,'2025_02_04_100000_create_banner_analytics_table',1),(25,'2025_02_04_100001_create_conversations_table',1),(26,'2025_02_04_100002_create_omnichannel_messages_table',1),(27,'2025_02_04_100003_create_message_channels_table',1),(28,'2025_02_04_100004_create_message_templates_table',1),(29,'2025_02_04_100005_create_auto_responses_table',1),(30,'2025_02_04_100006_create_chat_assignments_table',1),(31,'2025_02_04_100007_create_internal_notes_table',1),(32,'2025_02_04_100008_create_message_status_logs_table',1),(33,'2025_02_04_100009_create_omnichannel_message_attachments_table',1),(34,'2025_02_05_000000_add_age_to_users_table',1),(35,'2025_02_05_100000_add_floating_auth_to_general_settings',1),(36,'2025_10_16_000000_add_social_provider_columns_to_users_table',1),(37,'2026_02_05_191214_add_loyalty_features_to_tables',1),(38,'2026_02_05_200022_create_loyalty_transactions_table',1),(39,'2026_02_05_201755_create_product_comparisons_table',1),(40,'2026_02_08_193500_create_product_variants_system',1),(41,'2026_02_09_000001_add_admin_online_to_general_settings',1),(42,'2026_02_09_100000_add_product_video_and_user_gender',1),(43,'2026_02_11_000000_add_click_url_to_notification_logs_table',1),(44,'2026_02_11_000001_create_contact_channel_integrations_table',1),(45,'2026_02_11_000002_create_contact_channel_messages_table',1),(46,'2026_02_11_000003_add_contact_handles_to_users_table',1),(47,'2026_02_11_000004_add_channel_reference_to_support_tickets_table',1),(48,'2026_02_11_100000_add_keywords_and_name_to_auto_responses',1),(49,'2026_02_11_110000_add_is_public_to_auto_responses',1),(50,'2026_02_11_120000_create_admin_reports_table',1),(51,'2026_02_12_100000_add_publish_status_and_scheduled_at_to_categories_table',1),(52,'2026_02_13_100000_create_homepage_top_features_table',1),(53,'2026_02_14_100000_create_autopay_messages_table',1),(54,'2026_02_15_100000_enforce_single_spotlight_per_product',1),(55,'2026_02_16_100000_add_role_to_admins_table',1),(56,'2026_02_16_120000_add_mobile_to_admins_table',1),(57,'2026_02_16_140000_add_allowed_sections_to_admins_table',1),(58,'2026_02_16_160000_create_deposits_table',1),(59,'2026_02_16_170000_add_deposits_created_at_index',1),(60,'2026_02_16_180000_create_courier_logs_table',1),(61,'2026_02_16_200000_create_cache_table',1),(62,'2026_02_16_200001_create_sessions_table',1),(63,'2026_02_16_210000_create_shipping_zones_table',1),(64,'2026_02_16_210001_create_shipping_zone_countries_table',1),(65,'2026_02_16_210002_create_shipping_zone_areas_table',1),(66,'2026_02_16_210003_add_zone_fields_to_shipping_methods_table',1),(67,'2026_02_16_210004_create_shipping_rules_table',1),(68,'2026_02_16_210005_add_shipping_zone_to_orders_table',1),(69,'2026_02_18_100000_add_product_performance_indexes',1),(70,'2026_02_19_100000_add_product_price_and_slug_indexes',1),(71,'2026_02_19_120000_add_coupon_advanced_fields',1),(72,'2026_02_19_150000_add_key_features_to_products',1),(73,'2026_02_20_100000_add_extensible_fields_to_courierapis_table',1),(74,'2026_02_20_100000_ensure_shipping_zones_table',1),(75,'2026_02_20_100001_create_order_shipment_trackings_table',1),(76,'2026_02_20_120000_add_logo_to_gateways_table',1),(77,'2026_02_21_000001_add_invoice_settings_to_general_settings',1),(78,'2026_02_21_100000_add_show_to_user_and_region_to_courierapis',1),(79,'2026_02_21_100000_ensure_all_shipping_tables',1),(80,'2026_02_21_110000_create_popup_ads_table',2),(81,'2026_02_21_115000_add_position_to_popup_ads_table',2),(82,'2026_02_21_120000_add_display_type_to_popup_ads_table',2),(83,'2026_02_21_120000_add_return_status_to_courier_logs',2),(84,'2026_02_21_120000_add_version_fields_to_extensions_table',2),(85,'2026_02_21_130000_add_force_password_change_to_admins_table',2),(86,'2026_02_21_140000_create_cache_clear_logs_table',2),(87,'2026_02_21_150000_create_seeder_audit_logs_table',2),(88,'2026_02_21_160000_create_permissions_tables',2),(89,'2026_02_21_180000_create_user_search_logs_table',2),(90,'2026_02_21_200000_create_offer_timers_table',2),(91,'2026_02_21_200000_create_security_settings_table',2),(92,'2026_02_21_200001_create_security_audit_logs_table',2),(93,'2026_02_21_210000_add_size_to_offer_timers',2),(94,'2026_02_22_100000_add_last_chat_seen_at_to_users',2),(95,'2026_02_22_100000_add_shipping_zone_advanced_fields',2),(96,'2026_02_22_100000_create_divisions_table',2),(97,'2026_02_22_100001_create_districts_table',2),(98,'2026_02_22_100002_create_thanas_table',2),(99,'2026_02_22_110000_add_two_factor_to_admins_table',2),(100,'2026_02_22_120000_add_status_to_divisions_districts_thanas',2),(101,'2026_02_22_120000_create_payment_events_table',2),(102,'2026_02_22_120000_fix_courier_daily_stats_view',2),(103,'2026_02_22_120001_create_delivery_zones_table',2),(104,'2026_02_22_120002_create_user_saved_addresses_table',2),(105,'2026_02_22_120003_add_location_tracking_to_orders_table',2),(106,'2026_02_22_130000_create_security_events_table',2),(107,'2026_02_22_130000_fix_courier_trigger_definer_for_hosting',2),(108,'2026_02_22_140000_add_indexes_to_reviews_table',2),(109,'2026_02_22_140000_create_admin_sessions_table',2),(110,'2026_02_22_200000_add_admin_notes_to_admins_table',2),(111,'2026_02_22_200001_add_delivery_scan_to_orders_table',2),(112,'2026_02_22_210001_add_invoice_qr_and_driver_scan',2),(113,'2026_02_22_210002_add_delivery_scanned_notification_template',2),(114,'2026_02_22_220001_update_delivery_scanned_template_add_map_link',2),(115,'2026_02_23_100000_add_idempotency_to_payment_events',2),(116,'2026_02_23_100000_advanced_reviews_table',2),(117,'2026_02_23_110000_create_admin_ip_whitelist_table',2),(118,'2026_02_23_120000_create_admin_lockouts_table',2),(119,'2026_02_23_120000_create_product_questions_table',3),(120,'2026_02_23_130000_create_audit_logs_table',3),(121,'2026_02_24_000000_add_is_approved_to_reviews_if_missing',3),(122,'2026_02_24_100000_add_notification_logo_to_general_settings',3),(123,'2026_02_24_100000_add_orders_user_id_index',3),(124,'2026_02_24_100000_add_updated_at_to_user_search_logs',3),(125,'2026_02_24_100000_create_payment_ledger_table',3),(126,'2026_02_24_110000_create_trusted_admin_devices_table',3),(127,'2026_02_24_120000_add_image_path_to_user_search_logs',3),(128,'2026_02_24_140000_add_is_private_to_reviews',3),(129,'2026_02_24_160000_add_read_at_to_notification_logs',3),(130,'2026_02_25_100000_create_user_activity_logs_table',3),(131,'2026_02_26_100000_activity_indexes_and_fraud',3),(132,'2026_02_27_100000_create_cod_settings_table',3),(133,'2026_02_27_100001_add_cod_columns_to_orders_and_zones',3),(134,'2026_02_27_100002_create_cod_blacklists_table',3),(135,'2026_02_27_100003_create_cod_otp_verifications_table',3),(136,'2026_02_27_100004_add_cod_order_status_and_user_risk',3),(137,'2026_02_28_100000_create_payment_transactions_table',3),(138,'2026_02_28_100001_add_sort_order_to_gateways',3),(139,'2026_02_28_100002_create_payment_refunds_table',3),(140,'2026_02_28_100003_create_payment_fraud_attempts_table',3),(141,'2026_02_28_120000_ensure_users_table_has_auth_columns',4),(142,'2026_03_02_100000_add_username_editable_to_users_table',5),(143,'2026_03_02_150000_add_first_order_flag_to_coupons',5),(144,'2026_03_02_160000_add_ad_source_utm_to_orders',5),(145,'2026_03_02_160001_add_meta_pixel_to_general_settings',5),(146,'2026_03_03_170000_add_model_columns_to_admin_activity_logs',6),(147,'2026_03_03_120000_ensure_tracking_columns_on_general_settings',7),(148,'2026_03_03_100000_add_google_tiktok_tracking_to_general_settings',8),(149,'2026_03_03_100001_add_advance_payment_and_staff_notes_to_orders',8),(150,'2026_03_03_100002_create_fraud_guard_tables',8),(151,'2026_03_03_100003_add_tracking_link_to_order_shipment_trackings',8),(152,'2026_03_03_100004_create_revenue_expenses_table',8),(153,'2026_03_09_100000_add_general_product_and_clothing_fields',8),(154,'2026_03_09_100000_create_abandoned_carts_table',8),(155,'2026_03_09_100001_add_abandoned_cart_settings_to_general_settings',8),(156,'2026_03_09_100002_add_abandoned_cart_notification_template',8),(157,'2026_03_10_100000_add_trending_now_to_products',8),(158,'2026_03_11_100000_add_ui_settings_to_general_settings',9),(159,'2026_03_11_120000_create_ui_settings_table',9),(160,'2026_03_11_140000_add_product_card_color_fields_to_ui_settings',9),(161,'2026_03_12_100000_add_delivery_type_and_charge_to_products',9),(162,'2026_03_10_120000_add_indexes_to_products_table',10),(163,'2026_03_12_140000_add_composite_indexes_products_for_homepage',10),(164,'2026_03_13_100000_add_display_view_count_to_general_settings',11),(165,'2026_03_13_120000_add_guest_order_fields_to_orders_table',11),(166,'2026_03_13_150000_add_stock_order_messages_to_general_settings',11),(167,'2026_03_13_160000_add_restock_whatsapp_telegram_to_general_settings',11),(168,'2026_03_13_170000_add_home_line_and_order_to_categories_table',11),(169,'2026_03_18_200000_create_homepage_custom_product_rows_table',12),(170,'2026_03_13_170000_add_homepage_indexes_to_products_table',13),(171,'2026_03_14_100000_ensure_carts_wishlists_columns',13),(172,'2026_03_16_100000_add_order_source_to_orders_table',13),(173,'2026_03_17_100000_add_quick_order_fields_to_general_settings',13),(174,'2026_03_18_100000_backfill_product_slugs_for_clean_urls',13),(175,'2026_03_19_120000_create_homepage_ad_slots_table',13),(176,'2026_03_19_120000_regenerate_short_keyword_id_product_slugs',13),(177,'2026_03_18_210000_add_homepage_section_override_to_products_table',14),(178,'2026_03_27_000000_add_advanced_fields_to_homepage_ad_slots_table',15),(179,'2026_03_27_010000_add_targeting_columns_to_homepage_ad_slots_table',15),(180,'2014_10_12_000000_create_users_table',1),(181,'2014_10_12_100000_create_password_resets_table',1),(182,'2019_08_19_000000_create_failed_jobs_table',1),(183,'2019_12_14_000001_create_personal_access_tokens_table',1),(184,'2022_02_26_061836_create_forms_table',1),(185,'2023_02_22_095338_create_user_tokens_table',1),(186,'2023_02_22_101032_create_tokens_table',1),(187,'2023_02_23_144521_create_brands_table',1),(188,'2023_02_23_162048_create_categories_table',1),(189,'2023_02_25_092916_create_subcategories_table',1),(190,'2023_02_25_104148_create_coupons_table',1),(191,'2023_02_25_134428_create_products_table',1),(192,'2023_02_25_140858_create_product_galleries_table',1),(193,'2023_02_26_140953_create_reviews_table',1),(194,'2023_02_26_160717_create_orders_table',1),(195,'2023_02_27_094248_create_wishlists_table',1),(196,'2023_02_27_121428_create_carts_table',1),(197,'2023_02_27_135749_create_shipping_methods_table',1),(198,'2023_02_28_132511_create_order_details_table',1),(199,'2024_01_01_000000_create_courierapis_table',1),(200,'2025_01_31_000000_add_logo_effect_columns_to_general_settings',1),(201,'2025_02_04_000000_create_admin_activity_logs_table',1),(202,'2025_02_04_000001_add_channel_to_support_tickets_table',1),(203,'2025_02_04_100000_create_banner_analytics_table',1),(204,'2025_02_04_100001_create_conversations_table',1),(205,'2025_02_04_100002_create_omnichannel_messages_table',1),(206,'2025_02_04_100003_create_message_channels_table',1),(207,'2025_02_04_100004_create_message_templates_table',1),(208,'2025_02_04_100005_create_auto_responses_table',1),(209,'2025_02_04_100006_create_chat_assignments_table',1),(210,'2025_02_04_100007_create_internal_notes_table',1),(211,'2025_02_04_100008_create_message_status_logs_table',1),(212,'2025_02_04_100009_create_omnichannel_message_attachments_table',1),(213,'2025_02_05_000000_add_age_to_users_table',1),(214,'2025_02_05_100000_add_floating_auth_to_general_settings',1),(215,'2025_10_16_000000_add_social_provider_columns_to_users_table',1),(216,'2026_02_05_191214_add_loyalty_features_to_tables',1),(217,'2026_02_05_200022_create_loyalty_transactions_table',1),(218,'2026_02_05_201755_create_product_comparisons_table',1),(219,'2026_02_08_193500_create_product_variants_system',1),(220,'2026_02_09_000001_add_admin_online_to_general_settings',1),(221,'2026_02_09_100000_add_product_video_and_user_gender',1),(222,'2026_02_11_000000_add_click_url_to_notification_logs_table',1),(223,'2026_02_11_000001_create_contact_channel_integrations_table',1),(224,'2026_02_11_000002_create_contact_channel_messages_table',1),(225,'2026_02_11_000003_add_contact_handles_to_users_table',1),(226,'2026_02_11_000004_add_channel_reference_to_support_tickets_table',1),(227,'2026_02_11_100000_add_keywords_and_name_to_auto_responses',1),(228,'2026_02_11_110000_add_is_public_to_auto_responses',1),(229,'2026_02_11_120000_create_admin_reports_table',1),(230,'2021_01_01_000000_create_base_tables',16),(231,'2026_04_11_120000_add_split_banner_json_to_homepage_custom_product_rows',16),(232,'2026_04_14_120000_add_header_notice_text_to_shipping_rules_table',16),(233,'2026_04_14_130000_add_header_top_bg_to_ui_settings_table',16),(234,'2026_04_29_164951_create_sellers_table',16),(235,'2026_04_29_165348_add_seller_id_to_products_table',16),(236,'2026_05_02_181459_add_missing_columns_to_reviews_table',16),(237,'2026_05_02_190532_add_today_deals_to_products_table',16),(238,'2026_05_02_190601_add_product_id_to_reviews_table',16),(239,'2026_05_02_195212_add_system_info_to_general_settings_table',16);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notification_logs`
--

DROP TABLE IF EXISTS `notification_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notification_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `sender` varchar(40) DEFAULT NULL,
  `sent_from` varchar(40) DEFAULT NULL,
  `sent_to` varchar(40) DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `click_url` varchar(500) DEFAULT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `notification_type` varchar(40) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notification_logs`
--

LOCK TABLES `notification_logs` WRITE;
/*!40000 ALTER TABLE `notification_logs` DISABLE KEYS */;
INSERT INTO `notification_logs` VALUES (1,2,'php','info@viserlab.com','vfvfbf@gmail.com','Order successfully completed','<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\">\r\n  <!--[if !mso]><!-->\r\n  <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\">\r\n  <!--<![endif]-->\r\n  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\r\n  <title></title>\r\n  <style type=\"text/css\">\r\n.ReadMsgBody { width: 100%; background-color: #ffffff; }\r\n.ExternalClass { width: 100%; background-color: #ffffff; }\r\n.ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td, .ExternalClass div { line-height: 100%; }\r\nhtml { width: 100%; }\r\nbody { -webkit-text-size-adjust: none; -ms-text-size-adjust: none; margin: 0; padding: 0; }\r\ntable { border-spacing: 0; table-layout: fixed; margin: 0 auto;border-collapse: collapse; }\r\ntable table table { table-layout: auto; }\r\n.yshortcuts a { border-bottom: none !important; }\r\nimg:hover { opacity: 0.9 !important; }\r\na { color: #0087ff; text-decoration: none; }\r\n.textbutton a { font-family: \'open sans\', arial, sans-serif !important;}\r\n.btn-link a { color:#FFFFFF !important;}\r\n\r\n@media only screen and (max-width: 480px) {\r\nbody { width: auto !important; }\r\n*[class=\"table-inner\"] { width: 90% !important; text-align: center !important; }\r\n*[class=\"table-full\"] { width: 100% !important; text-align: center !important; }\r\n/* image */\r\nimg[class=\"img1\"] { width: 100% !important; height: auto !important; }\r\n}\r\n</style>\r\n\r\n\r\n\r\n  <table bgcolor=\"#414a51\" width=\"100%\" border=\"0\" align=\"center\" cellpadding=\"0\" cellspacing=\"0\">\r\n    <tbody><tr>\r\n      <td height=\"50\"></td>\r\n    </tr>\r\n    <tr>\r\n      <td align=\"center\" style=\"text-align:center;vertical-align:top;font-size:0;\">\r\n        <table align=\"center\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">\r\n          <tbody><tr>\r\n            <td align=\"center\" width=\"600\">\r\n              <!--header-->\r\n              <table class=\"table-inner\" width=\"95%\" border=\"0\" align=\"center\" cellpadding=\"0\" cellspacing=\"0\">\r\n                <tbody><tr>\r\n                  <td bgcolor=\"#0087ff\" style=\"border-top-left-radius:6px; border-top-right-radius:6px;text-align:center;vertical-align:top;font-size:0;\" align=\"center\">\r\n                    <table width=\"90%\" border=\"0\" align=\"center\" cellpadding=\"0\" cellspacing=\"0\">\r\n                      <tbody><tr>\r\n                        <td height=\"20\"></td>\r\n                      </tr>\r\n                      <tr>\r\n                        <td align=\"center\" style=\"font-family: \'Open sans\', Arial, sans-serif; color:#FFFFFF; font-size:16px; font-weight: bold;\">This is a System Generated Email</td>\r\n                      </tr>\r\n                      <tr>\r\n                        <td height=\"20\"></td>\r\n                      </tr>\r\n                    </tbody></table>\r\n                  </td>\r\n                </tr>\r\n              </tbody></table>\r\n              <!--end header-->\r\n              <table class=\"table-inner\" width=\"95%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\r\n                <tbody><tr>\r\n                  <td bgcolor=\"#FFFFFF\" align=\"center\" style=\"text-align:center;vertical-align:top;font-size:0;\">\r\n                    <table align=\"center\" width=\"90%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\r\n                      <tbody><tr>\r\n                        <td height=\"35\"></td>\r\n                      </tr>\r\n                      <!--logo-->\r\n                      <tr>\r\n                        <td align=\"center\" style=\"vertical-align:top;font-size:0;\">\r\n                          <a href=\"#\">\r\n                            <img style=\"display:block; line-height:0px; font-size:0px; border:0px;\" src=\"https://i.imgur.com/Z1qtvtV.png\" alt=\"img\">\r\n                          </a>\r\n                        </td>\r\n                      </tr>\r\n                      <!--end logo-->\r\n                      <tr>\r\n                        <td height=\"40\"></td>\r\n                      </tr>\r\n                      <!--headline-->\r\n                      <tr>\r\n                        <td align=\"center\" style=\"font-family: \'Open Sans\', Arial, sans-serif; font-size: 22px;color:#414a51;font-weight: bold;\">Hello RIAZUL ISLAM SHOJOL (hhuhuhu)</td>\r\n                      </tr>\r\n                      <!--end headline-->\r\n                      <tr>\r\n                        <td align=\"center\" style=\"text-align:center;vertical-align:top;font-size:0;\">\r\n                          <table width=\"40\" border=\"0\" align=\"center\" cellpadding=\"0\" cellspacing=\"0\">\r\n                            <tbody><tr>\r\n                              <td height=\"20\" style=\" border-bottom:3px solid #0087ff;\"></td>\r\n                            </tr>\r\n                          </tbody></table>\r\n                        </td>\r\n                      </tr>\r\n                      <tr>\r\n                        <td height=\"20\"></td>\r\n                      </tr>\r\n                      <!--content-->\r\n                      <tr>\r\n                        <td align=\"left\" style=\"font-family: \'Open sans\', Arial, sans-serif; color:#7f8c8d; font-size:16px; line-height: 28px;\"><div>Order successfully placed.</div><div>User Name : hhuhuhu</div><div>Order No:<b> OQXN824REWTF</b></div><div>Sub Total : <b>90.00&nbsp;</b><span style=\"text-align: var(--bs-body-text-align);\"><font color=\"#212529\"><b>USD</b></font></span></div><div>Shipping Charge : <b>1.00</b><span style=\"text-align: var(--bs-body-text-align);\"><font color=\"#212529\"><b>USD</b></font></span></div><div>Total:<b> 91.00&nbsp;</b><span style=\"text-align: var(--bs-body-text-align);\"><font color=\"#212529\"><b>USD</b></font></span></div></td>\r\n                      </tr>\r\n                      <!--end content-->\r\n                      <tr>\r\n                        <td height=\"40\"></td>\r\n                      </tr>\r\n              \r\n                    </tbody></table>\r\n                  </td>\r\n                </tr>\r\n                <tr>\r\n                  <td height=\"45\" align=\"center\" bgcolor=\"#f4f4f4\" style=\"border-bottom-left-radius:6px;border-bottom-right-radius:6px;\">\r\n                    <table align=\"center\" width=\"90%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\r\n                      <tbody><tr>\r\n                        <td height=\"10\"></td>\r\n                      </tr>\r\n                      <!--preference-->\r\n                      <tr>\r\n                        <td class=\"preference-link\" align=\"center\" style=\"font-family: \'Open sans\', Arial, sans-serif; color:#95a5a6; font-size:14px;\">\r\n                          Â© 2021 <a href=\"#\">Dealshop</a>&nbsp;. All Rights Reserved. \r\n                        </td>\r\n                      </tr>\r\n                      <!--end preference-->\r\n                      <tr>\r\n                        <td height=\"10\"></td>\r\n                      </tr>\r\n                    </tbody></table>\r\n                  </td>\r\n                </tr>\r\n              </tbody></table>\r\n            </td>\r\n          </tr>\r\n        </tbody></table>\r\n      </td>\r\n    </tr>\r\n    <tr>\r\n      <td height=\"60\"></td>\r\n    </tr>\r\n  </tbody></table>',NULL,NULL,'email','2025-10-10 12:27:55','2025-10-10 12:27:55'),(2,2,'php','info@viserlab.com','vfvfbf@gmail.com','Payment Request Submitted Successfully','<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\">\r\n  <!--[if !mso]><!-->\r\n  <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\">\r\n  <!--<![endif]-->\r\n  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\r\n  <title></title>\r\n  <style type=\"text/css\">\r\n.ReadMsgBody { width: 100%; background-color: #ffffff; }\r\n.ExternalClass { width: 100%; background-color: #ffffff; }\r\n.ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td, .ExternalClass div { line-height: 100%; }\r\nhtml { width: 100%; }\r\nbody { -webkit-text-size-adjust: none; -ms-text-size-adjust: none; margin: 0; padding: 0; }\r\ntable { border-spacing: 0; table-layout: fixed; margin: 0 auto;border-collapse: collapse; }\r\ntable table table { table-layout: auto; }\r\n.yshortcuts a { border-bottom: none !important; }\r\nimg:hover { opacity: 0.9 !important; }\r\na { color: #0087ff; text-decoration: none; }\r\n.textbutton a { font-family: \'open sans\', arial, sans-serif !important;}\r\n.btn-link a { color:#FFFFFF !important;}\r\n\r\n@media only screen and (max-width: 480px) {\r\nbody { width: auto !important; }\r\n*[class=\"table-inner\"] { width: 90% !important; text-align: center !important; }\r\n*[class=\"table-full\"] { width: 100% !important; text-align: center !important; }\r\n/* image */\r\nimg[class=\"img1\"] { width: 100% !important; height: auto !important; }\r\n}\r\n</style>\r\n\r\n\r\n\r\n  <table bgcolor=\"#414a51\" width=\"100%\" border=\"0\" align=\"center\" cellpadding=\"0\" cellspacing=\"0\">\r\n    <tbody><tr>\r\n      <td height=\"50\"></td>\r\n    </tr>\r\n    <tr>\r\n      <td align=\"center\" style=\"text-align:center;vertical-align:top;font-size:0;\">\r\n        <table align=\"center\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">\r\n          <tbody><tr>\r\n            <td align=\"center\" width=\"600\">\r\n              <!--header-->\r\n              <table class=\"table-inner\" width=\"95%\" border=\"0\" align=\"center\" cellpadding=\"0\" cellspacing=\"0\">\r\n                <tbody><tr>\r\n                  <td bgcolor=\"#0087ff\" style=\"border-top-left-radius:6px; border-top-right-radius:6px;text-align:center;vertical-align:top;font-size:0;\" align=\"center\">\r\n                    <table width=\"90%\" border=\"0\" align=\"center\" cellpadding=\"0\" cellspacing=\"0\">\r\n                      <tbody><tr>\r\n                        <td height=\"20\"></td>\r\n                      </tr>\r\n                      <tr>\r\n                        <td align=\"center\" style=\"font-family: \'Open sans\', Arial, sans-serif; color:#FFFFFF; font-size:16px; font-weight: bold;\">This is a System Generated Email</td>\r\n                      </tr>\r\n                      <tr>\r\n                        <td height=\"20\"></td>\r\n                      </tr>\r\n                    </tbody></table>\r\n                  </td>\r\n                </tr>\r\n              </tbody></table>\r\n              <!--end header-->\r\n              <table class=\"table-inner\" width=\"95%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\r\n                <tbody><tr>\r\n                  <td bgcolor=\"#FFFFFF\" align=\"center\" style=\"text-align:center;vertical-align:top;font-size:0;\">\r\n                    <table align=\"center\" width=\"90%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\r\n                      <tbody><tr>\r\n                        <td height=\"35\"></td>\r\n                      </tr>\r\n                      <!--logo-->\r\n                      <tr>\r\n                        <td align=\"center\" style=\"vertical-align:top;font-size:0;\">\r\n                          <a href=\"#\">\r\n                            <img style=\"display:block; line-height:0px; font-size:0px; border:0px;\" src=\"https://i.imgur.com/Z1qtvtV.png\" alt=\"img\">\r\n                          </a>\r\n                        </td>\r\n                      </tr>\r\n                      <!--end logo-->\r\n                      <tr>\r\n                        <td height=\"40\"></td>\r\n                      </tr>\r\n                      <!--headline-->\r\n                      <tr>\r\n                        <td align=\"center\" style=\"font-family: \'Open Sans\', Arial, sans-serif; font-size: 22px;color:#414a51;font-weight: bold;\">Hello RIAZUL ISLAM SHOJOL (hhuhuhu)</td>\r\n                      </tr>\r\n                      <!--end headline-->\r\n                      <tr>\r\n                        <td align=\"center\" style=\"text-align:center;vertical-align:top;font-size:0;\">\r\n                          <table width=\"40\" border=\"0\" align=\"center\" cellpadding=\"0\" cellspacing=\"0\">\r\n                            <tbody><tr>\r\n                              <td height=\"20\" style=\" border-bottom:3px solid #0087ff;\"></td>\r\n                            </tr>\r\n                          </tbody></table>\r\n                        </td>\r\n                      </tr>\r\n                      <tr>\r\n                        <td height=\"20\"></td>\r\n                      </tr>\r\n                      <!--content-->\r\n                      <tr>\r\n                        <td align=\"left\" style=\"font-family: \'Open sans\', Arial, sans-serif; color:#7f8c8d; font-size:16px; line-height: 28px;\"><div>Your payment request of&nbsp;<span style=\"font-weight: bolder;\">91.00 USD</span>&nbsp;is via&nbsp;&nbsp;<span style=\"font-weight: bolder;\">WinTerSMM&nbsp;</span>submitted successfully<span style=\"font-weight: bolder;\">&nbsp;.<br></span></div><div><span style=\"font-weight: bolder;\"><br></span></div><div><span style=\"font-weight: bolder;\">Details of your Payment :<br></span></div><div><br></div><div>Amount : 91.00 USD</div><div>Charge:&nbsp;<font color=\"#FF0000\">2.82 USD</font></div><div><br></div><div>Conversion Rate : 1 USD = 120.00 USDT</div><div>Payable : 11,258.40 USDT<br></div><div>Pay via :&nbsp; WinTerSMM</div><div><br></div><div><span style=\"color: rgb(33, 37, 41);\">Transaction Number : OQXN824REWTF</span><br></div><div>Order No : OQXN824REWTF</div><div><br></div><div><br style=\"font-family: Montserrat, sans-serif;\"></div></td>\r\n                      </tr>\r\n                      <!--end content-->\r\n                      <tr>\r\n                        <td height=\"40\"></td>\r\n                      </tr>\r\n              \r\n                    </tbody></table>\r\n                  </td>\r\n                </tr>\r\n                <tr>\r\n                  <td height=\"45\" align=\"center\" bgcolor=\"#f4f4f4\" style=\"border-bottom-left-radius:6px;border-bottom-right-radius:6px;\">\r\n                    <table align=\"center\" width=\"90%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\r\n                      <tbody><tr>\r\n                        <td height=\"10\"></td>\r\n                      </tr>\r\n                      <!--preference-->\r\n                      <tr>\r\n                        <td class=\"preference-link\" align=\"center\" style=\"font-family: \'Open sans\', Arial, sans-serif; color:#95a5a6; font-size:14px;\">\r\n                          Â© 2021 <a href=\"#\">Dealshop</a>&nbsp;. All Rights Reserved. \r\n                        </td>\r\n                      </tr>\r\n                      <!--end preference-->\r\n                      <tr>\r\n                        <td height=\"10\"></td>\r\n                      </tr>\r\n                    </tbody></table>\r\n                  </td>\r\n                </tr>\r\n              </tbody></table>\r\n            </td>\r\n          </tr>\r\n        </tbody></table>\r\n      </td>\r\n    </tr>\r\n    <tr>\r\n      <td height=\"60\"></td>\r\n    </tr>\r\n  </tbody></table>',NULL,NULL,'email','2025-10-10 12:27:56','2025-10-10 12:27:56'),(3,5,'php','info@viserlab.com','jnjguurghh@gmail.com','demo','<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\">\r\n  <!--[if !mso]><!-->\r\n  <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\">\r\n  <!--<![endif]-->\r\n  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\r\n  <title></title>\r\n  <style type=\"text/css\">\r\n.ReadMsgBody { width: 100%; background-color: #ffffff; }\r\n.ExternalClass { width: 100%; background-color: #ffffff; }\r\n.ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td, .ExternalClass div { line-height: 100%; }\r\nhtml { width: 100%; }\r\nbody { -webkit-text-size-adjust: none; -ms-text-size-adjust: none; margin: 0; padding: 0; }\r\ntable { border-spacing: 0; table-layout: fixed; margin: 0 auto;border-collapse: collapse; }\r\ntable table table { table-layout: auto; }\r\n.yshortcuts a { border-bottom: none !important; }\r\nimg:hover { opacity: 0.9 !important; }\r\na { color: #0087ff; text-decoration: none; }\r\n.textbutton a { font-family: \'open sans\', arial, sans-serif !important;}\r\n.btn-link a { color:#FFFFFF !important;}\r\n\r\n@media only screen and (max-width: 480px) {\r\nbody { width: auto !important; }\r\n*[class=\"table-inner\"] { width: 90% !important; text-align: center !important; }\r\n*[class=\"table-full\"] { width: 100% !important; text-align: center !important; }\r\n/* image */\r\nimg[class=\"img1\"] { width: 100% !important; height: auto !important; }\r\n}\r\n</style>\r\n\r\n\r\n\r\n  <table bgcolor=\"#414a51\" width=\"100%\" border=\"0\" align=\"center\" cellpadding=\"0\" cellspacing=\"0\">\r\n    <tbody><tr>\r\n      <td height=\"50\"></td>\r\n    </tr>\r\n    <tr>\r\n      <td align=\"center\" style=\"text-align:center;vertical-align:top;font-size:0;\">\r\n        <table align=\"center\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">\r\n          <tbody><tr>\r\n            <td align=\"center\" width=\"600\">\r\n              <!--header-->\r\n              <table class=\"table-inner\" width=\"95%\" border=\"0\" align=\"center\" cellpadding=\"0\" cellspacing=\"0\">\r\n                <tbody><tr>\r\n                  <td bgcolor=\"#0087ff\" style=\"border-top-left-radius:6px; border-top-right-radius:6px;text-align:center;vertical-align:top;font-size:0;\" align=\"center\">\r\n                    <table width=\"90%\" border=\"0\" align=\"center\" cellpadding=\"0\" cellspacing=\"0\">\r\n                      <tbody><tr>\r\n                        <td height=\"20\"></td>\r\n                      </tr>\r\n                      <tr>\r\n                        <td align=\"center\" style=\"font-family: \'Open sans\', Arial, sans-serif; color:#FFFFFF; font-size:16px; font-weight: bold;\">This is a System Generated Email</td>\r\n                      </tr>\r\n                      <tr>\r\n                        <td height=\"20\"></td>\r\n                      </tr>\r\n                    </tbody></table>\r\n                  </td>\r\n                </tr>\r\n              </tbody></table>\r\n              <!--end header-->\r\n              <table class=\"table-inner\" width=\"95%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\r\n                <tbody><tr>\r\n                  <td bgcolor=\"#FFFFFF\" align=\"center\" style=\"text-align:center;vertical-align:top;font-size:0;\">\r\n                    <table align=\"center\" width=\"90%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\r\n                      <tbody><tr>\r\n                        <td height=\"35\"></td>\r\n                      </tr>\r\n                      <!--logo-->\r\n                      <tr>\r\n                        <td align=\"center\" style=\"vertical-align:top;font-size:0;\">\r\n                          <a href=\"#\">\r\n                            <img style=\"display:block; line-height:0px; font-size:0px; border:0px;\" src=\"https://i.imgur.com/Z1qtvtV.png\" alt=\"img\">\r\n                          </a>\r\n                        </td>\r\n                      </tr>\r\n                      <!--end logo-->\r\n                      <tr>\r\n                        <td height=\"40\"></td>\r\n                      </tr>\r\n                      <!--headline-->\r\n                      <tr>\r\n                        <td align=\"center\" style=\"font-family: \'Open Sans\', Arial, sans-serif; font-size: 22px;color:#414a51;font-weight: bold;\">Hello opu mia (opumia)</td>\r\n                      </tr>\r\n                      <!--end headline-->\r\n                      <tr>\r\n                        <td align=\"center\" style=\"text-align:center;vertical-align:top;font-size:0;\">\r\n                          <table width=\"40\" border=\"0\" align=\"center\" cellpadding=\"0\" cellspacing=\"0\">\r\n                            <tbody><tr>\r\n                              <td height=\"20\" style=\" border-bottom:3px solid #0087ff;\"></td>\r\n                            </tr>\r\n                          </tbody></table>\r\n                        </td>\r\n                      </tr>\r\n                      <tr>\r\n                        <td height=\"20\"></td>\r\n                      </tr>\r\n                      <!--content-->\r\n                      <tr>\r\n                        <td align=\"left\" style=\"font-family: \'Open sans\', Arial, sans-serif; color:#7f8c8d; font-size:16px; line-height: 28px;\">tesr</td>\r\n                      </tr>\r\n                      <!--end content-->\r\n                      <tr>\r\n                        <td height=\"40\"></td>\r\n                      </tr>\r\n              \r\n                    </tbody></table>\r\n                  </td>\r\n                </tr>\r\n                <tr>\r\n                  <td height=\"45\" align=\"center\" bgcolor=\"#f4f4f4\" style=\"border-bottom-left-radius:6px;border-bottom-right-radius:6px;\">\r\n                    <table align=\"center\" width=\"90%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\r\n                      <tbody><tr>\r\n                        <td height=\"10\"></td>\r\n                      </tr>\r\n                      <!--preference-->\r\n                      <tr>\r\n                        <td class=\"preference-link\" align=\"center\" style=\"font-family: \'Open sans\', Arial, sans-serif; color:#95a5a6; font-size:14px;\">\r\n                          Â© 2021 <a href=\"#\">demo</a>&nbsp;. All Rights Reserved. \r\n                        </td>\r\n                      </tr>\r\n                      <!--end preference-->\r\n                      <tr>\r\n                        <td height=\"10\"></td>\r\n                      </tr>\r\n                    </tbody></table>\r\n                  </td>\r\n                </tr>\r\n              </tbody></table>\r\n            </td>\r\n          </tr>\r\n        </tbody></table>\r\n      </td>\r\n    </tr>\r\n    <tr>\r\n      <td height=\"60\"></td>\r\n    </tr>\r\n  </tbody></table>',NULL,NULL,'email','2025-10-16 02:10:36','2025-10-16 02:10:36'),(4,6,'php','info@viserlab.com','limonq56@gmail.com','demo','<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\">\r\n  <!--[if !mso]><!-->\r\n  <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\">\r\n  <!--<![endif]-->\r\n  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\r\n  <title></title>\r\n  <style type=\"text/css\">\r\n.ReadMsgBody { width: 100%; background-color: #ffffff; }\r\n.ExternalClass { width: 100%; background-color: #ffffff; }\r\n.ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td, .ExternalClass div { line-height: 100%; }\r\nhtml { width: 100%; }\r\nbody { -webkit-text-size-adjust: none; -ms-text-size-adjust: none; margin: 0; padding: 0; }\r\ntable { border-spacing: 0; table-layout: fixed; margin: 0 auto;border-collapse: collapse; }\r\ntable table table { table-layout: auto; }\r\n.yshortcuts a { border-bottom: none !important; }\r\nimg:hover { opacity: 0.9 !important; }\r\na { color: #0087ff; text-decoration: none; }\r\n.textbutton a { font-family: \'open sans\', arial, sans-serif !important;}\r\n.btn-link a { color:#FFFFFF !important;}\r\n\r\n@media only screen and (max-width: 480px) {\r\nbody { width: auto !important; }\r\n*[class=\"table-inner\"] { width: 90% !important; text-align: center !important; }\r\n*[class=\"table-full\"] { width: 100% !important; text-align: center !important; }\r\n/* image */\r\nimg[class=\"img1\"] { width: 100% !important; height: auto !important; }\r\n}\r\n</style>\r\n\r\n\r\n\r\n  <table bgcolor=\"#414a51\" width=\"100%\" border=\"0\" align=\"center\" cellpadding=\"0\" cellspacing=\"0\">\r\n    <tbody><tr>\r\n      <td height=\"50\"></td>\r\n    </tr>\r\n    <tr>\r\n      <td align=\"center\" style=\"text-align:center;vertical-align:top;font-size:0;\">\r\n        <table align=\"center\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">\r\n          <tbody><tr>\r\n            <td align=\"center\" width=\"600\">\r\n              <!--header-->\r\n              <table class=\"table-inner\" width=\"95%\" border=\"0\" align=\"center\" cellpadding=\"0\" cellspacing=\"0\">\r\n                <tbody><tr>\r\n                  <td bgcolor=\"#0087ff\" style=\"border-top-left-radius:6px; border-top-right-radius:6px;text-align:center;vertical-align:top;font-size:0;\" align=\"center\">\r\n                    <table width=\"90%\" border=\"0\" align=\"center\" cellpadding=\"0\" cellspacing=\"0\">\r\n                      <tbody><tr>\r\n                        <td height=\"20\"></td>\r\n                      </tr>\r\n                      <tr>\r\n                        <td align=\"center\" style=\"font-family: \'Open sans\', Arial, sans-serif; color:#FFFFFF; font-size:16px; font-weight: bold;\">This is a System Generated Email</td>\r\n                      </tr>\r\n                      <tr>\r\n                        <td height=\"20\"></td>\r\n                      </tr>\r\n                    </tbody></table>\r\n                  </td>\r\n                </tr>\r\n              </tbody></table>\r\n              <!--end header-->\r\n              <table class=\"table-inner\" width=\"95%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\r\n                <tbody><tr>\r\n                  <td bgcolor=\"#FFFFFF\" align=\"center\" style=\"text-align:center;vertical-align:top;font-size:0;\">\r\n                    <table align=\"center\" width=\"90%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\r\n                      <tbody><tr>\r\n                        <td height=\"35\"></td>\r\n                      </tr>\r\n                      <!--logo-->\r\n                      <tr>\r\n                        <td align=\"center\" style=\"vertical-align:top;font-size:0;\">\r\n                          <a href=\"#\">\r\n                            <img style=\"display:block; line-height:0px; font-size:0px; border:0px;\" src=\"https://i.imgur.com/Z1qtvtV.png\" alt=\"img\">\r\n                          </a>\r\n                        </td>\r\n                      </tr>\r\n                      <!--end logo-->\r\n                      <tr>\r\n                        <td height=\"40\"></td>\r\n                      </tr>\r\n                      <!--headline-->\r\n                      <tr>\r\n                        <td align=\"center\" style=\"font-family: \'Open Sans\', Arial, sans-serif; font-size: 22px;color:#414a51;font-weight: bold;\">Hello Md rifat Mia (test1333)</td>\r\n                      </tr>\r\n                      <!--end headline-->\r\n                      <tr>\r\n                        <td align=\"center\" style=\"text-align:center;vertical-align:top;font-size:0;\">\r\n                          <table width=\"40\" border=\"0\" align=\"center\" cellpadding=\"0\" cellspacing=\"0\">\r\n                            <tbody><tr>\r\n                              <td height=\"20\" style=\" border-bottom:3px solid #0087ff;\"></td>\r\n                            </tr>\r\n                          </tbody></table>\r\n                        </td>\r\n                      </tr>\r\n                      <tr>\r\n                        <td height=\"20\"></td>\r\n                      </tr>\r\n                      <!--content-->\r\n                      <tr>\r\n                        <td align=\"left\" style=\"font-family: \'Open sans\', Arial, sans-serif; color:#7f8c8d; font-size:16px; line-height: 28px;\">tesr</td>\r\n                      </tr>\r\n                      <!--end content-->\r\n                      <tr>\r\n                        <td height=\"40\"></td>\r\n                      </tr>\r\n              \r\n                    </tbody></table>\r\n                  </td>\r\n                </tr>\r\n                <tr>\r\n                  <td height=\"45\" align=\"center\" bgcolor=\"#f4f4f4\" style=\"border-bottom-left-radius:6px;border-bottom-right-radius:6px;\">\r\n                    <table align=\"center\" width=\"90%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\r\n                      <tbody><tr>\r\n                        <td height=\"10\"></td>\r\n                      </tr>\r\n                      <!--preference-->\r\n                      <tr>\r\n                        <td class=\"preference-link\" align=\"center\" style=\"font-family: \'Open sans\', Arial, sans-serif; color:#95a5a6; font-size:14px;\">\r\n                          Â© 2021 <a href=\"#\">demo</a>&nbsp;. All Rights Reserved. \r\n                        </td>\r\n                      </tr>\r\n                      <!--end preference-->\r\n                      <tr>\r\n                        <td height=\"10\"></td>\r\n                      </tr>\r\n                    </tbody></table>\r\n                  </td>\r\n                </tr>\r\n              </tbody></table>\r\n            </td>\r\n          </tr>\r\n        </tbody></table>\r\n      </td>\r\n    </tr>\r\n    <tr>\r\n      <td height=\"60\"></td>\r\n    </tr>\r\n  </tbody></table>',NULL,NULL,'email','2025-10-16 02:10:36','2025-10-16 02:10:36'),(10,9,'php','info@viserlab.com','opumia@gmail.com','opu','h',NULL,'2026-03-02 09:49:52','email','2026-03-02 09:49:49','2026-03-02 09:49:52'),(11,9,'php','info@viserlab.com','opumia@gmail.com','opu','h','http://localhost/staylbd/message/view/740630','2026-03-02 10:07:06','email','2026-03-02 10:06:57','2026-03-02 10:07:06'),(12,9,'php','info@viserlab.com','opumia@gmail.com','opu','gp','http://localhost/staylbd/message/view/224590','2026-03-02 10:24:01','email','2026-03-02 10:23:43','2026-03-02 10:24:01');
/*!40000 ALTER TABLE `notification_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notification_templates`
--

DROP TABLE IF EXISTS `notification_templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notification_templates` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `act` varchar(40) DEFAULT NULL,
  `name` varchar(40) DEFAULT NULL,
  `subj` varchar(255) DEFAULT NULL,
  `email_body` text DEFAULT NULL,
  `sms_body` text DEFAULT NULL,
  `shortcodes` text DEFAULT NULL,
  `email_status` tinyint(1) NOT NULL DEFAULT 1,
  `sms_status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=222 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notification_templates`
--

LOCK TABLES `notification_templates` WRITE;
/*!40000 ALTER TABLE `notification_templates` DISABLE KEYS */;
INSERT INTO `notification_templates` VALUES (3,'DEPOSIT_COMPLETE','Deposit - Automated - Successful','Deposit Completed Successfully','<div>Your deposit of&nbsp;<span style=\"font-weight: bolder;\">{{amount}} {{site_currency}}</span>&nbsp;is via&nbsp;&nbsp;<span style=\"font-weight: bolder;\">{{method_name}}&nbsp;</span>has been completed Successfully.<span style=\"font-weight: bolder;\"><br></span></div><div><span style=\"font-weight: bolder;\"><br></span></div><div><span style=\"font-weight: bolder;\">Details of your Deposit :<br></span></div><div><br></div><div>Amount : {{amount}} {{site_currency}}</div><div>Charge:&nbsp;<font color=\"#000000\">{{charge}} {{site_currency}}</font></div><div><br></div><div>Conversion Rate : 1 {{site_currency}} = {{rate}} {{method_currency}}</div><div>Received : {{method_amount}} {{method_currency}}<br></div><div>Paid via :&nbsp; {{method_name}}</div><div><br></div><div>Transaction Number : {{trx}}</div><div><br style=\"font-family: Montserrat, sans-serif;\"></div>','{{amount}} {{site_currency}} Deposit successfully by {{method_name}}','{\"trx\":\"Transaction number for the deposit\",\"amount\":\"Amount inserted by the user\",\"charge\":\"Gateway charge set by the admin\",\"rate\":\"Conversion rate between base currency and method currency\",\"method_name\":\"Name of the deposit method\",\"method_currency\":\"Currency of the deposit method\",\"method_amount\":\"Amount after conversion between base currency and method currency\"}',1,1,'2021-11-03 12:00:00','2023-05-23 04:27:02'),(4,'DEPOSIT_APPROVE','Deposit - Manual - Approved','Your Deposit is Approved','<div style=\"font-family: Montserrat, sans-serif;\">Your deposit request of&nbsp;<span style=\"font-weight: bolder;\">{{amount}} {{site_currency}}</span>&nbsp;is via&nbsp;&nbsp;<span style=\"font-weight: bolder;\">{{method_name}}&nbsp;</span>is Approved .<span style=\"font-weight: bolder;\"><br></span></div><div style=\"font-family: Montserrat, sans-serif;\"><span style=\"font-weight: bolder;\"><br></span></div><div style=\"font-family: Montserrat, sans-serif;\"><span style=\"font-weight: bolder;\">Details of your Deposit :<br></span></div><div style=\"font-family: Montserrat, sans-serif;\"><br></div><div style=\"font-family: Montserrat, sans-serif;\">Amount : {{amount}} {{site_currency}}</div><div style=\"font-family: Montserrat, sans-serif;\">Charge:&nbsp;<font color=\"#FF0000\">{{charge}} {{site_currency}}</font></div><div style=\"font-family: Montserrat, sans-serif;\"><br></div><div style=\"font-family: Montserrat, sans-serif;\">Conversion Rate : 1 {{site_currency}} = {{rate}} {{method_currency}}</div><div style=\"font-family: Montserrat, sans-serif;\">Received : {{method_amount}} {{method_currency}}<br></div><div style=\"font-family: Montserrat, sans-serif;\">Paid via :&nbsp; {{method_name}}</div><div style=\"font-family: Montserrat, sans-serif;\"><br></div><div style=\"font-family: Montserrat, sans-serif;\">Transaction Number : {{trx}}</div><div style=\"font-family: Montserrat, sans-serif;\"><br></div><div style=\"font-family: Montserrat, sans-serif;\"><br></div>','Admin Approve Your {{amount}} {{site_currency}} payment request by {{method_name}} transaction : {{trx}}','{\"trx\":\"Transaction number for the deposit\",\"amount\":\"Amount inserted by the user\",\"charge\":\"Gateway charge set by the admin\",\"rate\":\"Conversion rate between base currency and method currency\",\"method_name\":\"Name of the deposit method\",\"method_currency\":\"Currency of the deposit method\",\"method_amount\":\"Amount after conversion between base currency and method currency\"}',1,1,'2021-11-03 12:00:00','2023-05-23 04:28:04'),(5,'DEPOSIT_REJECT','Deposit - Manual - Rejected','Your Deposit Request is Rejected','<div style=\"font-family: Montserrat, sans-serif;\">Your deposit request of&nbsp;<span style=\"font-weight: bolder;\">{{amount}} {{site_currency}}</span>&nbsp;is via&nbsp;&nbsp;<span style=\"font-weight: bolder;\">{{method_name}} has been rejected</span>.<span style=\"font-weight: bolder;\"><br></span></div><div><br></div><div><br></div><div style=\"font-family: Montserrat, sans-serif;\">Conversion Rate : 1 {{site_currency}} = {{rate}} {{method_currency}}</div><div style=\"font-family: Montserrat, sans-serif;\">Received : {{method_amount}} {{method_currency}}<br></div><div style=\"font-family: Montserrat, sans-serif;\">Paid via :&nbsp; {{method_name}}</div><div style=\"font-family: Montserrat, sans-serif;\">Charge: {{charge}}</div><div style=\"font-family: Montserrat, sans-serif;\"><br></div><div style=\"font-family: Montserrat, sans-serif;\"><br></div><div style=\"font-family: Montserrat, sans-serif;\">Transaction Number was : {{trx}}</div><div style=\"font-family: Montserrat, sans-serif;\"><br></div><div style=\"font-family: Montserrat, sans-serif;\">if you have any queries, feel free to contact us.<br></div><br style=\"font-family: Montserrat, sans-serif;\"><div style=\"font-family: Montserrat, sans-serif;\"><br><br></div><span style=\"color: rgb(33, 37, 41); font-family: Montserrat, sans-serif;\">{{rejection_message}}</span><br>','Admin Rejected Your {{amount}} {{site_currency}} payment request by {{method_name}}\r\n\r\n{{rejection_message}}','{\"trx\":\"Transaction number for the deposit\",\"amount\":\"Amount inserted by the user\",\"charge\":\"Gateway charge set by the admin\",\"rate\":\"Conversion rate between base currency and method currency\",\"method_name\":\"Name of the deposit method\",\"method_currency\":\"Currency of the deposit method\",\"method_amount\":\"Amount after conversion between base currency and method currency\",\"rejection_message\":\"Rejection message by the admin\"}',1,1,'2021-11-03 12:00:00','2022-04-05 03:45:27'),(7,'PASS_RESET_CODE','Password - Reset - Code','Password Reset','<div style=\"font-family: Montserrat, sans-serif;\">We have received a request to reset the password for your account on&nbsp;<span style=\"font-weight: bolder;\">{{time}} .<br></span></div><div style=\"font-family: Montserrat, sans-serif;\">Requested From IP:&nbsp;<span style=\"font-weight: bolder;\">{{ip}}</span>&nbsp;using&nbsp;<span style=\"font-weight: bolder;\">{{browser}}</span>&nbsp;on&nbsp;<span style=\"font-weight: bolder;\">{{operating_system}}&nbsp;</span>.</div><div style=\"font-family: Montserrat, sans-serif;\"><br></div><br style=\"font-family: Montserrat, sans-serif;\"><div style=\"font-family: Montserrat, sans-serif;\"><div>Your account recovery code is:&nbsp;&nbsp;&nbsp;<font size=\"6\"><span style=\"font-weight: bolder;\">{{code}}</span></font></div><div><br></div></div><div style=\"font-family: Montserrat, sans-serif;\"><br></div><div style=\"font-family: Montserrat, sans-serif;\"><font size=\"4\" color=\"#CC0000\">If you do not wish to reset your password, please disregard this message.&nbsp;</font><br></div><div><font size=\"4\" color=\"#CC0000\"><br></font></div>','Your account recovery code is: {{code}}','{\"code\":\"Verification code for password reset\",\"ip\":\"IP address of the user\",\"browser\":\"Browser of the user\",\"operating_system\":\"Operating system of the user\",\"time\":\"Time of the request\"}',1,0,'2021-11-03 12:00:00','2022-03-20 20:47:05'),(8,'PASS_RESET_DONE','Password - Reset - Confirmation','You have reset your password','<p style=\"font-family: Montserrat, sans-serif;\">You have successfully reset your password.</p><p style=\"font-family: Montserrat, sans-serif;\">You changed from&nbsp; IP:&nbsp;<span style=\"font-weight: bolder;\">{{ip}}</span>&nbsp;using&nbsp;<span style=\"font-weight: bolder;\">{{browser}}</span>&nbsp;on&nbsp;<span style=\"font-weight: bolder;\">{{operating_system}}&nbsp;</span>&nbsp;on&nbsp;<span style=\"font-weight: bolder;\">{{time}}</span></p><p style=\"font-family: Montserrat, sans-serif;\"><span style=\"font-weight: bolder;\"><br></span></p><p style=\"font-family: Montserrat, sans-serif;\"><span style=\"font-weight: bolder;\"><font color=\"#ff0000\">If you did not change that, please contact us as soon as possible.</font></span></p>','Your password has been changed successfully','{\"ip\":\"IP address of the user\",\"browser\":\"Browser of the user\",\"operating_system\":\"Operating system of the user\",\"time\":\"Time of the request\"}',1,1,'2021-11-03 12:00:00','2022-04-05 03:46:35'),(9,'ADMIN_SUPPORT_REPLY','Support - Reply','Reply Support Ticket','<div><p><span data-mce-style=\"font-size: 11pt;\" style=\"font-size: 11pt;\"><span style=\"font-weight: bolder;\">A member from our support team has replied to the following ticket:</span></span></p><p><span style=\"font-weight: bolder;\"><span data-mce-style=\"font-size: 11pt;\" style=\"font-size: 11pt;\"><span style=\"font-weight: bolder;\"><br></span></span></span></p><p><span style=\"font-weight: bolder;\">[Ticket#{{ticket_id}}] {{ticket_subject}}<br><br>Click here to reply:&nbsp; {{link}}</span></p><p>----------------------------------------------</p><p>Here is the reply :<br></p><p>{{reply}}<br></p></div><div><br style=\"font-family: Montserrat, sans-serif;\"></div>','Your Ticket#{{ticket_id}} :  {{ticket_subject}} has been replied.','{\"ticket_id\":\"ID of the support ticket\",\"ticket_subject\":\"Subject  of the support ticket\",\"reply\":\"Reply made by the admin\",\"link\":\"URL to view the support ticket\"}',1,1,'2021-11-03 12:00:00','2022-03-20 20:47:51'),(10,'EVER_CODE','Verification - Email','Please verify your email address','<br><div><div style=\"font-family: Montserrat, sans-serif;\">Thanks For joining us.<br></div><div style=\"font-family: Montserrat, sans-serif;\">Please use the below code to verify your email address.<br></div><div style=\"font-family: Montserrat, sans-serif;\"><br></div><div style=\"font-family: Montserrat, sans-serif;\">Your email verification code is:<font size=\"6\"><span style=\"font-weight: bolder;\">&nbsp;{{code}}</span></font></div></div>','---','{\"code\":\"Email verification code\"}',1,0,'2021-11-03 12:00:00','2022-04-03 02:32:07'),(11,'SVER_CODE','Verification - SMS','Verify Your Mobile Number','---','Your phone verification code is: {{code}}','{\"code\":\"SMS Verification Code\"}',0,1,'2021-11-03 12:00:00','2022-03-20 19:24:37'),(15,'DEFAULT','Default Template','{{subject}}','{{message}}','{{message}}','{\"subject\":\"Subject\",\"message\":\"Message\"}',1,1,'2019-09-14 13:14:22','2021-11-04 09:38:55'),(18,'ORDER_COMPLETE','Order Completed','Order successfully completed','<div>{{method_name}}</div><div>User Name : {{user_name}}</div><div>Order No:<b> {{order_no}}</b></div><div>Sub Total : <b>{{subtotal}}&nbsp;</b><span style=\"text-align: var(--bs-body-text-align);\"><font color=\"#212529\"><b>{{site_currency}}</b></font></span></div><div>Shipping Charge : <b>{{shipping_charge}}</b><span style=\"text-align: var(--bs-body-text-align);\"><font color=\"#212529\"><b>{{site_currency}}</b></font></span></div><div>Total:<b> {{total}}&nbsp;</b><span style=\"text-align: var(--bs-body-text-align);\"><font color=\"#212529\"><b>{{site_currency}}</b></font></span></div>','{{method_name}}\r\nUser Name : {{user_name}}\r\nOrder No: {{order_no}}\r\nSub Total : {{subtotal}} {{site_currency}}\r\nShipping Charge : {{shipping_charge}}{{site_currency}}\r\nTotal: {{total}} {{site_currency}}','{\"method_name\":\"Order successfully done via Wallet\",\"user_name\":\"Order By\",\"subtotal\":\"subtotal\",\"shipping_charge\":\"Shipping charge amount\",\"total\":\"Grand total amount\",\"order_no\":\"Order Number\"}',1,1,'2019-09-14 13:14:22','2023-03-06 06:04:41'),(20,'ORDER_STATUS','Order Status Change','Order status has changed successfully','<div>{{method_name}}</div><div>User Name: {{user_name}} </div><div>Order No:<b> {{order_no}}</b></div>\r\n<div>Total Price:<b> {{total}}&nbsp;</b><span style=\"text-align: var(--bs-body-text-align);\"><font color=\"#212529\"><b>{{site_currency}}</b></font></span></div>','{{method_name}}\r\nUser Name: {{user_name}}\r\nOrder No: {{order_no}}\r\nTotal Price: {{total}} {{site_currency}}','{\"method_name\":\"Order status name\",\"user_name\":\"Order Creator\",\"order_no\":\"Order Number\",\"total\":\"Total Order Price\"}',1,1,'2019-09-14 13:14:22','2023-03-06 06:22:07'),(219,'PAYMENT_REQUEST','Payment - Requested','Payment Request Submitted Successfully','<div>Your payment request of&nbsp;<span style=\"font-weight: bolder;\">{{amount}} {{site_currency}}</span>&nbsp;is via&nbsp;&nbsp;<span style=\"font-weight: bolder;\">{{method_name}}&nbsp;</span>submitted successfully<span style=\"font-weight: bolder;\">&nbsp;.<br></span></div><div><span style=\"font-weight: bolder;\"><br></span></div><div><span style=\"font-weight: bolder;\">Details of your Payment :<br></span></div><div><br></div><div>Amount : {{amount}} {{site_currency}}</div><div>Charge:&nbsp;<font color=\"#FF0000\">{{charge}} {{site_currency}}</font></div><div><br></div><div>Conversion Rate : 1 {{site_currency}} = {{rate}} {{method_currency}}</div><div>Payable : {{method_amount}} {{method_currency}}<br></div><div>Pay via :&nbsp; {{method_name}}</div><div><br></div><div><span style=\"color: rgb(33, 37, 41);\">Transaction Number : {{trx}}</span><br></div><div>Order No : {{order_no}}</div><div><br></div><div><br style=\"font-family: Montserrat, sans-serif;\"></div>','{{amount}} {{site_currency}} Payment requested by {{method_name}}. Charge: {{charge}} . Trx: {{trx}} Order No : {{order_no}}','{\"trx\":\"Transaction number for the deposit\",\"amount\":\"Amount inserted by the user\",\"charge\":\"Gateway charge set by the admin\",\"rate\":\"Conversion rate between base currency and method currency\",\"method_name\":\"Name of the deposit method\",\"method_currency\":\"Currency of the deposit method\",\"method_amount\":\"Amount after conversion between base currency and method currency\",\"order_no\":\"Order no\"}',1,1,NULL,'2023-04-13 07:46:49'),(220,'DELIVERY_SCANNED_BY_DRIVER','Delivery location scanned','Delivery scanned for order {{order_no}}','<p>{{method_name}} Order <strong>{{order_no}}</strong>.</p><p>Your product is with the delivery person. Track delivery: <a href=\"{{map_link}}\" target=\"_blank\">Open Google Maps</a></p>','Product with delivery. Order {{order_no}}. Map: {{map_link}}','[\"order_no\",\"method_name\",\"map_link\"]',1,0,'2026-02-26 23:45:08','2026-02-26 23:45:08'),(221,'ABANDONED_CART','Abandoned Cart Reminder','You left items in your cart – complete your order','<p>Hi {{user_name}},</p><p>You left items in your cart. Complete your order now before stock runs out!</p><p><strong>Cart value:</strong> {{cart_value}}</p><p><a href=\"{{recovery_link}}\" style=\"display:inline-block;padding:10px 20px;background:#007bff;color:#fff;text-decoration:none;border-radius:5px;\">Complete my order</a></p><p>If you have any questions, reply to this email.</p>','You left items in your cart. Complete your order: {{recovery_link}}','[\"user_name\",\"recovery_link\",\"cart_value\",\"product_list\"]',1,1,'2026-03-10 13:13:08','2026-03-10 13:13:08');
/*!40000 ALTER TABLE `notification_templates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `offer_timers`
--

DROP TABLE IF EXISTS `offer_timers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `offer_timers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `subtitle` varchar(500) DEFAULT NULL,
  `end_at` datetime NOT NULL,
  `style` varchar(30) NOT NULL DEFAULT 'bar_large',
  `bar_width` varchar(50) DEFAULT NULL,
  `bar_height` varchar(50) DEFAULT NULL,
  `position` varchar(30) NOT NULL DEFAULT 'cart_top',
  `show_on_pages` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`show_on_pages`)),
  `product_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`product_ids`)),
  `category_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`category_ids`)),
  `link_url` varchar(500) DEFAULT NULL,
  `is_active` tinyint(3) unsigned NOT NULL DEFAULT 1,
  `sort_order` int(10) unsigned NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `offer_timers`
--

LOCK TABLES `offer_timers` WRITE;
/*!40000 ALTER TABLE `offer_timers` DISABLE KEYS */;
/*!40000 ALTER TABLE `offer_timers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `omnichannel_message_attachments`
--

DROP TABLE IF EXISTS `omnichannel_message_attachments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `omnichannel_message_attachments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `message_id` bigint(20) unsigned NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `file_type` varchar(100) DEFAULT NULL,
  `file_size` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `omnichannel_message_attachments_message_id_index` (`message_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `omnichannel_message_attachments`
--

LOCK TABLES `omnichannel_message_attachments` WRITE;
/*!40000 ALTER TABLE `omnichannel_message_attachments` DISABLE KEYS */;
/*!40000 ALTER TABLE `omnichannel_message_attachments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `omnichannel_messages`
--

DROP TABLE IF EXISTS `omnichannel_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `omnichannel_messages` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `conversation_id` bigint(20) unsigned DEFAULT NULL,
  `support_ticket_id` bigint(20) unsigned DEFAULT NULL,
  `sender_type` enum('user','admin','system','bot') NOT NULL DEFAULT 'user',
  `sender_id` bigint(20) unsigned DEFAULT NULL,
  `message` text NOT NULL,
  `channel` varchar(50) DEFAULT 'web',
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `omnichannel_messages_conversation_id_index` (`conversation_id`),
  KEY `omnichannel_messages_support_ticket_id_index` (`support_ticket_id`),
  KEY `omnichannel_messages_sender_type_index` (`sender_type`),
  KEY `omnichannel_messages_is_read_index` (`is_read`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `omnichannel_messages`
--

LOCK TABLES `omnichannel_messages` WRITE;
/*!40000 ALTER TABLE `omnichannel_messages` DISABLE KEYS */;
/*!40000 ALTER TABLE `omnichannel_messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_details`
--

DROP TABLE IF EXISTS `order_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `order_details` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL DEFAULT 0,
  `product_id` int(11) NOT NULL DEFAULT 0,
  `variant_id` bigint(20) unsigned DEFAULT NULL,
  `variant_details` text DEFAULT NULL COMMENT 'JSON for display',
  `quantity` varchar(255) NOT NULL DEFAULT '0',
  `price` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_details`
--

LOCK TABLES `order_details` WRITE;
/*!40000 ALTER TABLE `order_details` DISABLE KEYS */;
INSERT INTO `order_details` VALUES (1,1,1,NULL,NULL,'1',90.00000000,NULL,NULL);
/*!40000 ALTER TABLE `order_details` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_shipment_trackings`
--

DROP TABLE IF EXISTS `order_shipment_trackings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `order_shipment_trackings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint(20) unsigned NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'processing',
  `location_name` varchar(200) DEFAULT NULL,
  `location_address` varchar(255) DEFAULT NULL,
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `notes` varchar(500) DEFAULT NULL,
  `tracking_number` varchar(100) DEFAULT NULL,
  `courier_name` varchar(100) DEFAULT NULL,
  `tracking_link` varchar(500) DEFAULT NULL COMMENT 'URL to track on courier website',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_shipment_trackings_order_id_created_at_index` (`order_id`,`created_at`),
  CONSTRAINT `order_shipment_trackings_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_shipment_trackings`
--

LOCK TABLES `order_shipment_trackings` WRITE;
/*!40000 ALTER TABLE `order_shipment_trackings` DISABLE KEYS */;
/*!40000 ALTER TABLE `order_shipment_trackings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `orders` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_type` varchar(20) NOT NULL DEFAULT 'registered' COMMENT 'registered|guest',
  `guest_name` varchar(200) DEFAULT NULL,
  `guest_phone` varchar(50) DEFAULT NULL,
  `guest_email` varchar(100) DEFAULT NULL,
  `guest_address` text DEFAULT NULL,
  `guest_location` varchar(500) DEFAULT NULL COMMENT 'District/City/Area text or JSON',
  `guest_delivery_note` text DEFAULT NULL,
  `guest_preferred_delivery_time` varchar(200) DEFAULT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `order_no` varchar(255) DEFAULT NULL,
  `subtotal` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `discount` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `shipping_charge` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `cod_charge` decimal(18,2) NOT NULL DEFAULT 0.00,
  `total` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `advance_payment` decimal(14,2) NOT NULL DEFAULT 0.00 COMMENT 'Advance/partial payment received',
  `staff_notes` text DEFAULT NULL COMMENT 'Internal staff notes',
  `coupon_id` int(11) NOT NULL DEFAULT 0,
  `shipping_method_id` int(10) unsigned NOT NULL DEFAULT 0,
  `shipping_zone_id` bigint(20) unsigned DEFAULT NULL,
  `address` text DEFAULT NULL,
  `device_lat` decimal(10,7) DEFAULT NULL,
  `device_lng` decimal(10,7) DEFAULT NULL,
  `delivery_scan_token` varchar(64) DEFAULT NULL,
  `delivery_scanned_at` timestamp NULL DEFAULT NULL,
  `delivery_driver_scan_token` varchar(64) DEFAULT NULL,
  `delivery_driver_scanned_at` timestamp NULL DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `order_source` varchar(50) DEFAULT NULL COMMENT 'quick_order|checkout|etc',
  `ad_source` varchar(100) DEFAULT NULL COMMENT 'facebook, google, tiktok',
  `utm_source` varchar(200) DEFAULT NULL,
  `utm_medium` varchar(200) DEFAULT NULL,
  `utm_campaign` varchar(200) DEFAULT NULL,
  `location_risk_score` tinyint(3) unsigned DEFAULT NULL,
  `payment_type` tinyint(1) NOT NULL DEFAULT 0,
  `cod_verified_at` timestamp NULL DEFAULT NULL,
  `payment_status` tinyint(1) NOT NULL DEFAULT 0 COMMENT ' 0: pending\r\n 1: success\r\n 9: cancel',
  `order_status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0: pending\r\n1: confirm\r\n2: shipped\r\n3: delivered\r\n9: cancel\r\n',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `delivery_estimate` varchar(100) DEFAULT NULL,
  `courier_name` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `orders_delivery_scan_token_unique` (`delivery_scan_token`),
  UNIQUE KEY `orders_delivery_driver_scan_token_unique` (`delivery_driver_scan_token`),
  KEY `idx_orders_user_id` (`user_id`),
  KEY `idx_orders_order_status` (`order_status`),
  KEY `idx_orders_created_at` (`created_at`),
  KEY `idx_orders_order_no` (`order_no`),
  KEY `orders_shipping_zone_id_foreign` (`shipping_zone_id`),
  KEY `orders_user_id_index` (`user_id`),
  KEY `orders_payment_type_index` (`payment_type`),
  CONSTRAINT `orders_shipping_zone_foreign` FOREIGN KEY (`shipping_zone_id`) REFERENCES `shipping_zones` (`id`) ON DELETE SET NULL,
  CONSTRAINT `orders_shipping_zone_id_foreign` FOREIGN KEY (`shipping_zone_id`) REFERENCES `shipping_zones` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
INSERT INTO `orders` VALUES (1,'registered',NULL,NULL,NULL,NULL,NULL,NULL,NULL,2,'OQXN824REWTF',90.00000000,0.00000000,1.00000000,0.00,91.00000000,0.00,NULL,0,1,NULL,'{\"address\":\"\\u0997\\u09a1\\u09bc\\u09bf\\u09af\\u09bc\\u09be \\u09a8\\u09a4\\u09c1\\u09a8 \\u09b9\\u09be\\u099f\",\"state\":\"Dhaka\",\"zip\":\"17493\",\"country\":\"Bangladesh\",\"city\":\"\\u09ac\\u09b0\\u09bf\\u09b6\\u09be\\u09b2\"}',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,9,0,'2025-10-10 12:25:44','2025-10-16 13:32:13',NULL,NULL),(2,'registered',NULL,NULL,NULL,NULL,NULL,NULL,NULL,3,'OEODPYY517CH',180.00000000,0.00000000,1.00000000,0.00,181.00000000,0.00,NULL,0,1,NULL,'{\"address\":\"hjhbf\",\"state\":\"545\",\"zip\":\"65\",\"country\":\"Bangladesh\",\"city\":\"dfghh\"}',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,0,0,'2025-10-11 13:20:58','2025-10-11 13:20:58',NULL,NULL),(3,'registered',NULL,NULL,NULL,NULL,NULL,NULL,NULL,3,'G8XSRWQQ1BQM',180.00000000,0.00000000,1.00000000,0.00,181.00000000,0.00,NULL,0,1,NULL,'{\"address\":\"hjhbf\",\"state\":\"545\",\"zip\":\"65\",\"country\":\"Bangladesh\",\"city\":\"dfghh\"}',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,0,0,'2025-10-11 13:22:01','2025-10-11 13:22:01',NULL,NULL),(4,'registered',NULL,NULL,NULL,NULL,NULL,NULL,NULL,3,'SZP2JMHNZ3C5',180.00000000,0.00000000,1.00000000,0.00,181.00000000,0.00,NULL,0,1,NULL,'{\"address\":\"hjhbf\",\"state\":\"545\",\"zip\":\"65\",\"country\":\"Bangladesh\",\"city\":\"dfghh\"}',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,0,0,'2025-10-11 13:27:44','2025-10-11 13:27:44',NULL,NULL),(5,'registered',NULL,NULL,NULL,NULL,NULL,NULL,NULL,3,'YD9KEHAS8HRU',180.00000000,0.00000000,1.00000000,0.00,181.00000000,0.00,NULL,0,1,NULL,'{\"address\":\"hjhbf\",\"state\":\"545\",\"zip\":\"65\",\"country\":\"Bangladesh\",\"city\":\"dfghh\"}',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,0,0,'2025-10-11 13:28:25','2025-10-11 13:28:25',NULL,NULL),(6,'registered',NULL,NULL,NULL,NULL,NULL,NULL,NULL,3,'717RMVW2K1SS',180.00000000,0.00000000,1.00000000,0.00,181.00000000,0.00,NULL,0,1,NULL,'{\"address\":\"hjhbf\",\"state\":\"545\",\"zip\":\"65\",\"country\":\"Bangladesh\",\"city\":\"dfghh\"}',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,0,0,'2025-10-11 13:50:51','2025-10-11 13:50:51',NULL,NULL),(7,'registered',NULL,NULL,NULL,NULL,NULL,NULL,NULL,3,'B82K7OUMX7RJ',180.00000000,0.00000000,1.00000000,0.00,181.00000000,0.00,NULL,0,1,NULL,'{\"address\":\"hjhbf\",\"state\":\"545\",\"zip\":\"65\",\"country\":\"Bangladesh\",\"city\":\"dfghh\"}',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,0,0,'2025-10-11 13:51:42','2025-10-11 13:51:42',NULL,NULL),(8,'registered',NULL,NULL,NULL,NULL,NULL,NULL,NULL,3,'DGG5URG39KS7',180.00000000,0.00000000,1.00000000,0.00,181.00000000,0.00,NULL,0,1,NULL,'{\"address\":\"hjhbf\",\"state\":\"545\",\"zip\":\"65\",\"country\":\"Bangladesh\",\"city\":\"dfghh\"}',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,0,0,'2025-10-11 14:50:36','2025-10-11 14:50:36',NULL,NULL),(9,'registered',NULL,NULL,NULL,NULL,NULL,NULL,NULL,4,'584BUM39DB53',100.00000000,0.00000000,0.00000000,0.00,100.00000000,0.00,NULL,0,1,NULL,'{\"address\":\"\\u0997\\u09a1\\u09bc\\u09bf\\u09af\\u09bc\\u09be \\u09a8\\u09a4\\u09c1\\u09a8 \\u09b9\\u09be\\u099f\",\"state\":\"Dhaka\",\"zip\":\"17493\",\"country\":\"Bangladesh\",\"city\":\"\\u09ac\\u09b0\\u09bf\\u09b6\\u09be\\u09b2\"}',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,0,0,'2025-10-12 19:43:54','2025-10-12 19:43:54',NULL,NULL),(10,'registered',NULL,NULL,NULL,NULL,NULL,NULL,NULL,4,'R1X2987FCR12',100.00000000,0.00000000,0.00000000,0.00,100.00000000,0.00,NULL,0,1,NULL,'{\"address\":\"\\u0997\\u09a1\\u09bc\\u09bf\\u09af\\u09bc\\u09be \\u09a8\\u09a4\\u09c1\\u09a8 \\u09b9\\u09be\\u099f\",\"state\":\"Dhaka\",\"zip\":\"17493\",\"country\":\"Bangladesh\",\"city\":\"\\u09ac\\u09b0\\u09bf\\u09b6\\u09be\\u09b2\"}',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,0,0,'2025-10-13 10:51:47','2025-10-13 10:51:47',NULL,NULL),(11,'registered',NULL,NULL,NULL,NULL,NULL,NULL,NULL,4,'WWUSWDM9Y241',100.00000000,0.00000000,0.00000000,0.00,100.00000000,0.00,NULL,0,1,NULL,'{\"address\":\"\\u0997\\u09a1\\u09bc\\u09bf\\u09af\\u09bc\\u09be \\u09a8\\u09a4\\u09c1\\u09a8 \\u09b9\\u09be\\u099f\",\"state\":\"Dhaka\",\"zip\":\"17493\",\"country\":\"Bangladesh\",\"city\":\"\\u09ac\\u09b0\\u09bf\\u09b6\\u09be\\u09b2\"}',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,0,0,'2025-10-13 11:00:56','2025-10-13 11:00:56',NULL,NULL),(12,'registered',NULL,NULL,NULL,NULL,NULL,NULL,NULL,4,'V49XRZW6NZQ2',100.00000000,0.00000000,0.00000000,0.00,100.00000000,0.00,NULL,0,1,NULL,'{\"address\":\"\\u0997\\u09a1\\u09bc\\u09bf\\u09af\\u09bc\\u09be \\u09a8\\u09a4\\u09c1\\u09a8 \\u09b9\\u09be\\u099f\",\"state\":\"Dhaka\",\"zip\":\"17493\",\"country\":\"Bangladesh\",\"city\":\"\\u09ac\\u09b0\\u09bf\\u09b6\\u09be\\u09b2\"}',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,0,0,'2025-10-13 12:36:43','2025-10-13 12:36:43',NULL,NULL),(13,'registered',NULL,NULL,NULL,NULL,NULL,NULL,NULL,4,'A8OMXVBGWGHY',100.00000000,0.00000000,0.00000000,0.00,100.00000000,0.00,NULL,0,1,NULL,'{\"address\":\"\\u0997\\u09a1\\u09bc\\u09bf\\u09af\\u09bc\\u09be \\u09a8\\u09a4\\u09c1\\u09a8 \\u09b9\\u09be\\u099f\",\"state\":\"Dhaka\",\"zip\":\"17493\",\"country\":\"Bangladesh\",\"city\":\"\\u09ac\\u09b0\\u09bf\\u09b6\\u09be\\u09b2\"}',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,0,0,'2025-10-13 13:25:38','2025-10-13 13:25:38',NULL,NULL),(14,'registered',NULL,NULL,NULL,NULL,NULL,NULL,NULL,4,'5OMMKOPFO8MJ',100.00000000,0.00000000,0.00000000,0.00,100.00000000,0.00,NULL,0,1,NULL,'{\"address\":\"\\u0997\\u09a1\\u09bc\\u09bf\\u09af\\u09bc\\u09be \\u09a8\\u09a4\\u09c1\\u09a8 \\u09b9\\u09be\\u099f\",\"state\":\"Dhaka\",\"zip\":\"17493\",\"country\":\"Bangladesh\",\"city\":\"\\u09ac\\u09b0\\u09bf\\u09b6\\u09be\\u09b2\"}',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,0,0,'2025-10-13 13:58:39','2025-10-13 13:58:39',NULL,NULL),(15,'registered',NULL,NULL,NULL,NULL,NULL,NULL,NULL,4,'WVZKCOVQO2X3',100.00000000,0.00000000,0.00000000,0.00,100.00000000,0.00,NULL,0,1,NULL,'{\"address\":\"\\u0997\\u09a1\\u09bc\\u09bf\\u09af\\u09bc\\u09be \\u09a8\\u09a4\\u09c1\\u09a8 \\u09b9\\u09be\\u099f\",\"state\":\"Dhaka\",\"zip\":\"17493\",\"country\":\"Bangladesh\",\"city\":\"\\u09ac\\u09b0\\u09bf\\u09b6\\u09be\\u09b2\"}',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,0,0,'2025-10-14 12:04:54','2025-10-14 12:04:54',NULL,NULL),(16,'registered',NULL,NULL,NULL,NULL,NULL,NULL,NULL,4,'JYZGXD9UFRYS',100.00000000,0.00000000,0.00000000,0.00,100.00000000,0.00,NULL,0,1,NULL,'{\"address\":\"\\u0997\\u09a1\\u09bc\\u09bf\\u09af\\u09bc\\u09be \\u09a8\\u09a4\\u09c1\\u09a8 \\u09b9\\u09be\\u099f\",\"state\":\"Dhaka\",\"zip\":\"17493\",\"country\":\"Bangladesh\",\"city\":\"\\u09ac\\u09b0\\u09bf\\u09b6\\u09be\\u09b2\"}',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,0,0,'2025-10-14 12:12:33','2025-10-14 12:12:33',NULL,NULL),(17,'registered',NULL,NULL,NULL,NULL,NULL,NULL,NULL,4,'YAQ54JD9TE9S',100.00000000,0.00000000,0.00000000,0.00,100.00000000,0.00,NULL,0,1,NULL,'{\"address\":\"\\u0997\\u09a1\\u09bc\\u09bf\\u09af\\u09bc\\u09be \\u09a8\\u09a4\\u09c1\\u09a8 \\u09b9\\u09be\\u099f\",\"state\":\"Dhaka\",\"zip\":\"17493\",\"country\":\"Bangladesh\",\"city\":\"\\u09ac\\u09b0\\u09bf\\u09b6\\u09be\\u09b2\"}',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,0,0,'2025-10-14 13:24:01','2025-10-14 13:24:01',NULL,NULL),(18,'registered',NULL,NULL,NULL,NULL,NULL,NULL,NULL,4,'T6OXEMQ3ZPKY',100.00000000,0.00000000,0.00000000,0.00,100.00000000,0.00,NULL,0,1,NULL,'{\"address\":\"\\u0997\\u09a1\\u09bc\\u09bf\\u09af\\u09bc\\u09be \\u09a8\\u09a4\\u09c1\\u09a8 \\u09b9\\u09be\\u099f\",\"state\":\"Dhaka\",\"zip\":\"17493\",\"country\":\"Bangladesh\",\"city\":\"\\u09ac\\u09b0\\u09bf\\u09b6\\u09be\\u09b2\"}',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,0,0,'2025-10-14 13:49:29','2025-10-14 13:49:29',NULL,NULL),(19,'registered',NULL,NULL,NULL,NULL,NULL,NULL,NULL,4,'ASTMMCU1R6NX',200.00000000,0.00000000,0.00000000,0.00,200.00000000,0.00,NULL,0,1,NULL,'{\"address\":\"\\u0997\\u09a1\\u09bc\\u09bf\\u09af\\u09bc\\u09be \\u09a8\\u09a4\\u09c1\\u09a8 \\u09b9\\u09be\\u099f\",\"state\":\"Dhaka\",\"zip\":\"17493\",\"country\":\"Bangladesh\",\"city\":\"\\u09ac\\u09b0\\u09bf\\u09b6\\u09be\\u09b2\"}',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,0,0,'2025-10-15 10:45:55','2025-10-15 10:45:55',NULL,NULL),(20,'registered',NULL,NULL,NULL,NULL,NULL,NULL,NULL,5,'RVVMU3YOKRUC',100.00000000,0.00000000,0.00000000,0.00,100.00000000,0.00,NULL,0,1,NULL,'{\"address\":\"fvjfjbjfh\",\"state\":\"1207\",\"zip\":\"1207\",\"country\":\"Bangladesh\",\"city\":\"Dhaka\"}',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,0,0,'2025-10-15 11:43:59','2025-10-15 11:43:59',NULL,NULL),(21,'registered',NULL,NULL,NULL,NULL,NULL,NULL,NULL,6,'JDQB4PDBJ12F',100.00000000,0.00000000,0.00000000,0.00,100.00000000,0.00,NULL,0,1,NULL,'{\"address\":\"Noyakhali\",\"state\":\"Noakhali\",\"zip\":\"182\",\"country\":\"Afghanistan\",\"city\":\"Noakhali\"}',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,0,0,'2025-10-15 11:49:51','2025-10-15 11:49:51',NULL,NULL),(22,'registered',NULL,NULL,NULL,NULL,NULL,NULL,NULL,5,'3QBCFNGVVHEZ',100.00000000,0.00000000,0.00000000,0.00,100.00000000,0.00,NULL,0,1,NULL,'{\"address\":\"fvjfjbjfh\",\"state\":\"1207\",\"zip\":\"1207\",\"country\":\"Bangladesh\",\"city\":\"Dhaka\"}',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,0,0,'2025-10-15 12:41:18','2025-10-15 12:41:18',NULL,NULL),(23,'registered',NULL,NULL,NULL,NULL,NULL,NULL,NULL,6,'JV74HNT21OF5',100.00000000,0.00000000,0.00000000,0.00,100.00000000,0.00,NULL,0,1,NULL,'{\"address\":\"Noyakhali\",\"state\":\"Noakhali\",\"zip\":\"182\",\"country\":\"Afghanistan\",\"city\":\"Noakhali\"}',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,0,0,'2025-10-15 22:25:07','2025-10-15 22:25:07',NULL,NULL),(24,'registered',NULL,NULL,NULL,NULL,NULL,NULL,NULL,6,'PAB156CC2Y5E',100.00000000,0.00000000,0.00000000,0.00,100.00000000,0.00,NULL,0,1,NULL,'{\"address\":\"Noyakhali\",\"state\":\"Noakhali\",\"zip\":\"182\",\"country\":\"Afghanistan\",\"city\":\"Noakhali\"}',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,0,0,'2025-10-15 23:20:50','2025-10-15 23:20:50',NULL,NULL),(25,'registered',NULL,NULL,NULL,NULL,NULL,NULL,NULL,6,'S2K7XZUFYGOO',100.00000000,0.00000000,0.00000000,0.00,100.00000000,0.00,NULL,0,1,NULL,'{\"address\":\"Noyakhali\",\"state\":\"Noakhali\",\"zip\":\"182\",\"country\":\"Afghanistan\",\"city\":\"Noakhali\"}',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,0,0,'2025-10-15 23:26:38','2025-10-15 23:26:38',NULL,NULL),(26,'registered',NULL,NULL,NULL,NULL,NULL,NULL,NULL,6,'81JKTSQZVSSA',100.00000000,0.00000000,0.00000000,0.00,100.00000000,0.00,NULL,0,1,NULL,'{\"address\":\"Noyakhali\",\"state\":\"Noakhali\",\"zip\":\"182\",\"country\":\"Afghanistan\",\"city\":\"Noakhali\"}',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,0,0,'2025-10-15 23:30:42','2025-10-15 23:30:42',NULL,NULL),(27,'registered',NULL,NULL,NULL,NULL,NULL,NULL,NULL,6,'R24J8SM7UT46',100.00000000,0.00000000,0.00000000,0.00,100.00000000,0.00,NULL,0,1,NULL,'{\"address\":\"Noyakhali\",\"state\":\"Noakhali\",\"zip\":\"182\",\"country\":\"Afghanistan\",\"city\":\"Noakhali\"}',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,0,0,'2025-10-15 23:34:54','2025-10-15 23:34:54',NULL,NULL),(28,'registered',NULL,NULL,NULL,NULL,NULL,NULL,NULL,6,'Q1NYM36BMVPY',100.00000000,0.00000000,0.00000000,0.00,100.00000000,0.00,NULL,0,1,NULL,'{\"address\":\"Noyakhali\",\"state\":\"Noakhali\",\"zip\":\"182\",\"country\":\"Afghanistan\",\"city\":\"Noakhali\"}',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,0,0,'2025-10-15 23:50:17','2025-10-15 23:50:17',NULL,NULL),(29,'registered',NULL,NULL,NULL,NULL,NULL,NULL,NULL,6,'MSV9Y4RXW3VX',100.00000000,0.00000000,0.00000000,0.00,100.00000000,0.00,NULL,0,1,NULL,'{\"address\":\"Noyakhali\",\"state\":\"Noakhali\",\"zip\":\"182\",\"country\":\"Afghanistan\",\"city\":\"Noakhali\"}',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,0,0,'2025-10-15 23:59:41','2025-10-15 23:59:41',NULL,NULL),(30,'registered',NULL,NULL,NULL,NULL,NULL,NULL,NULL,6,'HKHHEMPRT63V',100.00000000,0.00000000,0.00000000,0.00,100.00000000,0.00,NULL,0,1,NULL,'{\"address\":\"Noyakhali\",\"state\":\"Noakhali\",\"zip\":\"182\",\"country\":\"Afghanistan\",\"city\":\"Noakhali\"}',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,0,0,'2025-10-16 00:09:49','2025-10-16 00:09:49',NULL,NULL),(31,'registered',NULL,NULL,NULL,NULL,NULL,NULL,NULL,6,'41HKZ25V8VXJ',100.00000000,0.00000000,0.00000000,0.00,100.00000000,0.00,NULL,0,1,NULL,'{\"address\":\"Noyakhali\",\"state\":\"Noakhali\",\"zip\":\"182\",\"country\":\"Afghanistan\",\"city\":\"Noakhali\"}',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,0,0,'2025-10-16 00:10:43','2025-10-16 00:10:43',NULL,NULL),(32,'registered',NULL,NULL,NULL,NULL,NULL,NULL,NULL,6,'TABMWD36UXF1',100.00000000,0.00000000,0.00000000,0.00,100.00000000,0.00,NULL,0,1,NULL,'{\"address\":\"Noyakhali\",\"state\":\"Noakhali\",\"zip\":\"182\",\"country\":\"Afghanistan\",\"city\":\"Noakhali\"}',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,0,0,'2025-10-16 00:45:10','2025-10-16 00:45:10',NULL,NULL),(33,'registered',NULL,NULL,NULL,NULL,NULL,NULL,NULL,6,'814FB5BFPDEO',150.00000000,0.00000000,0.00000000,0.00,150.00000000,0.00,NULL,0,1,NULL,'{\"address\":\"Noyakhali\",\"state\":\"Noakhali\",\"zip\":\"182\",\"country\":\"Afghanistan\",\"city\":\"Noakhali\"}',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,0,0,'2025-10-16 07:54:39','2025-10-16 07:54:39',NULL,NULL),(34,'registered',NULL,NULL,NULL,NULL,NULL,NULL,NULL,6,'T9Q97JQWADKR',250.00000000,0.00000000,0.00000000,0.00,250.00000000,0.00,NULL,0,1,NULL,'{\"address\":\"Noyakhali\",\"state\":\"Noakhali\",\"zip\":\"182\",\"country\":\"Afghanistan\",\"city\":\"Noakhali\"}',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,0,0,'2025-10-16 07:55:12','2025-10-16 07:55:12',NULL,NULL),(35,'registered',NULL,NULL,NULL,NULL,NULL,NULL,NULL,6,'UF1EMZDEC7WU',450.00000000,0.00000000,0.00000000,0.00,450.00000000,0.00,NULL,0,1,NULL,'{\"address\":\"Noyakhali\",\"state\":\"Noakhali\",\"zip\":\"182\",\"country\":\"Afghanistan\",\"city\":\"Noakhali\"}',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,0,0,'2025-10-16 11:36:01','2025-10-16 11:36:01',NULL,NULL),(36,'registered',NULL,NULL,NULL,NULL,NULL,NULL,NULL,8,'N5Q188M7FJUV',100.00000000,0.00000000,0.00000000,0.00,100.00000000,0.00,NULL,0,1,NULL,'{\"address\":\"fbfb\",\"address_2\":\"dbdbd\",\"state\":null,\"zip\":\"1207\",\"country\":\"Bangladesh\",\"city\":\"Dhaka\",\"thana\":null,\"division\":\"Dhaka\"}',NULL,NULL,NULL,NULL,NULL,NULL,'::1',NULL,NULL,NULL,NULL,NULL,0,1,NULL,0,0,'2026-02-28 12:53:56','2026-02-28 12:53:56',NULL,NULL);
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_resets`
--

DROP TABLE IF EXISTS `password_resets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `password_resets` (
  `email` varchar(40) DEFAULT NULL,
  `token` varchar(40) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_resets`
--

LOCK TABLES `password_resets` WRITE;
/*!40000 ALTER TABLE `password_resets` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_resets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payment_events`
--

DROP TABLE IF EXISTS `payment_events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payment_events` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `gateway` varchar(64) NOT NULL,
  `idempotency_key` varchar(128) DEFAULT NULL,
  `trx` varchar(100) DEFAULT NULL,
  `deposit_id` bigint(20) unsigned DEFAULT NULL,
  `order_id` bigint(20) unsigned DEFAULT NULL,
  `event_type` varchar(64) NOT NULL,
  `old_status` tinyint(3) unsigned DEFAULT NULL,
  `new_status` tinyint(3) unsigned DEFAULT NULL,
  `signature_valid` tinyint(1) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `gateway_response` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`gateway_response`)),
  `webhook_payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`webhook_payload`)),
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `payment_events_gateway_idempotency_unique` (`gateway`,`idempotency_key`),
  KEY `payment_events_gateway_index` (`gateway`),
  KEY `payment_events_trx_index` (`trx`),
  KEY `payment_events_deposit_id_index` (`deposit_id`),
  KEY `payment_events_order_id_index` (`order_id`),
  KEY `payment_events_event_type_index` (`event_type`),
  KEY `payment_events_idempotency_key_index` (`idempotency_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payment_events`
--

LOCK TABLES `payment_events` WRITE;
/*!40000 ALTER TABLE `payment_events` DISABLE KEYS */;
/*!40000 ALTER TABLE `payment_events` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payment_fraud_attempts`
--

DROP TABLE IF EXISTS `payment_fraud_attempts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payment_fraud_attempts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(45) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `order_id` bigint(20) unsigned DEFAULT NULL,
  `gateway` varchar(50) DEFAULT NULL,
  `reason` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `payment_fraud_attempts_ip_address_index` (`ip_address`),
  KEY `payment_fraud_attempts_user_id_index` (`user_id`),
  KEY `payment_fraud_attempts_order_id_index` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payment_fraud_attempts`
--

LOCK TABLES `payment_fraud_attempts` WRITE;
/*!40000 ALTER TABLE `payment_fraud_attempts` DISABLE KEYS */;
/*!40000 ALTER TABLE `payment_fraud_attempts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payment_ledger`
--

DROP TABLE IF EXISTS `payment_ledger`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payment_ledger` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint(20) unsigned DEFAULT NULL,
  `transaction_id` bigint(20) unsigned DEFAULT NULL,
  `deposit_id` bigint(20) unsigned DEFAULT NULL,
  `gateway` varchar(64) DEFAULT NULL,
  `amount` decimal(18,8) NOT NULL DEFAULT 0.00000000,
  `currency` varchar(10) NOT NULL DEFAULT 'USD',
  `status` varchar(32) NOT NULL,
  `trx` varchar(100) DEFAULT NULL,
  `previous_hash` varchar(64) DEFAULT NULL,
  `ledger_hash` varchar(64) NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `payment_ledger_created_at_index` (`created_at`),
  KEY `payment_ledger_order_id_index` (`order_id`),
  KEY `payment_ledger_transaction_id_index` (`transaction_id`),
  KEY `payment_ledger_deposit_id_index` (`deposit_id`),
  KEY `payment_ledger_status_index` (`status`),
  KEY `payment_ledger_trx_index` (`trx`),
  KEY `payment_ledger_ledger_hash_index` (`ledger_hash`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payment_ledger`
--

LOCK TABLES `payment_ledger` WRITE;
/*!40000 ALTER TABLE `payment_ledger` DISABLE KEYS */;
/*!40000 ALTER TABLE `payment_ledger` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payment_refunds`
--

DROP TABLE IF EXISTS `payment_refunds`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payment_refunds` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `deposit_id` bigint(20) unsigned DEFAULT NULL,
  `order_id` bigint(20) unsigned DEFAULT NULL,
  `amount` decimal(18,2) NOT NULL DEFAULT 0.00,
  `type` varchar(20) NOT NULL DEFAULT 'full',
  `status` varchar(30) NOT NULL DEFAULT 'pending',
  `gateway_refund_id` varchar(255) DEFAULT NULL,
  `processed_at` timestamp NULL DEFAULT NULL,
  `admin_id` bigint(20) unsigned DEFAULT NULL,
  `reason` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `payment_refunds_deposit_id_index` (`deposit_id`),
  KEY `payment_refunds_order_id_index` (`order_id`),
  KEY `payment_refunds_status_index` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payment_refunds`
--

LOCK TABLES `payment_refunds` WRITE;
/*!40000 ALTER TABLE `payment_refunds` DISABLE KEYS */;
/*!40000 ALTER TABLE `payment_refunds` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payment_transactions`
--

DROP TABLE IF EXISTS `payment_transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payment_transactions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint(20) unsigned DEFAULT NULL,
  `deposit_id` bigint(20) unsigned DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `transaction_id` varchar(255) DEFAULT NULL,
  `gateway_response` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`gateway_response`)),
  `amount` decimal(18,2) NOT NULL DEFAULT 0.00,
  `currency` varchar(10) NOT NULL DEFAULT 'BDT',
  `status` varchar(30) NOT NULL DEFAULT 'pending',
  `paid_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `payment_transactions_order_id_index` (`order_id`),
  KEY `payment_transactions_deposit_id_index` (`deposit_id`),
  KEY `payment_transactions_payment_method_index` (`payment_method`),
  KEY `payment_transactions_transaction_id_index` (`transaction_id`),
  KEY `payment_transactions_status_index` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payment_transactions`
--

LOCK TABLES `payment_transactions` WRITE;
/*!40000 ALTER TABLE `payment_transactions` DISABLE KEYS */;
/*!40000 ALTER TABLE `payment_transactions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `permissions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `group` varchar(50) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_unique` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permissions`
--

LOCK TABLES `permissions` WRITE;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `personal_access_tokens`
--

LOCK TABLES `personal_access_tokens` WRITE;
/*!40000 ALTER TABLE `personal_access_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `personal_access_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `popup_ads`
--

DROP TABLE IF EXISTS `popup_ads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `popup_ads` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `delay_seconds` smallint(5) unsigned NOT NULL DEFAULT 3 COMMENT 'Show after N seconds',
  `image` varchar(500) DEFAULT NULL,
  `link_url` varchar(500) DEFAULT NULL,
  `width` varchar(100) DEFAULT NULL COMMENT 'e.g. 90%, 800px, auto',
  `height` varchar(100) DEFAULT NULL COMMENT 'e.g. 80vh, 600px, auto',
  `position` varchar(20) NOT NULL DEFAULT 'center' COMMENT 'center, top-left, top-right, bottom-left, bottom-right',
  `display_type` varchar(20) NOT NULL DEFAULT 'popup' COMMENT 'popup=modal with close, inline=stays on page',
  `inline_placement` varchar(50) DEFAULT NULL COMMENT 'sidebar_right, sidebar_left, content_top, content_bottom',
  `show_on_pages` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'all, home, cart, etc.' CHECK (json_valid(`show_on_pages`)),
  `is_active` tinyint(3) unsigned NOT NULL DEFAULT 1,
  `sort_order` int(10) unsigned NOT NULL DEFAULT 0,
  `start_at` timestamp NULL DEFAULT NULL,
  `end_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `popup_ads`
--

LOCK TABLES `popup_ads` WRITE;
/*!40000 ALTER TABLE `popup_ads` DISABLE KEYS */;
/*!40000 ALTER TABLE `popup_ads` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_attribute_values`
--

DROP TABLE IF EXISTS `product_attribute_values`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `product_attribute_values` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint(20) unsigned NOT NULL,
  `attribute_id` bigint(20) unsigned NOT NULL,
  `value` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `prod_attr_idx` (`product_id`,`attribute_id`),
  KEY `fk_attr_val_attribute` (`attribute_id`),
  CONSTRAINT `fk_attr_val_attribute` FOREIGN KEY (`attribute_id`) REFERENCES `product_attributes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_attr_val_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_attribute_values`
--

LOCK TABLES `product_attribute_values` WRITE;
/*!40000 ALTER TABLE `product_attribute_values` DISABLE KEYS */;
/*!40000 ALTER TABLE `product_attribute_values` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_attributes`
--

DROP TABLE IF EXISTS `product_attributes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `product_attributes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL COMMENT 'Attribute name: Size, Color, Material, etc.',
  `slug` varchar(100) NOT NULL,
  `type` varchar(50) NOT NULL DEFAULT 'select' COMMENT 'select, color, text, number',
  `values` text DEFAULT NULL COMMENT 'JSON array of possible values',
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `idx_status` (`status`),
  KEY `idx_type` (`type`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_attributes`
--

LOCK TABLES `product_attributes` WRITE;
/*!40000 ALTER TABLE `product_attributes` DISABLE KEYS */;
INSERT INTO `product_attributes` VALUES (1,'Size','size','select','[\"XS\",\"S\",\"M\",\"L\",\"XL\",\"XXL\",\"XXXL\"]',1,1,'2026-02-11 07:36:13','2026-02-11 07:36:13'),(2,'Color','color','color','[\"Red\",\"Blue\",\"Green\",\"Black\",\"White\",\"Yellow\",\"Pink\",\"Purple\",\"Orange\",\"Brown\",\"Gray\",\"Navy\",\"Maroon\",\"Beige\"]',2,1,'2026-02-11 07:36:13','2026-02-11 07:36:13'),(3,'Material','material','select','[\"Cotton\",\"Polyester\",\"Silk\",\"Wool\",\"Leather\",\"Denim\",\"Linen\",\"Velvet\",\"Satin\",\"Nylon\"]',3,1,'2026-02-11 07:36:13','2026-02-11 07:36:13'),(4,'Capacity','capacity','select','[\"16GB\",\"32GB\",\"64GB\",\"128GB\",\"256GB\",\"512GB\",\"1TB\",\"2TB\"]',4,1,'2026-02-11 07:36:13','2026-02-11 07:36:13'),(5,'RAM','ram','select','[\"2GB\",\"4GB\",\"6GB\",\"8GB\",\"12GB\",\"16GB\",\"32GB\",\"64GB\"]',5,1,'2026-02-11 07:36:13','2026-02-11 07:36:13'),(6,'Storage Type','storage-type','select','[\"HDD\",\"SSD\",\"NVMe\",\"M.2 SSD\"]',6,1,'2026-02-11 07:36:13','2026-02-11 07:36:13'),(7,'Screen Size','screen-size','select','[\"5.5 inch\",\"6 inch\",\"6.5 inch\",\"6.7 inch\",\"7 inch\",\"10 inch\",\"13 inch\",\"15 inch\",\"17 inch\"]',7,1,'2026-02-11 07:36:13','2026-02-11 07:36:13'),(8,'Weight','weight','text',NULL,8,1,'2026-02-11 07:36:13','2026-02-11 07:36:13'),(9,'Dimensions','dimensions','text',NULL,9,1,'2026-02-11 07:36:13','2026-02-11 07:36:13'),(10,'Warranty','warranty','select','[\"No Warranty\",\"6 Months\",\"1 Year\",\"2 Years\",\"3 Years\",\"5 Years\",\"Lifetime\"]',10,1,'2026-02-11 07:36:13','2026-02-11 07:36:13');
/*!40000 ALTER TABLE `product_attributes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_comparisons`
--

DROP TABLE IF EXISTS `product_comparisons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `product_comparisons` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `session_id` varchar(255) DEFAULT NULL,
  `product_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `product_comparisons_user_id_index` (`user_id`),
  KEY `product_comparisons_product_id_index` (`product_id`),
  KEY `product_comparisons_session_id_index` (`session_id`)
) ENGINE=InnoDB AUTO_INCREMENT=105 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_comparisons`
--

LOCK TABLES `product_comparisons` WRITE;
/*!40000 ALTER TABLE `product_comparisons` DISABLE KEYS */;
INSERT INTO `product_comparisons` VALUES (1,8,NULL,2,'2026-02-28 12:52:14','2026-02-28 12:52:14'),(2,9,NULL,1,'2026-03-02 13:06:13','2026-03-02 13:06:13'),(3,9,NULL,2,'2026-03-02 13:07:45','2026-03-02 13:07:45'),(4,9,NULL,3,'2026-03-02 13:07:48','2026-03-02 13:07:48'),(6,NULL,'YYW8aHzTx7L9WKZfnEeWeBBLcG3utzM8wr3O0GCC',2,'2026-03-02 13:08:40','2026-03-02 13:08:40'),(7,NULL,'YYW8aHzTx7L9WKZfnEeWeBBLcG3utzM8wr3O0GCC',3,'2026-03-02 13:08:46','2026-03-02 13:08:46'),(8,NULL,'YYW8aHzTx7L9WKZfnEeWeBBLcG3utzM8wr3O0GCC',4,'2026-03-02 13:08:49','2026-03-02 13:08:49'),(9,NULL,'YYW8aHzTx7L9WKZfnEeWeBBLcG3utzM8wr3O0GCC',5,'2026-03-02 13:08:52','2026-03-02 13:08:52'),(11,NULL,'JBvj0UU68tZdA8SS6NNcWFqKfjIifonHkK10IJfe',3,'2026-03-04 08:49:00','2026-03-04 08:49:00'),(12,NULL,'loItlxC5pLxMxu7SZ103FrHUP684xnkIMMZ14lxv',2,'2026-03-04 08:49:30','2026-03-04 08:49:30'),(15,NULL,'HpaC2hxogXql26lWVlHXaHWrVJh9b4qkiwTXDFA7',2,'2026-03-04 09:00:58','2026-03-04 09:00:58'),(16,NULL,'SxM7aiOshBlBWJ0prgCtB7V4bX686boqg7hoWeqJ',2,'2026-03-04 09:12:58','2026-03-04 09:12:58'),(17,NULL,'SxM7aiOshBlBWJ0prgCtB7V4bX686boqg7hoWeqJ',1,'2026-03-04 09:13:19','2026-03-04 09:13:19'),(18,NULL,'29zjNouOJR99UahGQy3CXjEPNjgkfmm169w9xGmA',2,'2026-03-04 09:30:52','2026-03-04 09:30:52'),(19,NULL,'NWAxjd8iUULH1tpjCWp8B23JfUv85meYiTuLoVBN',2,'2026-03-04 09:39:44','2026-03-04 09:39:44'),(20,NULL,'35BYrrbes49ujtTwzytkgNuItsjCOtBk7dWlPUke',2,'2026-03-04 09:42:40','2026-03-04 09:42:40'),(21,NULL,'yk5DhLqEJIKHLKprvKYumQXwz995sVsDNdsoDP2J',2,'2026-03-04 09:47:27','2026-03-04 09:47:27'),(22,NULL,'pRhK6q0SRJXiekCcWhECS7CafZfOvrQPpNuh9Zb3',2,'2026-03-04 09:50:44','2026-03-04 09:50:44'),(23,NULL,'pRhK6q0SRJXiekCcWhECS7CafZfOvrQPpNuh9Zb3',1,'2026-03-04 09:50:50','2026-03-04 09:50:50'),(24,NULL,'pRhK6q0SRJXiekCcWhECS7CafZfOvrQPpNuh9Zb3',5,'2026-03-04 09:51:23','2026-03-04 09:51:23'),(25,NULL,'t3jSkeKmHXsra372NnnUk2CebwIV4TEKHVQEMj6y',1,'2026-03-04 10:04:12','2026-03-04 10:04:12'),(26,NULL,'t3jSkeKmHXsra372NnnUk2CebwIV4TEKHVQEMj6y',2,'2026-03-04 10:04:33','2026-03-04 10:04:33'),(27,NULL,'t3jSkeKmHXsra372NnnUk2CebwIV4TEKHVQEMj6y',4,'2026-03-04 10:04:43','2026-03-04 10:04:43'),(28,NULL,'zMrenI8pnMySQUk4HHNVidSdEIkNoPIXPaK5nI9h',1,'2026-03-04 10:11:29','2026-03-04 10:11:29'),(29,NULL,'YE9MiJNZclmEHAUlgxiR7zML6J6XE9VK5AJI1Zed',1,'2026-03-04 10:19:11','2026-03-04 10:19:11'),(31,NULL,'98I2bjBXIdlMToFy1Uri5OwgoaeB0GFXlkZxW4lr',2,'2026-03-04 10:26:55','2026-03-04 10:26:55'),(32,NULL,'461eWH3XzhubW6qB5pCu9JtYTlfJpQTc3UovrP3r',1,'2026-03-04 10:31:14','2026-03-04 10:31:14'),(33,NULL,'461eWH3XzhubW6qB5pCu9JtYTlfJpQTc3UovrP3r',2,'2026-03-04 10:31:16','2026-03-04 10:31:16'),(34,NULL,'MEuBR4C0r4nZdmG3solMyBOZmV1cVAztTHtidYom',2,'2026-03-04 10:37:13','2026-03-04 10:37:13'),(35,NULL,'ZKPQXJvuAti8KIx3HtZmTGmB61xtWniSFV5Mht7n',4,'2026-03-04 10:43:26','2026-03-04 10:43:26'),(36,NULL,'ZKPQXJvuAti8KIx3HtZmTGmB61xtWniSFV5Mht7n',5,'2026-03-04 10:43:30','2026-03-04 10:43:30'),(37,NULL,'fMujLtzZv6kWcLna441DUksrtFkCD75wWKPcuQkO',2,'2026-03-04 10:54:32','2026-03-04 10:54:32'),(38,NULL,'tGkpp9Rw1aYHvW6BrYSbq66viSANAcXYqtdd4Oba',2,'2026-03-07 08:39:20','2026-03-07 08:39:20'),(39,NULL,'tGkpp9Rw1aYHvW6BrYSbq66viSANAcXYqtdd4Oba',3,'2026-03-07 08:39:26','2026-03-07 08:39:26'),(40,NULL,'tGkpp9Rw1aYHvW6BrYSbq66viSANAcXYqtdd4Oba',4,'2026-03-07 08:39:28','2026-03-07 08:39:28'),(41,NULL,'tGkpp9Rw1aYHvW6BrYSbq66viSANAcXYqtdd4Oba',5,'2026-03-07 08:39:31','2026-03-07 08:39:31'),(42,NULL,'8MZCMNSA7vrJVKm3Uvg9WWR23auqnYo5hqXeJmxo',2,'2026-03-08 09:05:10','2026-03-08 09:05:10'),(43,NULL,'8MZCMNSA7vrJVKm3Uvg9WWR23auqnYo5hqXeJmxo',3,'2026-03-08 09:05:12','2026-03-08 09:05:12'),(44,NULL,'8MZCMNSA7vrJVKm3Uvg9WWR23auqnYo5hqXeJmxo',4,'2026-03-08 09:05:14','2026-03-08 09:05:14'),(45,NULL,'8MZCMNSA7vrJVKm3Uvg9WWR23auqnYo5hqXeJmxo',5,'2026-03-08 09:05:15','2026-03-08 09:05:15'),(46,NULL,'CbhJ1UDxYiyyfSCzdWsdQ5DutYQPy0rotgycGj5h',2,'2026-03-09 07:59:26','2026-03-09 07:59:26'),(47,NULL,'CbhJ1UDxYiyyfSCzdWsdQ5DutYQPy0rotgycGj5h',3,'2026-03-09 07:59:28','2026-03-09 07:59:28'),(48,NULL,'CbhJ1UDxYiyyfSCzdWsdQ5DutYQPy0rotgycGj5h',4,'2026-03-09 07:59:30','2026-03-09 07:59:30'),(49,NULL,'CbhJ1UDxYiyyfSCzdWsdQ5DutYQPy0rotgycGj5h',5,'2026-03-09 07:59:32','2026-03-09 07:59:32'),(50,NULL,'aJdnU8fg2dXR2CZqmVv81iKpJbxkKzj2MXKfLDbn',2,'2026-03-09 12:30:35','2026-03-09 12:30:35'),(51,NULL,'aJdnU8fg2dXR2CZqmVv81iKpJbxkKzj2MXKfLDbn',3,'2026-03-09 12:30:37','2026-03-09 12:30:37'),(52,NULL,'aJdnU8fg2dXR2CZqmVv81iKpJbxkKzj2MXKfLDbn',4,'2026-03-09 12:30:39','2026-03-09 12:30:39'),(53,NULL,'aJdnU8fg2dXR2CZqmVv81iKpJbxkKzj2MXKfLDbn',5,'2026-03-09 12:30:41','2026-03-09 12:30:41'),(54,NULL,'GjP4oV8yc6DtEarQkYUxIHc0HSgVp54BEBItJ4YU',6,'2026-03-10 07:55:24','2026-03-10 07:55:24'),(55,NULL,'GjP4oV8yc6DtEarQkYUxIHc0HSgVp54BEBItJ4YU',7,'2026-03-10 07:55:29','2026-03-10 07:55:29'),(56,NULL,'GjP4oV8yc6DtEarQkYUxIHc0HSgVp54BEBItJ4YU',5,'2026-03-10 07:55:32','2026-03-10 07:55:32'),(57,NULL,'GjP4oV8yc6DtEarQkYUxIHc0HSgVp54BEBItJ4YU',4,'2026-03-10 07:55:34','2026-03-10 07:55:34'),(58,NULL,'GjP4oV8yc6DtEarQkYUxIHc0HSgVp54BEBItJ4YU',8,'2026-03-10 09:01:32','2026-03-10 09:01:32'),(59,NULL,'GjP4oV8yc6DtEarQkYUxIHc0HSgVp54BEBItJ4YU',9,'2026-03-10 09:01:36','2026-03-10 09:01:36'),(60,NULL,'GjP4oV8yc6DtEarQkYUxIHc0HSgVp54BEBItJ4YU',10,'2026-03-10 09:01:40','2026-03-10 09:01:40'),(61,NULL,'GjP4oV8yc6DtEarQkYUxIHc0HSgVp54BEBItJ4YU',3,'2026-03-10 09:02:04','2026-03-10 09:02:04'),(62,NULL,'KdPlybFVNQ95Elf1DguFPx0wM6fRXXVEQeysTCE0',2,'2026-03-11 14:31:32','2026-03-11 14:31:32'),(63,NULL,'KdPlybFVNQ95Elf1DguFPx0wM6fRXXVEQeysTCE0',7,'2026-03-11 14:32:02','2026-03-11 14:32:02'),(64,NULL,'mQMhZEE5owugQrUwJcW2jAAOQUsfuRUlAJvQmMeS',9,'2026-03-12 23:14:02','2026-03-12 23:14:02'),(65,NULL,'mQMhZEE5owugQrUwJcW2jAAOQUsfuRUlAJvQmMeS',7,'2026-03-12 23:53:09','2026-03-12 23:53:09'),(66,NULL,'m0yy7tbWPKGjI2qA8xamd6GAWtB0hpVN7YfOKewz',7,'2026-03-13 05:31:02','2026-03-13 05:31:02'),(67,NULL,'m0yy7tbWPKGjI2qA8xamd6GAWtB0hpVN7YfOKewz',5,'2026-03-13 05:31:05','2026-03-13 05:31:05'),(68,NULL,'m0yy7tbWPKGjI2qA8xamd6GAWtB0hpVN7YfOKewz',4,'2026-03-13 05:31:08','2026-03-13 05:31:08'),(69,NULL,'jk9QyWa3c134IZsF2KgaXCMUpq3ghbV8ItFSgGzm',8,'2026-03-13 05:34:46','2026-03-13 05:34:46'),(70,NULL,'jk9QyWa3c134IZsF2KgaXCMUpq3ghbV8ItFSgGzm',5,'2026-03-13 05:34:51','2026-03-13 05:34:51'),(71,NULL,'m0yy7tbWPKGjI2qA8xamd6GAWtB0hpVN7YfOKewz',8,'2026-03-13 07:11:13','2026-03-13 07:11:13'),(72,NULL,'m0yy7tbWPKGjI2qA8xamd6GAWtB0hpVN7YfOKewz',9,'2026-03-13 07:11:29','2026-03-13 07:11:29'),(73,NULL,'m0yy7tbWPKGjI2qA8xamd6GAWtB0hpVN7YfOKewz',10,'2026-03-13 07:11:35','2026-03-13 07:11:35'),(74,NULL,'m0yy7tbWPKGjI2qA8xamd6GAWtB0hpVN7YfOKewz',2,'2026-03-13 07:44:23','2026-03-13 07:44:23'),(75,NULL,'m0yy7tbWPKGjI2qA8xamd6GAWtB0hpVN7YfOKewz',3,'2026-03-13 07:44:26','2026-03-13 07:44:26'),(79,NULL,'RDmxyY7rCGTdYB30g67paEAxxe9V9ko0brdsuTb0',7,'2026-03-13 09:04:15','2026-03-13 09:04:15'),(81,NULL,'RDmxyY7rCGTdYB30g67paEAxxe9V9ko0brdsuTb0',9,'2026-03-13 09:04:18','2026-03-13 09:04:18'),(82,NULL,'hktait89zrO2A2udioTjywpeKMCi1GUwQOE6xAkr',10,'2026-03-13 09:05:14','2026-03-13 09:05:14'),(83,NULL,'RDmxyY7rCGTdYB30g67paEAxxe9V9ko0brdsuTb0',3,'2026-03-13 09:23:37','2026-03-13 09:23:37'),(84,NULL,'RDmxyY7rCGTdYB30g67paEAxxe9V9ko0brdsuTb0',4,'2026-03-13 09:23:38','2026-03-13 09:23:38'),(85,NULL,'RDmxyY7rCGTdYB30g67paEAxxe9V9ko0brdsuTb0',5,'2026-03-13 09:23:40','2026-03-13 09:23:40'),(86,NULL,'RDmxyY7rCGTdYB30g67paEAxxe9V9ko0brdsuTb0',2,'2026-03-13 09:23:43','2026-03-13 09:23:43'),(87,NULL,'I9rdCTiUN4opFmZOQbZlddf3t2wbNOc14IhbonLy',10,'2026-03-15 09:59:11','2026-03-15 09:59:11'),(88,NULL,'I9rdCTiUN4opFmZOQbZlddf3t2wbNOc14IhbonLy',9,'2026-03-15 09:59:23','2026-03-15 09:59:23'),(93,NULL,'15Hi6EOyRLKOh6rt9TgTPbaQrB2P0WiabGnsnZcB',1,'2026-03-27 04:31:08','2026-03-27 04:31:08'),(97,NULL,'EyGMqyKCZC5SsVLfHhYowlq7GYsqwd0FZGMmaasQ',4,'2026-04-01 14:57:09','2026-04-01 14:57:09'),(98,NULL,'EyGMqyKCZC5SsVLfHhYowlq7GYsqwd0FZGMmaasQ',3,'2026-04-01 14:57:17','2026-04-01 14:57:17'),(99,NULL,'EyGMqyKCZC5SsVLfHhYowlq7GYsqwd0FZGMmaasQ',8,'2026-04-01 14:57:24','2026-04-01 14:57:24'),(101,NULL,'EyGMqyKCZC5SsVLfHhYowlq7GYsqwd0FZGMmaasQ',9,'2026-04-02 09:19:18','2026-04-02 09:19:18'),(103,NULL,'EyGMqyKCZC5SsVLfHhYowlq7GYsqwd0FZGMmaasQ',5,'2026-04-02 10:32:30','2026-04-02 10:32:30'),(104,NULL,'EyGMqyKCZC5SsVLfHhYowlq7GYsqwd0FZGMmaasQ',7,'2026-04-02 10:42:37','2026-04-02 10:42:37');
/*!40000 ALTER TABLE `product_comparisons` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_galleries`
--

DROP TABLE IF EXISTS `product_galleries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `product_galleries` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_galleries`
--

LOCK TABLES `product_galleries` WRITE;
/*!40000 ALTER TABLE `product_galleries` DISABLE KEYS */;
/*!40000 ALTER TABLE `product_galleries` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_questions`
--

DROP TABLE IF EXISTS `product_questions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `product_questions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `question` text NOT NULL,
  `answer` text DEFAULT NULL,
  `status` tinyint(3) unsigned NOT NULL DEFAULT 0 COMMENT '0=pending,1=answered,2=hidden',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `product_questions_product_id_index` (`product_id`),
  KEY `product_questions_user_id_index` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_questions`
--

LOCK TABLES `product_questions` WRITE;
/*!40000 ALTER TABLE `product_questions` DISABLE KEYS */;
/*!40000 ALTER TABLE `product_questions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_variants`
--

DROP TABLE IF EXISTS `product_variants`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `product_variants` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint(20) unsigned NOT NULL,
  `sku` varchar(100) NOT NULL,
  `attributes` text NOT NULL COMMENT 'JSON: {"size": "L", "color": "Red"}',
  `price` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `discount` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `discount_type` tinyint(4) NOT NULL DEFAULT 1 COMMENT '1=fixed, 2=percent',
  `quantity` int(11) NOT NULL DEFAULT 0,
  `image` varchar(255) DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_product_id` (`product_id`),
  KEY `idx_status` (`status`),
  KEY `idx_quantity` (`quantity`),
  KEY `idx_sku` (`sku`),
  CONSTRAINT `fk_variant_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_variants`
--

LOCK TABLES `product_variants` WRITE;
/*!40000 ALTER TABLE `product_variants` DISABLE KEYS */;
/*!40000 ALTER TABLE `product_variants` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `products` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `category_id` int(10) unsigned NOT NULL DEFAULT 0,
  `subcategory_id` int(10) unsigned NOT NULL DEFAULT 0,
  `brand_id` int(10) unsigned NOT NULL DEFAULT 0,
  `seller_id` bigint(20) unsigned DEFAULT NULL COMMENT 'Null if product belongs to Admin',
  `name` varchar(255) DEFAULT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `product_sku` varchar(40) DEFAULT NULL,
  `price` decimal(28,8) unsigned NOT NULL DEFAULT 0.00000000,
  `original_price` decimal(28,8) DEFAULT NULL,
  `quantity` int(10) unsigned NOT NULL DEFAULT 0,
  `low_stock_alert` int(10) unsigned DEFAULT NULL COMMENT 'Alert when stock falls below this',
  `warehouse_location` varchar(255) DEFAULT NULL,
  `shipping_weight` decimal(10,2) DEFAULT NULL COMMENT 'kg',
  `shipping_class` varchar(100) DEFAULT NULL,
  `delivery_time` varchar(100) DEFAULT NULL COMMENT 'e.g. 2-5 days',
  `delivery_type` varchar(20) NOT NULL DEFAULT 'free' COMMENT 'free|paid',
  `delivery_charge` decimal(12,2) NOT NULL DEFAULT 0.00,
  `has_variants` tinyint(4) NOT NULL DEFAULT 0,
  `variant_attributes` text DEFAULT NULL COMMENT 'JSON array of attribute IDs',
  `avg_rate` decimal(5,2) unsigned NOT NULL DEFAULT 0.00,
  `discount` decimal(5,2) unsigned NOT NULL DEFAULT 0.00,
  `discount_type` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT 'Fixed : 1, Percent : 2',
  `profit_margin` decimal(10,2) DEFAULT NULL COMMENT 'Percentage or fixed margin',
  `digital_item` tinyint(1) NOT NULL DEFAULT 0,
  `cod_disabled` tinyint(1) NOT NULL DEFAULT 0,
  `file_type` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'File:1, Link:2',
  `link` varchar(255) DEFAULT NULL,
  `file` varchar(255) DEFAULT NULL,
  `hot_deals` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `featured_product` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `today_deals` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `status` tinyint(1) unsigned NOT NULL DEFAULT 1,
  `product_type` varchar(30) DEFAULT 'general' COMMENT 'clothing, general',
  `fabric_type` varchar(100) DEFAULT NULL,
  `material` varchar(255) DEFAULT NULL,
  `season` varchar(50) DEFAULT NULL COMMENT 'spring, summer, fall, winter, all',
  `color_variants` text DEFAULT NULL COMMENT 'JSON array of color names',
  `source_url` varchar(500) DEFAULT NULL COMMENT 'Import source URL',
  `target_gender` varchar(20) DEFAULT NULL COMMENT 'male, female, unisex',
  `target_age_min` tinyint(3) unsigned DEFAULT NULL,
  `target_age_max` tinyint(3) unsigned DEFAULT NULL,
  `sale_count` int(10) unsigned NOT NULL DEFAULT 0,
  `summary` text DEFAULT NULL,
  `key_features` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_description` text DEFAULT NULL,
  `meta_keywords` text DEFAULT NULL COMMENT 'JSON array or comma-separated',
  `features` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `gallery` text NOT NULL,
  `video` varchar(255) DEFAULT NULL COMMENT 'Short product video',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `trending_now` tinyint(3) unsigned NOT NULL DEFAULT 0 COMMENT '1 = show in Trending Now section',
  `home_section_override` varchar(32) DEFAULT NULL,
  `home_section_rank` int(10) unsigned NOT NULL DEFAULT 0,
  `home_exclude_from_auto` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `products_slug_unique` (`slug`),
  KEY `idx_products_category_id` (`category_id`),
  KEY `idx_products_subcategory_id` (`subcategory_id`),
  KEY `idx_products_brand_id` (`brand_id`),
  KEY `idx_products_status` (`status`),
  KEY `idx_products_hot_deals` (`hot_deals`),
  KEY `idx_products_product_sku` (`product_sku`),
  KEY `products_category_id_index` (`category_id`),
  KEY `products_brand_id_index` (`brand_id`),
  KEY `products_status_index` (`status`),
  KEY `products_subcategory_id_index` (`subcategory_id`),
  KEY `products_created_at_index` (`created_at`),
  KEY `products_quantity_index` (`quantity`),
  KEY `products_price_index` (`price`),
  KEY `products_status_hot_deals_index` (`status`,`hot_deals`),
  KEY `products_status_featured_index` (`status`,`featured_product`),
  KEY `products_status_created_at_index` (`status`,`created_at`),
  KEY `products_status_sale_count_index` (`status`,`sale_count`),
  KEY `products_status_trending_now_index` (`status`,`trending_now`),
  KEY `products_featured_product_index` (`featured_product`),
  KEY `products_hot_deals_index` (`hot_deals`),
  KEY `products_today_deals_index` (`today_deals`),
  KEY `products_trending_now_index` (`trending_now`),
  KEY `products_sale_count_index` (`sale_count`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES (1,1,1,1,NULL,'RIAZUL ISLAM SHOJOL','riazul-islam-1','IPH15PRO-256BL',100.00000000,NULL,500,NULL,NULL,NULL,NULL,NULL,'free',0.00,0,NULL,0.00,0.00,2,NULL,0,0,0,NULL,NULL,0,0,1,1,'general',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,'kkkmk','jghruh','khkkmmkmk',NULL,NULL,'[\"RIAZUL\",\"ISLAM\",\"SHOJOL\",\"kkkmk\"]',NULL,'69a5cd7b28f141772473723.jpg','[\"69a5cd7b29c681772473723.jpg\"]',NULL,'2025-10-10 11:52:37','2026-03-17 22:04:34',1,NULL,0,0),(2,1,1,1,NULL,'WinTerSMM','wintersmm-2','8878787',100.00000000,NULL,1000,NULL,NULL,NULL,NULL,NULL,'free',0.00,0,NULL,0.00,50.00,2,NULL,0,0,0,NULL,NULL,0,0,0,1,'general',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,'test',NULL,'test',NULL,NULL,NULL,NULL,'69b05568ee4e71773163880.png','[\"69b05569007571773163881.png\"]',NULL,'2025-10-16 03:36:06','2026-03-18 03:00:53',0,NULL,0,0),(3,1,1,1,NULL,'Cricket Jersey || Affordable Custom Cricket Jersey With Sublimation Printing','cricket-jersey-3','12',4099.00000000,NULL,1200,NULL,NULL,NULL,NULL,NULL,'free',0.00,0,NULL,0.00,5.00,1,NULL,0,0,0,NULL,NULL,0,0,0,1,'general',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,'Affordable Custom Cricket Jersey With Sublimation Printing',NULL,'<h2>Affordable Custom Cricket Jersey With Sublimation Printing</h2>',NULL,NULL,NULL,NULL,'69b055417e0c51773163841.png','[\"69b0554184a851773163841.png\",\"69b0554185b271773163841.jpg\"]',NULL,'2025-11-14 04:15:48','2026-03-18 03:00:53',0,NULL,0,0),(4,1,1,1,NULL,'স্ক্রিনশট দিয়েছি  Affordable Custom Cricket Jersey With Sublimation Printing','skrnsht-dzch-4','12',4500.00000000,NULL,1200,NULL,NULL,NULL,NULL,NULL,'free',0.00,0,NULL,0.00,5.00,2,NULL,0,0,0,NULL,NULL,0,0,0,1,'general',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,'Affordable Custom Cricket Jersey With Sublimation Printing',NULL,'Affordable Custom Cricket Jersey With Sublimation Printing',NULL,NULL,NULL,NULL,'69b055206c0dc1773163808.png','[\"69b05520720ea1773163808.png\"]',NULL,'2025-11-14 05:25:34','2026-03-18 03:00:54',0,NULL,0,0),(5,1,1,1,NULL,'Affordable Custom Cricket Jersey With Sublimation Printing','affordable-custom-5','Mdopu',6000.00000000,NULL,1200,NULL,NULL,NULL,NULL,NULL,'free',0.00,0,NULL,0.00,5.00,1,NULL,0,0,0,NULL,NULL,0,0,0,1,'general',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,'Affordable Custom Cricket Jersey With Sublimation Printing opu','Affordable Custom Cricket opu mia','Affordable Custom Cricket Jersey With Sublimation Printing&nbsp;&nbsp;<span id=\"docs-internal-guid-bb8c50f2-7fff-3a79-7aa1-748c846fda0f\"><p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:0pt;\"><span style=\"font-size: 11pt; font-family: Arial, sans-serif; color: rgb(0, 0, 0); background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space-collapse: preserve;\">একটি পূর্ণাঙ্গ নির্দেশনা দিয়েছি নির্দেশনা অনুযায়ী স্ক্রিনশট দেওয়া হয়েছে স্ক্রিনশটে ভালোভাবে এনালাইসিস করুন এবং আমি যে নির্দেশনা দিয়েছে সে নির্দেশনা এনালাইসিস করে সবকিছু যুক্ত করুন এবং আপডেট করুন এবং এই ইনফরমেশন বা স্ক্রিনশট দেওয়া হয়েছে যে লিংকে যেমন: </span><a href=\"http://localhost/staylbd/product/details/affordable-custom-cricket-jersey-with-sublimation-printing/5\"><span style=\"font-size: 11pt; font-family: Arial, sans-serif; color: rgb(17, 85, 204); background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; text-decoration-line: underline; text-decoration-skip-ink: none; vertical-align: baseline; white-space-collapse: preserve;\">http://localhost/staylbd/product/details/affordable-custom-cricket-jersey-with-sublimation-printing/5</span></a><span style=\"font-size: 11pt; font-family: Arial, sans-serif; color: rgb(0, 0, 0); background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space-collapse: preserve;\"> এই লিংক হচ্ছে একটি প্রোডাক্ট লিংক প্রোডাক্ট লিংকে প্রফেশনালি কমার্স ওয়েবসাইটে যে সমস্ত ইনফরমেশন দেখা যায় সেই অনুযায়ী একটি নির্দেশনা দেওয়া হয়েছে ভালোভাবে এনালাইসিস করে সব কিছু আপডেট করুন প্রয়োজনে টার্মিনাল ব্যবহার করুন যতক্ষণ না সবকিছু আপডেট হবে ততক্ষণ আপডেট করতে থাকবেন এবং অত্যন্ত প্রফেশনাল ভাবে সবকিছু মেয়েদের ফোন করবেন যেন সবকিছু ঠিক থাকে কোন কিছু বিশেষ করে css&nbsp; ভেঙে না যায় এবং আমি স্ক্রিনশট দিয়েছি স্ক্রিনশটে যে যতটুকু জায়গা দেখা যাচ্ছে এই জায়গার ভেতরেই সমস্ত ফিচার থাকবে সেই ভাবে যুক্ত করবেন কোন প্রকার পেজ বড় করা যাবে না বা ছোট করা যাবে না এবং পেজের বাহিরেও ফিটার যুক্ত করা যাবে না আমি যে স্ক্রিনশট দিয়েছি স্ক্রিনশট এর ভিতরে যতটুকু জায়গা দেখা যাচ্ছে এর ভেতরে সুন্দরভাবে সাজিয়ে গুছিয়ে স্মার্ট এবং প্রফেশনাল ভাবে সবকিছু যুক্ত করতে হবে&nbsp;</span></p><div><span style=\"font-size: 11pt; font-family: Arial, sans-serif; color: rgb(0, 0, 0); background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space-collapse: preserve;\"><br></span></div></span>',NULL,NULL,'[\"Affordable\",\"Custom\",\"Cricket\",\"Jersey\",\"With\",\"Sublimation\",\"Printing\"]',NULL,'69b054fda55e21773163773.png','[\"69b054fdab31c1773163773.png\"]',NULL,'2025-11-14 05:28:57','2026-03-18 03:00:54',0,NULL,0,0),(6,3,2,2,NULL,'স্ক্রিনশট দিয়েছি ভালোভাবে এনালাইসিস করে দেখুন সবকিছু ঠিক রয়েছে কিনা এবং প্রফেশনাল ভাবে সবকিছু দেখাচ্ছে কিনা','skrnsht-dzch-6','IPH15PRO-256BL',500.00000000,NULL,6,NULL,NULL,NULL,NULL,NULL,'free',0.00,0,NULL,0.00,50.00,2,NULL,0,0,0,NULL,NULL,0,0,1,1,'general',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,'লগ ইন পেজ এবং রেজিস্ট্রেশন পেজ আপডেট করার পরে পূর্বের যারা একাউন্ট করেছিল বা ইউজার একাউন্ট তৈরি করেছিল বা রেজিস্ট্রেশন করেছিল সেই সমস্ত অ্যাকাউন্ট লগইন করা যাচ্ছে না যেমন:  জিমেইল: Opumia@gmail.com   পাসওয়ার্ড: \r\n0987654321  ইউজার অ্যাকাউন্ট লগইন করা যাচ্ছে না সমস্যা গুলো দেখুন এবং আরো প্রফেশনাল এবং আরো অপটিমাইজ করে লগইন পেজ এবং রেজিস্ট্রেশন পেজ আপডেট করুন কোন কিছু পরিবর্তন করা দরকার নাই শুধুমাত্র প্রফেশনাল এবং টেকনিক্যালি এবং সমস্ত দিক থেকে অপটিমাইজেশন করুন','লগ ইন পেজ এবং রেজিস্ট্রেশন পেজ আপডেট করার পরে পূর্বের যারা একাউন্ট করেছিল বা ইউজার একাউন্ট তৈরি করেছিল বা রেজিস্ট্রেশন করেছিল সেই সমস্ত অ্যাকাউন্ট লগইন করা যাচ্ছে না যেমন:  জিমেইল: Opumia@gmail.com   পাসওয়ার্ড: \r\n0987654321  ইউজার অ্যাকাউন্ট লগইন করা যাচ্ছে না সমস্যা গুলো দেখুন এবং আরো প্রফেশনাল এবং আরো অপটিমাইজ করে লগইন পেজ এবং রেজিস্ট্রেশন পেজ আপডেট করুন কোন কিছু পরিবর্তন করা দরকার নাই শুধুমাত্র প্রফেশনাল এবং টেকনিক্যালি এবং সমস্ত দিক থেকে অপটিমাইজেশন করুন','<span id=\"docs-internal-guid-9053f15d-7fff-8de0-2e9b-a68e1cd9fd8c\"><p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:0pt;\"><span style=\"font-size: 11pt; font-family: Arial, sans-serif; color: rgb(0, 0, 0); background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space-collapse: preserve;\">লগ ইন পেজ এবং রেজিস্ট্রেশন পেজ আপডেট করার পরে পূর্বের যারা একাউন্ট করেছিল বা ইউজার একাউন্ট তৈরি করেছিল বা রেজিস্ট্রেশন করেছিল সেই সমস্ত অ্যাকাউন্ট লগইন করা যাচ্ছে না যেমন:&nbsp; জিমেইল: </span><a href=\"mailto:Opumia@gmail.com\"><span style=\"font-size: 11pt; font-family: Arial, sans-serif; color: rgb(17, 85, 204); background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; text-decoration-line: underline; text-decoration-skip-ink: none; vertical-align: baseline; white-space-collapse: preserve;\">Opumia@gmail.com</span></a><span style=\"font-size: 11pt; font-family: Arial, sans-serif; color: rgb(0, 0, 0); background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space-collapse: preserve;\"> &nbsp; পাসওয়ার্ড:&nbsp;</span></p><p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:0pt;\"><span style=\"font-size: 11pt; font-family: Arial, sans-serif; color: rgb(0, 0, 0); background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space-collapse: preserve;\">0987654321&nbsp; ইউজার অ্যাকাউন্ট লগইন করা যাচ্ছে না সমস্যা গুলো দেখুন এবং আরো প্রফেশনাল এবং আরো অপটিমাইজ করে লগইন পেজ এবং রেজিস্ট্রেশন পেজ আপডেট করুন কোন কিছু পরিবর্তন করা দরকার নাই শুধুমাত্র প্রফেশনাল এবং টেকনিক্যালি এবং সমস্ত দিক থেকে অপটিমাইজেশন করুন&nbsp;</span></p><div><span style=\"font-size: 11pt; font-family: Arial, sans-serif; color: rgb(0, 0, 0); background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space-collapse: preserve;\"><br></span></div></span>',NULL,NULL,NULL,NULL,'69b021db50f4e1773150683.png','[\"69b021db54be11773150683.png\",\"69b021db560e21773150683.jpg\"]',NULL,'2026-03-10 07:51:23','2026-03-17 22:35:39',1,NULL,0,0),(7,3,3,2,NULL,'T-shirt, টি-শার্ট','tshirt-tsrt-7','IPH15PRO-256BL',500.00000000,NULL,100,NULL,NULL,NULL,NULL,NULL,'free',0.00,0,NULL,0.00,50.00,1,NULL,0,0,0,NULL,NULL,0,0,0,1,'general',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,'লগ ইন পেজ এবং রেজিস্ট্রেশন পেজ আপডেট করার পরে পূর্বের যারা একাউন্ট করেছিল বা ইউজার একাউন্ট তৈরি করেছিল বা রেজিস্ট্রেশন করেছিল সেই সমস্ত অ্যাকাউন্ট লগইন করা যাচ্ছে না যেমন:  জিমেইল: Opumia@gmail.com   পাসওয়ার্ড: \r\n0987654321  ইউজার অ্যাকাউন্ট লগইন করা যাচ্ছে না সমস্যা গুলো দেখুন এবং আরো প্রফেশনাল এবং আরো অপটিমাইজ করে লগইন পেজ এবং রেজিস্ট্রেশন পেজ আপডেট করুন কোন কিছু পরিবর্তন করা দরকার নাই শুধুমাত্র প্রফেশনাল এবং টেকনিক্যালি এবং সমস্ত দিক থেকে অপটিমাইজেশন করুন','লগ ইন পেজ এবং রেজিস্ট্রেশন পেজ আপডেট করার পরে পূর্বের যারা একাউন্ট করেছিল বা ইউজার একাউন্ট তৈরি করেছিল বা রেজিস্ট্রেশন করেছিল সেই সমস্ত অ্যাকাউন্ট লগইন করা যাচ্ছে না যেমন:  জিমেইল: Opumia@gmail.com   পাসওয়ার্ড: \r\n0987654321  ইউজার অ্যাকাউন্ট লগইন করা যাচ্ছে না সমস্যা গুলো দেখুন এবং আরো প্রফেশনাল এবং আরো অপটিমাইজ করে লগইন পেজ এবং রেজিস্ট্রেশন পেজ আপডেট করুন কোন কিছু পরিবর্তন করা দরকার নাই শুধুমাত্র প্রফেশনাল এবং টেকনিক্যালি এবং সমস্ত দিক থেকে অপটিমাইজেশন করুন','<span id=\"docs-internal-guid-9053f15d-7fff-8de0-2e9b-a68e1cd9fd8c\"><p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:0pt;\"><span style=\"font-size: 11pt; font-family: Arial, sans-serif; color: rgb(0, 0, 0); background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space-collapse: preserve;\">লগ ইন পেজ এবং রেজিস্ট্রেশন পেজ আপডেট করার পরে পূর্বের যারা একাউন্ট করেছিল বা ইউজার একাউন্ট তৈরি করেছিল বা রেজিস্ট্রেশন করেছিল সেই সমস্ত অ্যাকাউন্ট লগইন করা যাচ্ছে না যেমন:&nbsp; জিমেইল: </span><a href=\"mailto:Opumia@gmail.com\"><span style=\"font-size: 11pt; font-family: Arial, sans-serif; color: rgb(17, 85, 204); background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; text-decoration-line: underline; text-decoration-skip-ink: none; vertical-align: baseline; white-space-collapse: preserve;\">Opumia@gmail.com</span></a><span style=\"font-size: 11pt; font-family: Arial, sans-serif; color: rgb(0, 0, 0); background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space-collapse: preserve;\"> &nbsp; পাসওয়ার্ড:&nbsp;</span></p><p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:0pt;\"><span style=\"font-size: 11pt; font-family: Arial, sans-serif; color: rgb(0, 0, 0); background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space-collapse: preserve;\">0987654321&nbsp; ইউজার অ্যাকাউন্ট লগইন করা যাচ্ছে না সমস্যা গুলো দেখুন এবং আরো প্রফেশনাল এবং আরো অপটিমাইজ করে লগইন পেজ এবং রেজিস্ট্রেশন পেজ আপডেট করুন কোন কিছু পরিবর্তন করা দরকার নাই শুধুমাত্র প্রফেশনাল এবং টেকনিক্যালি এবং সমস্ত দিক থেকে অপটিমাইজেশন করুন&nbsp;</span></p><div><span style=\"font-size: 11pt; font-family: Arial, sans-serif; color: rgb(0, 0, 0); background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space-collapse: preserve;\"><br></span></div></span>',NULL,NULL,'[\"shirt\",\"Opumia\",\"gmail\",\"com\",\"0987654321\"]',NULL,'69b0222139ffb1773150753.png','[\"69b022213d1131773150753.png\",\"69b022213e0911773150753.jpg\"]',NULL,'2026-03-10 07:52:33','2026-03-18 03:00:54',0,NULL,0,0),(8,3,3,2,NULL,'T-shirt, টি-শার্ট','tshirt-tsrt-8','IPH15PRO-256BL',500.00000000,NULL,10,NULL,NULL,NULL,NULL,NULL,'free',0.00,0,NULL,0.00,60.00,1,NULL,0,0,0,NULL,NULL,0,0,0,1,'general',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,'http://localhost/staylbd/  এই লিংকে ইউজার পেজে হেডার সেকশনে একটি বাটন রয়েছে যেমন: compare  এই বাটনে চারটি প্রোডাক্টের বেশি অ্যাড করা যাচ্ছে না সমস্যাগুলোর দূরত্ব অডিট করে ফিক্সড করুন','http://localhost/staylbd/  এই লিংকে ইউজার পেজে হেডার সেকশনে একটি বাটন রয়েছে যেমন: compare  এই বাটনে চারটি প্রোডাক্টের বেশি অ্যাড করা যাচ্ছে না সমস্যাগুলোর দূরত্ব অডিট করে ফিক্সড করুন','<span id=\"docs-internal-guid-24c3dbea-7fff-358d-4b88-449dbb5ea9d7\"><a href=\"http://localhost/staylbd/product/details/riazul-islam-shojol/1\"><span style=\"font-size: 11pt; font-family: Arial, sans-serif; color: rgb(17, 85, 204); background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; text-decoration-line: underline; text-decoration-skip-ink: none; vertical-align: baseline; white-space-collapse: preserve;\">http://localhost/staylbd/</span></a><span style=\"font-size: 11pt; font-family: Arial, sans-serif; color: rgb(0, 0, 0); background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space-collapse: preserve;\">&nbsp; এই লিংকে ইউজার পেজে হেডার সেকশনে একটি বাটন রয়েছে যেমন: compare&nbsp; এই বাটনে চারটি প্রোডাক্টের বেশি অ্যাড করা যাচ্ছে না সমস্যাগুলোর দূরত্ব অডিট করে ফিক্সড করুন </span></span>',NULL,NULL,'[\"shirt\",\"http\",\"localhost\",\"staylbd\",\"compare\"]',NULL,'69b02379aeb431773151097.png','[\"69b02379b52411773151097.png\",\"69b02379b62071773151097.jpg\"]',NULL,'2026-03-10 07:58:17','2026-03-18 03:00:54',0,NULL,0,0),(9,1,1,2,NULL,'T-shirt, টি-শার্ট','tshirt-tsrt-9','IPH15PRO-256BL',500.00000000,NULL,1,NULL,NULL,NULL,NULL,NULL,'free',0.00,0,NULL,0.00,50.00,1,NULL,0,0,0,NULL,NULL,0,0,0,1,'general',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,'http://localhost/staylbd/  এই লিংকে ইউজার পেজে হেডার সেকশনে একটি বাটন রয়েছে যেমন: compare  এই বাটনে চারটি প্রোডাক্টের বেশি অ্যাড করা যাচ্ছে না সমস্যাগুলোর দূরত্ব অডিট করে ফিক্সড করুন','http://localhost/staylbd/  এই লিংকে ইউজার পেজে হেডার সেকশনে একটি বাটন রয়েছে যেমন: compare  এই বাটনে চারটি প্রোডাক্টের বেশি অ্যাড করা যাচ্ছে না সমস্যাগুলোর দূরত্ব অডিট করে ফিক্সড করুন','<span id=\"docs-internal-guid-24c3dbea-7fff-358d-4b88-449dbb5ea9d7\"><a href=\"http://localhost/staylbd/product/details/riazul-islam-shojol/1\"><span style=\"font-size: 11pt; font-family: Arial, sans-serif; color: rgb(17, 85, 204); background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; text-decoration-line: underline; text-decoration-skip-ink: none; vertical-align: baseline; white-space-collapse: preserve;\">http://localhost/staylbd/</span></a><span style=\"font-size: 11pt; font-family: Arial, sans-serif; color: rgb(0, 0, 0); background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space-collapse: preserve;\">&nbsp; এই লিংকে ইউজার পেজে হেডার সেকশনে একটি বাটন রয়েছে যেমন: compare&nbsp; এই বাটনে চারটি প্রোডাক্টের বেশি অ্যাড করা যাচ্ছে না সমস্যাগুলোর দূরত্ব অডিট করে ফিক্সড করুন </span></span>',NULL,NULL,NULL,NULL,'69b026df72d591773151967.png','[\"69b026df78c691773151967.png\",\"69b026df79e641773151967.png\"]',NULL,'2026-03-10 08:12:47','2026-03-18 03:00:53',0,NULL,0,0),(10,3,3,2,NULL,'T-shirt, টি-শার্ট','tshirt-tsrt-10','IPH15PRO-256BLBFN',600.00000000,NULL,1,NULL,NULL,NULL,NULL,NULL,'free',0.00,0,NULL,0.00,60.00,1,NULL,0,0,0,NULL,NULL,0,0,0,1,'general',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,'http://localhost/staylbd/sajaladminopu/product/create2 এই লিংকে যে ফিচার রয়েছে তারও আপডেট করতে হবে এবং এখান থেকে কোন পোস্ট করা যাচ্ছে না এবং আমি বলেছি এই লিংকে যত ফিচার রয়েছে সব ফিচারগুলো একটি পেজে স্থাপন করুন এবং সহজে যেন এবং পৃথিবীর যেকোনো প্রোডাক্ট যেন সহজেই আপলোড করতে পারি একটি পেজ থেকেই সেই ভাবে আপডেট করুন এবং যেকোনো ওয়েবসাইটের  প্রোডাক্ট  রিসেল যেন করতে পারি সেভাবে আপডেট করুন','http://localhost/staylbd/sajaladminopu/product/create2 এই লিংকে যে ফিচার রয়েছে তারও আপডেট করতে হবে এবং এখান থেকে কোন পোস্ট করা যাচ্ছে না এবং আমি বলেছি এই লিংকে যত ফিচার রয়েছে সব ফিচারগুলো একটি পেজে স্থাপন করুন এবং সহজে যেন এবং পৃথিবীর যেকোনো প্রোডাক্ট যেন সহজেই আপলোড করতে পারি একটি পেজ থেকেই সেই ভাবে আপডেট করুন এবং যেকোনো ওয়েবসাইটের  প্রোডাক্ট  রিসেল যেন করতে পারি সেভাবে আপডেট করুন','<span id=\"docs-internal-guid-06a14c61-7fff-5bfb-1339-a2b82b557f57\"><p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:0pt;\"><a href=\"http://localhost/staylbd/sajaladminopu/product/create2\"><span style=\"font-size: 11pt; font-family: Arial, sans-serif; color: rgb(17, 85, 204); background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; text-decoration-line: underline; text-decoration-skip-ink: none; vertical-align: baseline; white-space-collapse: preserve;\">http://localhost/staylbd/sajaladminopu/product/create2</span></a><span style=\"font-size: 11pt; font-family: Arial, sans-serif; color: rgb(0, 0, 0); background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space-collapse: preserve;\"> এই লিংকে যে ফিচার রয়েছে তারও আপডেট করতে হবে এবং এখান থেকে কোন পোস্ট করা যাচ্ছে না এবং আমি বলেছি এই লিংকে যত ফিচার রয়েছে সব ফিচারগুলো একটি পেজে স্থাপন করুন এবং সহজে যেন এবং পৃথিবীর যেকোনো প্রোডাক্ট যেন সহজেই আপলোড করতে পারি একটি পেজ থেকেই সেই ভাবে আপডেট করুন এবং যেকোনো ওয়েবসাইটের&nbsp; প্রোডাক্ট&nbsp; রিসেল যেন করতে পারি সেভাবে আপডেট করুন&nbsp;</span></p><div><span style=\"font-size: 11pt; font-family: Arial, sans-serif; color: rgb(0, 0, 0); background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space-collapse: preserve;\"><br></span></div></span>',NULL,NULL,NULL,NULL,'69b02961eab081773152609.png','[\"69b02961f11b31773152609.png\",\"69b02961f32b51773152609.jpg\",\"69b02962002eb1773152610.png\"]',NULL,'2026-03-10 08:23:30','2026-03-17 21:19:33',0,NULL,0,0);
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `revenue_expenses`
--

DROP TABLE IF EXISTS `revenue_expenses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `revenue_expenses` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `expense_date` date NOT NULL,
  `amount` decimal(14,2) NOT NULL,
  `category` varchar(100) DEFAULT NULL,
  `note` varchar(500) DEFAULT NULL,
  `added_by_admin_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `revenue_expenses_expense_date_index` (`expense_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `revenue_expenses`
--

LOCK TABLES `revenue_expenses` WRITE;
/*!40000 ALTER TABLE `revenue_expenses` DISABLE KEYS */;
/*!40000 ALTER TABLE `revenue_expenses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reviews`
--

DROP TABLE IF EXISTS `reviews`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reviews` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `product_id` int(10) unsigned NOT NULL DEFAULT 0,
  `title` varchar(255) DEFAULT NULL,
  `stars` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `review_comment` text DEFAULT NULL,
  `is_verified_purchase` tinyint(1) NOT NULL DEFAULT 0,
  `is_approved` tinyint(1) NOT NULL DEFAULT 1,
  `helpful_count` int(10) unsigned NOT NULL DEFAULT 0,
  `images` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`images`)),
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `is_private` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `reviews_product_id_index` (`product_id`),
  KEY `reviews_user_id_index` (`user_id`),
  KEY `reviews_created_at_index` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reviews`
--

LOCK TABLES `reviews` WRITE;
/*!40000 ALTER TABLE `reviews` DISABLE KEYS */;
/*!40000 ALTER TABLE `reviews` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role_permissions`
--

DROP TABLE IF EXISTS `role_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `role_permissions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `role` varchar(50) NOT NULL,
  `permission_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `role_permissions_role_permission_id_unique` (`role`,`permission_id`),
  KEY `role_permissions_permission_id_foreign` (`permission_id`),
  CONSTRAINT `role_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_permissions`
--

LOCK TABLES `role_permissions` WRITE;
/*!40000 ALTER TABLE `role_permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `role_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `security_audit_logs`
--

DROP TABLE IF EXISTS `security_audit_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `security_audit_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(100) NOT NULL,
  `old_value` text DEFAULT NULL,
  `new_value` text DEFAULT NULL,
  `admin_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(500) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `security_audit_logs_setting_key_index` (`setting_key`),
  KEY `security_audit_logs_admin_id_index` (`admin_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `security_audit_logs`
--

LOCK TABLES `security_audit_logs` WRITE;
/*!40000 ALTER TABLE `security_audit_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `security_audit_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `security_events`
--

DROP TABLE IF EXISTS `security_events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `security_events` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `event_type` varchar(64) NOT NULL,
  `severity` varchar(16) NOT NULL DEFAULT 'medium',
  `ip_address` varchar(45) DEFAULT NULL,
  `admin_id` bigint(20) unsigned DEFAULT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `route` varchar(255) DEFAULT NULL,
  `user_agent` varchar(500) DEFAULT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`payload`)),
  `message` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `security_events_event_type_index` (`event_type`),
  KEY `security_events_ip_address_index` (`ip_address`),
  KEY `security_events_admin_id_index` (`admin_id`),
  KEY `security_events_user_id_index` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `security_events`
--

LOCK TABLES `security_events` WRITE;
/*!40000 ALTER TABLE `security_events` DISABLE KEYS */;
INSERT INTO `security_events` VALUES (1,'admin_2fa_enabled','low','::1',1,NULL,'sajaladminopu/2fa/setup','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36',NULL,NULL,'2026-02-26 23:52:15','2026-02-26 23:52:15'),(2,'admin_2fa_failed','medium','::1',1,NULL,'sajaladminopu/2fa/verify','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36',NULL,NULL,'2026-03-06 06:04:04','2026-03-06 06:04:04'),(3,'admin_2fa_failed','medium','::1',1,NULL,'sajaladminopu/2fa/verify','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,NULL,'2026-04-01 06:32:11','2026-04-01 06:32:11'),(4,'admin_2fa_failed','medium','::1',1,NULL,'sajaladminopu/2fa/verify','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,NULL,'2026-04-01 06:32:14','2026-04-01 06:32:14'),(5,'admin_2fa_failed','medium','::1',1,NULL,'sajaladminopu/2fa/verify','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,NULL,'2026-04-01 06:32:28','2026-04-01 06:32:28'),(6,'admin_2fa_failed','medium','::1',1,NULL,'sajaladminopu/2fa/verify','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,NULL,'2026-04-01 06:33:28','2026-04-01 06:33:28'),(7,'admin_2fa_enabled','low','::1',1,NULL,'sajaladminopu/2fa/setup','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,NULL,'2026-04-01 06:35:26','2026-04-01 06:35:26');
/*!40000 ALTER TABLE `security_events` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `security_settings`
--

DROP TABLE IF EXISTS `security_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `security_settings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(100) NOT NULL,
  `value` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `security_settings_key_unique` (`key`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `security_settings`
--

LOCK TABLES `security_settings` WRITE;
/*!40000 ALTER TABLE `security_settings` DISABLE KEYS */;
INSERT INTO `security_settings` VALUES (1,'ip_whitelist_enabled','0','2026-03-02 08:51:40','2026-03-02 08:51:40'),(2,'admin_login_captcha','1','2026-03-02 08:51:40','2026-03-02 08:51:40');
/*!40000 ALTER TABLE `security_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `seeder_audit_logs`
--

DROP TABLE IF EXISTS `seeder_audit_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `seeder_audit_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `seeder_class` varchar(255) NOT NULL,
  `action` varchar(50) NOT NULL DEFAULT 'run',
  `message` text DEFAULT NULL,
  `environment` varchar(50) DEFAULT NULL,
  `run_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `seeder_audit_logs_seeder_class_index` (`seeder_class`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `seeder_audit_logs`
--

LOCK TABLES `seeder_audit_logs` WRITE;
/*!40000 ALTER TABLE `seeder_audit_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `seeder_audit_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sellers`
--

DROP TABLE IF EXISTS `sellers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sellers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `shop_name` varchar(255) NOT NULL,
  `owner_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(40) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `address` text DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `banner` varchar(255) DEFAULT NULL,
  `balance` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `commission_percentage` decimal(5,2) NOT NULL DEFAULT 10.00,
  `status` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0: Pending, 1: Active, 2: Banned',
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sellers_email_unique` (`email`),
  UNIQUE KEY `sellers_phone_unique` (`phone`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sellers`
--

LOCK TABLES `sellers` WRITE;
/*!40000 ALTER TABLE `sellers` DISABLE KEYS */;
/*!40000 ALTER TABLE `sellers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shipping_methods`
--

DROP TABLE IF EXISTS `shipping_methods`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shipping_methods` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `shipping_zone_id` bigint(20) unsigned DEFAULT NULL,
  `name` varchar(40) DEFAULT NULL,
  `price` decimal(28,8) DEFAULT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `base_price` decimal(18,2) NOT NULL DEFAULT 0.00,
  `price_per_kg` decimal(18,2) DEFAULT NULL,
  `estimated_days` varchar(50) DEFAULT NULL,
  `courier_name` varchar(100) DEFAULT NULL,
  `is_express` tinyint(1) NOT NULL DEFAULT 0,
  `weight_limit_kg` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `shipping_methods_shipping_zone_id_foreign` (`shipping_zone_id`),
  CONSTRAINT `shipping_methods_shipping_zone_id_foreign` FOREIGN KEY (`shipping_zone_id`) REFERENCES `shipping_zones` (`id`) ON DELETE SET NULL,
  CONSTRAINT `shipping_methods_zone_foreign` FOREIGN KEY (`shipping_zone_id`) REFERENCES `shipping_zones` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shipping_methods`
--

LOCK TABLES `shipping_methods` WRITE;
/*!40000 ALTER TABLE `shipping_methods` DISABLE KEYS */;
INSERT INTO `shipping_methods` VALUES (1,NULL,'Dhaka',0.00000000,1,'2025-10-10 11:21:22','2025-10-11 19:55:33',0.00,NULL,NULL,NULL,0,NULL),(2,1,'Inside Dhaka',60.00000000,1,'2026-05-04 17:54:02','2026-05-04 17:54:02',60.00,NULL,'2-3 Days',NULL,0,NULL),(3,2,'Outside Dhaka',120.00000000,1,'2026-05-04 17:54:02','2026-05-04 17:54:02',120.00,NULL,'3-5 Days',NULL,0,NULL),(4,3,'Remote Area',150.00000000,1,'2026-05-04 17:54:02','2026-05-04 17:54:02',150.00,NULL,'5-7 Days',NULL,0,NULL),(5,4,'International Standard',1200.00000000,1,'2026-05-04 17:54:02','2026-05-04 17:54:02',1200.00,NULL,'7-15 Days',NULL,0,NULL);
/*!40000 ALTER TABLE `shipping_methods` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shipping_rules`
--

DROP TABLE IF EXISTS `shipping_rules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shipping_rules` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `free_shipping_min_amount` decimal(18,2) DEFAULT NULL,
  `cod_extra_charge` decimal(18,2) NOT NULL DEFAULT 0.00,
  `express_extra_charge` decimal(18,2) NOT NULL DEFAULT 0.00,
  `international_enabled` tinyint(1) NOT NULL DEFAULT 1,
  `header_notice_text` varchar(255) NOT NULL DEFAULT 'Cash on Delivery available nationwide',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shipping_rules`
--

LOCK TABLES `shipping_rules` WRITE;
/*!40000 ALTER TABLE `shipping_rules` DISABLE KEYS */;
INSERT INTO `shipping_rules` VALUES (1,5000.00,0.00,50.00,1,'Cash on Delivery available nationwide','2026-02-27 05:35:18','2026-02-27 05:35:18');
/*!40000 ALTER TABLE `shipping_rules` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shipping_zone_areas`
--

DROP TABLE IF EXISTS `shipping_zone_areas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shipping_zone_areas` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `shipping_zone_id` bigint(20) unsigned NOT NULL,
  `area_name` varchar(100) NOT NULL,
  `district_names` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`district_names`)),
  `shipping_price` decimal(18,2) DEFAULT NULL,
  `free_shipping` tinyint(1) NOT NULL DEFAULT 0,
  `status` tinyint(3) unsigned NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `shipping_zone_areas_zone_id_index` (`shipping_zone_id`),
  CONSTRAINT `shipping_zone_areas_zone_foreign` FOREIGN KEY (`shipping_zone_id`) REFERENCES `shipping_zones` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shipping_zone_areas`
--

LOCK TABLES `shipping_zone_areas` WRITE;
/*!40000 ALTER TABLE `shipping_zone_areas` DISABLE KEYS */;
/*!40000 ALTER TABLE `shipping_zone_areas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shipping_zone_countries`
--

DROP TABLE IF EXISTS `shipping_zone_countries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shipping_zone_countries` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `shipping_zone_id` bigint(20) unsigned NOT NULL,
  `country_iso` varchar(5) NOT NULL,
  `country_name` varchar(100) DEFAULT NULL,
  `shipping_price` decimal(18,2) DEFAULT NULL,
  `status` tinyint(3) unsigned NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `shipping_zone_countries_zone_iso_unique` (`shipping_zone_id`,`country_iso`),
  KEY `shipping_zone_countries_country_iso_index` (`country_iso`),
  CONSTRAINT `shipping_zone_countries_zone_foreign` FOREIGN KEY (`shipping_zone_id`) REFERENCES `shipping_zones` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shipping_zone_countries`
--

LOCK TABLES `shipping_zone_countries` WRITE;
/*!40000 ALTER TABLE `shipping_zone_countries` DISABLE KEYS */;
/*!40000 ALTER TABLE `shipping_zone_countries` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shipping_zones`
--

DROP TABLE IF EXISTS `shipping_zones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shipping_zones` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `type` varchar(20) NOT NULL DEFAULT 'national',
  `status` tinyint(3) unsigned NOT NULL DEFAULT 1,
  `cod_enabled` tinyint(1) NOT NULL DEFAULT 1,
  `base_price` decimal(18,2) NOT NULL DEFAULT 0.00,
  `estimated_days` varchar(50) DEFAULT NULL,
  `free_shipping` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `shipping_zones_status_type_index` (`status`,`type`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shipping_zones`
--

LOCK TABLES `shipping_zones` WRITE;
/*!40000 ALTER TABLE `shipping_zones` DISABLE KEYS */;
INSERT INTO `shipping_zones` VALUES (1,'Inside Dhaka','national',1,1,60.00,'2-3 Days',0,'2026-05-04 17:54:02','2026-05-04 17:54:02'),(2,'Outside Dhaka','national',1,1,120.00,'3-5 Days',0,'2026-05-04 17:54:02','2026-05-04 17:54:02'),(3,'Remote Area','national',1,1,150.00,'5-7 Days',0,'2026-05-04 17:54:02','2026-05-04 17:54:02'),(4,'International Standard','international',1,1,1200.00,'7-15 Days',0,'2026-05-04 17:54:02','2026-05-04 17:54:02');
/*!40000 ALTER TABLE `shipping_zones` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `subcategories`
--

DROP TABLE IF EXISTS `subcategories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `subcategories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `category_id` int(10) unsigned NOT NULL DEFAULT 0,
  `name` varchar(255) DEFAULT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `subcategories`
--

LOCK TABLES `subcategories` WRITE;
/*!40000 ALTER TABLE `subcategories` DISABLE KEYS */;
INSERT INTO `subcategories` VALUES (1,1,'RIAZUL ISLAM SHOJOL',1,'2025-10-10 11:29:09','2025-10-10 11:29:09'),(2,3,'RIAZUL',1,'2026-03-10 07:48:54','2026-03-10 07:48:54'),(3,3,'T-shirt, টি-শার্ট',1,'2026-03-10 07:49:10','2026-03-10 07:49:10');
/*!40000 ALTER TABLE `subcategories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `subscribers`
--

DROP TABLE IF EXISTS `subscribers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `subscribers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(40) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `subscribers`
--

LOCK TABLES `subscribers` WRITE;
/*!40000 ALTER TABLE `subscribers` DISABLE KEYS */;
INSERT INTO `subscribers` VALUES (1,'uaabraxaszealoe@gmail.com','2025-10-26 10:53:44','2025-10-26 10:53:44');
/*!40000 ALTER TABLE `subscribers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `support_attachments`
--

DROP TABLE IF EXISTS `support_attachments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `support_attachments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `support_message_id` int(10) unsigned DEFAULT NULL,
  `attachment` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `support_attachments`
--

LOCK TABLES `support_attachments` WRITE;
/*!40000 ALTER TABLE `support_attachments` DISABLE KEYS */;
/*!40000 ALTER TABLE `support_attachments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `support_messages`
--

DROP TABLE IF EXISTS `support_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `support_messages` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `support_ticket_id` int(10) unsigned NOT NULL DEFAULT 0,
  `admin_id` int(10) unsigned NOT NULL DEFAULT 0,
  `message` longtext DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `support_messages`
--

LOCK TABLES `support_messages` WRITE;
/*!40000 ALTER TABLE `support_messages` DISABLE KEYS */;
INSERT INTO `support_messages` VALUES (1,1,0,'\"Hi,  \r\nI visited your website online and discovered that it was not showing up in any search results for the majority of keywords related to your company on Google, Yahoo, or Bing.  Do you want more targeted visitors on your website?  We can place your website on Google’s 1st Page. yahoo, AOL, Bing. Etc.  If interested, kindly provide me your name, phone number, and email.   \r\nRegards,   \r\nBrianna Belton\"','2025-10-22 06:39:58','2025-10-22 06:39:58'),(2,2,0,'hi','2026-02-28 12:52:53','2026-02-28 12:52:53'),(3,3,1,'h','2026-03-02 10:06:55','2026-03-02 10:06:55'),(4,3,0,'g','2026-03-02 10:22:49','2026-03-02 10:22:49'),(5,4,1,'gp','2026-03-02 10:23:41','2026-03-02 10:23:41'),(6,4,0,'hi','2026-03-02 10:24:19','2026-03-02 10:24:19');
/*!40000 ALTER TABLE `support_messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `support_tickets`
--

DROP TABLE IF EXISTS `support_tickets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `support_tickets` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT 0,
  `name` varchar(40) DEFAULT NULL,
  `email` varchar(40) DEFAULT NULL,
  `ticket` varchar(40) DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `channel` varchar(32) NOT NULL DEFAULT 'web',
  `channel_reference` varchar(255) DEFAULT NULL,
  `assigned_to` bigint(20) unsigned DEFAULT NULL COMMENT 'Admin/agent assigned',
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0: Open, 1: Answered, 2: Replied, 3: Closed',
  `priority` tinyint(1) NOT NULL DEFAULT 0 COMMENT '1 = Low, 2 = medium, 3 = heigh',
  `last_reply` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `support_tickets_channel_reference_index` (`channel_reference`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `support_tickets`
--

LOCK TABLES `support_tickets` WRITE;
/*!40000 ALTER TABLE `support_tickets` DISABLE KEYS */;
INSERT INTO `support_tickets` VALUES (1,0,'Brianna Belton','briannawebsolution@gmail.com','83616140','briannawebsolution@gmail.com','web',NULL,NULL,0,2,'2025-10-22 12:39:58','2025-10-22 06:39:58','2025-10-22 06:39:58'),(2,8,'bbff fhnfhb','sfdbfhfd@gmail.com','71723456','Live Chat Message','web',NULL,NULL,0,2,'2026-02-28 18:52:53','2026-02-28 12:52:53','2026-02-28 12:52:53'),(3,9,'opu mia','opumia@gmail.com','740630','opu','web',NULL,NULL,2,2,'2026-03-02 16:22:49','2026-03-02 10:06:55','2026-03-02 10:22:49'),(4,9,'opu mia','opumia@gmail.com','224590','opu','web',NULL,NULL,2,2,'2026-03-02 16:24:19','2026-03-02 10:23:41','2026-03-02 10:24:19');
/*!40000 ALTER TABLE `support_tickets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `suspicious_activities`
--

DROP TABLE IF EXISTS `suspicious_activities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `suspicious_activities` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `activity_log_id` bigint(20) unsigned DEFAULT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `reason` varchar(80) NOT NULL,
  `resolved` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `admin_note` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `suspicious_activities_activity_log_id_index` (`activity_log_id`),
  KEY `suspicious_activities_user_id_index` (`user_id`),
  KEY `suspicious_activities_ip_address_index` (`ip_address`),
  KEY `suspicious_activities_reason_index` (`reason`),
  KEY `suspicious_activities_resolved_index` (`resolved`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `suspicious_activities`
--

LOCK TABLES `suspicious_activities` WRITE;
/*!40000 ALTER TABLE `suspicious_activities` DISABLE KEYS */;
/*!40000 ALTER TABLE `suspicious_activities` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `thanas`
--

DROP TABLE IF EXISTS `thanas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `thanas` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `district_id` bigint(20) unsigned NOT NULL,
  `name_en` varchar(150) NOT NULL,
  `name_bn` varchar(150) NOT NULL,
  `postal_code` varchar(20) DEFAULT NULL,
  `sort_order` smallint(5) unsigned DEFAULT 0,
  `status` tinyint(3) unsigned NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `thanas_district_id_index` (`district_id`),
  CONSTRAINT `thanas_ibfk_1` FOREIGN KEY (`district_id`) REFERENCES `districts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `thanas`
--

LOCK TABLES `thanas` WRITE;
/*!40000 ALTER TABLE `thanas` DISABLE KEYS */;
/*!40000 ALTER TABLE `thanas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tokens`
--

DROP TABLE IF EXISTS `tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tokens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tokens`
--

LOCK TABLES `tokens` WRITE;
/*!40000 ALTER TABLE `tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `transactions`
--

DROP TABLE IF EXISTS `transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `transactions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `amount` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `charge` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `post_balance` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `trx_type` varchar(40) DEFAULT NULL,
  `trx` varchar(40) DEFAULT NULL,
  `details` varchar(255) DEFAULT NULL,
  `remark` varchar(40) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `transactions`
--

LOCK TABLES `transactions` WRITE;
/*!40000 ALTER TABLE `transactions` DISABLE KEYS */;
/*!40000 ALTER TABLE `transactions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `trusted_admin_devices`
--

DROP TABLE IF EXISTS `trusted_admin_devices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `trusted_admin_devices` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `admin_id` bigint(20) unsigned NOT NULL,
  `device_hash` varchar(64) NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(500) DEFAULT NULL,
  `verified_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `trusted_admin_devices_admin_id_device_hash_unique` (`admin_id`,`device_hash`),
  KEY `trusted_admin_devices_admin_id_index` (`admin_id`),
  KEY `trusted_admin_devices_device_hash_index` (`device_hash`),
  CONSTRAINT `trusted_admin_devices_admin_id_foreign` FOREIGN KEY (`admin_id`) REFERENCES `admins` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `trusted_admin_devices`
--

LOCK TABLES `trusted_admin_devices` WRITE;
/*!40000 ALTER TABLE `trusted_admin_devices` DISABLE KEYS */;
INSERT INTO `trusted_admin_devices` VALUES (1,1,'80e821cccf53b4e2e36fb6841f4eb2b184cd1b943c9ae01e52099f0c34dda21b','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36','2026-03-13 08:52:23','2026-02-26 23:52:15','2026-03-13 08:52:23'),(2,1,'95bd0b6a94ead7e82e9899f3d08bb2c2fa910792fa561f2c35d5247c88564caa','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-04-01 06:35:26','2026-04-01 06:35:26','2026-04-01 06:35:26');
/*!40000 ALTER TABLE `trusted_admin_devices` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ui_settings`
--

DROP TABLE IF EXISTS `ui_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ui_settings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
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
  `header_top_bg` varchar(30) DEFAULT NULL,
  `theme_template` varchar(50) NOT NULL DEFAULT 'default',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ui_settings`
--

LOCK TABLES `ui_settings` WRITE;
/*!40000 ALTER TABLE `ui_settings` DISABLE KEYS */;
INSERT INTO `ui_settings` VALUES (1,'#ffffff','#1f2937','#0e9f90','#0c8a7d',NULL,NULL,NULL,'#f59e0b','#dc2626',NULL,NULL,NULL,'default','2026-03-12 10:15:29','2026-03-12 10:15:29');
/*!40000 ALTER TABLE `ui_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_activity_logs`
--

DROP TABLE IF EXISTS `user_activity_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_activity_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `session_id` varchar(100) DEFAULT NULL,
  `action_type` varchar(60) NOT NULL,
  `description` varchar(1000) DEFAULT NULL,
  `model_type` varchar(100) DEFAULT NULL COMMENT 'e.g. product, order, deposit',
  `model_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `device` varchar(50) DEFAULT NULL,
  `browser` varchar(100) DEFAULT NULL,
  `os` varchar(100) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `url` varchar(500) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_activity_logs_user_id_index` (`user_id`),
  KEY `user_activity_logs_session_id_index` (`session_id`),
  KEY `user_activity_logs_action_type_index` (`action_type`),
  KEY `user_activity_logs_ip_address_index` (`ip_address`),
  KEY `user_activity_logs_country_index` (`country`),
  KEY `user_activity_logs_created_at_index` (`created_at`),
  KEY `user_activity_logs_action_created_index` (`action_type`,`created_at`),
  KEY `user_activity_logs_user_created_index` (`user_id`,`created_at`),
  KEY `user_activity_logs_ip_created_index` (`ip_address`,`created_at`),
  CONSTRAINT `user_activity_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=777 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_activity_logs`
--

LOCK TABLES `user_activity_logs` WRITE;
/*!40000 ALTER TABLE `user_activity_logs` DISABLE KEYS */;
INSERT INTO `user_activity_logs` VALUES (1,NULL,'2p2frdShwsCqzuKBmXeIdFHI2QRrvwf9QR35ENRx','login_failed','Failed login attempt: digitalzero.com@gmail.com',NULL,NULL,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/user/login','2026-02-26 23:50:28','2026-02-26 23:50:28'),(2,NULL,'2p2frdShwsCqzuKBmXeIdFHI2QRrvwf9QR35ENRx','login_failed','Failed login attempt: digitalzero.com@gmail.com',NULL,NULL,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/user/login','2026-02-26 23:50:34','2026-02-26 23:50:34'),(3,NULL,'2p2frdShwsCqzuKBmXeIdFHI2QRrvwf9QR35ENRx','login_failed','Failed login attempt: bfeyfgy',NULL,NULL,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/user/login','2026-02-26 23:50:48','2026-02-26 23:50:48'),(4,NULL,'Wia1iNxKughwk6TLGJ18FQBFC4jnLSs2DJYfZjRR','login_failed','Failed login attempt: digitalzero.com@gmail.com',NULL,NULL,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/user/login','2026-02-26 23:58:22','2026-02-26 23:58:22'),(5,NULL,'Wia1iNxKughwk6TLGJ18FQBFC4jnLSs2DJYfZjRR','login_failed','Failed login attempt: digitalzero.com@gmail.com',NULL,NULL,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/user/login','2026-02-26 23:58:25','2026-02-26 23:58:25'),(6,NULL,'Wia1iNxKughwk6TLGJ18FQBFC4jnLSs2DJYfZjRR','login_failed','Failed login attempt: fgbrnbb',NULL,NULL,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/user/login','2026-02-27 02:17:06','2026-02-27 02:17:06'),(7,NULL,'Wia1iNxKughwk6TLGJ18FQBFC4jnLSs2DJYfZjRR','login_failed','Failed login attempt: bfeyfgy',NULL,NULL,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/user/login','2026-02-27 02:17:22','2026-02-27 02:17:22'),(8,NULL,'FtNbSqwzWZh0NOcAgb0AR77umxiHslr2HSpvhuzq','login_failed','Failed login attempt: opumiax',NULL,NULL,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/user/login','2026-02-28 11:16:53','2026-02-28 11:16:53'),(9,NULL,'FtNbSqwzWZh0NOcAgb0AR77umxiHslr2HSpvhuzq','login_failed','Failed login attempt: opumiax',NULL,NULL,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/user/login','2026-02-28 11:17:05','2026-02-28 11:17:05'),(10,NULL,'FtNbSqwzWZh0NOcAgb0AR77umxiHslr2HSpvhuzq','login_failed','Failed login attempt: opumiax',NULL,NULL,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/user/login','2026-02-28 11:33:21','2026-02-28 11:33:21'),(11,NULL,'FtNbSqwzWZh0NOcAgb0AR77umxiHslr2HSpvhuzq','login_failed','Failed login attempt: opumiax',NULL,NULL,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/user/login','2026-02-28 11:35:18','2026-02-28 11:35:18'),(12,NULL,'FtNbSqwzWZh0NOcAgb0AR77umxiHslr2HSpvhuzq','login_failed','Failed login attempt: fgbrnbb',NULL,NULL,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/user/login','2026-02-28 11:47:01','2026-02-28 11:47:01'),(13,NULL,'FtNbSqwzWZh0NOcAgb0AR77umxiHslr2HSpvhuzq','login_failed','Failed login attempt: opumiax',NULL,NULL,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/user/login','2026-02-28 11:52:11','2026-02-28 11:52:11'),(14,NULL,'YG3nTDUvr6d3fzikcD4DEfHMDnmb5FH7MSjZcxMt','login_failed','Failed login attempt: opumiax',NULL,NULL,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/user/login','2026-02-28 11:53:16','2026-02-28 11:53:16'),(15,NULL,'NMzPUf0tiqd9iBJ5hYcSAiTk3aPZk8pucQuZeTNW','login_failed','Failed login attempt: opumiax',NULL,NULL,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/user/login','2026-02-28 12:49:43','2026-02-28 12:49:43'),(16,8,'vjVBjJiW1mKkqkRoKbrfF0tHWadROSvosqx1HE0d','registration','New user registered: opumiaxb',NULL,NULL,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/user/register','2026-02-28 12:51:00','2026-02-28 12:51:00'),(17,8,'vjVBjJiW1mKkqkRoKbrfF0tHWadROSvosqx1HE0d','logout','User logged out',NULL,NULL,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/user/logout','2026-02-28 12:51:23','2026-02-28 12:51:23'),(18,8,'AYAgGroA1rckmmXuJGgpwBQpgOMrt3Oyr6TLWgsz','login','User logged in',NULL,NULL,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/user/login','2026-02-28 12:51:27','2026-02-28 12:51:27'),(19,8,'AYAgGroA1rckmmXuJGgpwBQpgOMrt3Oyr6TLWgsz','cart_add','Added to cart: WinTerSMM','product',2,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-02-28 12:52:11','2026-02-28 12:52:11'),(20,8,'AYAgGroA1rckmmXuJGgpwBQpgOMrt3Oyr6TLWgsz','cart_remove','Removed from cart: WinTerSMM','product',2,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/remove','2026-02-28 12:52:13','2026-02-28 12:52:13'),(21,8,'AYAgGroA1rckmmXuJGgpwBQpgOMrt3Oyr6TLWgsz','compare_add','Added to compare: WinTerSMM','product',2,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/compare/add','2026-02-28 12:52:14','2026-02-28 12:52:14'),(22,8,'AYAgGroA1rckmmXuJGgpwBQpgOMrt3Oyr6TLWgsz','wishlist_add','Added to wishlist: WinTerSMM','product',2,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/wish-list/add','2026-02-28 12:52:16','2026-02-28 12:52:16'),(23,8,'AYAgGroA1rckmmXuJGgpwBQpgOMrt3Oyr6TLWgsz','cart_add','Added to cart: WinTerSMM','product',2,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-02-28 12:52:22','2026-02-28 12:52:22'),(24,8,'AYAgGroA1rckmmXuJGgpwBQpgOMrt3Oyr6TLWgsz','product_view','Viewed product: WinTerSMM','product',2,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/wintersmm/2','2026-02-28 12:52:24','2026-02-28 12:52:24'),(25,8,'AYAgGroA1rckmmXuJGgpwBQpgOMrt3Oyr6TLWgsz','cart_add','Added to cart: Cricket Jersey || Affordable Custom Cricket Jersey With Sublimation Printing','product',3,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-02-28 12:52:38','2026-02-28 12:52:38'),(26,8,'AYAgGroA1rckmmXuJGgpwBQpgOMrt3Oyr6TLWgsz','cart_remove','Removed from cart: Cricket Jersey || Affordable Custom Cricket Jersey With Sublimation Printing','product',3,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/remove','2026-02-28 12:52:39','2026-02-28 12:52:39'),(27,8,'AYAgGroA1rckmmXuJGgpwBQpgOMrt3Oyr6TLWgsz','live_chat','Live chat: Live Chat Message',NULL,2,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/contact/panel','2026-02-28 12:52:54','2026-02-28 12:52:54'),(28,8,'AYAgGroA1rckmmXuJGgpwBQpgOMrt3Oyr6TLWgsz','cart_add','Added to cart: WinTerSMM','product',2,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-02-28 12:53:02','2026-02-28 12:53:02'),(29,8,'AYAgGroA1rckmmXuJGgpwBQpgOMrt3Oyr6TLWgsz','order_place','Order placed: N5Q188M7FJUV','order',36,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/user/checkout/order','2026-02-28 12:53:56','2026-02-28 12:53:56'),(30,NULL,'qslF5EU9XfaaR4tlUrukdRuvKmEew472csscjU5I','login_failed','Failed login attempt: opumiaxb',NULL,NULL,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/user/login','2026-03-01 17:11:26','2026-03-01 17:11:26'),(31,NULL,'qslF5EU9XfaaR4tlUrukdRuvKmEew472csscjU5I','login_failed','Failed login attempt: opumiaxb',NULL,NULL,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/user/login','2026-03-01 17:11:39','2026-03-01 17:11:39'),(32,NULL,'qslF5EU9XfaaR4tlUrukdRuvKmEew472csscjU5I','login_failed','Failed login attempt: opumiaxb',NULL,NULL,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/user/login','2026-03-01 17:11:44','2026-03-01 17:11:44'),(33,NULL,'BXF5fNSVRlzutJ4Zya55Reel1QTWpA9lwXZF8bJo','login_failed','Failed login attempt: opumiaxb',NULL,NULL,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/user/login','2026-03-01 17:37:12','2026-03-01 17:37:12'),(34,9,'iI2OmQqqckKTLqoGlQLioL8c2lbBzNTEnQ01jU7k','registration','New user registered: user1772408352cnpogw',NULL,NULL,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/user/register','2026-03-01 17:39:12','2026-03-01 17:39:12'),(35,9,'iI2OmQqqckKTLqoGlQLioL8c2lbBzNTEnQ01jU7k','profile_update','Profile updated',NULL,NULL,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/user/profile-setting','2026-03-01 17:40:47','2026-03-01 17:40:47'),(36,9,'yhPpRZUoBxR4LQZJatuwsX4AzEA8QbwJfB1DYlnP','login','User logged in',NULL,NULL,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/user/login','2026-03-02 05:49:36','2026-03-02 05:49:36'),(37,9,'vLU1NvYT1rcZkNER6QGXM0LSSTpHthSfvsoixXxv','profile_update','Profile updated',NULL,NULL,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/user/profile-setting','2026-03-02 07:15:10','2026-03-02 07:15:10'),(38,9,'vLU1NvYT1rcZkNER6QGXM0LSSTpHthSfvsoixXxv','profile_update','Profile updated',NULL,NULL,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/user/profile-setting','2026-03-02 07:15:18','2026-03-02 07:15:18'),(39,9,'vLU1NvYT1rcZkNER6QGXM0LSSTpHthSfvsoixXxv','profile_update','Profile updated',NULL,NULL,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/user/profile-setting','2026-03-02 07:15:22','2026-03-02 07:15:22'),(40,9,'vLU1NvYT1rcZkNER6QGXM0LSSTpHthSfvsoixXxv','profile_update','Profile updated',NULL,NULL,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/user/profile-setting','2026-03-02 07:15:38','2026-03-02 07:15:38'),(41,9,'vLU1NvYT1rcZkNER6QGXM0LSSTpHthSfvsoixXxv','profile_update','Profile updated',NULL,NULL,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/user/profile-setting','2026-03-02 07:57:57','2026-03-02 07:57:57'),(42,9,'vLU1NvYT1rcZkNER6QGXM0LSSTpHthSfvsoixXxv','profile_update','Profile updated',NULL,NULL,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/user/profile-setting','2026-03-02 07:59:55','2026-03-02 07:59:55'),(43,9,'vLU1NvYT1rcZkNER6QGXM0LSSTpHthSfvsoixXxv','profile_update','Profile updated',NULL,NULL,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/user/profile-setting','2026-03-02 08:19:29','2026-03-02 08:19:29'),(44,9,'vQaxP71xgd5n8Ut7d2iBbqoiCWEqGOgNdaSqFzu3','product_view','Viewed product: RIAZUL ISLAM SHOJOL','product',1,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/riazul-islam-shojol/1','2026-03-02 11:53:18','2026-03-02 11:53:18'),(45,9,'vQaxP71xgd5n8Ut7d2iBbqoiCWEqGOgNdaSqFzu3','cart_add','Added to cart: RIAZUL ISLAM SHOJOL','product',1,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-03-02 12:51:22','2026-03-02 12:51:22'),(46,9,'vQaxP71xgd5n8Ut7d2iBbqoiCWEqGOgNdaSqFzu3','wishlist_add','Added to wishlist: RIAZUL ISLAM SHOJOL','product',1,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/wish-list/add','2026-03-02 12:51:25','2026-03-02 12:51:25'),(47,9,'vQaxP71xgd5n8Ut7d2iBbqoiCWEqGOgNdaSqFzu3','product_view','Viewed product: RIAZUL ISLAM SHOJOL','product',1,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/riazul-islam-shojol/1','2026-03-02 13:05:50','2026-03-02 13:05:50'),(48,9,'vQaxP71xgd5n8Ut7d2iBbqoiCWEqGOgNdaSqFzu3','compare_add','Added to compare: RIAZUL ISLAM SHOJOL','product',1,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/compare/add','2026-03-02 13:06:13','2026-03-02 13:06:13'),(49,9,'vQaxP71xgd5n8Ut7d2iBbqoiCWEqGOgNdaSqFzu3','compare_add','Added to compare: WinTerSMM','product',2,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/compare/add','2026-03-02 13:07:45','2026-03-02 13:07:45'),(50,9,'vQaxP71xgd5n8Ut7d2iBbqoiCWEqGOgNdaSqFzu3','cart_add','Added to cart: WinTerSMM','product',2,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-03-02 13:07:45','2026-03-02 13:07:45'),(51,9,'vQaxP71xgd5n8Ut7d2iBbqoiCWEqGOgNdaSqFzu3','wishlist_add','Added to wishlist: WinTerSMM','product',2,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/wish-list/add','2026-03-02 13:07:46','2026-03-02 13:07:46'),(52,9,'vQaxP71xgd5n8Ut7d2iBbqoiCWEqGOgNdaSqFzu3','cart_add','Added to cart: Cricket Jersey || Affordable Custom Cricket Jersey With Sublimation Printing','product',3,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-03-02 13:07:47','2026-03-02 13:07:47'),(53,9,'vQaxP71xgd5n8Ut7d2iBbqoiCWEqGOgNdaSqFzu3','compare_add','Added to compare: Cricket Jersey || Affordable Custom Cricket Jersey With Sublimation Printing','product',3,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/compare/add','2026-03-02 13:07:48','2026-03-02 13:07:48'),(54,9,'vQaxP71xgd5n8Ut7d2iBbqoiCWEqGOgNdaSqFzu3','wishlist_add','Added to wishlist: Cricket Jersey || Affordable Custom Cricket Jersey With Sublimation Printing','product',3,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/wish-list/add','2026-03-02 13:07:48','2026-03-02 13:07:48'),(55,9,'vQaxP71xgd5n8Ut7d2iBbqoiCWEqGOgNdaSqFzu3','cart_add','Added to cart: Affordable Custom Cricket Jersey With Sublimation Printing','product',4,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-03-02 13:07:50','2026-03-02 13:07:50'),(56,9,'vQaxP71xgd5n8Ut7d2iBbqoiCWEqGOgNdaSqFzu3','compare_add','Added to compare: Affordable Custom Cricket Jersey With Sublimation Printing','product',4,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/compare/add','2026-03-02 13:07:51','2026-03-02 13:07:51'),(57,9,'vQaxP71xgd5n8Ut7d2iBbqoiCWEqGOgNdaSqFzu3','wishlist_add','Added to wishlist: Affordable Custom Cricket Jersey With Sublimation Printing','product',4,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/wish-list/add','2026-03-02 13:07:51','2026-03-02 13:07:51'),(58,9,'vQaxP71xgd5n8Ut7d2iBbqoiCWEqGOgNdaSqFzu3','cart_add','Added to cart: Affordable Custom Cricket Jersey With Sublimation Printing','product',5,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-03-02 13:07:53','2026-03-02 13:07:53'),(59,9,'vQaxP71xgd5n8Ut7d2iBbqoiCWEqGOgNdaSqFzu3','wishlist_add','Added to wishlist: Affordable Custom Cricket Jersey With Sublimation Printing','product',5,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/wish-list/add','2026-03-02 13:07:54','2026-03-02 13:07:54'),(60,NULL,'YYW8aHzTx7L9WKZfnEeWeBBLcG3utzM8wr3O0GCC','cart_add','Added to cart: WinTerSMM','product',2,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-03-02 13:08:39','2026-03-02 13:08:39'),(61,NULL,'YYW8aHzTx7L9WKZfnEeWeBBLcG3utzM8wr3O0GCC','compare_add','Added to compare: WinTerSMM','product',2,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/compare/add','2026-03-02 13:08:40','2026-03-02 13:08:40'),(62,NULL,'YYW8aHzTx7L9WKZfnEeWeBBLcG3utzM8wr3O0GCC','wishlist_add','Added to wishlist: WinTerSMM','product',2,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/wish-list/add','2026-03-02 13:08:41','2026-03-02 13:08:41'),(63,NULL,'YYW8aHzTx7L9WKZfnEeWeBBLcG3utzM8wr3O0GCC','cart_add','Added to cart: Cricket Jersey || Affordable Custom Cricket Jersey With Sublimation Printing','product',3,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-03-02 13:08:45','2026-03-02 13:08:45'),(64,NULL,'YYW8aHzTx7L9WKZfnEeWeBBLcG3utzM8wr3O0GCC','compare_add','Added to compare: Cricket Jersey || Affordable Custom Cricket Jersey With Sublimation Printing','product',3,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/compare/add','2026-03-02 13:08:46','2026-03-02 13:08:46'),(65,NULL,'YYW8aHzTx7L9WKZfnEeWeBBLcG3utzM8wr3O0GCC','wishlist_add','Added to wishlist: Cricket Jersey || Affordable Custom Cricket Jersey With Sublimation Printing','product',3,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/wish-list/add','2026-03-02 13:08:47','2026-03-02 13:08:47'),(66,NULL,'YYW8aHzTx7L9WKZfnEeWeBBLcG3utzM8wr3O0GCC','cart_add','Added to cart: Affordable Custom Cricket Jersey With Sublimation Printing','product',4,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-03-02 13:08:49','2026-03-02 13:08:49'),(67,NULL,'YYW8aHzTx7L9WKZfnEeWeBBLcG3utzM8wr3O0GCC','compare_add','Added to compare: Affordable Custom Cricket Jersey With Sublimation Printing','product',4,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/compare/add','2026-03-02 13:08:49','2026-03-02 13:08:49'),(68,NULL,'YYW8aHzTx7L9WKZfnEeWeBBLcG3utzM8wr3O0GCC','wishlist_add','Added to wishlist: Affordable Custom Cricket Jersey With Sublimation Printing','product',4,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/wish-list/add','2026-03-02 13:08:50','2026-03-02 13:08:50'),(69,NULL,'YYW8aHzTx7L9WKZfnEeWeBBLcG3utzM8wr3O0GCC','cart_add','Added to cart: Affordable Custom Cricket Jersey With Sublimation Printing','product',5,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-03-02 13:08:52','2026-03-02 13:08:52'),(70,NULL,'YYW8aHzTx7L9WKZfnEeWeBBLcG3utzM8wr3O0GCC','compare_add','Added to compare: Affordable Custom Cricket Jersey With Sublimation Printing','product',5,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/compare/add','2026-03-02 13:08:52','2026-03-02 13:08:52'),(71,NULL,'YYW8aHzTx7L9WKZfnEeWeBBLcG3utzM8wr3O0GCC','wishlist_add','Added to wishlist: Affordable Custom Cricket Jersey With Sublimation Printing','product',5,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/wish-list/add','2026-03-02 13:08:53','2026-03-02 13:08:53'),(72,NULL,'R7GLIVt8pOe4VGJX97jK49pOC8rD3006h5ZVwQsY','product_view','Viewed product: RIAZUL ISLAM SHOJOL','product',1,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/riazul-islam-shojol/1','2026-03-03 09:17:34','2026-03-03 09:17:34'),(73,NULL,'R7GLIVt8pOe4VGJX97jK49pOC8rD3006h5ZVwQsY','product_view','Viewed product: Affordable Custom Cricket Jersey With Sublimation Printing','product',4,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/affordable-custom-cricket-jersey-with-sublimation-printing/4','2026-03-03 09:27:28','2026-03-03 09:27:28'),(74,NULL,'R7GLIVt8pOe4VGJX97jK49pOC8rD3006h5ZVwQsY','product_view','Viewed product: Affordable Custom Cricket Jersey With Sublimation Printing','product',4,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/affordable-custom-cricket-jersey-with-sublimation-printing/4','2026-03-03 09:27:28','2026-03-03 09:27:28'),(75,9,'R9ugQGOj67fTwfxI3jBPFL0eBfsJbotjHQfQTYNt','login','User logged in',NULL,NULL,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/user/login','2026-03-04 08:13:48','2026-03-04 08:13:48'),(76,9,'R9ugQGOj67fTwfxI3jBPFL0eBfsJbotjHQfQTYNt','search_text','Search: C',NULL,NULL,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/search/universal','2026-03-04 08:15:31','2026-03-04 08:15:31'),(77,9,'R9ugQGOj67fTwfxI3jBPFL0eBfsJbotjHQfQTYNt','search_text','Search: CO',NULL,NULL,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/search/universal','2026-03-04 08:15:32','2026-03-04 08:15:32'),(78,9,'R9ugQGOj67fTwfxI3jBPFL0eBfsJbotjHQfQTYNt','search_text','Search: COM',NULL,NULL,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/search/universal','2026-03-04 08:15:32','2026-03-04 08:15:32'),(79,9,'R9ugQGOj67fTwfxI3jBPFL0eBfsJbotjHQfQTYNt','search_text','Search: COMP',NULL,NULL,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/search/universal','2026-03-04 08:15:45','2026-03-04 08:15:45'),(80,9,'R9ugQGOj67fTwfxI3jBPFL0eBfsJbotjHQfQTYNt','search_text','Search: COMPA',NULL,NULL,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/search/universal','2026-03-04 08:15:45','2026-03-04 08:15:45'),(81,9,'R9ugQGOj67fTwfxI3jBPFL0eBfsJbotjHQfQTYNt','search_text','Search: COMPAR',NULL,NULL,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/search/universal','2026-03-04 08:15:47','2026-03-04 08:15:47'),(82,9,'R9ugQGOj67fTwfxI3jBPFL0eBfsJbotjHQfQTYNt','search_text','Search: COMPARE',NULL,NULL,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/search/universal','2026-03-04 08:16:05','2026-03-04 08:16:05'),(83,9,'R9ugQGOj67fTwfxI3jBPFL0eBfsJbotjHQfQTYNt','search_text','Search: COMPARE',NULL,NULL,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/search/universal','2026-03-04 08:16:41','2026-03-04 08:16:41'),(84,9,'R9ugQGOj67fTwfxI3jBPFL0eBfsJbotjHQfQTYNt','search_text','Search: COMPARE',NULL,NULL,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/search/universal','2026-03-04 08:16:49','2026-03-04 08:16:49'),(85,9,'R9ugQGOj67fTwfxI3jBPFL0eBfsJbotjHQfQTYNt','search_text','Search: COMPARE',NULL,NULL,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/search/universal','2026-03-04 08:16:56','2026-03-04 08:16:56'),(86,9,'R9ugQGOj67fTwfxI3jBPFL0eBfsJbotjHQfQTYNt','search_text','Search: COMPARE',NULL,NULL,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/search/universal','2026-03-04 08:26:03','2026-03-04 08:26:03'),(87,9,'R9ugQGOj67fTwfxI3jBPFL0eBfsJbotjHQfQTYNt','cart_remove','Removed from cart: Cricket Jersey || Affordable Custom Cricket Jersey With Sublimation Printing','product',3,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/remove','2026-03-04 08:32:35','2026-03-04 08:32:35'),(88,9,'R9ugQGOj67fTwfxI3jBPFL0eBfsJbotjHQfQTYNt','cart_add','Added to cart: Cricket Jersey || Affordable Custom Cricket Jersey With Sublimation Printing','product',3,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-03-04 08:32:37','2026-03-04 08:32:37'),(89,9,'R9ugQGOj67fTwfxI3jBPFL0eBfsJbotjHQfQTYNt','wishlist_remove','Removed from wishlist','product',3,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/wish-list/remove?product_id=3','2026-03-04 08:35:11','2026-03-04 08:35:11'),(90,9,'R9ugQGOj67fTwfxI3jBPFL0eBfsJbotjHQfQTYNt','wishlist_add','Added to wishlist: Cricket Jersey || Affordable Custom Cricket Jersey With Sublimation Printing','product',3,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/wish-list/add','2026-03-04 08:35:27','2026-03-04 08:35:27'),(91,9,'R9ugQGOj67fTwfxI3jBPFL0eBfsJbotjHQfQTYNt','cart_add','Added to cart: Cricket Jersey || Affordable Custom Cricket Jersey With Sublimation Printing','product',3,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-03-04 08:37:35','2026-03-04 08:37:35'),(92,9,'R9ugQGOj67fTwfxI3jBPFL0eBfsJbotjHQfQTYNt','cart_remove','Removed from cart: Cricket Jersey || Affordable Custom Cricket Jersey With Sublimation Printing','product',3,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/remove','2026-03-04 08:37:38','2026-03-04 08:37:38'),(93,9,'R9ugQGOj67fTwfxI3jBPFL0eBfsJbotjHQfQTYNt','cart_add','Added to cart: Cricket Jersey || Affordable Custom Cricket Jersey With Sublimation Printing','product',3,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-03-04 08:37:40','2026-03-04 08:37:40'),(94,9,'R9ugQGOj67fTwfxI3jBPFL0eBfsJbotjHQfQTYNt','cart_remove','Removed from cart: Affordable Custom Cricket Jersey With Sublimation Printing','product',4,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/remove','2026-03-04 08:40:12','2026-03-04 08:40:12'),(95,9,'R9ugQGOj67fTwfxI3jBPFL0eBfsJbotjHQfQTYNt','cart_add','Added to cart: Affordable Custom Cricket Jersey With Sublimation Printing','product',4,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-03-04 08:40:13','2026-03-04 08:40:13'),(96,NULL,'fulyamFOMSkmsxD4AU0bgNQaMwWBl4h2ZCXG19zG','cart_add','Added to cart: WinTerSMM','product',2,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-03-04 08:41:04','2026-03-04 08:41:04'),(97,NULL,'fulyamFOMSkmsxD4AU0bgNQaMwWBl4h2ZCXG19zG','cart_remove','Removed from cart: WinTerSMM','product',2,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/remove','2026-03-04 08:41:06','2026-03-04 08:41:06'),(98,NULL,'fulyamFOMSkmsxD4AU0bgNQaMwWBl4h2ZCXG19zG','compare_add','Added to compare: WinTerSMM','product',2,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/compare/add','2026-03-04 08:41:07','2026-03-04 08:41:07'),(99,NULL,'fulyamFOMSkmsxD4AU0bgNQaMwWBl4h2ZCXG19zG','compare_remove','Removed from compare','product',2,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/compare/remove','2026-03-04 08:41:08','2026-03-04 08:41:08'),(100,NULL,'fulyamFOMSkmsxD4AU0bgNQaMwWBl4h2ZCXG19zG','wishlist_add','Added to wishlist: WinTerSMM','product',2,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/wish-list/add','2026-03-04 08:41:09','2026-03-04 08:41:09'),(101,NULL,'fulyamFOMSkmsxD4AU0bgNQaMwWBl4h2ZCXG19zG','wishlist_remove','Removed from wishlist','product',2,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/wish-list/remove?product_id=2','2026-03-04 08:41:12','2026-03-04 08:41:12'),(102,NULL,'fulyamFOMSkmsxD4AU0bgNQaMwWBl4h2ZCXG19zG','cart_add','Added to cart: Cricket Jersey || Affordable Custom Cricket Jersey With Sublimation Printing','product',3,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-03-04 08:48:19','2026-03-04 08:48:19'),(103,NULL,'JBvj0UU68tZdA8SS6NNcWFqKfjIifonHkK10IJfe','cart_add','Added to cart: Cricket Jersey || Affordable Custom Cricket Jersey With Sublimation Printing','product',3,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-03-04 08:48:58','2026-03-04 08:48:58'),(104,NULL,'JBvj0UU68tZdA8SS6NNcWFqKfjIifonHkK10IJfe','compare_add','Added to compare: Cricket Jersey || Affordable Custom Cricket Jersey With Sublimation Printing','product',3,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/compare/add','2026-03-04 08:49:00','2026-03-04 08:49:00'),(105,NULL,'loItlxC5pLxMxu7SZ103FrHUP684xnkIMMZ14lxv','cart_add','Added to cart: WinTerSMM','product',2,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-03-04 08:49:28','2026-03-04 08:49:28'),(106,NULL,'loItlxC5pLxMxu7SZ103FrHUP684xnkIMMZ14lxv','compare_add','Added to compare: WinTerSMM','product',2,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/compare/add','2026-03-04 08:49:30','2026-03-04 08:49:30'),(107,NULL,'biO1PXijpnI7Clttms62OtaIXxf6gISzno3z1XN9','cart_add','Added to cart: Cricket Jersey || Affordable Custom Cricket Jersey With Sublimation Printing','product',3,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-03-04 08:53:50','2026-03-04 08:53:50'),(108,NULL,'biO1PXijpnI7Clttms62OtaIXxf6gISzno3z1XN9','compare_add','Added to compare: Cricket Jersey || Affordable Custom Cricket Jersey With Sublimation Printing','product',3,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/compare/add','2026-03-04 08:53:52','2026-03-04 08:53:52'),(109,NULL,'biO1PXijpnI7Clttms62OtaIXxf6gISzno3z1XN9','wishlist_add','Added to wishlist: Cricket Jersey || Affordable Custom Cricket Jersey With Sublimation Printing','product',3,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/wish-list/add','2026-03-04 08:53:53','2026-03-04 08:53:53'),(110,NULL,'biO1PXijpnI7Clttms62OtaIXxf6gISzno3z1XN9','wishlist_remove','Removed from wishlist','product',3,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/wish-list/remove?product_id=3','2026-03-04 08:53:56','2026-03-04 08:53:56'),(111,NULL,'biO1PXijpnI7Clttms62OtaIXxf6gISzno3z1XN9','cart_remove','Removed from cart: Cricket Jersey || Affordable Custom Cricket Jersey With Sublimation Printing','product',3,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/remove','2026-03-04 08:54:02','2026-03-04 08:54:02'),(112,NULL,'biO1PXijpnI7Clttms62OtaIXxf6gISzno3z1XN9','compare_remove','Removed from compare','product',3,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/compare/remove','2026-03-04 08:54:03','2026-03-04 08:54:03'),(113,NULL,'HpaC2hxogXql26lWVlHXaHWrVJh9b4qkiwTXDFA7','cart_add','Added to cart: RIAZUL ISLAM SHOJOL','product',1,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-03-04 09:00:48','2026-03-04 09:00:48'),(114,NULL,'HpaC2hxogXql26lWVlHXaHWrVJh9b4qkiwTXDFA7','cart_add','Added to cart: RIAZUL ISLAM SHOJOL','product',1,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-03-04 09:00:48','2026-03-04 09:00:48'),(115,NULL,'HpaC2hxogXql26lWVlHXaHWrVJh9b4qkiwTXDFA7','compare_add','Added to compare: RIAZUL ISLAM SHOJOL','product',1,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/compare/add','2026-03-04 09:00:50','2026-03-04 09:00:50'),(116,NULL,'HpaC2hxogXql26lWVlHXaHWrVJh9b4qkiwTXDFA7','wishlist_add','Added to wishlist: RIAZUL ISLAM SHOJOL','product',1,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/wish-list/add','2026-03-04 09:00:53','2026-03-04 09:00:53'),(117,NULL,'HpaC2hxogXql26lWVlHXaHWrVJh9b4qkiwTXDFA7','compare_remove','Removed from compare','product',1,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/compare/remove','2026-03-04 09:00:55','2026-03-04 09:00:55'),(118,NULL,'HpaC2hxogXql26lWVlHXaHWrVJh9b4qkiwTXDFA7','cart_remove','Removed from cart: RIAZUL ISLAM SHOJOL','product',1,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/remove','2026-03-04 09:00:56','2026-03-04 09:00:56'),(119,NULL,'HpaC2hxogXql26lWVlHXaHWrVJh9b4qkiwTXDFA7','wishlist_remove','Removed from wishlist','product',1,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/wish-list/remove?product_id=1','2026-03-04 09:00:57','2026-03-04 09:00:57'),(120,NULL,'HpaC2hxogXql26lWVlHXaHWrVJh9b4qkiwTXDFA7','cart_add','Added to cart: WinTerSMM','product',2,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-03-04 09:00:58','2026-03-04 09:00:58'),(121,NULL,'HpaC2hxogXql26lWVlHXaHWrVJh9b4qkiwTXDFA7','compare_add','Added to compare: WinTerSMM','product',2,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/compare/add','2026-03-04 09:00:58','2026-03-04 09:00:58'),(122,NULL,'HpaC2hxogXql26lWVlHXaHWrVJh9b4qkiwTXDFA7','wishlist_add','Added to wishlist: WinTerSMM','product',2,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/wish-list/add','2026-03-04 09:00:59','2026-03-04 09:00:59'),(123,NULL,'SxM7aiOshBlBWJ0prgCtB7V4bX686boqg7hoWeqJ','cart_add','Added to cart: WinTerSMM','product',2,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-03-04 09:12:55','2026-03-04 09:12:55'),(124,NULL,'SxM7aiOshBlBWJ0prgCtB7V4bX686boqg7hoWeqJ','cart_remove','Removed from cart: WinTerSMM','product',2,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/remove','2026-03-04 09:12:57','2026-03-04 09:12:57'),(125,NULL,'SxM7aiOshBlBWJ0prgCtB7V4bX686boqg7hoWeqJ','compare_add','Added to compare: WinTerSMM','product',2,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/compare/add','2026-03-04 09:12:58','2026-03-04 09:12:58'),(126,NULL,'SxM7aiOshBlBWJ0prgCtB7V4bX686boqg7hoWeqJ','wishlist_add','Added to wishlist: WinTerSMM','product',2,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/wish-list/add','2026-03-04 09:13:00','2026-03-04 09:13:00'),(127,NULL,'SxM7aiOshBlBWJ0prgCtB7V4bX686boqg7hoWeqJ','cart_add','Added to cart: WinTerSMM','product',2,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-03-04 09:13:10','2026-03-04 09:13:10'),(128,NULL,'SxM7aiOshBlBWJ0prgCtB7V4bX686boqg7hoWeqJ','cart_add','Added to cart: Affordable Custom Cricket Jersey With Sublimation Printing','product',4,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-03-04 09:13:12','2026-03-04 09:13:12'),(129,NULL,'SxM7aiOshBlBWJ0prgCtB7V4bX686boqg7hoWeqJ','cart_add','Added to cart: RIAZUL ISLAM SHOJOL','product',1,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-03-04 09:13:14','2026-03-04 09:13:14'),(130,NULL,'SxM7aiOshBlBWJ0prgCtB7V4bX686boqg7hoWeqJ','cart_remove','Removed from cart: RIAZUL ISLAM SHOJOL','product',1,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/remove','2026-03-04 09:13:17','2026-03-04 09:13:17'),(131,NULL,'SxM7aiOshBlBWJ0prgCtB7V4bX686boqg7hoWeqJ','compare_add','Added to compare: RIAZUL ISLAM SHOJOL','product',1,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/compare/add','2026-03-04 09:13:19','2026-03-04 09:13:19'),(132,NULL,'SxM7aiOshBlBWJ0prgCtB7V4bX686boqg7hoWeqJ','cart_add','Added to cart: RIAZUL ISLAM SHOJOL','product',1,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-03-04 09:13:35','2026-03-04 09:13:35'),(133,NULL,'SxM7aiOshBlBWJ0prgCtB7V4bX686boqg7hoWeqJ','wishlist_add','Added to wishlist: RIAZUL ISLAM SHOJOL','product',1,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/wish-list/add','2026-03-04 09:13:37','2026-03-04 09:13:37'),(134,NULL,'29zjNouOJR99UahGQy3CXjEPNjgkfmm169w9xGmA','wishlist_remove','Removed from wishlist','product',2,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/wish-list/remove?product_id=2','2026-03-04 09:29:52','2026-03-04 09:29:52'),(135,NULL,'29zjNouOJR99UahGQy3CXjEPNjgkfmm169w9xGmA','cart_add','Added to cart: WinTerSMM','product',2,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-03-04 09:30:50','2026-03-04 09:30:50'),(136,NULL,'29zjNouOJR99UahGQy3CXjEPNjgkfmm169w9xGmA','compare_add','Added to compare: WinTerSMM','product',2,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/compare/add','2026-03-04 09:30:52','2026-03-04 09:30:52'),(137,NULL,'29zjNouOJR99UahGQy3CXjEPNjgkfmm169w9xGmA','wishlist_add','Added to wishlist: WinTerSMM','product',2,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/wish-list/add','2026-03-04 09:30:53','2026-03-04 09:30:53'),(138,NULL,'NWAxjd8iUULH1tpjCWp8B23JfUv85meYiTuLoVBN','cart_add','Added to cart: WinTerSMM','product',2,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-03-04 09:39:42','2026-03-04 09:39:42'),(139,NULL,'NWAxjd8iUULH1tpjCWp8B23JfUv85meYiTuLoVBN','compare_add','Added to compare: WinTerSMM','product',2,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/compare/add','2026-03-04 09:39:44','2026-03-04 09:39:44'),(140,NULL,'NWAxjd8iUULH1tpjCWp8B23JfUv85meYiTuLoVBN','wishlist_add','Added to wishlist: WinTerSMM','product',2,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/wish-list/add','2026-03-04 09:39:45','2026-03-04 09:39:45'),(141,NULL,'NWAxjd8iUULH1tpjCWp8B23JfUv85meYiTuLoVBN','cart_remove','Removed from cart: WinTerSMM','product',2,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/remove','2026-03-04 09:41:46','2026-03-04 09:41:46'),(142,NULL,'NWAxjd8iUULH1tpjCWp8B23JfUv85meYiTuLoVBN','cart_remove','Removed from cart: WinTerSMM','product',2,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/remove','2026-03-04 09:41:46','2026-03-04 09:41:46'),(143,NULL,'NWAxjd8iUULH1tpjCWp8B23JfUv85meYiTuLoVBN','wishlist_remove','Removed from wishlist','product',2,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/wish-list/remove?product_id=2','2026-03-04 09:41:47','2026-03-04 09:41:47'),(144,NULL,'35BYrrbes49ujtTwzytkgNuItsjCOtBk7dWlPUke','wishlist_add','Added to wishlist: WinTerSMM','product',2,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/wish-list/add','2026-03-04 09:42:39','2026-03-04 09:42:39'),(145,NULL,'35BYrrbes49ujtTwzytkgNuItsjCOtBk7dWlPUke','compare_add','Added to compare: WinTerSMM','product',2,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/compare/add','2026-03-04 09:42:40','2026-03-04 09:42:40'),(146,NULL,'35BYrrbes49ujtTwzytkgNuItsjCOtBk7dWlPUke','cart_add','Added to cart: WinTerSMM','product',2,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-03-04 09:42:41','2026-03-04 09:42:41'),(147,NULL,'35BYrrbes49ujtTwzytkgNuItsjCOtBk7dWlPUke','cart_add','Added to cart: RIAZUL ISLAM SHOJOL','product',1,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-03-04 09:44:29','2026-03-04 09:44:29'),(148,NULL,'yk5DhLqEJIKHLKprvKYumQXwz995sVsDNdsoDP2J','cart_add','Added to cart: WinTerSMM','product',2,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-03-04 09:47:25','2026-03-04 09:47:25'),(149,NULL,'yk5DhLqEJIKHLKprvKYumQXwz995sVsDNdsoDP2J','compare_add','Added to compare: WinTerSMM','product',2,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/compare/add','2026-03-04 09:47:27','2026-03-04 09:47:27'),(150,NULL,'yk5DhLqEJIKHLKprvKYumQXwz995sVsDNdsoDP2J','wishlist_add','Added to wishlist: WinTerSMM','product',2,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/wish-list/add','2026-03-04 09:47:28','2026-03-04 09:47:28'),(151,NULL,'yk5DhLqEJIKHLKprvKYumQXwz995sVsDNdsoDP2J','wishlist_remove','Removed from wishlist','product',2,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/wish-list/remove?product_id=2','2026-03-04 09:47:30','2026-03-04 09:47:30'),(152,NULL,'pRhK6q0SRJXiekCcWhECS7CafZfOvrQPpNuh9Zb3','cart_add','Added to cart: WinTerSMM','product',2,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-03-04 09:50:43','2026-03-04 09:50:43'),(153,NULL,'pRhK6q0SRJXiekCcWhECS7CafZfOvrQPpNuh9Zb3','compare_add','Added to compare: WinTerSMM','product',2,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/compare/add','2026-03-04 09:50:44','2026-03-04 09:50:44'),(154,NULL,'pRhK6q0SRJXiekCcWhECS7CafZfOvrQPpNuh9Zb3','wishlist_add','Added to wishlist: WinTerSMM','product',2,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/wish-list/add','2026-03-04 09:50:45','2026-03-04 09:50:45'),(155,NULL,'pRhK6q0SRJXiekCcWhECS7CafZfOvrQPpNuh9Zb3','cart_add','Added to cart: RIAZUL ISLAM SHOJOL','product',1,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-03-04 09:50:49','2026-03-04 09:50:49'),(156,NULL,'pRhK6q0SRJXiekCcWhECS7CafZfOvrQPpNuh9Zb3','compare_add','Added to compare: RIAZUL ISLAM SHOJOL','product',1,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/compare/add','2026-03-04 09:50:50','2026-03-04 09:50:50'),(157,NULL,'pRhK6q0SRJXiekCcWhECS7CafZfOvrQPpNuh9Zb3','wishlist_add','Added to wishlist: RIAZUL ISLAM SHOJOL','product',1,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/wish-list/add','2026-03-04 09:50:52','2026-03-04 09:50:52'),(158,NULL,'pRhK6q0SRJXiekCcWhECS7CafZfOvrQPpNuh9Zb3','product_view','Viewed product: RIAZUL ISLAM SHOJOL','product',1,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/riazul-islam-shojol/1','2026-03-04 09:51:13','2026-03-04 09:51:13'),(159,NULL,'pRhK6q0SRJXiekCcWhECS7CafZfOvrQPpNuh9Zb3','cart_remove','Removed from cart: RIAZUL ISLAM SHOJOL','product',1,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/remove','2026-03-04 09:51:19','2026-03-04 09:51:19'),(160,NULL,'pRhK6q0SRJXiekCcWhECS7CafZfOvrQPpNuh9Zb3','wishlist_remove','Removed from wishlist','product',1,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/wish-list/remove?product_id=1','2026-03-04 09:51:21','2026-03-04 09:51:21'),(161,NULL,'pRhK6q0SRJXiekCcWhECS7CafZfOvrQPpNuh9Zb3','compare_add','Added to compare: Affordable Custom Cricket Jersey With Sublimation Printing','product',5,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/compare/add','2026-03-04 09:51:23','2026-03-04 09:51:23'),(162,NULL,'pRhK6q0SRJXiekCcWhECS7CafZfOvrQPpNuh9Zb3','product_view','Viewed product: RIAZUL ISLAM SHOJOL','product',1,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/riazul-islam-shojol/1','2026-03-04 09:51:30','2026-03-04 09:51:30'),(163,NULL,'OQG1x64B73wDsWL349oNARumcclhk7vH16dTG8G2','product_view','Viewed product: RIAZUL ISLAM SHOJOL','product',1,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/riazul-islam-shojol/1','2026-03-04 09:55:04','2026-03-04 09:55:04'),(164,NULL,'OQG1x64B73wDsWL349oNARumcclhk7vH16dTG8G2','cart_add','Added to cart: RIAZUL ISLAM SHOJOL','product',1,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-03-04 09:55:09','2026-03-04 09:55:09'),(165,NULL,'OQG1x64B73wDsWL349oNARumcclhk7vH16dTG8G2','wishlist_add','Added to wishlist: RIAZUL ISLAM SHOJOL','product',1,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/wish-list/add','2026-03-04 09:55:11','2026-03-04 09:55:11'),(166,NULL,'OQG1x64B73wDsWL349oNARumcclhk7vH16dTG8G2','product_view','Viewed product: RIAZUL ISLAM SHOJOL','product',1,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/riazul-islam-shojol/1','2026-03-04 09:55:21','2026-03-04 09:55:21'),(167,NULL,'OQG1x64B73wDsWL349oNARumcclhk7vH16dTG8G2','product_view','Viewed product: WinTerSMM','product',2,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/wintersmm/2','2026-03-04 09:55:42','2026-03-04 09:55:42'),(168,NULL,'OQG1x64B73wDsWL349oNARumcclhk7vH16dTG8G2','cart_add','Added to cart: WinTerSMM','product',2,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-03-04 09:55:46','2026-03-04 09:55:46'),(169,NULL,'OQG1x64B73wDsWL349oNARumcclhk7vH16dTG8G2','product_view','Viewed product: RIAZUL ISLAM SHOJOL','product',1,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/riazul-islam-shojol/1','2026-03-04 09:55:53','2026-03-04 09:55:53'),(170,NULL,'OQG1x64B73wDsWL349oNARumcclhk7vH16dTG8G2','cart_add','Added to cart: RIAZUL ISLAM SHOJOL','product',1,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-03-04 09:55:55','2026-03-04 09:55:55'),(171,NULL,'OQG1x64B73wDsWL349oNARumcclhk7vH16dTG8G2','product_view','Viewed product: RIAZUL ISLAM SHOJOL','product',1,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/riazul-islam-shojol/1','2026-03-04 09:56:00','2026-03-04 09:56:00'),(172,NULL,'OQG1x64B73wDsWL349oNARumcclhk7vH16dTG8G2','wishlist_remove','Removed from wishlist','product',1,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/wish-list/remove?product_id=1','2026-03-04 09:56:06','2026-03-04 09:56:06'),(173,NULL,'OQG1x64B73wDsWL349oNARumcclhk7vH16dTG8G2','cart_remove','Removed from cart: RIAZUL ISLAM SHOJOL','product',1,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/remove','2026-03-04 09:56:07','2026-03-04 09:56:07'),(174,NULL,'OQG1x64B73wDsWL349oNARumcclhk7vH16dTG8G2','cart_remove','Removed from cart: RIAZUL ISLAM SHOJOL','product',1,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/remove','2026-03-04 09:56:07','2026-03-04 09:56:07'),(175,NULL,'t3jSkeKmHXsra372NnnUk2CebwIV4TEKHVQEMj6y','cart_add','Added to cart: RIAZUL ISLAM SHOJOL','product',1,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-03-04 10:04:10','2026-03-04 10:04:10'),(176,NULL,'t3jSkeKmHXsra372NnnUk2CebwIV4TEKHVQEMj6y','compare_add','Added to compare: RIAZUL ISLAM SHOJOL','product',1,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/compare/add','2026-03-04 10:04:12','2026-03-04 10:04:12'),(177,NULL,'t3jSkeKmHXsra372NnnUk2CebwIV4TEKHVQEMj6y','wishlist_add','Added to wishlist: RIAZUL ISLAM SHOJOL','product',1,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/wish-list/add','2026-03-04 10:04:13','2026-03-04 10:04:13'),(178,NULL,'t3jSkeKmHXsra372NnnUk2CebwIV4TEKHVQEMj6y','product_view','Viewed product: RIAZUL ISLAM SHOJOL','product',1,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/riazul-islam-shojol/1','2026-03-04 10:04:15','2026-03-04 10:04:15'),(179,NULL,'t3jSkeKmHXsra372NnnUk2CebwIV4TEKHVQEMj6y','wishlist_remove','Removed from wishlist','product',1,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/wish-list/remove?product_id=1','2026-03-04 10:04:17','2026-03-04 10:04:17'),(180,NULL,'t3jSkeKmHXsra372NnnUk2CebwIV4TEKHVQEMj6y','wishlist_add','Added to wishlist: RIAZUL ISLAM SHOJOL','product',1,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/wish-list/add','2026-03-04 10:04:20','2026-03-04 10:04:20'),(181,NULL,'t3jSkeKmHXsra372NnnUk2CebwIV4TEKHVQEMj6y','cart_remove','Removed from cart: RIAZUL ISLAM SHOJOL','product',1,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/remove','2026-03-04 10:04:22','2026-03-04 10:04:22'),(182,NULL,'t3jSkeKmHXsra372NnnUk2CebwIV4TEKHVQEMj6y','cart_add','Added to cart: WinTerSMM','product',2,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-03-04 10:04:30','2026-03-04 10:04:30'),(183,NULL,'t3jSkeKmHXsra372NnnUk2CebwIV4TEKHVQEMj6y','compare_add','Added to compare: WinTerSMM','product',2,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/compare/add','2026-03-04 10:04:33','2026-03-04 10:04:33'),(184,NULL,'t3jSkeKmHXsra372NnnUk2CebwIV4TEKHVQEMj6y','cart_add','Added to cart: RIAZUL ISLAM SHOJOL','product',1,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-03-04 10:04:39','2026-03-04 10:04:39'),(185,NULL,'t3jSkeKmHXsra372NnnUk2CebwIV4TEKHVQEMj6y','compare_add','Added to compare: Affordable Custom Cricket Jersey With Sublimation Printing','product',4,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/compare/add','2026-03-04 10:04:43','2026-03-04 10:04:43'),(186,NULL,'t3jSkeKmHXsra372NnnUk2CebwIV4TEKHVQEMj6y','cart_add','Added to cart: Affordable Custom Cricket Jersey With Sublimation Printing','product',4,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-03-04 10:04:47','2026-03-04 10:04:47'),(187,NULL,'t3jSkeKmHXsra372NnnUk2CebwIV4TEKHVQEMj6y','wishlist_add','Added to wishlist: Affordable Custom Cricket Jersey With Sublimation Printing','product',4,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/wish-list/add','2026-03-04 10:04:49','2026-03-04 10:04:49'),(188,NULL,'zMrenI8pnMySQUk4HHNVidSdEIkNoPIXPaK5nI9h','cart_add','Added to cart: RIAZUL ISLAM SHOJOL','product',1,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-03-04 10:11:12','2026-03-04 10:11:12'),(189,NULL,'zMrenI8pnMySQUk4HHNVidSdEIkNoPIXPaK5nI9h','wishlist_add','Added to wishlist: RIAZUL ISLAM SHOJOL','product',1,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/wish-list/add','2026-03-04 10:11:14','2026-03-04 10:11:14'),(190,NULL,'zMrenI8pnMySQUk4HHNVidSdEIkNoPIXPaK5nI9h','cart_add','Added to cart: WinTerSMM','product',2,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-03-04 10:11:16','2026-03-04 10:11:16'),(191,NULL,'zMrenI8pnMySQUk4HHNVidSdEIkNoPIXPaK5nI9h','wishlist_remove','Removed from wishlist','product',1,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/wish-list/remove?product_id=1','2026-03-04 10:11:26','2026-03-04 10:11:26'),(192,NULL,'zMrenI8pnMySQUk4HHNVidSdEIkNoPIXPaK5nI9h','cart_remove','Removed from cart: RIAZUL ISLAM SHOJOL','product',1,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/remove','2026-03-04 10:11:28','2026-03-04 10:11:28'),(193,NULL,'zMrenI8pnMySQUk4HHNVidSdEIkNoPIXPaK5nI9h','compare_add','Added to compare: RIAZUL ISLAM SHOJOL','product',1,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/compare/add','2026-03-04 10:11:29','2026-03-04 10:11:29'),(194,NULL,'YE9MiJNZclmEHAUlgxiR7zML6J6XE9VK5AJI1Zed','product_view','Viewed product: RIAZUL ISLAM SHOJOL','product',1,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/riazul-islam-shojol/1','2026-03-04 10:19:01','2026-03-04 10:19:01'),(195,NULL,'YE9MiJNZclmEHAUlgxiR7zML6J6XE9VK5AJI1Zed','cart_add','Added to cart: RIAZUL ISLAM SHOJOL','product',1,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-03-04 10:19:09','2026-03-04 10:19:09'),(196,NULL,'YE9MiJNZclmEHAUlgxiR7zML6J6XE9VK5AJI1Zed','compare_add','Added to compare: RIAZUL ISLAM SHOJOL','product',1,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/compare/add','2026-03-04 10:19:11','2026-03-04 10:19:11'),(197,NULL,'YE9MiJNZclmEHAUlgxiR7zML6J6XE9VK5AJI1Zed','wishlist_add','Added to wishlist: WinTerSMM','product',2,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/wish-list/add','2026-03-04 10:19:12','2026-03-04 10:19:12'),(198,NULL,'YE9MiJNZclmEHAUlgxiR7zML6J6XE9VK5AJI1Zed','compare_add','Added to compare: WinTerSMM','product',2,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/compare/add','2026-03-04 10:19:14','2026-03-04 10:19:14'),(199,NULL,'YE9MiJNZclmEHAUlgxiR7zML6J6XE9VK5AJI1Zed','compare_remove','Removed from compare','product',2,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/compare/remove','2026-03-04 10:19:22','2026-03-04 10:19:22'),(200,NULL,'YE9MiJNZclmEHAUlgxiR7zML6J6XE9VK5AJI1Zed','wishlist_remove','Removed from wishlist','product',2,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/wish-list/remove?product_id=2','2026-03-04 10:19:23','2026-03-04 10:19:23'),(201,NULL,'98I2bjBXIdlMToFy1Uri5OwgoaeB0GFXlkZxW4lr','cart_add','Added to cart: RIAZUL ISLAM SHOJOL','product',1,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-03-04 10:26:52','2026-03-04 10:26:52'),(202,NULL,'98I2bjBXIdlMToFy1Uri5OwgoaeB0GFXlkZxW4lr','compare_add','Added to compare: WinTerSMM','product',2,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/compare/add','2026-03-04 10:26:55','2026-03-04 10:26:55'),(203,NULL,'98I2bjBXIdlMToFy1Uri5OwgoaeB0GFXlkZxW4lr','cart_add','Added to cart: WinTerSMM','product',2,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-03-04 10:27:03','2026-03-04 10:27:03'),(204,NULL,'461eWH3XzhubW6qB5pCu9JtYTlfJpQTc3UovrP3r','wishlist_add','Added to wishlist: RIAZUL ISLAM SHOJOL','product',1,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/wish-list/add','2026-03-04 10:31:12','2026-03-04 10:31:12'),(205,NULL,'461eWH3XzhubW6qB5pCu9JtYTlfJpQTc3UovrP3r','wishlist_remove','Removed from wishlist','product',1,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/wish-list/remove?product_id=1','2026-03-04 10:31:13','2026-03-04 10:31:13'),(206,NULL,'461eWH3XzhubW6qB5pCu9JtYTlfJpQTc3UovrP3r','compare_add','Added to compare: RIAZUL ISLAM SHOJOL','product',1,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/compare/add','2026-03-04 10:31:14','2026-03-04 10:31:14'),(207,NULL,'461eWH3XzhubW6qB5pCu9JtYTlfJpQTc3UovrP3r','compare_add','Added to compare: WinTerSMM','product',2,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/compare/add','2026-03-04 10:31:16','2026-03-04 10:31:16'),(208,NULL,'461eWH3XzhubW6qB5pCu9JtYTlfJpQTc3UovrP3r','wishlist_add','Added to wishlist: Affordable Custom Cricket Jersey With Sublimation Printing','product',5,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/wish-list/add','2026-03-04 10:31:19','2026-03-04 10:31:19'),(209,NULL,'461eWH3XzhubW6qB5pCu9JtYTlfJpQTc3UovrP3r','cart_add','Added to cart: Affordable Custom Cricket Jersey With Sublimation Printing','product',5,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-03-04 10:31:21','2026-03-04 10:31:21'),(210,NULL,'MEuBR4C0r4nZdmG3solMyBOZmV1cVAztTHtidYom','cart_add','Added to cart: RIAZUL ISLAM SHOJOL','product',1,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-03-04 10:37:11','2026-03-04 10:37:11'),(211,NULL,'MEuBR4C0r4nZdmG3solMyBOZmV1cVAztTHtidYom','cart_add','Added to cart: RIAZUL ISLAM SHOJOL','product',1,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-03-04 10:37:11','2026-03-04 10:37:11'),(212,NULL,'MEuBR4C0r4nZdmG3solMyBOZmV1cVAztTHtidYom','compare_add','Added to compare: WinTerSMM','product',2,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/compare/add','2026-03-04 10:37:13','2026-03-04 10:37:13'),(213,NULL,'ZKPQXJvuAti8KIx3HtZmTGmB61xtWniSFV5Mht7n','cart_add','Added to cart: RIAZUL ISLAM SHOJOL','product',1,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-03-04 10:43:18','2026-03-04 10:43:18'),(214,NULL,'ZKPQXJvuAti8KIx3HtZmTGmB61xtWniSFV5Mht7n','cart_add','Added to cart: WinTerSMM','product',2,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-03-04 10:43:20','2026-03-04 10:43:20'),(215,NULL,'ZKPQXJvuAti8KIx3HtZmTGmB61xtWniSFV5Mht7n','cart_add','Added to cart: Cricket Jersey || Affordable Custom Cricket Jersey With Sublimation Printing','product',3,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-03-04 10:43:22','2026-03-04 10:43:22'),(216,NULL,'ZKPQXJvuAti8KIx3HtZmTGmB61xtWniSFV5Mht7n','cart_add','Added to cart: Affordable Custom Cricket Jersey With Sublimation Printing','product',4,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-03-04 10:43:23','2026-03-04 10:43:23'),(217,NULL,'ZKPQXJvuAti8KIx3HtZmTGmB61xtWniSFV5Mht7n','compare_add','Added to compare: Affordable Custom Cricket Jersey With Sublimation Printing','product',4,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/compare/add','2026-03-04 10:43:26','2026-03-04 10:43:26'),(218,NULL,'ZKPQXJvuAti8KIx3HtZmTGmB61xtWniSFV5Mht7n','wishlist_add','Added to wishlist: Affordable Custom Cricket Jersey With Sublimation Printing','product',4,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/wish-list/add','2026-03-04 10:43:27','2026-03-04 10:43:27'),(219,NULL,'ZKPQXJvuAti8KIx3HtZmTGmB61xtWniSFV5Mht7n','compare_add','Added to compare: Affordable Custom Cricket Jersey With Sublimation Printing','product',5,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/compare/add','2026-03-04 10:43:30','2026-03-04 10:43:30'),(220,NULL,'ZKPQXJvuAti8KIx3HtZmTGmB61xtWniSFV5Mht7n','cart_add','Added to cart: Affordable Custom Cricket Jersey With Sublimation Printing','product',5,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-03-04 10:43:30','2026-03-04 10:43:30'),(221,NULL,'ZKPQXJvuAti8KIx3HtZmTGmB61xtWniSFV5Mht7n','wishlist_add','Added to wishlist: Affordable Custom Cricket Jersey With Sublimation Printing','product',5,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/wish-list/add','2026-03-04 10:43:31','2026-03-04 10:43:31'),(222,NULL,'ZKPQXJvuAti8KIx3HtZmTGmB61xtWniSFV5Mht7n','product_view','Viewed product: RIAZUL ISLAM SHOJOL','product',1,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/riazul-islam-shojol/1','2026-03-04 10:43:39','2026-03-04 10:43:39'),(223,NULL,'ZKPQXJvuAti8KIx3HtZmTGmB61xtWniSFV5Mht7n','product_view','Viewed product: RIAZUL ISLAM SHOJOL','product',1,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/riazul-islam-shojol/1','2026-03-04 10:44:00','2026-03-04 10:44:00'),(224,NULL,'fMujLtzZv6kWcLna441DUksrtFkCD75wWKPcuQkO','cart_add','Added to cart: RIAZUL ISLAM SHOJOL','product',1,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-03-04 10:54:29','2026-03-04 10:54:29'),(225,NULL,'fMujLtzZv6kWcLna441DUksrtFkCD75wWKPcuQkO','wishlist_add','Added to wishlist: WinTerSMM','product',2,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/wish-list/add','2026-03-04 10:54:31','2026-03-04 10:54:31'),(226,NULL,'fMujLtzZv6kWcLna441DUksrtFkCD75wWKPcuQkO','compare_add','Added to compare: WinTerSMM','product',2,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/compare/add','2026-03-04 10:54:32','2026-03-04 10:54:32'),(227,NULL,'fMujLtzZv6kWcLna441DUksrtFkCD75wWKPcuQkO','cart_add','Added to cart: WinTerSMM','product',2,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-03-04 10:54:34','2026-03-04 10:54:34'),(228,NULL,'fMujLtzZv6kWcLna441DUksrtFkCD75wWKPcuQkO','cart_add','Added to cart: Cricket Jersey || Affordable Custom Cricket Jersey With Sublimation Printing','product',3,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-03-04 10:54:43','2026-03-04 10:54:43'),(229,9,'ryqy57F22foQ2JOGOTioGKFj2F3yN25SQKBvviJ0','login','User logged in',NULL,NULL,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/user/login','2026-03-06 00:05:32','2026-03-06 00:05:32'),(230,9,'ryqy57F22foQ2JOGOTioGKFj2F3yN25SQKBvviJ0','product_view','Viewed product: RIAZUL ISLAM SHOJOL','product',1,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/riazul-islam-shojol/1','2026-03-06 00:24:24','2026-03-06 00:24:24'),(231,9,'ryqy57F22foQ2JOGOTioGKFj2F3yN25SQKBvviJ0','product_view','Viewed product: WinTerSMM','product',2,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/wintersmm/2','2026-03-06 00:24:43','2026-03-06 00:24:43'),(232,9,'ryqy57F22foQ2JOGOTioGKFj2F3yN25SQKBvviJ0','product_view','Viewed product: WinTerSMM','product',2,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/wintersmm/2','2026-03-06 01:02:15','2026-03-06 01:02:15'),(233,9,'ryqy57F22foQ2JOGOTioGKFj2F3yN25SQKBvviJ0','compare_remove','Removed from compare','product',4,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/compare/remove','2026-03-06 01:06:48','2026-03-06 01:06:48'),(234,9,'ryqy57F22foQ2JOGOTioGKFj2F3yN25SQKBvviJ0','cart_add','Added to cart: Cricket Jersey || Affordable Custom Cricket Jersey With Sublimation Printing','product',3,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-03-06 02:02:59','2026-03-06 02:02:59'),(235,9,'ryqy57F22foQ2JOGOTioGKFj2F3yN25SQKBvviJ0','product_view','Viewed product: Cricket Jersey || Affordable Custom Cricket Jersey With Sublimation Printing','product',3,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/cricket-jersey-affordable-custom-cricket-jersey-with-sublimation-printing/3','2026-03-06 02:09:18','2026-03-06 02:09:18'),(236,9,'ryqy57F22foQ2JOGOTioGKFj2F3yN25SQKBvviJ0','cart_add','Added to cart: Cricket Jersey || Affordable Custom Cricket Jersey With Sublimation Printing','product',3,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-03-06 02:09:28','2026-03-06 02:09:28'),(237,9,'ryqy57F22foQ2JOGOTioGKFj2F3yN25SQKBvviJ0','cart_add','Added to cart: Cricket Jersey || Affordable Custom Cricket Jersey With Sublimation Printing','product',3,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-03-06 02:09:28','2026-03-06 02:09:28'),(238,NULL,'ufHUwifxNgCHFoMcSdDS6TgG98qXOxqOK814bxZ3','product_view','Viewed product: WinTerSMM','product',2,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/wintersmm/2','2026-03-06 03:22:58','2026-03-06 03:22:58'),(239,9,'UErsR4VMnKdKnGM38SnV7UDPWKzimPXnX1ztnBgZ','login','User logged in',NULL,NULL,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/user/login','2026-03-06 03:23:15','2026-03-06 03:23:15'),(240,9,'AA7qc2o1Xn0jzOQQxnA895220a24wdZXf9BkdV46','login','User logged in',NULL,NULL,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/user/login','2026-03-06 03:37:30','2026-03-06 03:37:30'),(241,9,'lSQkPBalUrjiOmssbld4u6Yj22tAaJ4kRL0wcHS6','login','User logged in',NULL,NULL,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/user/login','2026-03-06 04:32:39','2026-03-06 04:32:39'),(242,9,'AjQMi77iB3dADiq7d5WMyWpiyVgt79WiJhNSUUav','login','User logged in',NULL,NULL,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/user/login','2026-03-06 04:48:28','2026-03-06 04:48:28'),(243,9,'AjQMi77iB3dADiq7d5WMyWpiyVgt79WiJhNSUUav','wishlist_remove','Removed from wishlist','product',3,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/wish-list/remove?product_id=3','2026-03-06 04:59:53','2026-03-06 04:59:53'),(244,9,'AjQMi77iB3dADiq7d5WMyWpiyVgt79WiJhNSUUav','product_view','Viewed product: WinTerSMM','product',2,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/wintersmm/2','2026-03-06 05:55:28','2026-03-06 05:55:28'),(245,9,'AjQMi77iB3dADiq7d5WMyWpiyVgt79WiJhNSUUav','cart_add','Added to cart: WinTerSMM','product',2,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-03-06 05:55:37','2026-03-06 05:55:37'),(246,NULL,'xyBXDQL075qmT8Sj4dUoPvJwSAiMbexBNWgcYmo2','login_failed','Failed login attempt: 420opu@gmail.com',NULL,NULL,'192.168.0.78','mobile','Handheld Browser','Android',NULL,NULL,NULL,NULL,'http://192.168.0.175/staylbd/user/login','2026-03-06 06:06:38','2026-03-06 06:06:38'),(247,NULL,'xyBXDQL075qmT8Sj4dUoPvJwSAiMbexBNWgcYmo2','login_failed','Failed login attempt: opumia',NULL,NULL,'192.168.0.78','mobile','Handheld Browser','Android',NULL,NULL,NULL,NULL,'http://192.168.0.175/staylbd/user/login','2026-03-06 06:07:03','2026-03-06 06:07:03'),(248,NULL,'xyBXDQL075qmT8Sj4dUoPvJwSAiMbexBNWgcYmo2','login_failed','Failed login attempt: opumia',NULL,NULL,'192.168.0.78','mobile','Handheld Browser','Android',NULL,NULL,NULL,NULL,'http://192.168.0.175/staylbd/user/login','2026-03-06 06:07:19','2026-03-06 06:07:19'),(249,10,'W1DgCccpWqmaesH5gDPnPVgTb9hAMK7v2g9xoqKN','registration','New user registered: opumiad',NULL,NULL,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/user/register','2026-03-06 07:24:43','2026-03-06 07:24:43'),(250,10,'W1DgCccpWqmaesH5gDPnPVgTb9hAMK7v2g9xoqKN','logout','User logged out',NULL,NULL,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/user/logout','2026-03-06 07:30:03','2026-03-06 07:30:03'),(251,9,'q4zvQi6mNuSLrnh6Rz6knpSQVnO8w0XYjUSBWFqK','login','User logged in',NULL,NULL,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/user/login','2026-03-06 07:31:20','2026-03-06 07:31:20'),(252,9,'q4zvQi6mNuSLrnh6Rz6knpSQVnO8w0XYjUSBWFqK','logout','User logged out',NULL,NULL,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/user/logout','2026-03-06 07:35:44','2026-03-06 07:35:44'),(253,9,'dO771g2PoVOR1kUJ2Yb6MhfjYQ5fn28DdECLxrt4','login','User logged in',NULL,NULL,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/user/login','2026-03-06 07:38:47','2026-03-06 07:38:47'),(254,9,'dO771g2PoVOR1kUJ2Yb6MhfjYQ5fn28DdECLxrt4','logout','User logged out',NULL,NULL,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/user/logout','2026-03-06 07:38:50','2026-03-06 07:38:50'),(255,9,'a0ylRqfdFNbELBKb8ObkVbecD9kktlfRhoQfKZBG','login','User logged in',NULL,NULL,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/user/login','2026-03-06 07:43:15','2026-03-06 07:43:15'),(256,9,'a0ylRqfdFNbELBKb8ObkVbecD9kktlfRhoQfKZBG','logout','User logged out',NULL,NULL,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/user/logout','2026-03-06 07:43:56','2026-03-06 07:43:56'),(257,9,'DHEXLWZkYkFEHOQRIdfzV5iMv46UaTQmI0qDAtok','login','User logged in',NULL,NULL,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/user/login','2026-03-06 07:44:35','2026-03-06 07:44:35'),(258,9,'DHEXLWZkYkFEHOQRIdfzV5iMv46UaTQmI0qDAtok','logout','User logged out',NULL,NULL,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/user/logout','2026-03-06 07:53:44','2026-03-06 07:53:44'),(259,9,'58Lf7CzkO5ttekqRhLzcTpOWwtSeaoEnvtMjvyD8','login','User logged in',NULL,NULL,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/user/login','2026-03-06 07:53:54','2026-03-06 07:53:54'),(260,9,'58Lf7CzkO5ttekqRhLzcTpOWwtSeaoEnvtMjvyD8','logout','User logged out',NULL,NULL,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/user/logout','2026-03-06 07:54:04','2026-03-06 07:54:04'),(261,11,'OUGzbwcbocLsp0O4tdjDDkwdX4CtWqJJZhrQd0Wc','registration','New user registered: gutuhg',NULL,NULL,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/user/register','2026-03-06 08:15:11','2026-03-06 08:15:11'),(262,11,'OUGzbwcbocLsp0O4tdjDDkwdX4CtWqJJZhrQd0Wc','logout','User logged out',NULL,NULL,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/user/logout','2026-03-06 08:15:47','2026-03-06 08:15:47'),(263,NULL,'ljx2rgR6TgQe0TXzuwo6qutiYmuuS8OWGrcbAI72','login_failed','Failed login attempt: 420opu@gmail.com',NULL,NULL,'192.168.0.78','mobile','Handheld Browser','Android',NULL,NULL,NULL,NULL,'http://192.168.0.175/staylbd/user/login','2026-03-06 08:16:14','2026-03-06 08:16:14'),(264,NULL,'ljx2rgR6TgQe0TXzuwo6qutiYmuuS8OWGrcbAI72','login_failed','Failed login attempt: 420opu@gmail.com',NULL,NULL,'192.168.0.78','mobile','Handheld Browser','Android',NULL,NULL,NULL,NULL,'http://192.168.0.175/staylbd/user/login','2026-03-06 08:16:24','2026-03-06 08:16:24'),(265,12,'BIM61mXF11pUpJxiWFgOXxOK06mT4zAPfYSO96YN','registration','New user registered: fgryjvtu',NULL,NULL,'192.168.0.78','mobile','Handheld Browser','Android',NULL,NULL,NULL,NULL,'http://192.168.0.175/staylbd/user/register','2026-03-06 08:27:21','2026-03-06 08:27:21'),(266,9,'6ynuwwXrwnQhB9GvK3mru1MoEAFaY0IR9ECeykiK','login','User logged in',NULL,NULL,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/user/login','2026-03-06 08:30:34','2026-03-06 08:30:34'),(267,12,'e6zSQuBTVAkGSFrnVe8YtNk6bnYCD78azA0l2q9v','login','User logged in',NULL,NULL,'192.168.0.78','mobile','Handheld Browser','Android',NULL,NULL,NULL,NULL,'http://192.168.0.175/staylbd/user/login','2026-03-06 08:35:33','2026-03-06 08:35:33'),(268,9,'6ynuwwXrwnQhB9GvK3mru1MoEAFaY0IR9ECeykiK','product_view','Viewed product: WinTerSMM','product',2,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/wintersmm/2','2026-03-06 09:54:45','2026-03-06 09:54:45'),(269,9,'6ynuwwXrwnQhB9GvK3mru1MoEAFaY0IR9ECeykiK','product_view','Viewed product: WinTerSMM','product',2,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/wintersmm/2','2026-03-06 09:55:23','2026-03-06 09:55:23'),(270,9,'6ynuwwXrwnQhB9GvK3mru1MoEAFaY0IR9ECeykiK','product_view','Viewed product: WinTerSMM','product',2,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/wintersmm/2','2026-03-06 09:56:10','2026-03-06 09:56:10'),(271,9,'6ynuwwXrwnQhB9GvK3mru1MoEAFaY0IR9ECeykiK','logout','User logged out',NULL,NULL,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/user/logout','2026-03-06 10:55:09','2026-03-06 10:55:09'),(272,9,'40am0PjU7LtShBxog4CwPMOt1GDMNvZEHKQzBsai','login','User logged in',NULL,NULL,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/user/login','2026-03-06 10:55:29','2026-03-06 10:55:29'),(273,NULL,'tGkpp9Rw1aYHvW6BrYSbq66viSANAcXYqtdd4Oba','compare_add','Added to compare: WinTerSMM','product',2,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/compare/add','2026-03-07 08:39:20','2026-03-07 08:39:20'),(274,NULL,'tGkpp9Rw1aYHvW6BrYSbq66viSANAcXYqtdd4Oba','cart_add','Added to cart: WinTerSMM','product',2,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-03-07 08:39:23','2026-03-07 08:39:23'),(275,NULL,'tGkpp9Rw1aYHvW6BrYSbq66viSANAcXYqtdd4Oba','wishlist_add','Added to wishlist: WinTerSMM','product',2,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/wish-list/add','2026-03-07 08:39:24','2026-03-07 08:39:24'),(276,NULL,'tGkpp9Rw1aYHvW6BrYSbq66viSANAcXYqtdd4Oba','cart_add','Added to cart: Cricket Jersey || Affordable Custom Cricket Jersey With Sublimation Printing','product',3,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-03-07 08:39:26','2026-03-07 08:39:26'),(277,NULL,'tGkpp9Rw1aYHvW6BrYSbq66viSANAcXYqtdd4Oba','compare_add','Added to compare: Cricket Jersey || Affordable Custom Cricket Jersey With Sublimation Printing','product',3,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/compare/add','2026-03-07 08:39:26','2026-03-07 08:39:26'),(278,NULL,'tGkpp9Rw1aYHvW6BrYSbq66viSANAcXYqtdd4Oba','wishlist_add','Added to wishlist: Cricket Jersey || Affordable Custom Cricket Jersey With Sublimation Printing','product',3,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/wish-list/add','2026-03-07 08:39:27','2026-03-07 08:39:27'),(279,NULL,'tGkpp9Rw1aYHvW6BrYSbq66viSANAcXYqtdd4Oba','cart_add','Added to cart: Affordable Custom Cricket Jersey With Sublimation Printing','product',4,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-03-07 08:39:28','2026-03-07 08:39:28'),(280,NULL,'tGkpp9Rw1aYHvW6BrYSbq66viSANAcXYqtdd4Oba','compare_add','Added to compare: Affordable Custom Cricket Jersey With Sublimation Printing','product',4,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/compare/add','2026-03-07 08:39:29','2026-03-07 08:39:29'),(281,NULL,'tGkpp9Rw1aYHvW6BrYSbq66viSANAcXYqtdd4Oba','wishlist_add','Added to wishlist: Affordable Custom Cricket Jersey With Sublimation Printing','product',4,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/wish-list/add','2026-03-07 08:39:29','2026-03-07 08:39:29'),(282,NULL,'tGkpp9Rw1aYHvW6BrYSbq66viSANAcXYqtdd4Oba','cart_add','Added to cart: Affordable Custom Cricket Jersey With Sublimation Printing','product',5,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-03-07 08:39:30','2026-03-07 08:39:30'),(283,NULL,'tGkpp9Rw1aYHvW6BrYSbq66viSANAcXYqtdd4Oba','compare_add','Added to compare: Affordable Custom Cricket Jersey With Sublimation Printing','product',5,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/compare/add','2026-03-07 08:39:31','2026-03-07 08:39:31'),(284,NULL,'tGkpp9Rw1aYHvW6BrYSbq66viSANAcXYqtdd4Oba','wishlist_add','Added to wishlist: Affordable Custom Cricket Jersey With Sublimation Printing','product',5,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/wish-list/add','2026-03-07 08:39:31','2026-03-07 08:39:31'),(285,NULL,'8MZCMNSA7vrJVKm3Uvg9WWR23auqnYo5hqXeJmxo','cart_add','Added to cart: WinTerSMM','product',2,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-03-08 09:05:10','2026-03-08 09:05:10'),(286,NULL,'8MZCMNSA7vrJVKm3Uvg9WWR23auqnYo5hqXeJmxo','compare_add','Added to compare: WinTerSMM','product',2,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/compare/add','2026-03-08 09:05:10','2026-03-08 09:05:10'),(287,NULL,'8MZCMNSA7vrJVKm3Uvg9WWR23auqnYo5hqXeJmxo','wishlist_add','Added to wishlist: WinTerSMM','product',2,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/wish-list/add','2026-03-08 09:05:11','2026-03-08 09:05:11'),(288,NULL,'8MZCMNSA7vrJVKm3Uvg9WWR23auqnYo5hqXeJmxo','cart_add','Added to cart: Cricket Jersey || Affordable Custom Cricket Jersey With Sublimation Printing','product',3,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-03-08 09:05:11','2026-03-08 09:05:11'),(289,NULL,'8MZCMNSA7vrJVKm3Uvg9WWR23auqnYo5hqXeJmxo','compare_add','Added to compare: Cricket Jersey || Affordable Custom Cricket Jersey With Sublimation Printing','product',3,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/compare/add','2026-03-08 09:05:12','2026-03-08 09:05:12'),(290,NULL,'8MZCMNSA7vrJVKm3Uvg9WWR23auqnYo5hqXeJmxo','wishlist_add','Added to wishlist: Cricket Jersey || Affordable Custom Cricket Jersey With Sublimation Printing','product',3,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/wish-list/add','2026-03-08 09:05:12','2026-03-08 09:05:12'),(291,NULL,'8MZCMNSA7vrJVKm3Uvg9WWR23auqnYo5hqXeJmxo','cart_add','Added to cart: Affordable Custom Cricket Jersey With Sublimation Printing','product',4,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-03-08 09:05:13','2026-03-08 09:05:13'),(292,NULL,'8MZCMNSA7vrJVKm3Uvg9WWR23auqnYo5hqXeJmxo','compare_add','Added to compare: Affordable Custom Cricket Jersey With Sublimation Printing','product',4,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/compare/add','2026-03-08 09:05:14','2026-03-08 09:05:14'),(293,NULL,'8MZCMNSA7vrJVKm3Uvg9WWR23auqnYo5hqXeJmxo','wishlist_add','Added to wishlist: Affordable Custom Cricket Jersey With Sublimation Printing','product',4,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/wish-list/add','2026-03-08 09:05:14','2026-03-08 09:05:14'),(294,NULL,'8MZCMNSA7vrJVKm3Uvg9WWR23auqnYo5hqXeJmxo','cart_add','Added to cart: Affordable Custom Cricket Jersey With Sublimation Printing','product',5,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-03-08 09:05:15','2026-03-08 09:05:15'),(295,NULL,'8MZCMNSA7vrJVKm3Uvg9WWR23auqnYo5hqXeJmxo','compare_add','Added to compare: Affordable Custom Cricket Jersey With Sublimation Printing','product',5,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/compare/add','2026-03-08 09:05:15','2026-03-08 09:05:15'),(296,NULL,'8MZCMNSA7vrJVKm3Uvg9WWR23auqnYo5hqXeJmxo','wishlist_add','Added to wishlist: Affordable Custom Cricket Jersey With Sublimation Printing','product',5,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/wish-list/add','2026-03-08 09:05:16','2026-03-08 09:05:16'),(297,NULL,'CbhJ1UDxYiyyfSCzdWsdQ5DutYQPy0rotgycGj5h','cart_add','Added to cart: WinTerSMM','product',2,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-03-09 07:59:25','2026-03-09 07:59:25'),(298,NULL,'CbhJ1UDxYiyyfSCzdWsdQ5DutYQPy0rotgycGj5h','compare_add','Added to compare: WinTerSMM','product',2,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/compare/add','2026-03-09 07:59:26','2026-03-09 07:59:26'),(299,NULL,'CbhJ1UDxYiyyfSCzdWsdQ5DutYQPy0rotgycGj5h','wishlist_add','Added to wishlist: WinTerSMM','product',2,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/wish-list/add','2026-03-09 07:59:26','2026-03-09 07:59:26'),(300,NULL,'CbhJ1UDxYiyyfSCzdWsdQ5DutYQPy0rotgycGj5h','cart_add','Added to cart: Cricket Jersey || Affordable Custom Cricket Jersey With Sublimation Printing','product',3,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-03-09 07:59:27','2026-03-09 07:59:27'),(301,NULL,'CbhJ1UDxYiyyfSCzdWsdQ5DutYQPy0rotgycGj5h','compare_add','Added to compare: Cricket Jersey || Affordable Custom Cricket Jersey With Sublimation Printing','product',3,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/compare/add','2026-03-09 07:59:28','2026-03-09 07:59:28'),(302,NULL,'CbhJ1UDxYiyyfSCzdWsdQ5DutYQPy0rotgycGj5h','wishlist_add','Added to wishlist: Cricket Jersey || Affordable Custom Cricket Jersey With Sublimation Printing','product',3,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/wish-list/add','2026-03-09 07:59:28','2026-03-09 07:59:28'),(303,NULL,'CbhJ1UDxYiyyfSCzdWsdQ5DutYQPy0rotgycGj5h','cart_add','Added to cart: Affordable Custom Cricket Jersey With Sublimation Printing','product',4,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-03-09 07:59:29','2026-03-09 07:59:29'),(304,NULL,'CbhJ1UDxYiyyfSCzdWsdQ5DutYQPy0rotgycGj5h','compare_add','Added to compare: Affordable Custom Cricket Jersey With Sublimation Printing','product',4,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/compare/add','2026-03-09 07:59:30','2026-03-09 07:59:30'),(305,NULL,'CbhJ1UDxYiyyfSCzdWsdQ5DutYQPy0rotgycGj5h','wishlist_add','Added to wishlist: Affordable Custom Cricket Jersey With Sublimation Printing','product',4,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/wish-list/add','2026-03-09 07:59:30','2026-03-09 07:59:30'),(306,NULL,'CbhJ1UDxYiyyfSCzdWsdQ5DutYQPy0rotgycGj5h','cart_add','Added to cart: Affordable Custom Cricket Jersey With Sublimation Printing','product',5,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-03-09 07:59:31','2026-03-09 07:59:31'),(307,NULL,'CbhJ1UDxYiyyfSCzdWsdQ5DutYQPy0rotgycGj5h','compare_add','Added to compare: Affordable Custom Cricket Jersey With Sublimation Printing','product',5,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/compare/add','2026-03-09 07:59:32','2026-03-09 07:59:32'),(308,NULL,'CbhJ1UDxYiyyfSCzdWsdQ5DutYQPy0rotgycGj5h','wishlist_add','Added to wishlist: Affordable Custom Cricket Jersey With Sublimation Printing','product',5,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/wish-list/add','2026-03-09 07:59:32','2026-03-09 07:59:32'),(309,9,'cxRyxuc7vD0uLKd0dROoGOVuwfSRVwxC3DM8RzUb','product_view','Viewed product: RIAZUL ISLAM SHOJOL','product',1,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/riazul-islam-shojol/1','2026-03-09 09:17:11','2026-03-09 09:17:11'),(310,NULL,'aJdnU8fg2dXR2CZqmVv81iKpJbxkKzj2MXKfLDbn','product_view','Viewed product: RIAZUL ISLAM SHOJOL','product',1,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/riazul-islam-shojol/1','2026-03-09 12:13:35','2026-03-09 12:13:35'),(311,NULL,'aJdnU8fg2dXR2CZqmVv81iKpJbxkKzj2MXKfLDbn','product_view','Viewed product: RIAZUL ISLAM SHOJOL','product',1,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/riazul-islam-shojol/1','2026-03-09 12:19:35','2026-03-09 12:19:35'),(312,NULL,'aJdnU8fg2dXR2CZqmVv81iKpJbxkKzj2MXKfLDbn','compare_add','Added to compare: WinTerSMM','product',2,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/compare/add','2026-03-09 12:30:35','2026-03-09 12:30:35'),(313,NULL,'aJdnU8fg2dXR2CZqmVv81iKpJbxkKzj2MXKfLDbn','cart_add','Added to cart: WinTerSMM','product',2,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-03-09 12:30:35','2026-03-09 12:30:35'),(314,NULL,'aJdnU8fg2dXR2CZqmVv81iKpJbxkKzj2MXKfLDbn','wishlist_add','Added to wishlist: WinTerSMM','product',2,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/wish-list/add','2026-03-09 12:30:36','2026-03-09 12:30:36'),(315,NULL,'aJdnU8fg2dXR2CZqmVv81iKpJbxkKzj2MXKfLDbn','cart_add','Added to cart: Cricket Jersey || Affordable Custom Cricket Jersey With Sublimation Printing','product',3,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-03-09 12:30:37','2026-03-09 12:30:37'),(316,NULL,'aJdnU8fg2dXR2CZqmVv81iKpJbxkKzj2MXKfLDbn','compare_add','Added to compare: Cricket Jersey || Affordable Custom Cricket Jersey With Sublimation Printing','product',3,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/compare/add','2026-03-09 12:30:37','2026-03-09 12:30:37'),(317,NULL,'aJdnU8fg2dXR2CZqmVv81iKpJbxkKzj2MXKfLDbn','wishlist_add','Added to wishlist: Cricket Jersey || Affordable Custom Cricket Jersey With Sublimation Printing','product',3,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/wish-list/add','2026-03-09 12:30:38','2026-03-09 12:30:38'),(318,NULL,'aJdnU8fg2dXR2CZqmVv81iKpJbxkKzj2MXKfLDbn','cart_add','Added to cart: Affordable Custom Cricket Jersey With Sublimation Printing','product',4,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-03-09 12:30:39','2026-03-09 12:30:39'),(319,NULL,'aJdnU8fg2dXR2CZqmVv81iKpJbxkKzj2MXKfLDbn','compare_add','Added to compare: Affordable Custom Cricket Jersey With Sublimation Printing','product',4,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/compare/add','2026-03-09 12:30:39','2026-03-09 12:30:39'),(320,NULL,'aJdnU8fg2dXR2CZqmVv81iKpJbxkKzj2MXKfLDbn','wishlist_add','Added to wishlist: Affordable Custom Cricket Jersey With Sublimation Printing','product',4,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/wish-list/add','2026-03-09 12:30:40','2026-03-09 12:30:40'),(321,NULL,'aJdnU8fg2dXR2CZqmVv81iKpJbxkKzj2MXKfLDbn','cart_add','Added to cart: Affordable Custom Cricket Jersey With Sublimation Printing','product',5,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-03-09 12:30:41','2026-03-09 12:30:41'),(322,NULL,'aJdnU8fg2dXR2CZqmVv81iKpJbxkKzj2MXKfLDbn','compare_add','Added to compare: Affordable Custom Cricket Jersey With Sublimation Printing','product',5,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/compare/add','2026-03-09 12:30:41','2026-03-09 12:30:41'),(323,NULL,'aJdnU8fg2dXR2CZqmVv81iKpJbxkKzj2MXKfLDbn','wishlist_add','Added to wishlist: Affordable Custom Cricket Jersey With Sublimation Printing','product',5,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/wish-list/add','2026-03-09 12:30:42','2026-03-09 12:30:42'),(324,NULL,'aJdnU8fg2dXR2CZqmVv81iKpJbxkKzj2MXKfLDbn','product_view','Viewed product: RIAZUL ISLAM SHOJOL','product',1,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/riazul-islam-shojol/1','2026-03-09 12:30:56','2026-03-09 12:30:56'),(325,NULL,'aJdnU8fg2dXR2CZqmVv81iKpJbxkKzj2MXKfLDbn','product_view','Viewed product: RIAZUL ISLAM SHOJOL','product',1,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/riazul-islam-shojol/1','2026-03-09 12:31:00','2026-03-09 12:31:00'),(326,NULL,'GjP4oV8yc6DtEarQkYUxIHc0HSgVp54BEBItJ4YU','compare_add','Added to compare: ,btjc\'sfnm;gjt v,nkl gkhjtjkk','product',6,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/compare/add','2026-03-10 07:55:25','2026-03-10 07:55:25'),(327,NULL,'GjP4oV8yc6DtEarQkYUxIHc0HSgVp54BEBItJ4YU','wishlist_add','Added to wishlist: ,btjc\'sfnm;gjt v,nkl gkhjtjkk','product',6,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/wish-list/add','2026-03-10 07:55:25','2026-03-10 07:55:25'),(328,NULL,'GjP4oV8yc6DtEarQkYUxIHc0HSgVp54BEBItJ4YU','cart_add','Added to cart: ,btjc\'sfnm;gjt v,nkl gkhjtjkk','product',6,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-03-10 07:55:26','2026-03-10 07:55:26'),(329,NULL,'GjP4oV8yc6DtEarQkYUxIHc0HSgVp54BEBItJ4YU','cart_add','Added to cart: T-shirt, টি-শার্ট','product',7,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-03-10 07:55:29','2026-03-10 07:55:29'),(330,NULL,'GjP4oV8yc6DtEarQkYUxIHc0HSgVp54BEBItJ4YU','compare_add','Added to compare: T-shirt, টি-শার্ট','product',7,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/compare/add','2026-03-10 07:55:29','2026-03-10 07:55:29'),(331,NULL,'GjP4oV8yc6DtEarQkYUxIHc0HSgVp54BEBItJ4YU','wishlist_add','Added to wishlist: T-shirt, টি-শার্ট','product',7,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/wish-list/add','2026-03-10 07:55:30','2026-03-10 07:55:30'),(332,NULL,'GjP4oV8yc6DtEarQkYUxIHc0HSgVp54BEBItJ4YU','cart_add','Added to cart: Affordable Custom Cricket Jersey With Sublimation Printing','product',5,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-03-10 07:55:31','2026-03-10 07:55:31'),(333,NULL,'GjP4oV8yc6DtEarQkYUxIHc0HSgVp54BEBItJ4YU','compare_add','Added to compare: Affordable Custom Cricket Jersey With Sublimation Printing','product',5,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/compare/add','2026-03-10 07:55:32','2026-03-10 07:55:32'),(334,NULL,'GjP4oV8yc6DtEarQkYUxIHc0HSgVp54BEBItJ4YU','wishlist_add','Added to wishlist: Affordable Custom Cricket Jersey With Sublimation Printing','product',5,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/wish-list/add','2026-03-10 07:55:32','2026-03-10 07:55:32'),(335,NULL,'GjP4oV8yc6DtEarQkYUxIHc0HSgVp54BEBItJ4YU','cart_add','Added to cart: Affordable Custom Cricket Jersey With Sublimation Printing','product',4,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-03-10 07:55:33','2026-03-10 07:55:33'),(336,NULL,'GjP4oV8yc6DtEarQkYUxIHc0HSgVp54BEBItJ4YU','compare_add','Added to compare: Affordable Custom Cricket Jersey With Sublimation Printing','product',4,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/compare/add','2026-03-10 07:55:34','2026-03-10 07:55:34'),(337,NULL,'GjP4oV8yc6DtEarQkYUxIHc0HSgVp54BEBItJ4YU','wishlist_add','Added to wishlist: Affordable Custom Cricket Jersey With Sublimation Printing','product',4,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/wish-list/add','2026-03-10 07:55:34','2026-03-10 07:55:34'),(338,NULL,'GjP4oV8yc6DtEarQkYUxIHc0HSgVp54BEBItJ4YU','cart_add','Added to cart: Cricket Jersey || Affordable Custom Cricket Jersey With Sublimation Printing','product',3,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-03-10 07:55:35','2026-03-10 07:55:35'),(339,NULL,'GjP4oV8yc6DtEarQkYUxIHc0HSgVp54BEBItJ4YU','wishlist_add','Added to wishlist: Cricket Jersey || Affordable Custom Cricket Jersey With Sublimation Printing','product',3,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/wish-list/add','2026-03-10 07:55:36','2026-03-10 07:55:36'),(340,NULL,'GjP4oV8yc6DtEarQkYUxIHc0HSgVp54BEBItJ4YU','cart_add','Added to cart: WinTerSMM','product',2,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-03-10 07:55:37','2026-03-10 07:55:37'),(341,NULL,'GjP4oV8yc6DtEarQkYUxIHc0HSgVp54BEBItJ4YU','wishlist_add','Added to wishlist: WinTerSMM','product',2,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/wish-list/add','2026-03-10 07:55:38','2026-03-10 07:55:38'),(342,NULL,'GjP4oV8yc6DtEarQkYUxIHc0HSgVp54BEBItJ4YU','wishlist_add','Added to wishlist: T-shirt, টি-শার্ট','product',10,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/wish-list/add','2026-03-10 08:58:55','2026-03-10 08:58:55'),(343,NULL,'GjP4oV8yc6DtEarQkYUxIHc0HSgVp54BEBItJ4YU','cart_add','Added to cart: T-shirt, টি-শার্ট','product',10,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-03-10 08:59:00','2026-03-10 08:59:00'),(344,NULL,'GjP4oV8yc6DtEarQkYUxIHc0HSgVp54BEBItJ4YU','cart_add','Added to cart: T-shirt, টি-শার্ট','product',9,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-03-10 08:59:08','2026-03-10 08:59:08'),(345,NULL,'GjP4oV8yc6DtEarQkYUxIHc0HSgVp54BEBItJ4YU','wishlist_add','Added to wishlist: T-shirt, টি-শার্ট','product',9,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/wish-list/add','2026-03-10 08:59:11','2026-03-10 08:59:11'),(346,NULL,'GjP4oV8yc6DtEarQkYUxIHc0HSgVp54BEBItJ4YU','cart_add','Added to cart: T-shirt, টি-শার্ট','product',8,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-03-10 08:59:16','2026-03-10 08:59:16'),(347,NULL,'GjP4oV8yc6DtEarQkYUxIHc0HSgVp54BEBItJ4YU','wishlist_add','Added to wishlist: T-shirt, টি-শার্ট','product',8,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/wish-list/add','2026-03-10 08:59:19','2026-03-10 08:59:19'),(348,NULL,'GjP4oV8yc6DtEarQkYUxIHc0HSgVp54BEBItJ4YU','compare_add','Added to compare: T-shirt, টি-শার্ট','product',8,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/compare/add','2026-03-10 09:01:32','2026-03-10 09:01:32'),(349,NULL,'GjP4oV8yc6DtEarQkYUxIHc0HSgVp54BEBItJ4YU','compare_add','Added to compare: T-shirt, টি-শার্ট','product',9,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/compare/add','2026-03-10 09:01:36','2026-03-10 09:01:36'),(350,NULL,'GjP4oV8yc6DtEarQkYUxIHc0HSgVp54BEBItJ4YU','compare_add','Added to compare: T-shirt, টি-শার্ট','product',10,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/compare/add','2026-03-10 09:01:40','2026-03-10 09:01:40'),(351,NULL,'GjP4oV8yc6DtEarQkYUxIHc0HSgVp54BEBItJ4YU','compare_add','Added to compare: Cricket Jersey || Affordable Custom Cricket Jersey With Sublimation Printing','product',3,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/compare/add','2026-03-10 09:02:04','2026-03-10 09:02:04'),(352,NULL,'ZFwlsoyxVALT8lYSPWV62yrQO37azVg3ewVvdKXK','product_view','Viewed product: RIAZUL ISLAM SHOJOL','product',1,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/riazul-islam-shojol/1','2026-03-10 11:26:58','2026-03-10 11:26:58'),(353,NULL,'36Nafi69nlEk279n9VuKzvEkSUU3F0u2XlDeYOHk','product_view','Viewed product: T-shirt, টি-শার্ট','product',9,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/t-shirt-ti-sart/9','2026-03-10 12:09:29','2026-03-10 12:09:29'),(354,NULL,'KdPlybFVNQ95Elf1DguFPx0wM6fRXXVEQeysTCE0','search_text','Search: l',NULL,NULL,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/search/universal','2026-03-11 09:04:59','2026-03-11 09:04:59'),(355,NULL,'KdPlybFVNQ95Elf1DguFPx0wM6fRXXVEQeysTCE0','search_text','Search: ln',NULL,NULL,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/search/universal','2026-03-11 09:04:59','2026-03-11 09:04:59'),(356,NULL,'KdPlybFVNQ95Elf1DguFPx0wM6fRXXVEQeysTCE0','search_text','Search: lns',NULL,NULL,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/search/universal','2026-03-11 09:05:00','2026-03-11 09:05:00'),(357,NULL,'KdPlybFVNQ95Elf1DguFPx0wM6fRXXVEQeysTCE0','search_text','Search: lns',NULL,NULL,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/search/universal','2026-03-11 09:05:08','2026-03-11 09:05:08'),(358,NULL,'KdPlybFVNQ95Elf1DguFPx0wM6fRXXVEQeysTCE0','search_text','Search: lnsp',NULL,NULL,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/search/universal','2026-03-11 09:05:09','2026-03-11 09:05:09'),(359,NULL,'KdPlybFVNQ95Elf1DguFPx0wM6fRXXVEQeysTCE0','search_text','Search: lnspe',NULL,NULL,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/search/universal','2026-03-11 09:05:10','2026-03-11 09:05:10'),(360,NULL,'KdPlybFVNQ95Elf1DguFPx0wM6fRXXVEQeysTCE0','search_text','Search: lnspec',NULL,NULL,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/search/universal','2026-03-11 09:05:11','2026-03-11 09:05:11'),(361,NULL,'KdPlybFVNQ95Elf1DguFPx0wM6fRXXVEQeysTCE0','search_text','Search: lnspec',NULL,NULL,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/search/universal','2026-03-11 09:05:17','2026-03-11 09:05:17'),(362,NULL,'KdPlybFVNQ95Elf1DguFPx0wM6fRXXVEQeysTCE0','search_text','Search: lnspect',NULL,NULL,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/search/universal','2026-03-11 09:05:19','2026-03-11 09:05:19'),(363,NULL,'KdPlybFVNQ95Elf1DguFPx0wM6fRXXVEQeysTCE0','search_text','Search: lnspect',NULL,NULL,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/search/universal','2026-03-11 09:18:02','2026-03-11 09:18:02'),(364,NULL,'KdPlybFVNQ95Elf1DguFPx0wM6fRXXVEQeysTCE0','search_text','Search: lnspect',NULL,NULL,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/search/universal','2026-03-11 09:40:24','2026-03-11 09:40:24'),(365,NULL,'KdPlybFVNQ95Elf1DguFPx0wM6fRXXVEQeysTCE0','search_text','Search: lnspect',NULL,NULL,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/search/universal','2026-03-11 09:40:26','2026-03-11 09:40:26'),(366,NULL,'KdPlybFVNQ95Elf1DguFPx0wM6fRXXVEQeysTCE0','cart_add','Added to cart: WinTerSMM','product',2,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-03-11 14:31:30','2026-03-11 14:31:30'),(367,NULL,'KdPlybFVNQ95Elf1DguFPx0wM6fRXXVEQeysTCE0','compare_add','Added to compare: WinTerSMM','product',2,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/compare/add','2026-03-11 14:31:32','2026-03-11 14:31:32'),(368,NULL,'KdPlybFVNQ95Elf1DguFPx0wM6fRXXVEQeysTCE0','wishlist_add','Added to wishlist: WinTerSMM','product',2,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/wish-list/add','2026-03-11 14:31:34','2026-03-11 14:31:34'),(369,NULL,'KdPlybFVNQ95Elf1DguFPx0wM6fRXXVEQeysTCE0','compare_add','Added to compare: T-shirt, টি-শার্ট','product',7,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/compare/add','2026-03-11 14:32:02','2026-03-11 14:32:02'),(370,NULL,'uEEcYRcgfqJuLFlrIEJKtKVaPEer27gh403DNBrc','product_view','Viewed product: T-shirt, টি-শার্ট','product',8,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/t-shirt-ti-sart/8','2026-03-12 11:22:01','2026-03-12 11:22:01'),(371,NULL,'IG0zfjVwtDmowaLsldtX0OhWT42kjpgtGWQn0v9H','product_view','Viewed product: Affordable Custom Cricket Jersey With Sublimation Printing','product',5,'192.168.0.139','mobile','Handheld Browser','Android',NULL,NULL,NULL,NULL,'http://192.168.0.175/staylbd/product/details/affordable-custom-cricket-jersey-with-sublimation-printing/5','2026-03-12 11:26:47','2026-03-12 11:26:47'),(372,NULL,'HVkYTPoPveNGZzaUS6dT2JUy7pplyHu6nWimn2j3','product_view','Viewed product: WinTerSMM','product',2,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/wintersmm/2','2026-03-12 21:26:23','2026-03-12 21:26:23'),(373,NULL,'Xfv2yVG0vtYmHkMqlyATkIynToo8pXHZR1j4vUO0','product_view','Viewed product: WinTerSMM','product',2,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/wintersmm/2','2026-03-12 21:26:37','2026-03-12 21:26:37'),(374,NULL,'mQMhZEE5owugQrUwJcW2jAAOQUsfuRUlAJvQmMeS','product_view','Viewed product: T-shirt, টি-শার্ট','product',8,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/t-shirt-ti-sart/8','2026-03-12 22:08:27','2026-03-12 22:08:27'),(375,NULL,'mQMhZEE5owugQrUwJcW2jAAOQUsfuRUlAJvQmMeS','product_view','Viewed product: T-shirt, টি-শার্ট','product',8,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/t-shirt-ti-sart/8','2026-03-12 22:08:42','2026-03-12 22:08:42'),(376,NULL,'mQMhZEE5owugQrUwJcW2jAAOQUsfuRUlAJvQmMeS','product_view','Viewed product: T-shirt, টি-শার্ট','product',8,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/t-shirt-ti-sart/8','2026-03-12 22:08:47','2026-03-12 22:08:47'),(377,NULL,'mQMhZEE5owugQrUwJcW2jAAOQUsfuRUlAJvQmMeS','product_view','Viewed product: T-shirt, টি-শার্ট','product',9,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/t-shirt-ti-sart/9','2026-03-12 22:09:23','2026-03-12 22:09:23'),(378,NULL,'mQMhZEE5owugQrUwJcW2jAAOQUsfuRUlAJvQmMeS','product_view','Viewed product: T-shirt, টি-শার্ট','product',9,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/t-shirt-ti-sart/9','2026-03-12 22:11:16','2026-03-12 22:11:16'),(379,NULL,'mQMhZEE5owugQrUwJcW2jAAOQUsfuRUlAJvQmMeS','product_view','Viewed product: T-shirt, টি-শার্ট','product',9,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/t-shirt-ti-sart/9','2026-03-12 22:13:18','2026-03-12 22:13:18'),(380,NULL,'mQMhZEE5owugQrUwJcW2jAAOQUsfuRUlAJvQmMeS','product_view','Viewed product: T-shirt, টি-শার্ট','product',9,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/t-shirt-ti-sart/9','2026-03-12 22:13:36','2026-03-12 22:13:36'),(381,NULL,'mQMhZEE5owugQrUwJcW2jAAOQUsfuRUlAJvQmMeS','product_view','Viewed product: T-shirt, টি-শার্ট','product',9,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/t-shirt-ti-sart/9','2026-03-12 22:19:07','2026-03-12 22:19:07'),(382,NULL,'mQMhZEE5owugQrUwJcW2jAAOQUsfuRUlAJvQmMeS','product_view','Viewed product: T-shirt, টি-শার্ট','product',9,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/t-shirt-ti-sart/9','2026-03-12 22:20:28','2026-03-12 22:20:28'),(383,NULL,'mQMhZEE5owugQrUwJcW2jAAOQUsfuRUlAJvQmMeS','product_view','Viewed product: T-shirt, টি-শার্ট','product',9,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/t-shirt-ti-sart/9','2026-03-12 22:29:54','2026-03-12 22:29:54'),(384,NULL,'mQMhZEE5owugQrUwJcW2jAAOQUsfuRUlAJvQmMeS','cart_add','Added to cart: T-shirt, টি-শার্ট','product',9,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-03-12 22:30:35','2026-03-12 22:30:35'),(385,NULL,'mQMhZEE5owugQrUwJcW2jAAOQUsfuRUlAJvQmMeS','product_view','Viewed product: T-shirt, টি-শার্ট','product',9,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/t-shirt-ti-sart/9','2026-03-12 22:32:52','2026-03-12 22:32:52'),(386,NULL,'mQMhZEE5owugQrUwJcW2jAAOQUsfuRUlAJvQmMeS','product_view','Viewed product: T-shirt, টি-শার্ট','product',9,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/t-shirt-ti-sart/9','2026-03-12 22:33:24','2026-03-12 22:33:24'),(387,NULL,'FjIqqr6DgSNMFLuatcz0l9kumM57if57uGDUJGjH','product_view','Viewed product: T-shirt, টি-শার্ট','product',7,'192.168.0.8','mobile','Handheld Browser','Android',NULL,NULL,NULL,NULL,'http://192.168.0.175/staylbd/product/details/t-shirt-ti-sart/7','2026-03-12 22:34:07','2026-03-12 22:34:07'),(388,NULL,'mQMhZEE5owugQrUwJcW2jAAOQUsfuRUlAJvQmMeS','product_view','Viewed product: T-shirt, টি-শার্ট','product',9,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/t-shirt-ti-sart/9','2026-03-12 22:42:01','2026-03-12 22:42:01'),(389,NULL,'mQMhZEE5owugQrUwJcW2jAAOQUsfuRUlAJvQmMeS','product_view','Viewed product: T-shirt, টি-শার্ট','product',9,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/t-shirt-ti-sart/9','2026-03-12 22:42:14','2026-03-12 22:42:14'),(390,NULL,'mQMhZEE5owugQrUwJcW2jAAOQUsfuRUlAJvQmMeS','product_view','Viewed product: T-shirt, টি-শার্ট','product',9,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/t-shirt-ti-sart/9','2026-03-12 22:44:07','2026-03-12 22:44:07'),(391,NULL,'FjIqqr6DgSNMFLuatcz0l9kumM57if57uGDUJGjH','product_view','Viewed product: T-shirt, টি-শার্ট','product',8,'192.168.0.8','mobile','Handheld Browser','Android',NULL,NULL,NULL,NULL,'http://192.168.0.175/staylbd/product/details/t-shirt-ti-sart/8','2026-03-12 22:44:51','2026-03-12 22:44:51'),(392,NULL,'mQMhZEE5owugQrUwJcW2jAAOQUsfuRUlAJvQmMeS','product_view','Viewed product: T-shirt, টি-শার্ট','product',9,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/t-shirt-ti-sart/9','2026-03-12 22:50:08','2026-03-12 22:50:08'),(393,NULL,'FjIqqr6DgSNMFLuatcz0l9kumM57if57uGDUJGjH','product_view','Viewed product: T-shirt, টি-শার্ট','product',9,'192.168.0.8','mobile','Handheld Browser','Android',NULL,NULL,NULL,NULL,'http://192.168.0.175/staylbd/product/details/t-shirt-ti-sart/9','2026-03-12 22:51:56','2026-03-12 22:51:56'),(394,NULL,'FjIqqr6DgSNMFLuatcz0l9kumM57if57uGDUJGjH','product_view','Viewed product: T-shirt, টি-শার্ট','product',9,'192.168.0.8','mobile','Handheld Browser','Android',NULL,NULL,NULL,NULL,'http://192.168.0.175/staylbd/product/details/t-shirt-ti-sart/9','2026-03-12 23:02:51','2026-03-12 23:02:51'),(395,NULL,'mQMhZEE5owugQrUwJcW2jAAOQUsfuRUlAJvQmMeS','product_view','Viewed product: T-shirt, টি-শার্ট','product',9,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/t-shirt-ti-sart/9','2026-03-12 23:03:13','2026-03-12 23:03:13'),(396,NULL,'mQMhZEE5owugQrUwJcW2jAAOQUsfuRUlAJvQmMeS','product_view','Viewed product: T-shirt, টি-শার্ট','product',9,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/t-shirt-ti-sart/9','2026-03-12 23:09:13','2026-03-12 23:09:13'),(397,NULL,'mQMhZEE5owugQrUwJcW2jAAOQUsfuRUlAJvQmMeS','product_view','Viewed product: T-shirt, টি-শার্ট','product',9,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/t-shirt-ti-sart/9','2026-03-12 23:10:02','2026-03-12 23:10:02'),(398,NULL,'mQMhZEE5owugQrUwJcW2jAAOQUsfuRUlAJvQmMeS','product_view','Viewed product: T-shirt, টি-শার্ট','product',9,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/t-shirt-ti-sart/9','2026-03-12 23:13:19','2026-03-12 23:13:19'),(399,NULL,'mQMhZEE5owugQrUwJcW2jAAOQUsfuRUlAJvQmMeS','product_view','Viewed product: T-shirt, টি-শার্ট','product',9,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/t-shirt-ti-sart/9','2026-03-12 23:13:39','2026-03-12 23:13:39'),(400,NULL,'mQMhZEE5owugQrUwJcW2jAAOQUsfuRUlAJvQmMeS','product_view','Viewed product: T-shirt, টি-শার্ট','product',9,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/t-shirt-ti-sart/9','2026-03-12 23:13:52','2026-03-12 23:13:52'),(401,NULL,'mQMhZEE5owugQrUwJcW2jAAOQUsfuRUlAJvQmMeS','cart_remove','Removed from cart: T-shirt, টি-শার্ট','product',9,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/remove','2026-03-12 23:13:56','2026-03-12 23:13:56'),(402,NULL,'mQMhZEE5owugQrUwJcW2jAAOQUsfuRUlAJvQmMeS','cart_add','Added to cart: T-shirt, টি-শার্ট','product',9,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-03-12 23:13:58','2026-03-12 23:13:58'),(403,NULL,'mQMhZEE5owugQrUwJcW2jAAOQUsfuRUlAJvQmMeS','compare_add','Added to compare: T-shirt, টি-শার্ট','product',9,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/compare/add','2026-03-12 23:14:02','2026-03-12 23:14:02'),(404,NULL,'mQMhZEE5owugQrUwJcW2jAAOQUsfuRUlAJvQmMeS','product_view','Viewed product: T-shirt, টি-শার্ট','product',9,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/t-shirt-ti-sart/9','2026-03-12 23:14:13','2026-03-12 23:14:13'),(405,NULL,'mQMhZEE5owugQrUwJcW2jAAOQUsfuRUlAJvQmMeS','wishlist_add','Added to wishlist: T-shirt, টি-শার্ট','product',9,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/wish-list/add','2026-03-12 23:14:15','2026-03-12 23:14:15'),(406,NULL,'mQMhZEE5owugQrUwJcW2jAAOQUsfuRUlAJvQmMeS','cart_add','Added to cart: T-shirt, টি-শার্ট','product',7,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-03-12 23:53:08','2026-03-12 23:53:08'),(407,NULL,'mQMhZEE5owugQrUwJcW2jAAOQUsfuRUlAJvQmMeS','compare_add','Added to compare: T-shirt, টি-শার্ট','product',7,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/compare/add','2026-03-12 23:53:09','2026-03-12 23:53:09'),(408,NULL,'mQMhZEE5owugQrUwJcW2jAAOQUsfuRUlAJvQmMeS','wishlist_add','Added to wishlist: T-shirt, টি-শার্ট','product',7,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/wish-list/add','2026-03-12 23:53:11','2026-03-12 23:53:11'),(409,NULL,'mQMhZEE5owugQrUwJcW2jAAOQUsfuRUlAJvQmMeS','product_view','Viewed product: Affordable Custom Cricket Jersey With Sublimation Printing','product',5,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/affordable-custom-cricket-jersey-with-sublimation-printing/5','2026-03-13 00:11:07','2026-03-13 00:11:07'),(410,NULL,'mQMhZEE5owugQrUwJcW2jAAOQUsfuRUlAJvQmMeS','product_view','Viewed product: Affordable Custom Cricket Jersey With Sublimation Printing','product',5,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/affordable-custom-cricket-jersey-with-sublimation-printing/5','2026-03-13 00:12:50','2026-03-13 00:12:50'),(411,NULL,'mQMhZEE5owugQrUwJcW2jAAOQUsfuRUlAJvQmMeS','product_view','Viewed product: Affordable Custom Cricket Jersey With Sublimation Printing','product',5,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/affordable-custom-cricket-jersey-with-sublimation-printing/5','2026-03-13 00:15:32','2026-03-13 00:15:32'),(412,NULL,'mQMhZEE5owugQrUwJcW2jAAOQUsfuRUlAJvQmMeS','product_view','Viewed product: T-shirt, টি-শার্ট','product',7,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/t-shirt-ti-sart/7','2026-03-13 00:16:10','2026-03-13 00:16:10'),(413,NULL,'mQMhZEE5owugQrUwJcW2jAAOQUsfuRUlAJvQmMeS','product_view','Viewed product: T-shirt, টি-শার্ট','product',7,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/t-shirt-ti-sart/7','2026-03-13 00:26:46','2026-03-13 00:26:46'),(414,NULL,'mQMhZEE5owugQrUwJcW2jAAOQUsfuRUlAJvQmMeS','product_view','Viewed product: T-shirt, টি-শার্ট','product',7,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/t-shirt-ti-sart/7','2026-03-13 00:30:00','2026-03-13 00:30:00'),(415,NULL,'mQMhZEE5owugQrUwJcW2jAAOQUsfuRUlAJvQmMeS','product_view','Viewed product: T-shirt, টি-শার্ট','product',7,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/t-shirt-ti-sart/7','2026-03-13 00:31:51','2026-03-13 00:31:51'),(416,NULL,'mQMhZEE5owugQrUwJcW2jAAOQUsfuRUlAJvQmMeS','cart_add','Added to cart: T-shirt, টি-শার্ট','product',7,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-03-13 00:31:56','2026-03-13 00:31:56'),(417,NULL,'mQMhZEE5owugQrUwJcW2jAAOQUsfuRUlAJvQmMeS','product_view','Viewed product: Affordable Custom Cricket Jersey With Sublimation Printing','product',5,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/affordable-custom-cricket-jersey-with-sublimation-printing/5','2026-03-13 00:40:04','2026-03-13 00:40:04'),(418,NULL,'FjIqqr6DgSNMFLuatcz0l9kumM57if57uGDUJGjH','product_view','Viewed product: স্ক্রিনশট দিয়েছি  Affordable Custom Cricket Jersey With Sublimation Printing','product',4,'192.168.0.8','mobile','Handheld Browser','Android',NULL,NULL,NULL,NULL,'http://192.168.0.175/staylbd/product/details/skrinsht-dizechi-affordable-custom-cricket-jersey-with-sublimation-printing/4','2026-03-13 00:41:04','2026-03-13 00:41:04'),(419,NULL,'FjIqqr6DgSNMFLuatcz0l9kumM57if57uGDUJGjH','product_view','Viewed product: T-shirt, টি-শার্ট','product',8,'192.168.0.8','mobile','Handheld Browser','Android',NULL,NULL,NULL,NULL,'http://192.168.0.175/staylbd/product/details/t-shirt-ti-sart/8','2026-03-13 00:41:44','2026-03-13 00:41:44'),(420,NULL,'FjIqqr6DgSNMFLuatcz0l9kumM57if57uGDUJGjH','product_view','Viewed product: T-shirt, টি-শার্ট','product',9,'192.168.0.8','mobile','Handheld Browser','Android',NULL,NULL,NULL,NULL,'http://192.168.0.175/staylbd/product/details/t-shirt-ti-sart/9','2026-03-13 00:48:26','2026-03-13 00:48:26'),(421,NULL,'FjIqqr6DgSNMFLuatcz0l9kumM57if57uGDUJGjH','product_view','Viewed product: Affordable Custom Cricket Jersey With Sublimation Printing','product',5,'192.168.0.8','mobile','Handheld Browser','Android',NULL,NULL,NULL,NULL,'http://192.168.0.175/staylbd/product/details/affordable-custom-cricket-jersey-with-sublimation-printing/5','2026-03-13 00:50:08','2026-03-13 00:50:08'),(422,NULL,'mQMhZEE5owugQrUwJcW2jAAOQUsfuRUlAJvQmMeS','product_view','Viewed product: Affordable Custom Cricket Jersey With Sublimation Printing','product',5,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/affordable-custom-cricket-jersey-with-sublimation-printing/5','2026-03-13 00:50:28','2026-03-13 00:50:28'),(423,NULL,'mQMhZEE5owugQrUwJcW2jAAOQUsfuRUlAJvQmMeS','product_view','Viewed product: Affordable Custom Cricket Jersey With Sublimation Printing','product',5,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/affordable-custom-cricket-jersey-with-sublimation-printing/5','2026-03-13 00:50:34','2026-03-13 00:50:34'),(424,NULL,'mQMhZEE5owugQrUwJcW2jAAOQUsfuRUlAJvQmMeS','product_view','Viewed product: Affordable Custom Cricket Jersey With Sublimation Printing','product',5,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/affordable-custom-cricket-jersey-with-sublimation-printing/5','2026-03-13 00:50:57','2026-03-13 00:50:57'),(425,NULL,'mQMhZEE5owugQrUwJcW2jAAOQUsfuRUlAJvQmMeS','product_view','Viewed product: Affordable Custom Cricket Jersey With Sublimation Printing','product',5,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/affordable-custom-cricket-jersey-with-sublimation-printing/5','2026-03-13 00:51:08','2026-03-13 00:51:08'),(426,NULL,'FjIqqr6DgSNMFLuatcz0l9kumM57if57uGDUJGjH','product_view','Viewed product: Affordable Custom Cricket Jersey With Sublimation Printing','product',5,'192.168.0.8','mobile','Handheld Browser','Android',NULL,NULL,NULL,NULL,'http://192.168.0.175/staylbd/product/details/affordable-custom-cricket-jersey-with-sublimation-printing/5','2026-03-13 01:05:53','2026-03-13 01:05:53'),(427,NULL,'mQMhZEE5owugQrUwJcW2jAAOQUsfuRUlAJvQmMeS','product_view','Viewed product: Affordable Custom Cricket Jersey With Sublimation Printing','product',5,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/affordable-custom-cricket-jersey-with-sublimation-printing/5','2026-03-13 02:06:34','2026-03-13 02:06:34'),(428,NULL,'FjIqqr6DgSNMFLuatcz0l9kumM57if57uGDUJGjH','product_view','Viewed product: স্ক্রিনশট দিয়েছি  Affordable Custom Cricket Jersey With Sublimation Printing','product',4,'192.168.0.8','mobile','Handheld Browser','Android',NULL,NULL,NULL,NULL,'http://192.168.0.175/staylbd/product/details/skrinsht-dizechi-affordable-custom-cricket-jersey-with-sublimation-printing/4','2026-03-13 02:14:02','2026-03-13 02:14:02'),(429,NULL,'mQMhZEE5owugQrUwJcW2jAAOQUsfuRUlAJvQmMeS','product_view','Viewed product: Affordable Custom Cricket Jersey With Sublimation Printing','product',5,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/affordable-custom-cricket-jersey-with-sublimation-printing/5','2026-03-13 02:14:53','2026-03-13 02:14:53'),(430,NULL,'mQMhZEE5owugQrUwJcW2jAAOQUsfuRUlAJvQmMeS','product_view','Viewed product: Affordable Custom Cricket Jersey With Sublimation Printing','product',5,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/affordable-custom-cricket-jersey-with-sublimation-printing/5','2026-03-13 02:15:04','2026-03-13 02:15:04'),(431,NULL,'mQMhZEE5owugQrUwJcW2jAAOQUsfuRUlAJvQmMeS','product_view','Viewed product: Affordable Custom Cricket Jersey With Sublimation Printing','product',5,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/affordable-custom-cricket-jersey-with-sublimation-printing/5','2026-03-13 02:20:41','2026-03-13 02:20:41'),(432,NULL,'FjIqqr6DgSNMFLuatcz0l9kumM57if57uGDUJGjH','product_view','Viewed product: T-shirt, টি-শার্ট','product',8,'192.168.0.8','mobile','Handheld Browser','Android',NULL,NULL,NULL,NULL,'http://192.168.0.175/staylbd/product/details/t-shirt-ti-sart/8','2026-03-13 02:28:36','2026-03-13 02:28:36'),(433,NULL,'mQMhZEE5owugQrUwJcW2jAAOQUsfuRUlAJvQmMeS','product_view','Viewed product: Affordable Custom Cricket Jersey With Sublimation Printing','product',5,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/affordable-custom-cricket-jersey-with-sublimation-printing/5','2026-03-13 02:30:12','2026-03-13 02:30:12'),(434,NULL,'mQMhZEE5owugQrUwJcW2jAAOQUsfuRUlAJvQmMeS','product_view','Viewed product: Affordable Custom Cricket Jersey With Sublimation Printing','product',5,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/affordable-custom-cricket-jersey-with-sublimation-printing/5','2026-03-13 02:30:34','2026-03-13 02:30:34'),(435,NULL,'mQMhZEE5owugQrUwJcW2jAAOQUsfuRUlAJvQmMeS','product_view','Viewed product: Affordable Custom Cricket Jersey With Sublimation Printing','product',5,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/affordable-custom-cricket-jersey-with-sublimation-printing/5','2026-03-13 02:35:02','2026-03-13 02:35:02'),(436,NULL,'mQMhZEE5owugQrUwJcW2jAAOQUsfuRUlAJvQmMeS','product_view','Viewed product: Affordable Custom Cricket Jersey With Sublimation Printing','product',5,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/affordable-custom-cricket-jersey-with-sublimation-printing/5','2026-03-13 02:35:07','2026-03-13 02:35:07'),(437,NULL,'mQMhZEE5owugQrUwJcW2jAAOQUsfuRUlAJvQmMeS','product_view','Viewed product: T-shirt, টি-শার্ট','product',7,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/t-shirt-ti-sart/7','2026-03-13 02:35:38','2026-03-13 02:35:38'),(438,NULL,'mQMhZEE5owugQrUwJcW2jAAOQUsfuRUlAJvQmMeS','product_view','Viewed product: Affordable Custom Cricket Jersey With Sublimation Printing','product',5,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/affordable-custom-cricket-jersey-with-sublimation-printing/5','2026-03-13 02:46:32','2026-03-13 02:46:32'),(439,NULL,'mQMhZEE5owugQrUwJcW2jAAOQUsfuRUlAJvQmMeS','product_view','Viewed product: Affordable Custom Cricket Jersey With Sublimation Printing','product',5,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/affordable-custom-cricket-jersey-with-sublimation-printing/5','2026-03-13 02:47:23','2026-03-13 02:47:23'),(440,NULL,'FjIqqr6DgSNMFLuatcz0l9kumM57if57uGDUJGjH','product_view','Viewed product: T-shirt, টি-শার্ট','product',9,'192.168.0.8','mobile','Handheld Browser','Android',NULL,NULL,NULL,NULL,'http://192.168.0.175/staylbd/product/details/t-shirt-ti-sart/9','2026-03-13 02:58:12','2026-03-13 02:58:12'),(441,NULL,'mQMhZEE5owugQrUwJcW2jAAOQUsfuRUlAJvQmMeS','product_view','Viewed product: T-shirt, টি-শার্ট','product',7,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/t-shirt-ti-sart/7','2026-03-13 03:51:51','2026-03-13 03:51:51'),(442,NULL,'FjIqqr6DgSNMFLuatcz0l9kumM57if57uGDUJGjH','product_view','Viewed product: Cricket Jersey || Affordable Custom Cricket Jersey With Sublimation Printing','product',3,'192.168.0.8','mobile','Handheld Browser','Android',NULL,NULL,NULL,NULL,'http://192.168.0.175/staylbd/product/details/cricket-jersey-affordable-custom-cricket-jersey-with-sublimation-printing/3','2026-03-13 04:07:54','2026-03-13 04:07:54'),(443,NULL,'mQMhZEE5owugQrUwJcW2jAAOQUsfuRUlAJvQmMeS','product_view','Viewed product: WinTerSMM','product',2,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/wintersmm/2','2026-03-13 04:17:35','2026-03-13 04:17:35'),(444,NULL,'mQMhZEE5owugQrUwJcW2jAAOQUsfuRUlAJvQmMeS','product_view','Viewed product: WinTerSMM','product',2,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/wintersmm/2','2026-03-13 04:17:52','2026-03-13 04:17:52'),(445,NULL,'FjIqqr6DgSNMFLuatcz0l9kumM57if57uGDUJGjH','product_view','Viewed product: T-shirt, টি-শার্ট','product',10,'192.168.0.8','mobile','Handheld Browser','Android',NULL,NULL,NULL,NULL,'http://192.168.0.175/staylbd/product/details/t-shirt-ti-sart/10','2026-03-13 04:50:53','2026-03-13 04:50:53'),(446,NULL,'mQMhZEE5owugQrUwJcW2jAAOQUsfuRUlAJvQmMeS','product_view','Viewed product: Affordable Custom Cricket Jersey With Sublimation Printing','product',5,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/affordable-custom-cricket-jersey-with-sublimation-printing/5','2026-03-13 04:53:14','2026-03-13 04:53:14'),(447,NULL,'mQMhZEE5owugQrUwJcW2jAAOQUsfuRUlAJvQmMeS','product_view','Viewed product: Affordable Custom Cricket Jersey With Sublimation Printing','product',5,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/affordable-custom-cricket-jersey-with-sublimation-printing/5','2026-03-13 04:53:48','2026-03-13 04:53:48'),(448,NULL,'FjIqqr6DgSNMFLuatcz0l9kumM57if57uGDUJGjH','product_view','Viewed product: T-shirt, টি-শার্ট','product',10,'192.168.0.8','mobile','Handheld Browser','Android',NULL,NULL,NULL,NULL,'http://192.168.0.175/staylbd/product/details/t-shirt-ti-sart/10','2026-03-13 05:00:35','2026-03-13 05:00:35'),(449,NULL,'FjIqqr6DgSNMFLuatcz0l9kumM57if57uGDUJGjH','product_view','Viewed product: T-shirt, টি-শার্ট','product',10,'192.168.0.8','mobile','Handheld Browser','Android',NULL,NULL,NULL,NULL,'http://192.168.0.175/staylbd/product/details/t-shirt-ti-sart/10','2026-03-13 05:24:55','2026-03-13 05:24:55'),(450,NULL,'FjIqqr6DgSNMFLuatcz0l9kumM57if57uGDUJGjH','cart_add','Added to cart: T-shirt, টি-শার্ট','product',10,'192.168.0.8','mobile','Handheld Browser','Android',NULL,NULL,NULL,NULL,'http://192.168.0.175/staylbd/cart-list/add','2026-03-13 05:25:30','2026-03-13 05:25:30'),(451,NULL,'m0yy7tbWPKGjI2qA8xamd6GAWtB0hpVN7YfOKewz','cart_add','Added to cart: T-shirt, টি-শার্ট','product',7,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-03-13 05:31:02','2026-03-13 05:31:02'),(452,NULL,'m0yy7tbWPKGjI2qA8xamd6GAWtB0hpVN7YfOKewz','compare_add','Added to compare: T-shirt, টি-শার্ট','product',7,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/compare/add','2026-03-13 05:31:02','2026-03-13 05:31:02'),(453,NULL,'m0yy7tbWPKGjI2qA8xamd6GAWtB0hpVN7YfOKewz','wishlist_add','Added to wishlist: T-shirt, টি-শার্ট','product',7,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/wish-list/add','2026-03-13 05:31:03','2026-03-13 05:31:03'),(454,NULL,'m0yy7tbWPKGjI2qA8xamd6GAWtB0hpVN7YfOKewz','cart_add','Added to cart: Affordable Custom Cricket Jersey With Sublimation Printing','product',5,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-03-13 05:31:05','2026-03-13 05:31:05'),(455,NULL,'m0yy7tbWPKGjI2qA8xamd6GAWtB0hpVN7YfOKewz','compare_add','Added to compare: Affordable Custom Cricket Jersey With Sublimation Printing','product',5,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/compare/add','2026-03-13 05:31:05','2026-03-13 05:31:05'),(456,NULL,'m0yy7tbWPKGjI2qA8xamd6GAWtB0hpVN7YfOKewz','wishlist_add','Added to wishlist: Affordable Custom Cricket Jersey With Sublimation Printing','product',5,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/wish-list/add','2026-03-13 05:31:06','2026-03-13 05:31:06'),(457,NULL,'m0yy7tbWPKGjI2qA8xamd6GAWtB0hpVN7YfOKewz','cart_add','Added to cart: স্ক্রিনশট দিয়েছি  Affordable Custom Cricket Jersey With Sublimation Printing','product',4,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-03-13 05:31:08','2026-03-13 05:31:08'),(458,NULL,'m0yy7tbWPKGjI2qA8xamd6GAWtB0hpVN7YfOKewz','compare_add','Added to compare: স্ক্রিনশট দিয়েছি  Affordable Custom Cricket Jersey With Sublimation Printing','product',4,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/compare/add','2026-03-13 05:31:08','2026-03-13 05:31:08'),(459,NULL,'m0yy7tbWPKGjI2qA8xamd6GAWtB0hpVN7YfOKewz','wishlist_add','Added to wishlist: স্ক্রিনশট দিয়েছি  Affordable Custom Cricket Jersey With Sublimation Printing','product',4,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/wish-list/add','2026-03-13 05:31:08','2026-03-13 05:31:08'),(460,NULL,'m0yy7tbWPKGjI2qA8xamd6GAWtB0hpVN7YfOKewz','product_view','Viewed product: Affordable Custom Cricket Jersey With Sublimation Printing','product',5,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/affordable-custom-cricket-jersey-with-sublimation-printing/5','2026-03-13 05:31:12','2026-03-13 05:31:12'),(461,NULL,'jk9QyWa3c134IZsF2KgaXCMUpq3ghbV8ItFSgGzm','cart_add','Added to cart: T-shirt, টি-শার্ট','product',8,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-03-13 05:34:45','2026-03-13 05:34:45'),(462,NULL,'jk9QyWa3c134IZsF2KgaXCMUpq3ghbV8ItFSgGzm','compare_add','Added to compare: T-shirt, টি-শার্ট','product',8,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/compare/add','2026-03-13 05:34:46','2026-03-13 05:34:46'),(463,NULL,'jk9QyWa3c134IZsF2KgaXCMUpq3ghbV8ItFSgGzm','wishlist_add','Added to wishlist: T-shirt, টি-শার্ট','product',8,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/wish-list/add','2026-03-13 05:34:47','2026-03-13 05:34:47'),(464,NULL,'jk9QyWa3c134IZsF2KgaXCMUpq3ghbV8ItFSgGzm','cart_add','Added to cart: Affordable Custom Cricket Jersey With Sublimation Printing','product',5,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-03-13 05:34:51','2026-03-13 05:34:51'),(465,NULL,'jk9QyWa3c134IZsF2KgaXCMUpq3ghbV8ItFSgGzm','compare_add','Added to compare: Affordable Custom Cricket Jersey With Sublimation Printing','product',5,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/compare/add','2026-03-13 05:34:51','2026-03-13 05:34:51'),(466,NULL,'jk9QyWa3c134IZsF2KgaXCMUpq3ghbV8ItFSgGzm','wishlist_add','Added to wishlist: Affordable Custom Cricket Jersey With Sublimation Printing','product',5,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/wish-list/add','2026-03-13 05:34:52','2026-03-13 05:34:52'),(467,NULL,'jk9QyWa3c134IZsF2KgaXCMUpq3ghbV8ItFSgGzm','product_view','Viewed product: Affordable Custom Cricket Jersey With Sublimation Printing','product',5,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/affordable-custom-cricket-jersey-with-sublimation-printing/5','2026-03-13 05:35:17','2026-03-13 05:35:17'),(468,NULL,'jk9QyWa3c134IZsF2KgaXCMUpq3ghbV8ItFSgGzm','cart_add','Added to cart: Affordable Custom Cricket Jersey With Sublimation Printing','product',5,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-03-13 05:35:23','2026-03-13 05:35:23'),(469,NULL,'jk9QyWa3c134IZsF2KgaXCMUpq3ghbV8ItFSgGzm','product_view','Viewed product: Affordable Custom Cricket Jersey With Sublimation Printing','product',5,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/affordable-custom-cricket-jersey-with-sublimation-printing/5','2026-03-13 05:41:06','2026-03-13 05:41:06'),(470,NULL,'m0yy7tbWPKGjI2qA8xamd6GAWtB0hpVN7YfOKewz','product_view','Viewed product: T-shirt, টি-শার্ট','product',7,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/t-shirt-ti-sart/7','2026-03-13 06:00:38','2026-03-13 06:00:38'),(471,NULL,'m0yy7tbWPKGjI2qA8xamd6GAWtB0hpVN7YfOKewz','cart_add','Added to cart: T-shirt, টি-শার্ট','product',7,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-03-13 06:00:46','2026-03-13 06:00:46'),(472,NULL,'m0yy7tbWPKGjI2qA8xamd6GAWtB0hpVN7YfOKewz','product_view','Viewed product: Affordable Custom Cricket Jersey With Sublimation Printing','product',5,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/affordable-custom-cricket-jersey-with-sublimation-printing/5','2026-03-13 06:51:42','2026-03-13 06:51:42'),(473,NULL,'m0yy7tbWPKGjI2qA8xamd6GAWtB0hpVN7YfOKewz','search_text','Search: action',NULL,NULL,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/search/universal','2026-03-13 07:06:06','2026-03-13 07:06:06'),(474,NULL,'m0yy7tbWPKGjI2qA8xamd6GAWtB0hpVN7YfOKewz','cart_add','Added to cart: T-shirt, টি-শার্ট','product',8,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-03-13 07:11:12','2026-03-13 07:11:12'),(475,NULL,'m0yy7tbWPKGjI2qA8xamd6GAWtB0hpVN7YfOKewz','compare_add','Added to compare: T-shirt, টি-শার্ট','product',8,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/compare/add','2026-03-13 07:11:13','2026-03-13 07:11:13'),(476,NULL,'m0yy7tbWPKGjI2qA8xamd6GAWtB0hpVN7YfOKewz','wishlist_add','Added to wishlist: T-shirt, টি-শার্ট','product',8,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/wish-list/add','2026-03-13 07:11:14','2026-03-13 07:11:14'),(477,NULL,'m0yy7tbWPKGjI2qA8xamd6GAWtB0hpVN7YfOKewz','compare_add','Added to compare: T-shirt, টি-শার্ট','product',9,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/compare/add','2026-03-13 07:11:29','2026-03-13 07:11:29'),(478,NULL,'m0yy7tbWPKGjI2qA8xamd6GAWtB0hpVN7YfOKewz','cart_add','Added to cart: T-shirt, টি-শার্ট','product',9,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-03-13 07:11:30','2026-03-13 07:11:30'),(479,NULL,'m0yy7tbWPKGjI2qA8xamd6GAWtB0hpVN7YfOKewz','wishlist_add','Added to wishlist: T-shirt, টি-শার্ট','product',9,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/wish-list/add','2026-03-13 07:11:31','2026-03-13 07:11:31'),(480,NULL,'m0yy7tbWPKGjI2qA8xamd6GAWtB0hpVN7YfOKewz','cart_add','Added to cart: T-shirt, টি-শার্ট','product',10,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-03-13 07:11:35','2026-03-13 07:11:35'),(481,NULL,'m0yy7tbWPKGjI2qA8xamd6GAWtB0hpVN7YfOKewz','compare_add','Added to compare: T-shirt, টি-শার্ট','product',10,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/compare/add','2026-03-13 07:11:35','2026-03-13 07:11:35'),(482,NULL,'m0yy7tbWPKGjI2qA8xamd6GAWtB0hpVN7YfOKewz','wishlist_add','Added to wishlist: T-shirt, টি-শার্ট','product',10,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/wish-list/add','2026-03-13 07:11:36','2026-03-13 07:11:36'),(483,NULL,'FjIqqr6DgSNMFLuatcz0l9kumM57if57uGDUJGjH','product_view','Viewed product: T-shirt, টি-শার্ট','product',10,'192.168.0.8','mobile','Handheld Browser','Android',NULL,NULL,NULL,NULL,'http://192.168.0.175/staylbd/product/details/t-shirt-ti-sart/10','2026-03-13 07:12:15','2026-03-13 07:12:15'),(484,NULL,'m0yy7tbWPKGjI2qA8xamd6GAWtB0hpVN7YfOKewz','compare_add','Added to compare: WinTerSMM','product',2,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/compare/add','2026-03-13 07:44:23','2026-03-13 07:44:23'),(485,NULL,'m0yy7tbWPKGjI2qA8xamd6GAWtB0hpVN7YfOKewz','compare_add','Added to compare: Cricket Jersey || Affordable Custom Cricket Jersey With Sublimation Printing','product',3,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/compare/add','2026-03-13 07:44:26','2026-03-13 07:44:26'),(486,NULL,'hktait89zrO2A2udioTjywpeKMCi1GUwQOE6xAkr','product_view','Viewed product: Affordable Custom Cricket Jersey With Sublimation Printing','product',5,'127.0.0.1','mobile','Handheld Browser','Android',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/affordable-custom-cricket-jersey-with-sublimation-printing/5','2026-03-13 08:58:46','2026-03-13 08:58:46'),(487,NULL,'hktait89zrO2A2udioTjywpeKMCi1GUwQOE6xAkr','product_view','Viewed product: Affordable Custom Cricket Jersey With Sublimation Printing','product',5,'127.0.0.1','mobile','Handheld Browser','Android',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/affordable-custom-cricket-jersey-with-sublimation-printing/5','2026-03-13 08:59:10','2026-03-13 08:59:10'),(488,NULL,'hktait89zrO2A2udioTjywpeKMCi1GUwQOE6xAkr','cart_add','Added to cart: Affordable Custom Cricket Jersey With Sublimation Printing','product',5,'127.0.0.1','mobile','Handheld Browser','Android',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-03-13 08:59:20','2026-03-13 08:59:20'),(489,NULL,'hktait89zrO2A2udioTjywpeKMCi1GUwQOE6xAkr','product_view','Viewed product: Affordable Custom Cricket Jersey With Sublimation Printing','product',5,'127.0.0.1','mobile','Handheld Browser','Android',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/affordable-custom-cricket-jersey-with-sublimation-printing/5','2026-03-13 08:59:47','2026-03-13 08:59:47'),(490,NULL,'RDmxyY7rCGTdYB30g67paEAxxe9V9ko0brdsuTb0','compare_add','Added to compare: Affordable Custom Cricket Jersey With Sublimation Printing','product',5,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/compare/add','2026-03-13 09:03:30','2026-03-13 09:03:30'),(491,NULL,'RDmxyY7rCGTdYB30g67paEAxxe9V9ko0brdsuTb0','cart_add','Added to cart: Affordable Custom Cricket Jersey With Sublimation Printing','product',5,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-03-13 09:03:31','2026-03-13 09:03:31'),(492,NULL,'RDmxyY7rCGTdYB30g67paEAxxe9V9ko0brdsuTb0','wishlist_add','Added to wishlist: Affordable Custom Cricket Jersey With Sublimation Printing','product',5,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/wish-list/add','2026-03-13 09:03:32','2026-03-13 09:03:32'),(493,NULL,'RDmxyY7rCGTdYB30g67paEAxxe9V9ko0brdsuTb0','cart_add','Added to cart: স্ক্রিনশট দিয়েছি  Affordable Custom Cricket Jersey With Sublimation Printing','product',4,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-03-13 09:03:38','2026-03-13 09:03:38'),(494,NULL,'RDmxyY7rCGTdYB30g67paEAxxe9V9ko0brdsuTb0','compare_add','Added to compare: স্ক্রিনশট দিয়েছি  Affordable Custom Cricket Jersey With Sublimation Printing','product',4,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/compare/add','2026-03-13 09:03:39','2026-03-13 09:03:39'),(495,NULL,'RDmxyY7rCGTdYB30g67paEAxxe9V9ko0brdsuTb0','wishlist_add','Added to wishlist: স্ক্রিনশট দিয়েছি  Affordable Custom Cricket Jersey With Sublimation Printing','product',4,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/wish-list/add','2026-03-13 09:03:39','2026-03-13 09:03:39'),(496,NULL,'RDmxyY7rCGTdYB30g67paEAxxe9V9ko0brdsuTb0','cart_add','Added to cart: Cricket Jersey || Affordable Custom Cricket Jersey With Sublimation Printing','product',3,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-03-13 09:03:41','2026-03-13 09:03:41'),(497,NULL,'RDmxyY7rCGTdYB30g67paEAxxe9V9ko0brdsuTb0','compare_add','Added to compare: Cricket Jersey || Affordable Custom Cricket Jersey With Sublimation Printing','product',3,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/compare/add','2026-03-13 09:03:42','2026-03-13 09:03:42'),(498,NULL,'RDmxyY7rCGTdYB30g67paEAxxe9V9ko0brdsuTb0','wishlist_add','Added to wishlist: Cricket Jersey || Affordable Custom Cricket Jersey With Sublimation Printing','product',3,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/wish-list/add','2026-03-13 09:03:43','2026-03-13 09:03:43'),(499,NULL,'RDmxyY7rCGTdYB30g67paEAxxe9V9ko0brdsuTb0','compare_add','Added to compare: T-shirt, টি-শার্ট','product',7,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/compare/add','2026-03-13 09:04:15','2026-03-13 09:04:15'),(500,NULL,'RDmxyY7rCGTdYB30g67paEAxxe9V9ko0brdsuTb0','compare_add','Added to compare: T-shirt, টি-শার্ট','product',8,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/compare/add','2026-03-13 09:04:16','2026-03-13 09:04:16'),(501,NULL,'RDmxyY7rCGTdYB30g67paEAxxe9V9ko0brdsuTb0','compare_add','Added to compare: T-shirt, টি-শার্ট','product',9,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/compare/add','2026-03-13 09:04:18','2026-03-13 09:04:18'),(502,NULL,'hktait89zrO2A2udioTjywpeKMCi1GUwQOE6xAkr','product_view','Viewed product: Affordable Custom Cricket Jersey With Sublimation Printing','product',5,'127.0.0.1','mobile','Handheld Browser','Android',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/affordable-custom-cricket-jersey-with-sublimation-printing/5','2026-03-13 09:04:58','2026-03-13 09:04:58'),(503,NULL,'hktait89zrO2A2udioTjywpeKMCi1GUwQOE6xAkr','compare_add','Added to compare: T-shirt, টি-শার্ট','product',10,'127.0.0.1','mobile','Handheld Browser','Android',NULL,NULL,NULL,NULL,'http://localhost/staylbd/compare/add','2026-03-13 09:05:14','2026-03-13 09:05:14'),(504,NULL,'RDmxyY7rCGTdYB30g67paEAxxe9V9ko0brdsuTb0','compare_add','Added to compare: Cricket Jersey || Affordable Custom Cricket Jersey With Sublimation Printing','product',3,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/compare/add','2026-03-13 09:23:37','2026-03-13 09:23:37'),(505,NULL,'RDmxyY7rCGTdYB30g67paEAxxe9V9ko0brdsuTb0','compare_add','Added to compare: স্ক্রিনশট দিয়েছি  Affordable Custom Cricket Jersey With Sublimation Printing','product',4,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/compare/add','2026-03-13 09:23:38','2026-03-13 09:23:38'),(506,NULL,'RDmxyY7rCGTdYB30g67paEAxxe9V9ko0brdsuTb0','compare_add','Added to compare: Affordable Custom Cricket Jersey With Sublimation Printing','product',5,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/compare/add','2026-03-13 09:23:40','2026-03-13 09:23:40'),(507,NULL,'RDmxyY7rCGTdYB30g67paEAxxe9V9ko0brdsuTb0','compare_add','Added to compare: WinTerSMM','product',2,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/compare/add','2026-03-13 09:23:43','2026-03-13 09:23:43'),(508,NULL,'hktait89zrO2A2udioTjywpeKMCi1GUwQOE6xAkr','product_view','Viewed product: T-shirt, টি-শার্ট','product',7,'127.0.0.1','mobile','Handheld Browser','Android',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/t-shirt-ti-sart/7','2026-03-13 09:40:04','2026-03-13 09:40:04'),(509,NULL,'hktait89zrO2A2udioTjywpeKMCi1GUwQOE6xAkr','product_view','Viewed product: T-shirt, টি-শার্ট','product',7,'127.0.0.1','mobile','Handheld Browser','Android',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/t-shirt-ti-sart/7','2026-03-13 10:45:05','2026-03-13 10:45:05'),(510,NULL,'hktait89zrO2A2udioTjywpeKMCi1GUwQOE6xAkr','wishlist_add','Added to wishlist: T-shirt, টি-শার্ট','product',10,'127.0.0.1','mobile','Handheld Browser','Android',NULL,NULL,NULL,NULL,'http://localhost/staylbd/wish-list/add','2026-03-13 10:46:00','2026-03-13 10:46:00'),(511,NULL,'hktait89zrO2A2udioTjywpeKMCi1GUwQOE6xAkr','product_view','Viewed product: T-shirt, টি-শার্ট','product',7,'127.0.0.1','mobile','Handheld Browser','Android',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/t-shirt-ti-sart/7','2026-03-13 10:46:18','2026-03-13 10:46:18'),(512,NULL,'RDmxyY7rCGTdYB30g67paEAxxe9V9ko0brdsuTb0','product_view','Viewed product: Affordable Custom Cricket Jersey With Sublimation Printing','product',5,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/affordable-custom-cricket-jersey-with-sublimation-printing/5','2026-03-13 12:52:05','2026-03-13 12:52:05'),(513,NULL,'M2rW0sQ6c0mbE8Ieet11Rh3nwibgEaYKa65gZDjZ','product_view','Viewed product: Affordable Custom Cricket Jersey With Sublimation Printing','product',5,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/affordable-custom-cricket-jersey-with-sublimation-printing/5','2026-03-14 08:24:55','2026-03-14 08:24:55'),(514,NULL,'M2rW0sQ6c0mbE8Ieet11Rh3nwibgEaYKa65gZDjZ','product_view','Viewed product: Affordable Custom Cricket Jersey With Sublimation Printing','product',5,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/affordable-custom-cricket-jersey-with-sublimation-printing/5','2026-03-14 11:05:48','2026-03-14 11:05:48'),(515,NULL,'M2rW0sQ6c0mbE8Ieet11Rh3nwibgEaYKa65gZDjZ','product_view','Viewed product: Affordable Custom Cricket Jersey With Sublimation Printing','product',5,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/affordable-custom-cricket-jersey-with-sublimation-printing/5','2026-03-14 11:06:25','2026-03-14 11:06:25'),(516,NULL,'3KOdjjcBQRcClk8WwLAKasTFLA2H9uXAGV7ggsbc','product_view','Viewed product: Affordable Custom Cricket Jersey With Sublimation Printing','product',5,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/affordable-custom-cricket-jersey-with-sublimation-printing/5','2026-03-14 11:07:18','2026-03-14 11:07:18'),(517,NULL,'3KOdjjcBQRcClk8WwLAKasTFLA2H9uXAGV7ggsbc','product_view','Viewed product: Affordable Custom Cricket Jersey With Sublimation Printing','product',5,'127.0.0.1','mobile','Handheld Browser','Android',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/affordable-custom-cricket-jersey-with-sublimation-printing/5','2026-03-14 11:07:52','2026-03-14 11:07:52'),(518,NULL,'UstGmNmFkeaYUVVAkqPG99gxgbpQGyJsLM0G58HS','product_view','Viewed product: Affordable Custom Cricket Jersey With Sublimation Printing','product',5,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/affordable-custom-cricket-jersey-with-sublimation-printing/5','2026-03-14 11:08:53','2026-03-14 11:08:53'),(519,NULL,'M2rW0sQ6c0mbE8Ieet11Rh3nwibgEaYKa65gZDjZ','cart_add','Added to cart: T-shirt, টি-শার্ট','product',7,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-03-14 11:12:43','2026-03-14 11:12:43'),(520,NULL,'M2rW0sQ6c0mbE8Ieet11Rh3nwibgEaYKa65gZDjZ','cart_add','Added to cart: Affordable Custom Cricket Jersey With Sublimation Printing','product',5,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-03-14 11:12:44','2026-03-14 11:12:44'),(521,NULL,'M2rW0sQ6c0mbE8Ieet11Rh3nwibgEaYKa65gZDjZ','cart_add','Added to cart: স্ক্রিনশট দিয়েছি  Affordable Custom Cricket Jersey With Sublimation Printing','product',4,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-03-14 11:12:47','2026-03-14 11:12:47'),(522,NULL,'M2rW0sQ6c0mbE8Ieet11Rh3nwibgEaYKa65gZDjZ','cart_add','Added to cart: Cricket Jersey || Affordable Custom Cricket Jersey With Sublimation Printing','product',3,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-03-14 11:12:48','2026-03-14 11:12:48'),(523,NULL,'M2rW0sQ6c0mbE8Ieet11Rh3nwibgEaYKa65gZDjZ','cart_add','Added to cart: T-shirt, টি-শার্ট','product',8,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-03-14 11:12:54','2026-03-14 11:12:54'),(524,NULL,'M2rW0sQ6c0mbE8Ieet11Rh3nwibgEaYKa65gZDjZ','cart_add','Added to cart: T-shirt, টি-শার্ট','product',9,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-03-14 11:12:56','2026-03-14 11:12:56'),(525,NULL,'M2rW0sQ6c0mbE8Ieet11Rh3nwibgEaYKa65gZDjZ','wishlist_add','Added to wishlist: স্ক্রিনশট দিয়েছি  Affordable Custom Cricket Jersey With Sublimation Printing','product',4,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/wish-list/add','2026-03-14 11:13:24','2026-03-14 11:13:24'),(526,NULL,'M2rW0sQ6c0mbE8Ieet11Rh3nwibgEaYKa65gZDjZ','wishlist_add','Added to wishlist: Affordable Custom Cricket Jersey With Sublimation Printing','product',5,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/wish-list/add','2026-03-14 11:13:26','2026-03-14 11:13:26'),(527,NULL,'M2rW0sQ6c0mbE8Ieet11Rh3nwibgEaYKa65gZDjZ','wishlist_add','Added to wishlist: T-shirt, টি-শার্ট','product',7,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/wish-list/add','2026-03-14 11:13:27','2026-03-14 11:13:27'),(528,NULL,'M2rW0sQ6c0mbE8Ieet11Rh3nwibgEaYKa65gZDjZ','wishlist_add','Added to wishlist: T-shirt, টি-শার্ট','product',9,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/wish-list/add','2026-03-14 11:13:28','2026-03-14 11:13:28'),(529,NULL,'M2rW0sQ6c0mbE8Ieet11Rh3nwibgEaYKa65gZDjZ','wishlist_add','Added to wishlist: T-shirt, টি-শার্ট','product',8,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/wish-list/add','2026-03-14 11:13:30','2026-03-14 11:13:30'),(530,NULL,'M2rW0sQ6c0mbE8Ieet11Rh3nwibgEaYKa65gZDjZ','wishlist_add','Added to wishlist: Cricket Jersey || Affordable Custom Cricket Jersey With Sublimation Printing','product',3,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/wish-list/add','2026-03-14 11:13:35','2026-03-14 11:13:35'),(531,NULL,'G5R4u6SGxNkTFZrk3Wz7qpBeep9dAVDXpXABWtMh','product_view','Viewed product: T-shirt, টি-শার্ট','product',7,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/t-shirt-ti-sart/7','2026-03-15 08:07:38','2026-03-15 08:07:38'),(532,NULL,'T2GjiNo4DgLEFkVAUA6TZN8N9J8UKZQ8DweHfXkF','product_view','Viewed product: Affordable Custom Cricket Jersey With Sublimation Printing','product',5,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/affordable-custom-cricket-jersey-with-sublimation-printing/5','2026-03-15 08:08:14','2026-03-15 08:08:14'),(533,NULL,'G5R4u6SGxNkTFZrk3Wz7qpBeep9dAVDXpXABWtMh','product_view','Viewed product: T-shirt, টি-শার্ট','product',7,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/t-shirt-ti-sart/7','2026-03-15 08:16:32','2026-03-15 08:16:32'),(534,NULL,'G5R4u6SGxNkTFZrk3Wz7qpBeep9dAVDXpXABWtMh','product_view','Viewed product: T-shirt, টি-শার্ট','product',7,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/t-shirt-ti-sart/7','2026-03-15 08:19:55','2026-03-15 08:19:55'),(535,NULL,'G5R4u6SGxNkTFZrk3Wz7qpBeep9dAVDXpXABWtMh','product_view','Viewed product: T-shirt, টি-শার্ট','product',7,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/t-shirt-ti-sart/7','2026-03-15 08:43:29','2026-03-15 08:43:29'),(536,NULL,'G5R4u6SGxNkTFZrk3Wz7qpBeep9dAVDXpXABWtMh','product_view','Viewed product: T-shirt, টি-শার্ট','product',7,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/t-shirt-ti-sart/7','2026-03-15 08:43:38','2026-03-15 08:43:38'),(537,NULL,'G5R4u6SGxNkTFZrk3Wz7qpBeep9dAVDXpXABWtMh','product_view','Viewed product: T-shirt, টি-শার্ট','product',7,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/t-shirt-ti-sart/7','2026-03-15 08:57:28','2026-03-15 08:57:28'),(538,NULL,'G5R4u6SGxNkTFZrk3Wz7qpBeep9dAVDXpXABWtMh','product_view','Viewed product: T-shirt, টি-শার্ট','product',7,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/t-shirt-ti-sart/7','2026-03-15 08:57:39','2026-03-15 08:57:39'),(539,NULL,'I9rdCTiUN4opFmZOQbZlddf3t2wbNOc14IhbonLy','product_view','Viewed product: T-shirt, টি-শার্ট','product',9,'192.168.0.227','mobile','Handheld Browser','Android',NULL,NULL,NULL,NULL,'http://192.168.0.175/staylbd/product/details/t-shirt-ti-sart/9','2026-03-15 09:38:03','2026-03-15 09:38:03'),(540,NULL,'I9rdCTiUN4opFmZOQbZlddf3t2wbNOc14IhbonLy','wishlist_add','Added to wishlist: T-shirt, টি-শার্ট','product',10,'192.168.0.227','mobile','Handheld Browser','Android',NULL,NULL,NULL,NULL,'http://192.168.0.175/staylbd/wish-list/add','2026-03-15 09:59:10','2026-03-15 09:59:10'),(541,NULL,'I9rdCTiUN4opFmZOQbZlddf3t2wbNOc14IhbonLy','compare_add','Added to compare: T-shirt, টি-শার্ট','product',10,'192.168.0.227','mobile','Handheld Browser','Android',NULL,NULL,NULL,NULL,'http://192.168.0.175/staylbd/compare/add','2026-03-15 09:59:11','2026-03-15 09:59:11'),(542,NULL,'I9rdCTiUN4opFmZOQbZlddf3t2wbNOc14IhbonLy','compare_add','Added to compare: T-shirt, টি-শার্ট','product',9,'192.168.0.227','mobile','Handheld Browser','Android',NULL,NULL,NULL,NULL,'http://192.168.0.175/staylbd/compare/add','2026-03-15 09:59:23','2026-03-15 09:59:23'),(543,NULL,'I9rdCTiUN4opFmZOQbZlddf3t2wbNOc14IhbonLy','wishlist_add','Added to wishlist: T-shirt, টি-শার্ট','product',9,'192.168.0.227','mobile','Handheld Browser','Android',NULL,NULL,NULL,NULL,'http://192.168.0.175/staylbd/wish-list/add','2026-03-15 09:59:25','2026-03-15 09:59:25'),(544,NULL,'I9rdCTiUN4opFmZOQbZlddf3t2wbNOc14IhbonLy','product_view','Viewed product: T-shirt, টি-শার্ট','product',7,'192.168.0.227','mobile','Handheld Browser','Android',NULL,NULL,NULL,NULL,'http://192.168.0.175/staylbd/product/details/t-shirt-ti-sart/7','2026-03-15 09:59:33','2026-03-15 09:59:33'),(545,NULL,'I9rdCTiUN4opFmZOQbZlddf3t2wbNOc14IhbonLy','wishlist_add','Added to wishlist: T-shirt, টি-শার্ট','product',7,'192.168.0.227','mobile','Handheld Browser','Android',NULL,NULL,NULL,NULL,'http://192.168.0.175/staylbd/wish-list/add','2026-03-15 09:59:57','2026-03-15 09:59:57'),(546,NULL,'DYVISFKi0edCgfIuRbVn2AvU24W9myKxAjFzwbFh','product_view','Viewed product: T-shirt, টি-শার্ট','product',7,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/t-shirt-ti-sart/7','2026-03-16 12:11:14','2026-03-16 12:11:14'),(547,NULL,'XArDHqBxdsH29AHC8SskzrGTrzeEX2u4Gt5mTfrD','product_view','Viewed product: T-shirt, টি-শার্ট','product',7,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/t-shirt-ti-sart/7','2026-03-17 07:19:01','2026-03-17 07:19:01'),(548,NULL,'XArDHqBxdsH29AHC8SskzrGTrzeEX2u4Gt5mTfrD','product_view','Viewed product: T-shirt, টি-শার্ট','product',7,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/t-shirt-ti-sart/7','2026-03-17 07:21:50','2026-03-17 07:21:50'),(549,NULL,'XArDHqBxdsH29AHC8SskzrGTrzeEX2u4Gt5mTfrD','product_view','Viewed product: Affordable Custom Cricket Jersey With Sublimation Printing','product',5,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/affordable-custom-cricket-jersey-with-sublimation-printing/5','2026-03-17 08:15:28','2026-03-17 08:15:28'),(550,NULL,'XArDHqBxdsH29AHC8SskzrGTrzeEX2u4Gt5mTfrD','cart_add','Added to cart: Affordable Custom Cricket Jersey With Sublimation Printing','product',5,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-03-17 08:26:06','2026-03-17 08:26:06'),(551,NULL,'XArDHqBxdsH29AHC8SskzrGTrzeEX2u4Gt5mTfrD','product_view','Viewed product: Affordable Custom Cricket Jersey With Sublimation Printing','product',5,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/affordable-custom-cricket-jersey-with-sublimation-printing/5','2026-03-17 08:26:23','2026-03-17 08:26:23'),(552,NULL,'XArDHqBxdsH29AHC8SskzrGTrzeEX2u4Gt5mTfrD','product_view','Viewed product: Affordable Custom Cricket Jersey With Sublimation Printing','product',5,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/affordable-custom-cricket-jersey-with-sublimation-printing/5','2026-03-17 08:26:34','2026-03-17 08:26:34'),(553,NULL,'XArDHqBxdsH29AHC8SskzrGTrzeEX2u4Gt5mTfrD','product_view','Viewed product: Affordable Custom Cricket Jersey With Sublimation Printing','product',5,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/affordable-custom-cricket-jersey-with-sublimation-printing/5','2026-03-17 09:02:48','2026-03-17 09:02:48'),(554,NULL,'XArDHqBxdsH29AHC8SskzrGTrzeEX2u4Gt5mTfrD','product_view','Viewed product: Affordable Custom Cricket Jersey With Sublimation Printing','product',5,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/affordable-custom-cricket-jersey-with-sublimation-printing/5','2026-03-17 09:22:44','2026-03-17 09:22:44'),(555,NULL,'XArDHqBxdsH29AHC8SskzrGTrzeEX2u4Gt5mTfrD','product_view','Viewed product: Affordable Custom Cricket Jersey With Sublimation Printing','product',5,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/affordable-custom-cricket-jersey-with-sublimation-printing/5','2026-03-17 09:23:03','2026-03-17 09:23:03'),(556,NULL,'XArDHqBxdsH29AHC8SskzrGTrzeEX2u4Gt5mTfrD','product_view','Viewed product: Affordable Custom Cricket Jersey With Sublimation Printing','product',5,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/affordable-custom-cricket-jersey-with-sublimation-printing/5','2026-03-17 09:27:56','2026-03-17 09:27:56'),(557,NULL,'XArDHqBxdsH29AHC8SskzrGTrzeEX2u4Gt5mTfrD','product_view','Viewed product: Affordable Custom Cricket Jersey With Sublimation Printing','product',5,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/affordable-custom-cricket-jersey-with-sublimation-printing/5','2026-03-17 09:30:01','2026-03-17 09:30:01'),(558,NULL,'XArDHqBxdsH29AHC8SskzrGTrzeEX2u4Gt5mTfrD','product_view','Viewed product: T-shirt, টি-শার্ট','product',7,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/t-shirt-ti-sart/7','2026-03-17 09:32:10','2026-03-17 09:32:10'),(559,NULL,'XArDHqBxdsH29AHC8SskzrGTrzeEX2u4Gt5mTfrD','wishlist_remove','Removed from wishlist','product',7,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/wish-list/remove?product_id=7','2026-03-17 09:32:14','2026-03-17 09:32:14'),(560,NULL,'XArDHqBxdsH29AHC8SskzrGTrzeEX2u4Gt5mTfrD','product_view','Viewed product: T-shirt, টি-শার্ট','product',7,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/t-shirt-ti-sart/7','2026-03-17 09:32:27','2026-03-17 09:32:27'),(561,NULL,'XArDHqBxdsH29AHC8SskzrGTrzeEX2u4Gt5mTfrD','product_view','Viewed product: T-shirt, টি-শার্ট','product',7,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/t-shirt-ti-sart/7','2026-03-17 09:38:21','2026-03-17 09:38:21'),(562,NULL,'XArDHqBxdsH29AHC8SskzrGTrzeEX2u4Gt5mTfrD','product_view','Viewed product: Affordable Custom Cricket Jersey With Sublimation Printing','product',5,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/affordable-custom-cricket-jersey-with-sublimation-printing/5','2026-03-17 09:40:36','2026-03-17 09:40:36'),(563,NULL,'XArDHqBxdsH29AHC8SskzrGTrzeEX2u4Gt5mTfrD','product_view','Viewed product: Affordable Custom Cricket Jersey With Sublimation Printing','product',5,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/affordable-custom-cricket-jersey-with-sublimation-printing/5','2026-03-17 09:48:18','2026-03-17 09:48:18'),(564,NULL,'XArDHqBxdsH29AHC8SskzrGTrzeEX2u4Gt5mTfrD','product_view','Viewed product: Affordable Custom Cricket Jersey With Sublimation Printing','product',5,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/affordable-custom-cricket-jersey-with-sublimation-printing/5','2026-03-17 09:51:41','2026-03-17 09:51:41'),(565,NULL,'XArDHqBxdsH29AHC8SskzrGTrzeEX2u4Gt5mTfrD','product_view','Viewed product: Affordable Custom Cricket Jersey With Sublimation Printing','product',5,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/affordable-custom-cricket-jersey-with-sublimation-printing/5','2026-03-17 09:56:32','2026-03-17 09:56:32'),(566,NULL,'XArDHqBxdsH29AHC8SskzrGTrzeEX2u4Gt5mTfrD','product_view','Viewed product: Affordable Custom Cricket Jersey With Sublimation Printing','product',5,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/affordable-custom-cricket-jersey-with-sublimation-printing/5','2026-03-17 10:00:30','2026-03-17 10:00:30'),(567,NULL,'XArDHqBxdsH29AHC8SskzrGTrzeEX2u4Gt5mTfrD','product_view','Viewed product: Affordable Custom Cricket Jersey With Sublimation Printing','product',5,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/affordable-custom-cricket-jersey-with-sublimation-printing/5','2026-03-17 10:01:09','2026-03-17 10:01:09'),(568,NULL,'XArDHqBxdsH29AHC8SskzrGTrzeEX2u4Gt5mTfrD','product_view','Viewed product: Affordable Custom Cricket Jersey With Sublimation Printing','product',5,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/affordable-custom-cricket-jersey-with-sublimation-printing/5','2026-03-17 10:03:13','2026-03-17 10:03:13'),(569,NULL,'rgsMLgtvX2k7Vgpl34lkzrGoNxzwzVVevdLlANKp','product_view','Viewed product: Affordable Custom Cricket Jersey With Sublimation Printing','product',5,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/affordable-custom-cricket-jersey-with-sublimation-printing/5','2026-03-17 19:37:21','2026-03-17 19:37:21'),(570,NULL,'rgsMLgtvX2k7Vgpl34lkzrGoNxzwzVVevdLlANKp','product_view','Viewed product: Affordable Custom Cricket Jersey With Sublimation Printing','product',5,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/affordable-custom-cricket-jersey-with-sublimation-printing/5','2026-03-17 19:39:36','2026-03-17 19:39:36'),(571,NULL,'rgsMLgtvX2k7Vgpl34lkzrGoNxzwzVVevdLlANKp','product_view','Viewed product: Affordable Custom Cricket Jersey With Sublimation Printing','product',5,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/affordable-custom-cricket-jersey-with-sublimation-printing/5','2026-03-17 19:43:56','2026-03-17 19:43:56'),(572,NULL,'rgsMLgtvX2k7Vgpl34lkzrGoNxzwzVVevdLlANKp','product_view','Viewed product: Affordable Custom Cricket Jersey With Sublimation Printing','product',5,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/affordable-custom-cricket-jersey-with-sublimation-printing/5','2026-03-17 19:47:09','2026-03-17 19:47:09'),(573,NULL,'rgsMLgtvX2k7Vgpl34lkzrGoNxzwzVVevdLlANKp','product_view','Viewed product: Affordable Custom Cricket Jersey With Sublimation Printing','product',5,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/affordable-custom-cricket-jersey-with-sublimation-printing/5','2026-03-17 19:55:02','2026-03-17 19:55:02'),(574,NULL,'rgsMLgtvX2k7Vgpl34lkzrGoNxzwzVVevdLlANKp','product_view','Viewed product: Affordable Custom Cricket Jersey With Sublimation Printing','product',5,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/affordable-custom-cricket-jersey-with-sublimation-printing/5','2026-03-17 19:57:41','2026-03-17 19:57:41'),(575,NULL,'rgsMLgtvX2k7Vgpl34lkzrGoNxzwzVVevdLlANKp','product_view','Viewed product: Affordable Custom Cricket Jersey With Sublimation Printing','product',5,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/affordable-custom-cricket-jersey-with-sublimation-printing/5','2026-03-17 20:04:21','2026-03-17 20:04:21'),(576,NULL,'rgsMLgtvX2k7Vgpl34lkzrGoNxzwzVVevdLlANKp','product_view','Viewed product: Affordable Custom Cricket Jersey With Sublimation Printing','product',5,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/affordable-custom-cricket-jersey-with-sublimation-printing/5','2026-03-17 20:08:35','2026-03-17 20:08:35'),(577,NULL,'rgsMLgtvX2k7Vgpl34lkzrGoNxzwzVVevdLlANKp','product_view','Viewed product: Affordable Custom Cricket Jersey With Sublimation Printing','product',5,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/affordable-custom-cricket-jersey-with-sublimation-printing/5','2026-03-17 20:12:42','2026-03-17 20:12:42'),(578,NULL,'rgsMLgtvX2k7Vgpl34lkzrGoNxzwzVVevdLlANKp','product_view','Viewed product: Affordable Custom Cricket Jersey With Sublimation Printing','product',5,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/affordable-custom-cricket-jersey-with-sublimation-printing/5','2026-03-17 20:12:47','2026-03-17 20:12:47'),(579,NULL,'rgsMLgtvX2k7Vgpl34lkzrGoNxzwzVVevdLlANKp','product_view','Viewed product: Affordable Custom Cricket Jersey With Sublimation Printing','product',5,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/affordable-custom-cricket-jersey-with-sublimation-printing/5','2026-03-17 20:12:52','2026-03-17 20:12:52'),(580,NULL,'AdPsTudbkD1AUzOfmLN8SOBXbP2kkOHoAxKCC33m','product_view','Viewed product: T-shirt, টি-শার্ট','product',9,'192.168.0.49','mobile','Handheld Browser','Android',NULL,NULL,NULL,NULL,'http://192.168.0.175/staylbd/product/details/t-shirt-ti-sart/9','2026-03-17 20:15:16','2026-03-17 20:15:16'),(581,NULL,'rgsMLgtvX2k7Vgpl34lkzrGoNxzwzVVevdLlANKp','product_view','Viewed product: Affordable Custom Cricket Jersey With Sublimation Printing','product',5,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/affordable-custom-cricket-jersey-with-sublimation-printing/5','2026-03-17 20:21:28','2026-03-17 20:21:28'),(582,NULL,'AdPsTudbkD1AUzOfmLN8SOBXbP2kkOHoAxKCC33m','product_view','Viewed product: T-shirt, টি-শার্ট','product',9,'192.168.0.49','mobile','Handheld Browser','Android',NULL,NULL,NULL,NULL,'http://192.168.0.175/staylbd/product/details/t-shirt-ti-sart/9','2026-03-17 20:22:02','2026-03-17 20:22:02'),(583,NULL,'AdPsTudbkD1AUzOfmLN8SOBXbP2kkOHoAxKCC33m','product_view','Viewed product: T-shirt, টি-শার্ট','product',10,'192.168.0.49','mobile','Handheld Browser','Android',NULL,NULL,NULL,NULL,'http://192.168.0.175/staylbd/product/details/t-shirt-ti-sart/10','2026-03-17 20:22:19','2026-03-17 20:22:19'),(584,NULL,'rgsMLgtvX2k7Vgpl34lkzrGoNxzwzVVevdLlANKp','product_view','Viewed product: Affordable Custom Cricket Jersey With Sublimation Printing','product',5,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/affordable-custom-cricket-jersey-with-sublimation-printing/5','2026-03-17 20:22:38','2026-03-17 20:22:38'),(585,NULL,'rgsMLgtvX2k7Vgpl34lkzrGoNxzwzVVevdLlANKp','product_view','Viewed product: Affordable Custom Cricket Jersey With Sublimation Printing','product',5,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/affordable-custom-cricket-jersey-with-sublimation-printing/5','2026-03-17 20:32:27','2026-03-17 20:32:27'),(586,NULL,'rgsMLgtvX2k7Vgpl34lkzrGoNxzwzVVevdLlANKp','product_view','Viewed product: Affordable Custom Cricket Jersey With Sublimation Printing','product',5,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/affordable-custom-cricket-jersey-with-sublimation-printing/5','2026-03-17 20:36:17','2026-03-17 20:36:17'),(587,NULL,'rgsMLgtvX2k7Vgpl34lkzrGoNxzwzVVevdLlANKp','product_view','Viewed product: T-shirt, টি-শার্ট','product',7,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/t-shirt-ti-sart/7','2026-03-17 20:40:25','2026-03-17 20:40:25'),(588,NULL,'rgsMLgtvX2k7Vgpl34lkzrGoNxzwzVVevdLlANKp','product_view','Viewed product: Affordable Custom Cricket Jersey With Sublimation Printing','product',5,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/affordable-custom-cricket-jersey-with-sublimation-printing/5','2026-03-17 20:43:33','2026-03-17 20:43:33'),(589,NULL,'rgsMLgtvX2k7Vgpl34lkzrGoNxzwzVVevdLlANKp','product_view','Viewed product: Affordable Custom Cricket Jersey With Sublimation Printing','product',5,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/details/affordable-custom-cricket-jersey-with-sublimation-printing/5','2026-03-17 20:44:02','2026-03-17 20:44:02'),(590,NULL,'rgsMLgtvX2k7Vgpl34lkzrGoNxzwzVVevdLlANKp','product_view','Viewed product: Affordable Custom Cricket Jersey With Sublimation Printing','product',5,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/affordable-custom-cricket-jersey-with-sublimation-printing','2026-03-17 20:56:40','2026-03-17 20:56:40'),(591,NULL,'rgsMLgtvX2k7Vgpl34lkzrGoNxzwzVVevdLlANKp','product_view','Viewed product: T-shirt, টি-শার্ট','product',7,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/t-shirt-ti-sart','2026-03-17 21:02:44','2026-03-17 21:02:44'),(592,NULL,'rgsMLgtvX2k7Vgpl34lkzrGoNxzwzVVevdLlANKp','product_view','Viewed product: স্ক্রিনশট দিয়েছি  Affordable Custom Cricket Jersey With Sublimation Printing','product',4,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/skrinsht-dizechi-affordable-custom-cricket-jersey-with-sublimation-printing','2026-03-17 21:03:06','2026-03-17 21:03:06'),(593,NULL,'rgsMLgtvX2k7Vgpl34lkzrGoNxzwzVVevdLlANKp','product_view','Viewed product: স্ক্রিনশট দিয়েছি  Affordable Custom Cricket Jersey With Sublimation Printing','product',4,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/skrinsht-dizechi-affordable-custom-cricket-jersey-with-sublimation-printing','2026-03-17 21:03:10','2026-03-17 21:03:10'),(594,NULL,'rgsMLgtvX2k7Vgpl34lkzrGoNxzwzVVevdLlANKp','product_view','Viewed product: T-shirt, টি-শার্ট','product',8,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/t-shirt-ti-sart-8','2026-03-17 21:12:00','2026-03-17 21:12:00'),(595,NULL,'rgsMLgtvX2k7Vgpl34lkzrGoNxzwzVVevdLlANKp','product_view','Viewed product: T-shirt, টি-শার্ট','product',8,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/t-shirt-ti-sart-8','2026-03-17 21:22:04','2026-03-17 21:22:04'),(596,NULL,'rgsMLgtvX2k7Vgpl34lkzrGoNxzwzVVevdLlANKp','product_view','Viewed product: T-shirt, টি-শার্ট','product',8,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/t-shirt-ti-sart-8','2026-03-17 21:22:22','2026-03-17 21:22:22'),(597,NULL,'rgsMLgtvX2k7Vgpl34lkzrGoNxzwzVVevdLlANKp','product_view','Viewed product: RIAZUL ISLAM SHOJOL','product',1,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/riazul-islam-1','2026-03-17 22:04:35','2026-03-17 22:04:35'),(598,NULL,'rgsMLgtvX2k7Vgpl34lkzrGoNxzwzVVevdLlANKp','product_view','Viewed product: T-shirt, টি-শার্ট','product',8,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/t-shirt-ti-sart-8','2026-03-17 22:22:30','2026-03-17 22:22:30'),(599,NULL,'rgsMLgtvX2k7Vgpl34lkzrGoNxzwzVVevdLlANKp','cart_remove','Removed from cart: T-shirt, টি-শার্ট','product',8,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/remove','2026-03-17 22:24:21','2026-03-17 22:24:21'),(600,NULL,'rgsMLgtvX2k7Vgpl34lkzrGoNxzwzVVevdLlANKp','cart_add','Added to cart: T-shirt, টি-শার্ট','product',8,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-03-17 22:24:23','2026-03-17 22:24:23'),(601,NULL,'rgsMLgtvX2k7Vgpl34lkzrGoNxzwzVVevdLlANKp','cart_remove','Removed from cart: T-shirt, টি-শার্ট','product',8,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/remove','2026-03-17 22:24:25','2026-03-17 22:24:25'),(602,NULL,'rgsMLgtvX2k7Vgpl34lkzrGoNxzwzVVevdLlANKp','cart_add','Added to cart: T-shirt, টি-শার্ট','product',8,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-03-17 22:24:28','2026-03-17 22:24:28'),(603,NULL,'rgsMLgtvX2k7Vgpl34lkzrGoNxzwzVVevdLlANKp','product_view','Viewed product: T-shirt, টি-শার্ট','product',8,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/t-shirt-ti-sart-8','2026-03-17 22:24:37','2026-03-17 22:24:37'),(604,NULL,'rgsMLgtvX2k7Vgpl34lkzrGoNxzwzVVevdLlANKp','wishlist_add','Added to wishlist: T-shirt, টি-শার্ট','product',8,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/wish-list/add','2026-03-17 22:24:40','2026-03-17 22:24:40'),(605,NULL,'rgsMLgtvX2k7Vgpl34lkzrGoNxzwzVVevdLlANKp','wishlist_remove','Removed from wishlist','product',8,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/wish-list/remove?product_id=8','2026-03-17 22:24:45','2026-03-17 22:24:45'),(606,NULL,'rgsMLgtvX2k7Vgpl34lkzrGoNxzwzVVevdLlANKp','cart_remove','Removed from cart: T-shirt, টি-শার্ট','product',8,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/remove','2026-03-17 22:24:47','2026-03-17 22:24:47'),(607,NULL,'rgsMLgtvX2k7Vgpl34lkzrGoNxzwzVVevdLlANKp','wishlist_add','Added to wishlist: T-shirt, টি-শার্ট','product',8,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/wish-list/add','2026-03-17 22:24:59','2026-03-17 22:24:59'),(608,NULL,'rgsMLgtvX2k7Vgpl34lkzrGoNxzwzVVevdLlANKp','wishlist_remove','Removed from wishlist','product',8,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/wish-list/remove?product_id=8','2026-03-17 22:25:01','2026-03-17 22:25:01'),(609,NULL,'rgsMLgtvX2k7Vgpl34lkzrGoNxzwzVVevdLlANKp','product_view','Viewed product: স্ক্রিনশট দিয়েছি ভালোভাবে এনালাইসিস করে দেখুন সবকিছু ঠিক রয়েছে কিনা এবং প্রফেশনাল ভাবে সবকিছু দেখাচ্ছে কিনা','product',6,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/skrnsht-dzch-6','2026-03-17 22:35:40','2026-03-17 22:35:40'),(610,NULL,'rgsMLgtvX2k7Vgpl34lkzrGoNxzwzVVevdLlANKp','product_view','Viewed product: স্ক্রিনশট দিয়েছি ভালোভাবে এনালাইসিস করে দেখুন সবকিছু ঠিক রয়েছে কিনা এবং প্রফেশনাল ভাবে সবকিছু দেখাচ্ছে কিনা','product',6,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/skrnsht-dzch-6','2026-03-17 22:43:51','2026-03-17 22:43:51'),(611,NULL,'rgsMLgtvX2k7Vgpl34lkzrGoNxzwzVVevdLlANKp','product_view','Viewed product: স্ক্রিনশট দিয়েছি ভালোভাবে এনালাইসিস করে দেখুন সবকিছু ঠিক রয়েছে কিনা এবং প্রফেশনাল ভাবে সবকিছু দেখাচ্ছে কিনা','product',6,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/skrnsht-dzch-6','2026-03-17 22:50:44','2026-03-17 22:50:44'),(612,NULL,'rgsMLgtvX2k7Vgpl34lkzrGoNxzwzVVevdLlANKp','cart_add','Added to cart: স্ক্রিনশট দিয়েছি ভালোভাবে এনালাইসিস করে দেখুন সবকিছু ঠিক রয়েছে কিনা এবং প্রফেশনাল ভাবে সবকিছু দেখাচ্ছে কিনা','product',6,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-03-17 22:50:47','2026-03-17 22:50:47'),(613,NULL,'rgsMLgtvX2k7Vgpl34lkzrGoNxzwzVVevdLlANKp','cart_remove','Removed from cart: স্ক্রিনশট দিয়েছি ভালোভাবে এনালাইসিস করে দেখুন সবকিছু ঠিক রয়েছে কিনা এবং প্রফেশনাল ভাবে সবকিছু দেখাচ্ছে কিনা','product',6,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/remove','2026-03-17 22:50:49','2026-03-17 22:50:49'),(614,NULL,'rgsMLgtvX2k7Vgpl34lkzrGoNxzwzVVevdLlANKp','compare_add','Added to compare: স্ক্রিনশট দিয়েছি ভালোভাবে এনালাইসিস করে দেখুন সবকিছু ঠিক রয়েছে কিনা এবং প্রফেশনাল ভাবে সবকিছু দেখাচ্ছে কিনা','product',6,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/compare/add','2026-03-17 22:50:50','2026-03-17 22:50:50'),(615,NULL,'rgsMLgtvX2k7Vgpl34lkzrGoNxzwzVVevdLlANKp','compare_remove','Removed from compare','product',6,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/compare/remove','2026-03-17 22:50:53','2026-03-17 22:50:53'),(616,NULL,'rgsMLgtvX2k7Vgpl34lkzrGoNxzwzVVevdLlANKp','wishlist_add','Added to wishlist: স্ক্রিনশট দিয়েছি ভালোভাবে এনালাইসিস করে দেখুন সবকিছু ঠিক রয়েছে কিনা এবং প্রফেশনাল ভাবে সবকিছু দেখাচ্ছে কিনা','product',6,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/wish-list/add','2026-03-17 22:50:54','2026-03-17 22:50:54'),(617,NULL,'rgsMLgtvX2k7Vgpl34lkzrGoNxzwzVVevdLlANKp','wishlist_remove','Removed from wishlist','product',6,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/wish-list/remove?product_id=6','2026-03-17 22:50:55','2026-03-17 22:50:55'),(618,NULL,'rgsMLgtvX2k7Vgpl34lkzrGoNxzwzVVevdLlANKp','product_view','Viewed product: T-shirt, টি-শার্ট','product',8,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/t-shirt-ti-sart-8','2026-03-17 22:58:27','2026-03-17 22:58:27'),(619,NULL,'rgsMLgtvX2k7Vgpl34lkzrGoNxzwzVVevdLlANKp','compare_remove','Removed from compare','product',8,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/compare/remove','2026-03-17 22:58:34','2026-03-17 22:58:34'),(620,NULL,'rgsMLgtvX2k7Vgpl34lkzrGoNxzwzVVevdLlANKp','cart_add','Added to cart: T-shirt, টি-শার্ট','product',8,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-03-17 22:58:36','2026-03-17 22:58:36'),(621,NULL,'rgsMLgtvX2k7Vgpl34lkzrGoNxzwzVVevdLlANKp','cart_remove','Removed from cart: T-shirt, টি-শার্ট','product',8,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/remove','2026-03-17 22:58:38','2026-03-17 22:58:38'),(622,NULL,'rgsMLgtvX2k7Vgpl34lkzrGoNxzwzVVevdLlANKp','product_view','Viewed product: T-shirt, টি-শার্ট','product',8,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/t-shirt-ti-sart-8','2026-03-17 22:58:40','2026-03-17 22:58:40'),(623,NULL,'rgsMLgtvX2k7Vgpl34lkzrGoNxzwzVVevdLlANKp','compare_add','Added to compare: T-shirt, টি-শার্ট','product',8,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/compare/add','2026-03-17 22:58:44','2026-03-17 22:58:44'),(624,NULL,'rgsMLgtvX2k7Vgpl34lkzrGoNxzwzVVevdLlANKp','compare_remove','Removed from compare','product',8,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/compare/remove','2026-03-17 22:58:45','2026-03-17 22:58:45'),(625,NULL,'rgsMLgtvX2k7Vgpl34lkzrGoNxzwzVVevdLlANKp','cart_add','Added to cart: T-shirt, টি-শার্ট','product',8,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-03-17 22:58:47','2026-03-17 22:58:47'),(626,NULL,'rgsMLgtvX2k7Vgpl34lkzrGoNxzwzVVevdLlANKp','cart_remove','Removed from cart: T-shirt, টি-শার্ট','product',8,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/remove','2026-03-17 22:58:48','2026-03-17 22:58:48'),(627,NULL,'rgsMLgtvX2k7Vgpl34lkzrGoNxzwzVVevdLlANKp','wishlist_add','Added to wishlist: T-shirt, টি-শার্ট','product',8,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/wish-list/add','2026-03-17 22:58:50','2026-03-17 22:58:50'),(628,NULL,'rgsMLgtvX2k7Vgpl34lkzrGoNxzwzVVevdLlANKp','wishlist_remove','Removed from wishlist','product',8,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/wish-list/remove?product_id=8','2026-03-17 22:58:52','2026-03-17 22:58:52'),(629,NULL,'rgsMLgtvX2k7Vgpl34lkzrGoNxzwzVVevdLlANKp','product_view','Viewed product: T-shirt, টি-শার্ট','product',8,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/t-shirt-ti-sart-8','2026-03-17 22:59:22','2026-03-17 22:59:22'),(630,NULL,'rgsMLgtvX2k7Vgpl34lkzrGoNxzwzVVevdLlANKp','product_view','Viewed product: স্ক্রিনশট দিয়েছি ভালোভাবে এনালাইসিস করে দেখুন সবকিছু ঠিক রয়েছে কিনা এবং প্রফেশনাল ভাবে সবকিছু দেখাচ্ছে কিনা','product',6,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/skrnsht-dzch-6','2026-03-17 23:05:29','2026-03-17 23:05:29'),(631,NULL,'q7KfO2uAwbY2bdX1en7aNF9PCv0PoCXk4MLq0XqW','product_view','Viewed product: RIAZUL ISLAM SHOJOL','product',1,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/riazul-islam-1','2026-03-18 03:09:09','2026-03-18 03:09:09'),(632,NULL,'q7KfO2uAwbY2bdX1en7aNF9PCv0PoCXk4MLq0XqW','cart_add','Added to cart: RIAZUL ISLAM SHOJOL','product',1,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-03-18 03:09:32','2026-03-18 03:09:32'),(633,NULL,'q7KfO2uAwbY2bdX1en7aNF9PCv0PoCXk4MLq0XqW','cart_remove','Removed from cart: RIAZUL ISLAM SHOJOL','product',1,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/remove','2026-03-18 03:09:34','2026-03-18 03:09:34'),(634,NULL,'q7KfO2uAwbY2bdX1en7aNF9PCv0PoCXk4MLq0XqW','compare_add','Added to compare: RIAZUL ISLAM SHOJOL','product',1,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/compare/add','2026-03-18 03:09:35','2026-03-18 03:09:35'),(635,NULL,'q7KfO2uAwbY2bdX1en7aNF9PCv0PoCXk4MLq0XqW','compare_remove','Removed from compare','product',1,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/compare/remove','2026-03-18 03:09:36','2026-03-18 03:09:36'),(636,NULL,'q7KfO2uAwbY2bdX1en7aNF9PCv0PoCXk4MLq0XqW','wishlist_add','Added to wishlist: RIAZUL ISLAM SHOJOL','product',1,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/wish-list/add','2026-03-18 03:09:38','2026-03-18 03:09:38'),(637,NULL,'q7KfO2uAwbY2bdX1en7aNF9PCv0PoCXk4MLq0XqW','wishlist_remove','Removed from wishlist','product',1,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/wish-list/remove?product_id=1','2026-03-18 03:09:39','2026-03-18 03:09:39'),(638,NULL,'q7KfO2uAwbY2bdX1en7aNF9PCv0PoCXk4MLq0XqW','compare_add','Added to compare: RIAZUL ISLAM SHOJOL','product',1,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/compare/add','2026-03-18 03:09:47','2026-03-18 03:09:47'),(639,NULL,'q7KfO2uAwbY2bdX1en7aNF9PCv0PoCXk4MLq0XqW','compare_remove','Removed from compare','product',1,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/compare/remove','2026-03-18 03:09:48','2026-03-18 03:09:48'),(640,NULL,'q7KfO2uAwbY2bdX1en7aNF9PCv0PoCXk4MLq0XqW','search_text','Search: d',NULL,NULL,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/search/universal','2026-03-18 03:35:10','2026-03-18 03:35:10'),(641,NULL,'q7KfO2uAwbY2bdX1en7aNF9PCv0PoCXk4MLq0XqW','search_text','Search: dg',NULL,NULL,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/search/universal','2026-03-18 03:35:10','2026-03-18 03:35:10'),(642,NULL,'q7KfO2uAwbY2bdX1en7aNF9PCv0PoCXk4MLq0XqW','search_text','Search: dgdg',NULL,NULL,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/search/universal','2026-03-18 03:35:10','2026-03-18 03:35:10'),(643,NULL,'q7KfO2uAwbY2bdX1en7aNF9PCv0PoCXk4MLq0XqW','wishlist_add','Added to wishlist: স্ক্রিনশট দিয়েছি  Affordable Custom Cricket Jersey With Sublimation Printing','product',4,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/wish-list/add','2026-03-18 03:36:48','2026-03-18 03:36:48'),(644,NULL,'0B9BtZLRMJ4AnHXzZrhx35tG5ciT5Zn9lME9l8cB','product_view','Viewed product: স্ক্রিনশট দিয়েছি ভালোভাবে এনালাইসিস করে দেখুন সবকিছু ঠিক রয়েছে কিনা এবং প্রফেশনাল ভাবে সবকিছু দেখাচ্ছে কিনা','product',6,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/skrnsht-dzch-6','2026-03-25 10:58:43','2026-03-25 10:58:43'),(645,NULL,'0B9BtZLRMJ4AnHXzZrhx35tG5ciT5Zn9lME9l8cB','product_view','Viewed product: T-shirt, টি-শার্ট','product',8,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/tshirt-tsrt-8','2026-03-25 10:58:43','2026-03-25 10:58:43'),(646,NULL,'15Hi6EOyRLKOh6rt9TgTPbaQrB2P0WiabGnsnZcB','product_view','Viewed product: T-shirt, টি-শার্ট','product',8,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/tshirt-tsrt-8','2026-03-27 04:27:58','2026-03-27 04:27:58'),(647,NULL,'15Hi6EOyRLKOh6rt9TgTPbaQrB2P0WiabGnsnZcB','product_view','Viewed product: T-shirt, টি-শার্ট','product',8,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/tshirt-tsrt-8','2026-03-27 04:28:56','2026-03-27 04:28:56'),(648,NULL,'15Hi6EOyRLKOh6rt9TgTPbaQrB2P0WiabGnsnZcB','cart_add','Added to cart: RIAZUL ISLAM SHOJOL','product',1,'127.0.0.1','mobile','Handheld Browser','Android',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-03-27 04:31:04','2026-03-27 04:31:04'),(649,NULL,'15Hi6EOyRLKOh6rt9TgTPbaQrB2P0WiabGnsnZcB','compare_add','Added to compare: RIAZUL ISLAM SHOJOL','product',1,'127.0.0.1','mobile','Handheld Browser','Android',NULL,NULL,NULL,NULL,'http://localhost/staylbd/compare/add','2026-03-27 04:31:08','2026-03-27 04:31:08'),(650,NULL,'15Hi6EOyRLKOh6rt9TgTPbaQrB2P0WiabGnsnZcB','wishlist_add','Added to wishlist: RIAZUL ISLAM SHOJOL','product',1,'127.0.0.1','mobile','Handheld Browser','Android',NULL,NULL,NULL,NULL,'http://localhost/staylbd/wish-list/add','2026-03-27 04:31:12','2026-03-27 04:31:12'),(651,NULL,'15Hi6EOyRLKOh6rt9TgTPbaQrB2P0WiabGnsnZcB','product_view','Viewed product: RIAZUL ISLAM SHOJOL','product',1,'127.0.0.1','mobile','Handheld Browser','Android',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/riazul-islam-1','2026-03-27 04:31:20','2026-03-27 04:31:20'),(652,NULL,'15Hi6EOyRLKOh6rt9TgTPbaQrB2P0WiabGnsnZcB','product_view','Viewed product: T-shirt, টি-শার্ট','product',8,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/tshirt-tsrt-8','2026-03-27 05:58:39','2026-03-27 05:58:39'),(653,NULL,'15Hi6EOyRLKOh6rt9TgTPbaQrB2P0WiabGnsnZcB','product_view','Viewed product: T-shirt, টি-শার্ট','product',8,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/tshirt-tsrt-8','2026-03-27 05:59:26','2026-03-27 05:59:26'),(654,NULL,'LfPElKD7Jdr6u6xHl0xr5I8AlW9r4REFVN6Bc0zJ','product_view','Viewed product: T-shirt, টি-শার্ট','product',10,'127.0.0.1','desktop','Unknown Browser','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/tshirt-tsrt-10','2026-03-27 06:21:27','2026-03-27 06:21:27'),(655,NULL,'3FJcCbsdHZy8Xe1iBtmtw4Lnh9OmraJaYnLQTr3B','product_view','Viewed product: T-shirt, টি-শার্ট','product',10,'127.0.0.1','desktop','Unknown Browser','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/tshirt-tsrt-10','2026-03-27 06:21:27','2026-03-27 06:21:27'),(656,NULL,'EmIKt49SldniiJBbBPsVElvyPLkF4OG6tdDwqK05','product_view','Viewed product: T-shirt, টি-শার্ট','product',10,'127.0.0.1','desktop','Unknown Browser','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/tshirt-tsrt-10','2026-03-27 06:25:58','2026-03-27 06:25:58'),(657,NULL,'15Hi6EOyRLKOh6rt9TgTPbaQrB2P0WiabGnsnZcB','product_view','Viewed product: T-shirt, টি-শার্ট','product',8,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/tshirt-tsrt-8','2026-03-27 10:17:32','2026-03-27 10:17:32'),(658,NULL,'15Hi6EOyRLKOh6rt9TgTPbaQrB2P0WiabGnsnZcB','wishlist_remove','Removed from wishlist','product',4,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/wish-list/remove?product_id=4','2026-03-27 10:58:56','2026-03-27 10:58:56'),(659,NULL,'15Hi6EOyRLKOh6rt9TgTPbaQrB2P0WiabGnsnZcB','wishlist_add','Added to wishlist: স্ক্রিনশট দিয়েছি  Affordable Custom Cricket Jersey With Sublimation Printing','product',4,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/wish-list/add','2026-03-27 10:58:59','2026-03-27 10:58:59'),(660,NULL,'15Hi6EOyRLKOh6rt9TgTPbaQrB2P0WiabGnsnZcB','wishlist_remove','Removed from wishlist','product',4,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/wish-list/remove?product_id=4','2026-03-27 10:59:01','2026-03-27 10:59:01'),(661,NULL,'15Hi6EOyRLKOh6rt9TgTPbaQrB2P0WiabGnsnZcB','wishlist_add','Added to wishlist: স্ক্রিনশট দিয়েছি  Affordable Custom Cricket Jersey With Sublimation Printing','product',4,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/wish-list/add','2026-03-27 10:59:02','2026-03-27 10:59:02'),(662,NULL,'15Hi6EOyRLKOh6rt9TgTPbaQrB2P0WiabGnsnZcB','compare_add','Added to compare: T-shirt, টি-শার্ট','product',9,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/compare/add','2026-03-27 10:59:07','2026-03-27 10:59:07'),(663,NULL,'15Hi6EOyRLKOh6rt9TgTPbaQrB2P0WiabGnsnZcB','wishlist_add','Added to wishlist: T-shirt, টি-শার্ট','product',9,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/wish-list/add','2026-03-27 10:59:11','2026-03-27 10:59:11'),(664,NULL,'15Hi6EOyRLKOh6rt9TgTPbaQrB2P0WiabGnsnZcB','compare_remove','Removed from compare','product',9,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/compare/remove','2026-03-27 10:59:15','2026-03-27 10:59:15'),(665,NULL,'15Hi6EOyRLKOh6rt9TgTPbaQrB2P0WiabGnsnZcB','wishlist_remove','Removed from wishlist','product',9,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/wish-list/remove?product_id=9','2026-03-27 10:59:16','2026-03-27 10:59:16'),(666,NULL,'15Hi6EOyRLKOh6rt9TgTPbaQrB2P0WiabGnsnZcB','product_view','Viewed product: T-shirt, টি-শার্ট','product',8,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/tshirt-tsrt-8','2026-03-27 11:27:35','2026-03-27 11:27:35'),(667,NULL,'15Hi6EOyRLKOh6rt9TgTPbaQrB2P0WiabGnsnZcB','product_view','Viewed product: T-shirt, টি-শার্ট','product',8,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/tshirt-tsrt-8','2026-03-27 11:30:34','2026-03-27 11:30:34'),(668,NULL,'15Hi6EOyRLKOh6rt9TgTPbaQrB2P0WiabGnsnZcB','product_view','Viewed product: T-shirt, টি-শার্ট','product',8,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/tshirt-tsrt-8','2026-03-27 11:33:12','2026-03-27 11:33:12'),(669,NULL,'15Hi6EOyRLKOh6rt9TgTPbaQrB2P0WiabGnsnZcB','product_view','Viewed product: T-shirt, টি-শার্ট','product',8,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/tshirt-tsrt-8','2026-03-27 11:33:26','2026-03-27 11:33:26'),(670,NULL,'15Hi6EOyRLKOh6rt9TgTPbaQrB2P0WiabGnsnZcB','product_view','Viewed product: T-shirt, টি-শার্ট','product',8,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/tshirt-tsrt-8','2026-03-27 11:33:53','2026-03-27 11:33:53'),(671,NULL,'15Hi6EOyRLKOh6rt9TgTPbaQrB2P0WiabGnsnZcB','product_view','Viewed product: T-shirt, টি-শার্ট','product',8,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/tshirt-tsrt-8','2026-03-27 11:34:00','2026-03-27 11:34:00'),(672,NULL,'15Hi6EOyRLKOh6rt9TgTPbaQrB2P0WiabGnsnZcB','product_view','Viewed product: T-shirt, টি-শার্ট','product',8,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/tshirt-tsrt-8','2026-03-27 11:37:41','2026-03-27 11:37:41'),(673,NULL,'15Hi6EOyRLKOh6rt9TgTPbaQrB2P0WiabGnsnZcB','cart_add','Added to cart: T-shirt, টি-শার্ট','product',8,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-03-27 11:37:59','2026-03-27 11:37:59'),(674,NULL,'15Hi6EOyRLKOh6rt9TgTPbaQrB2P0WiabGnsnZcB','cart_remove','Removed from cart: T-shirt, টি-শার্ট','product',8,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/remove','2026-03-27 11:38:01','2026-03-27 11:38:01'),(675,NULL,'15Hi6EOyRLKOh6rt9TgTPbaQrB2P0WiabGnsnZcB','compare_add','Added to compare: T-shirt, টি-শার্ট','product',8,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/compare/add','2026-03-27 11:38:03','2026-03-27 11:38:03'),(676,NULL,'15Hi6EOyRLKOh6rt9TgTPbaQrB2P0WiabGnsnZcB','compare_remove','Removed from compare','product',8,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/compare/remove','2026-03-27 11:38:05','2026-03-27 11:38:05'),(677,NULL,'15Hi6EOyRLKOh6rt9TgTPbaQrB2P0WiabGnsnZcB','wishlist_add','Added to wishlist: T-shirt, টি-শার্ট','product',8,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/wish-list/add','2026-03-27 11:38:07','2026-03-27 11:38:07'),(678,NULL,'15Hi6EOyRLKOh6rt9TgTPbaQrB2P0WiabGnsnZcB','wishlist_remove','Removed from wishlist','product',8,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/wish-list/remove?product_id=8','2026-03-27 11:38:09','2026-03-27 11:38:09'),(679,NULL,'15Hi6EOyRLKOh6rt9TgTPbaQrB2P0WiabGnsnZcB','cart_add','Added to cart: T-shirt, টি-শার্ট','product',8,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-03-27 11:38:11','2026-03-27 11:38:11'),(680,NULL,'15Hi6EOyRLKOh6rt9TgTPbaQrB2P0WiabGnsnZcB','cart_remove','Removed from cart: T-shirt, টি-শার্ট','product',8,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/remove','2026-03-27 11:38:13','2026-03-27 11:38:13'),(681,NULL,'15Hi6EOyRLKOh6rt9TgTPbaQrB2P0WiabGnsnZcB','cart_add','Added to cart: T-shirt, টি-শার্ট','product',8,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-03-27 11:38:22','2026-03-27 11:38:22'),(682,NULL,'15Hi6EOyRLKOh6rt9TgTPbaQrB2P0WiabGnsnZcB','product_view','Viewed product: T-shirt, টি-শার্ট','product',8,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/tshirt-tsrt-8','2026-03-27 11:38:52','2026-03-27 11:38:52'),(683,NULL,'15Hi6EOyRLKOh6rt9TgTPbaQrB2P0WiabGnsnZcB','product_view','Viewed product: T-shirt, টি-শার্ট','product',8,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/tshirt-tsrt-8','2026-03-27 11:42:16','2026-03-27 11:42:16'),(684,NULL,'15Hi6EOyRLKOh6rt9TgTPbaQrB2P0WiabGnsnZcB','cart_add','Added to cart: T-shirt, টি-শার্ট','product',8,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-03-27 11:42:22','2026-03-27 11:42:22'),(685,NULL,'15Hi6EOyRLKOh6rt9TgTPbaQrB2P0WiabGnsnZcB','product_view','Viewed product: T-shirt, টি-শার্ট','product',8,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/tshirt-tsrt-8','2026-03-27 11:42:45','2026-03-27 11:42:45'),(686,NULL,'15Hi6EOyRLKOh6rt9TgTPbaQrB2P0WiabGnsnZcB','product_view','Viewed product: T-shirt, টি-শার্ট','product',8,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/tshirt-tsrt-8','2026-03-27 11:54:49','2026-03-27 11:54:49'),(687,NULL,'15Hi6EOyRLKOh6rt9TgTPbaQrB2P0WiabGnsnZcB','product_view','Viewed product: স্ক্রিনশট দিয়েছি ভালোভাবে এনালাইসিস করে দেখুন সবকিছু ঠিক রয়েছে কিনা এবং প্রফেশনাল ভাবে সবকিছু দেখাচ্ছে কিনা','product',6,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/skrnsht-dzch-6','2026-03-27 11:55:06','2026-03-27 11:55:06'),(688,NULL,'15Hi6EOyRLKOh6rt9TgTPbaQrB2P0WiabGnsnZcB','cart_add','Added to cart: স্ক্রিনশট দিয়েছি ভালোভাবে এনালাইসিস করে দেখুন সবকিছু ঠিক রয়েছে কিনা এবং প্রফেশনাল ভাবে সবকিছু দেখাচ্ছে কিনা','product',6,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-03-27 11:55:09','2026-03-27 11:55:09'),(689,NULL,'15Hi6EOyRLKOh6rt9TgTPbaQrB2P0WiabGnsnZcB','product_view','Viewed product: স্ক্রিনশট দিয়েছি ভালোভাবে এনালাইসিস করে দেখুন সবকিছু ঠিক রয়েছে কিনা এবং প্রফেশনাল ভাবে সবকিছু দেখাচ্ছে কিনা','product',6,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/skrnsht-dzch-6','2026-03-27 11:55:14','2026-03-27 11:55:14'),(690,NULL,'15Hi6EOyRLKOh6rt9TgTPbaQrB2P0WiabGnsnZcB','cart_add','Added to cart: স্ক্রিনশট দিয়েছি ভালোভাবে এনালাইসিস করে দেখুন সবকিছু ঠিক রয়েছে কিনা এবং প্রফেশনাল ভাবে সবকিছু দেখাচ্ছে কিনা','product',6,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-03-27 11:55:17','2026-03-27 11:55:17'),(691,NULL,'EyGMqyKCZC5SsVLfHhYowlq7GYsqwd0FZGMmaasQ','wishlist_add','Added to wishlist: T-shirt, টি-শার্ট','product',7,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/wish-list/add','2026-04-01 14:56:54','2026-04-01 14:56:54'),(692,NULL,'EyGMqyKCZC5SsVLfHhYowlq7GYsqwd0FZGMmaasQ','compare_add','Added to compare: T-shirt, টি-শার্ট','product',7,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/compare/add','2026-04-01 14:56:54','2026-04-01 14:56:54'),(693,NULL,'EyGMqyKCZC5SsVLfHhYowlq7GYsqwd0FZGMmaasQ','cart_add','Added to cart: T-shirt, টি-শার্ট','product',7,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-04-01 14:56:55','2026-04-01 14:56:55'),(694,NULL,'EyGMqyKCZC5SsVLfHhYowlq7GYsqwd0FZGMmaasQ','compare_add','Added to compare: স্ক্রিনশট দিয়েছি  Affordable Custom Cricket Jersey With Sublimation Printing','product',4,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/compare/add','2026-04-01 14:57:09','2026-04-01 14:57:09'),(695,NULL,'EyGMqyKCZC5SsVLfHhYowlq7GYsqwd0FZGMmaasQ','cart_add','Added to cart: স্ক্রিনশট দিয়েছি  Affordable Custom Cricket Jersey With Sublimation Printing','product',4,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-04-01 14:57:11','2026-04-01 14:57:11'),(696,NULL,'EyGMqyKCZC5SsVLfHhYowlq7GYsqwd0FZGMmaasQ','wishlist_add','Added to wishlist: স্ক্রিনশট দিয়েছি  Affordable Custom Cricket Jersey With Sublimation Printing','product',4,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/wish-list/add','2026-04-01 14:57:13','2026-04-01 14:57:13'),(697,NULL,'EyGMqyKCZC5SsVLfHhYowlq7GYsqwd0FZGMmaasQ','wishlist_add','Added to wishlist: Cricket Jersey || Affordable Custom Cricket Jersey With Sublimation Printing','product',3,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/wish-list/add','2026-04-01 14:57:16','2026-04-01 14:57:16'),(698,NULL,'EyGMqyKCZC5SsVLfHhYowlq7GYsqwd0FZGMmaasQ','compare_add','Added to compare: Cricket Jersey || Affordable Custom Cricket Jersey With Sublimation Printing','product',3,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/compare/add','2026-04-01 14:57:17','2026-04-01 14:57:17'),(699,NULL,'EyGMqyKCZC5SsVLfHhYowlq7GYsqwd0FZGMmaasQ','cart_add','Added to cart: Cricket Jersey || Affordable Custom Cricket Jersey With Sublimation Printing','product',3,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-04-01 14:57:18','2026-04-01 14:57:18'),(700,NULL,'EyGMqyKCZC5SsVLfHhYowlq7GYsqwd0FZGMmaasQ','wishlist_add','Added to wishlist: T-shirt, টি-শার্ট','product',8,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/wish-list/add','2026-04-01 14:57:23','2026-04-01 14:57:23'),(701,NULL,'EyGMqyKCZC5SsVLfHhYowlq7GYsqwd0FZGMmaasQ','compare_add','Added to compare: T-shirt, টি-শার্ট','product',8,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/compare/add','2026-04-01 14:57:24','2026-04-01 14:57:24'),(702,NULL,'EyGMqyKCZC5SsVLfHhYowlq7GYsqwd0FZGMmaasQ','cart_add','Added to cart: T-shirt, টি-শার্ট','product',8,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-04-01 14:57:25','2026-04-01 14:57:25'),(703,NULL,'EyGMqyKCZC5SsVLfHhYowlq7GYsqwd0FZGMmaasQ','product_view','Viewed product: স্ক্রিনশট দিয়েছি ভালোভাবে এনালাইসিস করে দেখুন সবকিছু ঠিক রয়েছে কিনা এবং প্রফেশনাল ভাবে সবকিছু দেখাচ্ছে কিনা','product',6,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/skrnsht-dzch-6','2026-04-01 15:16:59','2026-04-01 15:16:59'),(704,NULL,'GHhSgSUhJodUsqju4V9N2GK96tZqHOWIAtXnFh5l','search_text','Search: G',NULL,NULL,'192.168.0.58','mobile','Handheld Browser','Android',NULL,NULL,NULL,NULL,'http://192.168.0.175/staylbd/search/universal','2026-04-01 15:23:21','2026-04-01 15:23:21'),(705,NULL,'GHhSgSUhJodUsqju4V9N2GK96tZqHOWIAtXnFh5l','search_text','Search: Gt',NULL,NULL,'192.168.0.58','mobile','Handheld Browser','Android',NULL,NULL,NULL,NULL,'http://192.168.0.175/staylbd/search/universal','2026-04-01 15:23:21','2026-04-01 15:23:21'),(706,NULL,'GHhSgSUhJodUsqju4V9N2GK96tZqHOWIAtXnFh5l','search_text','Search: Gtt',NULL,NULL,'192.168.0.58','mobile','Handheld Browser','Android',NULL,NULL,NULL,NULL,'http://192.168.0.175/staylbd/search/universal','2026-04-01 15:23:23','2026-04-01 15:23:23'),(707,NULL,'GHhSgSUhJodUsqju4V9N2GK96tZqHOWIAtXnFh5l','search_text','Search: Gtth',NULL,NULL,'192.168.0.58','mobile','Handheld Browser','Android',NULL,NULL,NULL,NULL,'http://192.168.0.175/staylbd/search/universal','2026-04-01 15:23:25','2026-04-01 15:23:25'),(708,NULL,'GHhSgSUhJodUsqju4V9N2GK96tZqHOWIAtXnFh5l','search_text','Search: Gtthf',NULL,NULL,'192.168.0.58','mobile','Handheld Browser','Android',NULL,NULL,NULL,NULL,'http://192.168.0.175/staylbd/search/universal','2026-04-01 15:23:25','2026-04-01 15:23:25'),(709,NULL,'GHhSgSUhJodUsqju4V9N2GK96tZqHOWIAtXnFh5l','search_text','Search: Gtthff',NULL,NULL,'192.168.0.58','mobile','Handheld Browser','Android',NULL,NULL,NULL,NULL,'http://192.168.0.175/staylbd/search/universal','2026-04-01 15:23:26','2026-04-01 15:23:26'),(710,NULL,'GHhSgSUhJodUsqju4V9N2GK96tZqHOWIAtXnFh5l','search_text','Search: Gtthffg',NULL,NULL,'192.168.0.58','mobile','Handheld Browser','Android',NULL,NULL,NULL,NULL,'http://192.168.0.175/staylbd/search/universal','2026-04-01 15:23:26','2026-04-01 15:23:26'),(711,NULL,'GHhSgSUhJodUsqju4V9N2GK96tZqHOWIAtXnFh5l','search_text','Search: Gtthffgg',NULL,NULL,'192.168.0.58','mobile','Handheld Browser','Android',NULL,NULL,NULL,NULL,'http://192.168.0.175/staylbd/search/universal','2026-04-01 15:23:26','2026-04-01 15:23:26'),(712,NULL,'GHhSgSUhJodUsqju4V9N2GK96tZqHOWIAtXnFh5l','search_text','Search: Gtthffggg',NULL,NULL,'192.168.0.58','mobile','Handheld Browser','Android',NULL,NULL,NULL,NULL,'http://192.168.0.175/staylbd/search/universal','2026-04-01 15:23:26','2026-04-01 15:23:26'),(713,NULL,'GHhSgSUhJodUsqju4V9N2GK96tZqHOWIAtXnFh5l','search_text','Search: Gtthffgggf',NULL,NULL,'192.168.0.58','mobile','Handheld Browser','Android',NULL,NULL,NULL,NULL,'http://192.168.0.175/staylbd/search/universal','2026-04-01 15:23:28','2026-04-01 15:23:28'),(714,NULL,'GHhSgSUhJodUsqju4V9N2GK96tZqHOWIAtXnFh5l','search_text','Search: Gtthffgggfr',NULL,NULL,'192.168.0.58','mobile','Handheld Browser','Android',NULL,NULL,NULL,NULL,'http://192.168.0.175/staylbd/search/universal','2026-04-01 15:23:32','2026-04-01 15:23:32'),(715,NULL,'GHhSgSUhJodUsqju4V9N2GK96tZqHOWIAtXnFh5l','search_text','Search: Gtthffgggfrt',NULL,NULL,'192.168.0.58','mobile','Handheld Browser','Android',NULL,NULL,NULL,NULL,'http://192.168.0.175/staylbd/search/universal','2026-04-01 15:23:32','2026-04-01 15:23:32'),(716,NULL,'GHhSgSUhJodUsqju4V9N2GK96tZqHOWIAtXnFh5l','search_text','Search: Gtthffgggfrtt',NULL,NULL,'192.168.0.58','mobile','Handheld Browser','Android',NULL,NULL,NULL,NULL,'http://192.168.0.175/staylbd/search/universal','2026-04-01 15:23:33','2026-04-01 15:23:33'),(717,NULL,'GHhSgSUhJodUsqju4V9N2GK96tZqHOWIAtXnFh5l','search_text','Search: Gtthffgggfrttt',NULL,NULL,'192.168.0.58','mobile','Handheld Browser','Android',NULL,NULL,NULL,NULL,'http://192.168.0.175/staylbd/search/universal','2026-04-01 15:23:33','2026-04-01 15:23:33'),(718,NULL,'GHhSgSUhJodUsqju4V9N2GK96tZqHOWIAtXnFh5l','search_text','Search: Gtthffgggfrtttt',NULL,NULL,'192.168.0.58','mobile','Handheld Browser','Android',NULL,NULL,NULL,NULL,'http://192.168.0.175/staylbd/search/universal','2026-04-01 15:23:33','2026-04-01 15:23:33'),(719,NULL,'GHhSgSUhJodUsqju4V9N2GK96tZqHOWIAtXnFh5l','search_text','Search: Gtthffgggfrttttt',NULL,NULL,'192.168.0.58','mobile','Handheld Browser','Android',NULL,NULL,NULL,NULL,'http://192.168.0.175/staylbd/search/universal','2026-04-01 15:23:34','2026-04-01 15:23:34'),(720,NULL,'GHhSgSUhJodUsqju4V9N2GK96tZqHOWIAtXnFh5l','search_text','Search: Gtthffgggfrtttttt',NULL,NULL,'192.168.0.58','mobile','Handheld Browser','Android',NULL,NULL,NULL,NULL,'http://192.168.0.175/staylbd/search/universal','2026-04-01 15:23:36','2026-04-01 15:23:36'),(721,NULL,'GHhSgSUhJodUsqju4V9N2GK96tZqHOWIAtXnFh5l','search_text','Search: Gtthffgggfrttttttr',NULL,NULL,'192.168.0.58','mobile','Handheld Browser','Android',NULL,NULL,NULL,NULL,'http://192.168.0.175/staylbd/search/universal','2026-04-01 15:23:38','2026-04-01 15:23:38'),(722,NULL,'GHhSgSUhJodUsqju4V9N2GK96tZqHOWIAtXnFh5l','search_text','Search: Gtthffgggfrttttttrú',NULL,NULL,'192.168.0.58','mobile','Handheld Browser','Android',NULL,NULL,NULL,NULL,'http://192.168.0.175/staylbd/search/universal','2026-04-01 15:23:39','2026-04-01 15:23:39'),(723,NULL,'QXNGeh3gje84P9ae5miWqZhwnDZrn3uTptdjHQTd','search_text','Search: h',NULL,NULL,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/search/universal','2026-04-02 07:35:28','2026-04-02 07:35:28'),(724,NULL,'QXNGeh3gje84P9ae5miWqZhwnDZrn3uTptdjHQTd','search_text','Search: ho',NULL,NULL,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/search/universal','2026-04-02 07:35:29','2026-04-02 07:35:29'),(725,NULL,'QXNGeh3gje84P9ae5miWqZhwnDZrn3uTptdjHQTd','search_text','Search: hom',NULL,NULL,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/search/universal','2026-04-02 07:35:30','2026-04-02 07:35:30'),(726,NULL,'QXNGeh3gje84P9ae5miWqZhwnDZrn3uTptdjHQTd','search_text','Search: hom o',NULL,NULL,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/search/universal','2026-04-02 07:35:30','2026-04-02 07:35:30'),(727,NULL,'QXNGeh3gje84P9ae5miWqZhwnDZrn3uTptdjHQTd','search_text','Search: hom op',NULL,NULL,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/search/universal','2026-04-02 07:35:31','2026-04-02 07:35:31'),(728,NULL,'QXNGeh3gje84P9ae5miWqZhwnDZrn3uTptdjHQTd','search_text','Search: hom opu',NULL,NULL,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/search/universal','2026-04-02 07:35:31','2026-04-02 07:35:31'),(729,NULL,'QXNGeh3gje84P9ae5miWqZhwnDZrn3uTptdjHQTd','search_text','Search: Hello microphone test microphone test',NULL,NULL,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/search/universal','2026-04-02 07:45:53','2026-04-02 07:45:53'),(730,NULL,'QXNGeh3gje84P9ae5miWqZhwnDZrn3uTptdjHQTd','search_text','Search: Hello microphone test microphone test',NULL,NULL,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/search/universal','2026-04-02 07:45:57','2026-04-02 07:45:57'),(731,NULL,'QXNGeh3gje84P9ae5miWqZhwnDZrn3uTptdjHQTd','search_text','Search: Hello microphone test microphone testg',NULL,NULL,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/search/universal','2026-04-02 07:46:04','2026-04-02 07:46:04'),(732,NULL,'QXNGeh3gje84P9ae5miWqZhwnDZrn3uTptdjHQTd','search_text','Search: Hello microphone test microphone testggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggg',NULL,NULL,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/search/universal','2026-04-02 07:46:08','2026-04-02 07:46:08'),(733,NULL,'QXNGeh3gje84P9ae5miWqZhwnDZrn3uTptdjHQTd','search_text','Search: Hello microphone test microphone testggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggg',NULL,NULL,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/search/universal','2026-04-02 07:46:40','2026-04-02 07:46:40'),(734,NULL,'QXNGeh3gje84P9ae5miWqZhwnDZrn3uTptdjHQTd','search_text','Search: ggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggg',NULL,NULL,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/search/universal','2026-04-02 07:47:01','2026-04-02 07:47:01'),(735,NULL,'C8wye4ZRr0oTaOlcWqWke94A2axndMxSDp2oL4FQ','product_view','Viewed product: T-shirt, টি-শার্ট','product',8,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/tshirt-tsrt-8','2026-04-02 08:04:18','2026-04-02 08:04:18'),(736,NULL,'C8wye4ZRr0oTaOlcWqWke94A2axndMxSDp2oL4FQ','wishlist_add','Added to wishlist: T-shirt, টি-শার্ট','product',7,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/wish-list/add','2026-04-02 08:49:40','2026-04-02 08:49:40'),(737,NULL,'C8wye4ZRr0oTaOlcWqWke94A2axndMxSDp2oL4FQ','cart_add','Added to cart: T-shirt, টি-শার্ট','product',7,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-04-02 08:49:42','2026-04-02 08:49:42'),(738,NULL,'C8wye4ZRr0oTaOlcWqWke94A2axndMxSDp2oL4FQ','compare_remove','Removed from compare','product',7,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/compare/remove','2026-04-02 09:13:43','2026-04-02 09:13:43'),(739,NULL,'C8wye4ZRr0oTaOlcWqWke94A2axndMxSDp2oL4FQ','compare_add','Added to compare: T-shirt, টি-শার্ট','product',7,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/compare/add','2026-04-02 09:13:45','2026-04-02 09:13:45'),(740,NULL,'C8wye4ZRr0oTaOlcWqWke94A2axndMxSDp2oL4FQ','wishlist_remove','Removed from wishlist','product',7,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/wish-list/remove?product_id=7','2026-04-02 09:13:46','2026-04-02 09:13:46'),(741,NULL,'C8wye4ZRr0oTaOlcWqWke94A2axndMxSDp2oL4FQ','wishlist_add','Added to wishlist: T-shirt, টি-শার্ট','product',7,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/wish-list/add','2026-04-02 09:13:47','2026-04-02 09:13:47'),(742,NULL,'C8wye4ZRr0oTaOlcWqWke94A2axndMxSDp2oL4FQ','wishlist_remove','Removed from wishlist','product',7,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/wish-list/remove?product_id=7','2026-04-02 09:14:18','2026-04-02 09:14:18'),(743,NULL,'C8wye4ZRr0oTaOlcWqWke94A2axndMxSDp2oL4FQ','compare_remove','Removed from compare','product',7,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/compare/remove','2026-04-02 09:14:19','2026-04-02 09:14:19'),(744,NULL,'C8wye4ZRr0oTaOlcWqWke94A2axndMxSDp2oL4FQ','cart_add','Added to cart: Affordable Custom Cricket Jersey With Sublimation Printing','product',5,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-04-02 09:19:13','2026-04-02 09:19:13'),(745,NULL,'C8wye4ZRr0oTaOlcWqWke94A2axndMxSDp2oL4FQ','cart_add','Added to cart: T-shirt, টি-শার্ট','product',9,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-04-02 09:19:16','2026-04-02 09:19:16'),(746,NULL,'C8wye4ZRr0oTaOlcWqWke94A2axndMxSDp2oL4FQ','compare_add','Added to compare: T-shirt, টি-শার্ট','product',9,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/compare/add','2026-04-02 09:19:19','2026-04-02 09:19:19'),(747,NULL,'C8wye4ZRr0oTaOlcWqWke94A2axndMxSDp2oL4FQ','wishlist_add','Added to wishlist: T-shirt, টি-শার্ট','product',9,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/wish-list/add','2026-04-02 09:19:21','2026-04-02 09:19:21'),(748,NULL,'C8wye4ZRr0oTaOlcWqWke94A2axndMxSDp2oL4FQ','search_text','Search: khth',NULL,NULL,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/search/universal','2026-04-02 09:35:08','2026-04-02 09:35:08'),(749,NULL,'C8wye4ZRr0oTaOlcWqWke94A2axndMxSDp2oL4FQ','search_text','Search: khthtii',NULL,NULL,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/search/universal','2026-04-02 09:35:08','2026-04-02 09:35:08'),(750,NULL,'C8wye4ZRr0oTaOlcWqWke94A2axndMxSDp2oL4FQ','search_text','Search: khthtiif',NULL,NULL,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/search/universal','2026-04-02 09:35:09','2026-04-02 09:35:09'),(751,NULL,'C8wye4ZRr0oTaOlcWqWke94A2axndMxSDp2oL4FQ','search_text','Search: khthtiifjhi',NULL,NULL,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/search/universal','2026-04-02 09:35:09','2026-04-02 09:35:09'),(752,NULL,'C8wye4ZRr0oTaOlcWqWke94A2axndMxSDp2oL4FQ','search_text','Search: khthtiifjhiijh',NULL,NULL,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/search/universal','2026-04-02 09:35:09','2026-04-02 09:35:09'),(753,NULL,'C8wye4ZRr0oTaOlcWqWke94A2axndMxSDp2oL4FQ','search_text','Search: khthtiifjhiijhb',NULL,NULL,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/search/universal','2026-04-02 09:35:11','2026-04-02 09:35:11'),(754,NULL,'C8wye4ZRr0oTaOlcWqWke94A2axndMxSDp2oL4FQ','search_text','Search: khthtiifjhiijhbth',NULL,NULL,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/search/universal','2026-04-02 09:35:11','2026-04-02 09:35:11'),(755,NULL,'C8wye4ZRr0oTaOlcWqWke94A2axndMxSDp2oL4FQ','cart_add','Added to cart: স্ক্রিনশট দিয়েছি  Affordable Custom Cricket Jersey With Sublimation Printing','product',4,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-04-02 10:16:11','2026-04-02 10:16:11'),(756,NULL,'C8wye4ZRr0oTaOlcWqWke94A2axndMxSDp2oL4FQ','wishlist_add','Added to wishlist: স্ক্রিনশট দিয়েছি  Affordable Custom Cricket Jersey With Sublimation Printing','product',4,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/wish-list/add','2026-04-02 10:16:13','2026-04-02 10:16:13'),(757,NULL,'C8wye4ZRr0oTaOlcWqWke94A2axndMxSDp2oL4FQ','cart_add','Added to cart: স্ক্রিনশট দিয়েছি  Affordable Custom Cricket Jersey With Sublimation Printing','product',4,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-04-02 10:16:16','2026-04-02 10:16:16'),(758,NULL,'C8wye4ZRr0oTaOlcWqWke94A2axndMxSDp2oL4FQ','cart_add','Added to cart: স্ক্রিনশট দিয়েছি  Affordable Custom Cricket Jersey With Sublimation Printing','product',4,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-04-02 10:16:19','2026-04-02 10:16:19'),(759,NULL,'C8wye4ZRr0oTaOlcWqWke94A2axndMxSDp2oL4FQ','cart_add','Added to cart: Affordable Custom Cricket Jersey With Sublimation Printing','product',5,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-04-02 10:24:30','2026-04-02 10:24:30'),(760,NULL,'C8wye4ZRr0oTaOlcWqWke94A2axndMxSDp2oL4FQ','compare_add','Added to compare: Affordable Custom Cricket Jersey With Sublimation Printing','product',5,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/compare/add','2026-04-02 10:24:32','2026-04-02 10:24:32'),(761,NULL,'C8wye4ZRr0oTaOlcWqWke94A2axndMxSDp2oL4FQ','wishlist_add','Added to wishlist: Affordable Custom Cricket Jersey With Sublimation Printing','product',5,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/wish-list/add','2026-04-02 10:24:33','2026-04-02 10:24:33'),(762,NULL,'C8wye4ZRr0oTaOlcWqWke94A2axndMxSDp2oL4FQ','cart_add','Added to cart: Affordable Custom Cricket Jersey With Sublimation Printing','product',5,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-04-02 10:32:18','2026-04-02 10:32:18'),(763,NULL,'C8wye4ZRr0oTaOlcWqWke94A2axndMxSDp2oL4FQ','wishlist_remove','Removed from wishlist','product',5,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/wish-list/remove?product_id=5','2026-04-02 10:32:21','2026-04-02 10:32:21'),(764,NULL,'C8wye4ZRr0oTaOlcWqWke94A2axndMxSDp2oL4FQ','compare_remove','Removed from compare','product',5,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/compare/remove','2026-04-02 10:32:25','2026-04-02 10:32:25'),(765,NULL,'C8wye4ZRr0oTaOlcWqWke94A2axndMxSDp2oL4FQ','cart_add','Added to cart: Affordable Custom Cricket Jersey With Sublimation Printing','product',5,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-04-02 10:32:27','2026-04-02 10:32:27'),(766,NULL,'C8wye4ZRr0oTaOlcWqWke94A2axndMxSDp2oL4FQ','compare_add','Added to compare: Affordable Custom Cricket Jersey With Sublimation Printing','product',5,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/compare/add','2026-04-02 10:32:30','2026-04-02 10:32:30'),(767,NULL,'C8wye4ZRr0oTaOlcWqWke94A2axndMxSDp2oL4FQ','wishlist_add','Added to wishlist: Affordable Custom Cricket Jersey With Sublimation Printing','product',5,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/wish-list/add','2026-04-02 10:32:32','2026-04-02 10:32:32'),(768,NULL,'C8wye4ZRr0oTaOlcWqWke94A2axndMxSDp2oL4FQ','cart_add','Added to cart: T-shirt, টি-শার্ট','product',7,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-04-02 10:42:36','2026-04-02 10:42:36'),(769,NULL,'C8wye4ZRr0oTaOlcWqWke94A2axndMxSDp2oL4FQ','compare_add','Added to compare: T-shirt, টি-শার্ট','product',7,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/compare/add','2026-04-02 10:42:37','2026-04-02 10:42:37'),(770,NULL,'C8wye4ZRr0oTaOlcWqWke94A2axndMxSDp2oL4FQ','wishlist_add','Added to wishlist: T-shirt, টি-শার্ট','product',7,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/wish-list/add','2026-04-02 10:42:38','2026-04-02 10:42:38'),(771,NULL,'C8wye4ZRr0oTaOlcWqWke94A2axndMxSDp2oL4FQ','cart_add','Added to cart: Affordable Custom Cricket Jersey With Sublimation Printing','product',5,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-04-02 10:42:45','2026-04-02 10:42:45'),(772,NULL,'C8wye4ZRr0oTaOlcWqWke94A2axndMxSDp2oL4FQ','cart_add','Added to cart: Affordable Custom Cricket Jersey With Sublimation Printing','product',5,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-04-02 10:42:47','2026-04-02 10:42:47'),(773,NULL,'UCWNtHbwgXud0Nl4pLLUSMkRZyxhfqPFpPB4fT9W','product_view','Viewed product: Affordable Custom Cricket Jersey With Sublimation Printing','product',5,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/affordable-custom-5','2026-04-02 22:04:29','2026-04-02 22:04:29'),(774,NULL,'UCWNtHbwgXud0Nl4pLLUSMkRZyxhfqPFpPB4fT9W','product_view','Viewed product: RIAZUL ISLAM SHOJOL','product',1,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/riazul-islam-1','2026-04-03 00:15:56','2026-04-03 00:15:56'),(775,NULL,'UCWNtHbwgXud0Nl4pLLUSMkRZyxhfqPFpPB4fT9W','cart_add','Added to cart: WinTerSMM','product',2,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/cart-list/add','2026-04-03 00:51:51','2026-04-03 00:51:51'),(776,NULL,'UCWNtHbwgXud0Nl4pLLUSMkRZyxhfqPFpPB4fT9W','product_view','Viewed product: Cricket Jersey || Affordable Custom Cricket Jersey With Sublimation Printing','product',3,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/product/cricket-jersey-3','2026-04-03 00:52:14','2026-04-03 00:52:14');
/*!40000 ALTER TABLE `user_activity_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_activity_logs_archive`
--

DROP TABLE IF EXISTS `user_activity_logs_archive`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_activity_logs_archive` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `session_id` varchar(100) DEFAULT NULL,
  `action_type` varchar(60) NOT NULL,
  `description` varchar(1000) DEFAULT NULL,
  `model_type` varchar(100) DEFAULT NULL,
  `model_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `device` varchar(50) DEFAULT NULL,
  `browser` varchar(100) DEFAULT NULL,
  `os` varchar(100) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `url` varchar(500) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_activity_logs_archive_user_id_index` (`user_id`),
  KEY `user_activity_logs_archive_action_type_index` (`action_type`),
  KEY `user_activity_logs_archive_ip_address_index` (`ip_address`),
  KEY `user_activity_logs_archive_country_index` (`country`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_activity_logs_archive`
--

LOCK TABLES `user_activity_logs_archive` WRITE;
/*!40000 ALTER TABLE `user_activity_logs_archive` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_activity_logs_archive` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_logins`
--

DROP TABLE IF EXISTS `user_logins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_logins` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `user_ip` varchar(40) DEFAULT NULL,
  `city` varchar(40) DEFAULT NULL,
  `country` varchar(40) DEFAULT NULL,
  `country_code` varchar(40) DEFAULT NULL,
  `longitude` varchar(40) DEFAULT NULL,
  `latitude` varchar(40) DEFAULT NULL,
  `browser` varchar(40) DEFAULT NULL,
  `os` varchar(40) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_logins`
--

LOCK TABLES `user_logins` WRITE;
/*!40000 ALTER TABLE `user_logins` DISABLE KEYS */;
INSERT INTO `user_logins` VALUES (1,1,'103.126.219.219','','','','','','Chrome','Windows 10','2025-10-10 11:24:15','2025-10-10 11:24:15'),(2,2,'103.126.219.219','','','','','','Chrome','Windows 10','2025-10-10 12:18:08','2025-10-10 12:18:08'),(3,3,'103.181.43.24','','','','','','Chrome','Windows 10','2025-10-11 13:19:39','2025-10-11 13:19:39'),(4,4,'103.181.43.24','','','','','','Chrome','Windows 10','2025-10-12 19:33:24','2025-10-12 19:33:24'),(5,5,'103.126.219.219','','','','','','Chrome','Windows 10','2025-10-15 11:42:04','2025-10-15 11:42:04'),(6,6,'103.181.43.24','','','','','','Handheld Browser','Android','2025-10-15 11:48:36','2025-10-15 11:48:36'),(7,7,'49.206.113.94','','','','','','Chrome','Windows 10','2025-11-03 03:52:29','2025-11-03 03:52:29'),(8,8,'127.0.0.1','','','','','','Chrome','Windows 10','2026-02-28 12:51:00','2026-02-28 12:51:00'),(9,8,'127.0.0.1','','','','','','Chrome','Windows 10','2026-02-28 12:51:27','2026-02-28 12:51:27'),(10,9,'127.0.0.1','','','','','','Chrome','Windows 10','2026-03-01 17:39:12','2026-03-01 17:39:12'),(11,9,'127.0.0.1','','','','','','Chrome','Windows 10','2026-03-02 05:49:36','2026-03-02 05:49:36'),(12,9,'127.0.0.1','','','','','','Chrome','Windows 10','2026-03-04 08:13:47','2026-03-04 08:13:47'),(13,9,'127.0.0.1','','','','','','Chrome','Windows 10','2026-03-06 00:05:32','2026-03-06 00:05:32'),(14,9,'127.0.0.1','','','','','','Chrome','Windows 10','2026-03-06 03:23:15','2026-03-06 03:23:15'),(15,9,'127.0.0.1','','','','','','Chrome','Windows 10','2026-03-06 03:37:30','2026-03-06 03:37:30'),(16,9,'127.0.0.1','','','','','','Chrome','Windows 10','2026-03-06 04:32:39','2026-03-06 04:32:39'),(17,9,'127.0.0.1','','','','','','Chrome','Windows 10','2026-03-06 04:48:28','2026-03-06 04:48:28'),(18,10,'127.0.0.1','','','','','','Chrome','Windows 10','2026-03-06 07:24:43','2026-03-06 07:24:43'),(19,9,'127.0.0.1','','','','','','Chrome','Windows 10','2026-03-06 07:31:20','2026-03-06 07:31:20'),(20,9,'127.0.0.1','','','','','','Chrome','Windows 10','2026-03-06 07:38:47','2026-03-06 07:38:47'),(21,9,'127.0.0.1','','','','','','Chrome','Windows 10','2026-03-06 07:43:15','2026-03-06 07:43:15'),(22,9,'127.0.0.1','','','','','','Chrome','Windows 10','2026-03-06 07:44:35','2026-03-06 07:44:35'),(23,9,'127.0.0.1','','','','','','Chrome','Windows 10','2026-03-06 07:53:54','2026-03-06 07:53:54'),(24,11,'127.0.0.1','','','','','','Chrome','Windows 10','2026-03-06 08:15:11','2026-03-06 08:15:11'),(25,12,'192.168.0.78','','','','','','Handheld Browser','Android','2026-03-06 08:27:21','2026-03-06 08:27:21'),(26,9,'127.0.0.1','','','','','','Chrome','Windows 10','2026-03-06 08:30:34','2026-03-06 08:30:34'),(27,12,'192.168.0.78','','','','','','Handheld Browser','Android','2026-03-06 08:35:32','2026-03-06 08:35:32'),(28,9,'127.0.0.1','','','','','','Chrome','Windows 10','2026-03-06 10:55:29','2026-03-06 10:55:29');
/*!40000 ALTER TABLE `user_logins` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_saved_addresses`
--

DROP TABLE IF EXISTS `user_saved_addresses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_saved_addresses` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `country` varchar(100) NOT NULL,
  `division_id` bigint(20) unsigned DEFAULT NULL,
  `district_id` bigint(20) unsigned DEFAULT NULL,
  `thana_id` bigint(20) unsigned DEFAULT NULL,
  `postal_code` varchar(20) DEFAULT NULL,
  `address_line` varchar(500) NOT NULL,
  `address_line_2` varchar(500) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `device_lat` decimal(10,7) DEFAULT NULL,
  `device_lng` decimal(10,7) DEFAULT NULL,
  `verified_status` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `is_default` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `label` varchar(50) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_saved_addresses_user_id_index` (`user_id`),
  KEY `user_saved_addresses_user_id_is_default_index` (`user_id`,`is_default`),
  CONSTRAINT `user_saved_addresses_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_saved_addresses`
--

LOCK TABLES `user_saved_addresses` WRITE;
/*!40000 ALTER TABLE `user_saved_addresses` DISABLE KEYS */;
INSERT INTO `user_saved_addresses` VALUES (1,8,'Bangladesh',3,18,NULL,'1207','fbfb','dbdbd','','Dhaka',NULL,NULL,0,1,NULL,'2026-02-28 12:53:56','2026-02-28 12:53:56');
/*!40000 ALTER TABLE `user_saved_addresses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_search_logs`
--

DROP TABLE IF EXISTS `user_search_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_search_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `query` varchar(500) NOT NULL COMMENT 'User search keyword/phrase',
  `user_id` bigint(20) unsigned DEFAULT NULL COMMENT 'Logged-in user; null for guest',
  `ip` varchar(45) DEFAULT NULL,
  `user_agent` varchar(512) DEFAULT NULL,
  `results_count` int(10) unsigned NOT NULL DEFAULT 0 COMMENT 'Number of results returned',
  `source` varchar(20) NOT NULL DEFAULT 'universal' COMMENT 'universal|voice|image',
  `image_path` varchar(500) DEFAULT NULL COMMENT 'Image search uploaded file path (WebP)',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_search_logs_query_index` (`query`),
  KEY `user_search_logs_user_id_index` (`user_id`),
  KEY `user_search_logs_ip_index` (`ip`),
  CONSTRAINT `user_search_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=95 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_search_logs`
--

LOCK TABLES `user_search_logs` WRITE;
/*!40000 ALTER TABLE `user_search_logs` DISABLE KEYS */;
INSERT INTO `user_search_logs` VALUES (1,'hrhrhbf',8,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36',0,'products_page',NULL,'2026-02-28 12:52:04',NULL),(2,'r5jjntkyf  opu',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36',0,'products_page',NULL,'2026-02-28 13:25:53',NULL),(3,'r5jjntkyf  opu',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36',0,'products_page',NULL,'2026-03-01 07:18:00',NULL),(4,'hrhrhbf',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36',0,'products_page',NULL,'2026-03-01 08:32:37',NULL),(5,'r5jjntkyf  opu',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36',0,'products_page',NULL,'2026-03-01 17:10:29',NULL),(6,'hrhrhbf',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36',0,'products_page',NULL,'2026-03-01 17:10:29',NULL),(7,'r5jjntkyf  opu',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36',0,'products_page',NULL,'2026-03-02 05:48:18',NULL),(8,'hrhrhbf',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36',0,'products_page',NULL,'2026-03-02 05:48:20',NULL),(9,'r5jjntkyf  opu',9,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36',0,'products_page',NULL,'2026-03-02 07:10:22',NULL),(10,'hrhrhbf',9,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36',0,'products_page',NULL,'2026-03-02 07:10:26',NULL),(11,'hrhrhbf',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36',0,'products_page',NULL,'2026-03-03 09:18:08',NULL),(12,'hrhrhbf',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36',0,'products_page',NULL,'2026-03-03 09:18:17',NULL),(13,'hrhrhbf',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36',0,'products_page',NULL,'2026-03-03 09:18:20',NULL),(14,'r5jjntkyf  opu',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36',0,'products_page',NULL,'2026-03-03 09:26:49',NULL),(15,'hrhrhbf',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36',0,'products_page',NULL,'2026-03-03 09:27:38',NULL),(16,'hrhrhbf',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36',0,'products_page',NULL,'2026-03-03 09:38:58',NULL),(17,'hrhrhbf',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36',0,'products_page',NULL,'2026-03-03 09:46:50',NULL),(18,'hrhrhbf',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36',0,'products_page',NULL,'2026-03-03 09:47:06',NULL),(19,'r5jjntkyf  opu',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36',0,'products_page',NULL,'2026-03-03 09:47:14',NULL),(20,'...',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36',0,'products_page',NULL,'2026-03-03 09:49:24',NULL),(21,'...',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36',0,'products_page',NULL,'2026-03-03 10:05:42',NULL),(22,'...',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36',0,'products_page',NULL,'2026-03-03 10:31:10',NULL),(23,'...',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36',0,'products_page',NULL,'2026-03-03 10:31:34',NULL),(24,'...',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36',0,'products_page',NULL,'2026-03-03 10:31:43',NULL),(25,'...',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36',0,'products_page',NULL,'2026-03-04 08:13:17',NULL),(26,'C',9,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36',52,'universal',NULL,'2026-03-04 08:15:31',NULL),(27,'CO',9,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36',38,'universal',NULL,'2026-03-04 08:15:32',NULL),(28,'COM',9,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36',15,'universal',NULL,'2026-03-04 08:15:32',NULL),(29,'COMP',9,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36',6,'universal',NULL,'2026-03-04 08:15:45',NULL),(30,'COMPA',9,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36',3,'universal',NULL,'2026-03-04 08:15:45',NULL),(31,'COMPAR',9,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36',2,'universal',NULL,'2026-03-04 08:15:47',NULL),(32,'COMPARE',9,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36',2,'universal',NULL,'2026-03-04 08:16:05',NULL),(33,'COMPARE',9,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36',2,'universal',NULL,'2026-03-04 08:16:41',NULL),(34,'COMPARE',9,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36',2,'universal',NULL,'2026-03-04 08:16:49',NULL),(35,'COMPARE',9,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36',2,'universal',NULL,'2026-03-04 08:16:56',NULL),(36,'COMPARE',9,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36',2,'universal',NULL,'2026-03-04 08:26:03',NULL),(37,'l',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36',59,'universal',NULL,'2026-03-11 09:04:58',NULL),(38,'ln',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36',0,'universal',NULL,'2026-03-11 09:04:59',NULL),(39,'lns',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36',0,'universal',NULL,'2026-03-11 09:05:00',NULL),(40,'lns',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36',0,'universal',NULL,'2026-03-11 09:05:08',NULL),(41,'lnsp',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36',0,'universal',NULL,'2026-03-11 09:05:09',NULL),(42,'lnspe',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36',0,'universal',NULL,'2026-03-11 09:05:10',NULL),(43,'lnspec',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36',0,'universal',NULL,'2026-03-11 09:05:11',NULL),(44,'lnspec',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36',0,'universal',NULL,'2026-03-11 09:05:16',NULL),(45,'lnspect',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36',0,'universal',NULL,'2026-03-11 09:05:19',NULL),(46,'lnspect',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36',0,'universal',NULL,'2026-03-11 09:18:02',NULL),(47,'lnspect',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36',0,'universal',NULL,'2026-03-11 09:40:24',NULL),(48,'lnspect',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36',0,'universal',NULL,'2026-03-11 09:40:26',NULL),(49,'action',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36',3,'universal',NULL,'2026-03-13 07:06:05',NULL),(50,'d',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',53,'universal',NULL,'2026-03-18 03:35:10',NULL),(51,'dg',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',6,'universal',NULL,'2026-03-18 03:35:10',NULL),(52,'dgdg',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',0,'universal',NULL,'2026-03-18 03:35:10',NULL),(53,'dgdg',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',0,'products_page',NULL,'2026-03-18 03:35:11',NULL),(54,'G',NULL,'192.168.0.58','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36',53,'universal',NULL,'2026-04-01 15:23:21',NULL),(55,'Gt',NULL,'192.168.0.58','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36',0,'universal',NULL,'2026-04-01 15:23:21',NULL),(56,'Gtt',NULL,'192.168.0.58','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36',0,'universal',NULL,'2026-04-01 15:23:23',NULL),(57,'Gtth',NULL,'192.168.0.58','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36',0,'universal',NULL,'2026-04-01 15:23:25',NULL),(58,'Gtthf',NULL,'192.168.0.58','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36',0,'universal',NULL,'2026-04-01 15:23:25',NULL),(59,'Gtthff',NULL,'192.168.0.58','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36',0,'universal',NULL,'2026-04-01 15:23:25',NULL),(60,'Gtthffg',NULL,'192.168.0.58','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36',0,'universal',NULL,'2026-04-01 15:23:26',NULL),(61,'Gtthffgg',NULL,'192.168.0.58','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36',0,'universal',NULL,'2026-04-01 15:23:26',NULL),(62,'Gtthffggg',NULL,'192.168.0.58','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36',0,'universal',NULL,'2026-04-01 15:23:26',NULL),(63,'Gtthffgggf',NULL,'192.168.0.58','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36',0,'universal',NULL,'2026-04-01 15:23:28',NULL),(64,'Gtthffgggfr',NULL,'192.168.0.58','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36',0,'universal',NULL,'2026-04-01 15:23:32',NULL),(65,'Gtthffgggfrt',NULL,'192.168.0.58','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36',0,'universal',NULL,'2026-04-01 15:23:32',NULL),(66,'Gtthffgggfrtt',NULL,'192.168.0.58','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36',0,'universal',NULL,'2026-04-01 15:23:33',NULL),(67,'Gtthffgggfrttt',NULL,'192.168.0.58','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36',0,'universal',NULL,'2026-04-01 15:23:33',NULL),(68,'Gtthffgggfrtttt',NULL,'192.168.0.58','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36',0,'universal',NULL,'2026-04-01 15:23:33',NULL),(69,'Gtthffgggfrttttt',NULL,'192.168.0.58','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36',0,'universal',NULL,'2026-04-01 15:23:34',NULL),(70,'Gtthffgggfrtttttt',NULL,'192.168.0.58','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36',0,'universal',NULL,'2026-04-01 15:23:36',NULL),(71,'Gtthffgggfrttttttr',NULL,'192.168.0.58','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36',0,'universal',NULL,'2026-04-01 15:23:38',NULL),(72,'Gtthffgggfrttttttrú',NULL,'192.168.0.58','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36',0,'universal',NULL,'2026-04-01 15:23:39',NULL),(73,'h',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',53,'universal',NULL,'2026-04-02 07:35:27',NULL),(74,'ho',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',43,'universal',NULL,'2026-04-02 07:35:29',NULL),(75,'hom',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',18,'universal',NULL,'2026-04-02 07:35:30',NULL),(76,'hom o',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',64,'universal',NULL,'2026-04-02 07:35:30',NULL),(77,'hom op',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',45,'universal',NULL,'2026-04-02 07:35:31',NULL),(78,'hom opu',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',30,'universal',NULL,'2026-04-02 07:35:31',NULL),(79,'hom opu',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',0,'products_page',NULL,'2026-04-02 07:35:34',NULL),(80,'hom opu',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',0,'products_page',NULL,'2026-04-02 07:41:29',NULL),(81,'Hello microphone test microphone test',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',5,'universal',NULL,'2026-04-02 07:45:53',NULL),(82,'Hello microphone test microphone test',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',5,'universal',NULL,'2026-04-02 07:45:57',NULL),(83,'Hello microphone test microphone testg',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',5,'universal',NULL,'2026-04-02 07:46:04',NULL),(84,'Hello microphone test microphone testggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggg',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',5,'universal',NULL,'2026-04-02 07:46:08',NULL),(85,'Hello microphone test microphone testggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggg',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',5,'universal',NULL,'2026-04-02 07:46:40',NULL),(86,'ggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggg',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',0,'universal',NULL,'2026-04-02 07:47:01',NULL),(87,'khth',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',0,'universal',NULL,'2026-04-02 09:35:08',NULL),(88,'khthtii',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',0,'universal',NULL,'2026-04-02 09:35:08',NULL),(89,'khthtiif',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',0,'universal',NULL,'2026-04-02 09:35:09',NULL),(90,'khthtiifjhi',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',0,'universal',NULL,'2026-04-02 09:35:09',NULL),(91,'khthtiifjhiijh',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',0,'universal',NULL,'2026-04-02 09:35:09',NULL),(92,'khthtiifjhiijhb',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',0,'universal',NULL,'2026-04-02 09:35:11',NULL),(93,'khthtiifjhiijhbth',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',0,'universal',NULL,'2026-04-02 09:35:11',NULL),(94,'khthtiifjhiijhbth',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',0,'products_page',NULL,'2026-04-02 09:35:18',NULL);
/*!40000 ALTER TABLE `user_search_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_tokens`
--

DROP TABLE IF EXISTS `user_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_tokens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_tokens`
--

LOCK TABLES `user_tokens` WRITE;
/*!40000 ALTER TABLE `user_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `firstname` varchar(40) DEFAULT NULL,
  `lastname` varchar(40) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `username` varchar(40) NOT NULL,
  `username_editable` tinyint(3) unsigned NOT NULL DEFAULT 1 COMMENT '1=auto-generated username, can edit once; 0=user set at registration, cannot edit',
  `email` varchar(40) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `country_code` varchar(40) DEFAULT NULL,
  `mobile` varchar(40) DEFAULT NULL,
  `whatsapp_identity` varchar(255) DEFAULT NULL,
  `telegram_username` varchar(255) DEFAULT NULL,
  `contact_channel_opt_in` tinyint(1) NOT NULL DEFAULT 1,
  `age` tinyint(3) unsigned DEFAULT NULL,
  `gender` varchar(20) DEFAULT NULL COMMENT 'male, female, other',
  `ref_by` int(10) unsigned NOT NULL DEFAULT 0,
  `balance` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `points` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `loyalty_points` int(11) NOT NULL DEFAULT 0,
  `image` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `address` text DEFAULT NULL COMMENT 'contains full address',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '0: banned, 1: active',
  `cod_failed_count` smallint(5) unsigned NOT NULL DEFAULT 0,
  `cod_disabled_until` timestamp NULL DEFAULT NULL,
  `kyc_data` text DEFAULT NULL,
  `ev` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0: email unverified, 1: email verified',
  `kv` tinyint(1) DEFAULT 0 COMMENT '0: kyc unverfied , 2 Kyc Pending,1=Kyc verified',
  `sv` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0: mobile unverified, 1: mobile verified',
  `profile_complete` tinyint(1) NOT NULL DEFAULT 0,
  `ver_code` varchar(40) DEFAULT NULL COMMENT 'stores verification code',
  `ver_code_send_at` datetime DEFAULT NULL COMMENT 'verification send time',
  `ts` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0: 2fa off, 1: 2fa on',
  `tv` tinyint(1) NOT NULL DEFAULT 1 COMMENT '0: 2fa unverified, 1: 2fa verified',
  `tsc` varchar(255) DEFAULT NULL,
  `ban_reason` varchar(255) DEFAULT NULL,
  `remember_token` varchar(255) DEFAULT NULL,
  `last_chat_seen_at` timestamp NULL DEFAULT NULL COMMENT 'When user last viewed live chat; used for unread admin reply count',
  `provider` varchar(255) DEFAULT NULL,
  `provider_id` varchar(255) DEFAULT NULL,
  `access_token` varchar(1024) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`,`email`),
  KEY `users_whatsapp_identity_index` (`whatsapp_identity`),
  KEY `users_telegram_username_index` (`telegram_username`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'RIAZUL ISLAM','SHOJOL',NULL,'jjjnjhnhn',1,'vfvfbfbf@gmail.com',NULL,'BD','8801766666666',NULL,NULL,1,NULL,NULL,0,0.00000000,0.00000000,0,NULL,'$2y$10$fxKfT2KnSXxhsK/2rvLYkOOe9kxdALD1cJbDXQsNvwgBiTRPdDobe','{\"country\":\"Bangladesh\",\"address\":\"\\u0997\\u09a1\\u09bc\\u09bf\\u09af\\u09bc\\u09be \\u09a8\\u09a4\\u09c1\\u09a8 \\u09b9\\u09be\\u099f\",\"state\":\"Dhaka\",\"zip\":\"17493\",\"city\":\"\\u09ac\\u09b0\\u09bf\\u09b6\\u09be\\u09b2\"}',1,0,NULL,NULL,1,1,1,1,NULL,NULL,0,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-10 11:24:15','2025-10-10 11:24:28'),(2,'RIAZUL ISLAM','SHOJOL',NULL,'hhuhuhu',1,'vfvfbf@gmail.com',NULL,'BD','8801999999999',NULL,NULL,1,NULL,NULL,0,0.00000000,0.00000000,0,NULL,'$2y$10$zj.bFbXxDKnsQqFK0qo6G.h1bEOv9n9XsYgLoByPU2OLgb7wTP9fq','{\"country\":\"Bangladesh\",\"address\":\"\\u0997\\u09a1\\u09bc\\u09bf\\u09af\\u09bc\\u09be \\u09a8\\u09a4\\u09c1\\u09a8 \\u09b9\\u09be\\u099f\",\"state\":\"Dhaka\",\"zip\":\"17493\",\"city\":\"\\u09ac\\u09b0\\u09bf\\u09b6\\u09be\\u09b2\"}',1,0,NULL,NULL,1,1,1,1,NULL,NULL,0,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-10 12:18:08','2025-10-10 12:18:30'),(3,'ygygyff','ffff',NULL,'adminkys',1,'info@wintersmm.com',NULL,'BD','8801388888888',NULL,NULL,1,NULL,NULL,0,0.00000000,0.00000000,0,NULL,'$2y$10$F8aJqPLN86J66i2I1bWpbOuyU5lJUTxY8wPoPgrIkHeOAOz8cY2cW','{\"country\":\"Bangladesh\",\"address\":\"hjhbf\",\"state\":\"545\",\"zip\":\"65\",\"city\":\"dfghh\"}',1,0,NULL,NULL,1,1,1,1,NULL,NULL,0,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-11 13:19:39','2025-10-11 13:20:04'),(4,'eefefeefehhh','vfvkk',NULL,'nillislam03',1,'xx@gmail.com',NULL,'AF','931909876543',NULL,NULL,1,NULL,NULL,0,0.00000000,0.00000000,0,NULL,'$2y$10$ztq.D51jgZGHuY1GXFCtJu2VmNykRK02MnqT5dLT67HHLjxZ9QTme','{\"country\":\"Bangladesh\",\"address\":\"\\u0997\\u09a1\\u09bc\\u09bf\\u09af\\u09bc\\u09be \\u09a8\\u09a4\\u09c1\\u09a8 \\u09b9\\u09be\\u099f\",\"state\":\"Dhaka\",\"zip\":\"17493\",\"city\":\"\\u09ac\\u09b0\\u09bf\\u09b6\\u09be\\u09b2\"}',1,0,NULL,NULL,1,1,1,1,NULL,NULL,0,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-12 19:33:24','2025-10-12 19:42:51'),(5,'opu','mia',NULL,'opumia',1,'jnjguurghh@gmail.com',NULL,'BD','88053445354353',NULL,NULL,1,NULL,NULL,0,0.00000000,0.00000000,0,NULL,'$2y$10$IflPI43FbxiprAxJqW6fTeVJA/vaVXOBYnRdw.eakPhP.5OhP07re','{\"country\":\"Bangladesh\",\"address\":\"fvjfjbjfh\",\"state\":\"1207\",\"zip\":\"1207\",\"city\":\"Dhaka\"}',1,0,NULL,NULL,1,1,1,1,NULL,NULL,0,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-15 11:42:04','2026-02-28 11:57:42'),(6,'Md rifat','Mia',NULL,'test1333',1,'limonq56@gmail.com',NULL,'AF','931325632562',NULL,NULL,1,NULL,NULL,0,0.00000000,0.00000000,0,NULL,'$2y$10$E7GvVEWwaJGouqcbvOBBYOj3EDlwXNbd8..7j4N70TZEci6//PYhK','{\"country\":\"Afghanistan\",\"address\":\"Noyakhali\",\"state\":\"Noakhali\",\"zip\":\"182\",\"city\":\"Noakhali\"}',1,0,NULL,NULL,1,1,1,1,NULL,NULL,0,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-15 11:48:36','2025-10-15 11:49:09'),(7,'john','Denilraj',NULL,'dsrgtsert',1,'a9tndw7ouy@wnbaldwy.com',NULL,'IN','918968468734',NULL,NULL,1,NULL,NULL,0,0.00000000,0.00000000,0,NULL,'$2y$10$Vb7t4HomSzLTqm9Z5tPoq.j9k2MnVlNwbffWtZIapqDsLw5g8owd2','{\"country\":\"India\",\"address\":\"765 Main Road\",\"state\":\"Tamilnadu\",\"zip\":\"657843\",\"city\":\"cbe\"}',1,0,NULL,NULL,1,1,1,1,NULL,NULL,0,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-11-03 03:52:29','2025-11-03 03:52:35'),(8,'bbff','fhnfhb',NULL,'opumiaxb',1,'sfdbfhfd@gmail.com',NULL,'BD','8801996522331',NULL,NULL,1,28,'male',0,0.00000000,0.00000000,0,NULL,'$2y$10$l8mWdFnwTiH6G9Do3xwZI.vZdxHbC6rRnasgc4c/oKBj40p26VWW.','{\"address\":\"fbfb\",\"address_2\":\"dbdbd\",\"state\":null,\"zip\":\"1207\",\"country\":\"Bangladesh\",\"city\":\"Dhaka\",\"thana\":null,\"division\":\"Dhaka\"}',1,0,NULL,'{\"newsletter_subscribe\":0}',1,1,1,1,NULL,NULL,0,1,NULL,NULL,NULL,'2026-02-28 13:06:11',NULL,NULL,NULL,'2026-02-28 12:51:00','2026-02-28 12:53:56'),(9,'opu','mia',NULL,'dhvhrg',1,'opumia@gmail.com',NULL,'AF','nomobile17724083524999','1772408352','opumiagfgh',1,15,'male',0,0.00000000,0.00000000,0,'69a4ce7f011331772408447.png','$2y$10$T.VeJsziGCeAaxNN.2Yibuj.B/X7HY.uS8uLJMMYKLD/TwD7aOM2C','{\"address\":null,\"address_2\":\"\",\"state\":\"\",\"zip\":\"\",\"country\":\"Afghanistan\",\"city\":\"\",\"division\":\"\",\"thana\":\"\"}',1,0,NULL,'{\"newsletter_subscribe\":0,\"occupation\":null,\"alternate_phone\":null}',1,1,1,1,NULL,NULL,0,1,NULL,NULL,'c5eIU1yRXaVHjU7eXnQFPLA2Fa8iCSBlu80ApB99PV9Gs9TwS4YxOOjHiVGs','2026-03-09 09:36:17',NULL,NULL,NULL,'2026-03-01 17:39:12','2026-03-02 08:19:28'),(10,'opu','mia',NULL,'opumiad',0,'jgtghnj@gmail.com',NULL,'AF','nomobile17728034837082',NULL,NULL,1,0,NULL,0,0.00000000,0.00000000,0,NULL,'$2y$10$/iyDJ3jNuOFsExIANNnXPOM6dtx2v9Kdpe/xcIYquUbEJI43VNQZu','{\"address\":\"\",\"state\":\"\",\"zip\":\"\",\"country\":\"Afghanistan\",\"city\":\"\",\"division\":\"\",\"district\":\"\",\"thana\":\"\"}',1,0,NULL,'{\"newsletter_subscribe\":0}',1,1,1,1,NULL,NULL,0,1,NULL,NULL,NULL,'2026-03-06 07:29:50',NULL,NULL,NULL,'2026-03-06 07:24:43','2026-03-06 07:24:43'),(11,'hi','opu',NULL,'gutuhg',0,'sfdbfhfed@gmail.com',NULL,'AF','nomobile17728065114661',NULL,NULL,1,0,NULL,0,0.00000000,0.00000000,0,NULL,'$2y$10$jCNfh4978RgYxgKqA9bkVeK.ETJDB.tptf7ZkDMjmKtI4t30l1GuK','{\"address\":\"\",\"state\":\"\",\"zip\":\"\",\"country\":\"Afghanistan\",\"city\":\"\",\"division\":\"\",\"district\":\"\",\"thana\":\"\"}',1,0,NULL,'{\"newsletter_subscribe\":0}',1,1,1,1,NULL,NULL,0,1,NULL,NULL,NULL,'2026-03-06 08:15:44',NULL,NULL,NULL,'2026-03-06 08:15:11','2026-03-06 08:15:11'),(12,'Ydu','Ygjjg',NULL,'fgryjvtu',0,'420opu@gmail.com',NULL,'AF','nomobile17728072411793',NULL,NULL,1,0,NULL,0,0.00000000,0.00000000,0,NULL,'$2y$10$BNKbqb7UaunhPIfGgtlWP.U37mZmDzImVKNNVBIH/hNWBpKbhrJ0C','{\"address\":\"\",\"state\":\"\",\"zip\":\"\",\"country\":\"Afghanistan\",\"city\":\"\",\"division\":\"\",\"district\":\"\",\"thana\":\"\"}',1,0,NULL,'{\"newsletter_subscribe\":0}',1,1,1,1,NULL,NULL,0,1,NULL,NULL,NULL,'2026-03-06 10:58:32',NULL,NULL,NULL,'2026-03-06 08:27:21','2026-03-06 08:27:21');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wishlists`
--

DROP TABLE IF EXISTS `wishlists`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wishlists` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `product_id` int(10) unsigned NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wishlists`
--

LOCK TABLES `wishlists` WRITE;
/*!40000 ALTER TABLE `wishlists` DISABLE KEYS */;
INSERT INTO `wishlists` VALUES (2,8,2,'2026-02-28 12:52:16','2026-02-28 12:52:16'),(3,9,1,'2026-03-02 12:51:25','2026-03-02 12:51:25'),(4,9,2,'2026-03-02 13:07:46','2026-03-02 13:07:46'),(6,9,4,'2026-03-02 13:07:51','2026-03-02 13:07:51'),(7,9,5,'2026-03-02 13:07:54','2026-03-02 13:07:54');
/*!40000 ALTER TABLE `wishlists` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'staylbd_wintersm'
--

--
-- Final view structure for view `courier_daily_stats`
--

/*!50001 DROP VIEW IF EXISTS `courier_daily_stats`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `courier_daily_stats` AS select `courier_logs`.`courier_type` AS `courier_type`,cast(`courier_logs`.`created_at` as date) AS `date`,count(0) AS `total_orders`,sum(case when `courier_logs`.`status` = 'success' then 1 else 0 end) AS `successful_orders`,sum(case when `courier_logs`.`status` = 'failed' then 1 else 0 end) AS `failed_orders` from `courier_logs` group by `courier_logs`.`courier_type`,cast(`courier_logs`.`created_at` as date) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-05-05  0:00:46
