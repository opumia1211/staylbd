-- StayLBD Master DB - Import only this file in cPanel. No migrations/patches required.
CREATE DATABASE IF NOT EXISTS `wintersm_tt` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `wintersm_tt`;

-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: wintersm_tt
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
  `cart_value` decimal(18,2) DEFAULT 0.00,
  `checkout_started_at` timestamp NULL DEFAULT NULL,
  `last_activity_at` timestamp NULL DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `device_type` varchar(50) DEFAULT NULL,
  `email` varchar(191) DEFAULT NULL,
  `mobile` varchar(50) DEFAULT NULL,
  `status` varchar(20) DEFAULT 'pending' COMMENT 'pending, abandoned, recovered',
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `abandoned_carts`
--

LOCK TABLES `abandoned_carts` WRITE;
/*!40000 ALTER TABLE `abandoned_carts` DISABLE KEYS */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin_lockouts`
--

LOCK TABLES `admin_lockouts` WRITE;
/*!40000 ALTER TABLE `admin_lockouts` DISABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin_notifications`
--

LOCK TABLES `admin_notifications` WRITE;
/*!40000 ALTER TABLE `admin_notifications` DISABLE KEYS */;
INSERT INTO `admin_notifications` VALUES (1,1,'New member registered',0,'/sajaladminopu/users/detail/1','2025-10-10 11:24:15','2025-10-10 11:24:15'),(2,2,'New member registered',0,'/sajaladminopu/users/detail/2','2025-10-10 12:18:08','2025-10-10 12:18:08'),(3,2,'Deposit request from hhuhuhu',0,'/sajaladminopu/deposit/details/1','2025-10-10 12:27:55','2025-10-10 12:27:55'),(4,2,'Order successfully placed.',1,'/sajaladminopu/order/details/1','2025-10-10 12:27:55','2025-10-10 12:35:38'),(5,3,'New member registered',0,'/sajaladminopu/users/detail/3','2025-10-11 13:19:39','2025-10-11 13:19:39'),(6,4,'New member registered',0,'/sajaladminopu/users/detail/4','2025-10-12 19:33:24','2025-10-12 19:33:24'),(7,5,'New member registered',0,'/sajaladminopu/users/detail/5','2025-10-15 11:42:04','2025-10-15 11:42:04'),(8,6,'New member registered',1,'/sajaladminopu/users/detail/6','2025-10-15 11:48:36','2025-10-16 02:09:40'),(9,0,'A new contact message has been submitted',0,'/sajaladminopu/messages/view/1','2025-10-22 06:39:58','2025-10-22 06:39:58'),(10,7,'New member registered',0,'/sajaladminopu/users/detail/7','2025-11-03 03:52:29','2025-11-03 03:52:29');
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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin_sessions`
--

LOCK TABLES `admin_sessions` WRITE;
/*!40000 ALTER TABLE `admin_sessions` DISABLE KEYS */;
INSERT INTO `admin_sessions` VALUES (3,1,'q3BUtT06DoWGxxYgdVzE7TJ6dxDCoO30dvKD5CSM','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36','2026-03-19 05:08:43','2026-03-19 04:42:48','2026-03-19 05:08:43');
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
INSERT INTO `admins` VALUES (1,'Owner Opu','digitalzero.com@gmail.com','+8801996522333',NULL,'Owner',NULL,'68eb0a278e3421760234023.png','$2y$10$lXymqU3OT5HMCxo1n1nlJeOt5l3CXF0mr.duHexukQpHVRjtAeFpW','owner',NULL,'uRBQNMXY6jgNVZHhNVOa3HbuhSUBC2LzYJX5G8dY4S5Qp2qCQEuyqnLt5ikQ','eyJpdiI6IjB3NUg3Nlk0RG1lZEJGVTIyTy8valE9PSIsInZhbHVlIjoiUytXam9mQlVkMHdLNE1HS2pZREVKemw2bGxaTFJyazRmeW4wUk5kbFpvYz0iLCJtYWMiOiJkNjYwMmI3ZTAwOTMyNGRiNjUwNGRlM2I5ZGYzYWY0N2I0NWU1MzYwNzRjYTQzNmNiMjNlODBmY2FhMWU5Yzc3IiwidGFnIjoiIn0=','[\"2Y4WZB6Q\",\"2LP8J7KN\",\"GH3ZEFPM\",\"G72D6DV7\",\"859SCUQ5\",\"3RAQRNGD\",\"CL668R3B\",\"TU4UFBFN\"]','2026-03-19 04:42:47',0,NULL,'2026-03-19 04:42:47');
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
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `banner_analytics`
--

LOCK TABLES `banner_analytics` WRITE;
/*!40000 ALTER TABLE `banner_analytics` DISABLE KEYS */;
INSERT INTO `banner_analytics` VALUES (1,106,'impression','desktop',NULL,'2026-02-11 07:58:27'),(2,102,'impression','desktop',NULL,'2026-02-11 07:58:32'),(3,106,'impression','desktop',NULL,'2026-02-11 07:58:37'),(4,102,'impression','desktop',NULL,'2026-02-11 07:58:42'),(5,106,'impression','desktop',NULL,'2026-02-11 07:58:47'),(6,102,'impression','desktop',NULL,'2026-02-11 07:58:52'),(7,106,'impression','desktop',NULL,'2026-02-11 07:58:57'),(8,102,'impression','desktop',NULL,'2026-02-11 07:59:02'),(9,106,'impression','desktop',NULL,'2026-02-11 07:59:07'),(10,102,'impression','desktop',NULL,'2026-02-11 07:59:12'),(11,106,'impression','desktop',NULL,'2026-02-11 07:59:17'),(12,102,'impression','desktop',NULL,'2026-02-11 07:59:22'),(13,106,'impression','desktop',NULL,'2026-02-11 07:59:27'),(14,102,'impression','desktop',NULL,'2026-02-11 07:59:32'),(15,106,'impression','desktop',NULL,'2026-02-11 07:59:37'),(16,102,'impression','desktop',NULL,'2026-02-11 07:59:42'),(17,106,'impression','desktop',NULL,'2026-02-11 07:59:47'),(18,102,'impression','desktop',NULL,'2026-02-11 07:59:52'),(19,106,'impression','desktop',NULL,'2026-02-11 07:59:57'),(20,102,'impression','desktop',NULL,'2026-02-11 08:00:02'),(21,106,'impression','desktop',NULL,'2026-02-11 08:00:07'),(22,102,'impression','desktop',NULL,'2026-02-11 08:00:12'),(23,102,'impression','desktop',NULL,'2026-02-11 08:00:18'),(24,106,'impression','desktop',NULL,'2026-02-11 08:00:23'),(25,102,'impression','desktop',NULL,'2026-02-27 05:50:13'),(26,102,'impression','desktop',NULL,'2026-02-27 05:50:20'),(27,106,'impression','desktop',NULL,'2026-02-27 05:50:25');
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `brands`
--

LOCK TABLES `brands` WRITE;
/*!40000 ALTER TABLE `brands` DISABLE KEYS */;
INSERT INTO `brands` VALUES (1,'WinTerSMM','68e9423035ff61760117296.png',0,1,'2025-10-10 11:28:16','2025-10-10 11:28:16');
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
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `carts`
--

LOCK TABLES `carts` WRITE;
/*!40000 ALTER TABLE `carts` DISABLE KEYS */;
INSERT INTO `carts` VALUES (1,1,1,NULL,NULL,5,'2025-10-10 11:53:10','2025-10-10 11:53:42'),(3,3,1,NULL,NULL,2,'2025-10-11 13:19:39','2025-10-11 13:20:29'),(4,4,1,NULL,NULL,2,'2025-10-12 19:33:24','2025-10-15 10:45:46'),(5,5,1,NULL,NULL,1,'2025-10-15 11:42:04','2025-10-15 11:42:04'),(10,6,2,NULL,NULL,1,'2025-10-16 04:46:14','2025-10-16 04:46:14'),(11,6,1,NULL,NULL,4,'2025-10-16 07:54:31','2025-10-16 11:07:51'),(12,7,1,NULL,NULL,2,'2025-11-03 03:52:29','2025-11-03 03:52:29');
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (1,'WinTerSMM','6916f16ba6a5f1763111275.png',0,1,0,1,'2025-10-10 11:27:15','2025-11-14 03:07:55','public',NULL);
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
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=CURRENT_USER*/ /*!50003 TRIGGER `update_courier_statistics`

            AFTER INSERT ON `courier_logs`

            FOR EACH ROW

            INSERT INTO courier_statistics (courier_type, date, total_orders, successful_orders, failed_orders)

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
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `order_id` int(10) unsigned NOT NULL DEFAULT 0,
  `method_code` int(10) unsigned NOT NULL DEFAULT 0,
  `amount` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `method_currency` varchar(40) DEFAULT NULL,
  `charge` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `rate` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `final_amo` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `detail` text DEFAULT NULL,
  `btc_amo` varchar(255) DEFAULT NULL,
  `btc_wallet` varchar(255) DEFAULT NULL,
  `trx` varchar(40) DEFAULT NULL,
  `payment_try` int(11) NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '1=>success, 2=>pending, 3=>cancel',
  `from_api` tinyint(1) NOT NULL DEFAULT 0,
  `admin_feedback` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `deposits_created_at_index` (`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=83 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `deposits`
--

LOCK TABLES `deposits` WRITE;
/*!40000 ALTER TABLE `deposits` DISABLE KEYS */;
INSERT INTO `deposits` VALUES (1,2,1,1000,91.00000000,'USDT',2.82000000,120.00000000,11258.40000000,'[]','0','','OQXN824REWTF',0,3,0,'pl','2025-10-10 12:27:48','2025-10-16 13:32:13'),(2,3,8,120,181.00000000,'USD',0.00000000,1.00000000,181.00000000,NULL,'0','','DGG5URG39KS7',0,0,0,NULL,'2025-10-11 14:54:23','2025-10-11 14:54:23'),(3,4,9,501,100.00000000,'BTC',0.00000000,1.00000000,100.00000000,NULL,'0','','584BUM39DB53',0,0,0,NULL,'2025-10-12 19:44:35','2025-10-12 19:44:35'),(4,4,10,902,100.00000000,'BDT',0.00000000,1.00000000,100.00000000,NULL,'0','','R1X2987FCR12',0,0,0,NULL,'2025-10-13 10:51:58','2025-10-13 10:51:58'),(5,4,10,902,100.00000000,'BDT',0.00000000,1.00000000,100.00000000,NULL,'0','','R1X2987FCR12',0,0,0,NULL,'2025-10-13 10:52:33','2025-10-13 10:52:33'),(6,4,11,902,100.00000000,'BDT',0.00000000,1.00000000,100.00000000,NULL,'0','','WWUSWDM9Y241',0,0,0,NULL,'2025-10-13 11:01:02','2025-10-13 11:01:02'),(7,4,12,902,100.00000000,'BDT',0.00000000,1.00000000,100.00000000,NULL,'0','','V49XRZW6NZQ2',0,0,0,NULL,'2025-10-13 12:37:07','2025-10-13 12:37:07'),(8,4,12,902,100.00000000,'BDT',0.00000000,1.00000000,100.00000000,NULL,'0','','V49XRZW6NZQ2',0,0,0,NULL,'2025-10-13 12:53:22','2025-10-13 12:53:22'),(9,4,12,902,100.00000000,'BDT',0.00000000,1.00000000,100.00000000,NULL,'0','','V49XRZW6NZQ2',0,0,0,NULL,'2025-10-13 13:16:45','2025-10-13 13:16:45'),(10,4,12,902,100.00000000,'BDT',0.00000000,1.00000000,100.00000000,NULL,'0','','V49XRZW6NZQ2',0,0,0,NULL,'2025-10-13 13:19:35','2025-10-13 13:19:35'),(11,4,12,902,100.00000000,'BDT',0.00000000,1.00000000,100.00000000,NULL,'0','','V49XRZW6NZQ2',0,0,0,NULL,'2025-10-13 13:25:18','2025-10-13 13:25:18'),(12,4,13,902,100.00000000,'BDT',0.00000000,1.00000000,100.00000000,NULL,'0','','A8OMXVBGWGHY',0,0,0,NULL,'2025-10-13 13:25:44','2025-10-13 13:25:44'),(13,4,13,902,100.00000000,'BDT',0.00000000,1.00000000,100.00000000,NULL,'0','','A8OMXVBGWGHY',0,0,0,NULL,'2025-10-13 13:30:32','2025-10-13 13:30:32'),(14,4,13,902,100.00000000,'BDT',0.00000000,1.00000000,100.00000000,NULL,'0','','A8OMXVBGWGHY',0,0,0,NULL,'2025-10-13 13:30:51','2025-10-13 13:30:51'),(15,4,13,902,100.00000000,'BDT',0.00000000,1.00000000,100.00000000,NULL,'0','','A8OMXVBGWGHY',0,0,0,NULL,'2025-10-13 13:31:15','2025-10-13 13:31:15'),(16,4,13,902,100.00000000,'BDT',0.00000000,1.00000000,100.00000000,NULL,'0','','A8OMXVBGWGHY',0,0,0,NULL,'2025-10-13 13:38:29','2025-10-13 13:38:29'),(17,4,14,902,100.00000000,'BDT',0.00000000,1.00000000,100.00000000,NULL,'0','','5OMMKOPFO8MJ',0,0,0,NULL,'2025-10-13 13:58:45','2025-10-13 13:58:45'),(18,4,14,902,100.00000000,'BDT',0.00000000,1.00000000,100.00000000,NULL,'0','','5OMMKOPFO8MJ',0,0,0,NULL,'2025-10-13 14:00:40','2025-10-13 14:00:40'),(19,4,14,902,100.00000000,'BDT',0.00000000,1.00000000,100.00000000,NULL,'0','','5OMMKOPFO8MJ',0,0,0,NULL,'2025-10-13 14:03:34','2025-10-13 14:03:34'),(20,4,15,999,100.00000000,'BDT',0.00000000,1.00000000,100.00000000,NULL,'0','','WVZKCOVQO2X3',0,0,0,NULL,'2025-10-14 12:05:02','2025-10-14 12:05:02'),(21,4,15,999,100.00000000,'BDT',0.00000000,1.00000000,100.00000000,NULL,'0','','WVZKCOVQO2X3',0,0,0,NULL,'2025-10-14 12:10:07','2025-10-14 12:10:07'),(22,4,15,999,100.00000000,'BDT',0.00000000,1.00000000,100.00000000,NULL,'0','','WVZKCOVQO2X3',0,0,0,NULL,'2025-10-14 12:11:57','2025-10-14 12:11:57'),(23,4,16,999,100.00000000,'BDT',0.00000000,1.00000000,100.00000000,NULL,'0','','JYZGXD9UFRYS',0,0,0,NULL,'2025-10-14 12:12:41','2025-10-14 12:12:41'),(24,4,16,999,100.00000000,'BDT',0.00000000,1.00000000,100.00000000,NULL,'0','','JYZGXD9UFRYS',0,0,0,NULL,'2025-10-14 12:15:01','2025-10-14 12:15:01'),(25,4,17,1001,100.00000000,'BDT',0.00000000,1.00000000,100.00000000,NULL,'0','','YAQ54JD9TE9S',0,0,0,NULL,'2025-10-14 13:24:09','2025-10-14 13:24:09'),(26,4,17,1001,100.00000000,'BDT',0.00000000,1.00000000,100.00000000,NULL,'0','','YAQ54JD9TE9S',0,0,0,NULL,'2025-10-14 13:24:31','2025-10-14 13:24:31'),(27,4,17,1001,100.00000000,'BDT',0.00000000,1.00000000,100.00000000,NULL,'0','','YAQ54JD9TE9S',0,0,0,NULL,'2025-10-14 13:24:41','2025-10-14 13:24:41'),(28,4,17,902,100.00000000,'BDT',0.00000000,1.00000000,100.00000000,NULL,'0','','YAQ54JD9TE9S',0,0,0,NULL,'2025-10-14 13:45:55','2025-10-14 13:45:55'),(29,4,18,902,100.00000000,'BDT',0.00000000,1.00000000,100.00000000,NULL,'0','','T6OXEMQ3ZPKY',0,0,0,NULL,'2025-10-14 14:28:41','2025-10-14 14:28:41'),(30,4,18,902,100.00000000,'BDT',0.00000000,1.00000000,100.00000000,NULL,'0','','T6OXEMQ3ZPKY',0,0,0,NULL,'2025-10-14 14:31:28','2025-10-14 14:31:28'),(31,4,18,902,100.00000000,'BDT',0.00000000,1.00000000,100.00000000,NULL,'0','','T6OXEMQ3ZPKY',0,0,0,NULL,'2025-10-14 14:36:55','2025-10-14 14:36:55'),(32,4,18,902,100.00000000,'BDT',0.00000000,1.00000000,100.00000000,NULL,'0','','T6OXEMQ3ZPKY',0,0,0,NULL,'2025-10-14 14:43:47','2025-10-14 14:43:47'),(33,4,19,902,200.00000000,'BDT',0.00000000,1.00000000,200.00000000,NULL,'0','','ASTMMCU1R6NX',0,0,0,NULL,'2025-10-15 10:46:01','2025-10-15 10:46:01'),(34,4,19,902,200.00000000,'BDT',0.00000000,1.00000000,200.00000000,NULL,'0','','ASTMMCU1R6NX',0,0,0,NULL,'2025-10-15 10:46:26','2025-10-15 10:46:26'),(35,4,19,902,200.00000000,'BDT',0.00000000,1.00000000,200.00000000,NULL,'0','','ASTMMCU1R6NX',0,0,0,NULL,'2025-10-15 11:01:51','2025-10-15 11:01:51'),(36,4,19,902,200.00000000,'BDT',0.00000000,1.00000000,200.00000000,NULL,'0','','ASTMMCU1R6NX',0,0,0,NULL,'2025-10-15 11:09:52','2025-10-15 11:09:52'),(37,4,19,902,200.00000000,'BDT',0.00000000,1.00000000,200.00000000,NULL,'0','','ASTMMCU1R6NX',0,0,0,NULL,'2025-10-15 11:12:33','2025-10-15 11:12:33'),(38,4,19,902,200.00000000,'BDT',0.00000000,1.00000000,200.00000000,NULL,'0','','ASTMMCU1R6NX',0,0,0,NULL,'2025-10-15 11:14:26','2025-10-15 11:14:26'),(39,4,19,902,200.00000000,'BDT',0.00000000,1.00000000,200.00000000,NULL,'0','','ASTMMCU1R6NX',0,0,0,NULL,'2025-10-15 11:17:32','2025-10-15 11:17:32'),(40,4,19,902,200.00000000,'BDT',0.00000000,1.00000000,200.00000000,NULL,'0','','ASTMMCU1R6NX',0,0,0,NULL,'2025-10-15 11:24:41','2025-10-15 11:24:41'),(41,5,20,902,100.00000000,'BDT',0.00000000,1.00000000,100.00000000,NULL,'0','','RVVMU3YOKRUC',0,0,0,NULL,'2025-10-15 11:44:13','2025-10-15 11:44:13'),(42,5,20,902,100.00000000,'BDT',0.00000000,1.00000000,100.00000000,NULL,'0','','RVVMU3YOKRUC',0,0,0,NULL,'2025-10-15 11:44:14','2025-10-15 11:44:14'),(43,6,21,902,100.00000000,'BDT',0.00000000,1.00000000,100.00000000,NULL,'0','','JDQB4PDBJ12F',0,0,0,NULL,'2025-10-15 11:50:03','2025-10-15 11:50:03'),(44,4,19,902,200.00000000,'BDT',0.00000000,1.00000000,200.00000000,NULL,'0','','ASTMMCU1R6NX',0,0,0,NULL,'2025-10-15 11:52:40','2025-10-15 11:52:40'),(45,4,19,902,200.00000000,'BDT',0.00000000,1.00000000,200.00000000,NULL,'0','','ASTMMCU1R6NX',0,0,0,NULL,'2025-10-15 12:30:34','2025-10-15 12:30:34'),(46,5,22,902,100.00000000,'BDT',0.00000000,1.00000000,100.00000000,NULL,'0','','3QBCFNGVVHEZ',0,0,0,NULL,'2025-10-15 12:41:23','2025-10-15 12:41:23'),(47,5,22,902,100.00000000,'BDT',0.00000000,1.00000000,100.00000000,NULL,'0','','3QBCFNGVVHEZ',0,0,0,NULL,'2025-10-15 12:45:18','2025-10-15 12:45:18'),(48,5,22,902,100.00000000,'BDT',0.00000000,1.00000000,100.00000000,NULL,'0','','3QBCFNGVVHEZ',0,0,0,NULL,'2025-10-15 13:44:13','2025-10-15 13:44:13'),(49,6,23,133,100.00000000,'BDT',0.00000000,1.00000000,100.00000000,NULL,'0','','JV74HNT21OF5',0,0,0,NULL,'2025-10-15 22:26:45','2025-10-15 22:26:45'),(50,6,23,133,100.00000000,'BDT',0.00000000,1.00000000,100.00000000,NULL,'0','','JV74HNT21OF5',0,0,0,NULL,'2025-10-15 22:30:45','2025-10-15 22:30:45'),(51,6,23,133,100.00000000,'BDT',0.00000000,1.00000000,100.00000000,NULL,'0','','JV74HNT21OF5',0,0,0,NULL,'2025-10-15 22:31:58','2025-10-15 22:31:58'),(52,6,23,133,100.00000000,'BDT',0.00000000,1.00000000,100.00000000,NULL,'0','','JV74HNT21OF5',0,0,0,NULL,'2025-10-15 22:36:10','2025-10-15 22:36:10'),(53,6,23,906,100.00000000,'BDT',0.00000000,1.00000000,100.00000000,NULL,'0','','JV74HNT21OF5',0,0,0,NULL,'2025-10-15 23:13:35','2025-10-15 23:13:35'),(54,6,23,906,100.00000000,'BDT',0.00000000,1.00000000,100.00000000,NULL,'0','','JV74HNT21OF5',0,0,0,NULL,'2025-10-15 23:19:21','2025-10-15 23:19:21'),(55,6,23,906,100.00000000,'BDT',0.00000000,1.00000000,100.00000000,NULL,'0','','JV74HNT21OF5',0,0,0,NULL,'2025-10-15 23:20:26','2025-10-15 23:20:26'),(56,6,24,906,100.00000000,'BDT',0.00000000,1.00000000,100.00000000,NULL,'0','','PAB156CC2Y5E',0,0,0,NULL,'2025-10-15 23:20:56','2025-10-15 23:20:56'),(57,6,25,906,100.00000000,'BDT',0.00000000,1.00000000,100.00000000,NULL,'0','','S2K7XZUFYGOO',0,0,0,NULL,'2025-10-15 23:26:42','2025-10-15 23:26:42'),(58,6,26,906,100.00000000,'BDT',0.00000000,1.00000000,100.00000000,NULL,'0','','81JKTSQZVSSA',0,0,0,NULL,'2025-10-15 23:30:47','2025-10-15 23:30:47'),(59,6,26,906,100.00000000,'BDT',0.00000000,1.00000000,100.00000000,NULL,'0','','81JKTSQZVSSA',0,0,0,NULL,'2025-10-15 23:34:14','2025-10-15 23:34:14'),(60,6,27,906,100.00000000,'BDT',0.00000000,1.00000000,100.00000000,NULL,'0','','R24J8SM7UT46',0,0,0,NULL,'2025-10-15 23:34:58','2025-10-15 23:34:58'),(61,6,27,906,100.00000000,'BDT',0.00000000,1.00000000,100.00000000,NULL,'0','','R24J8SM7UT46',0,0,0,NULL,'2025-10-15 23:35:38','2025-10-15 23:35:38'),(62,6,27,906,100.00000000,'BDT',0.00000000,1.00000000,100.00000000,NULL,'0','','R24J8SM7UT46',0,0,0,NULL,'2025-10-15 23:43:03','2025-10-15 23:43:03'),(63,6,28,906,100.00000000,'BDT',0.00000000,1.00000000,100.00000000,NULL,'0','','Q1NYM36BMVPY',0,0,0,NULL,'2025-10-15 23:50:21','2025-10-15 23:50:21'),(64,6,29,906,100.00000000,'BDT',0.00000000,1.00000000,100.00000000,NULL,'0','','MSV9Y4RXW3VX',0,0,0,NULL,'2025-10-15 23:59:47','2025-10-15 23:59:47'),(65,6,29,906,100.00000000,'BDT',0.00000000,1.00000000,100.00000000,NULL,'0','','MSV9Y4RXW3VX',0,0,0,NULL,'2025-10-16 00:01:02','2025-10-16 00:01:02'),(66,6,29,906,100.00000000,'BDT',0.00000000,1.00000000,100.00000000,NULL,'0','','MSV9Y4RXW3VX',0,0,0,NULL,'2025-10-16 00:07:34','2025-10-16 00:07:34'),(67,6,29,906,100.00000000,'BDT',0.00000000,1.00000000,100.00000000,NULL,'0','','MSV9Y4RXW3VX',0,0,0,NULL,'2025-10-16 00:07:55','2025-10-16 00:07:55'),(68,6,29,906,100.00000000,'BDT',0.00000000,1.00000000,100.00000000,NULL,'0','','MSV9Y4RXW3VX',0,0,0,NULL,'2025-10-16 00:09:28','2025-10-16 00:09:28'),(69,6,30,906,100.00000000,'BDT',0.00000000,1.00000000,100.00000000,NULL,'0','','HKHHEMPRT63V',0,0,0,NULL,'2025-10-16 00:09:53','2025-10-16 00:09:53'),(70,6,30,906,100.00000000,'BDT',0.00000000,1.00000000,100.00000000,NULL,'0','','HKHHEMPRT63V',0,0,0,NULL,'2025-10-16 00:10:12','2025-10-16 00:10:12'),(71,6,31,906,100.00000000,'BDT',0.00000000,1.00000000,100.00000000,NULL,'0','','41HKZ25V8VXJ',0,0,0,NULL,'2025-10-16 00:10:50','2025-10-16 00:10:50'),(72,6,31,906,100.00000000,'BDT',0.00000000,1.00000000,100.00000000,NULL,'0','','41HKZ25V8VXJ',0,0,0,NULL,'2025-10-16 00:11:48','2025-10-16 00:11:48'),(73,6,31,906,100.00000000,'BDT',0.00000000,1.00000000,100.00000000,NULL,'0','','41HKZ25V8VXJ',0,0,0,NULL,'2025-10-16 00:15:19','2025-10-16 00:15:19'),(74,6,31,907,100.00000000,'USD',0.00000000,1.00000000,100.00000000,NULL,'0','','41HKZ25V8VXJ',0,0,0,NULL,'2025-10-16 00:40:22','2025-10-16 00:40:22'),(75,6,31,907,100.00000000,'USD',0.00000000,1.00000000,100.00000000,NULL,'0','','41HKZ25V8VXJ',0,0,0,NULL,'2025-10-16 00:43:52','2025-10-16 00:43:52'),(76,6,32,907,100.00000000,'BDT',0.00000000,1.00000000,100.00000000,NULL,'0','','TABMWD36UXF1',0,0,0,NULL,'2025-10-16 00:46:09','2025-10-16 00:46:09'),(77,6,32,907,100.00000000,'BDT',0.00000000,1.00000000,100.00000000,NULL,'0','','TABMWD36UXF1',0,0,0,NULL,'2025-10-16 00:56:18','2025-10-16 00:56:18'),(78,6,32,907,100.00000000,'BDT',0.00000000,1.00000000,100.00000000,NULL,'0','','TABMWD36UXF1',0,0,0,NULL,'2025-10-16 01:02:43','2025-10-16 01:02:43'),(79,6,32,907,100.00000000,'BDT',0.00000000,1.00000000,100.00000000,NULL,'0','','TABMWD36UXF1',0,0,0,NULL,'2025-10-16 01:05:33','2025-10-16 01:05:33'),(80,6,33,907,150.00000000,'BDT',0.00000000,1.00000000,150.00000000,NULL,'0','','814FB5BFPDEO',0,0,0,NULL,'2025-10-16 07:54:47','2025-10-16 07:54:47'),(81,6,34,902,250.00000000,'BDT',0.00000000,1.00000000,250.00000000,NULL,'0','','T9Q97JQWADKR',0,0,0,NULL,'2025-10-16 07:55:17','2025-10-16 07:55:17'),(82,6,35,907,450.00000000,'BDT',0.00000000,1.00000000,450.00000000,NULL,'0','','UF1EMZDEC7WU',0,0,0,NULL,'2025-10-16 11:36:07','2025-10-16 11:36:07');
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
) ENGINE=InnoDB AUTO_INCREMENT=65 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `districts`
--

LOCK TABLES `districts` WRITE;
/*!40000 ALTER TABLE `districts` DISABLE KEYS */;
INSERT INTO `districts` VALUES (1,1,'Barguna','αª¼αª░αªùαºüαª¿αª╛',1,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(2,1,'Barisal','αª¼αª░αª┐αª╢αª╛αª▓',2,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(3,1,'Bhola','αª¡αºïαª▓αª╛',3,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(4,1,'Jhalokati','αª¥αª╛αª▓αªòαª╛αªáαª┐',4,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(5,1,'Patuakhali','αª¬αªƒαºüαª»αª╝αª╛αªûαª╛αª▓αºÇ',5,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(6,1,'Pirojpur','αª¬αª┐αª░αºïαª£αª¬αºüαª░',6,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(7,2,'Bandarban','αª¼αª╛αª¿αºìαªªαª░αª¼αª╛αª¿',7,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(8,2,'Brahmanbaria','αª¼αºìαª░αª╛αª╣αºìαª«αªúαª¼αª╛αªíαª╝αª┐αª»αª╝αª╛',8,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(9,2,'Chandpur','αªÜαª╛αªüαªªαª¬αºüαª░',9,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(10,2,'Chittagong','αªÜαªƒαºìαªƒαªùαºìαª░αª╛αª«',10,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(11,2,'Comilla','αªòαºüαª«αª┐αª▓αºìαª▓αª╛',11,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(12,2,'Cox\'s Bazar','αªòαªòαºìαª╕αª¼αª╛αª£αª╛αª░',12,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(13,2,'Feni','αª½αºçαª¿αºÇ',13,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(14,2,'Khagrachhari','αªûαª╛αªùαªíαª╝αª╛αª¢αªíαª╝αª┐',14,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(15,2,'Lakshmipur','αª▓αªòαºìαª╖αºìαª«αºÇαª¬αºüαª░',15,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(16,2,'Noakhali','αª¿αºïαª»αª╝αª╛αªûαª╛αª▓αºÇ',16,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(17,2,'Rangamati','αª░αª╛αªÖαºìαªùαª╛αª«αª╛αªƒαª┐',17,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(18,3,'Dhaka','αªóαª╛αªòαª╛',18,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(19,3,'Faridpur','αª½αª░αª┐αªªαª¬αºüαª░',19,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(20,3,'Gazipur','αªùαª╛αª£αºÇαª¬αºüαª░',20,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(21,3,'Gopalganj','αªùαºïαª¬αª╛αª▓αªùαª₧αºìαª£',21,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(22,3,'Kishoreganj','αªòαª┐αª╢αºïαª░αªùαª₧αºìαª£',22,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(23,3,'Madaripur','αª«αª╛αªªαª╛αª░αºÇαª¬αºüαª░',23,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(24,3,'Manikganj','αª«αª╛αª¿αª┐αªòαªùαª₧αºìαª£',24,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(25,3,'Munshiganj','αª«αºüαª¿αºìαª╕αª┐αªùαª₧αºìαª£',25,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(26,3,'Narayanganj','αª¿αª╛αª░αª╛αª»αª╝αªúαªùαª₧αºìαª£',26,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(27,3,'Narsingdi','αª¿αª░αª╕αª┐αªéαªªαºÇ',27,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(28,3,'Rajbari','αª░αª╛αª£αª¼αª╛αªíαª╝αºÇ',28,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(29,3,'Shariatpur','αª╢αª░αºÇαª»αª╝αªñαª¬αºüαª░',29,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(30,3,'Tangail','αªƒαª╛αªÖαºìαªùαª╛αªçαª▓',30,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(31,4,'Bagerhat','αª¼αª╛αªùαºçαª░αª╣αª╛αªƒ',31,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(32,4,'Chuadanga','αªÜαºüαª»αª╝αª╛αªíαª╛αªÖαºìαªùαª╛',32,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(33,4,'Jessore','αª»αª╢αºïαª░',33,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(34,4,'Jhenaidah','αª¥αª┐αª¿αª╛αªçαªªαª╣',34,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(35,4,'Khulna','αªûαºüαª▓αª¿αª╛',35,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(36,4,'Kushtia','αªòαºüαª╖αºìαªƒαª┐αª»αª╝αª╛',36,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(37,4,'Magura','αª«αª╛αªùαºüαª░αª╛',37,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(38,4,'Meherpur','αª«αºçαª╣αºçαª░αª¬αºüαª░',38,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(39,4,'Narail','αª¿αªíαª╝αª╛αªçαª▓',39,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(40,4,'Satkhira','αª╕αª╛αªñαªòαºìαª╖αºÇαª░αª╛',40,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(41,5,'Jamalpur','αª£αª╛αª«αª╛αª▓αª¬αºüαª░',41,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(42,5,'Mymensingh','αª«αª»αª╝αª«αª¿αª╕αª┐αªéαª╣',42,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(43,5,'Netrokona','αª¿αºçαªñαºìαª░αªòαºïαªúαª╛',43,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(44,5,'Sherpur','αª╢αºçαª░αª¬αºüαª░',44,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(45,6,'Bogra','αª¼αªùαºüαªíαª╝αª╛',45,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(46,6,'Joypurhat','αª£αª»αª╝αª¬αºüαª░αª╣αª╛αªƒ',46,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(47,6,'Naogaon','αª¿αªôαªùαª╛αªü',47,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(48,6,'Natore','αª¿αª╛αªƒαºïαª░',48,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(49,6,'Chapainawabganj','αªÜαª╛αªüαª¬αª╛αªçαª¿αª¼αª╛αª¼αªùαª₧αºìαª£',49,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(50,6,'Pabna','αª¬αª╛αª¼αª¿αª╛',50,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(51,6,'Rajshahi','αª░αª╛αª£αª╢αª╛αª╣αºÇ',51,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(52,6,'Sirajganj','αª╕αª┐αª░αª╛αª£αªùαª₧αºìαª£',52,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(53,7,'Dinajpur','αªªαª┐αª¿αª╛αª£αª¬αºüαª░',53,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(54,7,'Gaibandha','αªùαª╛αªçαª¼αª╛αª¿αºìαªºαª╛',54,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(55,7,'Kurigram','αªòαºüαªíαª╝αª┐αªùαºìαª░αª╛αª«',55,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(56,7,'Lalmonirhat','αª▓αª╛αª▓αª«αª¿αª┐αª░αª╣αª╛αªƒ',56,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(57,7,'Nilphamari','αª¿αºÇαª▓αª½αª╛αª«αª╛αª░αºÇ',57,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(58,7,'Panchagarh','αª¬αª₧αºìαªÜαªùαªíαª╝',58,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(59,7,'Rangpur','αª░αªéαª¬αºüαª░',59,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(60,7,'Thakurgaon','αªáαª╛αªòαºüαª░αªùαª╛αªüαªô',60,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(61,8,'Habiganj','αª╣αª¼αª┐αªùαª₧αºìαª£',61,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(62,8,'Moulvibazar','αª«αºîαª▓αª¡αºÇαª¼αª╛αª£αª╛αª░',62,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(63,8,'Sunamganj','αª╕αºüαª¿αª╛αª«αªùαª₧αºìαª£',63,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(64,8,'Sylhet','αª╕αª┐αª▓αºçαªƒ',64,1,'2026-02-27 05:34:35','2026-02-27 05:34:35');
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
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `divisions`
--

LOCK TABLES `divisions` WRITE;
/*!40000 ALTER TABLE `divisions` DISABLE KEYS */;
INSERT INTO `divisions` VALUES (1,'Barishal','αª¼αª░αª┐αª╢αª╛αª▓',1,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(2,'Chattogram','αªÜαªƒαºìαªƒαªùαºìαª░αª╛αª«',2,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(3,'Dhaka','αªóαª╛αªòαª╛',3,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(4,'Khulna','αªûαºüαª▓αª¿αª╛',4,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(5,'Mymensingh','αª«αª»αª╝αª«αª¿αª╕αª┐αªéαª╣',5,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(6,'Rajshahi','αª░αª╛αª£αª╢αª╛αª╣αºÇ',6,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(7,'Rangpur','αª░αªéαª¬αºüαª░',7,1,'2026-02-27 05:34:35','2026-02-27 05:34:35'),(8,'Sylhet','αª╕αª┐αª▓αºçαªƒ',8,1,'2026-02-27 05:34:35','2026-02-27 05:34:35');
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
) ENGINE=InnoDB AUTO_INCREMENT=122 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `frontends`
--

LOCK TABLES `frontends` WRITE;
/*!40000 ALTER TABLE `frontends` DISABLE KEYS */;
INSERT INTO `frontends` VALUES (1,'seo.data','{\"seo_image\":\"1\",\"keywords\":[\"dealshop\",\"e-commerce\",\"online shopping platfrom\",\"product\",\"discount\"],\"description\":\"Discover a world of endless possibilities at our eCommerce store. Shop the latest trends, find exclusive deals, and indulge in a seamless shopping experience. Explore a wide range of products, from fashion and electronics to home decor and more. With fast shipping and secure transactions, we\'re here to make your online shopping dreams come true. Start browsing now and unlock a world of convenience at our DealShop.\",\"social_title\":\"DealShop - Online E-commerce Shopping Platform\",\"social_description\":\"Discover a world of endless possibilities at our eCommerce store. Shop the latest trends, find exclusive deals, and indulge in a seamless shopping experience. Explore a wide range of products, from fashion and electronics to home decor and more. With fast shipping and secure transactions, we\'re here to make your online shopping dreams come true. Start browsing now and unlock a world of convenience at our DealShop.\",\"image\":\"64759c7b3443b1685429371.png\"}','2020-07-04 23:42:52','2023-05-30 05:39:52'),(27,'contact_us.content','{\"has_image\": \"1\", \"title\": \"Get in Touch Us\", \"subtitle\": \"Lorem ipsum dolor sit amet consectetur adipisicing elit. Sit eveniet soluta, nihil est\", \"contact_number\": \"01307644289\", \"contact_email\": \"contact@dealshop.com\", \"address\": \"4901 Seminary Rd #120,Alexandria,Vermont USA\", \"whatsapp_number\": \"\", \"telegram_username\": \"\", \"image\": \"63fb2824d83781677404196.png\"}','2020-10-28 00:59:19','2025-10-16 02:47:27'),(33,'feature.content','{\"heading\":\"asdf\",\"sub_heading\":\"asdf\"}','2021-01-03 23:40:54','2021-01-03 23:40:55'),(34,'feature.element','{\"title\":\"asdf\",\"description\":\"asdf\",\"feature_icon\":\"asdf\"}','2021-01-03 23:41:02','2021-01-03 23:41:02'),(36,'service.content','{\"trx_type\":\"deposit\",\"heading\":\"asdf fffff\",\"subheading\":\"555\"}','2021-03-06 01:27:34','2022-03-30 08:07:06'),(39,'banner.content','{\"heading\": \"Latest News\", \"sub_heading\": \"Lorem ipsum dolor sit, amet consectetur adipisicing elit. Esse voluptatum eaque earum quos quia? Id aspernatur ratione, voluptas nulla rerum laudantium neque ipsam eaque\", \"slide_interval_seconds\": \"5\", \"autoplay\": \"1\", \"banner_width\": \"2560\", \"banner_height\": \"400\"}','2021-05-02 06:09:30','2025-01-30 00:00:00'),(41,'cookie.data','{\"short_desc\":\"We may use cookies or any other tracking technologies when you visit our website, including any other media form, mobile website, or mobile application related or connected to help customize the Site and improve your experience.\",\"description\":\"<div class=\\\"mb-5\\\" style=\\\"color: rgb(111, 111, 111); font-family: Nunito, sans-serif; margin-bottom: 3rem !important;\\\" bis_skin_checked=\\\"1\\\"><h3 class=\\\"mb-3\\\" style=\\\"font-weight: 600; line-height: 1.3; font-size: 24px; font-family: Exo, sans-serif; color: rgb(54, 54, 54);\\\">What information do we collect?<\\/h3><p class=\\\"font-18\\\" style=\\\"margin-right: 0px; margin-left: 0px; font-size: 18px !important;\\\">We gather data from you when you register on our site, submit a request, buy any services, react to an overview, or round out a structure. At the point when requesting any assistance or enrolling on our site, as suitable, you might be approached to enter your: name, email address, or telephone number. You may, nonetheless, visit our site anonymously.<\\/p><\\/div><div class=\\\"mb-5\\\" style=\\\"color: rgb(111, 111, 111); font-family: Nunito, sans-serif; margin-bottom: 3rem !important;\\\" bis_skin_checked=\\\"1\\\"><h3 class=\\\"mb-3\\\" style=\\\"font-weight: 600; line-height: 1.3; font-size: 24px; font-family: Exo, sans-serif; color: rgb(54, 54, 54);\\\">How do we protect your information?<\\/h3><p class=\\\"font-18\\\" style=\\\"margin-right: 0px; margin-left: 0px; font-size: 18px !important;\\\">All provided delicate\\/credit data is sent through Stripe.<br>After an exchange, your private data (credit cards, social security numbers, financials, and so on) won\'t be put away on our workers.<\\/p><\\/div><div class=\\\"mb-5\\\" style=\\\"color: rgb(111, 111, 111); font-family: Nunito, sans-serif; margin-bottom: 3rem !important;\\\" bis_skin_checked=\\\"1\\\"><h3 class=\\\"mb-3\\\" style=\\\"font-weight: 600; line-height: 1.3; font-size: 24px; font-family: Exo, sans-serif; color: rgb(54, 54, 54);\\\">Do we disclose any information to outside parties?<\\/h3><p class=\\\"font-18\\\" style=\\\"margin-right: 0px; margin-left: 0px; font-size: 18px !important;\\\">We don\'t sell, exchange, or in any case move to outside gatherings by and by recognizable data. This does exclude confided in outsiders who help us in working our site, leading our business, or adjusting you, since those gatherings consent to keep this data private. We may likewise deliver your data when we accept discharge is suitable to follow the law, implement our site strategies, or ensure our own or others\' rights, property, or wellbeing.<\\/p><\\/div><div class=\\\"mb-5\\\" style=\\\"color: rgb(111, 111, 111); font-family: Nunito, sans-serif; margin-bottom: 3rem !important;\\\" bis_skin_checked=\\\"1\\\"><h3 class=\\\"mb-3\\\" style=\\\"font-weight: 600; line-height: 1.3; font-size: 24px; font-family: Exo, sans-serif; color: rgb(54, 54, 54);\\\">Children\'s Online Privacy Protection Act Compliance<\\/h3><p class=\\\"font-18\\\" style=\\\"margin-right: 0px; margin-left: 0px; font-size: 18px !important;\\\">We are consistent with the prerequisites of COPPA (Children\'s Online Privacy Protection Act), we don\'t gather any data from anybody under 13 years old. Our site, items, and administrations are completely coordinated to individuals who are in any event 13 years of age or more established.<\\/p><\\/div><div class=\\\"mb-5\\\" style=\\\"color: rgb(111, 111, 111); font-family: Nunito, sans-serif; margin-bottom: 3rem !important;\\\" bis_skin_checked=\\\"1\\\"><h3 class=\\\"mb-3\\\" style=\\\"font-weight: 600; line-height: 1.3; font-size: 24px; font-family: Exo, sans-serif; color: rgb(54, 54, 54);\\\">Changes to our Privacy Policy<\\/h3><p class=\\\"font-18\\\" style=\\\"margin-right: 0px; margin-left: 0px; font-size: 18px !important;\\\">If we decide to change our privacy policy, we will post those changes on this page.<\\/p><\\/div><div class=\\\"mb-5\\\" style=\\\"color: rgb(111, 111, 111); font-family: Nunito, sans-serif; margin-bottom: 3rem !important;\\\" bis_skin_checked=\\\"1\\\"><h3 class=\\\"mb-3\\\" style=\\\"font-weight: 600; line-height: 1.3; font-size: 24px; font-family: Exo, sans-serif; color: rgb(54, 54, 54);\\\">How long we retain your information?<\\/h3><p class=\\\"font-18\\\" style=\\\"margin-right: 0px; margin-left: 0px; font-size: 18px !important;\\\">At the point when you register for our site, we cycle and keep your information we have about you however long you don\'t erase the record or withdraw yourself (subject to laws and guidelines).<\\/p><\\/div><div class=\\\"mb-5\\\" style=\\\"color: rgb(111, 111, 111); font-family: Nunito, sans-serif; margin-bottom: 3rem !important;\\\" bis_skin_checked=\\\"1\\\"><h3 class=\\\"mb-3\\\" style=\\\"font-weight: 600; line-height: 1.3; font-size: 24px; font-family: Exo, sans-serif; color: rgb(54, 54, 54);\\\">What we don\\u2019t do with your data<\\/h3><p class=\\\"font-18\\\" style=\\\"margin-right: 0px; margin-left: 0px; font-size: 18px !important;\\\">We don\'t and will never share, unveil, sell, or in any case give your information to different organizations for the promoting of their items or administrations.<\\/p><\\/div>\",\"status\":1}','2020-07-04 23:42:52','2025-10-10 11:13:47'),(42,'policy_pages.element','{\"title\":\"Privacy Policy\",\"details\":\"<div class=\\\"mb-5\\\" style=\\\"color:rgb(111,111,111);font-family:Nunito, sans-serif;margin-bottom:3rem;\\\"><h3 class=\\\"mb-3\\\" style=\\\"font-weight:600;line-height:1.3;font-size:24px;font-family:Exo, sans-serif;color:rgb(54,54,54);\\\">What information do we collect?<\\/h3><p class=\\\"font-18\\\" style=\\\"margin-right:0px;margin-left:0px;font-size:18px;\\\">We gather data from you when you register on our site, submit a request, buy any services, react to an overview, or round out a structure. At the point when requesting any assistance or enrolling on our site, as suitable, you might be approached to enter your: name, email address, or telephone number. You may, nonetheless, visit our site anonymously.<\\/p><\\/div><div class=\\\"mb-5\\\" style=\\\"color:rgb(111,111,111);font-family:Nunito, sans-serif;margin-bottom:3rem;\\\"><h3 class=\\\"mb-3\\\" style=\\\"font-weight:600;line-height:1.3;font-size:24px;font-family:Exo, sans-serif;color:rgb(54,54,54);\\\">How do we protect your information?<\\/h3><p class=\\\"font-18\\\" style=\\\"margin-right:0px;margin-left:0px;font-size:18px;\\\">All provided delicate\\/credit data is sent through Stripe.<br \\/>After an exchange, your private data (credit cards, social security numbers, financials, and so on) won\'t be put away on our workers.<\\/p><\\/div><div class=\\\"mb-5\\\" style=\\\"color:rgb(111,111,111);font-family:Nunito, sans-serif;margin-bottom:3rem;\\\"><h3 class=\\\"mb-3\\\" style=\\\"font-weight:600;line-height:1.3;font-size:24px;font-family:Exo, sans-serif;color:rgb(54,54,54);\\\">Do we disclose any information to outside parties?<\\/h3><p class=\\\"font-18\\\" style=\\\"margin-right:0px;margin-left:0px;font-size:18px;\\\">We don\'t sell, exchange, or in any case move to outside gatherings by and by recognizable data. This does exclude confided in outsiders who help us in working our site, leading our business, or adjusting you, since those gatherings consent to keep this data private. We may likewise deliver your data when we accept discharge is suitable to follow the law, implement our site strategies, or ensure our own or others\' rights, property, or wellbeing.<\\/p><\\/div><div class=\\\"mb-5\\\" style=\\\"color:rgb(111,111,111);font-family:Nunito, sans-serif;margin-bottom:3rem;\\\"><h3 class=\\\"mb-3\\\" style=\\\"font-weight:600;line-height:1.3;font-size:24px;font-family:Exo, sans-serif;color:rgb(54,54,54);\\\">Children\'s Online Privacy Protection Act Compliance<\\/h3><p class=\\\"font-18\\\" style=\\\"margin-right:0px;margin-left:0px;font-size:18px;\\\">We are consistent with the prerequisites of COPPA (Children\'s Online Privacy Protection Act), we don\'t gather any data from anybody under 13 years old. Our site, items, and administrations are completely coordinated to individuals who are in any event 13 years of age or more established.<\\/p><\\/div><div class=\\\"mb-5\\\" style=\\\"color:rgb(111,111,111);font-family:Nunito, sans-serif;margin-bottom:3rem;\\\"><h3 class=\\\"mb-3\\\" style=\\\"font-weight:600;line-height:1.3;font-size:24px;font-family:Exo, sans-serif;color:rgb(54,54,54);\\\">Changes to our Privacy Policy<\\/h3><p class=\\\"font-18\\\" style=\\\"margin-right:0px;margin-left:0px;font-size:18px;\\\">If we decide to change our privacy policy, we will post those changes on this page.<\\/p><\\/div><div class=\\\"mb-5\\\" style=\\\"color:rgb(111,111,111);font-family:Nunito, sans-serif;margin-bottom:3rem;\\\"><h3 class=\\\"mb-3\\\" style=\\\"font-weight:600;line-height:1.3;font-size:24px;font-family:Exo, sans-serif;color:rgb(54,54,54);\\\">How long we retain your information?<\\/h3><p class=\\\"font-18\\\" style=\\\"margin-right:0px;margin-left:0px;font-size:18px;\\\">At the point when you register for our site, we cycle and keep your information we have about you however long you don\'t erase the record or withdraw yourself (subject to laws and guidelines).<\\/p><\\/div><div class=\\\"mb-5\\\" style=\\\"color:rgb(111,111,111);font-family:Nunito, sans-serif;margin-bottom:3rem;\\\"><h3 class=\\\"mb-3\\\" style=\\\"font-weight:600;line-height:1.3;font-size:24px;font-family:Exo, sans-serif;color:rgb(54,54,54);\\\">What we don\\u2019t do with your data<\\/h3><p class=\\\"font-18\\\" style=\\\"margin-right:0px;margin-left:0px;font-size:18px;\\\">We don\'t and will never share, unveil, sell, or in any case give your information to different organizations for the promoting of their items or administrations.<\\/p><\\/div>\"}','2021-06-09 08:50:42','2021-06-09 08:50:42'),(43,'policy_pages.element','{\"title\":\"Terms of Service\",\"details\":\"<div class=\\\"mb-5\\\" style=\\\"color:rgb(111,111,111);font-family:Nunito, sans-serif;margin-bottom:3rem;\\\"><p class=\\\"font-18\\\" style=\\\"margin-right:0px;margin-left:0px;font-size:18px;\\\">We claim all authority to dismiss, end, or handicap any help with or without cause per administrator discretion. This is a Complete independent facilitating, on the off chance that you misuse our ticket or Livechat or emotionally supportive network by submitting solicitations or protests we will impair your record. The solitary time you should reach us about the seaward facilitating is if there is an issue with the worker. We have not many substance limitations and everything is as per laws and guidelines. Try not to join on the off chance that you intend to do anything contrary to the guidelines, we do check these things and we will know, don\'t burn through our own and your time by joining on the off chance that you figure you will have the option to sneak by us and break the terms.<\\/p><\\/div><div class=\\\"mb-5\\\" style=\\\"color:rgb(111,111,111);font-family:Nunito, sans-serif;margin-bottom:3rem;\\\"><ul class=\\\"font-18\\\" style=\\\"padding-left:15px;list-style-type:disc;font-size:18px;\\\"><li style=\\\"margin-top:0px;margin-right:0px;margin-left:0px;\\\">Configuration requests - If you have a fully managed dedicated server with us then we offer custom PHP\\/MySQL configurations, firewalls for dedicated IPs, DNS, and httpd configurations.<\\/li><li style=\\\"margin-top:0px;margin-right:0px;margin-left:0px;\\\">Software requests - Cpanel Extension Installation will be granted as long as it does not interfere with the security, stability, and performance of other users on the server.<\\/li><li style=\\\"margin-top:0px;margin-right:0px;margin-left:0px;\\\">Emergency Support - We do not provide emergency support \\/ Phone Support \\/ LiveChat Support. Support may take some hours sometimes.<\\/li><li style=\\\"margin-top:0px;margin-right:0px;margin-left:0px;\\\">Webmaster help - We do not offer any support for webmaster related issues and difficulty including coding, &amp; installs, Error solving. if there is an issue where a library or configuration of the server then we can help you if it\'s possible from our end.<\\/li><li style=\\\"margin-top:0px;margin-right:0px;margin-left:0px;\\\">Backups - We keep backups but we are not responsible for data loss, you are fully responsible for all backups.<\\/li><li style=\\\"margin-top:0px;margin-right:0px;margin-left:0px;\\\">We Don\'t support any child porn or such material.<\\/li><li style=\\\"margin-top:0px;margin-right:0px;margin-left:0px;\\\">No spam-related sites or material, such as email lists, mass mail programs, and scripts, etc.<\\/li><li style=\\\"margin-top:0px;margin-right:0px;margin-left:0px;\\\">No harassing material that may cause people to retaliate against you.<\\/li><li style=\\\"margin-top:0px;margin-right:0px;margin-left:0px;\\\">No phishing pages.<\\/li><li style=\\\"margin-top:0px;margin-right:0px;margin-left:0px;\\\">You may not run any exploitation script from the server. reason can be terminated immediately.<\\/li><li style=\\\"margin-top:0px;margin-right:0px;margin-left:0px;\\\">If Anyone attempting to hack or exploit the server by using your script or hosting, we will terminate your account to keep safe other users.<\\/li><li style=\\\"margin-top:0px;margin-right:0px;margin-left:0px;\\\">Malicious Botnets are strictly forbidden.<\\/li><li style=\\\"margin-top:0px;margin-right:0px;margin-left:0px;\\\">Spam, mass mailing, or email marketing in any way are strictly forbidden here.<\\/li><li style=\\\"margin-top:0px;margin-right:0px;margin-left:0px;\\\">Malicious hacking materials, trojans, viruses, &amp; malicious bots running or for download are forbidden.<\\/li><li style=\\\"margin-top:0px;margin-right:0px;margin-left:0px;\\\">Resource and cronjob abuse is forbidden and will result in suspension or termination.<\\/li><li style=\\\"margin-top:0px;margin-right:0px;margin-left:0px;\\\">Php\\/CGI proxies are strictly forbidden.<\\/li><li style=\\\"margin-top:0px;margin-right:0px;margin-left:0px;\\\">CGI-IRC is strictly forbidden.<\\/li><li style=\\\"margin-top:0px;margin-right:0px;margin-left:0px;\\\">No fake or disposal mailers, mass mailing, mail bombers, SMS bombers, etc.<\\/li><li style=\\\"margin-top:0px;margin-right:0px;margin-left:0px;\\\">NO CREDIT OR REFUND will be granted for interruptions of service, due to User Agreement violations.<\\/li><\\/ul><\\/div><div class=\\\"mb-5\\\" style=\\\"color:rgb(111,111,111);font-family:Nunito, sans-serif;margin-bottom:3rem;\\\"><h3 class=\\\"mb-3\\\" style=\\\"font-weight:600;line-height:1.3;font-size:24px;font-family:Exo, sans-serif;color:rgb(54,54,54);\\\">Terms &amp; Conditions for Users<\\/h3><p class=\\\"font-18\\\" style=\\\"margin-right:0px;margin-left:0px;font-size:18px;\\\">Before getting to this site, you are consenting to be limited by these site Terms and Conditions of Use, every single appropriate law, and guidelines, and concur that you are answerable for consistency with any material neighborhood laws. If you disagree with any of these terms, you are restricted from utilizing or getting to this site.<\\/p><\\/div><div class=\\\"mb-5\\\" style=\\\"color:rgb(111,111,111);font-family:Nunito, sans-serif;margin-bottom:3rem;\\\"><h3 class=\\\"mb-3\\\" style=\\\"font-weight:600;line-height:1.3;font-size:24px;font-family:Exo, sans-serif;color:rgb(54,54,54);\\\">Support<\\/h3><p class=\\\"font-18\\\" style=\\\"margin-right:0px;margin-left:0px;font-size:18px;\\\">Whenever you have downloaded our item, you may get in touch with us for help through email and we will give a valiant effort to determine your issue. We will attempt to answer using the Email for more modest bug fixes, after which we will refresh the center bundle. Content help is offered to confirmed clients by Tickets as it were. Backing demands made by email and Livechat.<\\/p><p class=\\\"my-3 font-18 font-weight-bold\\\" style=\\\"margin-right:0px;margin-left:0px;font-size:18px;\\\">On the off chance that your help requires extra adjustment of the System, at that point, you have two alternatives:<\\/p><ul class=\\\"font-18\\\" style=\\\"padding-left:15px;list-style-type:disc;font-size:18px;\\\"><li style=\\\"margin-top:0px;margin-right:0px;margin-left:0px;\\\">Hang tight for additional update discharge.<\\/li><li style=\\\"margin-top:0px;margin-right:0px;margin-left:0px;\\\">Or on the other hand, enlist a specialist (We offer customization for extra charges).<\\/li><\\/ul><\\/div><div class=\\\"mb-5\\\" style=\\\"color:rgb(111,111,111);font-family:Nunito, sans-serif;margin-bottom:3rem;\\\"><h3 class=\\\"mb-3\\\" style=\\\"font-weight:600;line-height:1.3;font-size:24px;font-family:Exo, sans-serif;color:rgb(54,54,54);\\\">Ownership<\\/h3><p class=\\\"font-18\\\" style=\\\"margin-right:0px;margin-left:0px;font-size:18px;\\\">You may not guarantee scholarly or selective possession of any of our items, altered or unmodified. All items are property, we created them. Our items are given \\\"with no guarantees\\\" without guarantee of any sort, either communicated or suggested. On no occasion will our juridical individual be subject to any harms including, however not restricted to, immediate, roundabout, extraordinary, accidental, or significant harms or different misfortunes emerging out of the utilization of or powerlessness to utilize our items.<\\/p><\\/div><div class=\\\"mb-5\\\" style=\\\"color:rgb(111,111,111);font-family:Nunito, sans-serif;margin-bottom:3rem;\\\"><h3 class=\\\"mb-3\\\" style=\\\"font-weight:600;line-height:1.3;font-size:24px;font-family:Exo, sans-serif;color:rgb(54,54,54);\\\">Warranty<\\/h3><p class=\\\"font-18\\\" style=\\\"margin-right:0px;margin-left:0px;font-size:18px;\\\">We don\'t offer any guarantee or assurance of these Services in any way. When our Services have been modified we can\'t ensure they will work with all outsider plugins, modules, or internet browsers. Program similarity ought to be tried against the show formats on the demo worker. If you don\'t mind guarantee that the programs you use will work with the component, as we can not ensure that our systems will work with all program mixes.<\\/p><\\/div><div class=\\\"mb-5\\\" style=\\\"color:rgb(111,111,111);font-family:Nunito, sans-serif;margin-bottom:3rem;\\\"><h3 class=\\\"mb-3\\\" style=\\\"font-weight:600;line-height:1.3;font-size:24px;font-family:Exo, sans-serif;color:rgb(54,54,54);\\\">Unauthorized\\/Illegal Usage<\\/h3><p class=\\\"font-18\\\" style=\\\"margin-right:0px;margin-left:0px;font-size:18px;\\\">You may not utilize our things for any illicit or unapproved reason or may you, in the utilization of the stage, disregard any laws in your locale (counting yet not restricted to copyright laws) just as the laws of your nation and International law. Specifically, it is disallowed to utilize the things on our foundation for pages that advance: brutality, illegal intimidation, hard sexual entertainment, bigotry, obscenity content or warez programming joins.<br \\/><br \\/>You can\'t imitate, copy, duplicate, sell, exchange or adventure any of our segment, utilization of the offered on our things, or admittance to the administration without the express composed consent by us or item proprietor.<br \\/><br \\/>Our Members are liable for all substance posted on the discussion and demo and movement that happens under your record.<br \\/><br \\/>We hold the chance of hindering your participation account quickly if we will think about a particularly not allowed conduct.<br \\/><br \\/>If you make a record on our site, you are liable for keeping up the security of your record, and you are completely answerable for all exercises that happen under the record and some other activities taken regarding the record. You should quickly inform us, of any unapproved employments of your record or some other penetrates of security.<\\/p><\\/div><div class=\\\"mb-5\\\" style=\\\"color:rgb(111,111,111);font-family:Nunito, sans-serif;margin-bottom:3rem;\\\"><h3 class=\\\"mb-3\\\" style=\\\"font-weight:600;line-height:1.3;font-size:24px;font-family:Exo, sans-serif;color:rgb(54,54,54);\\\">Fiverr, Seoclerks Sellers Or Affiliates<\\/h3><p class=\\\"font-18\\\" style=\\\"margin-right:0px;margin-left:0px;font-size:18px;\\\">We do NOT ensure full SEO campaign conveyance within 24 hours. We make no assurance for conveyance time by any means. We give our best assessment to orders during the putting in of requests, anyway, these are gauges. We won\'t be considered liable for loss of assets, negative surveys or you being prohibited for late conveyance. If you are selling on a site that requires time touchy outcomes, utilize Our SEO Services at your own risk.<\\/p><\\/div><div class=\\\"mb-5\\\" style=\\\"color:rgb(111,111,111);font-family:Nunito, sans-serif;margin-bottom:3rem;\\\"><h3 class=\\\"mb-3\\\" style=\\\"font-weight:600;line-height:1.3;font-size:24px;font-family:Exo, sans-serif;color:rgb(54,54,54);\\\">Payment\\/Refund Policy<\\/h3><p class=\\\"font-18\\\" style=\\\"margin-right:0px;margin-left:0px;font-size:18px;\\\">No refund or cash back will be made. After a deposit has been finished, it is extremely unlikely to invert it. You should utilize your equilibrium on requests our administrations, Hosting, SEO campaign. You concur that once you complete a deposit, you won\'t document a debate or a chargeback against us in any way, shape, or form.<br \\/><br \\/>If you document a debate or chargeback against us after a deposit, we claim all authority to end every single future request, prohibit you from our site. False action, for example, utilizing unapproved or taken charge cards will prompt the end of your record. There are no special cases.<\\/p><\\/div><div class=\\\"mb-5\\\" style=\\\"color:rgb(111,111,111);font-family:Nunito, sans-serif;margin-bottom:3rem;\\\"><h3 class=\\\"mb-3\\\" style=\\\"font-weight:600;line-height:1.3;font-size:24px;font-family:Exo, sans-serif;color:rgb(54,54,54);\\\">Free Balance \\/ Coupon Policy<\\/h3><p class=\\\"font-18\\\" style=\\\"margin-right:0px;margin-left:0px;font-size:18px;\\\">We offer numerous approaches to get FREE Balance, Coupons and Deposit offers yet we generally reserve the privilege to audit it and deduct it from your record offset with any explanation we may it is a sort of misuse. If we choose to deduct a few or all of free Balance from your record balance, and your record balance becomes negative, at that point the record will naturally be suspended. If your record is suspended because of a negative Balance you can request to make a custom payment to settle your equilibrium to actuate your record.<\\/p><\\/div>\"}','2021-06-09 08:51:18','2021-06-09 08:51:18'),(44,'maintenance.data','{\"description\":\"<div class=\\\"mb-5\\\" style=\\\"color: rgb(111, 111, 111); font-family: Nunito, sans-serif; margin-bottom: 3rem !important;\\\"><h3 class=\\\"mb-3\\\" style=\\\"text-align: center; font-weight: 600; line-height: 1.3; font-size: 24px; font-family: Exo, sans-serif; color: rgb(54, 54, 54);\\\">What information do we collect?<\\/h3><p class=\\\"font-18\\\" style=\\\"text-align: center; margin-right: 0px; margin-left: 0px; font-size: 18px !important;\\\">We gather data from you when you register on our site, submit a request, buy any services, react to an overview, or round out a structure. At the point when requesting any assistance or enrolling on our site, as suitable, you might be approached to enter your: name, email address, or telephone number. You may, nonetheless, visit our site anonymously.<\\/p><\\/div>\"}','2020-07-04 23:42:52','2022-05-11 03:57:17'),(48,'footer.content','{\"subscribe_title\":\"Subscribe for new Offers and updates\",\"connect_title\":\"To get updates follow us on Facebook, Twitters etc.\"}','2023-02-26 07:07:04','2023-02-26 07:07:04'),(49,'footer.element','{\"has_image\":\"1\",\"image\":\"63fb28510697a1677404241.jpg\"}','2023-02-26 07:07:21','2023-02-26 07:07:21'),(50,'footer.element','{\"has_image\":\"1\",\"image\":\"63fb285c75e3c1677404252.jpg\"}','2023-02-26 07:07:32','2023-02-26 07:07:32'),(51,'footer.element','{\"has_image\":\"1\",\"image\":\"63fb286b77ef01677404267.jpg\"}','2023-02-26 07:07:47','2023-02-26 07:07:47'),(52,'footer.element','{\"has_image\":\"1\",\"image\":\"63fb28781d5ac1677404280.jpg\"}','2023-02-26 07:08:00','2023-02-26 07:08:00'),(53,'footer.element','{\"has_image\":\"1\",\"image\":\"63fb2885195a61677404293.jpg\"}','2023-02-26 07:08:13','2023-02-26 07:08:13'),(54,'footer.element','{\"has_image\":\"1\",\"image\":\"63fb28952a9311677404309.jpg\"}','2023-02-26 07:08:29','2023-02-26 07:08:29'),(55,'footer.element','{\"has_image\":\"1\",\"image\":\"63fb28a13a3c71677404321.jpg\"}','2023-02-26 07:08:41','2023-02-26 07:08:41'),(56,'footer.element','{\"has_image\":\"1\",\"image\":\"63fb28b0c801a1677404336.jpg\"}','2023-02-26 07:08:56','2023-02-26 07:08:56'),(57,'login.content','{\"has_image\":\"1\",\"heading\":\"Login Account\",\"subheading\":\"\",\"image\":\"63fb28dd2c0851677404381.jpg\"}','2023-02-26 07:09:41','2023-02-26 07:09:41'),(58,'policy_pages.element','{\"title\":\"Shipping and Delivery\",\"details\":\"<div><div><div><span style=\\\"font-size:1rem;\\\">Lorem ipsum dolor sit amet consectetur adipisicing elit. Cupiditate quae illo soluta sapiente minus voluptatibus molestias voluptates maiores repudiandae, velit quaerat error! Dolor alias voluptates rerum vitae illum officiis laboriosam, eos fugiat necessitatibus iste quasi vero porro at asperiores atque numquam adipisci esse perferendis hic dolore dolores facere quidem? Voluptatum, nemo voluptates. Qui, animi odit voluptatem velit nostrum rem maiores. Qui esse magnam enim natus numquam ab adipisci nihil mollitia odio ducimus architecto unde harum saepe illum, ipsa hic dicta alias cumque et minus veritatis assumenda a quo. Possimus, vitae est! Fuga quidem minima sunt modi. Officia natus quaerat nobis ut ab nulla. Tempora, corrupti? Animi excepturi voluptatem quod consectetur culpa autem aliquid? Inventore adipisci officia error dolore provident omnis sint perferendis, consequuntur, sapiente magni sequi quo quis nesciunt molestiae vero iure cum laboriosam fugit. Numquam sed expedita alias non? Sequi, harum cupiditate! Quasi non laboriosam optio ex fugit delectus minus incidunt excepturi! Nisi iure ex, nulla perspiciatis similique est, libero sapiente hic error amet, quisquam vel obcaecati fugit. Maxime cupiditate voluptatibus, nisi ullam error voluptas culpa at animi sequi eius suscipit ad ipsum qui illum provident dolores facere necessitatibus commodi vel in, laborum quidem aliquam ipsa quibusdam? Eius, alias voluptatem, laboriosam perferendis itaque, sapiente nisi beatae necessitatibus reprehenderit nam corrupti magnam qui omnis eveniet! Optio at expedita temporibus fugiat debitis eum? Dolore excepturi quod doloribus quam rem placeat at odit dicta amet expedita illo laboriosam minus ut minima, tenetur suscipit soluta assumenda. Nisi laboriosam adipisci animi consequuntur, ad illum repellat consequatur odit, laudantium velit non nobis labore illo omnis quod suscipit voluptates quaerat consectetur temporibus et, laborum quam ducimus earum! Repellat, fugit? Repudiandae repellendus maiores doloribus deleniti asperiores distinctio suscipit fugiat omnis culpa itaque? Harum et, velit ratione corrupti error asperiores optio, recusandae mollitia necessitatibus cumque vero voluptatem ullam porro aut eum earum! Consectetur voluptatum ratione dolor in earum molestiae ipsam quisquam, eum vitae suscipit voluptates recusandae. Cum eaque officiis ea et atque eveniet similique sequi illo!<\\/span><br \\/><\\/div><\\/div><\\/div>\"}','2023-02-26 07:11:24','2023-02-26 07:12:05'),(59,'register.content','{\"has_image\":\"1\",\"heading\":\"Create Account\",\"subheading\":\"\",\"image\":\"63fb29940bae41677404564.jpg\"}','2023-02-26 07:12:44','2023-02-26 07:12:44'),(60,'service.element','{\"has_image\":[\"1\"],\"title\":\"Gift Voucher\",\"short_detail\":\"Aliquam eleifend in elit congue\",\"image\":\"63fb29c5c36081677404613.png\"}','2023-02-26 07:13:33','2023-02-26 07:13:33'),(61,'service.element','{\"has_image\":[\"1\"],\"title\":\"Online Support 24\\/7\",\"short_detail\":\"Aliquam eleifend in elit congue\",\"image\":\"63fb2a1aa312d1677404698.png\"}','2023-02-26 07:14:58','2023-02-26 07:14:58'),(62,'service.element','{\"has_image\":[\"1\"],\"title\":\"Money Back Guarantee\",\"short_detail\":\"Aliquam eleifend in elit congue\",\"image\":\"63fb2a32b7b551677404722.png\"}','2023-02-26 07:15:22','2023-02-26 07:15:22'),(63,'service.element','{\"has_image\":[\"1\"],\"title\":\"Free Shipping\",\"short_detail\":\"Aliquam eleifend in elit congue\",\"image\":\"63fb2af3cf4ff1677404915.png\"}','2023-02-26 07:18:35','2023-02-26 07:18:35'),(64,'social_icon.element','{\"title\":\"Facebook\",\"icon\":\"<i class=\\\"fab fa-facebook-f\\\"><\\/i>\",\"url\":\"https:\\/\\/www.facebook.com\\/\"}','2023-02-26 07:20:07','2023-02-26 07:20:07'),(65,'social_icon.element','{\"title\":\"Twitter\",\"icon\":\"<i class=\\\"fab fa-twitter\\\"><\\/i>\",\"url\":\"https:\\/\\/www.twitter.com\\/\"}','2023-02-26 07:20:50','2023-02-26 07:20:50'),(66,'social_icon.element','{\"title\":\"Instagram\",\"icon\":\"<i class=\\\"fab fa-instagram\\\"><\\/i>\",\"url\":\"https:\\/\\/www.instagram.com\\/\"}','2023-02-26 07:21:30','2023-02-26 07:21:30'),(67,'social_icon.element','{\"title\":\"Linkedin\",\"icon\":\"<i class=\\\"fab fa-linkedin\\\"><\\/i>\",\"url\":\"https:\\/\\/www.linkedin.com\\/\"}','2023-02-26 07:21:58','2023-02-26 07:21:58'),(102,'banner.element','{\"has_image\":\"1\",\"url\":\"test\",\"image\":\"68eb0962511e81760233826.jpg\"}','2025-10-11 19:50:26','2025-10-11 19:50:26'),(106,'banner.element','{\"has_image\":\"1\",\"url\":\"https:\\/\\/stylebd.shop\",\"image\":\"6916fb0789ec51763113735.jpg\"}','2025-11-14 03:48:55','2025-11-14 03:48:55'),(109,'scrollbar.element','{\"title\":\"Home Banner Ticker\",\"display_order\":1,\"position\":\"banner_below\",\"template\":\"offer\",\"status\":1,\"visibility\":\"public\",\"visibility_users\":\"all\",\"visibility_pages\":\"all\",\"schedule_start\":null,\"schedule_end\":null,\"scroll_speed\":45,\"scroll_direction\":\"ltr\",\"loop_mode\":\"infinite\",\"pause_on_hover\":1,\"gap_between_items\":8,\"animation_type\":\"linear\",\"bar_height\":52,\"bar_padding\":null,\"bar_background_type\":null,\"bar_background_value\":null,\"bar_border\":null,\"bar_shadow\":null,\"hide_on_mobile\":0,\"hide_on_desktop\":0,\"items\":[{\"type\":\"emoji\",\"content\":\"≡ƒöÑ\",\"color\":\"#333333\",\"font_family\":\"inherit\",\"font_style\":\"normal\",\"font_size\":\"\",\"is_active\":1},{\"type\":\"text\",\"content\":\"Welcome! Get 20% OFF on first order.\",\"color\":\"#1a73e8\",\"font_family\":\"inherit\",\"font_style\":\"bold\",\"font_size\":\"\",\"is_active\":1},{\"type\":\"emoji\",\"content\":\"≡ƒÄü\",\"color\":\"#333333\",\"font_family\":\"inherit\",\"font_style\":\"normal\",\"font_size\":\"\",\"is_active\":1},{\"type\":\"text\",\"content\":\"Free shipping on orders over $50.\",\"color\":\"#059669\",\"font_family\":\"inherit\",\"font_style\":\"normal\",\"font_size\":\"\",\"is_active\":1},{\"type\":\"emoji\",\"content\":\"ΓÜí\",\"color\":\"#333333\",\"font_family\":\"inherit\",\"font_style\":\"normal\",\"font_size\":\"\",\"is_active\":1},{\"type\":\"text\",\"content\":\"Flash Sale: Up to 50% off on selected items.\",\"color\":\"#dc2626\",\"font_family\":\"inherit\",\"font_style\":\"bold\",\"font_size\":\"\",\"is_active\":1}]}','2026-03-19 10:16:50','2026-03-19 10:16:50'),(110,'home_section.settings','{\"power_zone_enabled\":1,\"show_category_icons\":1,\"show_flash_deals\":1,\"show_trending\":1,\"show_quick_services\":1,\"show_promo_blocks\":1,\"show_quick_category_boxes\":1,\"flash_sale_end_date\":\"2026-03-19T23:59:59+00:00\",\"flash_sale_title\":\"Flash Sale\",\"trust_section_enabled\":1,\"social_proof_enabled\":1,\"live_purchase_enabled\":0,\"reviews_slider_enabled\":1,\"top_rated_enabled\":1,\"recommendation_enabled\":1,\"recently_viewed_enabled\":1,\"similar_products_enabled\":1,\"sticky_cart_enabled\":1,\"quick_view_enabled\":1,\"wishlist_popup_enabled\":1,\"compare_enabled\":1,\"floating_cart_enabled\":1,\"conversion_enabled\":1,\"limited_stock_enabled\":1,\"only_x_left_enabled\":1,\"people_viewing_enabled\":0,\"recently_sold_enabled\":0,\"flash_deals_limit\":8,\"trending_limit\":8,\"top_rated_limit\":8,\"reviews_slider_limit\":6}','2026-03-19 04:44:04','2026-03-19 04:44:04'),(111,'home_section.quick_category','{\"title\":\"Hot Deals\",\"icon\":\"las la-bolt\",\"link_type\":\"hot_deal\",\"display_order\":1}','2026-03-19 04:44:04','2026-03-19 04:44:04'),(112,'home_section.quick_category','{\"title\":\"Top Selling\",\"icon\":\"las la-chart-line\",\"link_type\":\"best_selling\",\"display_order\":2}','2026-03-19 04:44:04','2026-03-19 04:44:04'),(113,'home_section.quick_category','{\"title\":\"New Arrival\",\"icon\":\"las la-star\",\"link_type\":\"new_arrival\",\"display_order\":3}','2026-03-19 04:44:04','2026-03-19 04:44:04'),(114,'home_section.quick_category','{\"title\":\"Featured\",\"icon\":\"las la-gem\",\"link_type\":\"featured\",\"display_order\":4}','2026-03-19 04:44:04','2026-03-19 04:44:04'),(115,'home_section.quick_category','{\"title\":\"Discount\",\"icon\":\"las la-tag\",\"link_type\":\"discount\",\"display_order\":5}','2026-03-19 04:44:04','2026-03-19 04:44:04'),(116,'home_section.trust','{\"title\":\"Secure Payment\",\"icon\":\"las la-lock\",\"short_detail\":\"100% secure payment\",\"url\":\"#\",\"display_order\":1}','2026-03-19 04:44:04','2026-03-19 04:44:04'),(117,'home_section.trust','{\"title\":\"Fast Delivery\",\"icon\":\"las la-shipping-fast\",\"short_detail\":\"Quick delivery\",\"url\":\"#\",\"display_order\":2}','2026-03-19 04:44:04','2026-03-19 04:44:04'),(118,'home_section.trust','{\"title\":\"Easy Return\",\"icon\":\"las la-undo\",\"short_detail\":\"Easy return policy\",\"url\":\"#\",\"display_order\":3}','2026-03-19 04:44:04','2026-03-19 04:44:04'),(119,'home_section.trust','{\"title\":\"Customer Satisfaction\",\"icon\":\"las la-smile\",\"short_detail\":\"Satisfaction guaranteed\",\"url\":\"#\",\"display_order\":4}','2026-03-19 04:44:04','2026-03-19 04:44:04'),(120,'home_section.trust','{\"title\":\"Authentic Product\",\"icon\":\"las la-certificate\",\"short_detail\":\"100% authentic\",\"url\":\"#\",\"display_order\":5}','2026-03-19 04:44:04','2026-03-19 04:44:04'),(121,'product_slider.settings','{\"auto_scroll_enabled\":1,\"scroll_interval_seconds\":4,\"scroll_animation_speed_ms\":600,\"products_per_row_desktop\":6,\"products_per_row_tablet\":4,\"products_per_row_mobile\":2,\"hot_deal_interval_seconds\":3,\"featured_interval_seconds\":5,\"new_arrivals_interval_seconds\":4,\"trending_interval_seconds\":4,\"best_selling_interval_seconds\":5,\"recommended_interval_seconds\":5}','2026-03-19 04:44:04','2026-03-19 04:44:04');
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
INSERT INTO `gateway_currencies` VALUES (1007,'bKash BDT','BDT','├ô┬║Γöé',902,'Bkash',1.00000000,1000000.00000000,0.00,0.00000000,1.00000000,NULL,'{\"app_key\":\"0vWQuCRGiUX7EPVjQDr0EUAYtc\",\"app_secret\":\"jcUNPBgbcqEDedNKdvE4G1cAK7D3hCjmJccNPZZBq96QIxxwAMEx\",\"username\":\"01770618567\",\"password\":\"D7DaC<*E*eG\",\"base_url\":\"https:\\/\\/tokenized.sandbox.bka.sh\\/v1.2.0-beta\"}','2025-10-14 14:31:15','2026-03-19 11:09:14'),(2019,'TZSMMPAY BDT','BDT','αº│',906,'TZSMMPAY',1.00000000,1000000.00000000,0.00,0.00000000,1.00000000,NULL,'{\"api_key\":\"xB4mEUSZVic4qloJuIzZJzusqZIOOuOodwgVIPwhrc6IIHwcDj\",\"create_url\":\"https:\\/\\/cdn.wintersmm.com\\/api\\/payment\\/create\"}','2025-10-16 00:15:07','2025-10-16 00:15:07'),(2023,'WINTERSMM','BDT','αº│',907,'WINTERSMM',10.00000000,100000000.00000000,0.00,0.00000000,1.00000000,NULL,'{\"brand_key\":\"gZ3Oheox6TJSMmOwsO4RRmjem3zS0LJ5htd5YTaqBFOjiIJJJG\",\"create_url\":\"https:\\/\\/cdn.wintersmm.com\\/api\\/payment\\/create\",\"verify_url\":\"https:\\/\\/cdn.wintersmm.com\\/api\\/payment\\/verify\"}','2025-10-16 00:45:58','2025-10-16 00:45:58'),(2024,'Aamarpay BDT','BDT','├ô┬║Γöé',903,'Aamarpay',1.00000000,1000000.00000000,0.00,0.00000000,1.00000000,NULL,'{\"instruction\":\"Pay with Aamarpay\"}','2026-02-11 07:38:42','2026-03-19 11:09:14'),(2025,'Nagad BDT','BDT','├ô┬║Γöé',904,'Nagad',1.00000000,1000000.00000000,0.00,0.00000000,1.00000000,NULL,'{\"instruction\":\"Pay with Nagad\"}','2026-02-11 07:38:42','2026-03-19 11:09:14');
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
INSERT INTO `gateways` VALUES (1,0,101,'Paypal','Paypal',1,0,'{\"paypal_email\":{\"title\":\"PayPal Email\",\"global\":true,\"value\":\"sb-owud61543012@business.example.com\"}}','{\"AUD\":\"AUD\",\"BRL\":\"BRL\",\"CAD\":\"CAD\",\"CZK\":\"CZK\",\"DKK\":\"DKK\",\"EUR\":\"EUR\",\"HKD\":\"HKD\",\"HUF\":\"HUF\",\"INR\":\"INR\",\"ILS\":\"ILS\",\"JPY\":\"JPY\",\"MYR\":\"MYR\",\"MXN\":\"MXN\",\"TWD\":\"TWD\",\"NZD\":\"NZD\",\"NOK\":\"NOK\",\"PHP\":\"PHP\",\"PLN\":\"PLN\",\"GBP\":\"GBP\",\"RUB\":\"RUB\",\"SGD\":\"SGD\",\"SEK\":\"SEK\",\"CHF\":\"CHF\",\"THB\":\"THB\",\"USD\":\"$\"}',0,NULL,NULL,NULL,'2019-09-14 13:14:22','2021-05-21 00:04:38'),(2,0,102,'Perfect Money','PerfectMoney',1,0,'{\"passphrase\":{\"title\":\"ALTERNATE PASSPHRASE\",\"global\":true,\"value\":\"hR26aw02Q1eEeUPSIfuwNypXX\"},\"wallet_id\":{\"title\":\"PM Wallet\",\"global\":false,\"value\":\"\"}}','{\"USD\":\"$\",\"EUR\":\"\\u20ac\"}',0,NULL,NULL,NULL,'2019-09-14 13:14:22','2021-05-21 01:35:33'),(3,0,103,'Stripe Hosted','Stripe',1,0,'{\"secret_key\":{\"title\":\"Secret Key\",\"global\":true,\"value\":\"sk_test_51I6GGiCGv1sRiQlEi5v1or9eR0HVbuzdMd2rW4n3DxC8UKfz66R4X6n4yYkzvI2LeAIuRU9H99ZpY7XCNFC9xMs500vBjZGkKG\"},\"publishable_key\":{\"title\":\"PUBLISHABLE KEY\",\"global\":true,\"value\":\"pk_test_51I6GGiCGv1sRiQlEOisPKrjBqQqqcFsw8mXNaZ2H2baN6R01NulFS7dKFji1NRRxuchoUTEDdB7ujKcyKYSVc0z500eth7otOM\"}}','{\"USD\":\"USD\",\"AUD\":\"AUD\",\"BRL\":\"BRL\",\"CAD\":\"CAD\",\"CHF\":\"CHF\",\"DKK\":\"DKK\",\"EUR\":\"EUR\",\"GBP\":\"GBP\",\"HKD\":\"HKD\",\"INR\":\"INR\",\"JPY\":\"JPY\",\"MXN\":\"MXN\",\"MYR\":\"MYR\",\"NOK\":\"NOK\",\"NZD\":\"NZD\",\"PLN\":\"PLN\",\"SEK\":\"SEK\",\"SGD\":\"SGD\"}',0,NULL,NULL,NULL,'2019-09-14 13:14:22','2021-05-21 00:48:36'),(4,0,104,'Skrill','Skrill',1,0,'{\"pay_to_email\":{\"title\":\"Skrill Email\",\"global\":true,\"value\":\"merchant@skrill.com\"},\"secret_key\":{\"title\":\"Secret Key\",\"global\":true,\"value\":\"---\"}}','{\"AED\":\"AED\",\"AUD\":\"AUD\",\"BGN\":\"BGN\",\"BHD\":\"BHD\",\"CAD\":\"CAD\",\"CHF\":\"CHF\",\"CZK\":\"CZK\",\"DKK\":\"DKK\",\"EUR\":\"EUR\",\"GBP\":\"GBP\",\"HKD\":\"HKD\",\"HRK\":\"HRK\",\"HUF\":\"HUF\",\"ILS\":\"ILS\",\"INR\":\"INR\",\"ISK\":\"ISK\",\"JOD\":\"JOD\",\"JPY\":\"JPY\",\"KRW\":\"KRW\",\"KWD\":\"KWD\",\"MAD\":\"MAD\",\"MYR\":\"MYR\",\"NOK\":\"NOK\",\"NZD\":\"NZD\",\"OMR\":\"OMR\",\"PLN\":\"PLN\",\"QAR\":\"QAR\",\"RON\":\"RON\",\"RSD\":\"RSD\",\"SAR\":\"SAR\",\"SEK\":\"SEK\",\"SGD\":\"SGD\",\"THB\":\"THB\",\"TND\":\"TND\",\"TRY\":\"TRY\",\"TWD\":\"TWD\",\"USD\":\"USD\",\"ZAR\":\"ZAR\",\"COP\":\"COP\"}',0,NULL,NULL,NULL,'2019-09-14 13:14:22','2021-05-21 01:30:16'),(5,0,105,'PayTM','Paytm',1,0,'{\"MID\":{\"title\":\"Merchant ID\",\"global\":true,\"value\":\"DIY12386817555501617\"},\"merchant_key\":{\"title\":\"Merchant Key\",\"global\":true,\"value\":\"bKMfNxPPf_QdZppa\"},\"WEBSITE\":{\"title\":\"Paytm Website\",\"global\":true,\"value\":\"DIYtestingweb\"},\"INDUSTRY_TYPE_ID\":{\"title\":\"Industry Type\",\"global\":true,\"value\":\"Retail\"},\"CHANNEL_ID\":{\"title\":\"CHANNEL ID\",\"global\":true,\"value\":\"WEB\"},\"transaction_url\":{\"title\":\"Transaction URL\",\"global\":true,\"value\":\"https:\\/\\/pguat.paytm.com\\/oltp-web\\/processTransaction\"},\"transaction_status_url\":{\"title\":\"Transaction STATUS URL\",\"global\":true,\"value\":\"https:\\/\\/pguat.paytm.com\\/paytmchecksum\\/paytmCallback.jsp\"}}','{\"AUD\":\"AUD\",\"ARS\":\"ARS\",\"BDT\":\"BDT\",\"BRL\":\"BRL\",\"BGN\":\"BGN\",\"CAD\":\"CAD\",\"CLP\":\"CLP\",\"CNY\":\"CNY\",\"COP\":\"COP\",\"HRK\":\"HRK\",\"CZK\":\"CZK\",\"DKK\":\"DKK\",\"EGP\":\"EGP\",\"EUR\":\"EUR\",\"GEL\":\"GEL\",\"GHS\":\"GHS\",\"HKD\":\"HKD\",\"HUF\":\"HUF\",\"INR\":\"INR\",\"IDR\":\"IDR\",\"ILS\":\"ILS\",\"JPY\":\"JPY\",\"KES\":\"KES\",\"MYR\":\"MYR\",\"MXN\":\"MXN\",\"MAD\":\"MAD\",\"NPR\":\"NPR\",\"NZD\":\"NZD\",\"NGN\":\"NGN\",\"NOK\":\"NOK\",\"PKR\":\"PKR\",\"PEN\":\"PEN\",\"PHP\":\"PHP\",\"PLN\":\"PLN\",\"RON\":\"RON\",\"RUB\":\"RUB\",\"SGD\":\"SGD\",\"ZAR\":\"ZAR\",\"KRW\":\"KRW\",\"LKR\":\"LKR\",\"SEK\":\"SEK\",\"CHF\":\"CHF\",\"THB\":\"THB\",\"TRY\":\"TRY\",\"UGX\":\"UGX\",\"UAH\":\"UAH\",\"AED\":\"AED\",\"GBP\":\"GBP\",\"USD\":\"USD\",\"VND\":\"VND\",\"XOF\":\"XOF\"}',0,NULL,NULL,NULL,'2019-09-14 13:14:22','2021-05-21 03:00:44'),(6,0,106,'Payeer','Payeer',1,0,'{\"merchant_id\":{\"title\":\"Merchant ID\",\"global\":true,\"value\":\"866989763\"},\"secret_key\":{\"title\":\"Secret key\",\"global\":true,\"value\":\"7575\"}}','{\"USD\":\"USD\",\"EUR\":\"EUR\",\"RUB\":\"RUB\"}',0,'{\"status\":{\"title\": \"Status URL\",\"value\":\"ipn.Payeer\"}}',NULL,NULL,'2019-09-14 13:14:22','2022-08-28 10:11:14'),(7,0,107,'PayStack','Paystack',1,0,'{\"public_key\":{\"title\":\"Public key\",\"global\":true,\"value\":\"pk_test_cd330608eb47970889bca397ced55c1dd5ad3783\"},\"secret_key\":{\"title\":\"Secret key\",\"global\":true,\"value\":\"sk_test_8a0b1f199362d7acc9c390bff72c4e81f74e2ac3\"}}','{\"USD\":\"USD\",\"NGN\":\"NGN\"}',0,'{\"callback\":{\"title\": \"Callback URL\",\"value\":\"ipn.Paystack\"},\"webhook\":{\"title\": \"Webhook URL\",\"value\":\"ipn.Paystack\"}}\r\n',NULL,NULL,'2019-09-14 13:14:22','2021-05-21 01:49:51'),(8,0,108,'VoguePay','Voguepay',1,0,'{\"merchant_id\":{\"title\":\"MERCHANT ID\",\"global\":true,\"value\":\"demo\"}}','{\"USD\":\"USD\",\"GBP\":\"GBP\",\"EUR\":\"EUR\",\"GHS\":\"GHS\",\"NGN\":\"NGN\",\"ZAR\":\"ZAR\"}',0,NULL,NULL,NULL,'2019-09-14 13:14:22','2021-05-21 01:22:38'),(9,0,109,'Flutterwave','Flutterwave',1,0,'{\"public_key\":{\"title\":\"Public Key\",\"global\":true,\"value\":\"----------------\"},\"secret_key\":{\"title\":\"Secret Key\",\"global\":true,\"value\":\"-----------------------\"},\"encryption_key\":{\"title\":\"Encryption Key\",\"global\":true,\"value\":\"------------------\"}}','{\"BIF\":\"BIF\",\"CAD\":\"CAD\",\"CDF\":\"CDF\",\"CVE\":\"CVE\",\"EUR\":\"EUR\",\"GBP\":\"GBP\",\"GHS\":\"GHS\",\"GMD\":\"GMD\",\"GNF\":\"GNF\",\"KES\":\"KES\",\"LRD\":\"LRD\",\"MWK\":\"MWK\",\"MZN\":\"MZN\",\"NGN\":\"NGN\",\"RWF\":\"RWF\",\"SLL\":\"SLL\",\"STD\":\"STD\",\"TZS\":\"TZS\",\"UGX\":\"UGX\",\"USD\":\"USD\",\"XAF\":\"XAF\",\"XOF\":\"XOF\",\"ZMK\":\"ZMK\",\"ZMW\":\"ZMW\",\"ZWD\":\"ZWD\"}',0,NULL,NULL,NULL,'2019-09-14 13:14:22','2021-06-05 11:37:45'),(10,0,110,'RazorPay','Razorpay',1,0,'{\"key_id\":{\"title\":\"Key Id\",\"global\":true,\"value\":\"rzp_test_kiOtejPbRZU90E\"},\"key_secret\":{\"title\":\"Key Secret \",\"global\":true,\"value\":\"osRDebzEqbsE1kbyQJ4y0re7\"}}','{\"INR\":\"INR\"}',0,NULL,NULL,NULL,'2019-09-14 13:14:22','2021-05-21 02:51:32'),(11,0,111,'Stripe Storefront','StripeJs',1,0,'{\"secret_key\":{\"title\":\"Secret Key\",\"global\":true,\"value\":\"sk_test_51I6GGiCGv1sRiQlEi5v1or9eR0HVbuzdMd2rW4n3DxC8UKfz66R4X6n4yYkzvI2LeAIuRU9H99ZpY7XCNFC9xMs500vBjZGkKG\"},\"publishable_key\":{\"title\":\"PUBLISHABLE KEY\",\"global\":true,\"value\":\"pk_test_51I6GGiCGv1sRiQlEOisPKrjBqQqqcFsw8mXNaZ2H2baN6R01NulFS7dKFji1NRRxuchoUTEDdB7ujKcyKYSVc0z500eth7otOM\"}}','{\"USD\":\"USD\",\"AUD\":\"AUD\",\"BRL\":\"BRL\",\"CAD\":\"CAD\",\"CHF\":\"CHF\",\"DKK\":\"DKK\",\"EUR\":\"EUR\",\"GBP\":\"GBP\",\"HKD\":\"HKD\",\"INR\":\"INR\",\"JPY\":\"JPY\",\"MXN\":\"MXN\",\"MYR\":\"MYR\",\"NOK\":\"NOK\",\"NZD\":\"NZD\",\"PLN\":\"PLN\",\"SEK\":\"SEK\",\"SGD\":\"SGD\"}',0,NULL,NULL,NULL,'2019-09-14 13:14:22','2021-05-21 00:53:10'),(12,0,112,'Instamojo','Instamojo',1,0,'{\"api_key\":{\"title\":\"API KEY\",\"global\":true,\"value\":\"test_2241633c3bc44a3de84a3b33969\"},\"auth_token\":{\"title\":\"Auth Token\",\"global\":true,\"value\":\"test_279f083f7bebefd35217feef22d\"},\"salt\":{\"title\":\"Salt\",\"global\":true,\"value\":\"19d38908eeff4f58b2ddda2c6d86ca25\"}}','{\"INR\":\"INR\"}',0,NULL,NULL,NULL,'2019-09-14 13:14:22','2021-05-21 02:56:20'),(13,0,501,'Blockchain','Blockchain',1,0,'{\"api_key\":{\"title\":\"API Key\",\"global\":true,\"value\":\"55529946-05ca-48ff-8710-f279d86b1cc5\"},\"xpub_code\":{\"title\":\"XPUB CODE\",\"global\":true,\"value\":\"xpub6CKQ3xxWyBoFAF83izZCSFUorptEU9AF8TezhtWeMU5oefjX3sFSBw62Lr9iHXPkXmDQJJiHZeTRtD9Vzt8grAYRhvbz4nEvBu3QKELVzFK\"}}','{\"BTC\":\"BTC\"}',1,NULL,NULL,NULL,'2019-09-14 13:14:22','2022-03-21 07:41:56'),(15,0,503,'CoinPayments','Coinpayments',1,0,'{\"public_key\":{\"title\":\"Public Key\",\"global\":true,\"value\":\"---------------\"},\"private_key\":{\"title\":\"Private Key\",\"global\":true,\"value\":\"------------\"},\"merchant_id\":{\"title\":\"Merchant ID\",\"global\":true,\"value\":\"93a1e014c4ad60a7980b4a7239673cb4\"}}','{\"BTC\":\"Bitcoin\",\"BTC.LN\":\"Bitcoin (Lightning Network)\",\"LTC\":\"Litecoin\",\"CPS\":\"CPS Coin\",\"VLX\":\"Velas\",\"APL\":\"Apollo\",\"AYA\":\"Aryacoin\",\"BAD\":\"Badcoin\",\"BCD\":\"Bitcoin Diamond\",\"BCH\":\"Bitcoin Cash\",\"BCN\":\"Bytecoin\",\"BEAM\":\"BEAM\",\"BITB\":\"Bean Cash\",\"BLK\":\"BlackCoin\",\"BSV\":\"Bitcoin SV\",\"BTAD\":\"Bitcoin Adult\",\"BTG\":\"Bitcoin Gold\",\"BTT\":\"BitTorrent\",\"CLOAK\":\"CloakCoin\",\"CLUB\":\"ClubCoin\",\"CRW\":\"Crown\",\"CRYP\":\"CrypticCoin\",\"CRYT\":\"CryTrExCoin\",\"CURE\":\"CureCoin\",\"DASH\":\"DASH\",\"DCR\":\"Decred\",\"DEV\":\"DeviantCoin\",\"DGB\":\"DigiByte\",\"DOGE\":\"Dogecoin\",\"EBST\":\"eBoost\",\"EOS\":\"EOS\",\"ETC\":\"Ether Classic\",\"ETH\":\"Ethereum\",\"ETN\":\"Electroneum\",\"EUNO\":\"EUNO\",\"EXP\":\"EXP\",\"Expanse\":\"Expanse\",\"FLASH\":\"FLASH\",\"GAME\":\"GameCredits\",\"GLC\":\"Goldcoin\",\"GRS\":\"Groestlcoin\",\"KMD\":\"Komodo\",\"LOKI\":\"LOKI\",\"LSK\":\"LSK\",\"MAID\":\"MaidSafeCoin\",\"MUE\":\"MonetaryUnit\",\"NAV\":\"NAV Coin\",\"NEO\":\"NEO\",\"NMC\":\"Namecoin\",\"NVST\":\"NVO Token\",\"NXT\":\"NXT\",\"OMNI\":\"OMNI\",\"PINK\":\"PinkCoin\",\"PIVX\":\"PIVX\",\"POT\":\"PotCoin\",\"PPC\":\"Peercoin\",\"PROC\":\"ProCurrency\",\"PURA\":\"PURA\",\"QTUM\":\"QTUM\",\"RES\":\"Resistance\",\"RVN\":\"Ravencoin\",\"RVR\":\"RevolutionVR\",\"SBD\":\"Steem Dollars\",\"SMART\":\"SmartCash\",\"SOXAX\":\"SOXAX\",\"STEEM\":\"STEEM\",\"STRAT\":\"STRAT\",\"SYS\":\"Syscoin\",\"TPAY\":\"TokenPay\",\"TRIGGERS\":\"Triggers\",\"TRX\":\" TRON\",\"UBQ\":\"Ubiq\",\"UNIT\":\"UniversalCurrency\",\"USDT\":\"Tether USD (Omni Layer)\",\"USDT.BEP20\":\"Tether USD (BSC Chain)\",\"USDT.ERC20\":\"Tether USD (ERC20)\",\"USDT.TRC20\":\"Tether USD (Tron/TRC20)\",\"VTC\":\"Vertcoin\",\"WAVES\":\"Waves\",\"XCP\":\"Counterparty\",\"XEM\":\"NEM\",\"XMR\":\"Monero\",\"XSN\":\"Stakenet\",\"XSR\":\"SucreCoin\",\"XVG\":\"VERGE\",\"XZC\":\"ZCoin\",\"ZEC\":\"ZCash\",\"ZEN\":\"Horizen\"}',1,NULL,NULL,NULL,'2019-09-14 13:14:22','2021-05-21 02:07:14'),(16,0,504,'CoinPayments Fiat','CoinpaymentsFiat',1,0,'{\"merchant_id\":{\"title\":\"Merchant ID\",\"global\":true,\"value\":\"6515561\"}}','{\"USD\":\"USD\",\"AUD\":\"AUD\",\"BRL\":\"BRL\",\"CAD\":\"CAD\",\"CHF\":\"CHF\",\"CLP\":\"CLP\",\"CNY\":\"CNY\",\"DKK\":\"DKK\",\"EUR\":\"EUR\",\"GBP\":\"GBP\",\"HKD\":\"HKD\",\"INR\":\"INR\",\"ISK\":\"ISK\",\"JPY\":\"JPY\",\"KRW\":\"KRW\",\"NZD\":\"NZD\",\"PLN\":\"PLN\",\"RUB\":\"RUB\",\"SEK\":\"SEK\",\"SGD\":\"SGD\",\"THB\":\"THB\",\"TWD\":\"TWD\"}',0,NULL,NULL,NULL,'2019-09-14 13:14:22','2021-05-21 02:07:44'),(17,0,505,'Coingate','Coingate',1,0,'{\"api_key\":{\"title\":\"API Key\",\"global\":true,\"value\":\"6354mwVCEw5kHzRJ6thbGo-N\"}}','{\"USD\":\"USD\",\"EUR\":\"EUR\"}',0,NULL,NULL,NULL,'2019-09-14 13:14:22','2022-03-30 09:24:57'),(18,0,506,'Coinbase Commerce','CoinbaseCommerce',1,0,'{\"api_key\":{\"title\":\"API Key\",\"global\":true,\"value\":\"c47cd7df-d8e8-424b-a20a\"},\"secret\":{\"title\":\"Webhook Shared Secret\",\"global\":true,\"value\":\"55871878-2c32-4f64-ab66\"}}','{\"USD\":\"USD\",\"EUR\":\"EUR\",\"JPY\":\"JPY\",\"GBP\":\"GBP\",\"AUD\":\"AUD\",\"CAD\":\"CAD\",\"CHF\":\"CHF\",\"CNY\":\"CNY\",\"SEK\":\"SEK\",\"NZD\":\"NZD\",\"MXN\":\"MXN\",\"SGD\":\"SGD\",\"HKD\":\"HKD\",\"NOK\":\"NOK\",\"KRW\":\"KRW\",\"TRY\":\"TRY\",\"RUB\":\"RUB\",\"INR\":\"INR\",\"BRL\":\"BRL\",\"ZAR\":\"ZAR\",\"AED\":\"AED\",\"AFN\":\"AFN\",\"ALL\":\"ALL\",\"AMD\":\"AMD\",\"ANG\":\"ANG\",\"AOA\":\"AOA\",\"ARS\":\"ARS\",\"AWG\":\"AWG\",\"AZN\":\"AZN\",\"BAM\":\"BAM\",\"BBD\":\"BBD\",\"BDT\":\"BDT\",\"BGN\":\"BGN\",\"BHD\":\"BHD\",\"BIF\":\"BIF\",\"BMD\":\"BMD\",\"BND\":\"BND\",\"BOB\":\"BOB\",\"BSD\":\"BSD\",\"BTN\":\"BTN\",\"BWP\":\"BWP\",\"BYN\":\"BYN\",\"BZD\":\"BZD\",\"CDF\":\"CDF\",\"CLF\":\"CLF\",\"CLP\":\"CLP\",\"COP\":\"COP\",\"CRC\":\"CRC\",\"CUC\":\"CUC\",\"CUP\":\"CUP\",\"CVE\":\"CVE\",\"CZK\":\"CZK\",\"DJF\":\"DJF\",\"DKK\":\"DKK\",\"DOP\":\"DOP\",\"DZD\":\"DZD\",\"EGP\":\"EGP\",\"ERN\":\"ERN\",\"ETB\":\"ETB\",\"FJD\":\"FJD\",\"FKP\":\"FKP\",\"GEL\":\"GEL\",\"GGP\":\"GGP\",\"GHS\":\"GHS\",\"GIP\":\"GIP\",\"GMD\":\"GMD\",\"GNF\":\"GNF\",\"GTQ\":\"GTQ\",\"GYD\":\"GYD\",\"HNL\":\"HNL\",\"HRK\":\"HRK\",\"HTG\":\"HTG\",\"HUF\":\"HUF\",\"IDR\":\"IDR\",\"ILS\":\"ILS\",\"IMP\":\"IMP\",\"IQD\":\"IQD\",\"IRR\":\"IRR\",\"ISK\":\"ISK\",\"JEP\":\"JEP\",\"JMD\":\"JMD\",\"JOD\":\"JOD\",\"KES\":\"KES\",\"KGS\":\"KGS\",\"KHR\":\"KHR\",\"KMF\":\"KMF\",\"KPW\":\"KPW\",\"KWD\":\"KWD\",\"KYD\":\"KYD\",\"KZT\":\"KZT\",\"LAK\":\"LAK\",\"LBP\":\"LBP\",\"LKR\":\"LKR\",\"LRD\":\"LRD\",\"LSL\":\"LSL\",\"LYD\":\"LYD\",\"MAD\":\"MAD\",\"MDL\":\"MDL\",\"MGA\":\"MGA\",\"MKD\":\"MKD\",\"MMK\":\"MMK\",\"MNT\":\"MNT\",\"MOP\":\"MOP\",\"MRO\":\"MRO\",\"MUR\":\"MUR\",\"MVR\":\"MVR\",\"MWK\":\"MWK\",\"MYR\":\"MYR\",\"MZN\":\"MZN\",\"NAD\":\"NAD\",\"NGN\":\"NGN\",\"NIO\":\"NIO\",\"NPR\":\"NPR\",\"OMR\":\"OMR\",\"PAB\":\"PAB\",\"PEN\":\"PEN\",\"PGK\":\"PGK\",\"PHP\":\"PHP\",\"PKR\":\"PKR\",\"PLN\":\"PLN\",\"PYG\":\"PYG\",\"QAR\":\"QAR\",\"RON\":\"RON\",\"RSD\":\"RSD\",\"RWF\":\"RWF\",\"SAR\":\"SAR\",\"SBD\":\"SBD\",\"SCR\":\"SCR\",\"SDG\":\"SDG\",\"SHP\":\"SHP\",\"SLL\":\"SLL\",\"SOS\":\"SOS\",\"SRD\":\"SRD\",\"SSP\":\"SSP\",\"STD\":\"STD\",\"SVC\":\"SVC\",\"SYP\":\"SYP\",\"SZL\":\"SZL\",\"THB\":\"THB\",\"TJS\":\"TJS\",\"TMT\":\"TMT\",\"TND\":\"TND\",\"TOP\":\"TOP\",\"TTD\":\"TTD\",\"TWD\":\"TWD\",\"TZS\":\"TZS\",\"UAH\":\"UAH\",\"UGX\":\"UGX\",\"UYU\":\"UYU\",\"UZS\":\"UZS\",\"VEF\":\"VEF\",\"VND\":\"VND\",\"VUV\":\"VUV\",\"WST\":\"WST\",\"XAF\":\"XAF\",\"XAG\":\"XAG\",\"XAU\":\"XAU\",\"XCD\":\"XCD\",\"XDR\":\"XDR\",\"XOF\":\"XOF\",\"XPD\":\"XPD\",\"XPF\":\"XPF\",\"XPT\":\"XPT\",\"YER\":\"YER\",\"ZMW\":\"ZMW\",\"ZWL\":\"ZWL\"}\r\n\r\n',0,'{\"endpoint\":{\"title\": \"Webhook Endpoint\",\"value\":\"ipn.CoinbaseCommerce\"}}',NULL,NULL,'2019-09-14 13:14:22','2021-05-21 02:02:47'),(24,0,113,'Paypal Express','PaypalSdk',1,0,'{\"clientId\":{\"title\":\"Paypal Client ID\",\"global\":true,\"value\":\"Ae0-tixtSV7DvLwIh3Bmu7JvHrjh5EfGdXr_cEklKAVjjezRZ747BxKILiBdzlKKyp-W8W_T7CKH1Ken\"},\"clientSecret\":{\"title\":\"Client Secret\",\"global\":true,\"value\":\"EOhbvHZgFNO21soQJT1L9Q00M3rK6PIEsdiTgXRBt2gtGtxwRer5JvKnVUGNU5oE63fFnjnYY7hq3HBA\"}}','{\"AUD\":\"AUD\",\"BRL\":\"BRL\",\"CAD\":\"CAD\",\"CZK\":\"CZK\",\"DKK\":\"DKK\",\"EUR\":\"EUR\",\"HKD\":\"HKD\",\"HUF\":\"HUF\",\"INR\":\"INR\",\"ILS\":\"ILS\",\"JPY\":\"JPY\",\"MYR\":\"MYR\",\"MXN\":\"MXN\",\"TWD\":\"TWD\",\"NZD\":\"NZD\",\"NOK\":\"NOK\",\"PHP\":\"PHP\",\"PLN\":\"PLN\",\"GBP\":\"GBP\",\"RUB\":\"RUB\",\"SGD\":\"SGD\",\"SEK\":\"SEK\",\"CHF\":\"CHF\",\"THB\":\"THB\",\"USD\":\"$\"}',0,NULL,NULL,NULL,'2019-09-14 13:14:22','2021-05-20 23:01:08'),(25,0,114,'Stripe Checkout','StripeV3',1,0,'{\"secret_key\":{\"title\":\"Secret Key\",\"global\":true,\"value\":\"sk_test_51I6GGiCGv1sRiQlEi5v1or9eR0HVbuzdMd2rW4n3DxC8UKfz66R4X6n4yYkzvI2LeAIuRU9H99ZpY7XCNFC9xMs500vBjZGkKG\"},\"publishable_key\":{\"title\":\"PUBLISHABLE KEY\",\"global\":true,\"value\":\"pk_test_51I6GGiCGv1sRiQlEOisPKrjBqQqqcFsw8mXNaZ2H2baN6R01NulFS7dKFji1NRRxuchoUTEDdB7ujKcyKYSVc0z500eth7otOM\"},\"end_point\":{\"title\":\"End Point Secret\",\"global\":true,\"value\":\"whsec_lUmit1gtxwKTveLnSe88xCSDdnPOt8g5\"}}','{\"USD\":\"USD\",\"AUD\":\"AUD\",\"BRL\":\"BRL\",\"CAD\":\"CAD\",\"CHF\":\"CHF\",\"DKK\":\"DKK\",\"EUR\":\"EUR\",\"GBP\":\"GBP\",\"HKD\":\"HKD\",\"INR\":\"INR\",\"JPY\":\"JPY\",\"MXN\":\"MXN\",\"MYR\":\"MYR\",\"NOK\":\"NOK\",\"NZD\":\"NZD\",\"PLN\":\"PLN\",\"SEK\":\"SEK\",\"SGD\":\"SGD\"}',0,'{\"webhook\":{\"title\": \"Webhook Endpoint\",\"value\":\"ipn.StripeV3\"}}',NULL,NULL,'2019-09-14 13:14:22','2021-05-21 00:58:38'),(27,0,115,'Mollie','Mollie',1,0,'{\"mollie_email\":{\"title\":\"Mollie Email \",\"global\":true,\"value\":\"vi@gmail.com\"},\"api_key\":{\"title\":\"API KEY\",\"global\":true,\"value\":\"test_cucfwKTWfft9s337qsVfn5CC4vNkrn\"}}','{\"AED\":\"AED\",\"AUD\":\"AUD\",\"BGN\":\"BGN\",\"BRL\":\"BRL\",\"CAD\":\"CAD\",\"CHF\":\"CHF\",\"CZK\":\"CZK\",\"DKK\":\"DKK\",\"EUR\":\"EUR\",\"GBP\":\"GBP\",\"HKD\":\"HKD\",\"HRK\":\"HRK\",\"HUF\":\"HUF\",\"ILS\":\"ILS\",\"ISK\":\"ISK\",\"JPY\":\"JPY\",\"MXN\":\"MXN\",\"MYR\":\"MYR\",\"NOK\":\"NOK\",\"NZD\":\"NZD\",\"PHP\":\"PHP\",\"PLN\":\"PLN\",\"RON\":\"RON\",\"RUB\":\"RUB\",\"SEK\":\"SEK\",\"SGD\":\"SGD\",\"THB\":\"THB\",\"TWD\":\"TWD\",\"USD\":\"USD\",\"ZAR\":\"ZAR\"}',0,NULL,NULL,NULL,'2019-09-14 13:14:22','2021-05-21 02:44:45'),(30,0,116,'Cashmaal','Cashmaal',1,0,'{\"web_id\":{\"title\":\"Web Id\",\"global\":true,\"value\":\"3748\"},\"ipn_key\":{\"title\":\"IPN Key\",\"global\":true,\"value\":\"546254628759524554647987\"}}','{\"PKR\":\"PKR\",\"USD\":\"USD\"}',0,'{\"webhook\":{\"title\": \"IPN URL\",\"value\":\"ipn.Cashmaal\"}}',NULL,NULL,NULL,'2021-06-22 08:05:04'),(36,0,119,'Mercado Pago','MercadoPago',1,0,'{\"access_token\":{\"title\":\"Access Token\",\"global\":true,\"value\":\"APP_USR-7924565816849832-082312-21941521997fab717db925cf1ea2c190-1071840315\"}}','{\"USD\":\"USD\",\"CAD\":\"CAD\",\"CHF\":\"CHF\",\"DKK\":\"DKK\",\"EUR\":\"EUR\",\"GBP\":\"GBP\",\"NOK\":\"NOK\",\"PLN\":\"PLN\",\"SEK\":\"SEK\",\"AUD\":\"AUD\",\"NZD\":\"NZD\"}',0,NULL,NULL,NULL,NULL,'2022-09-14 07:41:14'),(37,0,120,'Authorize.net','Authorize',1,0,'{\"login_id\":{\"title\":\"Login ID\",\"global\":true,\"value\":\"59e4P9DBcZv\"},\"transaction_key\":{\"title\":\"Transaction Key\",\"global\":true,\"value\":\"47x47TJyLw2E7DbR\"}}','{\"USD\":\"USD\",\"CAD\":\"CAD\",\"CHF\":\"CHF\",\"DKK\":\"DKK\",\"EUR\":\"EUR\",\"GBP\":\"GBP\",\"NOK\":\"NOK\",\"PLN\":\"PLN\",\"SEK\":\"SEK\",\"AUD\":\"AUD\",\"NZD\":\"NZD\"}',0,NULL,NULL,NULL,NULL,'2025-10-11 13:18:25'),(46,0,121,'NMI','NMI',1,0,'{\"api_key\":{\"title\":\"API Key\",\"global\":true,\"value\":\"2F822Rw39fx762MaV7Yy86jXGTC7sCDy\"}}','{\"AED\":\"AED\",\"ARS\":\"ARS\",\"AUD\":\"AUD\",\"BOB\":\"BOB\",\"BRL\":\"BRL\",\"CAD\":\"CAD\",\"CHF\":\"CHF\",\"CLP\":\"CLP\",\"CNY\":\"CNY\",\"COP\":\"COP\",\"DKK\":\"DKK\",\"EUR\":\"EUR\",\"GBP\":\"GBP\",\"HKD\":\"HKD\",\"IDR\":\"IDR\",\"ILS\":\"ILS\",\"INR\":\"INR\",\"JPY\":\"JPY\",\"KRW\":\"KRW\",\"MXN\":\"MXN\",\"MYR\":\"MYR\",\"NOK\":\"NOK\",\"NZD\":\"NZD\",\"PEN\":\"PEN\",\"PHP\":\"PHP\",\"PLN\":\"PLN\",\"PYG\":\"PYG\",\"RUB\":\"RUB\",\"SEC\":\"SEC\",\"SGD\":\"SGD\",\"THB\":\"THB\",\"TRY\":\"TRY\",\"TWD\":\"TWD\",\"USD\":\"USD\",\"ZAR\":\"ZAR\"}',0,NULL,NULL,NULL,NULL,'2022-08-28 10:32:31'),(50,0,507,'BTCPay','BTCPay',1,0,'{\"store_id\":{\"title\":\"Store Id\",\"global\":true,\"value\":\"HsqFVTXSeUFJu7caoYZc3CTnP8g5LErVdHhEXPVTheHf\"},\"api_key\":{\"title\":\"Api Key\",\"global\":true,\"value\":\"4436bd706f99efae69305e7c4eff4780de1335ce\"},\"server_name\":{\"title\":\"Server Name\",\"global\":true,\"value\":\"https:\\/\\/testnet.demo.btcpayserver.org\"},\"secret_code\":{\"title\":\"Secret Code\",\"global\":true,\"value\":\"SUCdqPn9CDkY7RmJHfpQVHP2Lf2\"}}','{\"BTC\":\"Bitcoin\",\"LTC\":\"Litecoin\"}',1,'{\"webhook\":{\"title\": \"IPN URL\",\"value\":\"ipn.BTCPay\"}}',NULL,NULL,NULL,'2023-02-14 04:42:09'),(51,0,508,'Now payments hosted','NowPaymentsHosted',1,0,'{\"api_key\":{\"title\":\"API Key\",\"global\":true,\"value\":\"--------\"},\"secret_key\":{\"title\":\"Secret Key\",\"global\":true,\"value\":\"------------\"}}','{\"BTG\":\"BTG\",\"ETH\":\"ETH\",\"XMR\":\"XMR\",\"ZEC\":\"ZEC\",\"XVG\":\"XVG\",\"ADA\":\"ADA\",\"LTC\":\"LTC\",\"BCH\":\"BCH\",\"QTUM\":\"QTUM\",\"DASH\":\"DASH\",\"XLM\":\"XLM\",\"XRP\":\"XRP\",\"XEM\":\"XEM\",\"DGB\":\"DGB\",\"LSK\":\"LSK\",\"DOGE\":\"DOGE\",\"TRX\":\"TRX\",\"KMD\":\"KMD\",\"REP\":\"REP\",\"BAT\":\"BAT\",\"ARK\":\"ARK\",\"WAVES\":\"WAVES\",\"BNB\":\"BNB\",\"XZC\":\"XZC\",\"NANO\":\"NANO\",\"TUSD\":\"TUSD\",\"VET\":\"VET\",\"ZEN\":\"ZEN\",\"GRS\":\"GRS\",\"FUN\":\"FUN\",\"NEO\":\"NEO\",\"GAS\":\"GAS\",\"PAX\":\"PAX\",\"USDC\":\"USDC\",\"ONT\":\"ONT\",\"XTZ\":\"XTZ\",\"LINK\":\"LINK\",\"RVN\":\"RVN\",\"BNBMAINNET\":\"BNBMAINNET\",\"ZIL\":\"ZIL\",\"BCD\":\"BCD\",\"USDT\":\"USDT\",\"USDTERC20\":\"USDTERC20\",\"CRO\":\"CRO\",\"DAI\":\"DAI\",\"HT\":\"HT\",\"WABI\":\"WABI\",\"BUSD\":\"BUSD\",\"ALGO\":\"ALGO\",\"USDTTRC20\":\"USDTTRC20\",\"GT\":\"GT\",\"STPT\":\"STPT\",\"AVA\":\"AVA\",\"SXP\":\"SXP\",\"UNI\":\"UNI\",\"OKB\":\"OKB\",\"BTC\":\"BTC\"}',1,'',NULL,NULL,NULL,'2023-02-14 05:08:23'),(65,0,122,'TwoCheckout','TwoCheckout',1,0,'{\"merchant_code\":{\"title\":\"Merchant Code\",\"global\":true,\"value\":\"---------\"},\"secret_key\":{\"title\":\"Secret Key\",\"global\":true,\"value\":\"--------\"}}','{\"AFN\": \"AFN\",\"ALL\": \"ALL\",\"DZD\": \"DZD\",\"ARS\": \"ARS\",\"AUD\": \"AUD\",\"AZN\": \"AZN\",\"BSD\": \"BSD\",\"BDT\": \"BDT\",\"BBD\": \"BBD\",\"BZD\": \"BZD\",\"BMD\": \"BMD\",\"BOB\": \"BOB\",\"BWP\": \"BWP\",\"BRL\": \"BRL\",\"GBP\": \"GBP\",\"BND\": \"BND\",\"BGN\": \"BGN\",\"CAD\": \"CAD\",\"CLP\": \"CLP\",\"CNY\": \"CNY\",\"COP\": \"COP\",\"CRC\": \"CRC\",\"HRK\": \"HRK\",\"CZK\": \"CZK\",\"DKK\": \"DKK\",\"DOP\": \"DOP\",\"XCD\": \"XCD\",\"EGP\": \"EGP\",\"EUR\": \"EUR\",\"FJD\": \"FJD\",\"GTQ\": \"GTQ\",\"HKD\": \"HKD\",\"HNL\": \"HNL\",\"HUF\": \"HUF\",\"INR\": \"INR\",\"IDR\": \"IDR\",\"ILS\": \"ILS\",\"JMD\": \"JMD\",\"JPY\": \"JPY\",\"KZT\": \"KZT\",\"KES\": \"KES\",\"LAK\": \"LAK\",\"MMK\": \"MMK\",\"LBP\": \"LBP\",\"LRD\": \"LRD\",\"MOP\": \"MOP\",\"MYR\": \"MYR\",\"MVR\": \"MVR\",\"MRO\": \"MRO\",\"MUR\": \"MUR\",\"MXN\": \"MXN\",\"MAD\": \"MAD\",\"NPR\": \"NPR\",\"TWD\": \"TWD\",\"NZD\": \"NZD\",\"NIO\": \"NIO\",\"NOK\": \"NOK\",\"PKR\": \"PKR\",\"PGK\": \"PGK\",\"PEN\": \"PEN\",\"PHP\": \"PHP\",\"PLN\": \"PLN\",\"QAR\": \"QAR\",\"RON\": \"RON\",\"RUB\": \"RUB\",\"WST\": \"WST\",\"SAR\": \"SAR\",\"SCR\": \"SCR\",\"SGD\": \"SGD\",\"SBD\": \"SBD\",\"ZAR\": \"ZAR\",\"KRW\": \"KRW\",\"LKR\": \"LKR\",\"SEK\": \"SEK\",\"CHF\": \"CHF\",\"SYP\": \"SYP\",\"THB\": \"THB\",\"TOP\": \"TOP\",\"TTD\": \"TTD\",\"TRY\": \"TRY\",\"UAH\": \"UAH\",\"AED\": \"AED\",\"USD\": \"USD\",\"VUV\": \"VUV\",\"VND\": \"VND\",\"XOF\": \"XOF\",\"YER\": \"YER\"}',1,'{\"approved_url\":{\"title\": \"Approved URL\",\"value\":\"ipn.TwoCheckout\"}}',NULL,NULL,NULL,'2023-05-25 04:17:21'),(66,0,123,'Checkout','Checkout',1,0,'{\"secret_key\":{\"title\":\"Secret Key\",\"global\":true,\"value\":\"sk_f7f9a069-dcc5-45d8-aa72-e60f605c9514\"},\"public_key\":{\"title\":\"PUBLIC KEY\",\"global\":true,\"value\":\"pk_66e19b3f-a431-44ff-823f-d773d960f6b9\"},\"processing_channel_id\":{\"title\":\"PROCESSING CHANNEL\",\"global\":true,\"value\":\"---\"}}','{\"USD\":\"USD\",\"EUR\":\"EUR\",\"GBP\":\"GBP\",\"HKD\":\"HKD\",\"AUD\":\"AUD\",\"CAN\":\"CAN\",\"CHF\":\"CHF\",\"SGD\":\"SGD\",\"JPY\":\"JPY\",\"NZD\":\"NZD\"}',0,NULL,NULL,NULL,NULL,NULL),(67,1,1000,'WinTerSMM','wintersmm',1,0,'[]','[]',0,NULL,'<br>',NULL,'2025-10-10 12:27:26','2025-10-10 12:27:26'),(69,0,903,'Aamarpay','Aamarpay',1,0,'{\"store_id\":{\"title\":\"Store ID\",\"global\":true,\"value\":\"\"},\"signature_key\":{\"title\":\"Signature Key\",\"global\":true,\"value\":\"\"},\"sandbox\":{\"title\":\"Sandbox Mode\",\"global\":true,\"value\":\"1\"},\"callback_url\":{\"title\":\"Callback URL\",\"global\":true,\"value\":\"\"}}','{\"BDT\":\"├ô┬║Γöé\"}',0,NULL,'Aamarpay Payment Gateway (Working Implementation)',NULL,'2025-10-13 18:33:31','2026-03-19 11:09:14'),(70,0,904,'Nagad','Nagad',1,0,'{\"merchant_id\":{\"title\":\"Merchant ID\",\"global\":true,\"value\":\"\"},\"merchant_number\":{\"title\":\"Merchant Number\",\"global\":true,\"value\":\"\"},\"private_key\":{\"title\":\"Private Key\",\"global\":true,\"value\":\"\"},\"public_key\":{\"title\":\"Public Key\",\"global\":true,\"value\":\"\"},\"sandbox\":{\"title\":\"Sandbox Mode\",\"global\":true,\"value\":\"1\"},\"callback_url\":{\"title\":\"Callback URL\",\"global\":true,\"value\":\"\"}}','{\"BDT\":\"├ô┬║Γöé\"}',0,NULL,'Nagad Payment Gateway (Working Implementation)',NULL,'2025-10-13 18:33:31','2026-03-19 11:09:14'),(1005,0,902,'bKash','Bkash',1,0,'{\"sandbox\":{\"title\":\"Sandbox Mode\",\"global\":true,\"value\":\"1\"},\"app_key\":{\"title\":\"App Key\",\"global\":true,\"value\":\"\"},\"app_secret\":{\"title\":\"App Secret\",\"global\":true,\"value\":\"\"},\"username\":{\"title\":\"Username\",\"global\":true,\"value\":\"\"},\"password\":{\"title\":\"Password\",\"global\":true,\"value\":\"\"},\"callback_url\":{\"title\":\"Callback URL\",\"global\":true,\"value\":\"\"}}','{\"BDT\":\"├ô┬║Γöé\"}',0,NULL,'bKash Tokenized Checkout (Working Implementation)',NULL,'2025-10-14 20:27:58','2026-03-19 11:09:14'),(1006,0,906,'TZSMMPAY','TZSMMPAY',1,0,'{\"api_key\":{\"title\":\"API Key\",\"global\":true,\"value\":\"xB4mEUSZVic4qloJuIzZJzusqZIOOuOodwgVIPwhrc6IIHwcDj\"},\"create_url\":{\"title\":\"Create Payment URL\",\"global\":true,\"value\":\"https:\\/\\/cdn.wintersmm.com\\/api\\/payment\\/create\"}}','{\"USD\":\"$\",\"BDT\":\"αº│\",\"EUR\":\"Γé¼\",\"GBP\":\"┬ú\"}',0,'{\"callback\":{\"title\":\"Callback Route\",\"value\":\"ipn.TZSMMPAY\"}}','TZSMMPAY Payment Gateway (Configurable from Admin Panel)',NULL,'2025-01-15 06:00:00','2025-10-16 00:15:07'),(1007,0,907,'WINTERSMM','WINTERSMM',1,0,'{\"brand_key\":{\"title\":\"Brand Key\",\"global\":true,\"value\":\"gZ3Oheox6TJSMmOwsO4RRmjem3zS0LJ5htd5YTaqBFOjiIJJJG\"},\"create_url\":{\"title\":\"Create Payment URL\",\"global\":true,\"value\":\"https:\\/\\/cdn.wintersmm.com\\/api\\/payment\\/create\"},\"verify_url\":{\"title\":\"Verify Payment URL\",\"global\":true,\"value\":\"https:\\/\\/cdn.wintersmm.com\\/api\\/payment\\/verify\"}}','{\"USD\":\"$\",\"BDT\":\"αº│\",\"EUR\":\"Γé¼\",\"GBP\":\"┬ú\"}',0,'{\"callback\":{\"title\":\"Callback Route\",\"value\":\"ipn.WINTERSMM\"}}','WintersMM Payment Gateway',NULL,'2025-01-15 06:00:00','2025-10-16 00:44:56');
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
INSERT INTO `general_settings` VALUES (1,NULL,NULL,1,NULL,0,NULL,0,NULL,60,1,0,30,'demo','logo_3de2c648dd37e0df8556cadc54ce3b40.png','logo_dark_cf3c4ed958148b7978b7f8fa0062cafc.png','favicon_f378144363400391799a5e4c69077667.png',NULL,NULL,NULL,NULL,NULL,NULL,0,'none','none','normal',1.00,200,60,35,'BDT',0.00000000,2,NULL,'αº│','info@viserlab.com','<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\">\r\n  <!--[if !mso]><!-->\r\n  <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\">\r\n  <!--<![endif]-->\r\n  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\r\n  <title></title>\r\n  <style type=\"text/css\">\r\n.ReadMsgBody { width: 100%; background-color: #ffffff; }\r\n.ExternalClass { width: 100%; background-color: #ffffff; }\r\n.ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td, .ExternalClass div { line-height: 100%; }\r\nhtml { width: 100%; }\r\nbody { -webkit-text-size-adjust: none; -ms-text-size-adjust: none; margin: 0; padding: 0; }\r\ntable { border-spacing: 0; table-layout: fixed; margin: 0 auto;border-collapse: collapse; }\r\ntable table table { table-layout: auto; }\r\n.yshortcuts a { border-bottom: none !important; }\r\nimg:hover { opacity: 0.9 !important; }\r\na { color: #0087ff; text-decoration: none; }\r\n.textbutton a { font-family: \'open sans\', arial, sans-serif !important;}\r\n.btn-link a { color:#FFFFFF !important;}\r\n\r\n@media only screen and (max-width: 480px) {\r\nbody { width: auto !important; }\r\n*[class=\"table-inner\"] { width: 90% !important; text-align: center !important; }\r\n*[class=\"table-full\"] { width: 100% !important; text-align: center !important; }\r\n/* image */\r\nimg[class=\"img1\"] { width: 100% !important; height: auto !important; }\r\n}\r\n</style>\r\n\r\n\r\n\r\n  <table bgcolor=\"#414a51\" width=\"100%\" border=\"0\" align=\"center\" cellpadding=\"0\" cellspacing=\"0\">\r\n    <tbody><tr>\r\n      <td height=\"50\"></td>\r\n    </tr>\r\n    <tr>\r\n      <td align=\"center\" style=\"text-align:center;vertical-align:top;font-size:0;\">\r\n        <table align=\"center\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">\r\n          <tbody><tr>\r\n            <td align=\"center\" width=\"600\">\r\n              <!--header-->\r\n              <table class=\"table-inner\" width=\"95%\" border=\"0\" align=\"center\" cellpadding=\"0\" cellspacing=\"0\">\r\n                <tbody><tr>\r\n                  <td bgcolor=\"#0087ff\" style=\"border-top-left-radius:6px; border-top-right-radius:6px;text-align:center;vertical-align:top;font-size:0;\" align=\"center\">\r\n                    <table width=\"90%\" border=\"0\" align=\"center\" cellpadding=\"0\" cellspacing=\"0\">\r\n                      <tbody><tr>\r\n                        <td height=\"20\"></td>\r\n                      </tr>\r\n                      <tr>\r\n                        <td align=\"center\" style=\"font-family: \'Open sans\', Arial, sans-serif; color:#FFFFFF; font-size:16px; font-weight: bold;\">This is a System Generated Email</td>\r\n                      </tr>\r\n                      <tr>\r\n                        <td height=\"20\"></td>\r\n                      </tr>\r\n                    </tbody></table>\r\n                  </td>\r\n                </tr>\r\n              </tbody></table>\r\n              <!--end header-->\r\n              <table class=\"table-inner\" width=\"95%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\r\n                <tbody><tr>\r\n                  <td bgcolor=\"#FFFFFF\" align=\"center\" style=\"text-align:center;vertical-align:top;font-size:0;\">\r\n                    <table align=\"center\" width=\"90%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\r\n                      <tbody><tr>\r\n                        <td height=\"35\"></td>\r\n                      </tr>\r\n                      <!--logo-->\r\n                      <tr>\r\n                        <td align=\"center\" style=\"vertical-align:top;font-size:0;\">\r\n                          <a href=\"#\">\r\n                            <img style=\"display:block; line-height:0px; font-size:0px; border:0px;\" src=\"https://i.imgur.com/Z1qtvtV.png\" alt=\"img\">\r\n                          </a>\r\n                        </td>\r\n                      </tr>\r\n                      <!--end logo-->\r\n                      <tr>\r\n                        <td height=\"40\"></td>\r\n                      </tr>\r\n                      <!--headline-->\r\n                      <tr>\r\n                        <td align=\"center\" style=\"font-family: \'Open Sans\', Arial, sans-serif; font-size: 22px;color:#414a51;font-weight: bold;\">Hello {{fullname}} ({{username}})</td>\r\n                      </tr>\r\n                      <!--end headline-->\r\n                      <tr>\r\n                        <td align=\"center\" style=\"text-align:center;vertical-align:top;font-size:0;\">\r\n                          <table width=\"40\" border=\"0\" align=\"center\" cellpadding=\"0\" cellspacing=\"0\">\r\n                            <tbody><tr>\r\n                              <td height=\"20\" style=\" border-bottom:3px solid #0087ff;\"></td>\r\n                            </tr>\r\n                          </tbody></table>\r\n                        </td>\r\n                      </tr>\r\n                      <tr>\r\n                        <td height=\"20\"></td>\r\n                      </tr>\r\n                      <!--content-->\r\n                      <tr>\r\n                        <td align=\"left\" style=\"font-family: \'Open sans\', Arial, sans-serif; color:#7f8c8d; font-size:16px; line-height: 28px;\">{{message}}</td>\r\n                      </tr>\r\n                      <!--end content-->\r\n                      <tr>\r\n                        <td height=\"40\"></td>\r\n                      </tr>\r\n              \r\n                    </tbody></table>\r\n                  </td>\r\n                </tr>\r\n                <tr>\r\n                  <td height=\"45\" align=\"center\" bgcolor=\"#f4f4f4\" style=\"border-bottom-left-radius:6px;border-bottom-right-radius:6px;\">\r\n                    <table align=\"center\" width=\"90%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\r\n                      <tbody><tr>\r\n                        <td height=\"10\"></td>\r\n                      </tr>\r\n                      <!--preference-->\r\n                      <tr>\r\n                        <td class=\"preference-link\" align=\"center\" style=\"font-family: \'Open sans\', Arial, sans-serif; color:#95a5a6; font-size:14px;\">\r\n                          ├é┬⌐ 2021 <a href=\"#\">{{site_name}}</a>&nbsp;. All Rights Reserved. \r\n                        </td>\r\n                      </tr>\r\n                      <!--end preference-->\r\n                      <tr>\r\n                        <td height=\"10\"></td>\r\n                      </tr>\r\n                    </tbody></table>\r\n                  </td>\r\n                </tr>\r\n              </tbody></table>\r\n            </td>\r\n          </tr>\r\n        </tbody></table>\r\n      </td>\r\n    </tr>\r\n    <tr>\r\n      <td height=\"60\"></td>\r\n    </tr>\r\n  </tbody></table>','hi {{fullname}} ({{username}}), {{message}}','ViserAdmin','3e8804','060662','{\"name\":\"php\"}','{\"name\":\"nexmo\",\"clickatell\":{\"api_key\":\"----------------\"},\"infobip\":{\"username\":\"------------8888888\",\"password\":\"-----------------\"},\"message_bird\":{\"api_key\":\"-------------------\"},\"nexmo\":{\"api_key\":\"----------------------\",\"api_secret\":\"----------------------\"},\"sms_broadcast\":{\"username\":\"----------------------\",\"password\":\"-----------------------------\"},\"twilio\":{\"account_sid\":\"-----------------------\",\"auth_token\":\"---------------------------\",\"from\":\"----------------------\"},\"text_magic\":{\"username\":\"-----------------------\",\"apiv2_key\":\"-------------------------------\"},\"custom\":{\"method\":\"get\",\"url\":\"https:\\/\\/hostname\\/demo-api-v1\",\"headers\":{\"name\":[\"api_key\"],\"value\":[\"test_api 555\"]},\"body\":{\"name\":[\"from_number\"],\"value\":[\"5657545757\"]}}}','{\n    \"site_name\":\"Name of your site\",\n    \"site_currency\":\"Currency of your site\",\n    \"currency_symbol\":\"Symbol of currency\"\n}',0,0,1,0,0,0,0,0,1,1,1,1,1,1,1,'basic','[]',NULL,'2026-03-19 04:38:33',1.00,0,0,NULL,NULL,NULL,NULL,'#ffffff','#1f2937','#374151','#f59e0b','#dc2626');
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
  `image` varchar(512) NOT NULL,
  `link_url` varchar(512) DEFAULT NULL,
  `open_new_tab` tinyint(1) NOT NULL DEFAULT 1,
  `frame_style` varchar(32) NOT NULL DEFAULT 'thin',
  `width_mode` varchar(16) NOT NULL DEFAULT 'full',
  `max_height_px` smallint(5) unsigned DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(10) unsigned NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `homepage_ad_slots`
--

LOCK TABLES `homepage_ad_slots` WRITE;
/*!40000 ALTER TABLE `homepage_ad_slots` DISABLE KEYS */;
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
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `homepage_custom_product_rows_is_active_sort_order_index` (`is_active`,`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `homepage_custom_product_rows`
--

LOCK TABLES `homepage_custom_product_rows` WRITE;
/*!40000 ALTER TABLE `homepage_custom_product_rows` DISABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=178 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'2014_10_12_000000_create_users_table',1),(2,'2014_10_12_100000_create_password_resets_table',1),(3,'2019_08_19_000000_create_failed_jobs_table',1),(4,'2019_12_14_000001_create_personal_access_tokens_table',1),(5,'2022_02_26_061836_create_forms_table',1),(6,'2023_02_22_095338_create_user_tokens_table',1),(7,'2023_02_22_101032_create_tokens_table',1),(8,'2023_02_23_144521_create_brands_table',1),(9,'2023_02_23_162048_create_categories_table',1),(10,'2023_02_25_092916_create_subcategories_table',1),(11,'2023_02_25_104148_create_coupons_table',1),(12,'2023_02_25_134428_create_products_table',1),(13,'2023_02_25_140858_create_product_galleries_table',1),(14,'2023_02_26_140953_create_reviews_table',1),(15,'2023_02_26_160717_create_orders_table',1),(16,'2023_02_27_094248_create_wishlists_table',1),(17,'2023_02_27_121428_create_carts_table',1),(18,'2023_02_27_135749_create_shipping_methods_table',1),(19,'2023_02_28_132511_create_order_details_table',1),(20,'2024_01_01_000000_create_courierapis_table',1),(21,'2025_01_31_000000_add_logo_effect_columns_to_general_settings',1),(22,'2025_02_04_000000_create_admin_activity_logs_table',1),(23,'2025_02_04_000001_add_channel_to_support_tickets_table',1),(24,'2025_02_04_100000_create_banner_analytics_table',1),(25,'2025_02_04_100001_create_conversations_table',1),(26,'2025_02_04_100002_create_omnichannel_messages_table',1),(27,'2025_02_04_100003_create_message_channels_table',1),(28,'2025_02_04_100004_create_message_templates_table',1),(29,'2025_02_04_100005_create_auto_responses_table',1),(30,'2025_02_04_100006_create_chat_assignments_table',1),(31,'2025_02_04_100007_create_internal_notes_table',1),(32,'2025_02_04_100008_create_message_status_logs_table',1),(33,'2025_02_04_100009_create_omnichannel_message_attachments_table',1),(34,'2025_02_05_000000_add_age_to_users_table',1),(35,'2025_02_05_100000_add_floating_auth_to_general_settings',1),(36,'2025_10_16_000000_add_social_provider_columns_to_users_table',1),(37,'2026_02_05_191214_add_loyalty_features_to_tables',1),(38,'2026_02_05_200022_create_loyalty_transactions_table',1),(39,'2026_02_05_201755_create_product_comparisons_table',1),(40,'2026_02_08_193500_create_product_variants_system',1),(41,'2026_02_09_000001_add_admin_online_to_general_settings',1),(42,'2026_02_09_100000_add_product_video_and_user_gender',1),(43,'2026_02_11_000000_add_click_url_to_notification_logs_table',1),(44,'2026_02_11_000001_create_contact_channel_integrations_table',1),(45,'2026_02_11_000002_create_contact_channel_messages_table',1),(46,'2026_02_11_000003_add_contact_handles_to_users_table',1),(47,'2026_02_11_000004_add_channel_reference_to_support_tickets_table',1),(48,'2026_02_11_100000_add_keywords_and_name_to_auto_responses',1),(49,'2026_02_11_110000_add_is_public_to_auto_responses',1),(50,'2026_02_11_120000_create_admin_reports_table',1),(51,'2026_02_12_100000_add_publish_status_and_scheduled_at_to_categories_table',1),(52,'2026_02_13_100000_create_homepage_top_features_table',1),(53,'2026_02_14_100000_create_autopay_messages_table',1),(54,'2026_02_15_100000_enforce_single_spotlight_per_product',1),(55,'2026_02_16_100000_add_role_to_admins_table',1),(56,'2026_02_16_120000_add_mobile_to_admins_table',1),(57,'2026_02_16_140000_add_allowed_sections_to_admins_table',1),(58,'2026_02_16_160000_create_deposits_table',1),(59,'2026_02_16_170000_add_deposits_created_at_index',1),(60,'2026_02_16_180000_create_courier_logs_table',1),(61,'2026_02_16_200000_create_cache_table',1),(62,'2026_02_16_200001_create_sessions_table',1),(63,'2026_02_16_210000_create_shipping_zones_table',1),(64,'2026_02_16_210001_create_shipping_zone_countries_table',1),(65,'2026_02_16_210002_create_shipping_zone_areas_table',1),(66,'2026_02_16_210003_add_zone_fields_to_shipping_methods_table',1),(67,'2026_02_16_210004_create_shipping_rules_table',1),(68,'2026_02_16_210005_add_shipping_zone_to_orders_table',1),(69,'2026_02_18_100000_add_product_performance_indexes',1),(70,'2026_02_19_100000_add_product_price_and_slug_indexes',1),(71,'2026_02_19_120000_add_coupon_advanced_fields',1),(72,'2026_02_19_150000_add_key_features_to_products',1),(73,'2026_02_20_100000_add_extensible_fields_to_courierapis_table',1),(74,'2026_02_20_100000_ensure_shipping_zones_table',1),(75,'2026_02_20_100001_create_order_shipment_trackings_table',1),(76,'2026_02_20_120000_add_logo_to_gateways_table',1),(77,'2026_02_21_000001_add_invoice_settings_to_general_settings',1),(78,'2026_02_21_100000_add_show_to_user_and_region_to_courierapis',1),(79,'2026_02_21_100000_ensure_all_shipping_tables',1),(80,'2026_02_21_110000_create_popup_ads_table',2),(81,'2026_02_21_115000_add_position_to_popup_ads_table',2),(82,'2026_02_21_120000_add_display_type_to_popup_ads_table',2),(83,'2026_02_21_120000_add_return_status_to_courier_logs',2),(84,'2026_02_21_120000_add_version_fields_to_extensions_table',2),(85,'2026_02_21_130000_add_force_password_change_to_admins_table',2),(86,'2026_02_21_140000_create_cache_clear_logs_table',2),(87,'2026_02_21_150000_create_seeder_audit_logs_table',2),(88,'2026_02_21_160000_create_permissions_tables',2),(89,'2026_02_21_180000_create_user_search_logs_table',2),(90,'2026_02_21_200000_create_offer_timers_table',2),(91,'2026_02_21_200000_create_security_settings_table',2),(92,'2026_02_21_200001_create_security_audit_logs_table',2),(93,'2026_02_21_210000_add_size_to_offer_timers',2),(94,'2026_02_22_100000_add_last_chat_seen_at_to_users',2),(95,'2026_02_22_100000_add_shipping_zone_advanced_fields',2),(96,'2026_02_22_100000_create_divisions_table',2),(97,'2026_02_22_100001_create_districts_table',2),(98,'2026_02_22_100002_create_thanas_table',2),(99,'2026_02_22_110000_add_two_factor_to_admins_table',2),(100,'2026_02_22_120000_add_status_to_divisions_districts_thanas',2),(101,'2026_02_22_120000_create_payment_events_table',2),(102,'2026_02_22_120000_fix_courier_daily_stats_view',2),(103,'2026_02_22_120001_create_delivery_zones_table',2),(104,'2026_02_22_120002_create_user_saved_addresses_table',2),(105,'2026_02_22_120003_add_location_tracking_to_orders_table',2),(106,'2026_02_22_130000_create_security_events_table',2),(107,'2026_02_22_130000_fix_courier_trigger_definer_for_hosting',2),(108,'2026_02_22_140000_add_indexes_to_reviews_table',2),(109,'2026_02_22_140000_create_admin_sessions_table',2),(110,'2026_02_22_200000_add_admin_notes_to_admins_table',2),(111,'2026_02_22_200001_add_delivery_scan_to_orders_table',2),(112,'2026_02_22_210001_add_invoice_qr_and_driver_scan',2),(113,'2026_02_22_210002_add_delivery_scanned_notification_template',2),(114,'2026_02_22_220001_update_delivery_scanned_template_add_map_link',2),(115,'2026_02_23_100000_add_idempotency_to_payment_events',2),(116,'2026_02_23_100000_advanced_reviews_table',2),(117,'2026_02_23_110000_create_admin_ip_whitelist_table',2),(118,'2026_02_23_120000_create_admin_lockouts_table',2),(119,'2026_02_23_120000_create_product_questions_table',3),(120,'2026_02_23_130000_create_audit_logs_table',3),(121,'2026_02_24_000000_add_is_approved_to_reviews_if_missing',3),(122,'2026_02_24_100000_add_notification_logo_to_general_settings',3),(123,'2026_02_24_100000_add_orders_user_id_index',3),(124,'2026_02_24_100000_add_updated_at_to_user_search_logs',3),(125,'2026_02_24_100000_create_payment_ledger_table',3),(126,'2026_02_24_110000_create_trusted_admin_devices_table',3),(127,'2026_02_24_120000_add_image_path_to_user_search_logs',3),(128,'2026_02_24_140000_add_is_private_to_reviews',3),(129,'2026_02_24_160000_add_read_at_to_notification_logs',3),(130,'2026_02_25_100000_create_user_activity_logs_table',3),(131,'2026_02_26_100000_activity_indexes_and_fraud',3),(132,'2026_02_27_100000_create_cod_settings_table',3),(133,'2026_02_27_100001_add_cod_columns_to_orders_and_zones',3),(134,'2026_02_27_100002_create_cod_blacklists_table',3),(135,'2026_02_27_100003_create_cod_otp_verifications_table',3),(136,'2026_02_27_100004_add_cod_order_status_and_user_risk',3),(137,'2026_02_28_100000_create_payment_transactions_table',3),(138,'2026_02_28_100001_add_sort_order_to_gateways',3),(139,'2026_02_28_100002_create_payment_refunds_table',3),(140,'2026_02_28_100003_create_payment_fraud_attempts_table',3),(141,'2026_02_28_120000_ensure_users_table_has_auth_columns',4),(142,'2026_03_02_100000_add_username_editable_to_users_table',4),(143,'2026_03_02_150000_add_first_order_flag_to_coupons',4),(144,'2026_03_02_160000_add_ad_source_utm_to_orders',4),(145,'2026_03_02_160001_add_meta_pixel_to_general_settings',4),(146,'2026_03_03_100000_add_google_tiktok_tracking_to_general_settings',4),(147,'2026_03_03_100001_add_advance_payment_and_staff_notes_to_orders',4),(148,'2026_03_03_100002_create_fraud_guard_tables',4),(149,'2026_03_03_100003_add_tracking_link_to_order_shipment_trackings',4),(150,'2026_03_03_100004_create_revenue_expenses_table',4),(151,'2026_03_03_120000_ensure_tracking_columns_on_general_settings',4),(152,'2026_03_03_170000_add_model_columns_to_admin_activity_logs',4),(153,'2026_03_09_100000_add_general_product_and_clothing_fields',4),(154,'2026_03_09_100000_create_abandoned_carts_table',4),(155,'2026_03_09_100001_add_abandoned_cart_settings_to_general_settings',4),(156,'2026_03_09_100002_add_abandoned_cart_notification_template',4),(157,'2026_03_10_100000_add_trending_now_to_products',4),(158,'2026_03_10_120000_add_indexes_to_products_table',4),(159,'2026_03_11_100000_add_ui_settings_to_general_settings',4),(160,'2026_03_11_120000_create_ui_settings_table',4),(161,'2026_03_11_140000_add_product_card_color_fields_to_ui_settings',4),(162,'2026_03_12_100000_add_delivery_type_and_charge_to_products',4),(163,'2026_03_12_140000_add_composite_indexes_products_for_homepage',4),(164,'2026_03_13_100000_add_display_view_count_to_general_settings',4),(165,'2026_03_13_120000_add_guest_order_fields_to_orders_table',4),(166,'2026_03_13_150000_add_stock_order_messages_to_general_settings',4),(167,'2026_03_13_160000_add_restock_whatsapp_telegram_to_general_settings',4),(168,'2026_03_13_170000_add_home_line_and_order_to_categories_table',4),(169,'2026_03_13_170000_add_homepage_indexes_to_products_table',4),(170,'2026_03_14_100000_ensure_carts_wishlists_columns',4),(171,'2026_03_16_100000_add_order_source_to_orders_table',4),(172,'2026_03_17_100000_add_quick_order_fields_to_general_settings',4),(173,'2026_03_18_100000_backfill_product_slugs_for_clean_urls',4),(174,'2026_03_18_200000_create_homepage_custom_product_rows_table',4),(175,'2026_03_18_210000_add_homepage_section_override_to_products_table',4),(176,'2026_03_19_120000_create_homepage_ad_slots_table',4),(177,'2026_03_19_120000_regenerate_short_keyword_id_product_slugs',4);
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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notification_logs`
--

LOCK TABLES `notification_logs` WRITE;
/*!40000 ALTER TABLE `notification_logs` DISABLE KEYS */;
INSERT INTO `notification_logs` VALUES (1,2,'php','info@viserlab.com','vfvfbf@gmail.com','Order successfully completed','<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\">\r\n  <!--[if !mso]><!-->\r\n  <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\">\r\n  <!--<![endif]-->\r\n  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\r\n  <title></title>\r\n  <style type=\"text/css\">\r\n.ReadMsgBody { width: 100%; background-color: #ffffff; }\r\n.ExternalClass { width: 100%; background-color: #ffffff; }\r\n.ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td, .ExternalClass div { line-height: 100%; }\r\nhtml { width: 100%; }\r\nbody { -webkit-text-size-adjust: none; -ms-text-size-adjust: none; margin: 0; padding: 0; }\r\ntable { border-spacing: 0; table-layout: fixed; margin: 0 auto;border-collapse: collapse; }\r\ntable table table { table-layout: auto; }\r\n.yshortcuts a { border-bottom: none !important; }\r\nimg:hover { opacity: 0.9 !important; }\r\na { color: #0087ff; text-decoration: none; }\r\n.textbutton a { font-family: \'open sans\', arial, sans-serif !important;}\r\n.btn-link a { color:#FFFFFF !important;}\r\n\r\n@media only screen and (max-width: 480px) {\r\nbody { width: auto !important; }\r\n*[class=\"table-inner\"] { width: 90% !important; text-align: center !important; }\r\n*[class=\"table-full\"] { width: 100% !important; text-align: center !important; }\r\n/* image */\r\nimg[class=\"img1\"] { width: 100% !important; height: auto !important; }\r\n}\r\n</style>\r\n\r\n\r\n\r\n  <table bgcolor=\"#414a51\" width=\"100%\" border=\"0\" align=\"center\" cellpadding=\"0\" cellspacing=\"0\">\r\n    <tbody><tr>\r\n      <td height=\"50\"></td>\r\n    </tr>\r\n    <tr>\r\n      <td align=\"center\" style=\"text-align:center;vertical-align:top;font-size:0;\">\r\n        <table align=\"center\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">\r\n          <tbody><tr>\r\n            <td align=\"center\" width=\"600\">\r\n              <!--header-->\r\n              <table class=\"table-inner\" width=\"95%\" border=\"0\" align=\"center\" cellpadding=\"0\" cellspacing=\"0\">\r\n                <tbody><tr>\r\n                  <td bgcolor=\"#0087ff\" style=\"border-top-left-radius:6px; border-top-right-radius:6px;text-align:center;vertical-align:top;font-size:0;\" align=\"center\">\r\n                    <table width=\"90%\" border=\"0\" align=\"center\" cellpadding=\"0\" cellspacing=\"0\">\r\n                      <tbody><tr>\r\n                        <td height=\"20\"></td>\r\n                      </tr>\r\n                      <tr>\r\n                        <td align=\"center\" style=\"font-family: \'Open sans\', Arial, sans-serif; color:#FFFFFF; font-size:16px; font-weight: bold;\">This is a System Generated Email</td>\r\n                      </tr>\r\n                      <tr>\r\n                        <td height=\"20\"></td>\r\n                      </tr>\r\n                    </tbody></table>\r\n                  </td>\r\n                </tr>\r\n              </tbody></table>\r\n              <!--end header-->\r\n              <table class=\"table-inner\" width=\"95%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\r\n                <tbody><tr>\r\n                  <td bgcolor=\"#FFFFFF\" align=\"center\" style=\"text-align:center;vertical-align:top;font-size:0;\">\r\n                    <table align=\"center\" width=\"90%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\r\n                      <tbody><tr>\r\n                        <td height=\"35\"></td>\r\n                      </tr>\r\n                      <!--logo-->\r\n                      <tr>\r\n                        <td align=\"center\" style=\"vertical-align:top;font-size:0;\">\r\n                          <a href=\"#\">\r\n                            <img style=\"display:block; line-height:0px; font-size:0px; border:0px;\" src=\"https://i.imgur.com/Z1qtvtV.png\" alt=\"img\">\r\n                          </a>\r\n                        </td>\r\n                      </tr>\r\n                      <!--end logo-->\r\n                      <tr>\r\n                        <td height=\"40\"></td>\r\n                      </tr>\r\n                      <!--headline-->\r\n                      <tr>\r\n                        <td align=\"center\" style=\"font-family: \'Open Sans\', Arial, sans-serif; font-size: 22px;color:#414a51;font-weight: bold;\">Hello RIAZUL ISLAM SHOJOL (hhuhuhu)</td>\r\n                      </tr>\r\n                      <!--end headline-->\r\n                      <tr>\r\n                        <td align=\"center\" style=\"text-align:center;vertical-align:top;font-size:0;\">\r\n                          <table width=\"40\" border=\"0\" align=\"center\" cellpadding=\"0\" cellspacing=\"0\">\r\n                            <tbody><tr>\r\n                              <td height=\"20\" style=\" border-bottom:3px solid #0087ff;\"></td>\r\n                            </tr>\r\n                          </tbody></table>\r\n                        </td>\r\n                      </tr>\r\n                      <tr>\r\n                        <td height=\"20\"></td>\r\n                      </tr>\r\n                      <!--content-->\r\n                      <tr>\r\n                        <td align=\"left\" style=\"font-family: \'Open sans\', Arial, sans-serif; color:#7f8c8d; font-size:16px; line-height: 28px;\"><div>Order successfully placed.</div><div>User Name : hhuhuhu</div><div>Order No:<b> OQXN824REWTF</b></div><div>Sub Total : <b>90.00&nbsp;</b><span style=\"text-align: var(--bs-body-text-align);\"><font color=\"#212529\"><b>USD</b></font></span></div><div>Shipping Charge : <b>1.00</b><span style=\"text-align: var(--bs-body-text-align);\"><font color=\"#212529\"><b>USD</b></font></span></div><div>Total:<b> 91.00&nbsp;</b><span style=\"text-align: var(--bs-body-text-align);\"><font color=\"#212529\"><b>USD</b></font></span></div></td>\r\n                      </tr>\r\n                      <!--end content-->\r\n                      <tr>\r\n                        <td height=\"40\"></td>\r\n                      </tr>\r\n              \r\n                    </tbody></table>\r\n                  </td>\r\n                </tr>\r\n                <tr>\r\n                  <td height=\"45\" align=\"center\" bgcolor=\"#f4f4f4\" style=\"border-bottom-left-radius:6px;border-bottom-right-radius:6px;\">\r\n                    <table align=\"center\" width=\"90%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\r\n                      <tbody><tr>\r\n                        <td height=\"10\"></td>\r\n                      </tr>\r\n                      <!--preference-->\r\n                      <tr>\r\n                        <td class=\"preference-link\" align=\"center\" style=\"font-family: \'Open sans\', Arial, sans-serif; color:#95a5a6; font-size:14px;\">\r\n                          ├é┬⌐ 2021 <a href=\"#\">Dealshop</a>&nbsp;. All Rights Reserved. \r\n                        </td>\r\n                      </tr>\r\n                      <!--end preference-->\r\n                      <tr>\r\n                        <td height=\"10\"></td>\r\n                      </tr>\r\n                    </tbody></table>\r\n                  </td>\r\n                </tr>\r\n              </tbody></table>\r\n            </td>\r\n          </tr>\r\n        </tbody></table>\r\n      </td>\r\n    </tr>\r\n    <tr>\r\n      <td height=\"60\"></td>\r\n    </tr>\r\n  </tbody></table>',NULL,NULL,'email','2025-10-10 12:27:55','2025-10-10 12:27:55'),(2,2,'php','info@viserlab.com','vfvfbf@gmail.com','Payment Request Submitted Successfully','<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\">\r\n  <!--[if !mso]><!-->\r\n  <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\">\r\n  <!--<![endif]-->\r\n  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\r\n  <title></title>\r\n  <style type=\"text/css\">\r\n.ReadMsgBody { width: 100%; background-color: #ffffff; }\r\n.ExternalClass { width: 100%; background-color: #ffffff; }\r\n.ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td, .ExternalClass div { line-height: 100%; }\r\nhtml { width: 100%; }\r\nbody { -webkit-text-size-adjust: none; -ms-text-size-adjust: none; margin: 0; padding: 0; }\r\ntable { border-spacing: 0; table-layout: fixed; margin: 0 auto;border-collapse: collapse; }\r\ntable table table { table-layout: auto; }\r\n.yshortcuts a { border-bottom: none !important; }\r\nimg:hover { opacity: 0.9 !important; }\r\na { color: #0087ff; text-decoration: none; }\r\n.textbutton a { font-family: \'open sans\', arial, sans-serif !important;}\r\n.btn-link a { color:#FFFFFF !important;}\r\n\r\n@media only screen and (max-width: 480px) {\r\nbody { width: auto !important; }\r\n*[class=\"table-inner\"] { width: 90% !important; text-align: center !important; }\r\n*[class=\"table-full\"] { width: 100% !important; text-align: center !important; }\r\n/* image */\r\nimg[class=\"img1\"] { width: 100% !important; height: auto !important; }\r\n}\r\n</style>\r\n\r\n\r\n\r\n  <table bgcolor=\"#414a51\" width=\"100%\" border=\"0\" align=\"center\" cellpadding=\"0\" cellspacing=\"0\">\r\n    <tbody><tr>\r\n      <td height=\"50\"></td>\r\n    </tr>\r\n    <tr>\r\n      <td align=\"center\" style=\"text-align:center;vertical-align:top;font-size:0;\">\r\n        <table align=\"center\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">\r\n          <tbody><tr>\r\n            <td align=\"center\" width=\"600\">\r\n              <!--header-->\r\n              <table class=\"table-inner\" width=\"95%\" border=\"0\" align=\"center\" cellpadding=\"0\" cellspacing=\"0\">\r\n                <tbody><tr>\r\n                  <td bgcolor=\"#0087ff\" style=\"border-top-left-radius:6px; border-top-right-radius:6px;text-align:center;vertical-align:top;font-size:0;\" align=\"center\">\r\n                    <table width=\"90%\" border=\"0\" align=\"center\" cellpadding=\"0\" cellspacing=\"0\">\r\n                      <tbody><tr>\r\n                        <td height=\"20\"></td>\r\n                      </tr>\r\n                      <tr>\r\n                        <td align=\"center\" style=\"font-family: \'Open sans\', Arial, sans-serif; color:#FFFFFF; font-size:16px; font-weight: bold;\">This is a System Generated Email</td>\r\n                      </tr>\r\n                      <tr>\r\n                        <td height=\"20\"></td>\r\n                      </tr>\r\n                    </tbody></table>\r\n                  </td>\r\n                </tr>\r\n              </tbody></table>\r\n              <!--end header-->\r\n              <table class=\"table-inner\" width=\"95%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\r\n                <tbody><tr>\r\n                  <td bgcolor=\"#FFFFFF\" align=\"center\" style=\"text-align:center;vertical-align:top;font-size:0;\">\r\n                    <table align=\"center\" width=\"90%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\r\n                      <tbody><tr>\r\n                        <td height=\"35\"></td>\r\n                      </tr>\r\n                      <!--logo-->\r\n                      <tr>\r\n                        <td align=\"center\" style=\"vertical-align:top;font-size:0;\">\r\n                          <a href=\"#\">\r\n                            <img style=\"display:block; line-height:0px; font-size:0px; border:0px;\" src=\"https://i.imgur.com/Z1qtvtV.png\" alt=\"img\">\r\n                          </a>\r\n                        </td>\r\n                      </tr>\r\n                      <!--end logo-->\r\n                      <tr>\r\n                        <td height=\"40\"></td>\r\n                      </tr>\r\n                      <!--headline-->\r\n                      <tr>\r\n                        <td align=\"center\" style=\"font-family: \'Open Sans\', Arial, sans-serif; font-size: 22px;color:#414a51;font-weight: bold;\">Hello RIAZUL ISLAM SHOJOL (hhuhuhu)</td>\r\n                      </tr>\r\n                      <!--end headline-->\r\n                      <tr>\r\n                        <td align=\"center\" style=\"text-align:center;vertical-align:top;font-size:0;\">\r\n                          <table width=\"40\" border=\"0\" align=\"center\" cellpadding=\"0\" cellspacing=\"0\">\r\n                            <tbody><tr>\r\n                              <td height=\"20\" style=\" border-bottom:3px solid #0087ff;\"></td>\r\n                            </tr>\r\n                          </tbody></table>\r\n                        </td>\r\n                      </tr>\r\n                      <tr>\r\n                        <td height=\"20\"></td>\r\n                      </tr>\r\n                      <!--content-->\r\n                      <tr>\r\n                        <td align=\"left\" style=\"font-family: \'Open sans\', Arial, sans-serif; color:#7f8c8d; font-size:16px; line-height: 28px;\"><div>Your payment request of&nbsp;<span style=\"font-weight: bolder;\">91.00 USD</span>&nbsp;is via&nbsp;&nbsp;<span style=\"font-weight: bolder;\">WinTerSMM&nbsp;</span>submitted successfully<span style=\"font-weight: bolder;\">&nbsp;.<br></span></div><div><span style=\"font-weight: bolder;\"><br></span></div><div><span style=\"font-weight: bolder;\">Details of your Payment :<br></span></div><div><br></div><div>Amount : 91.00 USD</div><div>Charge:&nbsp;<font color=\"#FF0000\">2.82 USD</font></div><div><br></div><div>Conversion Rate : 1 USD = 120.00 USDT</div><div>Payable : 11,258.40 USDT<br></div><div>Pay via :&nbsp; WinTerSMM</div><div><br></div><div><span style=\"color: rgb(33, 37, 41);\">Transaction Number : OQXN824REWTF</span><br></div><div>Order No : OQXN824REWTF</div><div><br></div><div><br style=\"font-family: Montserrat, sans-serif;\"></div></td>\r\n                      </tr>\r\n                      <!--end content-->\r\n                      <tr>\r\n                        <td height=\"40\"></td>\r\n                      </tr>\r\n              \r\n                    </tbody></table>\r\n                  </td>\r\n                </tr>\r\n                <tr>\r\n                  <td height=\"45\" align=\"center\" bgcolor=\"#f4f4f4\" style=\"border-bottom-left-radius:6px;border-bottom-right-radius:6px;\">\r\n                    <table align=\"center\" width=\"90%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\r\n                      <tbody><tr>\r\n                        <td height=\"10\"></td>\r\n                      </tr>\r\n                      <!--preference-->\r\n                      <tr>\r\n                        <td class=\"preference-link\" align=\"center\" style=\"font-family: \'Open sans\', Arial, sans-serif; color:#95a5a6; font-size:14px;\">\r\n                          ├é┬⌐ 2021 <a href=\"#\">Dealshop</a>&nbsp;. All Rights Reserved. \r\n                        </td>\r\n                      </tr>\r\n                      <!--end preference-->\r\n                      <tr>\r\n                        <td height=\"10\"></td>\r\n                      </tr>\r\n                    </tbody></table>\r\n                  </td>\r\n                </tr>\r\n              </tbody></table>\r\n            </td>\r\n          </tr>\r\n        </tbody></table>\r\n      </td>\r\n    </tr>\r\n    <tr>\r\n      <td height=\"60\"></td>\r\n    </tr>\r\n  </tbody></table>',NULL,NULL,'email','2025-10-10 12:27:56','2025-10-10 12:27:56'),(3,5,'php','info@viserlab.com','jnjguurghh@gmail.com','demo','<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\">\r\n  <!--[if !mso]><!-->\r\n  <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\">\r\n  <!--<![endif]-->\r\n  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\r\n  <title></title>\r\n  <style type=\"text/css\">\r\n.ReadMsgBody { width: 100%; background-color: #ffffff; }\r\n.ExternalClass { width: 100%; background-color: #ffffff; }\r\n.ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td, .ExternalClass div { line-height: 100%; }\r\nhtml { width: 100%; }\r\nbody { -webkit-text-size-adjust: none; -ms-text-size-adjust: none; margin: 0; padding: 0; }\r\ntable { border-spacing: 0; table-layout: fixed; margin: 0 auto;border-collapse: collapse; }\r\ntable table table { table-layout: auto; }\r\n.yshortcuts a { border-bottom: none !important; }\r\nimg:hover { opacity: 0.9 !important; }\r\na { color: #0087ff; text-decoration: none; }\r\n.textbutton a { font-family: \'open sans\', arial, sans-serif !important;}\r\n.btn-link a { color:#FFFFFF !important;}\r\n\r\n@media only screen and (max-width: 480px) {\r\nbody { width: auto !important; }\r\n*[class=\"table-inner\"] { width: 90% !important; text-align: center !important; }\r\n*[class=\"table-full\"] { width: 100% !important; text-align: center !important; }\r\n/* image */\r\nimg[class=\"img1\"] { width: 100% !important; height: auto !important; }\r\n}\r\n</style>\r\n\r\n\r\n\r\n  <table bgcolor=\"#414a51\" width=\"100%\" border=\"0\" align=\"center\" cellpadding=\"0\" cellspacing=\"0\">\r\n    <tbody><tr>\r\n      <td height=\"50\"></td>\r\n    </tr>\r\n    <tr>\r\n      <td align=\"center\" style=\"text-align:center;vertical-align:top;font-size:0;\">\r\n        <table align=\"center\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">\r\n          <tbody><tr>\r\n            <td align=\"center\" width=\"600\">\r\n              <!--header-->\r\n              <table class=\"table-inner\" width=\"95%\" border=\"0\" align=\"center\" cellpadding=\"0\" cellspacing=\"0\">\r\n                <tbody><tr>\r\n                  <td bgcolor=\"#0087ff\" style=\"border-top-left-radius:6px; border-top-right-radius:6px;text-align:center;vertical-align:top;font-size:0;\" align=\"center\">\r\n                    <table width=\"90%\" border=\"0\" align=\"center\" cellpadding=\"0\" cellspacing=\"0\">\r\n                      <tbody><tr>\r\n                        <td height=\"20\"></td>\r\n                      </tr>\r\n                      <tr>\r\n                        <td align=\"center\" style=\"font-family: \'Open sans\', Arial, sans-serif; color:#FFFFFF; font-size:16px; font-weight: bold;\">This is a System Generated Email</td>\r\n                      </tr>\r\n                      <tr>\r\n                        <td height=\"20\"></td>\r\n                      </tr>\r\n                    </tbody></table>\r\n                  </td>\r\n                </tr>\r\n              </tbody></table>\r\n              <!--end header-->\r\n              <table class=\"table-inner\" width=\"95%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\r\n                <tbody><tr>\r\n                  <td bgcolor=\"#FFFFFF\" align=\"center\" style=\"text-align:center;vertical-align:top;font-size:0;\">\r\n                    <table align=\"center\" width=\"90%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\r\n                      <tbody><tr>\r\n                        <td height=\"35\"></td>\r\n                      </tr>\r\n                      <!--logo-->\r\n                      <tr>\r\n                        <td align=\"center\" style=\"vertical-align:top;font-size:0;\">\r\n                          <a href=\"#\">\r\n                            <img style=\"display:block; line-height:0px; font-size:0px; border:0px;\" src=\"https://i.imgur.com/Z1qtvtV.png\" alt=\"img\">\r\n                          </a>\r\n                        </td>\r\n                      </tr>\r\n                      <!--end logo-->\r\n                      <tr>\r\n                        <td height=\"40\"></td>\r\n                      </tr>\r\n                      <!--headline-->\r\n                      <tr>\r\n                        <td align=\"center\" style=\"font-family: \'Open Sans\', Arial, sans-serif; font-size: 22px;color:#414a51;font-weight: bold;\">Hello opu mia (opumia)</td>\r\n                      </tr>\r\n                      <!--end headline-->\r\n                      <tr>\r\n                        <td align=\"center\" style=\"text-align:center;vertical-align:top;font-size:0;\">\r\n                          <table width=\"40\" border=\"0\" align=\"center\" cellpadding=\"0\" cellspacing=\"0\">\r\n                            <tbody><tr>\r\n                              <td height=\"20\" style=\" border-bottom:3px solid #0087ff;\"></td>\r\n                            </tr>\r\n                          </tbody></table>\r\n                        </td>\r\n                      </tr>\r\n                      <tr>\r\n                        <td height=\"20\"></td>\r\n                      </tr>\r\n                      <!--content-->\r\n                      <tr>\r\n                        <td align=\"left\" style=\"font-family: \'Open sans\', Arial, sans-serif; color:#7f8c8d; font-size:16px; line-height: 28px;\">tesr</td>\r\n                      </tr>\r\n                      <!--end content-->\r\n                      <tr>\r\n                        <td height=\"40\"></td>\r\n                      </tr>\r\n              \r\n                    </tbody></table>\r\n                  </td>\r\n                </tr>\r\n                <tr>\r\n                  <td height=\"45\" align=\"center\" bgcolor=\"#f4f4f4\" style=\"border-bottom-left-radius:6px;border-bottom-right-radius:6px;\">\r\n                    <table align=\"center\" width=\"90%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\r\n                      <tbody><tr>\r\n                        <td height=\"10\"></td>\r\n                      </tr>\r\n                      <!--preference-->\r\n                      <tr>\r\n                        <td class=\"preference-link\" align=\"center\" style=\"font-family: \'Open sans\', Arial, sans-serif; color:#95a5a6; font-size:14px;\">\r\n                          ├é┬⌐ 2021 <a href=\"#\">demo</a>&nbsp;. All Rights Reserved. \r\n                        </td>\r\n                      </tr>\r\n                      <!--end preference-->\r\n                      <tr>\r\n                        <td height=\"10\"></td>\r\n                      </tr>\r\n                    </tbody></table>\r\n                  </td>\r\n                </tr>\r\n              </tbody></table>\r\n            </td>\r\n          </tr>\r\n        </tbody></table>\r\n      </td>\r\n    </tr>\r\n    <tr>\r\n      <td height=\"60\"></td>\r\n    </tr>\r\n  </tbody></table>',NULL,NULL,'email','2025-10-16 02:10:36','2025-10-16 02:10:36'),(4,6,'php','info@viserlab.com','limonq56@gmail.com','demo','<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\">\r\n  <!--[if !mso]><!-->\r\n  <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\">\r\n  <!--<![endif]-->\r\n  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\r\n  <title></title>\r\n  <style type=\"text/css\">\r\n.ReadMsgBody { width: 100%; background-color: #ffffff; }\r\n.ExternalClass { width: 100%; background-color: #ffffff; }\r\n.ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td, .ExternalClass div { line-height: 100%; }\r\nhtml { width: 100%; }\r\nbody { -webkit-text-size-adjust: none; -ms-text-size-adjust: none; margin: 0; padding: 0; }\r\ntable { border-spacing: 0; table-layout: fixed; margin: 0 auto;border-collapse: collapse; }\r\ntable table table { table-layout: auto; }\r\n.yshortcuts a { border-bottom: none !important; }\r\nimg:hover { opacity: 0.9 !important; }\r\na { color: #0087ff; text-decoration: none; }\r\n.textbutton a { font-family: \'open sans\', arial, sans-serif !important;}\r\n.btn-link a { color:#FFFFFF !important;}\r\n\r\n@media only screen and (max-width: 480px) {\r\nbody { width: auto !important; }\r\n*[class=\"table-inner\"] { width: 90% !important; text-align: center !important; }\r\n*[class=\"table-full\"] { width: 100% !important; text-align: center !important; }\r\n/* image */\r\nimg[class=\"img1\"] { width: 100% !important; height: auto !important; }\r\n}\r\n</style>\r\n\r\n\r\n\r\n  <table bgcolor=\"#414a51\" width=\"100%\" border=\"0\" align=\"center\" cellpadding=\"0\" cellspacing=\"0\">\r\n    <tbody><tr>\r\n      <td height=\"50\"></td>\r\n    </tr>\r\n    <tr>\r\n      <td align=\"center\" style=\"text-align:center;vertical-align:top;font-size:0;\">\r\n        <table align=\"center\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">\r\n          <tbody><tr>\r\n            <td align=\"center\" width=\"600\">\r\n              <!--header-->\r\n              <table class=\"table-inner\" width=\"95%\" border=\"0\" align=\"center\" cellpadding=\"0\" cellspacing=\"0\">\r\n                <tbody><tr>\r\n                  <td bgcolor=\"#0087ff\" style=\"border-top-left-radius:6px; border-top-right-radius:6px;text-align:center;vertical-align:top;font-size:0;\" align=\"center\">\r\n                    <table width=\"90%\" border=\"0\" align=\"center\" cellpadding=\"0\" cellspacing=\"0\">\r\n                      <tbody><tr>\r\n                        <td height=\"20\"></td>\r\n                      </tr>\r\n                      <tr>\r\n                        <td align=\"center\" style=\"font-family: \'Open sans\', Arial, sans-serif; color:#FFFFFF; font-size:16px; font-weight: bold;\">This is a System Generated Email</td>\r\n                      </tr>\r\n                      <tr>\r\n                        <td height=\"20\"></td>\r\n                      </tr>\r\n                    </tbody></table>\r\n                  </td>\r\n                </tr>\r\n              </tbody></table>\r\n              <!--end header-->\r\n              <table class=\"table-inner\" width=\"95%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\r\n                <tbody><tr>\r\n                  <td bgcolor=\"#FFFFFF\" align=\"center\" style=\"text-align:center;vertical-align:top;font-size:0;\">\r\n                    <table align=\"center\" width=\"90%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\r\n                      <tbody><tr>\r\n                        <td height=\"35\"></td>\r\n                      </tr>\r\n                      <!--logo-->\r\n                      <tr>\r\n                        <td align=\"center\" style=\"vertical-align:top;font-size:0;\">\r\n                          <a href=\"#\">\r\n                            <img style=\"display:block; line-height:0px; font-size:0px; border:0px;\" src=\"https://i.imgur.com/Z1qtvtV.png\" alt=\"img\">\r\n                          </a>\r\n                        </td>\r\n                      </tr>\r\n                      <!--end logo-->\r\n                      <tr>\r\n                        <td height=\"40\"></td>\r\n                      </tr>\r\n                      <!--headline-->\r\n                      <tr>\r\n                        <td align=\"center\" style=\"font-family: \'Open Sans\', Arial, sans-serif; font-size: 22px;color:#414a51;font-weight: bold;\">Hello Md rifat Mia (test1333)</td>\r\n                      </tr>\r\n                      <!--end headline-->\r\n                      <tr>\r\n                        <td align=\"center\" style=\"text-align:center;vertical-align:top;font-size:0;\">\r\n                          <table width=\"40\" border=\"0\" align=\"center\" cellpadding=\"0\" cellspacing=\"0\">\r\n                            <tbody><tr>\r\n                              <td height=\"20\" style=\" border-bottom:3px solid #0087ff;\"></td>\r\n                            </tr>\r\n                          </tbody></table>\r\n                        </td>\r\n                      </tr>\r\n                      <tr>\r\n                        <td height=\"20\"></td>\r\n                      </tr>\r\n                      <!--content-->\r\n                      <tr>\r\n                        <td align=\"left\" style=\"font-family: \'Open sans\', Arial, sans-serif; color:#7f8c8d; font-size:16px; line-height: 28px;\">tesr</td>\r\n                      </tr>\r\n                      <!--end content-->\r\n                      <tr>\r\n                        <td height=\"40\"></td>\r\n                      </tr>\r\n              \r\n                    </tbody></table>\r\n                  </td>\r\n                </tr>\r\n                <tr>\r\n                  <td height=\"45\" align=\"center\" bgcolor=\"#f4f4f4\" style=\"border-bottom-left-radius:6px;border-bottom-right-radius:6px;\">\r\n                    <table align=\"center\" width=\"90%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\r\n                      <tbody><tr>\r\n                        <td height=\"10\"></td>\r\n                      </tr>\r\n                      <!--preference-->\r\n                      <tr>\r\n                        <td class=\"preference-link\" align=\"center\" style=\"font-family: \'Open sans\', Arial, sans-serif; color:#95a5a6; font-size:14px;\">\r\n                          ├é┬⌐ 2021 <a href=\"#\">demo</a>&nbsp;. All Rights Reserved. \r\n                        </td>\r\n                      </tr>\r\n                      <!--end preference-->\r\n                      <tr>\r\n                        <td height=\"10\"></td>\r\n                      </tr>\r\n                    </tbody></table>\r\n                  </td>\r\n                </tr>\r\n              </tbody></table>\r\n            </td>\r\n          </tr>\r\n        </tbody></table>\r\n      </td>\r\n    </tr>\r\n    <tr>\r\n      <td height=\"60\"></td>\r\n    </tr>\r\n  </tbody></table>',NULL,NULL,'email','2025-10-16 02:10:36','2025-10-16 02:10:36');
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
INSERT INTO `notification_templates` VALUES (3,'DEPOSIT_COMPLETE','Deposit - Automated - Successful','Deposit Completed Successfully','<div>Your deposit of&nbsp;<span style=\"font-weight: bolder;\">{{amount}} {{site_currency}}</span>&nbsp;is via&nbsp;&nbsp;<span style=\"font-weight: bolder;\">{{method_name}}&nbsp;</span>has been completed Successfully.<span style=\"font-weight: bolder;\"><br></span></div><div><span style=\"font-weight: bolder;\"><br></span></div><div><span style=\"font-weight: bolder;\">Details of your Deposit :<br></span></div><div><br></div><div>Amount : {{amount}} {{site_currency}}</div><div>Charge:&nbsp;<font color=\"#000000\">{{charge}} {{site_currency}}</font></div><div><br></div><div>Conversion Rate : 1 {{site_currency}} = {{rate}} {{method_currency}}</div><div>Received : {{method_amount}} {{method_currency}}<br></div><div>Paid via :&nbsp; {{method_name}}</div><div><br></div><div>Transaction Number : {{trx}}</div><div><br style=\"font-family: Montserrat, sans-serif;\"></div>','{{amount}} {{site_currency}} Deposit successfully by {{method_name}}','{\"trx\":\"Transaction number for the deposit\",\"amount\":\"Amount inserted by the user\",\"charge\":\"Gateway charge set by the admin\",\"rate\":\"Conversion rate between base currency and method currency\",\"method_name\":\"Name of the deposit method\",\"method_currency\":\"Currency of the deposit method\",\"method_amount\":\"Amount after conversion between base currency and method currency\"}',1,1,'2021-11-03 12:00:00','2023-05-23 04:27:02'),(4,'DEPOSIT_APPROVE','Deposit - Manual - Approved','Your Deposit is Approved','<div style=\"font-family: Montserrat, sans-serif;\">Your deposit request of&nbsp;<span style=\"font-weight: bolder;\">{{amount}} {{site_currency}}</span>&nbsp;is via&nbsp;&nbsp;<span style=\"font-weight: bolder;\">{{method_name}}&nbsp;</span>is Approved .<span style=\"font-weight: bolder;\"><br></span></div><div style=\"font-family: Montserrat, sans-serif;\"><span style=\"font-weight: bolder;\"><br></span></div><div style=\"font-family: Montserrat, sans-serif;\"><span style=\"font-weight: bolder;\">Details of your Deposit :<br></span></div><div style=\"font-family: Montserrat, sans-serif;\"><br></div><div style=\"font-family: Montserrat, sans-serif;\">Amount : {{amount}} {{site_currency}}</div><div style=\"font-family: Montserrat, sans-serif;\">Charge:&nbsp;<font color=\"#FF0000\">{{charge}} {{site_currency}}</font></div><div style=\"font-family: Montserrat, sans-serif;\"><br></div><div style=\"font-family: Montserrat, sans-serif;\">Conversion Rate : 1 {{site_currency}} = {{rate}} {{method_currency}}</div><div style=\"font-family: Montserrat, sans-serif;\">Received : {{method_amount}} {{method_currency}}<br></div><div style=\"font-family: Montserrat, sans-serif;\">Paid via :&nbsp; {{method_name}}</div><div style=\"font-family: Montserrat, sans-serif;\"><br></div><div style=\"font-family: Montserrat, sans-serif;\">Transaction Number : {{trx}}</div><div style=\"font-family: Montserrat, sans-serif;\"><br></div><div style=\"font-family: Montserrat, sans-serif;\"><br></div>','Admin Approve Your {{amount}} {{site_currency}} payment request by {{method_name}} transaction : {{trx}}','{\"trx\":\"Transaction number for the deposit\",\"amount\":\"Amount inserted by the user\",\"charge\":\"Gateway charge set by the admin\",\"rate\":\"Conversion rate between base currency and method currency\",\"method_name\":\"Name of the deposit method\",\"method_currency\":\"Currency of the deposit method\",\"method_amount\":\"Amount after conversion between base currency and method currency\"}',1,1,'2021-11-03 12:00:00','2023-05-23 04:28:04'),(5,'DEPOSIT_REJECT','Deposit - Manual - Rejected','Your Deposit Request is Rejected','<div style=\"font-family: Montserrat, sans-serif;\">Your deposit request of&nbsp;<span style=\"font-weight: bolder;\">{{amount}} {{site_currency}}</span>&nbsp;is via&nbsp;&nbsp;<span style=\"font-weight: bolder;\">{{method_name}} has been rejected</span>.<span style=\"font-weight: bolder;\"><br></span></div><div><br></div><div><br></div><div style=\"font-family: Montserrat, sans-serif;\">Conversion Rate : 1 {{site_currency}} = {{rate}} {{method_currency}}</div><div style=\"font-family: Montserrat, sans-serif;\">Received : {{method_amount}} {{method_currency}}<br></div><div style=\"font-family: Montserrat, sans-serif;\">Paid via :&nbsp; {{method_name}}</div><div style=\"font-family: Montserrat, sans-serif;\">Charge: {{charge}}</div><div style=\"font-family: Montserrat, sans-serif;\"><br></div><div style=\"font-family: Montserrat, sans-serif;\"><br></div><div style=\"font-family: Montserrat, sans-serif;\">Transaction Number was : {{trx}}</div><div style=\"font-family: Montserrat, sans-serif;\"><br></div><div style=\"font-family: Montserrat, sans-serif;\">if you have any queries, feel free to contact us.<br></div><br style=\"font-family: Montserrat, sans-serif;\"><div style=\"font-family: Montserrat, sans-serif;\"><br><br></div><span style=\"color: rgb(33, 37, 41); font-family: Montserrat, sans-serif;\">{{rejection_message}}</span><br>','Admin Rejected Your {{amount}} {{site_currency}} payment request by {{method_name}}\r\n\r\n{{rejection_message}}','{\"trx\":\"Transaction number for the deposit\",\"amount\":\"Amount inserted by the user\",\"charge\":\"Gateway charge set by the admin\",\"rate\":\"Conversion rate between base currency and method currency\",\"method_name\":\"Name of the deposit method\",\"method_currency\":\"Currency of the deposit method\",\"method_amount\":\"Amount after conversion between base currency and method currency\",\"rejection_message\":\"Rejection message by the admin\"}',1,1,'2021-11-03 12:00:00','2022-04-05 03:45:27'),(7,'PASS_RESET_CODE','Password - Reset - Code','Password Reset','<div style=\"font-family: Montserrat, sans-serif;\">We have received a request to reset the password for your account on&nbsp;<span style=\"font-weight: bolder;\">{{time}} .<br></span></div><div style=\"font-family: Montserrat, sans-serif;\">Requested From IP:&nbsp;<span style=\"font-weight: bolder;\">{{ip}}</span>&nbsp;using&nbsp;<span style=\"font-weight: bolder;\">{{browser}}</span>&nbsp;on&nbsp;<span style=\"font-weight: bolder;\">{{operating_system}}&nbsp;</span>.</div><div style=\"font-family: Montserrat, sans-serif;\"><br></div><br style=\"font-family: Montserrat, sans-serif;\"><div style=\"font-family: Montserrat, sans-serif;\"><div>Your account recovery code is:&nbsp;&nbsp;&nbsp;<font size=\"6\"><span style=\"font-weight: bolder;\">{{code}}</span></font></div><div><br></div></div><div style=\"font-family: Montserrat, sans-serif;\"><br></div><div style=\"font-family: Montserrat, sans-serif;\"><font size=\"4\" color=\"#CC0000\">If you do not wish to reset your password, please disregard this message.&nbsp;</font><br></div><div><font size=\"4\" color=\"#CC0000\"><br></font></div>','Your account recovery code is: {{code}}','{\"code\":\"Verification code for password reset\",\"ip\":\"IP address of the user\",\"browser\":\"Browser of the user\",\"operating_system\":\"Operating system of the user\",\"time\":\"Time of the request\"}',1,0,'2021-11-03 12:00:00','2022-03-20 20:47:05'),(8,'PASS_RESET_DONE','Password - Reset - Confirmation','You have reset your password','<p style=\"font-family: Montserrat, sans-serif;\">You have successfully reset your password.</p><p style=\"font-family: Montserrat, sans-serif;\">You changed from&nbsp; IP:&nbsp;<span style=\"font-weight: bolder;\">{{ip}}</span>&nbsp;using&nbsp;<span style=\"font-weight: bolder;\">{{browser}}</span>&nbsp;on&nbsp;<span style=\"font-weight: bolder;\">{{operating_system}}&nbsp;</span>&nbsp;on&nbsp;<span style=\"font-weight: bolder;\">{{time}}</span></p><p style=\"font-family: Montserrat, sans-serif;\"><span style=\"font-weight: bolder;\"><br></span></p><p style=\"font-family: Montserrat, sans-serif;\"><span style=\"font-weight: bolder;\"><font color=\"#ff0000\">If you did not change that, please contact us as soon as possible.</font></span></p>','Your password has been changed successfully','{\"ip\":\"IP address of the user\",\"browser\":\"Browser of the user\",\"operating_system\":\"Operating system of the user\",\"time\":\"Time of the request\"}',1,1,'2021-11-03 12:00:00','2022-04-05 03:46:35'),(9,'ADMIN_SUPPORT_REPLY','Support - Reply','Reply Support Ticket','<div><p><span data-mce-style=\"font-size: 11pt;\" style=\"font-size: 11pt;\"><span style=\"font-weight: bolder;\">A member from our support team has replied to the following ticket:</span></span></p><p><span style=\"font-weight: bolder;\"><span data-mce-style=\"font-size: 11pt;\" style=\"font-size: 11pt;\"><span style=\"font-weight: bolder;\"><br></span></span></span></p><p><span style=\"font-weight: bolder;\">[Ticket#{{ticket_id}}] {{ticket_subject}}<br><br>Click here to reply:&nbsp; {{link}}</span></p><p>----------------------------------------------</p><p>Here is the reply :<br></p><p>{{reply}}<br></p></div><div><br style=\"font-family: Montserrat, sans-serif;\"></div>','Your Ticket#{{ticket_id}} :  {{ticket_subject}} has been replied.','{\"ticket_id\":\"ID of the support ticket\",\"ticket_subject\":\"Subject  of the support ticket\",\"reply\":\"Reply made by the admin\",\"link\":\"URL to view the support ticket\"}',1,1,'2021-11-03 12:00:00','2022-03-20 20:47:51'),(10,'EVER_CODE','Verification - Email','Please verify your email address','<br><div><div style=\"font-family: Montserrat, sans-serif;\">Thanks For joining us.<br></div><div style=\"font-family: Montserrat, sans-serif;\">Please use the below code to verify your email address.<br></div><div style=\"font-family: Montserrat, sans-serif;\"><br></div><div style=\"font-family: Montserrat, sans-serif;\">Your email verification code is:<font size=\"6\"><span style=\"font-weight: bolder;\">&nbsp;{{code}}</span></font></div></div>','---','{\"code\":\"Email verification code\"}',1,0,'2021-11-03 12:00:00','2022-04-03 02:32:07'),(11,'SVER_CODE','Verification - SMS','Verify Your Mobile Number','---','Your phone verification code is: {{code}}','{\"code\":\"SMS Verification Code\"}',0,1,'2021-11-03 12:00:00','2022-03-20 19:24:37'),(15,'DEFAULT','Default Template','{{subject}}','{{message}}','{{message}}','{\"subject\":\"Subject\",\"message\":\"Message\"}',1,1,'2019-09-14 13:14:22','2021-11-04 09:38:55'),(18,'ORDER_COMPLETE','Order Completed','Order successfully completed','<div>{{method_name}}</div><div>User Name : {{user_name}}</div><div>Order No:<b> {{order_no}}</b></div><div>Sub Total : <b>{{subtotal}}&nbsp;</b><span style=\"text-align: var(--bs-body-text-align);\"><font color=\"#212529\"><b>{{site_currency}}</b></font></span></div><div>Shipping Charge : <b>{{shipping_charge}}</b><span style=\"text-align: var(--bs-body-text-align);\"><font color=\"#212529\"><b>{{site_currency}}</b></font></span></div><div>Total:<b> {{total}}&nbsp;</b><span style=\"text-align: var(--bs-body-text-align);\"><font color=\"#212529\"><b>{{site_currency}}</b></font></span></div>','{{method_name}}\r\nUser Name : {{user_name}}\r\nOrder No: {{order_no}}\r\nSub Total : {{subtotal}} {{site_currency}}\r\nShipping Charge : {{shipping_charge}}{{site_currency}}\r\nTotal: {{total}} {{site_currency}}','{\"method_name\":\"Order successfully done via Wallet\",\"user_name\":\"Order By\",\"subtotal\":\"subtotal\",\"shipping_charge\":\"Shipping charge amount\",\"total\":\"Grand total amount\",\"order_no\":\"Order Number\"}',1,1,'2019-09-14 13:14:22','2023-03-06 06:04:41'),(20,'ORDER_STATUS','Order Status Change','Order status has changed successfully','<div>{{method_name}}</div><div>User Name: {{user_name}} </div><div>Order No:<b> {{order_no}}</b></div>\r\n<div>Total Price:<b> {{total}}&nbsp;</b><span style=\"text-align: var(--bs-body-text-align);\"><font color=\"#212529\"><b>{{site_currency}}</b></font></span></div>','{{method_name}}\r\nUser Name: {{user_name}}\r\nOrder No: {{order_no}}\r\nTotal Price: {{total}} {{site_currency}}','{\"method_name\":\"Order status name\",\"user_name\":\"Order Creator\",\"order_no\":\"Order Number\",\"total\":\"Total Order Price\"}',1,1,'2019-09-14 13:14:22','2023-03-06 06:22:07'),(219,'PAYMENT_REQUEST','Payment - Requested','Payment Request Submitted Successfully','<div>Your payment request of&nbsp;<span style=\"font-weight: bolder;\">{{amount}} {{site_currency}}</span>&nbsp;is via&nbsp;&nbsp;<span style=\"font-weight: bolder;\">{{method_name}}&nbsp;</span>submitted successfully<span style=\"font-weight: bolder;\">&nbsp;.<br></span></div><div><span style=\"font-weight: bolder;\"><br></span></div><div><span style=\"font-weight: bolder;\">Details of your Payment :<br></span></div><div><br></div><div>Amount : {{amount}} {{site_currency}}</div><div>Charge:&nbsp;<font color=\"#FF0000\">{{charge}} {{site_currency}}</font></div><div><br></div><div>Conversion Rate : 1 {{site_currency}} = {{rate}} {{method_currency}}</div><div>Payable : {{method_amount}} {{method_currency}}<br></div><div>Pay via :&nbsp; {{method_name}}</div><div><br></div><div><span style=\"color: rgb(33, 37, 41);\">Transaction Number : {{trx}}</span><br></div><div>Order No : {{order_no}}</div><div><br></div><div><br style=\"font-family: Montserrat, sans-serif;\"></div>','{{amount}} {{site_currency}} Payment requested by {{method_name}}. Charge: {{charge}} . Trx: {{trx}} Order No : {{order_no}}','{\"trx\":\"Transaction number for the deposit\",\"amount\":\"Amount inserted by the user\",\"charge\":\"Gateway charge set by the admin\",\"rate\":\"Conversion rate between base currency and method currency\",\"method_name\":\"Name of the deposit method\",\"method_currency\":\"Currency of the deposit method\",\"method_amount\":\"Amount after conversion between base currency and method currency\",\"order_no\":\"Order no\"}',1,1,NULL,'2023-04-13 07:46:49'),(220,'DELIVERY_SCANNED_BY_DRIVER','Delivery location scanned','Delivery scanned for order {{order_no}}','<p>{{method_name}} Order <strong>{{order_no}}</strong>.</p><p>Your product is with the delivery person. Track delivery: <a href=\"{{map_link}}\" target=\"_blank\">Open Google Maps</a></p>','Product with delivery. Order {{order_no}}. Map: {{map_link}}','[\"order_no\",\"method_name\",\"map_link\"]',1,0,'2026-02-26 23:45:08','2026-02-26 23:45:08'),(221,'ABANDONED_CART','Abandoned Cart Reminder','You left items in your cart ΓÇô complete your order','<p>Hi {{user_name}},</p><p>You left items in your cart. Complete your order now before stock runs out!</p><p><strong>Cart value:</strong> {{cart_value}}</p><p><a href=\"{{recovery_link}}\" style=\"display:inline-block;padding:10px 20px;background:#007bff;color:#fff;text-decoration:none;border-radius:5px;\">Complete my order</a></p><p>If you have any questions, reply to this email.</p>','You left items in your cart. Complete your order: {{recovery_link}}','[\"user_name\",\"recovery_link\",\"cart_value\",\"product_list\"]',1,1,'2026-03-19 04:21:54','2026-03-19 04:21:54');
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
  CONSTRAINT `orders_shipping_zone_id_foreign` FOREIGN KEY (`shipping_zone_id`) REFERENCES `shipping_zones` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
INSERT INTO `orders` VALUES (1,'registered',NULL,NULL,NULL,NULL,NULL,NULL,NULL,2,'OQXN824REWTF',90.00000000,0.00000000,1.00000000,0.00,91.00000000,0.00,NULL,0,1,NULL,'{\"address\":\"\\u0997\\u09a1\\u09bc\\u09bf\\u09af\\u09bc\\u09be \\u09a8\\u09a4\\u09c1\\u09a8 \\u09b9\\u09be\\u099f\",\"state\":\"Dhaka\",\"zip\":\"17493\",\"country\":\"Bangladesh\",\"city\":\"\\u09ac\\u09b0\\u09bf\\u09b6\\u09be\\u09b2\"}',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,9,0,'2025-10-10 12:25:44','2025-10-16 13:32:13',NULL,NULL),(2,'registered',NULL,NULL,NULL,NULL,NULL,NULL,NULL,3,'OEODPYY517CH',180.00000000,0.00000000,1.00000000,0.00,181.00000000,0.00,NULL,0,1,NULL,'{\"address\":\"hjhbf\",\"state\":\"545\",\"zip\":\"65\",\"country\":\"Bangladesh\",\"city\":\"dfghh\"}',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,0,0,'2025-10-11 13:20:58','2025-10-11 13:20:58',NULL,NULL),(3,'registered',NULL,NULL,NULL,NULL,NULL,NULL,NULL,3,'G8XSRWQQ1BQM',180.00000000,0.00000000,1.00000000,0.00,181.00000000,0.00,NULL,0,1,NULL,'{\"address\":\"hjhbf\",\"state\":\"545\",\"zip\":\"65\",\"country\":\"Bangladesh\",\"city\":\"dfghh\"}',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,0,0,'2025-10-11 13:22:01','2025-10-11 13:22:01',NULL,NULL),(4,'registered',NULL,NULL,NULL,NULL,NULL,NULL,NULL,3,'SZP2JMHNZ3C5',180.00000000,0.00000000,1.00000000,0.00,181.00000000,0.00,NULL,0,1,NULL,'{\"address\":\"hjhbf\",\"state\":\"545\",\"zip\":\"65\",\"country\":\"Bangladesh\",\"city\":\"dfghh\"}',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,0,0,'2025-10-11 13:27:44','2025-10-11 13:27:44',NULL,NULL),(5,'registered',NULL,NULL,NULL,NULL,NULL,NULL,NULL,3,'YD9KEHAS8HRU',180.00000000,0.00000000,1.00000000,0.00,181.00000000,0.00,NULL,0,1,NULL,'{\"address\":\"hjhbf\",\"state\":\"545\",\"zip\":\"65\",\"country\":\"Bangladesh\",\"city\":\"dfghh\"}',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,0,0,'2025-10-11 13:28:25','2025-10-11 13:28:25',NULL,NULL),(6,'registered',NULL,NULL,NULL,NULL,NULL,NULL,NULL,3,'717RMVW2K1SS',180.00000000,0.00000000,1.00000000,0.00,181.00000000,0.00,NULL,0,1,NULL,'{\"address\":\"hjhbf\",\"state\":\"545\",\"zip\":\"65\",\"country\":\"Bangladesh\",\"city\":\"dfghh\"}',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,0,0,'2025-10-11 13:50:51','2025-10-11 13:50:51',NULL,NULL),(7,'registered',NULL,NULL,NULL,NULL,NULL,NULL,NULL,3,'B82K7OUMX7RJ',180.00000000,0.00000000,1.00000000,0.00,181.00000000,0.00,NULL,0,1,NULL,'{\"address\":\"hjhbf\",\"state\":\"545\",\"zip\":\"65\",\"country\":\"Bangladesh\",\"city\":\"dfghh\"}',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,0,0,'2025-10-11 13:51:42','2025-10-11 13:51:42',NULL,NULL),(8,'registered',NULL,NULL,NULL,NULL,NULL,NULL,NULL,3,'DGG5URG39KS7',180.00000000,0.00000000,1.00000000,0.00,181.00000000,0.00,NULL,0,1,NULL,'{\"address\":\"hjhbf\",\"state\":\"545\",\"zip\":\"65\",\"country\":\"Bangladesh\",\"city\":\"dfghh\"}',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,0,0,'2025-10-11 14:50:36','2025-10-11 14:50:36',NULL,NULL),(9,'registered',NULL,NULL,NULL,NULL,NULL,NULL,NULL,4,'584BUM39DB53',100.00000000,0.00000000,0.00000000,0.00,100.00000000,0.00,NULL,0,1,NULL,'{\"address\":\"\\u0997\\u09a1\\u09bc\\u09bf\\u09af\\u09bc\\u09be \\u09a8\\u09a4\\u09c1\\u09a8 \\u09b9\\u09be\\u099f\",\"state\":\"Dhaka\",\"zip\":\"17493\",\"country\":\"Bangladesh\",\"city\":\"\\u09ac\\u09b0\\u09bf\\u09b6\\u09be\\u09b2\"}',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,0,0,'2025-10-12 19:43:54','2025-10-12 19:43:54',NULL,NULL),(10,'registered',NULL,NULL,NULL,NULL,NULL,NULL,NULL,4,'R1X2987FCR12',100.00000000,0.00000000,0.00000000,0.00,100.00000000,0.00,NULL,0,1,NULL,'{\"address\":\"\\u0997\\u09a1\\u09bc\\u09bf\\u09af\\u09bc\\u09be \\u09a8\\u09a4\\u09c1\\u09a8 \\u09b9\\u09be\\u099f\",\"state\":\"Dhaka\",\"zip\":\"17493\",\"country\":\"Bangladesh\",\"city\":\"\\u09ac\\u09b0\\u09bf\\u09b6\\u09be\\u09b2\"}',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,0,0,'2025-10-13 10:51:47','2025-10-13 10:51:47',NULL,NULL),(11,'registered',NULL,NULL,NULL,NULL,NULL,NULL,NULL,4,'WWUSWDM9Y241',100.00000000,0.00000000,0.00000000,0.00,100.00000000,0.00,NULL,0,1,NULL,'{\"address\":\"\\u0997\\u09a1\\u09bc\\u09bf\\u09af\\u09bc\\u09be \\u09a8\\u09a4\\u09c1\\u09a8 \\u09b9\\u09be\\u099f\",\"state\":\"Dhaka\",\"zip\":\"17493\",\"country\":\"Bangladesh\",\"city\":\"\\u09ac\\u09b0\\u09bf\\u09b6\\u09be\\u09b2\"}',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,0,0,'2025-10-13 11:00:56','2025-10-13 11:00:56',NULL,NULL),(12,'registered',NULL,NULL,NULL,NULL,NULL,NULL,NULL,4,'V49XRZW6NZQ2',100.00000000,0.00000000,0.00000000,0.00,100.00000000,0.00,NULL,0,1,NULL,'{\"address\":\"\\u0997\\u09a1\\u09bc\\u09bf\\u09af\\u09bc\\u09be \\u09a8\\u09a4\\u09c1\\u09a8 \\u09b9\\u09be\\u099f\",\"state\":\"Dhaka\",\"zip\":\"17493\",\"country\":\"Bangladesh\",\"city\":\"\\u09ac\\u09b0\\u09bf\\u09b6\\u09be\\u09b2\"}',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,0,0,'2025-10-13 12:36:43','2025-10-13 12:36:43',NULL,NULL),(13,'registered',NULL,NULL,NULL,NULL,NULL,NULL,NULL,4,'A8OMXVBGWGHY',100.00000000,0.00000000,0.00000000,0.00,100.00000000,0.00,NULL,0,1,NULL,'{\"address\":\"\\u0997\\u09a1\\u09bc\\u09bf\\u09af\\u09bc\\u09be \\u09a8\\u09a4\\u09c1\\u09a8 \\u09b9\\u09be\\u099f\",\"state\":\"Dhaka\",\"zip\":\"17493\",\"country\":\"Bangladesh\",\"city\":\"\\u09ac\\u09b0\\u09bf\\u09b6\\u09be\\u09b2\"}',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,0,0,'2025-10-13 13:25:38','2025-10-13 13:25:38',NULL,NULL),(14,'registered',NULL,NULL,NULL,NULL,NULL,NULL,NULL,4,'5OMMKOPFO8MJ',100.00000000,0.00000000,0.00000000,0.00,100.00000000,0.00,NULL,0,1,NULL,'{\"address\":\"\\u0997\\u09a1\\u09bc\\u09bf\\u09af\\u09bc\\u09be \\u09a8\\u09a4\\u09c1\\u09a8 \\u09b9\\u09be\\u099f\",\"state\":\"Dhaka\",\"zip\":\"17493\",\"country\":\"Bangladesh\",\"city\":\"\\u09ac\\u09b0\\u09bf\\u09b6\\u09be\\u09b2\"}',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,0,0,'2025-10-13 13:58:39','2025-10-13 13:58:39',NULL,NULL),(15,'registered',NULL,NULL,NULL,NULL,NULL,NULL,NULL,4,'WVZKCOVQO2X3',100.00000000,0.00000000,0.00000000,0.00,100.00000000,0.00,NULL,0,1,NULL,'{\"address\":\"\\u0997\\u09a1\\u09bc\\u09bf\\u09af\\u09bc\\u09be \\u09a8\\u09a4\\u09c1\\u09a8 \\u09b9\\u09be\\u099f\",\"state\":\"Dhaka\",\"zip\":\"17493\",\"country\":\"Bangladesh\",\"city\":\"\\u09ac\\u09b0\\u09bf\\u09b6\\u09be\\u09b2\"}',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,0,0,'2025-10-14 12:04:54','2025-10-14 12:04:54',NULL,NULL),(16,'registered',NULL,NULL,NULL,NULL,NULL,NULL,NULL,4,'JYZGXD9UFRYS',100.00000000,0.00000000,0.00000000,0.00,100.00000000,0.00,NULL,0,1,NULL,'{\"address\":\"\\u0997\\u09a1\\u09bc\\u09bf\\u09af\\u09bc\\u09be \\u09a8\\u09a4\\u09c1\\u09a8 \\u09b9\\u09be\\u099f\",\"state\":\"Dhaka\",\"zip\":\"17493\",\"country\":\"Bangladesh\",\"city\":\"\\u09ac\\u09b0\\u09bf\\u09b6\\u09be\\u09b2\"}',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,0,0,'2025-10-14 12:12:33','2025-10-14 12:12:33',NULL,NULL),(17,'registered',NULL,NULL,NULL,NULL,NULL,NULL,NULL,4,'YAQ54JD9TE9S',100.00000000,0.00000000,0.00000000,0.00,100.00000000,0.00,NULL,0,1,NULL,'{\"address\":\"\\u0997\\u09a1\\u09bc\\u09bf\\u09af\\u09bc\\u09be \\u09a8\\u09a4\\u09c1\\u09a8 \\u09b9\\u09be\\u099f\",\"state\":\"Dhaka\",\"zip\":\"17493\",\"country\":\"Bangladesh\",\"city\":\"\\u09ac\\u09b0\\u09bf\\u09b6\\u09be\\u09b2\"}',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,0,0,'2025-10-14 13:24:01','2025-10-14 13:24:01',NULL,NULL),(18,'registered',NULL,NULL,NULL,NULL,NULL,NULL,NULL,4,'T6OXEMQ3ZPKY',100.00000000,0.00000000,0.00000000,0.00,100.00000000,0.00,NULL,0,1,NULL,'{\"address\":\"\\u0997\\u09a1\\u09bc\\u09bf\\u09af\\u09bc\\u09be \\u09a8\\u09a4\\u09c1\\u09a8 \\u09b9\\u09be\\u099f\",\"state\":\"Dhaka\",\"zip\":\"17493\",\"country\":\"Bangladesh\",\"city\":\"\\u09ac\\u09b0\\u09bf\\u09b6\\u09be\\u09b2\"}',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,0,0,'2025-10-14 13:49:29','2025-10-14 13:49:29',NULL,NULL),(19,'registered',NULL,NULL,NULL,NULL,NULL,NULL,NULL,4,'ASTMMCU1R6NX',200.00000000,0.00000000,0.00000000,0.00,200.00000000,0.00,NULL,0,1,NULL,'{\"address\":\"\\u0997\\u09a1\\u09bc\\u09bf\\u09af\\u09bc\\u09be \\u09a8\\u09a4\\u09c1\\u09a8 \\u09b9\\u09be\\u099f\",\"state\":\"Dhaka\",\"zip\":\"17493\",\"country\":\"Bangladesh\",\"city\":\"\\u09ac\\u09b0\\u09bf\\u09b6\\u09be\\u09b2\"}',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,0,0,'2025-10-15 10:45:55','2025-10-15 10:45:55',NULL,NULL),(20,'registered',NULL,NULL,NULL,NULL,NULL,NULL,NULL,5,'RVVMU3YOKRUC',100.00000000,0.00000000,0.00000000,0.00,100.00000000,0.00,NULL,0,1,NULL,'{\"address\":\"fvjfjbjfh\",\"state\":\"1207\",\"zip\":\"1207\",\"country\":\"Bangladesh\",\"city\":\"Dhaka\"}',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,0,0,'2025-10-15 11:43:59','2025-10-15 11:43:59',NULL,NULL),(21,'registered',NULL,NULL,NULL,NULL,NULL,NULL,NULL,6,'JDQB4PDBJ12F',100.00000000,0.00000000,0.00000000,0.00,100.00000000,0.00,NULL,0,1,NULL,'{\"address\":\"Noyakhali\",\"state\":\"Noakhali\",\"zip\":\"182\",\"country\":\"Afghanistan\",\"city\":\"Noakhali\"}',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,0,0,'2025-10-15 11:49:51','2025-10-15 11:49:51',NULL,NULL),(22,'registered',NULL,NULL,NULL,NULL,NULL,NULL,NULL,5,'3QBCFNGVVHEZ',100.00000000,0.00000000,0.00000000,0.00,100.00000000,0.00,NULL,0,1,NULL,'{\"address\":\"fvjfjbjfh\",\"state\":\"1207\",\"zip\":\"1207\",\"country\":\"Bangladesh\",\"city\":\"Dhaka\"}',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,0,0,'2025-10-15 12:41:18','2025-10-15 12:41:18',NULL,NULL),(23,'registered',NULL,NULL,NULL,NULL,NULL,NULL,NULL,6,'JV74HNT21OF5',100.00000000,0.00000000,0.00000000,0.00,100.00000000,0.00,NULL,0,1,NULL,'{\"address\":\"Noyakhali\",\"state\":\"Noakhali\",\"zip\":\"182\",\"country\":\"Afghanistan\",\"city\":\"Noakhali\"}',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,0,0,'2025-10-15 22:25:07','2025-10-15 22:25:07',NULL,NULL),(24,'registered',NULL,NULL,NULL,NULL,NULL,NULL,NULL,6,'PAB156CC2Y5E',100.00000000,0.00000000,0.00000000,0.00,100.00000000,0.00,NULL,0,1,NULL,'{\"address\":\"Noyakhali\",\"state\":\"Noakhali\",\"zip\":\"182\",\"country\":\"Afghanistan\",\"city\":\"Noakhali\"}',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,0,0,'2025-10-15 23:20:50','2025-10-15 23:20:50',NULL,NULL),(25,'registered',NULL,NULL,NULL,NULL,NULL,NULL,NULL,6,'S2K7XZUFYGOO',100.00000000,0.00000000,0.00000000,0.00,100.00000000,0.00,NULL,0,1,NULL,'{\"address\":\"Noyakhali\",\"state\":\"Noakhali\",\"zip\":\"182\",\"country\":\"Afghanistan\",\"city\":\"Noakhali\"}',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,0,0,'2025-10-15 23:26:38','2025-10-15 23:26:38',NULL,NULL),(26,'registered',NULL,NULL,NULL,NULL,NULL,NULL,NULL,6,'81JKTSQZVSSA',100.00000000,0.00000000,0.00000000,0.00,100.00000000,0.00,NULL,0,1,NULL,'{\"address\":\"Noyakhali\",\"state\":\"Noakhali\",\"zip\":\"182\",\"country\":\"Afghanistan\",\"city\":\"Noakhali\"}',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,0,0,'2025-10-15 23:30:42','2025-10-15 23:30:42',NULL,NULL),(27,'registered',NULL,NULL,NULL,NULL,NULL,NULL,NULL,6,'R24J8SM7UT46',100.00000000,0.00000000,0.00000000,0.00,100.00000000,0.00,NULL,0,1,NULL,'{\"address\":\"Noyakhali\",\"state\":\"Noakhali\",\"zip\":\"182\",\"country\":\"Afghanistan\",\"city\":\"Noakhali\"}',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,0,0,'2025-10-15 23:34:54','2025-10-15 23:34:54',NULL,NULL),(28,'registered',NULL,NULL,NULL,NULL,NULL,NULL,NULL,6,'Q1NYM36BMVPY',100.00000000,0.00000000,0.00000000,0.00,100.00000000,0.00,NULL,0,1,NULL,'{\"address\":\"Noyakhali\",\"state\":\"Noakhali\",\"zip\":\"182\",\"country\":\"Afghanistan\",\"city\":\"Noakhali\"}',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,0,0,'2025-10-15 23:50:17','2025-10-15 23:50:17',NULL,NULL),(29,'registered',NULL,NULL,NULL,NULL,NULL,NULL,NULL,6,'MSV9Y4RXW3VX',100.00000000,0.00000000,0.00000000,0.00,100.00000000,0.00,NULL,0,1,NULL,'{\"address\":\"Noyakhali\",\"state\":\"Noakhali\",\"zip\":\"182\",\"country\":\"Afghanistan\",\"city\":\"Noakhali\"}',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,0,0,'2025-10-15 23:59:41','2025-10-15 23:59:41',NULL,NULL),(30,'registered',NULL,NULL,NULL,NULL,NULL,NULL,NULL,6,'HKHHEMPRT63V',100.00000000,0.00000000,0.00000000,0.00,100.00000000,0.00,NULL,0,1,NULL,'{\"address\":\"Noyakhali\",\"state\":\"Noakhali\",\"zip\":\"182\",\"country\":\"Afghanistan\",\"city\":\"Noakhali\"}',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,0,0,'2025-10-16 00:09:49','2025-10-16 00:09:49',NULL,NULL),(31,'registered',NULL,NULL,NULL,NULL,NULL,NULL,NULL,6,'41HKZ25V8VXJ',100.00000000,0.00000000,0.00000000,0.00,100.00000000,0.00,NULL,0,1,NULL,'{\"address\":\"Noyakhali\",\"state\":\"Noakhali\",\"zip\":\"182\",\"country\":\"Afghanistan\",\"city\":\"Noakhali\"}',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,0,0,'2025-10-16 00:10:43','2025-10-16 00:10:43',NULL,NULL),(32,'registered',NULL,NULL,NULL,NULL,NULL,NULL,NULL,6,'TABMWD36UXF1',100.00000000,0.00000000,0.00000000,0.00,100.00000000,0.00,NULL,0,1,NULL,'{\"address\":\"Noyakhali\",\"state\":\"Noakhali\",\"zip\":\"182\",\"country\":\"Afghanistan\",\"city\":\"Noakhali\"}',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,0,0,'2025-10-16 00:45:10','2025-10-16 00:45:10',NULL,NULL),(33,'registered',NULL,NULL,NULL,NULL,NULL,NULL,NULL,6,'814FB5BFPDEO',150.00000000,0.00000000,0.00000000,0.00,150.00000000,0.00,NULL,0,1,NULL,'{\"address\":\"Noyakhali\",\"state\":\"Noakhali\",\"zip\":\"182\",\"country\":\"Afghanistan\",\"city\":\"Noakhali\"}',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,0,0,'2025-10-16 07:54:39','2025-10-16 07:54:39',NULL,NULL),(34,'registered',NULL,NULL,NULL,NULL,NULL,NULL,NULL,6,'T9Q97JQWADKR',250.00000000,0.00000000,0.00000000,0.00,250.00000000,0.00,NULL,0,1,NULL,'{\"address\":\"Noyakhali\",\"state\":\"Noakhali\",\"zip\":\"182\",\"country\":\"Afghanistan\",\"city\":\"Noakhali\"}',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,0,0,'2025-10-16 07:55:12','2025-10-16 07:55:12',NULL,NULL),(35,'registered',NULL,NULL,NULL,NULL,NULL,NULL,NULL,6,'UF1EMZDEC7WU',450.00000000,0.00000000,0.00000000,0.00,450.00000000,0.00,NULL,0,1,NULL,'{\"address\":\"Noyakhali\",\"state\":\"Noakhali\",\"zip\":\"182\",\"country\":\"Afghanistan\",\"city\":\"Noakhali\"}',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,0,0,'2025-10-16 11:36:01','2025-10-16 11:36:01',NULL,NULL);
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
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_attributes`
--

LOCK TABLES `product_attributes` WRITE;
/*!40000 ALTER TABLE `product_attributes` DISABLE KEYS */;
INSERT INTO `product_attributes` VALUES (1,'Size','size','select','[\"XS\",\"S\",\"M\",\"L\",\"XL\",\"XXL\",\"XXXL\"]',1,1,'2026-02-11 07:36:13','2026-03-19 10:14:40'),(2,'Color','color','color','[\"Red\",\"Blue\",\"Green\",\"Black\",\"White\",\"Yellow\",\"Pink\",\"Purple\",\"Orange\",\"Brown\",\"Gray\",\"Navy\",\"Maroon\",\"Beige\"]',2,1,'2026-02-11 07:36:13','2026-03-19 10:14:40'),(3,'Material','material','select','[\"Cotton\",\"Polyester\",\"Silk\",\"Wool\",\"Leather\",\"Denim\",\"Linen\",\"Velvet\",\"Satin\",\"Nylon\"]',3,1,'2026-02-11 07:36:13','2026-03-19 10:14:40'),(4,'Capacity','capacity','select','[\"16GB\",\"32GB\",\"64GB\",\"128GB\",\"256GB\",\"512GB\",\"1TB\",\"2TB\"]',4,1,'2026-02-11 07:36:13','2026-03-19 10:14:40'),(5,'RAM','ram','select','[\"2GB\",\"4GB\",\"6GB\",\"8GB\",\"12GB\",\"16GB\",\"32GB\",\"64GB\"]',5,1,'2026-02-11 07:36:13','2026-03-19 10:14:40'),(6,'Storage Type','storage-type','select','[\"HDD\",\"SSD\",\"NVMe\",\"M.2 SSD\"]',6,1,'2026-02-11 07:36:13','2026-03-19 10:14:40'),(7,'Screen Size','screen-size','select','[\"5.5 inch\",\"6 inch\",\"6.5 inch\",\"6.7 inch\",\"7 inch\",\"10 inch\",\"13 inch\",\"15 inch\",\"17 inch\"]',7,1,'2026-02-11 07:36:13','2026-03-19 10:14:40'),(8,'Weight','weight','text',NULL,8,1,'2026-02-11 07:36:13','2026-03-19 10:14:40'),(9,'Dimensions','dimensions','text',NULL,9,1,'2026-02-11 07:36:13','2026-03-19 10:14:40'),(10,'Warranty','warranty','select','[\"No Warranty\",\"6 Months\",\"1 Year\",\"2 Years\",\"3 Years\",\"5 Years\",\"Lifetime\"]',10,1,'2026-02-11 07:36:13','2026-03-19 10:14:40');
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_comparisons`
--

LOCK TABLES `product_comparisons` WRITE;
/*!40000 ALTER TABLE `product_comparisons` DISABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES (1,1,1,1,'RIAZUL ISLAM SHOJOL','riazul-islam-1','IPH15PRO-256BL',100.00000000,NULL,500,NULL,NULL,NULL,NULL,NULL,'free',0.00,0,NULL,0.00,0.00,1,NULL,0,0,0,NULL,NULL,0,0,1,1,'general',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,'kkkmk',NULL,'kmmkmk',NULL,NULL,NULL,NULL,'68e947e5b4ef91760118757.png','[\"68e947e5bb2731760118757.jpg\"]',NULL,'2025-10-10 11:52:37','2026-03-19 04:21:55',0,NULL,0,0),(2,1,1,1,'WinTerSMM','wintersmm-2','8878787',100.00000000,NULL,1000,NULL,NULL,NULL,NULL,NULL,'free',0.00,0,NULL,0.00,50.00,1,NULL,0,0,0,NULL,NULL,0,0,0,1,'general',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,'test',NULL,'test',NULL,NULL,NULL,NULL,'68f0bc85ee3bb1760607365.png','[\"68f0bc86158641760607366.png\"]',NULL,'2025-10-16 03:36:06','2026-03-19 04:21:55',0,NULL,0,0),(3,1,1,1,'Cricket Jersey || Affordable Custom Cricket Jersey With Sublimation Printing','cricket-jersey-3','12',4099.00000000,NULL,1200,NULL,NULL,NULL,NULL,NULL,'free',0.00,0,NULL,0.00,5.00,2,NULL,0,0,0,NULL,NULL,0,0,0,1,'general',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,'Affordable Custom Cricket Jersey With Sublimation Printing',NULL,'<h2>Affordable Custom Cricket Jersey With Sublimation Printing</h2>',NULL,NULL,NULL,NULL,'69170154420be1763115348.jpg','[\"69170154518e91763115348.jpg\",\"691701545d7501763115348.jpg\",\"6917015463fed1763115348.png\",\"69170154697c21763115348.jpg\",\"6917015471cb71763115348.jpg\",\"691701547b33d1763115348.jpg\"]',NULL,'2025-11-14 04:15:48','2026-03-19 04:21:55',0,NULL,0,0),(4,1,1,1,'Affordable Custom Cricket Jersey With Sublimation Printing','affordable-custom-4','12',4500.00000000,NULL,1200,NULL,NULL,NULL,NULL,NULL,'free',0.00,0,NULL,0.00,5.00,2,NULL,0,0,0,NULL,NULL,0,0,0,1,'general',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,'Affordable Custom Cricket Jersey With Sublimation Printing',NULL,'Affordable Custom Cricket Jersey With Sublimation Printing',NULL,NULL,NULL,NULL,'691711aeb4c9b1763119534.jpg','[\"691711aebfda61763119534.jpg\",\"691711aec90531763119534.png\",\"691711aece2a01763119534.jpg\",\"691711aed488e1763119534.jpg\",\"691711aeda8e51763119534.jpg\",\"691711aee084a1763119534.jpg\"]',NULL,'2025-11-14 05:25:34','2026-03-19 04:21:55',0,NULL,0,0),(5,1,1,1,'Affordable Custom Cricket Jersey With Sublimation Printing','affordable-custom-5','12 pic',6000.00000000,NULL,1200,NULL,NULL,NULL,NULL,NULL,'free',0.00,0,NULL,0.00,5.00,2,NULL,0,0,0,NULL,NULL,0,0,0,1,'general',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,'Affordable Custom Cricket Jersey With Sublimation Printing',NULL,'Affordable Custom Cricket Jersey With Sublimation Printing',NULL,NULL,NULL,NULL,'69171278d428e1763119736.jpg','[\"69171278dea5c1763119736.jpg\",\"69171278e81421763119736.png\",\"69171278ed64b1763119736.jpg\",\"69171278f3fa61763119736.jpg\",\"69171279063751763119737.jpg\",\"691712790cadc1763119737.jpg\"]',NULL,'2025-11-14 05:28:57','2026-03-19 04:21:55',0,NULL,0,0);
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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `security_events`
--

LOCK TABLES `security_events` WRITE;
/*!40000 ALTER TABLE `security_events` DISABLE KEYS */;
INSERT INTO `security_events` VALUES (1,'admin_2fa_enabled','low','::1',1,NULL,'sajaladminopu/2fa/setup','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36',NULL,NULL,'2026-02-26 23:52:15','2026-02-26 23:52:15'),(2,'admin_2fa_failed','medium','::1',1,NULL,'sajaladminopu/2fa/verify','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36',NULL,NULL,'2026-03-19 04:23:51','2026-03-19 04:23:51'),(3,'admin_2fa_enabled','low','::1',1,NULL,'sajaladminopu/2fa/setup','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36',NULL,NULL,'2026-03-19 04:42:47','2026-03-19 04:42:47');
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
INSERT INTO `security_settings` VALUES (1,'ip_whitelist_enabled','0','2026-03-19 04:43:49','2026-03-19 04:43:49'),(2,'admin_login_captcha','1','2026-03-19 04:43:49','2026-03-19 04:43:49');
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
  CONSTRAINT `shipping_methods_shipping_zone_id_foreign` FOREIGN KEY (`shipping_zone_id`) REFERENCES `shipping_zones` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shipping_methods`
--

LOCK TABLES `shipping_methods` WRITE;
/*!40000 ALTER TABLE `shipping_methods` DISABLE KEYS */;
INSERT INTO `shipping_methods` VALUES (1,NULL,'Dhaka',0.00000000,1,'2025-10-10 11:21:22','2025-10-11 19:55:33',0.00,NULL,NULL,NULL,0,NULL);
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
INSERT INTO `shipping_rules` VALUES (1,5000.00,0.00,50.00,1,'2026-02-27 05:35:18','2026-02-27 05:35:18');
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shipping_zones`
--

LOCK TABLES `shipping_zones` WRITE;
/*!40000 ALTER TABLE `shipping_zones` DISABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `subcategories`
--

LOCK TABLES `subcategories` WRITE;
/*!40000 ALTER TABLE `subcategories` DISABLE KEYS */;
INSERT INTO `subcategories` VALUES (1,1,'RIAZUL ISLAM SHOJOL',1,'2025-10-10 11:29:09','2025-10-10 11:29:09');
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `support_messages`
--

LOCK TABLES `support_messages` WRITE;
/*!40000 ALTER TABLE `support_messages` DISABLE KEYS */;
INSERT INTO `support_messages` VALUES (1,1,0,'\"Hi,  \r\nI visited your website online and discovered that it was not showing up in any search results for the majority of keywords related to your company on Google, Yahoo, or Bing.  Do you want more targeted visitors on your website?  We can place your website on GoogleΓÇÖs 1st Page. yahoo, AOL, Bing. Etc.  If interested, kindly provide me your name, phone number, and email.   \r\nRegards,   \r\nBrianna Belton\"','2025-10-22 06:39:58','2025-10-22 06:39:58');
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `support_tickets`
--

LOCK TABLES `support_tickets` WRITE;
/*!40000 ALTER TABLE `support_tickets` DISABLE KEYS */;
INSERT INTO `support_tickets` VALUES (1,0,'Brianna Belton','briannawebsolution@gmail.com','83616140','briannawebsolution@gmail.com','web',NULL,NULL,0,2,'2025-10-22 12:39:58','2025-10-22 06:39:58','2025-10-22 06:39:58');
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `trusted_admin_devices`
--

LOCK TABLES `trusted_admin_devices` WRITE;
/*!40000 ALTER TABLE `trusted_admin_devices` DISABLE KEYS */;
INSERT INTO `trusted_admin_devices` VALUES (1,1,'80e821cccf53b4e2e36fb6841f4eb2b184cd1b943c9ae01e52099f0c34dda21b','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36','2026-03-19 04:42:47','2026-02-26 23:52:15','2026-03-19 04:42:47');
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
INSERT INTO `ui_settings` VALUES (1,'#ffffff','#1f2937','#0e9f90','#0c8a7d',NULL,NULL,NULL,'#f59e0b','#dc2626',NULL,NULL,'default','2026-03-19 04:21:54','2026-03-19 04:21:54');
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
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_activity_logs`
--

LOCK TABLES `user_activity_logs` WRITE;
/*!40000 ALTER TABLE `user_activity_logs` DISABLE KEYS */;
INSERT INTO `user_activity_logs` VALUES (1,NULL,'2p2frdShwsCqzuKBmXeIdFHI2QRrvwf9QR35ENRx','login_failed','Failed login attempt: digitalzero.com@gmail.com',NULL,NULL,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/user/login','2026-02-26 23:50:28','2026-02-26 23:50:28'),(2,NULL,'2p2frdShwsCqzuKBmXeIdFHI2QRrvwf9QR35ENRx','login_failed','Failed login attempt: digitalzero.com@gmail.com',NULL,NULL,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/user/login','2026-02-26 23:50:34','2026-02-26 23:50:34'),(3,NULL,'2p2frdShwsCqzuKBmXeIdFHI2QRrvwf9QR35ENRx','login_failed','Failed login attempt: bfeyfgy',NULL,NULL,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/user/login','2026-02-26 23:50:48','2026-02-26 23:50:48'),(4,NULL,'Wia1iNxKughwk6TLGJ18FQBFC4jnLSs2DJYfZjRR','login_failed','Failed login attempt: digitalzero.com@gmail.com',NULL,NULL,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/user/login','2026-02-26 23:58:22','2026-02-26 23:58:22'),(5,NULL,'Wia1iNxKughwk6TLGJ18FQBFC4jnLSs2DJYfZjRR','login_failed','Failed login attempt: digitalzero.com@gmail.com',NULL,NULL,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/user/login','2026-02-26 23:58:25','2026-02-26 23:58:25'),(6,NULL,'Wia1iNxKughwk6TLGJ18FQBFC4jnLSs2DJYfZjRR','login_failed','Failed login attempt: fgbrnbb',NULL,NULL,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/user/login','2026-02-27 02:17:06','2026-02-27 02:17:06'),(7,NULL,'Wia1iNxKughwk6TLGJ18FQBFC4jnLSs2DJYfZjRR','login_failed','Failed login attempt: bfeyfgy',NULL,NULL,'127.0.0.1','desktop','Chrome','Windows 10',NULL,NULL,NULL,NULL,'http://localhost/staylbd/user/login','2026-02-27 02:17:22','2026-02-27 02:17:22');
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
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_logins`
--

LOCK TABLES `user_logins` WRITE;
/*!40000 ALTER TABLE `user_logins` DISABLE KEYS */;
INSERT INTO `user_logins` VALUES (1,1,'103.126.219.219','','','','','','Chrome','Windows 10','2025-10-10 11:24:15','2025-10-10 11:24:15'),(2,2,'103.126.219.219','','','','','','Chrome','Windows 10','2025-10-10 12:18:08','2025-10-10 12:18:08'),(3,3,'103.181.43.24','','','','','','Chrome','Windows 10','2025-10-11 13:19:39','2025-10-11 13:19:39'),(4,4,'103.181.43.24','','','','','','Chrome','Windows 10','2025-10-12 19:33:24','2025-10-12 19:33:24'),(5,5,'103.126.219.219','','','','','','Chrome','Windows 10','2025-10-15 11:42:04','2025-10-15 11:42:04'),(6,6,'103.181.43.24','','','','','','Handheld Browser','Android','2025-10-15 11:48:36','2025-10-15 11:48:36'),(7,7,'49.206.113.94','','','','','','Chrome','Windows 10','2025-11-03 03:52:29','2025-11-03 03:52:29');
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_saved_addresses`
--

LOCK TABLES `user_saved_addresses` WRITE;
/*!40000 ALTER TABLE `user_saved_addresses` DISABLE KEYS */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_search_logs`
--

LOCK TABLES `user_search_logs` WRITE;
/*!40000 ALTER TABLE `user_search_logs` DISABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'RIAZUL ISLAM','SHOJOL',NULL,'jjjnjhnhn',1,'vfvfbfbf@gmail.com',NULL,'BD','8801766666666',NULL,NULL,1,NULL,NULL,0,0.00000000,0.00000000,0,NULL,'$2y$10$fxKfT2KnSXxhsK/2rvLYkOOe9kxdALD1cJbDXQsNvwgBiTRPdDobe','{\"country\":\"Bangladesh\",\"address\":\"\\u0997\\u09a1\\u09bc\\u09bf\\u09af\\u09bc\\u09be \\u09a8\\u09a4\\u09c1\\u09a8 \\u09b9\\u09be\\u099f\",\"state\":\"Dhaka\",\"zip\":\"17493\",\"city\":\"\\u09ac\\u09b0\\u09bf\\u09b6\\u09be\\u09b2\"}',1,0,NULL,NULL,1,1,1,1,NULL,NULL,0,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-10 11:24:15','2025-10-10 11:24:28'),(2,'RIAZUL ISLAM','SHOJOL',NULL,'hhuhuhu',1,'vfvfbf@gmail.com',NULL,'BD','8801999999999',NULL,NULL,1,NULL,NULL,0,0.00000000,0.00000000,0,NULL,'$2y$10$zj.bFbXxDKnsQqFK0qo6G.h1bEOv9n9XsYgLoByPU2OLgb7wTP9fq','{\"country\":\"Bangladesh\",\"address\":\"\\u0997\\u09a1\\u09bc\\u09bf\\u09af\\u09bc\\u09be \\u09a8\\u09a4\\u09c1\\u09a8 \\u09b9\\u09be\\u099f\",\"state\":\"Dhaka\",\"zip\":\"17493\",\"city\":\"\\u09ac\\u09b0\\u09bf\\u09b6\\u09be\\u09b2\"}',1,0,NULL,NULL,1,1,1,1,NULL,NULL,0,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-10 12:18:08','2025-10-10 12:18:30'),(3,'ygygyff','ffff',NULL,'adminkys',1,'info@wintersmm.com',NULL,'BD','8801388888888',NULL,NULL,1,NULL,NULL,0,0.00000000,0.00000000,0,NULL,'$2y$10$F8aJqPLN86J66i2I1bWpbOuyU5lJUTxY8wPoPgrIkHeOAOz8cY2cW','{\"country\":\"Bangladesh\",\"address\":\"hjhbf\",\"state\":\"545\",\"zip\":\"65\",\"city\":\"dfghh\"}',1,0,NULL,NULL,1,1,1,1,NULL,NULL,0,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-11 13:19:39','2025-10-11 13:20:04'),(4,'eefefeefehhh','vfvkk',NULL,'nillislam03',1,'xx@gmail.com',NULL,'AF','931909876543',NULL,NULL,1,NULL,NULL,0,0.00000000,0.00000000,0,NULL,'$2y$10$ztq.D51jgZGHuY1GXFCtJu2VmNykRK02MnqT5dLT67HHLjxZ9QTme','{\"country\":\"Bangladesh\",\"address\":\"\\u0997\\u09a1\\u09bc\\u09bf\\u09af\\u09bc\\u09be \\u09a8\\u09a4\\u09c1\\u09a8 \\u09b9\\u09be\\u099f\",\"state\":\"Dhaka\",\"zip\":\"17493\",\"city\":\"\\u09ac\\u09b0\\u09bf\\u09b6\\u09be\\u09b2\"}',1,0,NULL,NULL,1,1,1,1,NULL,NULL,0,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-12 19:33:24','2025-10-12 19:42:51'),(5,'opu','mia',NULL,'opumia',1,'jnjguurghh@gmail.com',NULL,'BD','88053445354353',NULL,NULL,1,NULL,NULL,0,0.00000000,0.00000000,0,NULL,'$2y$10$qkXn.abBj/nXNMJybYlBkeP1Non0kLRt5VAXuqfZUlNJ/PoAT3FnC','{\"country\":\"Bangladesh\",\"address\":\"fvjfjbjfh\",\"state\":\"1207\",\"zip\":\"1207\",\"city\":\"Dhaka\"}',1,0,NULL,NULL,1,1,1,1,NULL,NULL,0,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-15 11:42:04','2025-10-15 11:42:44'),(6,'Md rifat','Mia',NULL,'test1333',1,'limonq56@gmail.com',NULL,'AF','931325632562',NULL,NULL,1,NULL,NULL,0,0.00000000,0.00000000,0,NULL,'$2y$10$E7GvVEWwaJGouqcbvOBBYOj3EDlwXNbd8..7j4N70TZEci6//PYhK','{\"country\":\"Afghanistan\",\"address\":\"Noyakhali\",\"state\":\"Noakhali\",\"zip\":\"182\",\"city\":\"Noakhali\"}',1,0,NULL,NULL,1,1,1,1,NULL,NULL,0,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-15 11:48:36','2025-10-15 11:49:09'),(7,'john','Denilraj',NULL,'dsrgtsert',1,'a9tndw7ouy@wnbaldwy.com',NULL,'IN','918968468734',NULL,NULL,1,NULL,NULL,0,0.00000000,0.00000000,0,NULL,'$2y$10$Vb7t4HomSzLTqm9Z5tPoq.j9k2MnVlNwbffWtZIapqDsLw5g8owd2','{\"country\":\"India\",\"address\":\"765 Main Road\",\"state\":\"Tamilnadu\",\"zip\":\"657843\",\"city\":\"cbe\"}',1,0,NULL,NULL,1,1,1,1,NULL,NULL,0,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-11-03 03:52:29','2025-11-03 03:52:35');
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wishlists`
--

LOCK TABLES `wishlists` WRITE;
/*!40000 ALTER TABLE `wishlists` DISABLE KEYS */;
/*!40000 ALTER TABLE `wishlists` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'wintersm_tt'
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
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=CURRENT_USER SQL SECURITY DEFINER */
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

-- Dump completed on 2026-03-19 17:09:33
