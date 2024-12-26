-- MySQL dump 10.13  Distrib 9.0.1, for macos14.7 (arm64)
--
-- Host: localhost    Database: capstone_poss
-- ------------------------------------------------------
-- Server version	9.0.1

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
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `picture` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `brands`
--

LOCK TABLES `brands` WRITE;
/*!40000 ALTER TABLE `brands` DISABLE KEYS */;
INSERT INTO `brands` VALUES (5,'Capstone','brands/imSFp0zVHPfg0BcNNe1UV51rkVK4xZQpzYLHs5A0.png','2024-11-26 12:11:14','2024-11-26 12:11:14'),(7,'Brand 2','brands/25SZzgVsEoRvBvOjnwLUMwqeSiUDpNxQbsHEwHba.png','2024-11-29 12:57:53','2024-11-29 12:57:53'),(8,'CAPS','brands/sJZC7UeJKWiSEUEjgZMKIXYH8PGJjRPMLSJRqiNp.png','2024-12-17 21:01:13','2024-12-17 21:01:13'),(9,'KSKSK','brands/QgL0748yDJyTHnBqQdcgt1iIimlOqCZukWqroWOC.png','2024-12-17 21:01:35','2024-12-17 21:01:35'),(10,'Adidas','brands/EJjP2ehMWXx3fmOFERePRskQBoVQjC5CmZnzifsf.png','2024-12-17 21:01:47','2024-12-17 21:01:47'),(11,'Nike','brands/GYo8p4lEg2462KNvokgBlmSaMelBUj3R25ypLD85.jpg','2024-12-17 21:02:11','2024-12-17 21:02:11');
/*!40000 ALTER TABLE `brands` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
INSERT INTO `cache` VALUES ('spatie.permission.cache','a:3:{s:5:\"alias\";a:0:{}s:11:\"permissions\";a:0:{}s:5:\"roles\";a:0:{}}',1734794039);
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
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
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `picture` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `brand_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `categories_brand_id_foreign` (`brand_id`),
  CONSTRAINT `categories_brand_id_foreign` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (4,'Hoodies',NULL,5,'2024-11-26 12:11:21','2024-11-26 12:11:21'),(6,'Category two',NULL,7,'2024-11-29 12:58:08','2024-11-29 12:58:08'),(7,'SKSKSK',NULL,10,'2024-12-17 21:02:42','2024-12-17 21:02:42');
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `colors`
--

DROP TABLE IF EXISTS `colors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `colors` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `colors`
--

LOCK TABLES `colors` WRITE;
/*!40000 ALTER TABLE `colors` DISABLE KEYS */;
/*!40000 ALTER TABLE `colors` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
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
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `item_id` bigint unsigned NOT NULL,
  `size_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `item_size_item_id_foreign` (`item_id`),
  KEY `item_size_size_id_foreign` (`size_id`),
  CONSTRAINT `item_size_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE,
  CONSTRAINT `item_size_size_id_foreign` FOREIGN KEY (`size_id`) REFERENCES `sizes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=89 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `item_size`
--

LOCK TABLES `item_size` WRITE;
/*!40000 ALTER TABLE `item_size` DISABLE KEYS */;
INSERT INTO `item_size` VALUES (28,63,1,NULL,NULL),(29,63,2,NULL,NULL),(30,63,3,NULL,NULL),(31,64,5,NULL,NULL),(39,67,1,NULL,NULL),(40,67,2,NULL,NULL),(41,67,3,NULL,NULL),(42,68,1,NULL,NULL),(43,68,2,NULL,NULL),(44,68,3,NULL,NULL),(45,69,7,NULL,NULL),(46,70,2,NULL,NULL),(47,70,3,NULL,NULL),(48,70,5,NULL,NULL),(49,71,2,NULL,NULL),(50,71,3,NULL,NULL),(51,71,5,NULL,NULL),(52,72,1,NULL,NULL),(53,72,5,NULL,NULL),(54,73,1,NULL,NULL),(55,73,2,NULL,NULL),(56,73,3,NULL,NULL),(57,74,1,NULL,NULL),(58,74,2,NULL,NULL),(59,74,3,NULL,NULL),(60,75,1,NULL,NULL),(61,75,2,NULL,NULL),(62,75,3,NULL,NULL),(63,76,1,NULL,NULL),(64,76,4,NULL,NULL),(65,77,1,NULL,NULL),(66,77,2,NULL,NULL),(77,83,1,NULL,NULL),(78,83,2,NULL,NULL),(83,86,1,NULL,NULL),(84,86,3,NULL,NULL),(85,87,1,NULL,NULL),(86,87,2,NULL,NULL),(87,88,1,NULL,NULL),(88,88,2,NULL,NULL);
/*!40000 ALTER TABLE `item_size` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `items`
--

DROP TABLE IF EXISTS `items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `category_id` bigint unsigned NOT NULL,
  `brand_id` bigint unsigned NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `barcode` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quantity` int NOT NULL DEFAULT '0',
  `buying_price` decimal(8,2) DEFAULT NULL,
  `selling_price` decimal(8,2) DEFAULT NULL,
  `picture` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tax` decimal(8,2) DEFAULT NULL,
  `applied_sale` decimal(8,2) DEFAULT NULL,
  `discount_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'percentage',
  `discount_value` decimal(8,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=89 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `items`
--

LOCK TABLES `items` WRITE;
/*!40000 ALTER TABLE `items` DISABLE KEYS */;
INSERT INTO `items` VALUES (63,4,5,'Widefit hoodie','2024-11-26 12:11:43','2024-12-18 11:45:02','barcodes/00005463.png','00005463',126,100.00,500.00,NULL,NULL,20.00,'percentage',0.00),(64,4,5,'Aiii','2024-11-26 12:55:06','2024-12-20 13:16:48','barcodes/00005464.png','00005464',50,200.00,1000.00,NULL,NULL,20.00,'percentage',0.00),(67,6,7,'Item Two','2024-11-29 12:58:34','2024-12-15 09:41:14','barcodes/00007667.png','00007667',1,100.00,200.00,NULL,NULL,10.00,'percentage',0.00),(68,4,5,'lala','2024-12-15 10:15:20','2024-12-18 11:31:28','barcodes/00005468.png','00005468',0,200.00,900.00,NULL,0.00,0.00,'percentage',0.00),(69,6,5,'Shoe one','2024-12-16 10:30:23','2024-12-18 09:29:53','barcodes/00005669.png','00005669',9,100.00,2000.00,NULL,0.00,0.00,'percentage',0.00),(70,6,7,'Nan','2024-12-17 20:57:32','2024-12-18 11:31:28',NULL,NULL,99,99.00,999.00,NULL,0.00,0.00,'percentage',0.00),(71,6,7,'Nan','2024-12-17 20:57:58','2024-12-17 20:57:58','barcodes/768.png','768',100,99.00,999.00,NULL,0.00,0.00,'percentage',0.00),(72,6,7,'TOOO','2024-12-17 20:59:26','2024-12-17 21:10:05','barcodes/00007672.png','00007672',917,5.00,999.00,NULL,0.00,0.00,'percentage',0.00),(73,7,11,'KAKAK','2024-12-17 21:03:16','2024-12-17 21:10:05','barcodes/00011773.png','00011773',97,3.00,33.00,NULL,0.00,0.00,'percentage',0.00),(74,7,11,'LKO','2024-12-17 21:03:56','2024-12-20 13:08:16','barcodes/0000011774.png','0000011774',996,99.00,999.00,NULL,0.00,9.00,'percentage',0.00),(75,7,11,'OP','2024-12-17 21:06:08','2024-12-18 12:21:52','barcodes/1010111775.png','1010111775',123,100.00,200.00,NULL,NULL,12.00,'percentage',10.00),(76,7,11,'NX','2024-12-17 21:07:11','2024-12-18 11:27:39','barcodes/1177600000.png','1177600000',9990,9.00,8888.00,NULL,0.00,8.00,'percentage',0.00),(77,7,11,'AX','2024-12-17 21:30:47','2024-12-18 11:48:39','barcodes/1107000077.png','1107000077',28,2.00,10.00,NULL,0.00,2.00,'percentage',0.00),(83,7,11,'HOl','2024-12-17 21:42:32','2024-12-18 11:13:06','barcodes/0110070083.png','0110070083',18,1.00,5.00,NULL,0.00,0.00,'percentage',0.00),(86,7,11,'Kas','2024-12-17 21:48:10','2024-12-18 09:30:30','barcodes/0110070086.png','0110070086',1,1.00,11.00,NULL,0.00,0.00,'percentage',0.00),(87,7,11,'Xaa','2024-12-18 09:20:18','2024-12-18 10:37:20','barcodes/0110070087.png','0110070087',88,4.00,900.00,NULL,0.00,0.00,'percentage',0.00),(88,7,11,'LetsTry','2024-12-18 12:10:01','2024-12-18 12:25:35','barcodes/0110070088.png','0110070088',199,900.00,9000.00,NULL,NULL,0.00,'fixed',20.00);
/*!40000 ALTER TABLE `items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
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
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
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
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=61 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2024_09_27_130153_create_items_table',2),(5,'2024_09_27_130153_create_sale_items_table',2),(6,'2024_09_27_130153_create_sales_table',2),(34,'2024_09_27_131748_create_brands_table',3),(35,'2024_09_27_131757_create_categories_table',3),(36,'2024_09_27_141005_add_category_id_to_items_table',3),(37,'2024_09_27_141611_add_brand_id_to_items_table',3),(38,'2024_09_27_151256_add_total_amount_to_sales_table',3),(39,'2024_09_28_071410_add_barcode_to_items_table',4),(40,'2024_09_28_140736_add_code_to_items_table',4),(41,'2024_09_28_143042_drop_code_from_items_table',4),(42,'2024_09_28_155850_add_quantity_to_items_table',4),(43,'2024_09_28_160919_add_item_id_to_sales_table',4),(44,'2024_11_25_134643_create_sizes_table',5),(45,'2024_11_25_135509_add_picture_to_items_table',6),(46,'2024_11_25_135908_create_item_size_table',7),(47,'2024_11_27_121311_create_refunds_table',8),(48,'2024_11_27_213752_create_permission_tables',9),(49,'2024_11_28_174041_add_refunded_quantity_to_sale_items',10),(50,'2024_12_15_003631_create_cash_drawers_table',11),(51,'2024_12_15_003653_create_cash_drawers_table',12),(52,'2024_12_16_172029_add_refund_status_to_sales_table',13),(53,'2024_12_16_180523_add_customer_fields_to_sales_table',14),(54,'2024_12_17_231127_create_colors_table',15),(55,'2024_12_17_231508_create_color_item_table',16),(56,'2024_12_17_233736_add_foreign_keys_to_color_item_table',17),(57,'2024_12_17_234159_create_color_item_table',18),(58,'2024_12_18_135701_add_discount_type_to_items_table',19),(59,'2024_12_18_141507_add_discount_value_to_items_table',20),(60,'2024_12_20_151021_create_colors_table',21);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `model_has_permissions`
--

DROP TABLE IF EXISTS `model_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
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
  `role_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
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
INSERT INTO `model_has_roles` VALUES (1,'App\\Models\\User',1),(2,'App\\Models\\User',2),(1,'App\\Models\\User',3),(3,'App\\Models\\User',6);
/*!40000 ALTER TABLE `model_has_roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
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
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
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
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `sale_id` bigint unsigned NOT NULL,
  `item_id` bigint unsigned NOT NULL,
  `quantity_refunded` int NOT NULL,
  `refund_amount` decimal(10,2) NOT NULL,
  `reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `refunds_sale_id_foreign` (`sale_id`),
  KEY `refunds_item_id_foreign` (`item_id`),
  CONSTRAINT `refunds_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE,
  CONSTRAINT `refunds_sale_id_foreign` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `refunds`
--

LOCK TABLES `refunds` WRITE;
/*!40000 ALTER TABLE `refunds` DISABLE KEYS */;
INSERT INTO `refunds` VALUES (1,304,64,1,1000.00,'No','2024-11-28 14:27:40','2024-11-28 14:27:40'),(2,304,64,1,1000.00,NULL,'2024-11-28 14:28:26','2024-11-28 14:28:26'),(3,304,64,1,1000.00,NULL,'2024-11-28 14:28:39','2024-11-28 14:28:39'),(8,306,63,1,500.00,NULL,'2024-11-28 15:42:14','2024-11-28 15:42:14'),(9,306,63,1,500.00,NULL,'2024-11-28 15:42:14','2024-11-28 15:42:14'),(10,306,63,1,500.00,NULL,'2024-11-28 15:42:14','2024-11-28 15:42:14'),(11,306,64,1,1000.00,NULL,'2024-11-28 15:42:14','2024-11-28 15:42:14'),(13,311,64,3,3000.00,NULL,'2024-12-14 21:27:12','2024-12-14 21:27:12'),(14,312,64,1,1000.00,NULL,'2024-12-14 21:28:49','2024-12-14 21:28:49'),(15,312,67,1,200.00,NULL,'2024-12-14 21:28:49','2024-12-14 21:28:49'),(16,312,63,1,500.00,NULL,'2024-12-14 21:28:49','2024-12-14 21:28:49'),(17,313,64,1,1000.00,NULL,'2024-12-14 21:44:28','2024-12-14 21:44:28'),(18,318,63,1,500.00,NULL,'2024-12-15 09:00:04','2024-12-15 09:00:04'),(19,319,64,1,1000.00,NULL,'2024-12-15 09:03:57','2024-12-15 09:03:57'),(20,329,63,1,500.00,NULL,'2024-12-16 15:17:17','2024-12-16 15:17:17'),(21,331,68,2,1800.00,NULL,'2024-12-16 15:24:04','2024-12-16 15:24:04'),(22,331,68,1,900.00,NULL,'2024-12-16 15:25:58','2024-12-16 15:25:58'),(23,329,63,1,500.00,NULL,'2024-12-16 15:26:27','2024-12-16 15:26:27'),(24,329,69,2,4000.00,NULL,'2024-12-16 15:26:27','2024-12-16 15:26:27'),(25,328,63,1,500.00,NULL,'2024-12-16 15:26:50','2024-12-16 15:26:50'),(26,318,63,2,1000.00,NULL,'2024-12-16 15:27:04','2024-12-16 15:27:04'),(27,330,69,1,2000.00,NULL,'2024-12-16 15:27:51','2024-12-16 15:27:51'),(28,328,63,1,500.00,NULL,'2024-12-16 15:28:10','2024-12-16 15:28:10'),(29,319,63,3,1500.00,NULL,'2024-12-16 15:34:27','2024-12-16 15:34:27'),(30,319,64,1,1000.00,NULL,'2024-12-16 15:34:27','2024-12-16 15:34:27'),(31,306,63,1,500.00,NULL,'2024-12-16 15:42:28','2024-12-16 15:42:28'),(32,332,63,1,500.00,NULL,'2024-12-17 11:19:02','2024-12-17 11:19:02'),(33,332,63,1,500.00,NULL,'2024-12-17 11:31:18','2024-12-17 11:31:18');
/*!40000 ALTER TABLE `refunds` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role_has_permissions`
--

DROP TABLE IF EXISTS `role_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `role_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `role_id` bigint unsigned NOT NULL,
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
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
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
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `sale_id` bigint unsigned NOT NULL,
  `item_id` bigint unsigned NOT NULL,
  `quantity` int NOT NULL,
  `price` decimal(10,2) DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `refunded_quantity` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `sale_items_sale_id_foreign` (`sale_id`),
  KEY `sale_items_item_id_foreign` (`item_id`),
  CONSTRAINT `sale_items_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE,
  CONSTRAINT `sale_items_sale_id_foreign` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=204 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sale_items`
--

LOCK TABLES `sale_items` WRITE;
/*!40000 ALTER TABLE `sale_items` DISABLE KEYS */;
INSERT INTO `sale_items` VALUES (118,301,63,1,400.00,'2024-11-26 13:15:50','2024-11-26 13:15:50',0),(119,302,63,1,400.00,'2024-11-26 17:30:56','2024-11-26 17:30:56',0),(120,302,63,1,400.00,'2024-11-26 17:30:56','2024-11-26 17:30:56',0),(121,303,63,1,400.00,'2024-11-27 07:42:53','2024-11-27 07:42:53',0),(122,303,63,1,400.00,'2024-11-27 07:42:53','2024-11-27 07:42:53',0),(123,303,63,1,400.00,'2024-11-27 07:42:53','2024-11-27 07:42:53',0),(124,303,63,1,400.00,'2024-11-27 07:42:53','2024-11-27 07:42:53',0),(125,303,63,1,400.00,'2024-11-27 07:42:53','2024-11-27 07:42:53',0),(126,303,63,1,400.00,'2024-11-27 07:42:53','2024-11-27 07:42:53',0),(127,303,63,1,400.00,'2024-11-27 07:42:53','2024-11-27 07:42:53',0),(128,303,63,1,400.00,'2024-11-27 07:42:53','2024-11-27 07:42:53',0),(129,303,64,1,800.00,'2024-11-27 07:42:53','2024-11-27 07:42:53',0),(130,303,64,1,800.00,'2024-11-27 07:42:53','2024-11-27 07:42:53',0),(131,303,64,1,800.00,'2024-11-27 07:42:53','2024-11-27 07:42:53',0),(133,304,64,1,800.00,'2024-11-27 08:56:19','2024-11-27 08:56:19',0),(135,306,63,1,400.00,'2024-11-28 14:40:48','2024-11-28 15:42:14',1),(136,306,63,1,400.00,'2024-11-28 14:40:48','2024-11-28 15:42:14',1),(137,306,63,1,400.00,'2024-11-28 14:40:48','2024-11-28 15:42:14',1),(138,306,64,1,800.00,'2024-11-28 14:40:48','2024-11-28 15:42:14',1),(139,307,67,1,180.00,'2024-12-14 20:46:40','2024-12-14 20:46:40',0),(140,309,63,1,400.00,'2024-12-14 21:14:15','2024-12-14 21:14:15',0),(141,310,64,10,800.00,'2024-12-14 21:18:30','2024-12-14 21:18:30',0),(142,310,67,2,180.00,'2024-12-14 21:18:30','2024-12-14 21:18:30',0),(143,310,63,9,400.00,'2024-12-14 21:18:30','2024-12-14 21:18:30',0),(144,311,64,21,800.00,'2024-12-14 21:25:59','2024-12-14 21:25:59',0),(145,312,64,1,800.00,'2024-12-14 21:28:08','2024-12-14 21:28:08',0),(146,312,67,2,180.00,'2024-12-14 21:28:08','2024-12-14 21:28:08',0),(147,312,63,4,400.00,'2024-12-14 21:28:08','2024-12-14 21:28:08',0),(148,313,64,4,800.00,'2024-12-14 21:30:52','2024-12-14 21:30:52',0),(149,313,63,3,400.00,'2024-12-14 21:30:52','2024-12-14 21:30:52',0),(150,315,67,24,180.00,'2024-12-14 22:18:17','2024-12-14 22:18:17',0),(151,316,67,1,180.00,'2024-12-14 22:19:41','2024-12-14 22:19:41',0),(152,317,63,24,400.00,'2024-12-14 22:22:23','2024-12-14 22:22:23',0),(153,318,63,9,400.00,'2024-12-15 08:59:17','2024-12-15 08:59:17',0),(154,319,63,3,400.00,'2024-12-15 09:03:33','2024-12-15 09:03:33',0),(155,319,64,1,800.00,'2024-12-15 09:03:33','2024-12-15 09:03:33',0),(156,320,63,1,400.00,'2024-12-15 09:41:14','2024-12-15 09:41:14',0),(157,320,67,1,180.00,'2024-12-15 09:41:14','2024-12-15 09:41:14',0),(158,321,63,6,400.00,'2024-12-15 09:46:36','2024-12-15 09:46:36',0),(159,322,64,3,800.00,'2024-12-15 09:55:42','2024-12-15 09:55:42',0),(160,322,63,11,400.00,'2024-12-15 09:55:42','2024-12-15 09:55:42',0),(161,323,64,3,800.00,'2024-12-16 07:30:36','2024-12-16 07:30:36',0),(162,324,64,3,800.00,'2024-12-16 07:45:19','2024-12-16 07:45:19',0),(163,325,64,8,800.00,'2024-12-16 07:54:55','2024-12-16 07:54:55',0),(164,325,68,37,900.00,'2024-12-16 07:54:55','2024-12-16 07:54:55',0),(165,326,63,2,400.00,'2024-12-16 09:49:53','2024-12-16 09:49:53',0),(166,328,63,1,400.00,'2024-12-16 09:52:07','2024-12-16 09:52:07',0),(167,329,63,1,400.00,'2024-12-16 10:50:30','2024-12-16 10:50:30',0),(168,329,69,2,2000.00,'2024-12-16 10:50:30','2024-12-16 10:50:30',0),(169,330,69,7,2000.00,'2024-12-16 10:52:51','2024-12-16 10:52:51',0),(170,331,68,6,900.00,'2024-12-16 15:23:56','2024-12-16 15:23:56',0),(171,332,64,1,800.00,'2024-12-17 11:18:37','2024-12-17 11:18:37',0),(172,332,63,2,400.00,'2024-12-17 11:18:37','2024-12-17 11:18:37',0),(173,332,69,1,2000.00,'2024-12-17 11:18:37','2024-12-17 11:18:37',0),(174,333,76,2,8176.96,'2024-12-17 21:10:05','2024-12-17 21:10:05',0),(175,333,75,3,176.00,'2024-12-17 21:10:05','2024-12-17 21:10:05',0),(176,333,74,1,909.09,'2024-12-17 21:10:05','2024-12-17 21:10:05',0),(177,333,73,2,33.00,'2024-12-17 21:10:05','2024-12-17 21:10:05',0),(178,333,72,2,999.00,'2024-12-17 21:10:05','2024-12-17 21:10:05',0),(179,333,69,4,2000.00,'2024-12-17 21:10:05','2024-12-17 21:10:05',0),(180,334,64,1,800.00,'2024-12-17 22:09:15','2024-12-17 22:09:15',0),(181,334,83,1,5.00,'2024-12-17 22:09:15','2024-12-17 22:09:15',0),(182,335,87,2,900.00,'2024-12-18 09:28:41','2024-12-18 09:28:41',0),(185,337,87,10,900.00,'2024-12-18 09:30:30','2024-12-18 09:30:30',0),(186,337,86,10,11.00,'2024-12-18 09:30:30','2024-12-18 09:30:30',0),(187,338,87,800,900.00,'2024-12-18 10:37:20','2024-12-18 10:37:20',0),(188,339,83,2,5.00,'2024-12-18 11:13:06','2024-12-18 11:13:06',0),(189,339,76,5,8176.96,'2024-12-18 11:13:06','2024-12-18 11:13:06',0),(190,340,76,2,8176.96,'2024-12-18 11:27:39','2024-12-18 11:27:39',0),(191,340,75,3,176.00,'2024-12-18 11:27:39','2024-12-18 11:27:39',0),(192,341,68,1,900.00,'2024-12-18 11:31:28','2024-12-18 11:31:28',0),(193,341,70,1,999.00,'2024-12-18 11:31:28','2024-12-18 11:31:28',0),(197,346,64,1,800.00,'2024-12-18 11:45:02','2024-12-18 11:45:02',0),(198,346,63,1,400.00,'2024-12-18 11:45:02','2024-12-18 11:45:02',0),(199,347,77,1,9.80,'2024-12-18 11:48:39','2024-12-18 11:48:39',0),(201,349,88,1,9000.00,'2024-12-18 12:25:35','2024-12-18 12:25:35',0),(202,350,74,2,909.09,'2024-12-20 13:08:16','2024-12-20 13:08:16',0),(203,351,64,1,800.00,'2024-12-20 13:16:48','2024-12-20 13:16:48',0);
/*!40000 ALTER TABLE `sale_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sales`
--

DROP TABLE IF EXISTS `sales`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sales` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `item_id` bigint unsigned DEFAULT NULL,
  `total_amount` decimal(8,2) NOT NULL,
  `refund_status` enum('no_refund','partial_refund','full_refund') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'no_refund',
  `sale_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `customer_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `discount_type` enum('percentage','fixed') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `discount_value` decimal(10,2) DEFAULT NULL,
  `payment_method` enum('cash','credit_card','paypal','bank_transfer') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subtotal` decimal(10,2) NOT NULL DEFAULT '0.00',
  `discount` decimal(10,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id`),
  KEY `sales_item_id_foreign` (`item_id`),
  CONSTRAINT `sales_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=352 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sales`
--

LOCK TABLES `sales` WRITE;
/*!40000 ALTER TABLE `sales` DISABLE KEYS */;
INSERT INTO `sales` VALUES (295,NULL,1000.00,'no_refund','2024-11-25 19:21:23','2024-11-25 17:21:23','2024-11-25 17:21:23',NULL,NULL,NULL,NULL,NULL,0.00,0.00),(296,NULL,290000.00,'no_refund','2024-11-25 19:24:45','2024-11-25 17:24:45','2024-11-25 17:24:45',NULL,NULL,NULL,NULL,NULL,0.00,0.00),(297,NULL,200.00,'no_refund','2024-11-25 19:32:16','2024-11-25 17:32:16','2024-11-25 17:32:16',NULL,NULL,NULL,NULL,NULL,0.00,0.00),(298,NULL,3960.00,'no_refund','2024-11-26 12:25:55','2024-11-26 10:25:55','2024-11-26 10:25:55',NULL,NULL,NULL,NULL,NULL,0.00,0.00),(299,NULL,3920.00,'no_refund','2024-11-26 13:36:21','2024-11-26 11:36:21','2024-11-26 11:36:21',NULL,NULL,NULL,NULL,NULL,0.00,0.00),(300,NULL,1180.00,'no_refund','2024-11-26 13:37:32','2024-11-26 11:37:32','2024-11-26 11:37:32',NULL,NULL,NULL,NULL,NULL,0.00,0.00),(301,NULL,400.00,'no_refund','2024-11-26 15:15:50','2024-11-26 13:15:50','2024-11-26 13:15:50',NULL,NULL,NULL,NULL,NULL,0.00,0.00),(302,NULL,800.00,'no_refund','2024-11-26 19:30:56','2024-11-26 17:30:56','2024-11-26 17:30:56',NULL,NULL,NULL,NULL,NULL,0.00,0.00),(303,NULL,5600.00,'no_refund','2024-11-27 09:42:53','2024-11-27 07:42:53','2024-11-27 07:42:53',NULL,NULL,NULL,NULL,NULL,0.00,0.00),(304,NULL,1300.00,'no_refund','2024-11-27 10:56:19','2024-11-27 08:56:19','2024-11-27 08:56:19',NULL,NULL,NULL,NULL,NULL,0.00,0.00),(305,NULL,900.00,'no_refund','2024-11-28 16:30:35','2024-11-28 14:30:35','2024-11-28 14:30:35',NULL,NULL,NULL,NULL,NULL,0.00,0.00),(306,NULL,2000.00,'partial_refund','2024-11-28 16:40:48','2024-11-28 14:40:48','2024-12-16 15:42:28',NULL,NULL,NULL,NULL,NULL,0.00,0.00),(307,NULL,180.00,'no_refund','2024-12-14 22:46:40','2024-12-14 20:46:40','2024-12-14 20:46:40',NULL,NULL,NULL,NULL,NULL,0.00,0.00),(309,NULL,400.00,'no_refund','2024-12-14 23:14:15','2024-12-14 21:14:15','2024-12-14 21:14:15',NULL,NULL,NULL,NULL,NULL,0.00,0.00),(310,NULL,11960.00,'no_refund','2024-12-14 23:18:30','2024-12-14 21:18:30','2024-12-14 21:18:30',NULL,NULL,NULL,NULL,NULL,0.00,0.00),(311,NULL,16800.00,'no_refund','2024-12-14 23:25:59','2024-12-14 21:25:59','2024-12-14 21:25:59',NULL,NULL,NULL,NULL,NULL,0.00,0.00),(312,NULL,2760.00,'no_refund','2024-12-14 23:28:08','2024-12-14 21:28:08','2024-12-14 21:28:08',NULL,NULL,NULL,NULL,NULL,0.00,0.00),(313,NULL,4400.00,'no_refund','2024-12-14 23:30:52','2024-12-14 21:30:52','2024-12-14 21:30:52',NULL,NULL,NULL,NULL,NULL,0.00,0.00),(315,NULL,4320.00,'no_refund','2024-12-15 00:18:17','2024-12-14 22:18:17','2024-12-14 22:18:17',NULL,NULL,NULL,NULL,NULL,0.00,0.00),(316,NULL,180.00,'no_refund','2024-12-15 00:19:41','2024-12-14 22:19:41','2024-12-14 22:19:41',NULL,NULL,NULL,NULL,NULL,0.00,0.00),(317,NULL,9600.00,'no_refund','2024-12-15 00:22:23','2024-12-14 22:22:23','2024-12-14 22:22:23',NULL,NULL,NULL,NULL,NULL,0.00,0.00),(318,NULL,3600.00,'partial_refund','2024-12-15 10:59:17','2024-12-15 08:59:17','2024-12-16 15:27:04',NULL,NULL,NULL,NULL,NULL,0.00,0.00),(319,NULL,2000.00,'full_refund','2024-12-15 11:03:33','2024-12-15 09:03:33','2024-12-16 15:34:27',NULL,NULL,NULL,NULL,NULL,0.00,0.00),(320,NULL,580.00,'no_refund','2024-12-15 11:41:14','2024-12-15 09:41:14','2024-12-15 09:41:14',NULL,NULL,NULL,NULL,NULL,0.00,0.00),(321,NULL,2400.00,'no_refund','2024-12-15 11:46:36','2024-12-15 09:46:36','2024-12-15 09:46:36',NULL,NULL,NULL,NULL,NULL,0.00,0.00),(322,NULL,6800.00,'no_refund','2024-12-15 11:55:42','2024-12-15 09:55:42','2024-12-15 09:55:42',NULL,NULL,NULL,NULL,NULL,0.00,0.00),(323,NULL,2400.00,'no_refund','2024-12-16 09:30:36','2024-12-16 07:30:36','2024-12-16 07:30:36',NULL,NULL,NULL,NULL,NULL,0.00,0.00),(324,NULL,2400.00,'no_refund','2024-12-16 09:45:19','2024-12-16 07:45:19','2024-12-16 07:45:19',NULL,NULL,NULL,NULL,NULL,0.00,0.00),(325,NULL,39700.00,'no_refund','2024-12-16 09:54:55','2024-12-16 07:54:55','2024-12-16 07:54:55',NULL,NULL,NULL,NULL,NULL,0.00,0.00),(326,NULL,800.00,'no_refund','2024-12-16 11:49:53','2024-12-16 09:49:53','2024-12-16 09:49:53',NULL,NULL,NULL,NULL,NULL,0.00,0.00),(328,NULL,400.00,'full_refund','2024-12-16 11:52:07','2024-12-16 09:52:07','2024-12-16 15:26:50',NULL,NULL,NULL,NULL,NULL,0.00,0.00),(329,NULL,4400.00,'full_refund','2024-12-16 12:50:30','2024-12-16 10:50:30','2024-12-16 15:26:27',NULL,NULL,NULL,NULL,NULL,0.00,0.00),(330,NULL,14000.00,'partial_refund','2024-12-16 12:52:51','2024-12-16 10:52:51','2024-12-16 15:27:51',NULL,NULL,NULL,NULL,NULL,0.00,0.00),(331,NULL,5400.00,'partial_refund','2024-12-16 17:23:56','2024-12-16 15:23:56','2024-12-16 15:25:58',NULL,NULL,NULL,NULL,NULL,0.00,0.00),(332,NULL,3600.00,'partial_refund','2024-12-17 13:18:37','2024-12-17 11:18:37','2024-12-17 11:19:02',NULL,NULL,NULL,NULL,NULL,0.00,0.00),(333,NULL,27855.01,'no_refund','2024-12-17 23:10:05','2024-12-17 21:10:05','2024-12-17 21:10:05',NULL,NULL,NULL,NULL,NULL,0.00,0.00),(334,NULL,805.00,'no_refund','2024-12-18 00:09:15','2024-12-17 22:09:15','2024-12-17 22:09:15',NULL,NULL,NULL,NULL,NULL,0.00,0.00),(335,NULL,1800.00,'no_refund','2024-12-18 11:28:41','2024-12-18 09:28:41','2024-12-18 09:28:41',NULL,NULL,NULL,NULL,NULL,0.00,0.00),(337,NULL,9110.00,'no_refund','2024-12-18 11:30:30','2024-12-18 09:30:30','2024-12-18 09:30:30',NULL,NULL,NULL,NULL,NULL,0.00,0.00),(338,NULL,720000.00,'no_refund','2024-12-18 12:37:20','2024-12-18 10:37:20','2024-12-18 10:37:20',NULL,NULL,NULL,NULL,NULL,0.00,0.00),(339,NULL,40894.80,'no_refund','2024-12-18 13:13:06','2024-12-18 11:13:06','2024-12-18 11:13:06',NULL,NULL,NULL,NULL,NULL,0.00,0.00),(340,NULL,16881.92,'no_refund','2024-12-18 13:27:39','2024-12-18 11:27:39','2024-12-18 11:27:39',NULL,NULL,NULL,NULL,NULL,0.00,0.00),(341,NULL,1899.00,'no_refund','2024-12-18 13:31:28','2024-12-18 11:31:28','2024-12-18 11:31:28',NULL,NULL,NULL,NULL,NULL,0.00,0.00),(346,NULL,1200.00,'no_refund','2024-12-18 13:45:02','2024-12-18 11:45:02','2024-12-18 11:45:02',NULL,NULL,NULL,NULL,NULL,0.00,0.00),(347,NULL,0.00,'no_refund','2024-12-18 13:48:39','2024-12-18 11:48:39','2024-12-18 11:48:39','KI','01292019191',NULL,0.00,NULL,0.00,0.00),(349,NULL,9000.00,'no_refund','2024-12-18 14:25:35','2024-12-18 12:25:35','2024-12-18 12:25:35',NULL,NULL,NULL,NULL,NULL,0.00,0.00),(350,NULL,1818.18,'no_refund','2024-12-20 15:08:16','2024-12-20 13:08:16','2024-12-20 13:08:16',NULL,NULL,NULL,NULL,NULL,0.00,0.00),(351,NULL,800.00,'no_refund','2024-12-20 15:16:48','2024-12-20 13:16:48','2024-12-20 13:16:48',NULL,NULL,NULL,NULL,NULL,0.00,0.00);
/*!40000 ALTER TABLE `sales` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
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
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sizes`
--

LOCK TABLES `sizes` WRITE;
/*!40000 ALTER TABLE `sizes` DISABLE KEYS */;
INSERT INTO `sizes` VALUES (1,'xs','clothes','2024-11-25 11:47:49','2024-11-25 11:47:49'),(2,'s','clothes','2024-11-25 12:09:45','2024-11-25 12:09:45'),(3,'m','clothes','2024-11-25 12:09:51','2024-11-25 12:09:51'),(4,'l','clothes','2024-11-25 12:09:56','2024-11-25 12:09:56'),(5,'xl','clothes','2024-11-25 12:10:00','2024-11-25 12:10:00'),(6,'40','shoes','2024-11-25 16:15:40','2024-11-25 16:15:40'),(7,'41','shoes','2024-11-25 16:15:49','2024-11-25 16:15:49');
/*!40000 ALTER TABLE `sizes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'lara_admin','lara_admin@admin.com',NULL,'$2y$12$7V6udZJpN4rMfwFnvtluf.xqTBLzw1A1vNP3emhcwS8TApB10kFr6',NULL,'2024-09-27 10:34:05','2024-09-27 10:34:05'),(2,'Maher Admin','madmin@lhub.com',NULL,'$2y$12$zqgL/xtP8IaAVZCC2JVtEuRYwgaZOLDwcZ.4XAT2KEXQwUuqhYnd6',NULL,'2024-11-22 11:37:23','2024-11-22 11:37:23'),(3,'admin@localhub.com','admin@localhub.com',NULL,'$2y$12$juCVm4SiVKvuVKcYlaAVju.sOeMvLcm5ECxI6Z6ehn3At/8SILR66',NULL,'2024-11-22 11:39:23','2024-11-22 11:39:23'),(6,'Non1','non1@lhub.com',NULL,'$2y$12$VfKakXMLcBRl9FEfRQIqBeawT6Dws5oHciysCx0dyTA18mEW7AZTC',NULL,'2024-12-14 22:08:38','2024-12-14 22:08:38');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2024-12-20 17:30:28
