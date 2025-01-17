-- MySQL dump 10.13  Distrib 8.0.40, for macos14.7 (arm64)
--
-- Host: 127.0.0.1    Database: capstone_poss
-- ------------------------------------------------------
-- Server version	5.5.5-10.4.21-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `brands`
--

DROP TABLE IF EXISTS `brands`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `brands` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `picture` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `brands`
--

LOCK TABLES `brands` WRITE;
/*!40000 ALTER TABLE `brands` DISABLE KEYS */;
INSERT INTO `brands` VALUES (14,'Capstone','brands/xeDHsd5M8aMEq20zGkWDTbhjicYSgL5smqRZzF2x.png','2025-01-16 14:28:18','2025-01-17 01:08:37'),(15,'Je-x','brands/RW2skGSmS2ofg7OlwL0S88B0K9mtiSz2azFaTf7P.jpg','2025-01-16 14:29:57','2025-01-16 14:29:57'),(16,'COUP','brands/kOjKoa0e1PfztuoWzXuXjgw2TOxWry5fpGxztfuK.jpg','2025-01-16 15:07:17','2025-01-16 15:07:17'),(17,'ALG','brands/Ab6bPlHdrS6zdGMwwT8mrVRxdvomTXDwRiTDQ1WX.jpg','2025-01-16 15:11:43','2025-01-16 15:11:43'),(18,'Florinzaa','brands/la8US4P5iwVizMTNA50mxgmdEqmucYarkPOmMUzQ.jpg','2025-01-16 15:11:56','2025-01-16 15:11:56'),(19,'GoodStuff','brands/ibEljyCuRus1CdOPH30lozsa3KsDOWM9DVCCyJsg.jpg','2025-01-16 15:12:08','2025-01-16 15:12:08'),(20,'Helio','brands/TFxaeD5vSXbhdR3v7vza1AdCwmpLfIXXXRZXxv44.jpg','2025-01-16 15:15:30','2025-01-16 15:15:30'),(21,'Glamourazzii','brands/D3Jp5N3qMrOAB4pRCyhi42KWxg1luLLSSzS1TdYP.jpg','2025-01-16 15:17:58','2025-01-16 15:17:58'),(22,'Mewz','brands/Fn2ciluQUYjS0xzS0vN4aMaQTAz3iQQRwif3lBUU.jpg','2025-01-16 15:19:14','2025-01-16 15:19:14'),(23,'Deolinda','brands/Xi7bZQAkNn8BU1s3Yivg5mODHOPx91B61JDv3Yjy.jpg','2025-01-16 15:21:51','2025-01-16 15:21:51'),(24,'DUO11','brands/JVV2ptRY7tzpWjoqaWQWBRYlnWpUkBWVtyFUonS1.jpg','2025-01-16 15:23:58','2025-01-16 15:23:58'),(25,'MGA True','brands/x9SLS4AUBiTBxdFpKb4gFgClaqrQXSW6Wwwma89d.jpg','2025-01-16 15:25:26','2025-01-16 15:25:26'),(26,'SKYLA','brands/7iMiurHBEWuIwV3kWpBTItXUfNCyhxyWH2OtwulS.jpg','2025-01-16 15:26:42','2025-01-16 15:26:42'),(27,'The Scotts','brands/6THUkTpDSI2qTkuJJ8ZVdjCThcKvw1y9FAAbsHwN.jpg','2025-01-16 15:28:03','2025-01-16 15:28:03'),(29,'Dotted','brands/oBjxKUXowFQbquiSbrd03uMdK6LLyLkTupS4IQWN.jpg','2025-01-17 00:51:18','2025-01-17 00:51:18'),(30,'Street Rugs','brands/zrR2X0mGptAjeLnkOfH1F7UnIQoUCPMuAAg3ZXkb.jpg','2025-01-17 00:55:10','2025-01-17 00:55:10');
/*!40000 ALTER TABLE `brands` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
INSERT INTO `cache` VALUES ('non1@lhub.com|127.0.0.1','i:2;',1736938847),('non1@lhub.com|127.0.0.1:timer','i:1736938847;',1736938847),('non1@moderator.lhub|127.0.0.1','i:1;',1736938878),('non1@moderator.lhub|127.0.0.1:timer','i:1736938878;',1736938878),('spatie.permission.cache','a:3:{s:5:\"alias\";a:0:{}s:11:\"permissions\";a:0:{}s:5:\"roles\";a:0:{}}',1737157533);
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `picture` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `brand_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `categories_brand_id_foreign` (`brand_id`),
  CONSTRAINT `categories_brand_id_foreign` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (11,'Hoodies',NULL,14,'2025-01-16 14:31:02','2025-01-17 01:08:52'),(12,'Wide fit Jeans',NULL,15,'2025-01-16 16:06:50','2025-01-16 16:06:50'),(13,'Sneakers',NULL,27,'2025-01-16 16:07:25','2025-01-16 16:07:25'),(14,'Jacket',NULL,17,'2025-01-16 16:07:35','2025-01-16 16:07:35'),(15,'Quarters',NULL,17,'2025-01-16 16:07:49','2025-01-16 16:07:49'),(16,'Accessories',NULL,21,'2025-01-16 17:40:40','2025-01-16 17:40:40');
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `color_item`
--

DROP TABLE IF EXISTS `color_item`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `color_item` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `item_id` bigint(20) unsigned NOT NULL,
  `color_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `color_item_item_id_color_id_unique` (`item_id`,`color_id`),
  KEY `color_item_color_id_foreign` (`color_id`),
  CONSTRAINT `color_item_color_id_foreign` FOREIGN KEY (`color_id`) REFERENCES `colors` (`id`) ON DELETE CASCADE,
  CONSTRAINT `color_item_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=112 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `color_item`
--

LOCK TABLES `color_item` WRITE;
/*!40000 ALTER TABLE `color_item` DISABLE KEYS */;
INSERT INTO `color_item` VALUES (80,179,3,NULL,NULL),(81,180,3,NULL,NULL),(82,181,3,NULL,NULL),(83,182,4,NULL,NULL),(84,183,4,NULL,NULL),(85,184,4,NULL,NULL),(86,185,5,NULL,NULL),(87,186,5,NULL,NULL),(88,187,5,NULL,NULL),(89,189,2,NULL,NULL),(90,190,2,NULL,NULL),(91,191,2,NULL,NULL),(92,192,3,NULL,NULL),(93,193,3,NULL,NULL),(94,194,3,NULL,NULL),(95,196,3,NULL,NULL),(96,197,3,NULL,NULL),(97,198,3,NULL,NULL),(98,199,5,NULL,NULL),(99,200,5,NULL,NULL),(100,201,5,NULL,NULL),(101,203,3,NULL,NULL),(102,204,3,NULL,NULL),(103,205,3,NULL,NULL),(104,206,5,NULL,NULL),(105,207,5,NULL,NULL),(106,208,5,NULL,NULL),(107,210,1,NULL,NULL),(110,215,4,NULL,NULL),(111,216,4,NULL,NULL);
/*!40000 ALTER TABLE `color_item` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `colors`
--

DROP TABLE IF EXISTS `colors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `colors` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `hex_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `colors`
--

LOCK TABLES `colors` WRITE;
/*!40000 ALTER TABLE `colors` DISABLE KEYS */;
INSERT INTO `colors` VALUES (1,'Red',NULL,'2024-12-22 08:32:06','2024-12-22 08:32:06'),(2,'Blue',NULL,'2024-12-25 10:08:21','2024-12-25 10:08:21'),(3,'Black',NULL,'2025-01-16 14:07:36','2025-01-16 14:07:36'),(4,'White',NULL,'2025-01-16 14:07:41','2025-01-16 14:07:41'),(5,'Grey',NULL,'2025-01-16 14:07:47','2025-01-16 14:07:47'),(6,'Beige',NULL,'2025-01-16 14:11:41','2025-01-16 14:11:41'),(7,'Yellow',NULL,'2025-01-16 14:11:46','2025-01-16 14:11:46'),(8,'Brown',NULL,'2025-01-16 14:11:50','2025-01-16 14:11:50');
/*!40000 ALTER TABLE `colors` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
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
-- Table structure for table `item_size`
--

DROP TABLE IF EXISTS `item_size`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `item_size` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `item_id` bigint(20) unsigned NOT NULL,
  `size_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `item_size_item_id_foreign` (`item_id`),
  KEY `item_size_size_id_foreign` (`size_id`),
  CONSTRAINT `item_size_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE,
  CONSTRAINT `item_size_size_id_foreign` FOREIGN KEY (`size_id`) REFERENCES `sizes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=214 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `item_size`
--

LOCK TABLES `item_size` WRITE;
/*!40000 ALTER TABLE `item_size` DISABLE KEYS */;
INSERT INTO `item_size` VALUES (182,179,2,NULL,NULL),(183,180,3,NULL,NULL),(184,181,4,NULL,NULL),(185,182,2,NULL,NULL),(186,183,3,NULL,NULL),(187,184,4,NULL,NULL),(188,185,2,NULL,NULL),(189,186,3,NULL,NULL),(190,187,4,NULL,NULL),(191,189,2,NULL,NULL),(192,190,3,NULL,NULL),(193,191,4,NULL,NULL),(194,192,2,NULL,NULL),(195,193,3,NULL,NULL),(196,194,4,NULL,NULL),(197,196,3,NULL,NULL),(198,197,4,NULL,NULL),(199,198,5,NULL,NULL),(200,199,3,NULL,NULL),(201,200,4,NULL,NULL),(202,201,5,NULL,NULL),(203,203,7,NULL,NULL),(204,204,8,NULL,NULL),(205,205,10,NULL,NULL),(206,206,7,NULL,NULL),(207,207,8,NULL,NULL),(208,208,10,NULL,NULL),(209,210,4,NULL,NULL),(212,215,3,NULL,NULL),(213,216,4,NULL,NULL);
/*!40000 ALTER TABLE `item_size` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `items`
--

DROP TABLE IF EXISTS `items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` bigint(20) unsigned DEFAULT NULL,
  `category_id` bigint(20) unsigned NOT NULL,
  `brand_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `barcode` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quantity` int(11) NOT NULL DEFAULT 0,
  `buying_price` decimal(8,2) DEFAULT NULL,
  `selling_price` decimal(8,2) DEFAULT NULL,
  `picture` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tax` decimal(8,2) DEFAULT NULL,
  `applied_sale` decimal(8,2) DEFAULT NULL,
  `discount_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT 'percentage',
  `discount_value` decimal(8,2) NOT NULL DEFAULT 0.00,
  `barcodes` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`barcodes`)),
  `is_parent` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`),
  CONSTRAINT `items_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `items` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=217 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `items`
--

LOCK TABLES `items` WRITE;
/*!40000 ALTER TABLE `items` DISABLE KEYS */;
INSERT INTO `items` VALUES (178,NULL,11,14,'Widefit hoodie','2025-01-16 14:32:21','2025-01-17 00:24:32',NULL,'0140110178',719,0.00,500.00,NULL,0.00,NULL,'percentage',0.00,NULL,1),(179,178,11,14,'Widefit hoodie - s - Black','2025-01-16 14:32:21','2025-01-17 01:42:06','barcodes/01401101780302.png','01401101780302',75,0.00,500.00,NULL,0.00,NULL,'percentage',0.00,NULL,0),(180,178,11,14,'Widefit hoodie - m - Black','2025-01-16 14:32:21','2025-01-17 01:42:06','barcodes/01401101780303.png','01401101780303',75,0.00,500.00,NULL,0.00,NULL,'percentage',0.00,NULL,0),(181,178,11,14,'Widefit hoodie - l - Black','2025-01-16 14:32:21','2025-01-17 01:42:06','barcodes/01401101780304.png','01401101780304',75,0.00,500.00,NULL,0.00,NULL,'percentage',0.00,NULL,0),(182,178,11,14,'Widefit hoodie - s - White','2025-01-16 14:32:21','2025-01-17 00:24:32','barcodes/01401101780402.png','01401101780402',0,0.00,500.00,NULL,0.00,NULL,'percentage',0.00,NULL,0),(183,178,11,14,'Widefit hoodie - m - White','2025-01-16 14:32:21','2025-01-17 01:42:06','barcodes/01401101780403.png','01401101780403',75,0.00,500.00,NULL,0.00,NULL,'percentage',0.00,NULL,0),(184,178,11,14,'Widefit hoodie - l - White','2025-01-16 14:32:21','2025-01-17 01:42:06','barcodes/01401101780404.png','01401101780404',75,0.00,500.00,NULL,0.00,NULL,'percentage',0.00,NULL,0),(185,178,11,14,'Widefit hoodie - s - Grey','2025-01-16 14:32:21','2025-01-17 01:42:06','barcodes/01401101780502.png','01401101780502',75,0.00,500.00,NULL,0.00,NULL,'percentage',0.00,NULL,0),(186,178,11,14,'Widefit hoodie - m - Grey','2025-01-16 14:32:21','2025-01-17 01:42:06','barcodes/01401101780503.png','01401101780503',75,0.00,500.00,NULL,0.00,NULL,'percentage',0.00,NULL,0),(187,178,11,14,'Widefit hoodie - l - Grey','2025-01-16 14:32:21','2025-01-17 01:42:06','barcodes/01401101780504.png','01401101780504',74,0.00,500.00,NULL,0.00,NULL,'percentage',0.00,NULL,0),(188,NULL,11,14,'Tight Hoodie','2025-01-16 14:44:39','2025-01-17 00:09:47',NULL,'0140110188',39,0.00,100.00,NULL,0.00,NULL,'percentage',0.00,NULL,1),(189,188,11,14,'Tight Hoodie - s - Blue','2025-01-16 14:44:39','2025-01-17 01:42:06','barcodes/01401101880202.png','01401101880202',4,0.00,100.00,NULL,0.00,NULL,'percentage',0.00,NULL,0),(190,188,11,14,'Tight Hoodie - m - Blue','2025-01-16 14:44:39','2025-01-17 01:08:23','barcodes/01401101880203.png','01401101880203',2,0.00,100.00,NULL,0.00,NULL,'percentage',0.00,NULL,0),(191,188,11,14,'Tight Hoodie - l - Blue','2025-01-16 14:44:39','2025-01-17 01:08:23','barcodes/01401101880204.png','01401101880204',0,0.00,100.00,NULL,0.00,NULL,'percentage',0.00,NULL,0),(192,188,11,14,'Tight Hoodie - s - Black','2025-01-16 14:44:39','2025-01-17 01:08:23','barcodes/01401101880302.png','01401101880302',19,0.00,100.00,NULL,0.00,NULL,'percentage',0.00,NULL,0),(193,188,11,14,'Tight Hoodie - m - Black','2025-01-16 14:44:39','2025-01-17 01:08:23','barcodes/01401101880303.png','01401101880303',0,0.00,100.00,NULL,0.00,NULL,'percentage',0.00,NULL,0),(194,188,11,14,'Tight Hoodie - l - Black','2025-01-16 14:44:39','2025-01-17 01:08:23','barcodes/01401101880304.png','01401101880304',9,0.00,100.00,NULL,0.00,NULL,'percentage',0.00,NULL,0),(195,NULL,14,20,'Pattern Jacket','2025-01-16 17:44:50','2025-01-16 17:44:50',NULL,'0200140195',50,0.00,1000.00,NULL,0.00,NULL,'percentage',0.00,NULL,1),(196,195,14,20,'Pattern Jacket - m - Black','2025-01-16 17:44:50','2025-01-16 18:25:05','barcodes/02001401950303.png','02001401950303',7,0.00,1000.00,NULL,0.00,NULL,'percentage',0.00,NULL,0),(197,195,14,20,'Pattern Jacket - l - Black','2025-01-16 17:44:50','2025-01-16 17:44:50','barcodes/02001401950304.png','02001401950304',8,0.00,1000.00,NULL,0.00,NULL,'percentage',0.00,NULL,0),(198,195,14,20,'Pattern Jacket - xl - Black','2025-01-16 17:44:50','2025-01-16 17:44:50','barcodes/02001401950305.png','02001401950305',10,0.00,1000.00,NULL,0.00,NULL,'percentage',0.00,NULL,0),(199,195,14,20,'Pattern Jacket - m - Grey','2025-01-16 17:44:50','2025-01-16 17:44:50','barcodes/02001401950503.png','02001401950503',9,0.00,1000.00,NULL,0.00,NULL,'percentage',0.00,NULL,0),(200,195,14,20,'Pattern Jacket - l - Grey','2025-01-16 17:44:50','2025-01-17 01:03:06','barcodes/02001401950504.png','02001401950504',7,0.00,1000.00,NULL,0.00,NULL,'percentage',0.00,NULL,0),(201,195,14,20,'Pattern Jacket - xl - Grey','2025-01-16 17:44:50','2025-01-16 17:44:50','barcodes/02001401950505.png','02001401950505',7,0.00,1000.00,NULL,0.00,NULL,'percentage',0.00,NULL,0),(202,NULL,13,27,'Ready Sneakers','2025-01-16 17:47:34','2025-01-16 17:47:34',NULL,'0270130202',28,0.00,2000.00,NULL,0.00,NULL,'percentage',10.00,NULL,1),(203,202,13,27,'Ready Sneakers - 40 - Black','2025-01-16 17:47:34','2025-01-17 00:43:34','barcodes/02701302020307.png','02701302020307',9,0.00,2000.00,NULL,0.00,NULL,'percentage',10.00,NULL,0),(204,202,13,27,'Ready Sneakers - 41 - Black','2025-01-16 17:47:34','2025-01-16 17:47:34','barcodes/02701302020308.png','02701302020308',0,0.00,2000.00,NULL,0.00,NULL,'percentage',10.00,NULL,0),(205,202,13,27,'Ready Sneakers - 42 - Black','2025-01-16 17:47:34','2025-01-16 17:47:34','barcodes/02701302020310.png','02701302020310',2,0.00,2000.00,NULL,0.00,NULL,'percentage',10.00,NULL,0),(206,202,13,27,'Ready Sneakers - 40 - Grey','2025-01-16 17:47:34','2025-01-16 18:25:05','barcodes/02701302020507.png','02701302020507',0,0.00,2000.00,NULL,0.00,NULL,'percentage',10.00,NULL,0),(207,202,13,27,'Ready Sneakers - 41 - Grey','2025-01-16 17:47:34','2025-01-16 17:47:34','barcodes/02701302020508.png','02701302020508',4,0.00,2000.00,NULL,0.00,NULL,'percentage',10.00,NULL,0),(208,202,13,27,'Ready Sneakers - 42 - Grey','2025-01-16 17:47:34','2025-01-16 17:47:34','barcodes/02701302020510.png','02701302020510',9,0.00,2000.00,NULL,0.00,NULL,'percentage',10.00,NULL,0),(209,NULL,16,28,'IronMan','2025-01-16 18:09:31','2025-01-16 18:09:31',NULL,'0280160209',1,0.00,2100.00,NULL,0.00,NULL,'percentage',0.00,NULL,1),(210,209,16,28,'IronMan - l - Red','2025-01-16 18:09:31','2025-01-17 00:49:30','barcodes/02801602090104.png','02801602090104',1,0.00,2100.00,NULL,0.00,NULL,'percentage',0.00,NULL,0),(214,NULL,16,16,'Envy Bracelet','2025-01-17 00:20:45','2025-01-17 01:05:00',NULL,'0160160214',28,0.00,100.00,NULL,0.00,NULL,'percentage',20.00,NULL,1),(215,214,16,16,'Envy Bracelet - m - White','2025-01-17 00:20:45','2025-01-17 01:05:00','barcodes/01601602140403.png','01601602140403',7,0.00,100.00,NULL,0.00,NULL,'percentage',20.00,NULL,0),(216,214,16,16,'Envy Bracelet - l - White','2025-01-17 00:20:45','2025-01-17 01:05:00','barcodes/01601602140404.png','01601602140404',9,0.00,100.00,NULL,0.00,NULL,'percentage',20.00,NULL,0);
/*!40000 ALTER TABLE `items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=69 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2024_09_27_130153_create_items_table',2),(5,'2024_09_27_130153_create_sale_items_table',2),(6,'2024_09_27_130153_create_sales_table',2),(34,'2024_09_27_131748_create_brands_table',3),(35,'2024_09_27_131757_create_categories_table',3),(36,'2024_09_27_141005_add_category_id_to_items_table',3),(37,'2024_09_27_141611_add_brand_id_to_items_table',3),(38,'2024_09_27_151256_add_total_amount_to_sales_table',3),(39,'2024_09_28_071410_add_barcode_to_items_table',4),(40,'2024_09_28_140736_add_code_to_items_table',4),(41,'2024_09_28_143042_drop_code_from_items_table',4),(42,'2024_09_28_155850_add_quantity_to_items_table',4),(43,'2024_09_28_160919_add_item_id_to_sales_table',4),(44,'2024_11_25_134643_create_sizes_table',5),(45,'2024_11_25_135509_add_picture_to_items_table',6),(46,'2024_11_25_135908_create_item_size_table',7),(47,'2024_11_27_121311_create_refunds_table',8),(48,'2024_11_27_213752_create_permission_tables',9),(49,'2024_11_28_174041_add_refunded_quantity_to_sale_items',10),(50,'2024_12_15_003631_create_cash_drawers_table',11),(51,'2024_12_15_003653_create_cash_drawers_table',12),(52,'2024_12_16_172029_add_refund_status_to_sales_table',13),(53,'2024_12_16_180523_add_customer_fields_to_sales_table',14),(54,'2024_12_17_231127_create_colors_table',15),(55,'2024_12_17_231508_create_color_item_table',16),(56,'2024_12_17_233736_add_foreign_keys_to_color_item_table',17),(57,'2024_12_17_234159_create_color_item_table',18),(58,'2024_12_18_135701_add_discount_type_to_items_table',19),(59,'2024_12_18_141507_add_discount_value_to_items_table',20),(60,'2024_12_20_151021_create_colors_table',21),(61,'2024_12_22_103547_create_color_item_table',22),(62,'2024_12_22_115540_add_parent_id_to_items_table',23),(63,'2024_12_23_011454_adding_preferences_column_to_users_table',24),(64,'2024_12_23_132054_add_barcodes_to_items_table',25),(65,'2025_01_11_021626_create_cash_drawer_logs',26),(66,'2025_01_11_021759_create_cash_drawer_logs',27),(67,'2025_01_14_143754_add_parent_id_to_items_table',28),(68,'2025_01_15_014607_add_last_login_to_users_table',29);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `model_has_permissions`
--

DROP TABLE IF EXISTS `model_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) unsigned NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `model_has_permissions`
--

LOCK TABLES `model_has_permissions` WRITE;
/*!40000 ALTER TABLE `model_has_permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `model_has_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `model_has_roles`
--

DROP TABLE IF EXISTS `model_has_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) unsigned NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `model_has_roles`
--

LOCK TABLES `model_has_roles` WRITE;
/*!40000 ALTER TABLE `model_has_roles` DISABLE KEYS */;
INSERT INTO `model_has_roles` VALUES (1,'App\\Models\\User',1),(1,'App\\Models\\User',3),(1,'App\\Models\\User',13),(2,'App\\Models\\User',6),(3,'App\\Models\\User',2);
/*!40000 ALTER TABLE `model_has_roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `permissions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
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
-- Table structure for table `refunds`
--

DROP TABLE IF EXISTS `refunds`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `refunds` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `sale_id` bigint(20) unsigned NOT NULL,
  `item_id` bigint(20) unsigned NOT NULL,
  `quantity_refunded` int(11) NOT NULL,
  `refund_amount` decimal(10,2) NOT NULL,
  `reason` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `refunds_sale_id_foreign` (`sale_id`),
  KEY `refunds_item_id_foreign` (`item_id`),
  CONSTRAINT `refunds_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE,
  CONSTRAINT `refunds_sale_id_foreign` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=65 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `refunds`
--

LOCK TABLES `refunds` WRITE;
/*!40000 ALTER TABLE `refunds` DISABLE KEYS */;
INSERT INTO `refunds` VALUES (62,453,210,1,2100.00,NULL,'2025-01-16 23:52:47','2025-01-16 23:52:47'),(63,455,215,7,700.00,NULL,'2025-01-17 00:24:57','2025-01-17 00:24:57'),(64,457,200,1,1000.00,NULL,'2025-01-17 01:03:06','2025-01-17 01:03:06');
/*!40000 ALTER TABLE `refunds` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role_has_permissions`
--

DROP TABLE IF EXISTS `role_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) unsigned NOT NULL,
  `role_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`),
  CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_has_permissions`
--

LOCK TABLES `role_has_permissions` WRITE;
/*!40000 ALTER TABLE `role_has_permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `role_has_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'admin','web','2024-11-28 09:24:23','2024-11-28 09:24:23'),(2,'moderator','web','2024-11-28 09:24:23','2024-11-28 09:24:23'),(3,'cashier','web','2024-11-28 10:07:45','2024-11-28 10:07:45');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sale_items`
--

DROP TABLE IF EXISTS `sale_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sale_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `sale_id` bigint(20) unsigned NOT NULL,
  `item_id` bigint(20) unsigned NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `refunded_quantity` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `sale_items_sale_id_foreign` (`sale_id`),
  KEY `sale_items_item_id_foreign` (`item_id`),
  CONSTRAINT `sale_items_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE,
  CONSTRAINT `sale_items_sale_id_foreign` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=353 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sale_items`
--

LOCK TABLES `sale_items` WRITE;
/*!40000 ALTER TABLE `sale_items` DISABLE KEYS */;
INSERT INTO `sale_items` VALUES (330,450,187,1,500.00,'2025-01-16 14:32:48','2025-01-16 14:32:48',0),(331,451,196,1,1000.00,'2025-01-16 17:46:07','2025-01-16 17:46:07',0),(332,451,192,1,100.00,'2025-01-16 17:46:07','2025-01-16 17:46:07',0),(333,452,206,2,1800.00,'2025-01-16 17:49:52','2025-01-16 17:49:52',0),(335,454,189,1,100.00,'2025-01-16 18:25:05','2025-01-16 18:25:05',0),(336,454,206,1,1800.00,'2025-01-16 18:25:05','2025-01-16 18:25:05',0),(337,454,196,1,1000.00,'2025-01-16 18:25:05','2025-01-16 18:25:05',0),(338,455,215,2,100.00,'2025-01-17 00:23:21','2025-01-17 00:24:57',0),(339,455,216,9,100.00,'2025-01-17 00:23:21','2025-01-17 00:23:21',0),(340,456,215,1,100.00,'2025-01-17 00:42:42','2025-01-17 00:42:42',0),(341,456,210,1,2100.00,'2025-01-17 00:42:42','2025-01-17 00:42:42',0),(343,457,203,1,1800.00,'2025-01-17 00:43:34','2025-01-17 00:43:34',0),(344,458,179,15,500.00,'2025-01-17 01:42:06','2025-01-17 01:42:06',0),(345,458,180,15,500.00,'2025-01-17 01:42:06','2025-01-17 01:42:06',0),(346,458,181,15,500.00,'2025-01-17 01:42:06','2025-01-17 01:42:06',0),(347,458,183,15,500.00,'2025-01-17 01:42:06','2025-01-17 01:42:06',0),(348,458,184,15,500.00,'2025-01-17 01:42:06','2025-01-17 01:42:06',0),(349,458,185,15,500.00,'2025-01-17 01:42:06','2025-01-17 01:42:06',0),(350,458,186,15,500.00,'2025-01-17 01:42:06','2025-01-17 01:42:06',0),(351,458,187,15,500.00,'2025-01-17 01:42:06','2025-01-17 01:42:06',0),(352,458,189,5,100.00,'2025-01-17 01:42:06','2025-01-17 01:42:06',0);
/*!40000 ALTER TABLE `sale_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sales`
--

DROP TABLE IF EXISTS `sales`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sales` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `item_id` bigint(20) unsigned DEFAULT NULL,
  `total_amount` decimal(8,2) NOT NULL,
  `refund_status` enum('no_refund','partial_refund','full_refund') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'no_refund',
  `sale_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `customer_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `discount_type` enum('percentage','fixed','none') COLLATE utf8mb4_unicode_ci NOT NULL,
  `discount_value` decimal(10,2) DEFAULT NULL,
  `payment_method` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subtotal` decimal(10,2) NOT NULL DEFAULT 0.00,
  `discount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `user_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sales_item_id_foreign` (`item_id`),
  CONSTRAINT `sales_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=459 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sales`
--

LOCK TABLES `sales` WRITE;
/*!40000 ALTER TABLE `sales` DISABLE KEYS */;
INSERT INTO `sales` VALUES (450,NULL,500.00,'no_refund','2025-01-16 14:32:48','2025-01-16 14:32:48','2025-01-16 14:32:48',NULL,NULL,'none',0.00,'cash',500.00,0.00,1),(451,NULL,990.00,'no_refund','2025-01-16 17:46:07','2025-01-16 17:46:07','2025-01-16 17:46:07','Nour','01113207752','percentage',10.00,'cash',1100.00,110.00,1),(452,NULL,3550.00,'no_refund','2025-01-16 17:49:52','2025-01-16 17:49:52','2025-01-16 17:49:52',NULL,NULL,'fixed',50.00,'credit_card',3600.00,50.00,1),(453,NULL,0.00,'full_refund','2025-01-16 18:09:48','2025-01-16 18:09:48','2025-01-16 23:52:47',NULL,NULL,'none',0.00,'mobile_pay',2100.00,0.00,1),(454,NULL,2610.00,'no_refund','2025-01-16 18:25:05','2025-01-16 18:25:05','2025-01-16 18:25:05','Nour','01113207752','percentage',10.00,'mobile_pay',2900.00,290.00,1),(455,NULL,1100.00,'partial_refund','2025-01-17 00:23:21','2025-01-17 00:23:21','2025-01-17 00:24:57',NULL,NULL,'none',0.00,'cash',1800.00,0.00,1),(456,NULL,2200.00,'no_refund','2025-01-17 00:42:42','2025-01-17 00:42:42','2025-01-17 00:42:42',NULL,NULL,'none',0.00,'cash',2200.00,0.00,1),(457,NULL,1744.00,'partial_refund','2025-01-17 00:43:34','2025-01-17 00:43:34','2025-01-17 01:03:06','Nour','01113207752','percentage',2.00,'cash',2800.00,56.00,1),(458,NULL,54450.00,'no_refund','2025-01-17 01:42:06','2025-01-17 01:42:06','2025-01-17 01:42:06','Nour','01113207752','percentage',10.00,'cash',60500.00,6050.00,1);
/*!40000 ALTER TABLE `sales` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
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
INSERT INTO `sessions` VALUES ('CjzdG0V0SMm9m240XNyRIFYGwoAOIlzGxnf6tkq5',1,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10.15; rv:132.0) Gecko/20100101 Firefox/132.0','YTo0OntzOjY6Il90b2tlbiI7czo0MDoiZkVpTE12S2hiUUNLS3A1ekIzRG00Zks5Ykt5ejVTWUdFYXJkU2pOSCI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9kYXNoYm9hcmQiO31zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO30=',1734536801),('xKBDFx2i2W1JivkEkLwkwQhYZYEK40OIEbnR73Hy',6,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10.15; rv:132.0) Gecko/20100101 Firefox/132.0','YTo0OntzOjY6Il90b2tlbiI7czo0MDoib1RNdUZDT3NyN3NsQnNmRzExWXN5ZTBueTEwSlZkUmtFd0FKNkpOVCI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzQ6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9zYWxlcy9jcmVhdGUiO31zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aTo2O30=',1734564337);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sizes`
--

DROP TABLE IF EXISTS `sizes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sizes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sizes`
--

LOCK TABLES `sizes` WRITE;
/*!40000 ALTER TABLE `sizes` DISABLE KEYS */;
INSERT INTO `sizes` VALUES (1,'xs','clothes','2024-11-25 11:47:49','2024-11-25 11:47:49'),(2,'s','clothes','2024-11-25 12:09:45','2024-11-25 12:09:45'),(3,'m','clothes','2024-11-25 12:09:51','2024-11-25 12:09:51'),(4,'l','clothes','2024-11-25 12:09:56','2024-11-25 12:09:56'),(5,'xl','clothes','2024-11-25 12:10:00','2024-11-25 12:10:00'),(6,'39','shoes','2024-11-25 16:15:40','2025-01-16 14:06:45'),(7,'40','shoes','2024-11-25 16:15:49','2025-01-16 14:06:51'),(8,'41','shoes','2025-01-16 14:05:50','2025-01-16 14:06:57'),(10,'42','shoes','2025-01-16 14:06:08','2025-01-16 14:07:02'),(12,'44','shoes','2025-01-16 14:06:22','2025-01-16 14:07:14');
/*!40000 ALTER TABLE `sizes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `last_login` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'lara_admin','lara_admin@admin.com',NULL,'$2y$12$7V6udZJpN4rMfwFnvtluf.xqTBLzw1A1vNP3emhcwS8TApB10kFr6','o9hvhDMadnQHWrFLQwZBbd53NT1tA4oqcgNjJ2Ryjm4CGBkvddVu6t0ptvJa','2024-09-27 10:34:05','2025-01-17 13:36:09','2025-01-17 13:36:09'),(2,'Maher','maher@cashier.com',NULL,'$2y$12$zqgL/xtP8IaAVZCC2JVtEuRYwgaZOLDwcZ.4XAT2KEXQwUuqhYnd6',NULL,'2024-11-22 11:37:23','2025-01-15 00:03:43',NULL),(3,'admin@localhub.com','admin@localhub.com',NULL,'$2y$12$juCVm4SiVKvuVKcYlaAVju.sOeMvLcm5ECxI6Z6ehn3At/8SILR66',NULL,'2024-11-22 11:39:23','2024-11-22 11:39:23',NULL),(6,'Non1','non1@moderator.com',NULL,'$2y$12$VfKakXMLcBRl9FEfRQIqBeawT6Dws5oHciysCx0dyTA18mEW7AZTC',NULL,'2024-12-14 22:08:38','2025-01-15 11:00:44','2025-01-15 11:00:44'),(13,'Omar Meneeem','omar_meneeem@admin.lhub',NULL,'$2y$12$EHzzCqIgSzmEs8LjOP1GleedxrzdzgzVCJdmhPlqooeD7cLZ2i5K2',NULL,'2024-12-24 15:35:59','2024-12-24 15:35:59',NULL);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'capstone_poss'
--
