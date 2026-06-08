-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: medicine_delivery
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
-- Table structure for table `addresses`
--

DROP TABLE IF EXISTS `addresses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `addresses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `label` varchar(50) DEFAULT 'Home',
  `address_line1` text NOT NULL,
  `address_line2` text DEFAULT NULL,
  `city` varchar(50) DEFAULT NULL,
  `state` varchar(50) DEFAULT NULL,
  `pincode` varchar(10) DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `is_default` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `addresses_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `addresses`
--

LOCK TABLES `addresses` WRITE;
/*!40000 ALTER TABLE `addresses` DISABLE KEYS */;
INSERT INTO `addresses` VALUES (3,3,'Phaltan','Phaltan City, Phaltan','','Phaltan','Maharashtra','415523',NULL,NULL,1,'2026-04-27 14:36:41',NULL),(4,3,'Phaltan','Station Area, Phaltan',NULL,'Phaltan','Maharashtra',NULL,NULL,NULL,0,'2026-04-27 14:50:07',NULL),(5,13,'Building A','Phaltan City, Phaltan',NULL,'Phaltan','Maharashtra','415523',NULL,NULL,0,'2026-05-01 05:17:11',NULL),(6,14,'Home','Phaltan City, Phaltan',NULL,'Phaltan','Maharashtra','94016',NULL,NULL,0,'2026-05-17 06:21:24',NULL);
/*!40000 ALTER TABLE `addresses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `agent_locations`
--

DROP TABLE IF EXISTS `agent_locations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `agent_locations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `agent_id` int(11) NOT NULL,
  `latitude` decimal(10,8) NOT NULL,
  `longitude` decimal(11,8) NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `agent_id` (`agent_id`),
  CONSTRAINT `agent_locations_ibfk_1` FOREIGN KEY (`agent_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `agent_locations`
--

LOCK TABLES `agent_locations` WRITE;
/*!40000 ALTER TABLE `agent_locations` DISABLE KEYS */;
INSERT INTO `agent_locations` VALUES (1,4,17.98680000,74.43790000,'2026-04-27 13:52:21'),(2,4,18.51200000,73.83590000,'2026-05-20 08:57:44'),(3,4,18.51200000,73.83590000,'2026-05-20 08:58:14'),(4,4,18.51200000,73.83590000,'2026-05-20 08:58:42'),(5,4,18.51200000,73.83590000,'2026-05-20 08:59:11'),(6,4,18.51200000,73.83590000,'2026-05-20 09:00:11'),(7,4,18.51200000,73.83590000,'2026-05-20 09:00:41'),(8,4,18.51200000,73.83590000,'2026-05-20 09:01:15'),(9,4,18.51200000,73.83590000,'2026-05-20 09:16:15'),(10,4,18.55140000,73.82190000,'2026-05-20 13:20:04'),(11,4,18.55140000,73.82190000,'2026-05-20 13:20:33'),(12,4,18.55140000,73.82190000,'2026-05-20 13:21:03'),(13,4,18.55140000,73.82190000,'2026-05-20 13:21:34'),(14,4,18.55140000,73.82190000,'2026-05-20 13:22:07'),(15,4,18.55140000,73.82190000,'2026-05-20 13:22:34'),(16,4,18.55140000,73.82190000,'2026-05-20 13:23:05'),(17,4,18.55140000,73.82190000,'2026-05-20 13:23:34'),(18,4,18.55140000,73.82190000,'2026-05-20 13:24:31'),(19,4,18.55140000,73.82190000,'2026-05-20 13:25:05'),(20,4,18.55140000,73.82190000,'2026-05-20 13:26:15'),(21,4,18.55140000,73.82190000,'2026-05-20 13:26:41'),(22,4,18.55140000,73.82190000,'2026-05-20 13:27:14'),(23,4,18.55140000,73.82190000,'2026-05-20 13:27:40'),(24,4,18.55140000,73.82190000,'2026-05-20 13:28:16'),(25,4,18.55140000,73.82190000,'2026-05-20 13:28:44'),(26,4,18.55140000,73.82190000,'2026-05-20 13:29:30');
/*!40000 ALTER TABLE `agent_locations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `audit_logs`
--

DROP TABLE IF EXISTS `audit_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `audit_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `target_model` varchar(100) DEFAULT NULL,
  `target_id` int(11) DEFAULT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`payload`)),
  `metadata` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=90 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `audit_logs`
--

LOCK TABLES `audit_logs` WRITE;
/*!40000 ALTER TABLE `audit_logs` DISABLE KEYS */;
INSERT INTO `audit_logs` VALUES (1,1,'GLOBAL_PULSE_TRIGGERED','System',0,'{\"sla_breaches\":0,\"inventory_reconciliation\":0,\"timestamp\":\"2026-04-26 06:55:39\"}',NULL,'2026-04-26 04:55:39'),(2,1,'ORDER_ASSIGNED','Order',1,'{\"partner_id\":4}',NULL,'2026-04-26 05:13:59'),(3,1,'PERMISSIONS_UPDATED','Role',16,'\"Access Control Matrix redefined\"',NULL,'2026-04-26 07:50:03'),(4,1,'PERMISSIONS_UPDATED','Role',16,'\"Access Control Matrix redefined\"',NULL,'2026-04-26 07:50:08'),(5,1,'PERMISSIONS_UPDATED','Role',16,'\"Access Control Matrix redefined\"',NULL,'2026-04-26 07:50:08'),(6,1,'PERMISSIONS_UPDATED','Role',16,'\"Access Control Matrix redefined\"',NULL,'2026-04-26 07:50:11'),(7,1,'PERMISSIONS_UPDATED','Role',16,'\"Access Control Matrix redefined\"',NULL,'2026-04-26 07:50:12'),(8,1,'PERMISSIONS_UPDATED','Role',16,'\"Access Control Matrix redefined\"',NULL,'2026-04-26 07:50:12'),(9,1,'PERMISSIONS_UPDATED','Role',16,'\"Access Control Matrix redefined\"',NULL,'2026-04-26 07:50:15'),(10,1,'PERMISSIONS_UPDATED','Role',16,'\"Access Control Matrix redefined\"',NULL,'2026-04-26 07:50:19'),(11,1,'PERMISSIONS_UPDATED','Role',16,'\"Access Control Matrix redefined\"',NULL,'2026-04-26 07:50:19'),(12,1,'PERMISSIONS_UPDATED','Role',16,'\"Access Control Matrix redefined\"',NULL,'2026-04-26 07:51:57'),(13,1,'PERMISSIONS_UPDATED','Role',16,'\"Access Control Matrix redefined\"',NULL,'2026-04-26 07:51:57'),(14,1,'PERMISSIONS_UPDATED','Role',16,'\"Access Control Matrix redefined\"',NULL,'2026-04-26 07:51:58'),(15,1,'PERMISSIONS_UPDATED','Role',16,'\"Access Control Matrix redefined\"',NULL,'2026-04-26 07:51:58'),(16,1,'PERMISSIONS_UPDATED','Role',16,'\"Access Control Matrix redefined\"',NULL,'2026-04-26 07:51:59'),(17,1,'PERMISSIONS_UPDATED','Role',16,'\"Access Control Matrix redefined\"',NULL,'2026-04-26 07:51:59'),(18,1,'PERMISSIONS_UPDATED','Role',16,'\"Access Control Matrix redefined\"',NULL,'2026-04-26 07:51:59'),(19,1,'PERMISSIONS_UPDATED','Role',16,'\"Access Control Matrix redefined\"',NULL,'2026-04-26 07:52:01'),(20,1,'PERMISSIONS_UPDATED','Role',16,'\"Access Control Matrix redefined\"',NULL,'2026-04-26 07:52:01'),(21,1,'PERMISSIONS_UPDATED','Role',16,'\"Access Control Matrix redefined\"',NULL,'2026-04-26 07:52:02'),(22,1,'PERMISSIONS_UPDATED','Role',16,'\"Access Control Matrix redefined\"',NULL,'2026-04-26 07:52:02'),(23,1,'PERMISSIONS_UPDATED','Role',16,'\"Access Control Matrix redefined\"',NULL,'2026-04-26 07:52:02'),(24,1,'PERMISSIONS_UPDATED','Role',16,'\"Access Control Matrix redefined\"',NULL,'2026-04-26 07:52:03'),(25,1,'PERMISSIONS_UPDATED','Role',16,'\"Access Control Matrix redefined\"',NULL,'2026-04-26 07:52:03'),(26,1,'PERMISSIONS_UPDATED','Role',16,'\"Access Control Matrix redefined\"',NULL,'2026-04-26 07:52:04'),(27,1,'PERMISSIONS_UPDATED','Role',16,'\"Access Control Matrix redefined\"',NULL,'2026-04-26 07:52:04'),(28,1,'PERMISSIONS_UPDATED','Role',16,'\"Access Control Matrix redefined\"',NULL,'2026-04-26 07:52:04'),(29,1,'PERMISSIONS_UPDATED','Role',16,'\"Access Control Matrix redefined\"',NULL,'2026-04-26 07:52:04'),(30,1,'PERMISSIONS_UPDATED','Role',16,'\"Access Control Matrix redefined\"',NULL,'2026-04-26 07:52:05'),(31,1,'PERMISSIONS_UPDATED','Role',16,'\"Access Control Matrix redefined\"',NULL,'2026-04-26 07:52:05'),(32,1,'PERMISSIONS_UPDATED','Role',16,'\"Access Control Matrix redefined\"',NULL,'2026-04-26 07:52:05'),(33,1,'PERMISSIONS_UPDATED','Role',16,'\"Access Control Matrix redefined\"',NULL,'2026-04-26 07:52:05'),(34,1,'PERMISSIONS_UPDATED','Role',16,'\"Access Control Matrix redefined\"',NULL,'2026-04-26 07:52:06'),(35,1,'PERMISSIONS_UPDATED','Role',16,'\"Access Control Matrix redefined\"',NULL,'2026-04-26 07:52:06'),(36,1,'PERMISSIONS_UPDATED','Role',16,'\"Access Control Matrix redefined\"',NULL,'2026-04-26 07:52:06'),(37,1,'PERMISSIONS_UPDATED','Role',16,'\"Access Control Matrix redefined\"',NULL,'2026-04-26 07:52:07'),(38,1,'PERMISSIONS_UPDATED','Role',16,'\"Access Control Matrix redefined\"',NULL,'2026-04-26 07:52:07'),(39,1,'PERMISSIONS_UPDATED','Role',16,'\"Access Control Matrix redefined\"',NULL,'2026-04-26 07:52:08'),(40,1,'PERMISSIONS_UPDATED','Role',16,'\"Access Control Matrix redefined\"',NULL,'2026-04-26 07:52:09'),(41,1,'PERMISSIONS_UPDATED','Role',16,'\"Access Control Matrix redefined\"',NULL,'2026-04-26 07:52:10'),(42,1,'PERMISSIONS_UPDATED','Role',16,'\"Access Control Matrix redefined\"',NULL,'2026-04-26 07:52:11'),(43,1,'PERMISSIONS_UPDATED','Role',16,'\"Access Control Matrix redefined\"',NULL,'2026-04-26 07:52:11'),(44,1,'PERMISSIONS_UPDATED','Role',16,'\"Access Control Matrix redefined\"',NULL,'2026-04-26 07:52:12'),(45,1,'PERMISSIONS_UPDATED','Role',16,'\"Access Control Matrix redefined\"',NULL,'2026-04-26 07:52:12'),(46,1,'PERMISSIONS_UPDATED','Role',16,'\"Access Control Matrix redefined\"',NULL,'2026-04-26 07:52:13'),(47,1,'PERMISSIONS_UPDATED','Role',16,'\"Access Control Matrix redefined\"',NULL,'2026-04-26 07:52:13'),(48,1,'PERMISSIONS_UPDATED','Role',16,'\"Access Control Matrix redefined\"',NULL,'2026-04-26 07:52:13'),(49,1,'PERMISSIONS_UPDATED','Role',16,'\"Access Control Matrix redefined\"',NULL,'2026-04-26 07:52:14'),(50,1,'PERMISSIONS_UPDATED','Role',16,'\"Access Control Matrix redefined\"',NULL,'2026-04-26 07:52:14'),(51,1,'PERMISSIONS_UPDATED','Role',16,'\"Access Control Matrix redefined\"',NULL,'2026-04-26 07:52:14'),(52,1,'PERMISSIONS_UPDATED','Role',16,'\"Access Control Matrix redefined\"',NULL,'2026-04-26 07:52:14'),(53,1,'PERMISSIONS_UPDATED','Role',16,'\"Access Control Matrix redefined\"',NULL,'2026-04-26 07:52:14'),(54,1,'PERMISSIONS_UPDATED','Role',16,'\"Access Control Matrix redefined\"',NULL,'2026-04-26 07:52:15'),(55,1,'PERMISSIONS_UPDATED','Role',16,'\"Access Control Matrix redefined\"',NULL,'2026-04-26 07:52:15'),(56,1,'PERMISSIONS_UPDATED','Role',16,'\"Access Control Matrix redefined\"',NULL,'2026-04-26 07:52:15'),(57,1,'PERMISSIONS_UPDATED','Role',16,'\"Access Control Matrix redefined\"',NULL,'2026-04-26 08:01:42'),(58,1,'PERMISSIONS_UPDATED','Role',16,'\"Access Control Matrix redefined\"',NULL,'2026-04-26 08:01:43'),(59,1,'PERMISSIONS_UPDATED','Role',16,'\"Access Control Matrix redefined\"',NULL,'2026-04-26 08:01:43'),(60,1,'PERMISSIONS_UPDATED','Role',16,'\"Access Control Matrix redefined\"',NULL,'2026-04-26 08:01:51'),(61,1,'PERMISSIONS_UPDATED','Role',16,'\"Access Control Matrix redefined\"',NULL,'2026-04-26 08:01:52'),(62,1,'PERMISSIONS_UPDATED','Role',16,'\"Access Control Matrix redefined\"',NULL,'2026-04-26 08:01:53'),(63,1,'PERMISSIONS_UPDATED','Role',16,'\"Access Control Matrix redefined\"',NULL,'2026-04-26 08:01:53'),(64,1,'PERMISSIONS_UPDATED','Role',16,'\"Access Control Matrix redefined\"',NULL,'2026-04-26 08:01:53'),(65,1,'PERMISSIONS_UPDATED','Role',16,'\"Access Control Matrix redefined\"',NULL,'2026-04-26 08:01:54'),(66,1,'PERMISSIONS_UPDATED','Role',16,'\"Access Control Matrix redefined\"',NULL,'2026-04-26 08:01:55'),(67,1,'GLOBAL_PULSE_TRIGGERED','System',0,'{\"sla_breaches\":0,\"inventory_reconciliation\":0,\"timestamp\":\"2026-04-26 10:45:00\"}',NULL,'2026-04-26 08:45:00'),(68,1,'KYC_STATUS_UPDATED','KYC',1,'{\"status\":\"verified\"}',NULL,'2026-04-26 14:29:25'),(69,2,'KYC_SUBMITTED','KycDocument',2,'{\"type\":\"pharmacy_license\"}',NULL,'2026-04-27 04:18:34'),(70,2,'KYC_SUBMITTED','KycDocument',3,'{\"type\":\"pharmacy_license\"}',NULL,'2026-04-27 04:18:34'),(71,1,'ORDER_ASSIGNED','Order',17,'{\"partner_id\":4}',NULL,'2026-05-20 15:04:17'),(72,1,'ORDER_ASSIGNED','Order',15,'{\"partner_id\":7}',NULL,'2026-05-20 15:04:26'),(73,1,'ORDER_ASSIGNED','Order',15,'{\"partner_id\":7}',NULL,'2026-05-20 15:04:26'),(74,2,'KYC_SUBMITTED','KycDocument',4,'{\"type\":\"pharmacy_license\"}',NULL,'2026-05-22 08:33:37'),(75,2,'KYC_SUBMITTED','KycDocument',5,'{\"type\":\"aadhar\"}',NULL,'2026-05-22 08:34:08'),(76,1,'KYC_STATUS_UPDATED','KYC',5,'{\"status\":\"verified\"}',NULL,'2026-05-22 13:15:52'),(77,1,'KYC_STATUS_UPDATED','KYC',5,'{\"status\":\"verified\"}',NULL,'2026-05-22 13:15:53'),(78,1,'KYC_STATUS_UPDATED','KYC',5,'{\"status\":\"verified\"}',NULL,'2026-05-22 13:15:54'),(79,1,'KYC_STATUS_UPDATED','KYC',5,'{\"status\":\"verified\"}',NULL,'2026-05-22 13:15:54'),(80,1,'KYC_STATUS_UPDATED','KYC',5,'{\"status\":\"verified\"}',NULL,'2026-05-22 13:15:54'),(81,1,'KYC_STATUS_UPDATED','KYC',4,'{\"status\":\"verified\"}',NULL,'2026-05-22 13:16:00'),(82,1,'KYC_STATUS_UPDATED','KYC',4,'{\"status\":\"verified\"}',NULL,'2026-05-22 13:16:00'),(83,1,'KYC_STATUS_UPDATED','KYC',3,'{\"status\":\"verified\"}',NULL,'2026-05-22 13:16:04'),(84,1,'KYC_STATUS_UPDATED','KYC',2,'{\"status\":\"verified\"}',NULL,'2026-05-22 13:16:07'),(85,1,'KYC_STATUS_UPDATED','KYC',2,'{\"status\":\"verified\"}',NULL,'2026-05-22 13:16:08'),(86,1,'MEDICINE_VETTED','Medicine',202,'{\"status\":\"approved\"}',NULL,'2026-05-22 14:28:12'),(87,1,'ORDER_ASSIGNED','Order',16,'{\"partner_id\":4}',NULL,'2026-05-23 04:30:51'),(88,1,'GLOBAL_PULSE_TRIGGERED','System',0,'{\"sla_breaches\":8,\"inventory_reconciliation\":0,\"timestamp\":\"2026-05-23 07:10:11\"}',NULL,'2026-05-23 05:10:11'),(89,1,'GLOBAL_PULSE_TRIGGERED','System',0,'{\"sla_breaches\":0,\"inventory_reconciliation\":0,\"timestamp\":\"2026-05-23 07:10:20\"}',NULL,'2026-05-23 05:10:20');
/*!40000 ALTER TABLE `audit_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `banners`
--

DROP TABLE IF EXISTS `banners`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `banners` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `image_url` varchar(255) NOT NULL,
  `link` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `banners`
--

LOCK TABLES `banners` WRITE;
/*!40000 ALTER TABLE `banners` DISABLE KEYS */;
INSERT INTO `banners` VALUES (1,'Express Delivery Now Live!','/assets/images/banners/express.png',NULL,'active','2026-04-25 15:33:48'),(2,'Save 20% on Health Supplements','/assets/images/banners/sale.png',NULL,'active','2026-04-25 15:33:48');
/*!40000 ALTER TABLE `banners` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `blogs`
--

DROP TABLE IF EXISTS `blogs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `blogs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `cover_image` varchar(255) DEFAULT NULL,
  `author_name` varchar(100) DEFAULT 'MediMitra Clinical Team',
  `status` enum('draft','published') DEFAULT 'published',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `blogs`
--

LOCK TABLES `blogs` WRITE;
/*!40000 ALTER TABLE `blogs` DISABLE KEYS */;
INSERT INTO `blogs` VALUES (1,'Managing Hypertension with Clinical Precision','managing-hypertension','<p>Hypertension, commonly known as high blood pressure, is a condition...</p>','','MediMitra Clinical Team','published','2026-05-11 08:31:41','2026-05-11 08:31:41'),(2,'Understanding Antibiotic Resistance','antibiotic-resistance','<p>Antibiotic resistance occurs when bacteria change in response to the use of these medicines...</p>','','MediMitra Clinical Team','published','2026-05-11 08:31:41','2026-05-11 08:31:41');
/*!40000 ALTER TABLE `blogs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `brands`
--

DROP TABLE IF EXISTS `brands`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `brands` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `status` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `hq` varchar(255) DEFAULT 'Phaltan, India',
  `type` varchar(100) DEFAULT 'MNC',
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `brands`
--

LOCK TABLES `brands` WRITE;
/*!40000 ALTER TABLE `brands` DISABLE KEYS */;
INSERT INTO `brands` VALUES (1,'Cipla','cipla',1,'2026-04-25 15:33:48','Phaltan, India','MNC'),(2,'Sun Pharma','sun-pharma',1,'2026-04-25 15:33:48','Phaltan, India','MNC'),(3,'Abbott','abbott',1,'2026-04-25 15:33:48','Chicago, USA','MNC'),(4,'Mankind','mankind',1,'2026-04-25 15:33:48','New Delhi, India','Domestic'),(5,'Pfizer','pfizer',1,'2026-04-26 04:37:34','New York, USA','MNC'),(6,'Dr. Reddys','dr.-reddys',1,'2026-04-26 04:37:34','Hyderabad, India','MNC'),(7,'Glenmark','glenmark',1,'2026-04-26 04:37:34','Phaltan, India','Generic'),(8,'Himalaya','himalaya',1,'2026-04-26 04:37:34','Bengaluru, India','Ayurvedic'),(9,'Zydus Cadila','zydus-cadila',1,'2026-04-26 04:37:34','Ahmedabad, India','MNC'),(10,'Patanjali','patanjali',1,'2026-04-26 05:49:07','Bjp-Office','Ayurvedic'),(11,'Local Brand Test','',1,'2026-04-27 07:21:26','Phaltan, India','Local');
/*!40000 ALTER TABLE `brands` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cart_items`
--

DROP TABLE IF EXISTS `cart_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cart_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `medicine_id` int(11) NOT NULL,
  `quantity` int(11) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `medicine_id` (`medicine_id`),
  CONSTRAINT `cart_items_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cart_items_ibfk_2` FOREIGN KEY (`medicine_id`) REFERENCES `medicines` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cart_items`
--

LOCK TABLES `cart_items` WRITE;
/*!40000 ALTER TABLE `cart_items` DISABLE KEYS */;
INSERT INTO `cart_items` VALUES (35,13,193,1,'2026-05-01 05:15:18');
/*!40000 ALTER TABLE `cart_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `slug` varchar(100) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `status` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (1,NULL,'Tablets & Capsules',NULL,'tablets-capsules','/assets/images/medicines/tablet.png',1,'2026-04-25 15:33:48'),(2,NULL,'Syrups & Liquids',NULL,'syrups-liquids','/assets/images/medicines/syrup.png',1,'2026-04-25 15:33:48'),(3,NULL,'Oral Drops',NULL,'oral-drops','/assets/images/medicines/syrup.png',1,'2026-04-25 15:33:48'),(4,NULL,'Topicals (Creams, Gels)',NULL,'topicals','/assets/images/medicines/ointment.png',1,'2026-04-25 15:33:48'),(5,NULL,'Injections & Critical Care',NULL,'injections-critical','/assets/images/medicines/device.png',1,'2026-04-25 15:33:48'),(6,NULL,'Inhalers & Respiratory',NULL,'inhalers-respiratory','/assets/images/medicines/device.png',1,'2026-04-25 15:33:48'),(7,NULL,'Eye Care',NULL,'eye-care','/assets/images/medicines/ointment.png',1,'2026-04-25 15:33:48'),(8,NULL,'Ear & Nasal Products',NULL,'ear-nasal','/assets/images/medicines/syrup.png',1,'2026-04-25 15:33:48'),(9,NULL,'Medical Devices',NULL,'medical-devices','/assets/images/medicines/device.png',1,'2026-04-25 15:33:48'),(10,NULL,'Diagnostic Kits',NULL,'diagnostic-kits','/assets/images/medicines/device.png',1,'2026-04-25 15:33:48'),(11,NULL,'Surgical Supplies',NULL,'surgical-supplies','/assets/images/medicines/device.png',1,'2026-04-25 15:33:48'),(12,NULL,'First Aid',NULL,'first-aid','/assets/images/medicines/herbal.png',1,'2026-04-25 15:33:48'),(13,NULL,'Health Supplements & OTC',NULL,'health-supplements-otc','/assets/images/medicines/tablet.png',1,'2026-04-25 15:33:48'),(14,NULL,'Ayurvedic & Herbal',NULL,'ayurvedic-herbal','/assets/images/medicines/herbal.png',1,'2026-04-25 15:33:48'),(15,NULL,'Homeopathy',NULL,'homeopathy','/assets/images/medicines/herbal.png',1,'2026-04-25 15:33:48'),(16,NULL,'Personal Care & Hygiene',NULL,'personal-care-hygiene','/assets/images/medicines/ointment.png',1,'2026-04-25 15:33:48'),(17,NULL,'Other / Miscellaneous',NULL,'other-miscellaneous','/assets/images/medicines/tablet.png',1,'2026-04-25 15:33:48'),(18,NULL,'Analgesics','Pain relief and anti-inflammatory medications.','',NULL,1,'2026-04-26 05:37:19');
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `clinical_recommendations`
--

DROP TABLE IF EXISTS `clinical_recommendations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `clinical_recommendations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `medicine_id` int(11) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `score` decimal(5,2) DEFAULT 0.00,
  `reason` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `medicine_id` (`medicine_id`),
  CONSTRAINT `clinical_recommendations_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `clinical_recommendations_ibfk_2` FOREIGN KEY (`medicine_id`) REFERENCES `medicines` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `clinical_recommendations`
--

LOCK TABLES `clinical_recommendations` WRITE;
/*!40000 ALTER TABLE `clinical_recommendations` DISABLE KEYS */;
/*!40000 ALTER TABLE `clinical_recommendations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `delivery_methods`
--

DROP TABLE IF EXISTS `delivery_methods`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `delivery_methods` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `display_name` varchar(100) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT 0.00,
  `estimated_time` varchar(100) DEFAULT NULL,
  `priority` int(11) DEFAULT 1,
  `sla_hours` int(11) DEFAULT 24,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `delivery_methods`
--

LOCK TABLES `delivery_methods` WRITE;
/*!40000 ALTER TABLE `delivery_methods` DISABLE KEYS */;
INSERT INTO `delivery_methods` VALUES (1,'standard','Standard Delivery',25.00,'2-3 Days',1,72),(2,'express','Express Delivery',80.00,'Same Day',2,24),(3,'emergency','Emergency Delivery',70.00,'2 Hours',3,2);
/*!40000 ALTER TABLE `delivery_methods` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `delivery_payouts`
--

DROP TABLE IF EXISTS `delivery_payouts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `delivery_payouts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rider_id` int(11) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `status` varchar(20) DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `delivery_payouts`
--

LOCK TABLES `delivery_payouts` WRITE;
/*!40000 ALTER TABLE `delivery_payouts` DISABLE KEYS */;
/*!40000 ALTER TABLE `delivery_payouts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `delivery_profiles`
--

DROP TABLE IF EXISTS `delivery_profiles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `delivery_profiles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `dob` date DEFAULT NULL,
  `vehicle_type` enum('cycle','bike','scooter','car') DEFAULT NULL,
  `vehicle_number` varchar(50) DEFAULT NULL,
  `zone` varchar(100) DEFAULT NULL,
  `license_number` varchar(50) DEFAULT NULL,
  `selfie_image` varchar(255) DEFAULT NULL,
  `aadhaar_number` varchar(20) DEFAULT NULL,
  `pan_number` varchar(20) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `bank_name` varchar(100) DEFAULT NULL,
  `account_number` varchar(50) DEFAULT NULL,
  `ifsc_code` varchar(20) DEFAULT NULL,
  `current_address` text DEFAULT NULL,
  `permanent_address` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `delivery_profiles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `delivery_profiles`
--

LOCK TABLES `delivery_profiles` WRITE;
/*!40000 ALTER TABLE `delivery_profiles` DISABLE KEYS */;
INSERT INTO `delivery_profiles` VALUES (1,7,NULL,'bike','MH-12-TEST','Phaltan Hub',NULL,NULL,NULL,NULL,'active','2026-04-26 13:37:52','ICICI','112233','ICIC001',NULL,NULL),(2,10,NULL,'bike','MH-12-AB-1234','Phaltan Hub','DL-99999',NULL,NULL,NULL,'active','2026-04-26 13:38:53','SBI','0099887766','SBIN0001234','Kothrud, Pune','Mumbai, Maharashtra'),(3,4,NULL,'','MH-01-ED-1234','Phaltan Hub','L-MUM-98765',NULL,NULL,NULL,'inactive','2026-04-26 15:22:42',NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `delivery_profiles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `delivery_reviews`
--

DROP TABLE IF EXISTS `delivery_reviews`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `delivery_reviews` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `rider_id` int(11) NOT NULL,
  `rating` tinyint(4) NOT NULL CHECK (`rating` between 1 and 5),
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `order_id` (`order_id`),
  KEY `user_id` (`user_id`),
  KEY `rider_id` (`rider_id`),
  CONSTRAINT `delivery_reviews_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `delivery_reviews_ibfk_2` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `delivery_reviews_ibfk_3` FOREIGN KEY (`rider_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `delivery_reviews`
--

LOCK TABLES `delivery_reviews` WRITE;
/*!40000 ALTER TABLE `delivery_reviews` DISABLE KEYS */;
/*!40000 ALTER TABLE `delivery_reviews` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `drug_interactions`
--

DROP TABLE IF EXISTS `drug_interactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `drug_interactions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `medicine_a_id` int(11) NOT NULL,
  `medicine_b_id` int(11) NOT NULL,
  `severity` enum('low','moderate','severe') NOT NULL,
  `warning_text` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `medicine_a_id` (`medicine_a_id`),
  KEY `medicine_b_id` (`medicine_b_id`),
  CONSTRAINT `drug_interactions_ibfk_1` FOREIGN KEY (`medicine_a_id`) REFERENCES `medicines` (`id`) ON DELETE CASCADE,
  CONSTRAINT `drug_interactions_ibfk_2` FOREIGN KEY (`medicine_b_id`) REFERENCES `medicines` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `drug_interactions`
--

LOCK TABLES `drug_interactions` WRITE;
/*!40000 ALTER TABLE `drug_interactions` DISABLE KEYS */;
/*!40000 ALTER TABLE `drug_interactions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `failed_jobs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `health_metrics`
--

DROP TABLE IF EXISTS `health_metrics`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `health_metrics` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `metric_type` varchar(50) NOT NULL,
  `value_1` decimal(10,2) NOT NULL,
  `value_2` decimal(10,2) DEFAULT NULL,
  `unit` varchar(20) DEFAULT NULL,
  `reading_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `health_metrics_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `health_metrics`
--

LOCK TABLES `health_metrics` WRITE;
/*!40000 ALTER TABLE `health_metrics` DISABLE KEYS */;
/*!40000 ALTER TABLE `health_metrics` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inventory_logs`
--

DROP TABLE IF EXISTS `inventory_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `inventory_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `medicine_id` int(11) NOT NULL,
  `vendor_id` int(11) NOT NULL,
  `change_qty` int(11) NOT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `medicine_id` (`medicine_id`),
  KEY `vendor_id` (`vendor_id`),
  CONSTRAINT `inventory_logs_ibfk_1` FOREIGN KEY (`medicine_id`) REFERENCES `medicines` (`id`) ON DELETE CASCADE,
  CONSTRAINT `inventory_logs_ibfk_2` FOREIGN KEY (`vendor_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventory_logs`
--

LOCK TABLES `inventory_logs` WRITE;
/*!40000 ALTER TABLE `inventory_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `inventory_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `kyc_documents`
--

DROP TABLE IF EXISTS `kyc_documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kyc_documents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `document_type` enum('aadhar','license','pharmacy_license','gst','pan') NOT NULL,
  `document_number` varchar(50) NOT NULL,
  `document_image` varchar(255) NOT NULL,
  `status` enum('pending','verified','rejected') DEFAULT 'pending',
  `rejection_reason` text DEFAULT NULL,
  `verified_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `kyc_documents_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `kyc_documents`
--

LOCK TABLES `kyc_documents` WRITE;
/*!40000 ALTER TABLE `kyc_documents` DISABLE KEYS */;
INSERT INTO `kyc_documents` VALUES (2,2,'pharmacy_license','MH11562','kyc_2_1777263513.png','verified','Documents verified by Admin.','2026-05-22 13:16:08','2026-04-27 04:18:33'),(3,2,'pharmacy_license','MH11562','kyc_2_1777263514.png','verified','Documents verified by Admin.','2026-05-22 13:16:04','2026-04-27 04:18:34'),(4,2,'pharmacy_license','NA','kyc_2_1779438817.png','verified','Documents verified by Admin.','2026-05-22 13:16:00','2026-05-22 08:33:37'),(5,2,'aadhar','NA','kyc_2_1779438848.png','verified','Documents verified by Admin.','2026-05-22 13:15:54','2026-05-22 08:34:08');
/*!40000 ALTER TABLE `kyc_documents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `medicine_substitutes`
--

DROP TABLE IF EXISTS `medicine_substitutes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `medicine_substitutes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `medicine_id` int(11) NOT NULL,
  `substitute_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `medicine_id` (`medicine_id`),
  KEY `substitute_id` (`substitute_id`),
  CONSTRAINT `medicine_substitutes_ibfk_1` FOREIGN KEY (`medicine_id`) REFERENCES `medicines` (`id`) ON DELETE CASCADE,
  CONSTRAINT `medicine_substitutes_ibfk_2` FOREIGN KEY (`substitute_id`) REFERENCES `medicines` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1509 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `medicine_substitutes`
--

LOCK TABLES `medicine_substitutes` WRITE;
/*!40000 ALTER TABLE `medicine_substitutes` DISABLE KEYS */;
INSERT INTO `medicine_substitutes` VALUES (33,201,202,'2026-04-27 08:20:31'),(36,202,201,'2026-04-27 08:20:31'),(37,203,204,'2026-04-27 08:20:31'),(38,203,205,'2026-04-27 08:20:31'),(39,203,206,'2026-04-27 08:20:31'),(40,204,203,'2026-04-27 08:20:31'),(41,204,205,'2026-04-27 08:20:31'),(42,204,206,'2026-04-27 08:20:31'),(43,205,203,'2026-04-27 08:20:31'),(44,205,204,'2026-04-27 08:20:31'),(45,205,206,'2026-04-27 08:20:31'),(46,206,203,'2026-04-27 08:20:31'),(47,206,204,'2026-04-27 08:20:31'),(48,206,205,'2026-04-27 08:20:31'),(49,207,208,'2026-04-27 08:20:31'),(50,207,209,'2026-04-27 08:20:31'),(51,207,210,'2026-04-27 08:20:31'),(52,208,207,'2026-04-27 08:20:31'),(53,208,209,'2026-04-27 08:20:31'),(54,208,210,'2026-04-27 08:20:31'),(55,209,207,'2026-04-27 08:20:31'),(56,209,208,'2026-04-27 08:20:31'),(57,209,210,'2026-04-27 08:20:31'),(58,210,207,'2026-04-27 08:20:31'),(59,210,208,'2026-04-27 08:20:31'),(60,210,209,'2026-04-27 08:20:31'),(61,211,212,'2026-04-27 08:20:31'),(62,211,213,'2026-04-27 08:20:31'),(63,211,214,'2026-04-27 08:20:31'),(64,212,211,'2026-04-27 08:20:31'),(65,212,213,'2026-04-27 08:20:31'),(66,212,214,'2026-04-27 08:20:31'),(67,213,211,'2026-04-27 08:20:31'),(68,213,212,'2026-04-27 08:20:31'),(69,213,214,'2026-04-27 08:20:31'),(70,214,211,'2026-04-27 08:20:31'),(71,214,212,'2026-04-27 08:20:31'),(72,214,213,'2026-04-27 08:20:31'),(73,215,216,'2026-04-27 08:20:31'),(74,215,217,'2026-04-27 08:20:31'),(75,215,218,'2026-04-27 08:20:31'),(76,215,427,'2026-04-27 08:20:31'),(77,215,428,'2026-04-27 08:20:31'),(78,215,429,'2026-04-27 08:20:31'),(79,215,430,'2026-04-27 08:20:31'),(80,216,215,'2026-04-27 08:20:31'),(81,216,217,'2026-04-27 08:20:31'),(82,216,218,'2026-04-27 08:20:31'),(83,216,427,'2026-04-27 08:20:31'),(84,216,428,'2026-04-27 08:20:31'),(85,216,429,'2026-04-27 08:20:31'),(86,216,430,'2026-04-27 08:20:31'),(87,217,215,'2026-04-27 08:20:31'),(88,217,216,'2026-04-27 08:20:31'),(89,217,218,'2026-04-27 08:20:31'),(90,217,427,'2026-04-27 08:20:31'),(91,217,428,'2026-04-27 08:20:31'),(92,217,429,'2026-04-27 08:20:31'),(93,217,430,'2026-04-27 08:20:31'),(94,218,215,'2026-04-27 08:20:31'),(95,218,216,'2026-04-27 08:20:31'),(96,218,217,'2026-04-27 08:20:31'),(97,218,427,'2026-04-27 08:20:31'),(98,218,428,'2026-04-27 08:20:31'),(99,218,429,'2026-04-27 08:20:31'),(100,218,430,'2026-04-27 08:20:31'),(101,219,220,'2026-04-27 08:20:31'),(102,219,221,'2026-04-27 08:20:31'),(103,219,222,'2026-04-27 08:20:31'),(104,220,219,'2026-04-27 08:20:31'),(105,220,221,'2026-04-27 08:20:31'),(106,220,222,'2026-04-27 08:20:31'),(107,221,219,'2026-04-27 08:20:31'),(108,221,220,'2026-04-27 08:20:31'),(109,221,222,'2026-04-27 08:20:31'),(110,222,219,'2026-04-27 08:20:31'),(111,222,220,'2026-04-27 08:20:31'),(112,222,221,'2026-04-27 08:20:31'),(113,223,224,'2026-04-27 08:20:31'),(114,223,225,'2026-04-27 08:20:31'),(115,223,226,'2026-04-27 08:20:31'),(116,224,223,'2026-04-27 08:20:31'),(117,224,225,'2026-04-27 08:20:31'),(118,224,226,'2026-04-27 08:20:31'),(119,225,223,'2026-04-27 08:20:31'),(120,225,224,'2026-04-27 08:20:31'),(121,225,226,'2026-04-27 08:20:31'),(122,226,223,'2026-04-27 08:20:31'),(123,226,224,'2026-04-27 08:20:31'),(124,226,225,'2026-04-27 08:20:31'),(125,227,228,'2026-04-27 08:20:31'),(126,227,229,'2026-04-27 08:20:31'),(127,227,230,'2026-04-27 08:20:31'),(128,228,227,'2026-04-27 08:20:31'),(129,228,229,'2026-04-27 08:20:31'),(130,228,230,'2026-04-27 08:20:31'),(131,229,227,'2026-04-27 08:20:31'),(132,229,228,'2026-04-27 08:20:31'),(133,229,230,'2026-04-27 08:20:31'),(134,230,227,'2026-04-27 08:20:31'),(135,230,228,'2026-04-27 08:20:31'),(136,230,229,'2026-04-27 08:20:31'),(137,231,232,'2026-04-27 08:20:31'),(138,231,233,'2026-04-27 08:20:31'),(139,231,234,'2026-04-27 08:20:31'),(140,232,231,'2026-04-27 08:20:31'),(141,232,233,'2026-04-27 08:20:31'),(142,232,234,'2026-04-27 08:20:31'),(143,233,231,'2026-04-27 08:20:31'),(144,233,232,'2026-04-27 08:20:31'),(145,233,234,'2026-04-27 08:20:31'),(146,234,231,'2026-04-27 08:20:31'),(147,234,232,'2026-04-27 08:20:31'),(148,234,233,'2026-04-27 08:20:31'),(149,235,236,'2026-04-27 08:20:31'),(150,235,237,'2026-04-27 08:20:31'),(151,235,238,'2026-04-27 08:20:31'),(152,235,435,'2026-04-27 08:20:31'),(153,235,436,'2026-04-27 08:20:31'),(154,235,437,'2026-04-27 08:20:31'),(155,235,438,'2026-04-27 08:20:31'),(156,236,235,'2026-04-27 08:20:31'),(157,236,237,'2026-04-27 08:20:31'),(158,236,238,'2026-04-27 08:20:31'),(159,236,435,'2026-04-27 08:20:31'),(160,236,436,'2026-04-27 08:20:31'),(161,236,437,'2026-04-27 08:20:31'),(162,236,438,'2026-04-27 08:20:31'),(163,237,235,'2026-04-27 08:20:31'),(164,237,236,'2026-04-27 08:20:31'),(165,237,238,'2026-04-27 08:20:31'),(166,237,435,'2026-04-27 08:20:31'),(167,237,436,'2026-04-27 08:20:31'),(168,237,437,'2026-04-27 08:20:31'),(169,237,438,'2026-04-27 08:20:31'),(170,238,235,'2026-04-27 08:20:31'),(171,238,236,'2026-04-27 08:20:31'),(172,238,237,'2026-04-27 08:20:31'),(173,238,435,'2026-04-27 08:20:31'),(174,238,436,'2026-04-27 08:20:31'),(175,238,437,'2026-04-27 08:20:31'),(176,238,438,'2026-04-27 08:20:31'),(177,239,240,'2026-04-27 08:20:31'),(178,239,241,'2026-04-27 08:20:31'),(179,239,242,'2026-04-27 08:20:31'),(180,240,239,'2026-04-27 08:20:31'),(181,240,241,'2026-04-27 08:20:31'),(182,240,242,'2026-04-27 08:20:31'),(183,241,239,'2026-04-27 08:20:31'),(184,241,240,'2026-04-27 08:20:31'),(185,241,242,'2026-04-27 08:20:31'),(186,242,239,'2026-04-27 08:20:31'),(187,242,240,'2026-04-27 08:20:31'),(188,242,241,'2026-04-27 08:20:31'),(189,243,244,'2026-04-27 08:20:31'),(190,243,245,'2026-04-27 08:20:31'),(191,243,246,'2026-04-27 08:20:31'),(192,243,419,'2026-04-27 08:20:31'),(193,243,420,'2026-04-27 08:20:31'),(194,243,421,'2026-04-27 08:20:31'),(195,243,422,'2026-04-27 08:20:31'),(196,244,243,'2026-04-27 08:20:31'),(197,244,245,'2026-04-27 08:20:31'),(198,244,246,'2026-04-27 08:20:31'),(199,244,419,'2026-04-27 08:20:31'),(200,244,420,'2026-04-27 08:20:31'),(201,244,421,'2026-04-27 08:20:31'),(202,244,422,'2026-04-27 08:20:31'),(203,245,243,'2026-04-27 08:20:31'),(204,245,244,'2026-04-27 08:20:31'),(205,245,246,'2026-04-27 08:20:31'),(206,245,419,'2026-04-27 08:20:31'),(207,245,420,'2026-04-27 08:20:31'),(208,245,421,'2026-04-27 08:20:31'),(209,245,422,'2026-04-27 08:20:31'),(210,246,243,'2026-04-27 08:20:31'),(211,246,244,'2026-04-27 08:20:31'),(212,246,245,'2026-04-27 08:20:31'),(213,246,419,'2026-04-27 08:20:31'),(214,246,420,'2026-04-27 08:20:31'),(215,246,421,'2026-04-27 08:20:31'),(216,246,422,'2026-04-27 08:20:31'),(217,247,248,'2026-04-27 08:20:31'),(218,247,249,'2026-04-27 08:20:31'),(219,247,250,'2026-04-27 08:20:31'),(220,248,247,'2026-04-27 08:20:31'),(221,248,249,'2026-04-27 08:20:31'),(222,248,250,'2026-04-27 08:20:31'),(223,249,247,'2026-04-27 08:20:31'),(224,249,248,'2026-04-27 08:20:31'),(225,249,250,'2026-04-27 08:20:31'),(226,250,247,'2026-04-27 08:20:31'),(227,250,248,'2026-04-27 08:20:31'),(228,250,249,'2026-04-27 08:20:31'),(229,251,252,'2026-04-27 08:20:31'),(230,251,253,'2026-04-27 08:20:31'),(231,251,254,'2026-04-27 08:20:31'),(232,252,251,'2026-04-27 08:20:31'),(233,252,253,'2026-04-27 08:20:31'),(234,252,254,'2026-04-27 08:20:31'),(235,253,251,'2026-04-27 08:20:31'),(236,253,252,'2026-04-27 08:20:31'),(237,253,254,'2026-04-27 08:20:31'),(238,254,251,'2026-04-27 08:20:31'),(239,254,252,'2026-04-27 08:20:31'),(240,254,253,'2026-04-27 08:20:31'),(241,255,256,'2026-04-27 08:20:31'),(242,255,257,'2026-04-27 08:20:31'),(243,255,258,'2026-04-27 08:20:31'),(244,255,423,'2026-04-27 08:20:31'),(245,255,424,'2026-04-27 08:20:31'),(246,255,425,'2026-04-27 08:20:31'),(247,255,426,'2026-04-27 08:20:31'),(248,256,255,'2026-04-27 08:20:31'),(249,256,257,'2026-04-27 08:20:31'),(250,256,258,'2026-04-27 08:20:31'),(251,256,423,'2026-04-27 08:20:31'),(252,256,424,'2026-04-27 08:20:31'),(253,256,425,'2026-04-27 08:20:31'),(254,256,426,'2026-04-27 08:20:31'),(255,257,255,'2026-04-27 08:20:31'),(256,257,256,'2026-04-27 08:20:31'),(257,257,258,'2026-04-27 08:20:31'),(258,257,423,'2026-04-27 08:20:31'),(259,257,424,'2026-04-27 08:20:31'),(260,257,425,'2026-04-27 08:20:31'),(261,257,426,'2026-04-27 08:20:31'),(262,258,255,'2026-04-27 08:20:31'),(263,258,256,'2026-04-27 08:20:31'),(264,258,257,'2026-04-27 08:20:31'),(265,258,423,'2026-04-27 08:20:31'),(266,258,424,'2026-04-27 08:20:31'),(267,258,425,'2026-04-27 08:20:31'),(268,258,426,'2026-04-27 08:20:31'),(269,259,260,'2026-04-27 08:20:31'),(270,259,261,'2026-04-27 08:20:31'),(271,259,262,'2026-04-27 08:20:31'),(272,260,259,'2026-04-27 08:20:31'),(273,260,261,'2026-04-27 08:20:31'),(274,260,262,'2026-04-27 08:20:31'),(275,261,259,'2026-04-27 08:20:31'),(276,261,260,'2026-04-27 08:20:31'),(277,261,262,'2026-04-27 08:20:31'),(278,262,259,'2026-04-27 08:20:31'),(279,262,260,'2026-04-27 08:20:31'),(280,262,261,'2026-04-27 08:20:31'),(281,263,264,'2026-04-27 08:20:31'),(282,263,265,'2026-04-27 08:20:31'),(283,263,266,'2026-04-27 08:20:31'),(284,264,263,'2026-04-27 08:20:31'),(285,264,265,'2026-04-27 08:20:31'),(286,264,266,'2026-04-27 08:20:31'),(287,265,263,'2026-04-27 08:20:31'),(288,265,264,'2026-04-27 08:20:31'),(289,265,266,'2026-04-27 08:20:31'),(290,266,263,'2026-04-27 08:20:31'),(291,266,264,'2026-04-27 08:20:31'),(292,266,265,'2026-04-27 08:20:31'),(293,267,268,'2026-04-27 08:20:31'),(294,267,269,'2026-04-27 08:20:31'),(295,267,270,'2026-04-27 08:20:31'),(296,267,407,'2026-04-27 08:20:31'),(297,267,408,'2026-04-27 08:20:31'),(298,267,409,'2026-04-27 08:20:31'),(299,267,410,'2026-04-27 08:20:31'),(300,268,267,'2026-04-27 08:20:31'),(301,268,269,'2026-04-27 08:20:31'),(302,268,270,'2026-04-27 08:20:31'),(303,268,407,'2026-04-27 08:20:31'),(304,268,408,'2026-04-27 08:20:31'),(305,268,409,'2026-04-27 08:20:31'),(306,268,410,'2026-04-27 08:20:31'),(307,269,267,'2026-04-27 08:20:31'),(308,269,268,'2026-04-27 08:20:31'),(309,269,270,'2026-04-27 08:20:31'),(310,269,407,'2026-04-27 08:20:31'),(311,269,408,'2026-04-27 08:20:31'),(312,269,409,'2026-04-27 08:20:31'),(313,269,410,'2026-04-27 08:20:31'),(314,270,267,'2026-04-27 08:20:31'),(315,270,268,'2026-04-27 08:20:31'),(316,270,269,'2026-04-27 08:20:31'),(317,270,407,'2026-04-27 08:20:31'),(318,270,408,'2026-04-27 08:20:31'),(319,270,409,'2026-04-27 08:20:31'),(320,270,410,'2026-04-27 08:20:31'),(321,271,272,'2026-04-27 08:20:31'),(322,271,273,'2026-04-27 08:20:31'),(323,271,274,'2026-04-27 08:20:31'),(324,272,271,'2026-04-27 08:20:31'),(325,272,273,'2026-04-27 08:20:31'),(326,272,274,'2026-04-27 08:20:31'),(327,273,271,'2026-04-27 08:20:31'),(328,273,272,'2026-04-27 08:20:31'),(329,273,274,'2026-04-27 08:20:31'),(330,274,271,'2026-04-27 08:20:31'),(331,274,272,'2026-04-27 08:20:31'),(332,274,273,'2026-04-27 08:20:31'),(333,275,276,'2026-04-27 08:20:31'),(334,275,277,'2026-04-27 08:20:31'),(335,275,278,'2026-04-27 08:20:31'),(336,276,275,'2026-04-27 08:20:31'),(337,276,277,'2026-04-27 08:20:31'),(338,276,278,'2026-04-27 08:20:31'),(339,277,275,'2026-04-27 08:20:31'),(340,277,276,'2026-04-27 08:20:31'),(341,277,278,'2026-04-27 08:20:31'),(342,278,275,'2026-04-27 08:20:31'),(343,278,276,'2026-04-27 08:20:31'),(344,278,277,'2026-04-27 08:20:31'),(345,279,280,'2026-04-27 08:20:31'),(346,279,281,'2026-04-27 08:20:31'),(347,279,282,'2026-04-27 08:20:31'),(348,280,279,'2026-04-27 08:20:31'),(349,280,281,'2026-04-27 08:20:31'),(350,280,282,'2026-04-27 08:20:31'),(351,281,279,'2026-04-27 08:20:31'),(352,281,280,'2026-04-27 08:20:31'),(353,281,282,'2026-04-27 08:20:31'),(354,282,279,'2026-04-27 08:20:31'),(355,282,280,'2026-04-27 08:20:31'),(356,282,281,'2026-04-27 08:20:31'),(357,283,284,'2026-04-27 08:20:31'),(358,283,285,'2026-04-27 08:20:31'),(359,283,286,'2026-04-27 08:20:31'),(360,284,283,'2026-04-27 08:20:31'),(361,284,285,'2026-04-27 08:20:31'),(362,284,286,'2026-04-27 08:20:31'),(363,285,283,'2026-04-27 08:20:31'),(364,285,284,'2026-04-27 08:20:31'),(365,285,286,'2026-04-27 08:20:31'),(366,286,283,'2026-04-27 08:20:31'),(367,286,284,'2026-04-27 08:20:31'),(368,286,285,'2026-04-27 08:20:31'),(369,287,288,'2026-04-27 08:20:31'),(370,287,289,'2026-04-27 08:20:31'),(371,287,290,'2026-04-27 08:20:31'),(372,288,287,'2026-04-27 08:20:31'),(373,288,289,'2026-04-27 08:20:31'),(374,288,290,'2026-04-27 08:20:31'),(375,289,287,'2026-04-27 08:20:31'),(376,289,288,'2026-04-27 08:20:31'),(377,289,290,'2026-04-27 08:20:31'),(378,290,287,'2026-04-27 08:20:31'),(379,290,288,'2026-04-27 08:20:31'),(380,290,289,'2026-04-27 08:20:31'),(381,291,292,'2026-04-27 08:20:31'),(382,291,293,'2026-04-27 08:20:31'),(383,291,294,'2026-04-27 08:20:31'),(384,291,439,'2026-04-27 08:20:31'),(385,291,440,'2026-04-27 08:20:31'),(386,291,441,'2026-04-27 08:20:31'),(387,291,442,'2026-04-27 08:20:31'),(388,292,291,'2026-04-27 08:20:31'),(389,292,293,'2026-04-27 08:20:32'),(390,292,294,'2026-04-27 08:20:32'),(391,292,439,'2026-04-27 08:20:32'),(392,292,440,'2026-04-27 08:20:32'),(393,292,441,'2026-04-27 08:20:32'),(394,292,442,'2026-04-27 08:20:32'),(395,293,291,'2026-04-27 08:20:32'),(396,293,292,'2026-04-27 08:20:32'),(397,293,294,'2026-04-27 08:20:32'),(398,293,439,'2026-04-27 08:20:32'),(399,293,440,'2026-04-27 08:20:32'),(400,293,441,'2026-04-27 08:20:32'),(401,293,442,'2026-04-27 08:20:32'),(402,294,291,'2026-04-27 08:20:32'),(403,294,292,'2026-04-27 08:20:32'),(404,294,293,'2026-04-27 08:20:32'),(405,294,439,'2026-04-27 08:20:32'),(406,294,440,'2026-04-27 08:20:32'),(407,294,441,'2026-04-27 08:20:32'),(408,294,442,'2026-04-27 08:20:32'),(409,295,296,'2026-04-27 08:20:32'),(410,295,297,'2026-04-27 08:20:32'),(411,295,298,'2026-04-27 08:20:32'),(412,296,295,'2026-04-27 08:20:32'),(413,296,297,'2026-04-27 08:20:32'),(414,296,298,'2026-04-27 08:20:32'),(415,297,295,'2026-04-27 08:20:32'),(416,297,296,'2026-04-27 08:20:32'),(417,297,298,'2026-04-27 08:20:32'),(418,298,295,'2026-04-27 08:20:32'),(419,298,296,'2026-04-27 08:20:32'),(420,298,297,'2026-04-27 08:20:32'),(421,299,300,'2026-04-27 08:20:32'),(422,299,301,'2026-04-27 08:20:32'),(423,299,302,'2026-04-27 08:20:32'),(424,300,299,'2026-04-27 08:20:32'),(425,300,301,'2026-04-27 08:20:32'),(426,300,302,'2026-04-27 08:20:32'),(427,301,299,'2026-04-27 08:20:32'),(428,301,300,'2026-04-27 08:20:32'),(429,301,302,'2026-04-27 08:20:32'),(430,302,299,'2026-04-27 08:20:32'),(431,302,300,'2026-04-27 08:20:32'),(432,302,301,'2026-04-27 08:20:32'),(433,303,304,'2026-04-27 08:20:32'),(434,303,305,'2026-04-27 08:20:32'),(435,303,306,'2026-04-27 08:20:32'),(436,304,303,'2026-04-27 08:20:32'),(437,304,305,'2026-04-27 08:20:32'),(438,304,306,'2026-04-27 08:20:32'),(439,305,303,'2026-04-27 08:20:32'),(440,305,304,'2026-04-27 08:20:32'),(441,305,306,'2026-04-27 08:20:32'),(442,306,303,'2026-04-27 08:20:32'),(443,306,304,'2026-04-27 08:20:32'),(444,306,305,'2026-04-27 08:20:32'),(445,307,308,'2026-04-27 08:20:32'),(446,307,309,'2026-04-27 08:20:32'),(447,307,310,'2026-04-27 08:20:32'),(448,308,307,'2026-04-27 08:20:32'),(449,308,309,'2026-04-27 08:20:32'),(450,308,310,'2026-04-27 08:20:32'),(451,309,307,'2026-04-27 08:20:32'),(452,309,308,'2026-04-27 08:20:32'),(453,309,310,'2026-04-27 08:20:32'),(454,310,307,'2026-04-27 08:20:32'),(455,310,308,'2026-04-27 08:20:32'),(456,310,309,'2026-04-27 08:20:32'),(457,311,312,'2026-04-27 08:20:32'),(458,311,313,'2026-04-27 08:20:32'),(459,311,314,'2026-04-27 08:20:32'),(460,312,311,'2026-04-27 08:20:32'),(461,312,313,'2026-04-27 08:20:32'),(462,312,314,'2026-04-27 08:20:32'),(463,313,311,'2026-04-27 08:20:32'),(464,313,312,'2026-04-27 08:20:32'),(465,313,314,'2026-04-27 08:20:32'),(466,314,311,'2026-04-27 08:20:32'),(467,314,312,'2026-04-27 08:20:32'),(468,314,313,'2026-04-27 08:20:32'),(469,315,316,'2026-04-27 08:20:32'),(470,315,317,'2026-04-27 08:20:32'),(471,315,318,'2026-04-27 08:20:32'),(472,316,315,'2026-04-27 08:20:32'),(473,316,317,'2026-04-27 08:20:32'),(474,316,318,'2026-04-27 08:20:32'),(475,317,315,'2026-04-27 08:20:32'),(476,317,316,'2026-04-27 08:20:32'),(477,317,318,'2026-04-27 08:20:32'),(478,318,315,'2026-04-27 08:20:32'),(479,318,316,'2026-04-27 08:20:32'),(480,318,317,'2026-04-27 08:20:32'),(481,319,320,'2026-04-27 08:20:32'),(482,319,321,'2026-04-27 08:20:32'),(483,319,322,'2026-04-27 08:20:32'),(484,320,319,'2026-04-27 08:20:32'),(485,320,321,'2026-04-27 08:20:32'),(486,320,322,'2026-04-27 08:20:32'),(487,321,319,'2026-04-27 08:20:32'),(488,321,320,'2026-04-27 08:20:32'),(489,321,322,'2026-04-27 08:20:32'),(490,322,319,'2026-04-27 08:20:32'),(491,322,320,'2026-04-27 08:20:32'),(492,322,321,'2026-04-27 08:20:32'),(493,323,324,'2026-04-27 08:20:32'),(494,323,325,'2026-04-27 08:20:32'),(495,323,326,'2026-04-27 08:20:32'),(496,324,323,'2026-04-27 08:20:32'),(497,324,325,'2026-04-27 08:20:32'),(498,324,326,'2026-04-27 08:20:32'),(499,325,323,'2026-04-27 08:20:32'),(500,325,324,'2026-04-27 08:20:32'),(501,325,326,'2026-04-27 08:20:32'),(502,326,323,'2026-04-27 08:20:32'),(503,326,324,'2026-04-27 08:20:32'),(504,326,325,'2026-04-27 08:20:32'),(505,327,328,'2026-04-27 08:20:32'),(506,327,329,'2026-04-27 08:20:32'),(507,327,330,'2026-04-27 08:20:32'),(508,328,327,'2026-04-27 08:20:32'),(509,328,329,'2026-04-27 08:20:32'),(510,328,330,'2026-04-27 08:20:32'),(511,329,327,'2026-04-27 08:20:32'),(512,329,328,'2026-04-27 08:20:32'),(513,329,330,'2026-04-27 08:20:32'),(514,330,327,'2026-04-27 08:20:32'),(515,330,328,'2026-04-27 08:20:32'),(516,330,329,'2026-04-27 08:20:32'),(517,331,332,'2026-04-27 08:20:32'),(518,331,333,'2026-04-27 08:20:32'),(519,331,334,'2026-04-27 08:20:32'),(520,332,331,'2026-04-27 08:20:32'),(521,332,333,'2026-04-27 08:20:32'),(522,332,334,'2026-04-27 08:20:32'),(523,333,331,'2026-04-27 08:20:32'),(524,333,332,'2026-04-27 08:20:32'),(525,333,334,'2026-04-27 08:20:32'),(526,334,331,'2026-04-27 08:20:32'),(527,334,332,'2026-04-27 08:20:32'),(528,334,333,'2026-04-27 08:20:32'),(529,335,336,'2026-04-27 08:20:32'),(530,335,337,'2026-04-27 08:20:32'),(531,335,338,'2026-04-27 08:20:32'),(532,336,335,'2026-04-27 08:20:32'),(533,336,337,'2026-04-27 08:20:32'),(534,336,338,'2026-04-27 08:20:32'),(535,337,335,'2026-04-27 08:20:32'),(536,337,336,'2026-04-27 08:20:32'),(537,337,338,'2026-04-27 08:20:32'),(538,338,335,'2026-04-27 08:20:32'),(539,338,336,'2026-04-27 08:20:32'),(540,338,337,'2026-04-27 08:20:32'),(541,339,340,'2026-04-27 08:20:32'),(542,339,341,'2026-04-27 08:20:32'),(543,339,342,'2026-04-27 08:20:32'),(544,340,339,'2026-04-27 08:20:32'),(545,340,341,'2026-04-27 08:20:32'),(546,340,342,'2026-04-27 08:20:32'),(547,341,339,'2026-04-27 08:20:32'),(548,341,340,'2026-04-27 08:20:32'),(549,341,342,'2026-04-27 08:20:32'),(550,342,339,'2026-04-27 08:20:32'),(551,342,340,'2026-04-27 08:20:32'),(552,342,341,'2026-04-27 08:20:32'),(553,343,344,'2026-04-27 08:20:32'),(554,343,345,'2026-04-27 08:20:32'),(555,343,346,'2026-04-27 08:20:32'),(556,344,343,'2026-04-27 08:20:32'),(557,344,345,'2026-04-27 08:20:32'),(558,344,346,'2026-04-27 08:20:32'),(559,345,343,'2026-04-27 08:20:32'),(560,345,344,'2026-04-27 08:20:32'),(561,345,346,'2026-04-27 08:20:32'),(562,346,343,'2026-04-27 08:20:32'),(563,346,344,'2026-04-27 08:20:32'),(564,346,345,'2026-04-27 08:20:32'),(565,347,348,'2026-04-27 08:20:32'),(566,347,349,'2026-04-27 08:20:32'),(567,347,350,'2026-04-27 08:20:32'),(568,348,347,'2026-04-27 08:20:32'),(569,348,349,'2026-04-27 08:20:32'),(570,348,350,'2026-04-27 08:20:32'),(571,349,347,'2026-04-27 08:20:32'),(572,349,348,'2026-04-27 08:20:32'),(573,349,350,'2026-04-27 08:20:32'),(574,350,347,'2026-04-27 08:20:32'),(575,350,348,'2026-04-27 08:20:32'),(576,350,349,'2026-04-27 08:20:32'),(577,351,352,'2026-04-27 08:20:32'),(578,351,353,'2026-04-27 08:20:32'),(579,351,354,'2026-04-27 08:20:32'),(580,352,351,'2026-04-27 08:20:32'),(581,352,353,'2026-04-27 08:20:32'),(582,352,354,'2026-04-27 08:20:32'),(583,353,351,'2026-04-27 08:20:32'),(584,353,352,'2026-04-27 08:20:32'),(585,353,354,'2026-04-27 08:20:32'),(586,354,351,'2026-04-27 08:20:32'),(587,354,352,'2026-04-27 08:20:32'),(588,354,353,'2026-04-27 08:20:32'),(589,355,356,'2026-04-27 08:20:32'),(590,355,357,'2026-04-27 08:20:32'),(591,355,358,'2026-04-27 08:20:32'),(592,356,355,'2026-04-27 08:20:32'),(593,356,357,'2026-04-27 08:20:32'),(594,356,358,'2026-04-27 08:20:32'),(595,357,355,'2026-04-27 08:20:32'),(596,357,356,'2026-04-27 08:20:32'),(597,357,358,'2026-04-27 08:20:32'),(598,358,355,'2026-04-27 08:20:32'),(599,358,356,'2026-04-27 08:20:32'),(600,358,357,'2026-04-27 08:20:32'),(601,359,360,'2026-04-27 08:20:32'),(602,359,361,'2026-04-27 08:20:32'),(603,359,362,'2026-04-27 08:20:32'),(604,359,455,'2026-04-27 08:20:32'),(605,359,456,'2026-04-27 08:20:32'),(606,359,457,'2026-04-27 08:20:32'),(607,359,458,'2026-04-27 08:20:32'),(608,360,359,'2026-04-27 08:20:32'),(609,360,361,'2026-04-27 08:20:32'),(610,360,362,'2026-04-27 08:20:32'),(611,360,455,'2026-04-27 08:20:32'),(612,360,456,'2026-04-27 08:20:32'),(613,360,457,'2026-04-27 08:20:32'),(614,360,458,'2026-04-27 08:20:32'),(615,361,359,'2026-04-27 08:20:32'),(616,361,360,'2026-04-27 08:20:32'),(617,361,362,'2026-04-27 08:20:32'),(618,361,455,'2026-04-27 08:20:32'),(619,361,456,'2026-04-27 08:20:32'),(620,361,457,'2026-04-27 08:20:32'),(621,361,458,'2026-04-27 08:20:32'),(622,362,359,'2026-04-27 08:20:32'),(623,362,360,'2026-04-27 08:20:32'),(624,362,361,'2026-04-27 08:20:32'),(625,362,455,'2026-04-27 08:20:32'),(626,362,456,'2026-04-27 08:20:32'),(627,362,457,'2026-04-27 08:20:32'),(628,362,458,'2026-04-27 08:20:32'),(629,363,364,'2026-04-27 08:20:32'),(630,363,365,'2026-04-27 08:20:32'),(631,363,366,'2026-04-27 08:20:32'),(632,364,363,'2026-04-27 08:20:32'),(633,364,365,'2026-04-27 08:20:32'),(634,364,366,'2026-04-27 08:20:32'),(635,365,363,'2026-04-27 08:20:32'),(636,365,364,'2026-04-27 08:20:32'),(637,365,366,'2026-04-27 08:20:32'),(638,366,363,'2026-04-27 08:20:32'),(639,366,364,'2026-04-27 08:20:32'),(640,366,365,'2026-04-27 08:20:32'),(641,367,368,'2026-04-27 08:20:32'),(642,367,369,'2026-04-27 08:20:32'),(643,367,370,'2026-04-27 08:20:32'),(644,368,367,'2026-04-27 08:20:32'),(645,368,369,'2026-04-27 08:20:32'),(646,368,370,'2026-04-27 08:20:32'),(647,369,367,'2026-04-27 08:20:32'),(648,369,368,'2026-04-27 08:20:32'),(649,369,370,'2026-04-27 08:20:32'),(650,370,367,'2026-04-27 08:20:32'),(651,370,368,'2026-04-27 08:20:32'),(652,370,369,'2026-04-27 08:20:32'),(653,371,372,'2026-04-27 08:20:32'),(654,371,373,'2026-04-27 08:20:32'),(655,371,374,'2026-04-27 08:20:32'),(656,372,371,'2026-04-27 08:20:32'),(657,372,373,'2026-04-27 08:20:32'),(658,372,374,'2026-04-27 08:20:32'),(659,373,371,'2026-04-27 08:20:32'),(660,373,372,'2026-04-27 08:20:32'),(661,373,374,'2026-04-27 08:20:32'),(662,374,371,'2026-04-27 08:20:32'),(663,374,372,'2026-04-27 08:20:32'),(664,374,373,'2026-04-27 08:20:32'),(665,375,376,'2026-04-27 08:20:32'),(666,375,377,'2026-04-27 08:20:32'),(667,375,378,'2026-04-27 08:20:32'),(668,376,375,'2026-04-27 08:20:32'),(669,376,377,'2026-04-27 08:20:32'),(670,376,378,'2026-04-27 08:20:32'),(671,377,375,'2026-04-27 08:20:32'),(672,377,376,'2026-04-27 08:20:32'),(673,377,378,'2026-04-27 08:20:32'),(674,378,375,'2026-04-27 08:20:32'),(675,378,376,'2026-04-27 08:20:32'),(676,378,377,'2026-04-27 08:20:32'),(677,379,380,'2026-04-27 08:20:32'),(678,379,381,'2026-04-27 08:20:32'),(679,379,382,'2026-04-27 08:20:32'),(680,380,379,'2026-04-27 08:20:32'),(681,380,381,'2026-04-27 08:20:32'),(682,380,382,'2026-04-27 08:20:32'),(683,381,379,'2026-04-27 08:20:32'),(684,381,380,'2026-04-27 08:20:32'),(685,381,382,'2026-04-27 08:20:32'),(686,382,379,'2026-04-27 08:20:32'),(687,382,380,'2026-04-27 08:20:32'),(688,382,381,'2026-04-27 08:20:32'),(689,383,384,'2026-04-27 08:20:32'),(690,383,385,'2026-04-27 08:20:32'),(691,383,386,'2026-04-27 08:20:32'),(692,384,383,'2026-04-27 08:20:32'),(693,384,385,'2026-04-27 08:20:32'),(694,384,386,'2026-04-27 08:20:32'),(695,385,383,'2026-04-27 08:20:32'),(696,385,384,'2026-04-27 08:20:32'),(697,385,386,'2026-04-27 08:20:32'),(698,386,383,'2026-04-27 08:20:32'),(699,386,384,'2026-04-27 08:20:32'),(700,386,385,'2026-04-27 08:20:32'),(701,387,388,'2026-04-27 08:20:32'),(702,387,389,'2026-04-27 08:20:32'),(703,387,390,'2026-04-27 08:20:32'),(704,388,387,'2026-04-27 08:20:32'),(705,388,389,'2026-04-27 08:20:32'),(706,388,390,'2026-04-27 08:20:32'),(707,389,387,'2026-04-27 08:20:32'),(708,389,388,'2026-04-27 08:20:32'),(709,389,390,'2026-04-27 08:20:32'),(710,390,387,'2026-04-27 08:20:32'),(711,390,388,'2026-04-27 08:20:32'),(712,390,389,'2026-04-27 08:20:32'),(713,391,392,'2026-04-27 08:20:32'),(714,391,393,'2026-04-27 08:20:32'),(715,391,394,'2026-04-27 08:20:32'),(716,392,391,'2026-04-27 08:20:32'),(717,392,393,'2026-04-27 08:20:32'),(718,392,394,'2026-04-27 08:20:32'),(719,393,391,'2026-04-27 08:20:32'),(720,393,392,'2026-04-27 08:20:32'),(721,393,394,'2026-04-27 08:20:32'),(722,394,391,'2026-04-27 08:20:32'),(723,394,392,'2026-04-27 08:20:32'),(724,394,393,'2026-04-27 08:20:32'),(725,395,396,'2026-04-27 08:20:32'),(726,395,397,'2026-04-27 08:20:32'),(727,395,398,'2026-04-27 08:20:32'),(728,396,395,'2026-04-27 08:20:32'),(729,396,397,'2026-04-27 08:20:32'),(730,396,398,'2026-04-27 08:20:32'),(731,397,395,'2026-04-27 08:20:32'),(732,397,396,'2026-04-27 08:20:32'),(733,397,398,'2026-04-27 08:20:32'),(734,398,395,'2026-04-27 08:20:32'),(735,398,396,'2026-04-27 08:20:32'),(736,398,397,'2026-04-27 08:20:32'),(737,399,400,'2026-04-27 08:20:32'),(738,399,401,'2026-04-27 08:20:32'),(739,399,402,'2026-04-27 08:20:32'),(740,400,399,'2026-04-27 08:20:32'),(741,400,401,'2026-04-27 08:20:32'),(742,400,402,'2026-04-27 08:20:32'),(743,401,399,'2026-04-27 08:20:32'),(744,401,400,'2026-04-27 08:20:32'),(745,401,402,'2026-04-27 08:20:32'),(746,402,399,'2026-04-27 08:20:32'),(747,402,400,'2026-04-27 08:20:32'),(748,402,401,'2026-04-27 08:20:32'),(749,403,404,'2026-04-27 08:20:32'),(750,403,405,'2026-04-27 08:20:32'),(751,403,406,'2026-04-27 08:20:32'),(752,404,403,'2026-04-27 08:20:32'),(753,404,405,'2026-04-27 08:20:32'),(754,404,406,'2026-04-27 08:20:32'),(755,405,403,'2026-04-27 08:20:32'),(756,405,404,'2026-04-27 08:20:32'),(757,405,406,'2026-04-27 08:20:32'),(758,406,403,'2026-04-27 08:20:32'),(759,406,404,'2026-04-27 08:20:32'),(760,406,405,'2026-04-27 08:20:32'),(761,407,267,'2026-04-27 08:20:32'),(762,407,268,'2026-04-27 08:20:32'),(763,407,269,'2026-04-27 08:20:32'),(764,407,270,'2026-04-27 08:20:32'),(765,407,408,'2026-04-27 08:20:32'),(766,407,409,'2026-04-27 08:20:32'),(767,407,410,'2026-04-27 08:20:32'),(768,408,267,'2026-04-27 08:20:32'),(769,408,268,'2026-04-27 08:20:32'),(770,408,269,'2026-04-27 08:20:32'),(771,408,270,'2026-04-27 08:20:32'),(772,408,407,'2026-04-27 08:20:32'),(773,408,409,'2026-04-27 08:20:32'),(774,408,410,'2026-04-27 08:20:32'),(775,409,267,'2026-04-27 08:20:32'),(776,409,268,'2026-04-27 08:20:32'),(777,409,269,'2026-04-27 08:20:32'),(778,409,270,'2026-04-27 08:20:32'),(779,409,407,'2026-04-27 08:20:32'),(780,409,408,'2026-04-27 08:20:32'),(781,409,410,'2026-04-27 08:20:32'),(782,410,267,'2026-04-27 08:20:32'),(783,410,268,'2026-04-27 08:20:32'),(784,410,269,'2026-04-27 08:20:32'),(785,410,270,'2026-04-27 08:20:32'),(786,410,407,'2026-04-27 08:20:32'),(787,410,408,'2026-04-27 08:20:32'),(788,410,409,'2026-04-27 08:20:32'),(789,411,412,'2026-04-27 08:20:32'),(790,411,413,'2026-04-27 08:20:32'),(791,411,414,'2026-04-27 08:20:32'),(792,412,411,'2026-04-27 08:20:32'),(793,412,413,'2026-04-27 08:20:32'),(794,412,414,'2026-04-27 08:20:32'),(795,413,411,'2026-04-27 08:20:32'),(796,413,412,'2026-04-27 08:20:32'),(797,413,414,'2026-04-27 08:20:32'),(798,414,411,'2026-04-27 08:20:32'),(799,414,412,'2026-04-27 08:20:32'),(800,414,413,'2026-04-27 08:20:32'),(801,415,416,'2026-04-27 08:20:32'),(802,415,417,'2026-04-27 08:20:32'),(803,415,418,'2026-04-27 08:20:32'),(804,416,415,'2026-04-27 08:20:32'),(805,416,417,'2026-04-27 08:20:32'),(806,416,418,'2026-04-27 08:20:32'),(807,417,415,'2026-04-27 08:20:32'),(808,417,416,'2026-04-27 08:20:32'),(809,417,418,'2026-04-27 08:20:32'),(810,418,415,'2026-04-27 08:20:32'),(811,418,416,'2026-04-27 08:20:32'),(812,418,417,'2026-04-27 08:20:32'),(813,419,243,'2026-04-27 08:20:32'),(814,419,244,'2026-04-27 08:20:32'),(815,419,245,'2026-04-27 08:20:32'),(816,419,246,'2026-04-27 08:20:32'),(817,419,420,'2026-04-27 08:20:32'),(818,419,421,'2026-04-27 08:20:32'),(819,419,422,'2026-04-27 08:20:32'),(820,420,243,'2026-04-27 08:20:32'),(821,420,244,'2026-04-27 08:20:32'),(822,420,245,'2026-04-27 08:20:32'),(823,420,246,'2026-04-27 08:20:32'),(824,420,419,'2026-04-27 08:20:32'),(825,420,421,'2026-04-27 08:20:32'),(826,420,422,'2026-04-27 08:20:32'),(827,421,243,'2026-04-27 08:20:32'),(828,421,244,'2026-04-27 08:20:32'),(829,421,245,'2026-04-27 08:20:32'),(830,421,246,'2026-04-27 08:20:32'),(831,421,419,'2026-04-27 08:20:32'),(832,421,420,'2026-04-27 08:20:32'),(833,421,422,'2026-04-27 08:20:32'),(834,422,243,'2026-04-27 08:20:32'),(835,422,244,'2026-04-27 08:20:32'),(836,422,245,'2026-04-27 08:20:32'),(837,422,246,'2026-04-27 08:20:32'),(838,422,419,'2026-04-27 08:20:32'),(839,422,420,'2026-04-27 08:20:32'),(840,422,421,'2026-04-27 08:20:32'),(841,423,255,'2026-04-27 08:20:32'),(842,423,256,'2026-04-27 08:20:32'),(843,423,257,'2026-04-27 08:20:32'),(844,423,258,'2026-04-27 08:20:32'),(845,423,424,'2026-04-27 08:20:32'),(846,423,425,'2026-04-27 08:20:32'),(847,423,426,'2026-04-27 08:20:32'),(848,424,255,'2026-04-27 08:20:32'),(849,424,256,'2026-04-27 08:20:32'),(850,424,257,'2026-04-27 08:20:32'),(851,424,258,'2026-04-27 08:20:32'),(852,424,423,'2026-04-27 08:20:32'),(853,424,425,'2026-04-27 08:20:32'),(854,424,426,'2026-04-27 08:20:32'),(855,425,255,'2026-04-27 08:20:32'),(856,425,256,'2026-04-27 08:20:32'),(857,425,257,'2026-04-27 08:20:32'),(858,425,258,'2026-04-27 08:20:32'),(859,425,423,'2026-04-27 08:20:32'),(860,425,424,'2026-04-27 08:20:32'),(861,425,426,'2026-04-27 08:20:32'),(862,426,255,'2026-04-27 08:20:32'),(863,426,256,'2026-04-27 08:20:32'),(864,426,257,'2026-04-27 08:20:32'),(865,426,258,'2026-04-27 08:20:32'),(866,426,423,'2026-04-27 08:20:32'),(867,426,424,'2026-04-27 08:20:32'),(868,426,425,'2026-04-27 08:20:32'),(869,427,215,'2026-04-27 08:20:32'),(870,427,216,'2026-04-27 08:20:32'),(871,427,217,'2026-04-27 08:20:32'),(872,427,218,'2026-04-27 08:20:32'),(873,427,428,'2026-04-27 08:20:32'),(874,427,429,'2026-04-27 08:20:32'),(875,427,430,'2026-04-27 08:20:32'),(876,428,215,'2026-04-27 08:20:32'),(877,428,216,'2026-04-27 08:20:32'),(878,428,217,'2026-04-27 08:20:32'),(879,428,218,'2026-04-27 08:20:32'),(880,428,427,'2026-04-27 08:20:32'),(881,428,429,'2026-04-27 08:20:32'),(882,428,430,'2026-04-27 08:20:32'),(883,429,215,'2026-04-27 08:20:32'),(884,429,216,'2026-04-27 08:20:32'),(885,429,217,'2026-04-27 08:20:32'),(886,429,218,'2026-04-27 08:20:32'),(887,429,427,'2026-04-27 08:20:32'),(888,429,428,'2026-04-27 08:20:32'),(889,429,430,'2026-04-27 08:20:32'),(890,430,215,'2026-04-27 08:20:32'),(891,430,216,'2026-04-27 08:20:32'),(892,430,217,'2026-04-27 08:20:32'),(893,430,218,'2026-04-27 08:20:32'),(894,430,427,'2026-04-27 08:20:32'),(895,430,428,'2026-04-27 08:20:32'),(896,430,429,'2026-04-27 08:20:32'),(897,431,432,'2026-04-27 08:20:32'),(898,431,433,'2026-04-27 08:20:32'),(899,431,434,'2026-04-27 08:20:32'),(900,432,431,'2026-04-27 08:20:32'),(901,432,433,'2026-04-27 08:20:32'),(902,432,434,'2026-04-27 08:20:32'),(903,433,431,'2026-04-27 08:20:32'),(904,433,432,'2026-04-27 08:20:32'),(905,433,434,'2026-04-27 08:20:32'),(906,434,431,'2026-04-27 08:20:32'),(907,434,432,'2026-04-27 08:20:32'),(908,434,433,'2026-04-27 08:20:32'),(909,435,235,'2026-04-27 08:20:32'),(910,435,236,'2026-04-27 08:20:32'),(911,435,237,'2026-04-27 08:20:32'),(912,435,238,'2026-04-27 08:20:32'),(913,435,436,'2026-04-27 08:20:32'),(914,435,437,'2026-04-27 08:20:32'),(915,435,438,'2026-04-27 08:20:32'),(916,436,235,'2026-04-27 08:20:32'),(917,436,236,'2026-04-27 08:20:32'),(918,436,237,'2026-04-27 08:20:32'),(919,436,238,'2026-04-27 08:20:32'),(920,436,435,'2026-04-27 08:20:32'),(921,436,437,'2026-04-27 08:20:32'),(922,436,438,'2026-04-27 08:20:32'),(923,437,235,'2026-04-27 08:20:32'),(924,437,236,'2026-04-27 08:20:32'),(925,437,237,'2026-04-27 08:20:32'),(926,437,238,'2026-04-27 08:20:32'),(927,437,435,'2026-04-27 08:20:32'),(928,437,436,'2026-04-27 08:20:32'),(929,437,438,'2026-04-27 08:20:32'),(930,438,235,'2026-04-27 08:20:32'),(931,438,236,'2026-04-27 08:20:32'),(932,438,237,'2026-04-27 08:20:32'),(933,438,238,'2026-04-27 08:20:32'),(934,438,435,'2026-04-27 08:20:32'),(935,438,436,'2026-04-27 08:20:32'),(936,438,437,'2026-04-27 08:20:32'),(937,439,291,'2026-04-27 08:20:32'),(938,439,292,'2026-04-27 08:20:32'),(939,439,293,'2026-04-27 08:20:32'),(940,439,294,'2026-04-27 08:20:32'),(941,439,440,'2026-04-27 08:20:32'),(942,439,441,'2026-04-27 08:20:32'),(943,439,442,'2026-04-27 08:20:32'),(944,440,291,'2026-04-27 08:20:32'),(945,440,292,'2026-04-27 08:20:32'),(946,440,293,'2026-04-27 08:20:32'),(947,440,294,'2026-04-27 08:20:32'),(948,440,439,'2026-04-27 08:20:32'),(949,440,441,'2026-04-27 08:20:32'),(950,440,442,'2026-04-27 08:20:32'),(951,441,291,'2026-04-27 08:20:32'),(952,441,292,'2026-04-27 08:20:32'),(953,441,293,'2026-04-27 08:20:32'),(954,441,294,'2026-04-27 08:20:32'),(955,441,439,'2026-04-27 08:20:32'),(956,441,440,'2026-04-27 08:20:32'),(957,441,442,'2026-04-27 08:20:32'),(958,442,291,'2026-04-27 08:20:32'),(959,442,292,'2026-04-27 08:20:32'),(960,442,293,'2026-04-27 08:20:32'),(961,442,294,'2026-04-27 08:20:32'),(962,442,439,'2026-04-27 08:20:32'),(963,442,440,'2026-04-27 08:20:32'),(964,442,441,'2026-04-27 08:20:32'),(965,443,444,'2026-04-27 08:20:32'),(966,443,445,'2026-04-27 08:20:32'),(967,443,446,'2026-04-27 08:20:32'),(968,444,443,'2026-04-27 08:20:32'),(969,444,445,'2026-04-27 08:20:32'),(970,444,446,'2026-04-27 08:20:32'),(971,445,443,'2026-04-27 08:20:32'),(972,445,444,'2026-04-27 08:20:32'),(973,445,446,'2026-04-27 08:20:32'),(974,446,443,'2026-04-27 08:20:32'),(975,446,444,'2026-04-27 08:20:32'),(976,446,445,'2026-04-27 08:20:32'),(977,447,448,'2026-04-27 08:20:32'),(978,447,449,'2026-04-27 08:20:32'),(979,447,450,'2026-04-27 08:20:32'),(980,448,447,'2026-04-27 08:20:32'),(981,448,449,'2026-04-27 08:20:32'),(982,448,450,'2026-04-27 08:20:32'),(983,449,447,'2026-04-27 08:20:32'),(984,449,448,'2026-04-27 08:20:32'),(985,449,450,'2026-04-27 08:20:32'),(986,450,447,'2026-04-27 08:20:32'),(987,450,448,'2026-04-27 08:20:32'),(988,450,449,'2026-04-27 08:20:32'),(989,451,452,'2026-04-27 08:20:32'),(990,451,453,'2026-04-27 08:20:32'),(991,451,454,'2026-04-27 08:20:32'),(992,452,451,'2026-04-27 08:20:32'),(993,452,453,'2026-04-27 08:20:32'),(994,452,454,'2026-04-27 08:20:32'),(995,453,451,'2026-04-27 08:20:32'),(996,453,452,'2026-04-27 08:20:32'),(997,453,454,'2026-04-27 08:20:32'),(998,454,451,'2026-04-27 08:20:32'),(999,454,452,'2026-04-27 08:20:32'),(1000,454,453,'2026-04-27 08:20:32'),(1001,455,359,'2026-04-27 08:20:32'),(1002,455,360,'2026-04-27 08:20:32'),(1003,455,361,'2026-04-27 08:20:32'),(1004,455,362,'2026-04-27 08:20:32'),(1005,455,456,'2026-04-27 08:20:32'),(1006,455,457,'2026-04-27 08:20:32'),(1007,455,458,'2026-04-27 08:20:32'),(1008,456,359,'2026-04-27 08:20:32'),(1009,456,360,'2026-04-27 08:20:32'),(1010,456,361,'2026-04-27 08:20:32'),(1011,456,362,'2026-04-27 08:20:32'),(1012,456,455,'2026-04-27 08:20:32'),(1013,456,457,'2026-04-27 08:20:32'),(1014,456,458,'2026-04-27 08:20:32'),(1015,457,359,'2026-04-27 08:20:32'),(1016,457,360,'2026-04-27 08:20:32'),(1017,457,361,'2026-04-27 08:20:32'),(1018,457,362,'2026-04-27 08:20:32'),(1019,457,455,'2026-04-27 08:20:32'),(1020,457,456,'2026-04-27 08:20:32'),(1021,457,458,'2026-04-27 08:20:32'),(1022,458,359,'2026-04-27 08:20:32'),(1023,458,360,'2026-04-27 08:20:32'),(1024,458,361,'2026-04-27 08:20:32'),(1025,458,362,'2026-04-27 08:20:32'),(1026,458,455,'2026-04-27 08:20:32'),(1027,458,456,'2026-04-27 08:20:32'),(1028,458,457,'2026-04-27 08:20:32'),(1029,459,460,'2026-04-27 08:20:32'),(1030,459,461,'2026-04-27 08:20:32'),(1031,459,462,'2026-04-27 08:20:32'),(1032,460,459,'2026-04-27 08:20:32'),(1033,460,461,'2026-04-27 08:20:32'),(1034,460,462,'2026-04-27 08:20:32'),(1035,461,459,'2026-04-27 08:20:32'),(1036,461,460,'2026-04-27 08:20:32'),(1037,461,462,'2026-04-27 08:20:32'),(1038,462,459,'2026-04-27 08:20:32'),(1039,462,460,'2026-04-27 08:20:32'),(1040,462,461,'2026-04-27 08:20:32'),(1041,463,464,'2026-04-27 08:20:32'),(1042,463,465,'2026-04-27 08:20:32'),(1043,463,466,'2026-04-27 08:20:32'),(1044,464,463,'2026-04-27 08:20:32'),(1045,464,465,'2026-04-27 08:20:32'),(1046,464,466,'2026-04-27 08:20:32'),(1047,465,463,'2026-04-27 08:20:32'),(1048,465,464,'2026-04-27 08:20:32'),(1049,465,466,'2026-04-27 08:20:32'),(1050,466,463,'2026-04-27 08:20:32'),(1051,466,464,'2026-04-27 08:20:32'),(1052,466,465,'2026-04-27 08:20:32'),(1053,467,468,'2026-04-27 08:20:32'),(1054,467,469,'2026-04-27 08:20:32'),(1055,467,470,'2026-04-27 08:20:32'),(1056,468,467,'2026-04-27 08:20:32'),(1057,468,469,'2026-04-27 08:20:32'),(1058,468,470,'2026-04-27 08:20:32'),(1059,469,467,'2026-04-27 08:20:32'),(1060,469,468,'2026-04-27 08:20:32'),(1061,469,470,'2026-04-27 08:20:32'),(1062,470,467,'2026-04-27 08:20:32'),(1063,470,468,'2026-04-27 08:20:32'),(1064,470,469,'2026-04-27 08:20:32'),(1065,471,472,'2026-04-27 08:20:32'),(1066,471,473,'2026-04-27 08:20:32'),(1067,471,474,'2026-04-27 08:20:32'),(1068,472,471,'2026-04-27 08:20:32'),(1069,472,473,'2026-04-27 08:20:32'),(1070,472,474,'2026-04-27 08:20:32'),(1071,473,471,'2026-04-27 08:20:32'),(1072,473,472,'2026-04-27 08:20:32'),(1073,473,474,'2026-04-27 08:20:32'),(1074,474,471,'2026-04-27 08:20:32'),(1075,474,472,'2026-04-27 08:20:32'),(1076,474,473,'2026-04-27 08:20:32'),(1077,475,476,'2026-04-27 08:20:32'),(1078,475,477,'2026-04-27 08:20:32'),(1079,475,478,'2026-04-27 08:20:32'),(1080,476,475,'2026-04-27 08:20:32'),(1081,476,477,'2026-04-27 08:20:32'),(1082,476,478,'2026-04-27 08:20:32'),(1083,477,475,'2026-04-27 08:20:32'),(1084,477,476,'2026-04-27 08:20:32'),(1085,477,478,'2026-04-27 08:20:32'),(1086,478,475,'2026-04-27 08:20:32'),(1087,478,476,'2026-04-27 08:20:32'),(1088,478,477,'2026-04-27 08:20:32'),(1089,479,480,'2026-04-27 08:20:32'),(1090,479,481,'2026-04-27 08:20:32'),(1091,479,482,'2026-04-27 08:20:32'),(1092,480,479,'2026-04-27 08:20:32'),(1093,480,481,'2026-04-27 08:20:32'),(1094,480,482,'2026-04-27 08:20:32'),(1095,481,479,'2026-04-27 08:20:32'),(1096,481,480,'2026-04-27 08:20:32'),(1097,481,482,'2026-04-27 08:20:32'),(1098,482,479,'2026-04-27 08:20:32'),(1099,482,480,'2026-04-27 08:20:32'),(1100,482,481,'2026-04-27 08:20:32'),(1101,483,484,'2026-04-27 08:20:32'),(1102,483,485,'2026-04-27 08:20:32'),(1103,483,486,'2026-04-27 08:20:32'),(1104,484,483,'2026-04-27 08:20:32'),(1105,484,485,'2026-04-27 08:20:32'),(1106,484,486,'2026-04-27 08:20:32'),(1107,485,483,'2026-04-27 08:20:32'),(1108,485,484,'2026-04-27 08:20:32'),(1109,485,486,'2026-04-27 08:20:32'),(1110,486,483,'2026-04-27 08:20:32'),(1111,486,484,'2026-04-27 08:20:32'),(1112,486,485,'2026-04-27 08:20:32'),(1113,487,488,'2026-04-27 08:20:32'),(1114,487,489,'2026-04-27 08:20:32'),(1115,487,490,'2026-04-27 08:20:32'),(1116,487,579,'2026-04-27 08:20:32'),(1117,487,580,'2026-04-27 08:20:32'),(1118,487,581,'2026-04-27 08:20:32'),(1119,487,582,'2026-04-27 08:20:32'),(1120,488,487,'2026-04-27 08:20:32'),(1121,488,489,'2026-04-27 08:20:32'),(1122,488,490,'2026-04-27 08:20:32'),(1123,488,579,'2026-04-27 08:20:32'),(1124,488,580,'2026-04-27 08:20:32'),(1125,488,581,'2026-04-27 08:20:32'),(1126,488,582,'2026-04-27 08:20:32'),(1127,489,487,'2026-04-27 08:20:32'),(1128,489,488,'2026-04-27 08:20:32'),(1129,489,490,'2026-04-27 08:20:32'),(1130,489,579,'2026-04-27 08:20:32'),(1131,489,580,'2026-04-27 08:20:32'),(1132,489,581,'2026-04-27 08:20:32'),(1133,489,582,'2026-04-27 08:20:32'),(1134,490,487,'2026-04-27 08:20:32'),(1135,490,488,'2026-04-27 08:20:32'),(1136,490,489,'2026-04-27 08:20:32'),(1137,490,579,'2026-04-27 08:20:32'),(1138,490,580,'2026-04-27 08:20:32'),(1139,490,581,'2026-04-27 08:20:32'),(1140,490,582,'2026-04-27 08:20:32'),(1141,491,492,'2026-04-27 08:20:32'),(1142,491,493,'2026-04-27 08:20:32'),(1143,491,494,'2026-04-27 08:20:32'),(1144,492,491,'2026-04-27 08:20:32'),(1145,492,493,'2026-04-27 08:20:32'),(1146,492,494,'2026-04-27 08:20:32'),(1147,493,491,'2026-04-27 08:20:32'),(1148,493,492,'2026-04-27 08:20:32'),(1149,493,494,'2026-04-27 08:20:32'),(1150,494,491,'2026-04-27 08:20:32'),(1151,494,492,'2026-04-27 08:20:32'),(1152,494,493,'2026-04-27 08:20:32'),(1153,495,496,'2026-04-27 08:20:32'),(1154,495,497,'2026-04-27 08:20:32'),(1155,495,498,'2026-04-27 08:20:32'),(1156,495,575,'2026-04-27 08:20:32'),(1157,495,576,'2026-04-27 08:20:32'),(1158,495,577,'2026-04-27 08:20:32'),(1159,495,578,'2026-04-27 08:20:32'),(1160,496,495,'2026-04-27 08:20:32'),(1161,496,497,'2026-04-27 08:20:32'),(1162,496,498,'2026-04-27 08:20:32'),(1163,496,575,'2026-04-27 08:20:32'),(1164,496,576,'2026-04-27 08:20:32'),(1165,496,577,'2026-04-27 08:20:32'),(1166,496,578,'2026-04-27 08:20:32'),(1167,497,495,'2026-04-27 08:20:32'),(1168,497,496,'2026-04-27 08:20:32'),(1169,497,498,'2026-04-27 08:20:32'),(1170,497,575,'2026-04-27 08:20:32'),(1171,497,576,'2026-04-27 08:20:32'),(1172,497,577,'2026-04-27 08:20:32'),(1173,497,578,'2026-04-27 08:20:32'),(1174,498,495,'2026-04-27 08:20:32'),(1175,498,496,'2026-04-27 08:20:32'),(1176,498,497,'2026-04-27 08:20:32'),(1177,498,575,'2026-04-27 08:20:32'),(1178,498,576,'2026-04-27 08:20:32'),(1179,498,577,'2026-04-27 08:20:32'),(1180,498,578,'2026-04-27 08:20:32'),(1181,499,500,'2026-04-27 08:20:32'),(1182,499,501,'2026-04-27 08:20:32'),(1183,499,502,'2026-04-27 08:20:32'),(1184,500,499,'2026-04-27 08:20:32'),(1185,500,501,'2026-04-27 08:20:32'),(1186,500,502,'2026-04-27 08:20:32'),(1187,501,499,'2026-04-27 08:20:32'),(1188,501,500,'2026-04-27 08:20:32'),(1189,501,502,'2026-04-27 08:20:32'),(1190,502,499,'2026-04-27 08:20:32'),(1191,502,500,'2026-04-27 08:20:32'),(1192,502,501,'2026-04-27 08:20:32'),(1193,503,504,'2026-04-27 08:20:32'),(1194,503,505,'2026-04-27 08:20:32'),(1195,503,506,'2026-04-27 08:20:32'),(1196,504,503,'2026-04-27 08:20:32'),(1197,504,505,'2026-04-27 08:20:32'),(1198,504,506,'2026-04-27 08:20:32'),(1199,505,503,'2026-04-27 08:20:32'),(1200,505,504,'2026-04-27 08:20:32'),(1201,505,506,'2026-04-27 08:20:32'),(1202,506,503,'2026-04-27 08:20:32'),(1203,506,504,'2026-04-27 08:20:32'),(1204,506,505,'2026-04-27 08:20:32'),(1205,507,508,'2026-04-27 08:20:32'),(1206,507,509,'2026-04-27 08:20:32'),(1207,507,510,'2026-04-27 08:20:32'),(1208,508,507,'2026-04-27 08:20:32'),(1209,508,509,'2026-04-27 08:20:32'),(1210,508,510,'2026-04-27 08:20:32'),(1211,509,507,'2026-04-27 08:20:32'),(1212,509,508,'2026-04-27 08:20:32'),(1213,509,510,'2026-04-27 08:20:32'),(1214,510,507,'2026-04-27 08:20:32'),(1215,510,508,'2026-04-27 08:20:32'),(1216,510,509,'2026-04-27 08:20:32'),(1217,511,512,'2026-04-27 08:20:32'),(1218,511,513,'2026-04-27 08:20:32'),(1219,511,514,'2026-04-27 08:20:32'),(1220,512,511,'2026-04-27 08:20:32'),(1221,512,513,'2026-04-27 08:20:32'),(1222,512,514,'2026-04-27 08:20:32'),(1223,513,511,'2026-04-27 08:20:32'),(1224,513,512,'2026-04-27 08:20:32'),(1225,513,514,'2026-04-27 08:20:32'),(1226,514,511,'2026-04-27 08:20:32'),(1227,514,512,'2026-04-27 08:20:32'),(1228,514,513,'2026-04-27 08:20:32'),(1229,515,516,'2026-04-27 08:20:32'),(1230,515,517,'2026-04-27 08:20:32'),(1231,515,518,'2026-04-27 08:20:32'),(1232,516,515,'2026-04-27 08:20:32'),(1233,516,517,'2026-04-27 08:20:32'),(1234,516,518,'2026-04-27 08:20:32'),(1235,517,515,'2026-04-27 08:20:32'),(1236,517,516,'2026-04-27 08:20:32'),(1237,517,518,'2026-04-27 08:20:32'),(1238,518,515,'2026-04-27 08:20:32'),(1239,518,516,'2026-04-27 08:20:32'),(1240,518,517,'2026-04-27 08:20:32'),(1241,519,520,'2026-04-27 08:20:32'),(1242,519,521,'2026-04-27 08:20:32'),(1243,519,522,'2026-04-27 08:20:32'),(1244,520,519,'2026-04-27 08:20:32'),(1245,520,521,'2026-04-27 08:20:32'),(1246,520,522,'2026-04-27 08:20:32'),(1247,521,519,'2026-04-27 08:20:32'),(1248,521,520,'2026-04-27 08:20:32'),(1249,521,522,'2026-04-27 08:20:32'),(1250,522,519,'2026-04-27 08:20:32'),(1251,522,520,'2026-04-27 08:20:32'),(1252,522,521,'2026-04-27 08:20:32'),(1253,523,524,'2026-04-27 08:20:32'),(1254,523,525,'2026-04-27 08:20:32'),(1255,523,526,'2026-04-27 08:20:32'),(1256,524,523,'2026-04-27 08:20:32'),(1257,524,525,'2026-04-27 08:20:32'),(1258,524,526,'2026-04-27 08:20:32'),(1259,525,523,'2026-04-27 08:20:32'),(1260,525,524,'2026-04-27 08:20:32'),(1261,525,526,'2026-04-27 08:20:32'),(1262,526,523,'2026-04-27 08:20:32'),(1263,526,524,'2026-04-27 08:20:32'),(1264,526,525,'2026-04-27 08:20:32'),(1265,527,528,'2026-04-27 08:20:32'),(1266,527,529,'2026-04-27 08:20:32'),(1267,527,530,'2026-04-27 08:20:32'),(1268,528,527,'2026-04-27 08:20:32'),(1269,528,529,'2026-04-27 08:20:32'),(1270,528,530,'2026-04-27 08:20:32'),(1271,529,527,'2026-04-27 08:20:32'),(1272,529,528,'2026-04-27 08:20:32'),(1273,529,530,'2026-04-27 08:20:32'),(1274,530,527,'2026-04-27 08:20:32'),(1275,530,528,'2026-04-27 08:20:32'),(1276,530,529,'2026-04-27 08:20:32'),(1277,531,532,'2026-04-27 08:20:32'),(1278,531,533,'2026-04-27 08:20:32'),(1279,531,534,'2026-04-27 08:20:32'),(1280,532,531,'2026-04-27 08:20:32'),(1281,532,533,'2026-04-27 08:20:32'),(1282,532,534,'2026-04-27 08:20:32'),(1283,533,531,'2026-04-27 08:20:32'),(1284,533,532,'2026-04-27 08:20:32'),(1285,533,534,'2026-04-27 08:20:32'),(1286,534,531,'2026-04-27 08:20:32'),(1287,534,532,'2026-04-27 08:20:32'),(1288,534,533,'2026-04-27 08:20:32'),(1289,535,536,'2026-04-27 08:20:32'),(1290,535,537,'2026-04-27 08:20:32'),(1291,535,538,'2026-04-27 08:20:32'),(1292,536,535,'2026-04-27 08:20:32'),(1293,536,537,'2026-04-27 08:20:32'),(1294,536,538,'2026-04-27 08:20:32'),(1295,537,535,'2026-04-27 08:20:32'),(1296,537,536,'2026-04-27 08:20:32'),(1297,537,538,'2026-04-27 08:20:32'),(1298,538,535,'2026-04-27 08:20:32'),(1299,538,536,'2026-04-27 08:20:32'),(1300,538,537,'2026-04-27 08:20:32'),(1301,539,540,'2026-04-27 08:20:32'),(1302,539,541,'2026-04-27 08:20:32'),(1303,539,542,'2026-04-27 08:20:32'),(1304,540,539,'2026-04-27 08:20:32'),(1305,540,541,'2026-04-27 08:20:32'),(1306,540,542,'2026-04-27 08:20:32'),(1307,541,539,'2026-04-27 08:20:32'),(1308,541,540,'2026-04-27 08:20:32'),(1309,541,542,'2026-04-27 08:20:32'),(1310,542,539,'2026-04-27 08:20:32'),(1311,542,540,'2026-04-27 08:20:32'),(1312,542,541,'2026-04-27 08:20:32'),(1313,543,544,'2026-04-27 08:20:32'),(1314,543,545,'2026-04-27 08:20:32'),(1315,543,546,'2026-04-27 08:20:32'),(1316,544,543,'2026-04-27 08:20:32'),(1317,544,545,'2026-04-27 08:20:32'),(1318,544,546,'2026-04-27 08:20:32'),(1319,545,543,'2026-04-27 08:20:32'),(1320,545,544,'2026-04-27 08:20:32'),(1321,545,546,'2026-04-27 08:20:32'),(1322,546,543,'2026-04-27 08:20:32'),(1323,546,544,'2026-04-27 08:20:32'),(1324,546,545,'2026-04-27 08:20:32'),(1325,547,548,'2026-04-27 08:20:32'),(1326,547,549,'2026-04-27 08:20:32'),(1327,547,550,'2026-04-27 08:20:32'),(1328,548,547,'2026-04-27 08:20:32'),(1329,548,549,'2026-04-27 08:20:32'),(1330,548,550,'2026-04-27 08:20:32'),(1331,549,547,'2026-04-27 08:20:32'),(1332,549,548,'2026-04-27 08:20:32'),(1333,549,550,'2026-04-27 08:20:32'),(1334,550,547,'2026-04-27 08:20:32'),(1335,550,548,'2026-04-27 08:20:32'),(1336,550,549,'2026-04-27 08:20:32'),(1337,551,552,'2026-04-27 08:20:32'),(1338,551,553,'2026-04-27 08:20:32'),(1339,551,554,'2026-04-27 08:20:32'),(1340,552,551,'2026-04-27 08:20:32'),(1341,552,553,'2026-04-27 08:20:32'),(1342,552,554,'2026-04-27 08:20:32'),(1343,553,551,'2026-04-27 08:20:32'),(1344,553,552,'2026-04-27 08:20:32'),(1345,553,554,'2026-04-27 08:20:32'),(1346,554,551,'2026-04-27 08:20:32'),(1347,554,552,'2026-04-27 08:20:32'),(1348,554,553,'2026-04-27 08:20:32'),(1349,555,556,'2026-04-27 08:20:32'),(1350,555,557,'2026-04-27 08:20:32'),(1351,555,558,'2026-04-27 08:20:32'),(1352,556,555,'2026-04-27 08:20:32'),(1353,556,557,'2026-04-27 08:20:32'),(1354,556,558,'2026-04-27 08:20:32'),(1355,557,555,'2026-04-27 08:20:32'),(1356,557,556,'2026-04-27 08:20:32'),(1357,557,558,'2026-04-27 08:20:32'),(1358,558,555,'2026-04-27 08:20:32'),(1359,558,556,'2026-04-27 08:20:32'),(1360,558,557,'2026-04-27 08:20:32'),(1361,559,560,'2026-04-27 08:20:32'),(1362,559,561,'2026-04-27 08:20:32'),(1363,559,562,'2026-04-27 08:20:32'),(1364,560,559,'2026-04-27 08:20:32'),(1365,560,561,'2026-04-27 08:20:32'),(1366,560,562,'2026-04-27 08:20:32'),(1367,561,559,'2026-04-27 08:20:32'),(1368,561,560,'2026-04-27 08:20:32'),(1369,561,562,'2026-04-27 08:20:32'),(1370,562,559,'2026-04-27 08:20:32'),(1371,562,560,'2026-04-27 08:20:32'),(1372,562,561,'2026-04-27 08:20:32'),(1373,563,564,'2026-04-27 08:20:32'),(1374,563,565,'2026-04-27 08:20:32'),(1375,563,566,'2026-04-27 08:20:32'),(1376,564,563,'2026-04-27 08:20:32'),(1377,564,565,'2026-04-27 08:20:32'),(1378,564,566,'2026-04-27 08:20:32'),(1379,565,563,'2026-04-27 08:20:32'),(1380,565,564,'2026-04-27 08:20:32'),(1381,565,566,'2026-04-27 08:20:32'),(1382,566,563,'2026-04-27 08:20:32'),(1383,566,564,'2026-04-27 08:20:32'),(1384,566,565,'2026-04-27 08:20:32'),(1385,567,568,'2026-04-27 08:20:32'),(1386,567,569,'2026-04-27 08:20:32'),(1387,567,570,'2026-04-27 08:20:32'),(1388,568,567,'2026-04-27 08:20:32'),(1389,568,569,'2026-04-27 08:20:32'),(1390,568,570,'2026-04-27 08:20:32'),(1391,569,567,'2026-04-27 08:20:32'),(1392,569,568,'2026-04-27 08:20:32'),(1393,569,570,'2026-04-27 08:20:32'),(1394,570,567,'2026-04-27 08:20:32'),(1395,570,568,'2026-04-27 08:20:32'),(1396,570,569,'2026-04-27 08:20:32'),(1397,571,572,'2026-04-27 08:20:32'),(1398,571,573,'2026-04-27 08:20:32'),(1399,571,574,'2026-04-27 08:20:32'),(1400,571,583,'2026-04-27 08:20:32'),(1401,571,584,'2026-04-27 08:20:32'),(1402,571,585,'2026-04-27 08:20:32'),(1403,571,586,'2026-04-27 08:20:32'),(1404,572,571,'2026-04-27 08:20:32'),(1405,572,573,'2026-04-27 08:20:32'),(1406,572,574,'2026-04-27 08:20:32'),(1407,572,583,'2026-04-27 08:20:32'),(1408,572,584,'2026-04-27 08:20:32'),(1409,572,585,'2026-04-27 08:20:32'),(1410,572,586,'2026-04-27 08:20:32'),(1411,573,571,'2026-04-27 08:20:32'),(1412,573,572,'2026-04-27 08:20:32'),(1413,573,574,'2026-04-27 08:20:32'),(1414,573,583,'2026-04-27 08:20:32'),(1415,573,584,'2026-04-27 08:20:32'),(1416,573,585,'2026-04-27 08:20:32'),(1417,573,586,'2026-04-27 08:20:32'),(1418,574,571,'2026-04-27 08:20:32'),(1419,574,572,'2026-04-27 08:20:32'),(1420,574,573,'2026-04-27 08:20:32'),(1421,574,583,'2026-04-27 08:20:32'),(1422,574,584,'2026-04-27 08:20:32'),(1423,574,585,'2026-04-27 08:20:32'),(1424,574,586,'2026-04-27 08:20:32'),(1425,575,495,'2026-04-27 08:20:32'),(1426,575,496,'2026-04-27 08:20:32'),(1427,575,497,'2026-04-27 08:20:32'),(1428,575,498,'2026-04-27 08:20:32'),(1429,575,576,'2026-04-27 08:20:32'),(1430,575,577,'2026-04-27 08:20:32'),(1431,575,578,'2026-04-27 08:20:32'),(1432,576,495,'2026-04-27 08:20:32'),(1433,576,496,'2026-04-27 08:20:32'),(1434,576,497,'2026-04-27 08:20:32'),(1435,576,498,'2026-04-27 08:20:32'),(1436,576,575,'2026-04-27 08:20:32'),(1437,576,577,'2026-04-27 08:20:32'),(1438,576,578,'2026-04-27 08:20:32'),(1439,577,495,'2026-04-27 08:20:32'),(1440,577,496,'2026-04-27 08:20:32'),(1441,577,497,'2026-04-27 08:20:32'),(1442,577,498,'2026-04-27 08:20:32'),(1443,577,575,'2026-04-27 08:20:32'),(1444,577,576,'2026-04-27 08:20:32'),(1445,577,578,'2026-04-27 08:20:32'),(1446,578,495,'2026-04-27 08:20:32'),(1447,578,496,'2026-04-27 08:20:32'),(1448,578,497,'2026-04-27 08:20:32'),(1449,578,498,'2026-04-27 08:20:32'),(1450,578,575,'2026-04-27 08:20:32'),(1451,578,576,'2026-04-27 08:20:32'),(1452,578,577,'2026-04-27 08:20:32'),(1453,579,487,'2026-04-27 08:20:32'),(1454,579,488,'2026-04-27 08:20:32'),(1455,579,489,'2026-04-27 08:20:32'),(1456,579,490,'2026-04-27 08:20:32'),(1457,579,580,'2026-04-27 08:20:32'),(1458,579,581,'2026-04-27 08:20:32'),(1459,579,582,'2026-04-27 08:20:32'),(1460,580,487,'2026-04-27 08:20:32'),(1461,580,488,'2026-04-27 08:20:32'),(1462,580,489,'2026-04-27 08:20:32'),(1463,580,490,'2026-04-27 08:20:32'),(1464,580,579,'2026-04-27 08:20:32'),(1465,580,581,'2026-04-27 08:20:32'),(1466,580,582,'2026-04-27 08:20:32'),(1467,581,487,'2026-04-27 08:20:32'),(1468,581,488,'2026-04-27 08:20:32'),(1469,581,489,'2026-04-27 08:20:32'),(1470,581,490,'2026-04-27 08:20:32'),(1471,581,579,'2026-04-27 08:20:32'),(1472,581,580,'2026-04-27 08:20:32'),(1473,581,582,'2026-04-27 08:20:32'),(1474,582,487,'2026-04-27 08:20:32'),(1475,582,488,'2026-04-27 08:20:32'),(1476,582,489,'2026-04-27 08:20:32'),(1477,582,490,'2026-04-27 08:20:32'),(1478,582,579,'2026-04-27 08:20:32'),(1479,582,580,'2026-04-27 08:20:32'),(1480,582,581,'2026-04-27 08:20:32'),(1481,583,571,'2026-04-27 08:20:32'),(1482,583,572,'2026-04-27 08:20:32'),(1483,583,573,'2026-04-27 08:20:32'),(1484,583,574,'2026-04-27 08:20:32'),(1485,583,584,'2026-04-27 08:20:32'),(1486,583,585,'2026-04-27 08:20:32'),(1487,583,586,'2026-04-27 08:20:32'),(1488,584,571,'2026-04-27 08:20:32'),(1489,584,572,'2026-04-27 08:20:32'),(1490,584,573,'2026-04-27 08:20:32'),(1491,584,574,'2026-04-27 08:20:32'),(1492,584,583,'2026-04-27 08:20:32'),(1493,584,585,'2026-04-27 08:20:32'),(1494,584,586,'2026-04-27 08:20:32'),(1495,585,571,'2026-04-27 08:20:32'),(1496,585,572,'2026-04-27 08:20:32'),(1497,585,573,'2026-04-27 08:20:32'),(1498,585,574,'2026-04-27 08:20:32'),(1499,585,583,'2026-04-27 08:20:32'),(1500,585,584,'2026-04-27 08:20:32'),(1501,585,586,'2026-04-27 08:20:32'),(1502,586,571,'2026-04-27 08:20:32'),(1503,586,572,'2026-04-27 08:20:32'),(1504,586,573,'2026-04-27 08:20:32'),(1505,586,574,'2026-04-27 08:20:32'),(1506,586,583,'2026-04-27 08:20:32'),(1507,586,584,'2026-04-27 08:20:32'),(1508,586,585,'2026-04-27 08:20:32');
/*!40000 ALTER TABLE `medicine_substitutes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `medicines`
--

DROP TABLE IF EXISTS `medicines`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `medicines` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `vendor_id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `brand_id` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `salt` varchar(255) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `mrp` decimal(10,2) DEFAULT NULL,
  `stock` int(11) DEFAULT 0,
  `expiry_date` date DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `requires_prescription` tinyint(1) DEFAULT 0,
  `description` text DEFAULT NULL,
  `approval_status` enum('pending','approved','rejected') DEFAULT 'pending',
  `vetting_reason` text DEFAULT NULL,
  `vetting_note` text DEFAULT NULL,
  `vetted_at` timestamp NULL DEFAULT NULL,
  `vetted_by` int(11) DEFAULT NULL,
  `status` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `vendor_id` (`vendor_id`),
  KEY `category_id` (`category_id`),
  KEY `brand_id` (`brand_id`),
  CONSTRAINT `medicines_ibfk_1` FOREIGN KEY (`vendor_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `medicines_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
  CONSTRAINT `medicines_ibfk_3` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=363 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `medicines`
--

LOCK TABLES `medicines` WRITE;
/*!40000 ALTER TABLE `medicines` DISABLE KEYS */;
INSERT INTO `medicines` VALUES (1,2,1,2,'Paracetamol 500mg','paracetamol-500mg-0-mnc','Paracetamol',25.00,29.00,57,NULL,'paracetamol-500mg.jpg',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-11 15:56:43','2026-05-22 04:34:21',NULL),(5,2,1,6,'Amoxicillin Capsules','amoxicillin-capsules-1-mnc','Amoxicillin',61.00,70.00,50,NULL,'amoxicillin_capsules_1778507853087.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-11 15:56:43','2026-05-22 04:34:21',NULL),(9,2,1,2,'Cetirizine 10mg','cetirizine-10mg-2-mnc','Cetirizine',15.00,17.00,49,NULL,'cetirizine_tablets_1778507868145.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-11 15:56:44','2026-05-22 04:34:21',NULL),(13,2,1,6,'Omeprazole 20mg','omeprazole-20mg-3-mnc','Omeprazole',42.00,48.00,52,NULL,'omeprazole_capsules_1778507883875.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-11 15:56:44','2026-05-22 04:34:21',NULL),(17,2,1,2,'Azithromycin 500mg','azithromycin-500mg-4-mnc','Azithromycin',95.00,109.00,78,NULL,'azithromycin_tablets_1778507899119.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-11 15:56:44','2026-05-22 04:34:21',NULL),(21,2,1,6,'Ibuprofen 400mg','ibuprofen-400mg-5-mnc','Ibuprofen',35.00,40.00,55,NULL,'ibuprofen_tablets_1778507914522.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-11 15:56:44','2026-05-22 04:34:21',NULL),(25,2,2,2,'Vitamin C 500mg','vitamin-c-500mg-6-mnc','Ascorbic Acid',89.00,102.00,81,NULL,'vitamin_c_tablets_1778514730165.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-11 15:56:44','2026-05-22 04:34:21',NULL),(29,2,4,6,'Antibiotic Eye Ointment','antibiotic-eye-ointment-7-mnc','Ciprofloxacin',119.00,137.00,67,NULL,'eye_ointment_tube_1778514747227.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-11 15:56:44','2026-05-22 04:34:21',NULL),(33,2,3,2,'Orthopedic Knee Brace','orthopedic-knee-brace-8-mnc','Support Device',46.00,53.00,62,NULL,'knee_brace_box_1778514764394.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-11 15:56:44','2026-05-22 04:34:21',NULL),(37,2,1,6,'Metformin 500mg','metformin-500mg-9-mnc','Metformin',28.00,32.00,87,NULL,'metformin_tablets_1778514780678.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-11 15:56:44','2026-05-22 04:34:21',NULL),(41,2,2,2,'Pediatric Multivitamin Syrup','pediatric-multivitamin-syrup-10-mnc','Vitamin Complex',120.00,138.00,68,NULL,'multivitamin_syrup_1778514797763.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-11 15:56:44','2026-05-22 04:34:21',NULL),(45,2,1,6,'Diclofenac Pain Spray','diclofenac-pain-spray-11-mnc','Diclofenac Sodium',27.00,31.00,70,NULL,'diclofenac_spray_1778514812612.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-11 15:56:44','2026-05-22 04:34:21',NULL),(49,2,1,2,'Calcium + D3 Tablets','calcium-d3-tablets-12-mnc','Calcium Citrate',165.00,190.00,80,NULL,'calcium_tablets_1778514828387.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-11 15:56:44','2026-05-22 04:34:21',NULL),(53,2,1,6,'Telmisartan 40mg','telmisartan-40mg-13-mnc','Telmisartan',52.00,60.00,51,NULL,'telmisartan_tablets_1778514847903.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-11 15:56:44','2026-05-22 04:34:21',NULL),(57,2,6,2,'Albuterol Nebulizer Sol','albuterol-nebulizer-sol-14-mnc','Albuterol Sulfate',305.00,351.00,83,NULL,'albuterol_nebulizer_solution_1778514862544.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-11 15:56:44','2026-05-22 04:34:21',NULL),(61,2,2,6,'Antifungal Dusting Powder','antifungal-dusting-powder-15-mnc','Clotrimazole',67.00,77.00,56,NULL,'antifungal_powder_1778514877151.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-11 15:56:44','2026-05-22 04:34:21',NULL),(65,2,1,2,'Digoxin 0.25mg','digoxin-0-25mg-16-mnc','Digoxin',21.00,24.00,95,NULL,'digoxin_tablets_1778514921406.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-11 15:56:44','2026-05-22 04:34:21',NULL),(69,2,1,6,'Warfarin 5mg','warfarin-5mg-17-mnc','Warfarin Sodium',28.00,32.00,54,NULL,'warfarin_tablets_1778514938540.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-11 15:56:44','2026-05-22 04:34:21',NULL),(73,2,4,2,'Antacid Gel','antacid-gel-18-mnc','Aluminium/Magnesium',95.00,109.00,58,NULL,'antacid-gel.jpg',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-11 15:56:44','2026-05-22 04:34:21',NULL),(77,2,2,6,'Ashwagandha Extract','ashwagandha-extract-19-mnc','Withania Somnifera',128.00,147.00,74,NULL,'ashwagandha-extract.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-11 15:56:44','2026-05-22 04:34:21',NULL),(81,2,1,2,'Aspirin 150mg','aspirin-150mg-20-mnc','Acetylsalicylic Acid',55.00,63.00,85,NULL,'aspirin-150mg.jpg',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-11 15:56:44','2026-05-22 04:34:21',NULL),(85,2,6,6,'Asthma Relief Inhaler','asthma-relief-inhaler-21-mnc','Salbutamol',285.00,328.00,88,NULL,'asthma-relief-inhaler.jpg',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-11 15:56:44','2026-05-22 04:34:21',NULL),(89,2,3,2,'Blood Glucose Monitoring Kit','blood-glucose-monitoring-kit-22-mnc','Diagnostic Kit',79.00,91.00,96,NULL,'blood-glucose-kit.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-11 15:56:44','2026-05-22 04:34:21',NULL),(93,2,4,6,'Clotrimazole Cream','clotrimazole-cream-23-mnc','Clotrimazole',65.00,75.00,92,NULL,'clotrimazole-cream.jpg',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-11 15:56:44','2026-05-22 04:34:21',NULL),(97,2,2,2,'Cough Syrup Expectorant','cough-syrup-expectorant-24-mnc','Guaifenesin',85.00,98.00,95,NULL,'cough-syrup-ex.jpg',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-11 15:56:44','2026-05-22 04:34:21',NULL),(101,2,9,6,'Digital Thermometer','digital-thermometer-25-mnc','Medical Device',250.00,288.00,57,NULL,'digital-thermometer.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-11 15:56:44','2026-05-22 04:34:21',NULL),(105,2,12,2,'Emergency First Aid Kit','emergency-first-aid-kit-26-mnc','Multiple Components',350.00,403.00,78,NULL,'first-aid-kit.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-11 15:56:45','2026-05-22 04:34:21',NULL),(109,2,12,6,'Alcohol Hand Sanitizer','alcohol-hand-sanitizer-27-mnc','70% Alcohol',75.00,86.00,100,NULL,'hand-sanitizer.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-11 15:56:45','2026-05-22 04:34:21',NULL),(113,2,15,2,'Homeopathic Pellets','homeopathic-pellets-28-mnc','Herbal Formula',95.00,109.00,49,NULL,'homeopathic-pellets.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-11 15:56:45','2026-05-22 04:34:21',NULL),(117,2,5,6,'Insulin Glargine Pen','insulin-glargine-pen-29-mnc','Insulin',850.00,978.00,94,NULL,'insulin-glargine.jpg',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-11 15:56:45','2026-05-22 04:34:21',NULL),(121,2,7,2,'Lubricating Eye Drops','lubricating-eye-drops-30-mnc','Sodium Hyaluronate',125.00,144.00,56,NULL,'lubricating-eye-drops.jpg',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-11 15:56:45','2026-05-22 04:55:25',NULL),(125,2,4,6,'Diclofenac Pain Relief Gel','diclofenac-pain-relief-gel-31-mnc','Diclofenac',95.00,109.00,76,NULL,'pain-relief-gel.jpg',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-11 15:56:45','2026-05-22 04:34:21',NULL),(129,2,2,2,'Pediatric Vitamin Drops','pediatric-vitamin-drops-32-mnc','Essential Vitamins',66.00,76.00,54,NULL,'pediatric-vitamin-drops.jpg',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-11 15:56:45','2026-05-22 04:55:25',NULL),(133,2,8,6,'Saline Nasal Drops','saline-nasal-drops-33-mnc','Normal Saline',55.00,63.00,95,NULL,'saline-nasal-drops.jpg',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-11 15:56:45','2026-05-22 04:34:21',NULL),(137,2,11,2,'Sterile Surgical Gloves','sterile-surgical-gloves-34-mnc','Latex Free',120.00,138.00,94,NULL,'surgical-gloves.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-11 15:56:45','2026-05-22 04:34:21',NULL),(141,2,1,6,'Multivitamin Blister Pack','multivitamin-blister-pack-35-mnc','Vitamins/Minerals',145.00,167.00,98,NULL,'tablet_blister.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-11 15:56:45','2026-05-22 04:34:21',NULL),(145,2,5,2,'Vitamin B12 Injection','vitamin-b12-injection-36-mnc','Cyanocobalamin',35.00,40.00,91,NULL,'vitamin-b12-injection.jpg',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-11 15:56:45','2026-05-22 04:34:21',NULL),(149,2,1,6,'Amlodipine 5mg','amlodipine-5mg-37-mnc','Amlodipine',32.00,37.00,66,NULL,'archetypes/generic.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-11 15:56:45','2026-05-22 04:34:21',NULL),(153,2,1,2,'Atorvastatin 10mg','atorvastatin-10mg-38-mnc','Atorvastatin',55.00,63.00,92,NULL,'archetypes/generic.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-11 15:56:45','2026-05-22 04:34:21',NULL),(157,2,1,6,'Losartan 50mg','losartan-50mg-39-mnc','Losartan',45.00,52.00,56,NULL,'archetypes/generic.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-11 15:56:45','2026-05-22 04:34:21',NULL),(161,2,1,2,'Hydrochlorothiazide 12.5mg','hydrochlorothiazide-12-5mg-40-mnc','Hydrochlorothiazide',45.00,52.00,52,NULL,'archetypes/generic.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-11 15:56:45','2026-05-22 04:34:21',NULL),(165,2,1,6,'Gabapentin 300mg','gabapentin-300mg-41-mnc','Gabapentin',85.00,98.00,73,NULL,'archetypes/generic.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-11 15:56:45','2026-05-22 04:34:21',NULL),(169,2,1,2,'Sertraline 50mg','sertraline-50mg-42-mnc','Sertraline',78.00,90.00,59,NULL,'archetypes/generic.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-11 15:56:45','2026-05-22 04:34:21',NULL),(173,2,1,6,'Montelukast 10mg','montelukast-10mg-43-mnc','Montelukast',125.00,144.00,86,NULL,'archetypes/generic.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-11 15:56:45','2026-05-22 04:34:21',NULL),(177,2,1,2,'Pantoprazole 40mg','pantoprazole-40mg-44-mnc','Pantoprazole',58.00,67.00,92,NULL,'archetypes/generic.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-11 15:56:45','2026-05-22 04:34:21',NULL),(181,2,1,6,'Furosemide 20mg','furosemide-20mg-45-mnc','Furosemide',12.00,14.00,75,NULL,'archetypes/generic.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-11 15:56:45','2026-05-22 04:34:21',NULL),(185,2,1,2,'Prednisone 5mg','prednisone-5mg-46-mnc','Prednisone',22.00,25.00,77,NULL,'archetypes/generic.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-11 15:56:45','2026-05-22 04:34:21',NULL),(189,2,1,6,'Lisinopril 20mg','lisinopril-20mg-47-mnc','Lisinopril',48.00,55.00,60,NULL,'archetypes/generic.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-11 15:56:45','2026-05-22 04:34:21',NULL),(193,2,1,2,'Metoprolol 25mg','metoprolol-25mg-48-mnc','Metoprolol',38.00,44.00,50,NULL,'archetypes/generic.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-11 15:56:45','2026-05-22 04:34:21',NULL),(197,2,1,6,'Simvastatin 40mg','simvastatin-40mg-49-mnc','Simvastatin',62.00,71.00,70,NULL,'archetypes/generic.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-11 15:56:45','2026-05-22 04:34:21',NULL),(201,2,1,NULL,'parace.','parace--79cb',NULL,15.00,15.00,53,NULL,NULL,0,'','pending',NULL,NULL,NULL,NULL,1,'2026-05-21 15:39:51','2026-05-22 04:55:25',NULL),(202,2,1,NULL,'para 2','para-2-b2c9',NULL,9.00,9.00,42,NULL,NULL,0,'Tablet','approved','Verified for distribution',NULL,'2026-05-22 10:58:12',1,1,'2026-05-21 15:46:26','2026-05-22 14:28:12',NULL),(203,2,1,NULL,'parace3','parace3-d086',NULL,4.00,4.00,69,NULL,'parace3_1779384573.png',0,'','approved',NULL,NULL,NULL,NULL,1,'2026-05-21 17:29:33','2026-05-21 17:29:33',NULL),(204,6,1,NULL,'Paracetamol 500mg','paracetamol-500mg-0-mnc-v6','Paracetamol',13.00,15.00,136,NULL,'paracetamol-500mg.jpg',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(205,6,1,NULL,'Amoxicillin Capsules','amoxicillin-capsules-1-mnc-v6','Amoxicillin',32.00,36.00,74,NULL,'amoxicillin_capsules_1778507853087.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(206,6,1,NULL,'Cetirizine 10mg','cetirizine-10mg-2-mnc-v6','Cetirizine',8.00,9.00,112,NULL,'cetirizine_tablets_1778507868145.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(207,6,1,NULL,'Omeprazole 20mg','omeprazole-20mg-3-mnc-v6','Omeprazole',22.00,25.00,128,NULL,'omeprazole_capsules_1778507883875.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(208,6,1,NULL,'Azithromycin 500mg','azithromycin-500mg-4-mnc-v6','Azithromycin',49.00,55.00,86,NULL,'azithromycin_tablets_1778507899119.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(209,6,1,NULL,'Ibuprofen 400mg','ibuprofen-400mg-5-mnc-v6','Ibuprofen',18.00,20.00,66,NULL,'ibuprofen_tablets_1778507914522.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(210,6,2,NULL,'Vitamin C 500mg','vitamin-c-500mg-6-mnc-v6','Ascorbic Acid',46.00,52.00,30,NULL,'vitamin_c_tablets_1778514730165.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(211,6,4,NULL,'Antibiotic Eye Ointment','antibiotic-eye-ointment-7-mnc-v6','Ciprofloxacin',62.00,69.00,27,NULL,'eye_ointment_tube_1778514747227.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(212,6,3,NULL,'Orthopedic Knee Brace','orthopedic-knee-brace-8-mnc-v6','Support Device',24.00,27.00,23,NULL,'knee_brace_box_1778514764394.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(213,6,1,NULL,'Metformin 500mg','metformin-500mg-9-mnc-v6','Metformin',15.00,17.00,85,NULL,'metformin_tablets_1778514780678.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(214,6,2,NULL,'Pediatric Multivitamin Syrup','pediatric-multivitamin-syrup-10-mnc-v6','Vitamin Complex',62.00,69.00,120,NULL,'multivitamin_syrup_1778514797763.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(215,6,1,NULL,'Diclofenac Pain Spray','diclofenac-pain-spray-11-mnc-v6','Diclofenac Sodium',14.00,16.00,56,NULL,'diclofenac_spray_1778514812612.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(216,6,1,NULL,'Calcium + D3 Tablets','calcium-d3-tablets-12-mnc-v6','Calcium Citrate',86.00,96.00,80,NULL,'calcium_tablets_1778514828387.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(217,6,1,NULL,'Telmisartan 40mg','telmisartan-40mg-13-mnc-v6','Telmisartan',27.00,30.00,52,NULL,'telmisartan_tablets_1778514847903.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(218,6,6,NULL,'Albuterol Nebulizer Sol','albuterol-nebulizer-sol-14-mnc-v6','Albuterol Sulfate',159.00,178.00,63,NULL,'albuterol_nebulizer_solution_1778514862544.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(219,6,2,NULL,'Antifungal Dusting Powder','antifungal-dusting-powder-15-mnc-v6','Clotrimazole',35.00,39.00,123,NULL,'antifungal_powder_1778514877151.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(220,6,1,NULL,'Digoxin 0.25mg','digoxin-0-25mg-16-mnc-v6','Digoxin',11.00,12.00,133,NULL,'digoxin_tablets_1778514921406.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(221,6,1,NULL,'Warfarin 5mg','warfarin-5mg-17-mnc-v6','Warfarin Sodium',15.00,17.00,137,NULL,'warfarin_tablets_1778514938540.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(222,6,4,NULL,'Antacid Gel','antacid-gel-18-mnc-v6','Aluminium/Magnesium',49.00,55.00,31,NULL,'antacid-gel.jpg',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(223,6,2,NULL,'Ashwagandha Extract','ashwagandha-extract-19-mnc-v6','Withania Somnifera',67.00,75.00,79,NULL,'ashwagandha-extract.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(224,6,1,NULL,'Aspirin 150mg','aspirin-150mg-20-mnc-v6','Acetylsalicylic Acid',29.00,32.00,48,NULL,'aspirin-150mg.jpg',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(225,6,6,NULL,'Asthma Relief Inhaler','asthma-relief-inhaler-21-mnc-v6','Salbutamol',148.00,166.00,37,NULL,'asthma-relief-inhaler.jpg',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(226,6,3,NULL,'Blood Glucose Monitoring Kit','blood-glucose-monitoring-kit-22-mnc-v6','Diagnostic Kit',41.00,46.00,106,NULL,'blood-glucose-kit.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(227,6,4,NULL,'Clotrimazole Cream','clotrimazole-cream-23-mnc-v6','Clotrimazole',34.00,38.00,85,NULL,'clotrimazole-cream.jpg',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(228,6,2,NULL,'Cough Syrup Expectorant','cough-syrup-expectorant-24-mnc-v6','Guaifenesin',44.00,49.00,31,NULL,'cough-syrup-ex.jpg',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(229,6,9,NULL,'Digital Thermometer','digital-thermometer-25-mnc-v6','Medical Device',130.00,146.00,118,NULL,'digital-thermometer.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(230,6,12,NULL,'Emergency First Aid Kit','emergency-first-aid-kit-26-mnc-v6','Multiple Components',182.00,204.00,33,NULL,'first-aid-kit.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(231,6,12,NULL,'Alcohol Hand Sanitizer','alcohol-hand-sanitizer-27-mnc-v6','70% Alcohol',39.00,44.00,50,NULL,'hand-sanitizer.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(232,6,15,NULL,'Homeopathic Pellets','homeopathic-pellets-28-mnc-v6','Herbal Formula',49.00,55.00,146,NULL,'homeopathic-pellets.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(233,6,5,NULL,'Insulin Glargine Pen','insulin-glargine-pen-29-mnc-v6','Insulin',442.00,495.00,122,NULL,'insulin-glargine.jpg',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(234,6,7,NULL,'Lubricating Eye Drops','lubricating-eye-drops-30-mnc-v6','Sodium Hyaluronate',65.00,73.00,32,NULL,'lubricating-eye-drops.jpg',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(235,6,4,NULL,'Diclofenac Pain Relief Gel','diclofenac-pain-relief-gel-31-mnc-v6','Diclofenac',49.00,55.00,51,NULL,'pain-relief-gel.jpg',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(236,6,2,NULL,'Pediatric Vitamin Drops','pediatric-vitamin-drops-32-mnc-v6','Essential Vitamins',34.00,38.00,80,NULL,'pediatric-vitamin-drops.jpg',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(237,6,8,NULL,'Saline Nasal Drops','saline-nasal-drops-33-mnc-v6','Normal Saline',29.00,32.00,69,NULL,'saline-nasal-drops.jpg',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(238,6,11,NULL,'Sterile Surgical Gloves','sterile-surgical-gloves-34-mnc-v6','Latex Free',62.00,69.00,103,NULL,'surgical-gloves.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(239,6,1,NULL,'Multivitamin Blister Pack','multivitamin-blister-pack-35-mnc-v6','Vitamins/Minerals',75.00,84.00,112,NULL,'tablet_blister.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(240,6,5,NULL,'Vitamin B12 Injection','vitamin-b12-injection-36-mnc-v6','Cyanocobalamin',18.00,20.00,104,NULL,'vitamin-b12-injection.jpg',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(241,6,1,NULL,'Amlodipine 5mg','amlodipine-5mg-37-mnc-v6','Amlodipine',17.00,19.00,82,NULL,'archetypes/generic.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(242,6,1,NULL,'Atorvastatin 10mg','atorvastatin-10mg-38-mnc-v6','Atorvastatin',29.00,32.00,136,NULL,'archetypes/generic.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(243,6,1,NULL,'Losartan 50mg','losartan-50mg-39-mnc-v6','Losartan',23.00,26.00,136,NULL,'archetypes/generic.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(244,6,1,NULL,'Hydrochlorothiazide 12.5mg','hydrochlorothiazide-12-5mg-40-mnc-v6','Hydrochlorothiazide',23.00,26.00,79,NULL,'archetypes/generic.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(245,6,1,NULL,'Gabapentin 300mg','gabapentin-300mg-41-mnc-v6','Gabapentin',44.00,49.00,46,NULL,'archetypes/generic.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(246,6,1,NULL,'Sertraline 50mg','sertraline-50mg-42-mnc-v6','Sertraline',41.00,46.00,33,NULL,'archetypes/generic.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(247,6,1,NULL,'Montelukast 10mg','montelukast-10mg-43-mnc-v6','Montelukast',65.00,73.00,113,NULL,'archetypes/generic.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(248,6,1,NULL,'Pantoprazole 40mg','pantoprazole-40mg-44-mnc-v6','Pantoprazole',30.00,34.00,136,NULL,'archetypes/generic.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(249,6,1,NULL,'Furosemide 20mg','furosemide-20mg-45-mnc-v6','Furosemide',6.00,7.00,51,NULL,'archetypes/generic.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(250,6,1,NULL,'Prednisone 5mg','prednisone-5mg-46-mnc-v6','Prednisone',11.00,12.00,63,NULL,'archetypes/generic.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(251,6,1,NULL,'Lisinopril 20mg','lisinopril-20mg-47-mnc-v6','Lisinopril',25.00,28.00,46,NULL,'archetypes/generic.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(252,6,1,NULL,'Metoprolol 25mg','metoprolol-25mg-48-mnc-v6','Metoprolol',20.00,22.00,136,NULL,'archetypes/generic.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(253,6,1,NULL,'Simvastatin 40mg','simvastatin-40mg-49-mnc-v6','Simvastatin',32.00,36.00,70,NULL,'archetypes/generic.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(254,6,1,NULL,'parace.','parace--79cb-v6',NULL,8.00,9.00,62,NULL,NULL,0,'','approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(255,6,1,NULL,'para 2','para-2-b2c9-v6',NULL,5.00,6.00,47,NULL,NULL,0,'Tablet','approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(256,6,1,NULL,'parace3','parace3-d086-v6',NULL,2.00,2.00,42,NULL,'parace3_1779384573.png',0,'','approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(257,9,1,NULL,'Paracetamol 500mg','paracetamol-500mg-0-mnc-v9','Paracetamol',18.00,20.00,34,NULL,'paracetamol-500mg.jpg',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(258,9,1,NULL,'Amoxicillin Capsules','amoxicillin-capsules-1-mnc-v9','Amoxicillin',44.00,49.00,70,NULL,'amoxicillin_capsules_1778507853087.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(259,9,1,NULL,'Cetirizine 10mg','cetirizine-10mg-2-mnc-v9','Cetirizine',11.00,12.00,62,NULL,'cetirizine_tablets_1778507868145.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(260,9,1,NULL,'Omeprazole 20mg','omeprazole-20mg-3-mnc-v9','Omeprazole',30.00,34.00,32,NULL,'omeprazole_capsules_1778507883875.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(261,9,1,NULL,'Azithromycin 500mg','azithromycin-500mg-4-mnc-v9','Azithromycin',68.00,76.00,40,NULL,'azithromycin_tablets_1778507899119.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(262,9,1,NULL,'Ibuprofen 400mg','ibuprofen-400mg-5-mnc-v9','Ibuprofen',25.00,28.00,69,NULL,'ibuprofen_tablets_1778507914522.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(263,9,2,NULL,'Vitamin C 500mg','vitamin-c-500mg-6-mnc-v9','Ascorbic Acid',64.00,72.00,148,NULL,'vitamin_c_tablets_1778514730165.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(264,9,4,NULL,'Antibiotic Eye Ointment','antibiotic-eye-ointment-7-mnc-v9','Ciprofloxacin',86.00,96.00,104,NULL,'eye_ointment_tube_1778514747227.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(265,9,3,NULL,'Orthopedic Knee Brace','orthopedic-knee-brace-8-mnc-v9','Support Device',33.00,37.00,117,NULL,'knee_brace_box_1778514764394.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(266,9,1,NULL,'Metformin 500mg','metformin-500mg-9-mnc-v9','Metformin',20.00,22.00,100,NULL,'metformin_tablets_1778514780678.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(267,9,2,NULL,'Pediatric Multivitamin Syrup','pediatric-multivitamin-syrup-10-mnc-v9','Vitamin Complex',86.00,96.00,122,NULL,'multivitamin_syrup_1778514797763.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(268,9,1,NULL,'Diclofenac Pain Spray','diclofenac-pain-spray-11-mnc-v9','Diclofenac Sodium',19.00,21.00,68,NULL,'diclofenac_spray_1778514812612.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(269,9,1,NULL,'Calcium + D3 Tablets','calcium-d3-tablets-12-mnc-v9','Calcium Citrate',119.00,133.00,38,NULL,'calcium_tablets_1778514828387.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(270,9,1,NULL,'Telmisartan 40mg','telmisartan-40mg-13-mnc-v9','Telmisartan',37.00,41.00,135,NULL,'telmisartan_tablets_1778514847903.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(271,9,6,NULL,'Albuterol Nebulizer Sol','albuterol-nebulizer-sol-14-mnc-v9','Albuterol Sulfate',220.00,246.00,25,NULL,'albuterol_nebulizer_solution_1778514862544.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(272,9,2,NULL,'Antifungal Dusting Powder','antifungal-dusting-powder-15-mnc-v9','Clotrimazole',48.00,54.00,93,NULL,'antifungal_powder_1778514877151.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(273,9,1,NULL,'Digoxin 0.25mg','digoxin-0-25mg-16-mnc-v9','Digoxin',15.00,17.00,90,NULL,'digoxin_tablets_1778514921406.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(274,9,1,NULL,'Warfarin 5mg','warfarin-5mg-17-mnc-v9','Warfarin Sodium',20.00,22.00,116,NULL,'warfarin_tablets_1778514938540.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(275,9,4,NULL,'Antacid Gel','antacid-gel-18-mnc-v9','Aluminium/Magnesium',68.00,76.00,136,NULL,'antacid-gel.jpg',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(276,9,2,NULL,'Ashwagandha Extract','ashwagandha-extract-19-mnc-v9','Withania Somnifera',92.00,103.00,131,NULL,'ashwagandha-extract.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(277,9,1,NULL,'Aspirin 150mg','aspirin-150mg-20-mnc-v9','Acetylsalicylic Acid',40.00,45.00,78,NULL,'aspirin-150mg.jpg',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(278,9,6,NULL,'Asthma Relief Inhaler','asthma-relief-inhaler-21-mnc-v9','Salbutamol',205.00,230.00,27,NULL,'asthma-relief-inhaler.jpg',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(279,9,3,NULL,'Blood Glucose Monitoring Kit','blood-glucose-monitoring-kit-22-mnc-v9','Diagnostic Kit',57.00,64.00,125,NULL,'blood-glucose-kit.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(280,9,4,NULL,'Clotrimazole Cream','clotrimazole-cream-23-mnc-v9','Clotrimazole',47.00,53.00,127,NULL,'clotrimazole-cream.jpg',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(281,9,2,NULL,'Cough Syrup Expectorant','cough-syrup-expectorant-24-mnc-v9','Guaifenesin',61.00,68.00,67,NULL,'cough-syrup-ex.jpg',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(282,9,9,NULL,'Digital Thermometer','digital-thermometer-25-mnc-v9','Medical Device',180.00,202.00,94,NULL,'digital-thermometer.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(283,9,12,NULL,'Emergency First Aid Kit','emergency-first-aid-kit-26-mnc-v9','Multiple Components',252.00,282.00,74,NULL,'first-aid-kit.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(284,9,12,NULL,'Alcohol Hand Sanitizer','alcohol-hand-sanitizer-27-mnc-v9','70% Alcohol',54.00,60.00,131,NULL,'hand-sanitizer.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(285,9,15,NULL,'Homeopathic Pellets','homeopathic-pellets-28-mnc-v9','Herbal Formula',68.00,76.00,71,NULL,'homeopathic-pellets.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(286,9,5,NULL,'Insulin Glargine Pen','insulin-glargine-pen-29-mnc-v9','Insulin',612.00,685.00,145,NULL,'insulin-glargine.jpg',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(287,9,7,NULL,'Lubricating Eye Drops','lubricating-eye-drops-30-mnc-v9','Sodium Hyaluronate',90.00,101.00,114,NULL,'lubricating-eye-drops.jpg',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(288,9,4,NULL,'Diclofenac Pain Relief Gel','diclofenac-pain-relief-gel-31-mnc-v9','Diclofenac',68.00,76.00,56,NULL,'pain-relief-gel.jpg',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(289,9,2,NULL,'Pediatric Vitamin Drops','pediatric-vitamin-drops-32-mnc-v9','Essential Vitamins',48.00,54.00,148,NULL,'pediatric-vitamin-drops.jpg',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(290,9,8,NULL,'Saline Nasal Drops','saline-nasal-drops-33-mnc-v9','Normal Saline',40.00,45.00,26,NULL,'saline-nasal-drops.jpg',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(291,9,11,NULL,'Sterile Surgical Gloves','sterile-surgical-gloves-34-mnc-v9','Latex Free',86.00,96.00,117,NULL,'surgical-gloves.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(292,9,1,NULL,'Multivitamin Blister Pack','multivitamin-blister-pack-35-mnc-v9','Vitamins/Minerals',104.00,116.00,37,NULL,'tablet_blister.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(293,9,5,NULL,'Vitamin B12 Injection','vitamin-b12-injection-36-mnc-v9','Cyanocobalamin',25.00,28.00,101,NULL,'vitamin-b12-injection.jpg',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(294,9,1,NULL,'Amlodipine 5mg','amlodipine-5mg-37-mnc-v9','Amlodipine',23.00,26.00,107,NULL,'archetypes/generic.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(295,9,1,NULL,'Atorvastatin 10mg','atorvastatin-10mg-38-mnc-v9','Atorvastatin',40.00,45.00,107,NULL,'archetypes/generic.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(296,9,1,NULL,'Losartan 50mg','losartan-50mg-39-mnc-v9','Losartan',32.00,36.00,42,NULL,'archetypes/generic.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(297,9,1,NULL,'Hydrochlorothiazide 12.5mg','hydrochlorothiazide-12-5mg-40-mnc-v9','Hydrochlorothiazide',32.00,36.00,101,NULL,'archetypes/generic.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(298,9,1,NULL,'Gabapentin 300mg','gabapentin-300mg-41-mnc-v9','Gabapentin',61.00,68.00,64,NULL,'archetypes/generic.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(299,9,1,NULL,'Sertraline 50mg','sertraline-50mg-42-mnc-v9','Sertraline',56.00,63.00,114,NULL,'archetypes/generic.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(300,9,1,NULL,'Montelukast 10mg','montelukast-10mg-43-mnc-v9','Montelukast',90.00,101.00,23,NULL,'archetypes/generic.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(301,9,1,NULL,'Pantoprazole 40mg','pantoprazole-40mg-44-mnc-v9','Pantoprazole',42.00,47.00,22,NULL,'archetypes/generic.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(302,9,1,NULL,'Furosemide 20mg','furosemide-20mg-45-mnc-v9','Furosemide',9.00,10.00,146,NULL,'archetypes/generic.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(303,9,1,NULL,'Prednisone 5mg','prednisone-5mg-46-mnc-v9','Prednisone',16.00,18.00,100,NULL,'archetypes/generic.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(304,9,1,NULL,'Lisinopril 20mg','lisinopril-20mg-47-mnc-v9','Lisinopril',35.00,39.00,91,NULL,'archetypes/generic.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(305,9,1,NULL,'Metoprolol 25mg','metoprolol-25mg-48-mnc-v9','Metoprolol',27.00,30.00,83,NULL,'archetypes/generic.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(306,9,1,NULL,'Simvastatin 40mg','simvastatin-40mg-49-mnc-v9','Simvastatin',45.00,50.00,57,NULL,'archetypes/generic.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(307,9,1,NULL,'parace.','parace--79cb-v9',NULL,11.00,12.00,144,NULL,NULL,0,'','approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(308,9,1,NULL,'para 2','para-2-b2c9-v9',NULL,6.00,7.00,92,NULL,NULL,0,'Tablet','approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(309,9,1,NULL,'parace3','parace3-d086-v9',NULL,3.00,3.00,34,NULL,'parace3_1779384573.png',0,'','approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(310,11,1,NULL,'Paracetamol 500mg','paracetamol-500mg-0-mnc-v11','Paracetamol',10.00,11.00,44,NULL,'paracetamol-500mg.jpg',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(311,11,1,NULL,'Amoxicillin Capsules','amoxicillin-capsules-1-mnc-v11','Amoxicillin',24.00,27.00,147,NULL,'amoxicillin_capsules_1778507853087.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(312,11,1,NULL,'Cetirizine 10mg','cetirizine-10mg-2-mnc-v11','Cetirizine',6.00,7.00,114,NULL,'cetirizine_tablets_1778507868145.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(313,11,1,NULL,'Omeprazole 20mg','omeprazole-20mg-3-mnc-v11','Omeprazole',17.00,19.00,101,NULL,'omeprazole_capsules_1778507883875.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(314,11,1,NULL,'Azithromycin 500mg','azithromycin-500mg-4-mnc-v11','Azithromycin',38.00,43.00,108,NULL,'azithromycin_tablets_1778507899119.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(315,11,1,NULL,'Ibuprofen 400mg','ibuprofen-400mg-5-mnc-v11','Ibuprofen',14.00,16.00,33,NULL,'ibuprofen_tablets_1778507914522.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(316,11,2,NULL,'Vitamin C 500mg','vitamin-c-500mg-6-mnc-v11','Ascorbic Acid',36.00,40.00,149,NULL,'vitamin_c_tablets_1778514730165.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(317,11,4,NULL,'Antibiotic Eye Ointment','antibiotic-eye-ointment-7-mnc-v11','Ciprofloxacin',48.00,54.00,128,NULL,'eye_ointment_tube_1778514747227.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(318,11,3,NULL,'Orthopedic Knee Brace','orthopedic-knee-brace-8-mnc-v11','Support Device',18.00,20.00,102,NULL,'knee_brace_box_1778514764394.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(319,11,1,NULL,'Metformin 500mg','metformin-500mg-9-mnc-v11','Metformin',11.00,12.00,79,NULL,'metformin_tablets_1778514780678.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(320,11,2,NULL,'Pediatric Multivitamin Syrup','pediatric-multivitamin-syrup-10-mnc-v11','Vitamin Complex',48.00,54.00,93,NULL,'multivitamin_syrup_1778514797763.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(321,11,1,NULL,'Diclofenac Pain Spray','diclofenac-pain-spray-11-mnc-v11','Diclofenac Sodium',11.00,12.00,107,NULL,'diclofenac_spray_1778514812612.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(322,11,1,NULL,'Calcium + D3 Tablets','calcium-d3-tablets-12-mnc-v11','Calcium Citrate',66.00,74.00,25,NULL,'calcium_tablets_1778514828387.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(323,11,1,NULL,'Telmisartan 40mg','telmisartan-40mg-13-mnc-v11','Telmisartan',21.00,24.00,78,NULL,'telmisartan_tablets_1778514847903.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(324,11,6,NULL,'Albuterol Nebulizer Sol','albuterol-nebulizer-sol-14-mnc-v11','Albuterol Sulfate',122.00,137.00,67,NULL,'albuterol_nebulizer_solution_1778514862544.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(325,11,2,NULL,'Antifungal Dusting Powder','antifungal-dusting-powder-15-mnc-v11','Clotrimazole',27.00,30.00,86,NULL,'antifungal_powder_1778514877151.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(326,11,1,NULL,'Digoxin 0.25mg','digoxin-0-25mg-16-mnc-v11','Digoxin',8.00,9.00,85,NULL,'digoxin_tablets_1778514921406.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(327,11,1,NULL,'Warfarin 5mg','warfarin-5mg-17-mnc-v11','Warfarin Sodium',11.00,12.00,104,NULL,'warfarin_tablets_1778514938540.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(328,11,4,NULL,'Antacid Gel','antacid-gel-18-mnc-v11','Aluminium/Magnesium',38.00,43.00,26,NULL,'antacid-gel.jpg',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(329,11,2,NULL,'Ashwagandha Extract','ashwagandha-extract-19-mnc-v11','Withania Somnifera',51.00,57.00,146,NULL,'ashwagandha-extract.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(330,11,1,NULL,'Aspirin 150mg','aspirin-150mg-20-mnc-v11','Acetylsalicylic Acid',22.00,25.00,27,NULL,'aspirin-150mg.jpg',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(331,11,6,NULL,'Asthma Relief Inhaler','asthma-relief-inhaler-21-mnc-v11','Salbutamol',114.00,128.00,87,NULL,'asthma-relief-inhaler.jpg',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(332,11,3,NULL,'Blood Glucose Monitoring Kit','blood-glucose-monitoring-kit-22-mnc-v11','Diagnostic Kit',32.00,36.00,66,NULL,'blood-glucose-kit.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(333,11,4,NULL,'Clotrimazole Cream','clotrimazole-cream-23-mnc-v11','Clotrimazole',26.00,29.00,85,NULL,'clotrimazole-cream.jpg',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(334,11,2,NULL,'Cough Syrup Expectorant','cough-syrup-expectorant-24-mnc-v11','Guaifenesin',34.00,38.00,143,NULL,'cough-syrup-ex.jpg',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(335,11,9,NULL,'Digital Thermometer','digital-thermometer-25-mnc-v11','Medical Device',100.00,112.00,141,NULL,'digital-thermometer.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(336,11,12,NULL,'Emergency First Aid Kit','emergency-first-aid-kit-26-mnc-v11','Multiple Components',140.00,157.00,49,NULL,'first-aid-kit.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(337,11,12,NULL,'Alcohol Hand Sanitizer','alcohol-hand-sanitizer-27-mnc-v11','70% Alcohol',30.00,34.00,26,NULL,'hand-sanitizer.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(338,11,15,NULL,'Homeopathic Pellets','homeopathic-pellets-28-mnc-v11','Herbal Formula',38.00,43.00,31,NULL,'homeopathic-pellets.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(339,11,5,NULL,'Insulin Glargine Pen','insulin-glargine-pen-29-mnc-v11','Insulin',340.00,381.00,61,NULL,'insulin-glargine.jpg',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(340,11,7,NULL,'Lubricating Eye Drops','lubricating-eye-drops-30-mnc-v11','Sodium Hyaluronate',50.00,56.00,111,NULL,'lubricating-eye-drops.jpg',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(341,11,4,NULL,'Diclofenac Pain Relief Gel','diclofenac-pain-relief-gel-31-mnc-v11','Diclofenac',38.00,43.00,48,NULL,'pain-relief-gel.jpg',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(342,11,2,NULL,'Pediatric Vitamin Drops','pediatric-vitamin-drops-32-mnc-v11','Essential Vitamins',26.00,29.00,55,NULL,'pediatric-vitamin-drops.jpg',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(343,11,8,NULL,'Saline Nasal Drops','saline-nasal-drops-33-mnc-v11','Normal Saline',22.00,25.00,116,NULL,'saline-nasal-drops.jpg',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(344,11,11,NULL,'Sterile Surgical Gloves','sterile-surgical-gloves-34-mnc-v11','Latex Free',48.00,54.00,81,NULL,'surgical-gloves.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(345,11,1,NULL,'Multivitamin Blister Pack','multivitamin-blister-pack-35-mnc-v11','Vitamins/Minerals',58.00,65.00,80,NULL,'tablet_blister.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(346,11,5,NULL,'Vitamin B12 Injection','vitamin-b12-injection-36-mnc-v11','Cyanocobalamin',14.00,16.00,144,NULL,'vitamin-b12-injection.jpg',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(347,11,1,NULL,'Amlodipine 5mg','amlodipine-5mg-37-mnc-v11','Amlodipine',13.00,15.00,127,NULL,'archetypes/generic.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(348,11,1,NULL,'Atorvastatin 10mg','atorvastatin-10mg-38-mnc-v11','Atorvastatin',22.00,25.00,23,NULL,'archetypes/generic.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(349,11,1,NULL,'Losartan 50mg','losartan-50mg-39-mnc-v11','Losartan',18.00,20.00,34,NULL,'archetypes/generic.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(350,11,1,NULL,'Hydrochlorothiazide 12.5mg','hydrochlorothiazide-12-5mg-40-mnc-v11','Hydrochlorothiazide',18.00,20.00,43,NULL,'archetypes/generic.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(351,11,1,NULL,'Gabapentin 300mg','gabapentin-300mg-41-mnc-v11','Gabapentin',34.00,38.00,45,NULL,'archetypes/generic.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(352,11,1,NULL,'Sertraline 50mg','sertraline-50mg-42-mnc-v11','Sertraline',31.00,35.00,40,NULL,'archetypes/generic.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(353,11,1,NULL,'Montelukast 10mg','montelukast-10mg-43-mnc-v11','Montelukast',50.00,56.00,136,NULL,'archetypes/generic.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(354,11,1,NULL,'Pantoprazole 40mg','pantoprazole-40mg-44-mnc-v11','Pantoprazole',23.00,26.00,27,NULL,'archetypes/generic.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(355,11,1,NULL,'Furosemide 20mg','furosemide-20mg-45-mnc-v11','Furosemide',5.00,6.00,45,NULL,'archetypes/generic.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(356,11,1,NULL,'Prednisone 5mg','prednisone-5mg-46-mnc-v11','Prednisone',9.00,10.00,138,NULL,'archetypes/generic.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(357,11,1,NULL,'Lisinopril 20mg','lisinopril-20mg-47-mnc-v11','Lisinopril',19.00,21.00,139,NULL,'archetypes/generic.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(358,11,1,NULL,'Metoprolol 25mg','metoprolol-25mg-48-mnc-v11','Metoprolol',15.00,17.00,148,NULL,'archetypes/generic.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(359,11,1,NULL,'Simvastatin 40mg','simvastatin-40mg-49-mnc-v11','Simvastatin',25.00,28.00,128,NULL,'archetypes/generic.png',0,NULL,'approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(360,11,1,NULL,'parace.','parace--79cb-v11',NULL,6.00,7.00,120,NULL,NULL,0,'','approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(361,11,1,NULL,'para 2','para-2-b2c9-v11',NULL,4.00,4.00,75,NULL,NULL,0,'Tablet','approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL),(362,11,1,NULL,'parace3','parace3-d086-v11',NULL,2.00,2.00,142,NULL,'parace3_1779384573.png',0,'','approved',NULL,NULL,NULL,NULL,1,'2026-05-22 04:48:02','2026-05-22 04:48:02',NULL);
/*!40000 ALTER TABLE `medicines` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `newsletter_campaigns`
--

DROP TABLE IF EXISTS `newsletter_campaigns`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `newsletter_campaigns` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `subject` varchar(255) NOT NULL,
  `body` text NOT NULL,
  `sent_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `newsletter_campaigns`
--

LOCK TABLES `newsletter_campaigns` WRITE;
/*!40000 ALTER TABLE `newsletter_campaigns` DISABLE KEYS */;
INSERT INTO `newsletter_campaigns` VALUES (1,'hello','HiI',NULL,'2026-04-26 12:39:06');
/*!40000 ALTER TABLE `newsletter_campaigns` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `newsletter_segments`
--

DROP TABLE IF EXISTS `newsletter_segments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `newsletter_segments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `query_logic` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `newsletter_segments`
--

LOCK TABLES `newsletter_segments` WRITE;
/*!40000 ALTER TABLE `newsletter_segments` DISABLE KEYS */;
/*!40000 ALTER TABLE `newsletter_segments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `newsletter_subscribers`
--

DROP TABLE IF EXISTS `newsletter_subscribers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `newsletter_subscribers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(100) NOT NULL,
  `status` enum('active','unsubscribed') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `newsletter_subscribers`
--

LOCK TABLES `newsletter_subscribers` WRITE;
/*!40000 ALTER TABLE `newsletter_subscribers` DISABLE KEYS */;
/*!40000 ALTER TABLE `newsletter_subscribers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifications`
--

LOCK TABLES `notifications` WRITE;
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
/*!40000 ALTER TABLE `notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_items`
--

DROP TABLE IF EXISTS `order_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `order_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `medicine_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_items`
--

LOCK TABLES `order_items` WRITE;
/*!40000 ALTER TABLE `order_items` DISABLE KEYS */;
INSERT INTO `order_items` VALUES (1,1,1,1,450.00,450.00),(2,1,1,1,450.00,450.00),(3,5,191,1,62.00,62.00),(4,5,192,5,244.75,1223.75),(5,6,194,1,246.25,246.25),(6,6,193,1,210.50,210.50),(7,7,201,1,301.50,301.50),(8,8,196,1,195.75,195.75),(9,9,281,1,233.50,233.50),(10,10,205,1,399.50,399.50),(11,11,196,1,195.75,195.75),(12,12,209,1,183.50,183.50),(13,13,225,1,263.50,263.50),(14,14,192,1,244.75,244.75),(15,15,238,1,434.25,434.25),(16,15,191,2,62.00,124.00),(17,15,194,6,246.25,1477.50),(18,15,192,8,244.75,1958.00),(19,15,196,5,195.75,978.75),(20,15,268,1,67.75,67.75),(21,16,196,1,195.75,195.75),(22,17,191,1,30.00,30.00);
/*!40000 ALTER TABLE `order_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_status_logs`
--

DROP TABLE IF EXISTS `order_status_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `order_status_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `status` varchar(50) NOT NULL,
  `changed_by` int(11) DEFAULT NULL,
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  CONSTRAINT `order_status_logs_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_status_logs`
--

LOCK TABLES `order_status_logs` WRITE;
/*!40000 ALTER TABLE `order_status_logs` DISABLE KEYS */;
INSERT INTO `order_status_logs` VALUES (1,1,'placed',NULL,NULL,'2026-04-25 21:51:00'),(2,1,'confirmed',NULL,NULL,'2026-04-25 21:51:00'),(3,5,'pending',3,NULL,'2026-04-27 15:23:20'),(4,6,'pending',3,NULL,'2026-04-27 15:27:14'),(5,7,'pending',3,NULL,'2026-04-27 15:28:12'),(6,8,'pending',3,NULL,'2026-04-27 15:37:20'),(7,9,'pending',3,NULL,'2026-04-27 15:39:04'),(8,10,'pending',3,NULL,'2026-04-27 15:49:41'),(9,11,'pending',3,NULL,'2026-04-27 15:51:08'),(10,12,'pending',3,NULL,'2026-04-27 16:00:33'),(11,13,'pending',3,NULL,'2026-04-27 16:01:37'),(12,14,'pending',3,NULL,'2026-04-27 16:14:29'),(13,14,'confirmed',NULL,NULL,'2026-04-27 16:14:44'),(14,15,'pending',3,NULL,'2026-04-28 16:11:14'),(15,15,'confirmed',NULL,NULL,'2026-04-28 16:11:30'),(16,16,'pending',3,NULL,'2026-05-01 05:56:20'),(17,17,'pending',3,NULL,'2026-05-19 13:08:41'),(18,17,'confirmed',NULL,NULL,'2026-05-19 13:08:53'),(19,1,'undefined',4,NULL,'2026-05-20 13:46:22'),(20,1,'heading_to_pickup',4,NULL,'2026-05-20 14:53:21'),(21,6,'sla_breached',NULL,'Order exceeded expected delivery timeframe.','2026-05-23 05:10:11'),(22,7,'sla_breached',NULL,'Order exceeded expected delivery timeframe.','2026-05-23 05:10:11'),(23,10,'sla_breached',NULL,'Order exceeded expected delivery timeframe.','2026-05-23 05:10:11'),(24,11,'sla_breached',NULL,'Order exceeded expected delivery timeframe.','2026-05-23 05:10:11'),(25,12,'sla_breached',NULL,'Order exceeded expected delivery timeframe.','2026-05-23 05:10:11'),(26,15,'sla_breached',NULL,'Order exceeded expected delivery timeframe.','2026-05-23 05:10:11'),(27,16,'sla_breached',NULL,'Order exceeded expected delivery timeframe.','2026-05-23 05:10:11'),(28,17,'sla_breached',NULL,'Order exceeded expected delivery timeframe.','2026-05-23 05:10:11');
/*!40000 ALTER TABLE `order_status_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `vendor_id` int(11) NOT NULL,
  `delivery_partner_id` int(11) DEFAULT NULL,
  `delivery_method_id` int(11) DEFAULT NULL,
  `address_id` int(11) DEFAULT NULL,
  `prescription_id` int(11) DEFAULT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `delivery_fee` decimal(10,2) DEFAULT 0.00,
  `grand_total` decimal(10,2) NOT NULL,
  `is_emergency` tinyint(1) DEFAULT 0,
  `status` enum('pending','confirmed','processing','dispatched','delivered','cancelled','return_requested','returned') DEFAULT 'pending',
  `pickup_otp` varchar(6) DEFAULT NULL,
  `delivery_otp` varchar(6) DEFAULT NULL,
  `payment_status` enum('pending','paid','failed') DEFAULT 'pending',
  `payment_method` enum('card','upi','cod','wallet') DEFAULT 'card',
  `idempotency_key` varchar(255) DEFAULT NULL,
  `assigned_at` timestamp NULL DEFAULT NULL,
  `sla_breach` tinyint(1) DEFAULT 0,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  `delivered_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `vendor_id` (`vendor_id`),
  KEY `delivery_partner_id` (`delivery_partner_id`),
  KEY `delivery_method_id` (`delivery_method_id`),
  CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`vendor_id`) REFERENCES `users` (`id`),
  CONSTRAINT `orders_ibfk_3` FOREIGN KEY (`delivery_partner_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `orders_ibfk_4` FOREIGN KEY (`delivery_method_id`) REFERENCES `delivery_methods` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
INSERT INTO `orders` VALUES (1,3,2,4,1,NULL,NULL,450.00,0.00,500.00,0,'returned','209639','527352','paid','card',NULL,'2026-04-26 01:43:58',0,NULL,'2026-04-25 21:50:38','2026-05-21 04:20:12',NULL,NULL),(5,3,2,NULL,1,3,NULL,1285.75,25.00,1310.75,0,'delivered',NULL,'440554','paid','card','2425046c2e0955eddd9f5d10129c4384',NULL,0,NULL,'2026-04-27 15:23:19','2026-05-20 06:46:49',NULL,NULL),(6,3,2,NULL,1,3,NULL,456.75,25.00,481.75,0,'confirmed',NULL,'829417','paid','card','976fd03f146a0d17726bd48dc76b1f1e',NULL,1,NULL,'2026-04-27 15:27:14','2026-05-23 05:10:10',NULL,NULL),(7,3,2,NULL,3,3,NULL,301.50,70.00,371.50,0,'confirmed',NULL,'009420','paid','card','40a47d87d0c1439fb23c0d0089795812',NULL,1,NULL,'2026-04-27 15:28:12','2026-05-23 05:10:11',NULL,NULL),(8,3,2,4,3,3,NULL,195.75,70.00,265.75,1,'','162516','052181','paid','card','40d07bb148b5b662aec185f680572a7b','2026-05-20 13:29:04',0,NULL,'2026-04-27 15:37:20','2026-05-20 14:54:09',NULL,NULL),(9,3,2,4,3,3,NULL,233.50,25.00,258.50,1,'',NULL,'177658','paid','card','1840fd9bcd812c01aee67700bb42f5b1','2026-05-20 13:29:10',0,NULL,'2026-04-27 15:39:04','2026-05-20 13:29:10',NULL,NULL),(10,3,2,NULL,3,3,NULL,399.50,70.00,469.50,0,'dispatched',NULL,'258995','paid','card','218a5e0b379aa24456ae7ac41b80fb7e',NULL,1,NULL,'2026-04-27 15:49:41','2026-05-23 05:10:11',NULL,NULL),(11,3,2,NULL,1,3,NULL,195.75,25.00,220.75,0,'dispatched',NULL,'680214','paid','card','3b51b9600e27ddb328e0fe7281038371',NULL,1,NULL,'2026-04-27 15:51:08','2026-05-23 05:10:11',NULL,NULL),(12,3,2,NULL,1,3,NULL,183.50,25.00,208.50,0,'dispatched',NULL,'806477','paid','card','78600a82cc017083947994a08942c12b',NULL,1,NULL,'2026-04-27 16:00:33','2026-05-23 05:10:11',NULL,NULL),(13,3,2,NULL,1,4,NULL,263.50,25.00,288.50,0,'pending',NULL,'395462','pending','cod','b5bc0f1eed23b1427677891db9e41ebe',NULL,0,NULL,'2026-04-27 16:01:37','2026-04-29 14:30:46',NULL,NULL),(14,3,2,4,1,3,NULL,244.75,25.00,269.75,0,'','354109','038603','paid','card','476c7804edf5f95c052ac5e510578e54','2026-05-20 13:29:28',0,NULL,'2026-04-27 16:14:29','2026-05-20 14:55:52',NULL,NULL),(15,3,2,7,1,3,NULL,5040.25,25.00,5065.25,0,'dispatched',NULL,'731237','paid','card','9cd6c609a5f5a7d341679aac612dba2f','2026-05-20 11:34:26',1,NULL,'2026-04-28 16:11:14','2026-05-23 05:10:11',NULL,NULL),(16,3,2,4,1,3,NULL,195.75,25.00,220.75,0,'processing','436778','435837','pending','card','02941e6f838fa476cd7ff9cb38cee63c','2026-05-23 01:00:51',1,NULL,'2026-05-01 05:56:20','2026-05-23 05:10:11',NULL,NULL),(17,3,2,4,1,3,NULL,30.00,25.00,55.00,1,'dispatched','073790','205232','paid','card','5d8b518c486b2ff38d960494c5e4b96f','2026-05-20 11:34:17',1,NULL,'2026-05-19 13:08:41','2026-05-23 05:10:11',NULL,NULL);
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_resets`
--

DROP TABLE IF EXISTS `password_resets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `password_resets` (
  `email` varchar(100) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  KEY `email` (`email`),
  KEY `token` (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_resets`
--

LOCK TABLES `password_resets` WRITE;
/*!40000 ALTER TABLE `password_resets` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_resets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `method` enum('card','upi','cod','wallet') NOT NULL,
  `transaction_id` varchar(100) DEFAULT NULL,
  `status` enum('pending','success','failed') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  CONSTRAINT `payments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payments`
--

LOCK TABLES `payments` WRITE;
/*!40000 ALTER TABLE `payments` DISABLE KEYS */;
INSERT INTO `payments` VALUES (1,14,3,269.75,'card','MP_1777306484608','success','2026-04-27 16:14:44'),(2,15,3,5065.25,'card','MP_1777392690375','success','2026-04-28 16:11:30'),(3,17,3,55.00,'upi','MP_1779196133201','success','2026-05-19 13:08:53');
/*!40000 ALTER TABLE `payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `permissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `group` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permissions`
--

LOCK TABLES `permissions` WRITE;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
INSERT INTO `permissions` VALUES (25,'View Own Orders','view_own_orders','Orders','2026-05-23 09:08:44'),(26,'Place Order','place_order','Orders','2026-05-23 09:08:44'),(27,'Cancel Order','cancel_order','Orders','2026-05-23 09:08:44'),(28,'Manage All Orders','manage_all_orders','Orders','2026-05-23 09:08:44'),(29,'Assign Delivery','assign_delivery','Orders','2026-05-23 09:08:44'),(30,'Update Delivery Status','update_delivery_status','Orders','2026-05-23 09:08:44'),(31,'View Medicine Catalog','view_catalog','Inventory','2026-05-23 09:08:44'),(32,'Add/Edit Medicines','manage_medicines','Inventory','2026-05-23 09:08:44'),(33,'Approve Medicines','approve_medicines','Inventory','2026-05-23 09:08:44'),(34,'Manage Stock','manage_stock','Inventory','2026-05-23 09:08:44'),(35,'Upload Prescription','upload_prescription','Prescriptions','2026-05-23 09:08:44'),(36,'Review Prescriptions','review_prescriptions','Prescriptions','2026-05-23 09:08:44'),(37,'View User Directory','view_users','Users','2026-05-23 09:08:44'),(38,'Block/Unblock Users','block_users','Users','2026-05-23 09:08:44'),(39,'Approve Vendors','approve_vendors','Users','2026-05-23 09:08:44'),(40,'View Own Earnings','view_own_earnings','Finance','2026-05-23 09:08:44'),(41,'Request Payout','request_payout','Finance','2026-05-23 09:08:44'),(42,'View Platform Financials','view_financials','Finance','2026-05-23 09:08:44'),(43,'Process Payouts','process_payouts','Finance','2026-05-23 09:08:44'),(44,'Submit Support Tickets','submit_tickets','Support','2026-05-23 09:08:44'),(45,'Manage All Tickets','manage_tickets','Support','2026-05-23 09:08:44'),(46,'View System Settings','view_settings','Settings','2026-05-23 09:08:44'),(47,'Modify Platform Settings','modify_settings','Settings','2026-05-23 09:08:44'),(48,'Manage Roles','manage_roles','Settings','2026-05-23 09:08:44');
/*!40000 ALTER TABLE `permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pos_sale_items`
--

DROP TABLE IF EXISTS `pos_sale_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos_sale_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pos_sale_id` int(11) NOT NULL,
  `medicine_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `pos_sale_id` (`pos_sale_id`),
  KEY `medicine_id` (`medicine_id`),
  CONSTRAINT `pos_sale_items_ibfk_1` FOREIGN KEY (`pos_sale_id`) REFERENCES `pos_sales` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pos_sale_items`
--

LOCK TABLES `pos_sale_items` WRITE;
/*!40000 ALTER TABLE `pos_sale_items` DISABLE KEYS */;
INSERT INTO `pos_sale_items` VALUES (1,1,193,1,100.00,100.00),(2,1,9,1,100.00,100.00),(3,1,45,1,100.00,100.00),(4,2,9,3,100.00,300.00),(5,3,113,1,100.00,100.00),(6,4,113,1,100.00,100.00),(7,5,113,1,100.00,100.00),(8,6,113,1,100.00,100.00),(9,7,201,2,15.00,30.00),(10,7,121,1,125.00,125.00),(11,7,129,1,66.00,66.00);
/*!40000 ALTER TABLE `pos_sale_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pos_sales`
--

DROP TABLE IF EXISTS `pos_sales`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos_sales` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `vendor_id` int(11) NOT NULL,
  `customer_name` varchar(100) DEFAULT NULL,
  `customer_phone` varchar(20) DEFAULT NULL,
  `seller_name` varchar(100) DEFAULT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `payment_method` enum('cash','card','upi') DEFAULT 'cash',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `vendor_id` (`vendor_id`),
  CONSTRAINT `pos_sales_ibfk_1` FOREIGN KEY (`vendor_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pos_sales`
--

LOCK TABLES `pos_sales` WRITE;
/*!40000 ALTER TABLE `pos_sales` DISABLE KEYS */;
INSERT INTO `pos_sales` VALUES (1,2,NULL,NULL,NULL,315.00,'card','2026-05-21 04:38:52'),(2,2,NULL,NULL,NULL,315.00,'card','2026-05-21 04:39:03'),(3,2,NULL,NULL,NULL,105.00,'card','2026-05-21 04:49:03'),(4,2,NULL,NULL,NULL,105.00,'card','2026-05-21 04:49:03'),(5,2,NULL,NULL,NULL,105.00,'cash','2026-05-21 04:49:12'),(6,2,NULL,NULL,NULL,105.00,'cash','2026-05-21 04:49:12'),(7,2,NULL,NULL,NULL,232.05,'cash','2026-05-22 04:55:25');
/*!40000 ALTER TABLE `pos_sales` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `prescription_reviews`
--

DROP TABLE IF EXISTS `prescription_reviews`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `prescription_reviews` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `prescription_id` int(11) NOT NULL,
  `vendor_id` int(11) NOT NULL,
  `status` enum('approved','rejected') NOT NULL,
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `prescription_id` (`prescription_id`),
  KEY `vendor_id` (`vendor_id`),
  CONSTRAINT `prescription_reviews_ibfk_1` FOREIGN KEY (`prescription_id`) REFERENCES `prescriptions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `prescription_reviews_ibfk_2` FOREIGN KEY (`vendor_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `prescription_reviews`
--

LOCK TABLES `prescription_reviews` WRITE;
/*!40000 ALTER TABLE `prescription_reviews` DISABLE KEYS */;
/*!40000 ALTER TABLE `prescription_reviews` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `prescriptions`
--

DROP TABLE IF EXISTS `prescriptions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `prescriptions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `doctor_name` varchar(100) DEFAULT NULL,
  `image_path` varchar(255) NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `prescriptions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `prescriptions`
--

LOCK TABLES `prescriptions` WRITE;
/*!40000 ALTER TABLE `prescriptions` DISABLE KEYS */;
INSERT INTO `prescriptions` VALUES (5,5,NULL,'Clinical Practitioner','rx_5_1776963304_69ea4ee851ca4.pdf','approved',NULL,'2026-05-22 15:12:20',NULL),(6,5,NULL,'Clinical Practitioner','rx_5_1776963314_69ea4ef26044a.pdf','pending',NULL,'2026-05-22 15:12:20',NULL),(7,5,NULL,'Clinical Practitioner','rx_5_1776963374_69ea4f2ee8428.pdf','pending',NULL,'2026-05-22 15:12:20',NULL),(8,5,NULL,'Clinical Practitioner','rx_5_1776963862_69ea51165a436.pdf','approved',NULL,'2026-05-22 15:12:20',NULL),(9,5,NULL,'Clinical Practitioner','rx_5_1776964193_69ea5261a075e.pdf','approved',NULL,'2026-05-22 15:12:20',NULL),(10,5,NULL,'Clinical Practitioner','rx_5_1776964564_69ea53d44e998.pdf','pending',NULL,'2026-05-22 15:12:20',NULL);
/*!40000 ALTER TABLE `prescriptions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `refunds`
--

DROP TABLE IF EXISTS `refunds`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `refunds` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `payment_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `reason` text DEFAULT NULL,
  `status` enum('pending','processed','failed') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `payment_id` (`payment_id`),
  CONSTRAINT `refunds_ibfk_1` FOREIGN KEY (`payment_id`) REFERENCES `payments` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `refunds`
--

LOCK TABLES `refunds` WRITE;
/*!40000 ALTER TABLE `refunds` DISABLE KEYS */;
/*!40000 ALTER TABLE `refunds` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `return_requests`
--

DROP TABLE IF EXISTS `return_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `return_requests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `reason` text DEFAULT NULL,
  `status` enum('pending','approved','rejected','completed') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `order_id` (`order_id`),
  CONSTRAINT `return_requests_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `return_requests_ibfk_2` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `return_requests`
--

LOCK TABLES `return_requests` WRITE;
/*!40000 ALTER TABLE `return_requests` DISABLE KEYS */;
INSERT INTO `return_requests` VALUES (3,3,1,'Damaged seal','rejected','2026-05-19 16:28:57',NULL),(5,3,1,'Package Damaged','approved','2026-05-20 05:40:21',NULL);
/*!40000 ALTER TABLE `return_requests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reviews`
--

DROP TABLE IF EXISTS `reviews`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reviews` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `medicine_id` int(11) NOT NULL,
  `rating` tinyint(4) NOT NULL CHECK (`rating` between 1 and 5),
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `medicine_id` (`medicine_id`),
  CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`medicine_id`) REFERENCES `medicines` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reviews`
--

LOCK TABLES `reviews` WRITE;
/*!40000 ALTER TABLE `reviews` DISABLE KEYS */;
/*!40000 ALTER TABLE `reviews` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `revoked_tokens`
--

DROP TABLE IF EXISTS `revoked_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `revoked_tokens` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `token_hash` varchar(255) NOT NULL,
  `expires_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `token_hash` (`token_hash`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `revoked_tokens`
--

LOCK TABLES `revoked_tokens` WRITE;
/*!40000 ALTER TABLE `revoked_tokens` DISABLE KEYS */;
INSERT INTO `revoked_tokens` VALUES (1,'b9d8c0762c90b4da02bbe27a1a4cdb8c4b4e03fa1f8816ea60a1f5baf6a9f3f1','2026-04-27 08:15:16','2026-04-26 08:33:00'),(2,'3653c34815a398308fc1d537920e1ef3ca3f6e453b5e48f88aa7faf1d25e8e44','2026-04-27 13:20:43','2026-04-26 13:39:34'),(3,'8cf52bc96f1b5f83d06ed69213ba889fdda177c52ebda9d7cebb3e40e3263499','2026-04-27 14:03:08','2026-04-26 14:03:23'),(4,'efd7f053abeae3e35596eaff48821601b50e417fb5019643aa14c0e6f9bc0e0e','2026-04-27 14:04:14','2026-04-26 14:04:18'),(5,'c8c1173b445d7ad5fff30f2380d93cb86f2ad6ab8ac626f060a7bbe2b7cc4d07','2026-04-27 14:23:27','2026-04-26 15:03:13'),(6,'8fa7097ce61d46aef7bead4cb19c8856c1a1f5cf4c869c7789e0e55a2ebd106b','2026-04-27 15:54:43','2026-04-26 17:40:22'),(7,'69c404b26fa923b74c02939825eb9b629c48aae73e3fc5079fc8ca636bc60bd3','2026-04-28 03:11:06','2026-04-27 04:09:47'),(8,'1f22760bf1c403760c9f014d8af9efdb798695e19475748fbd940a78b0ec460a','2026-04-28 04:17:32','2026-04-27 04:23:12'),(9,'be776e216dd0dc8bbf7740b815be2dde28f19fd584e973ffd1ec5a318ff2606b','2026-04-28 13:45:52','2026-04-27 14:03:45'),(10,'242640d46c8a092dffbf54cf99f40902c1d2cadb761745103ab64193af1ba046','2026-04-28 14:03:58','2026-04-27 14:04:30'),(11,'e3215caea309d037e19fd9e9104928aaa7d7fe798ab95ddf9c1e210f501a5ff5','2026-04-29 13:56:51','2026-04-28 13:57:11'),(12,'697e2a73755d59b536b674fe249878f47aa9d30cf877090e5172a9f4868e5373','2026-04-29 13:58:11','2026-04-28 13:58:37'),(13,'01a13849e74081fb93f1629422493b23e6ba99e462ad41b970f5273f31e5b156','2026-05-20 13:00:51','2026-05-19 13:02:39'),(14,'8eec788abd5a8f5385da9f3ff184a3f447feea950c70df7075567307c6d2acba','2026-05-20 13:30:06','2026-05-19 13:47:22'),(15,'d6779c01d5f8b3897fdc5dc3d8e802027cfba95a42e50fa3f716dfd2dc8bcb28','2026-05-21 05:02:59','2026-05-20 08:55:39'),(17,'a2b298946ae13c5653609c455eaa93ed47b548f0c6a997857bfe55feac678586','2026-05-21 08:57:06','2026-05-20 09:01:24'),(18,'3e0dc1c581cb626bcfd81368bc31b934bc5e8722c7d50799fcb9279535c5d1e0','2026-05-21 09:01:50','2026-05-20 09:15:23'),(19,'7f548749d23c2648790119f49eb05a23e60dc30a024bd06900af41ef98bbca4a','2026-05-21 09:15:42','2026-05-20 14:56:29'),(20,'d39791654c89da71688a883edd0b5fe377ae4c4ac89dfeef46db17d6489b6e52','2026-05-21 14:56:49','2026-05-20 15:02:13'),(21,'aec2909d186c0d5782734e0e65f7cfdc438c2eba80d0363aa9485adb2f5eab96','2026-05-21 15:03:28','2026-05-20 15:04:40'),(22,'f4de717b49659ab7b7bb8b7cb4c6ccce53951358361ec183f1fa6e6d0b463475','2026-05-23 04:13:23','2026-05-22 04:54:31'),(23,'9701a2a133935de163486bf9b9a8d3beb0dc43edbf011d3f723587cbaeb254e9','2026-05-23 04:54:47','2026-05-22 08:40:47'),(24,'c776d316ce51a9c682dd96761f385f7964ba5bc263461c207732012c9d9ec2d1','2026-05-23 08:41:32','2026-05-22 08:45:42'),(25,'f29b71fa88a1149ab70a44b3ce54fe92700f6a8cf2755be7f440217e05f00cf7','2026-05-23 08:45:53','2026-05-23 06:56:36');
/*!40000 ALTER TABLE `revoked_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role_permissions`
--

DROP TABLE IF EXISTS `role_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `role_permissions` (
  `role_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL,
  PRIMARY KEY (`role_id`,`permission_id`),
  KEY `permission_id` (`permission_id`),
  CONSTRAINT `role_permissions_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_permissions_ibfk_2` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_permissions`
--

LOCK TABLES `role_permissions` WRITE;
/*!40000 ALTER TABLE `role_permissions` DISABLE KEYS */;
INSERT INTO `role_permissions` VALUES (1,25),(1,26),(1,27),(1,28),(1,29),(1,30),(1,31),(1,32),(1,33),(1,34),(1,35),(1,36),(1,37),(1,38),(1,39),(1,40),(1,41),(1,42),(1,43),(1,44),(1,45),(1,46),(1,47),(1,48),(2,28),(2,31),(2,32),(2,34),(2,36),(2,40),(2,41),(2,44),(3,30),(3,40),(3,41),(3,44),(4,25),(4,26),(4,27),(4,31),(4,35),(4,44);
/*!40000 ALTER TABLE `role_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `display_name` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'admin','System Administrator','Full access to clinical and personnel nodes.','2026-04-25 15:33:48'),(2,'vendor','Pharmacy Vendor','Access to inventory and order fulfillment.','2026-04-25 15:33:48'),(3,'delivery','Logistics Partner','Access to dispatch queue and route telemetry.','2026-04-25 15:33:48'),(4,'user','Patient / Customer','Access to storefront and personal health records.','2026-04-25 15:33:48');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `safety_alerts`
--

DROP TABLE IF EXISTS `safety_alerts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `safety_alerts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `alert_type` enum('interaction','dosage','allergy') NOT NULL,
  `severity` enum('low','medium','high','critical') NOT NULL,
  `message` text NOT NULL,
  `is_acknowledged` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `order_id` (`order_id`),
  CONSTRAINT `safety_alerts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `safety_alerts_ibfk_2` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `safety_alerts`
--

LOCK TABLES `safety_alerts` WRITE;
/*!40000 ALTER TABLE `safety_alerts` DISABLE KEYS */;
/*!40000 ALTER TABLE `safety_alerts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key_name` varchar(100) NOT NULL,
  `value` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `key_name` (`key_name`)
) ENGINE=InnoDB AUTO_INCREMENT=395 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settings`
--

LOCK TABLES `settings` WRITE;
/*!40000 ALTER TABLE `settings` DISABLE KEYS */;
INSERT INTO `settings` VALUES (1,'site_name','MediMitra',NULL,'2026-04-25 15:33:48'),(2,'emergency_contact','+91 90000 00000',NULL,'2026-04-25 15:33:48'),(3,'free_delivery_threshold','1000.00',NULL,'2026-04-25 15:33:48'),(4,'vendor_commission_percent','10.0',NULL,'2026-04-25 15:33:48'),(5,'two_fa','1',NULL,'2026-05-23 09:39:04'),(6,'force_https','1',NULL,'2026-04-26 07:51:39'),(7,'email_verification','1',NULL,'2026-04-26 07:51:39'),(8,'allow_registration','1',NULL,'2026-04-26 07:51:39'),(9,'maintenance_mode','0',NULL,'2026-05-23 09:38:56'),(10,'rate_limit_api','100',NULL,'2026-04-26 07:51:39'),(11,'rate_limit_login','5',NULL,'2026-04-26 07:51:39'),(12,'lockout_duration','30',NULL,'2026-04-26 07:51:39'),(13,'jwt_expiry','24',NULL,'2026-04-26 07:51:39'),(14,'cors_origins',', https://medimitra.in',NULL,'2026-04-26 07:51:39'),(15,'ip_whitelist','',NULL,'2026-04-26 07:51:39'),(16,'debug_mode','0',NULL,'2026-05-23 09:38:56'),(17,'audit_admin','1',NULL,'2026-04-26 07:51:39'),(18,'log_failed_logins','1',NULL,'2026-04-26 07:51:39'),(19,'log_retention','90',NULL,'2026-04-26 07:51:39');
/*!40000 ALTER TABLE `settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `support_ticket_messages`
--

DROP TABLE IF EXISTS `support_ticket_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `support_ticket_messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ticket_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `ticket_id` (`ticket_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `support_ticket_messages_ibfk_1` FOREIGN KEY (`ticket_id`) REFERENCES `support_tickets` (`id`) ON DELETE CASCADE,
  CONSTRAINT `support_ticket_messages_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `support_ticket_messages`
--

LOCK TABLES `support_ticket_messages` WRITE;
/*!40000 ALTER TABLE `support_ticket_messages` DISABLE KEYS */;
/*!40000 ALTER TABLE `support_ticket_messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `support_tickets`
--

DROP TABLE IF EXISTS `support_tickets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `support_tickets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `status` enum('open','in_progress','resolved','closed') DEFAULT 'open',
  `priority` enum('low','medium','high') DEFAULT 'medium',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `support_tickets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `support_tickets`
--

LOCK TABLES `support_tickets` WRITE;
/*!40000 ALTER TABLE `support_tickets` DISABLE KEYS */;
/*!40000 ALTER TABLE `support_tickets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_profiles`
--

DROP TABLE IF EXISTS `user_profiles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_profiles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `gender` enum('male','female','other') DEFAULT NULL,
  `blood_group` varchar(5) DEFAULT NULL,
  `emergency_contact` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `user_profiles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_profiles`
--

LOCK TABLES `user_profiles` WRITE;
/*!40000 ALTER TABLE `user_profiles` DISABLE KEYS */;
INSERT INTO `user_profiles` VALUES (1,5,NULL,'1990-01-01','male',NULL,NULL,'2026-04-26 13:36:47'),(2,8,NULL,'1990-01-01','male',NULL,NULL,'2026-04-26 13:38:53'),(3,13,NULL,'1990-01-01','',NULL,NULL,'2026-05-01 04:39:37'),(4,14,NULL,'1990-01-01','male',NULL,NULL,'2026-05-17 06:21:24');
/*!40000 ALTER TABLE `user_profiles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','vendor','user','delivery') DEFAULT 'user',
  `phone` varchar(20) DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `wallet_balance` decimal(10,2) DEFAULT 0.00,
  `status` enum('active','inactive','suspended') DEFAULT 'active',
  `is_verified` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Admin Super','admin@mediflow.com','$2y$10$y6XccsTsxLCgy3N2WNUgruU7OdMKjIlhaAPjYwr.yFNagRDLnvjRm','admin','9000000001',NULL,NULL,0.00,'active',0,'2026-04-25 15:33:48','2026-04-26 15:06:02',NULL),(2,'MedVault','vendor@mediflow.com','$2y$10$y6XccsTsxLCgy3N2WNUgruU7OdMKjIlhaAPjYwr.yFNagRDLnvjRm','vendor','9000000002',NULL,NULL,0.00,'active',0,'2026-04-25 15:33:48','2026-05-22 04:54:03',NULL),(3,'Patient John','user@mediflow.com','$2y$10$y6XccsTsxLCgy3N2WNUgruU7OdMKjIlhaAPjYwr.yFNagRDLnvjRm','user','9000000003',NULL,NULL,1500.00,'active',0,'2026-04-25 15:33:48','2026-04-29 16:34:26',NULL),(4,'Suresh Delivery','delivery@mediflow.com','$2y$10$y6XccsTsxLCgy3N2WNUgruU7OdMKjIlhaAPjYwr.yFNagRDLnvjRm','delivery','9000000004',NULL,NULL,0.00,'active',0,'2026-04-25 15:33:48','2026-04-26 15:06:02',NULL),(5,'Test user 160','test_user_7616@medimitra.com','$2y$10$VRjXi7gXUgcufLIFQI1vIur7IbvlA0wUUC659bfLir.VL7J25FWGy','user','9876543210',NULL,NULL,0.00,'active',0,'2026-04-26 13:36:47','2026-04-26 13:36:47',NULL),(6,'Ganesh Medical','vendor171@test.com','$2y$10$AeBWvy0MJ0JGKFS/MJ6H3ucIF2.T3KtIxnH6jGZlmXlP2Xs.h36gS','vendor',NULL,NULL,NULL,0.00,'active',0,'2026-04-26 13:37:40','2026-05-22 08:49:14',NULL),(7,'Delivery Test 71','delivery544@test.com','$2y$10$HfbF8FOZG35j/fggMz6SGuG5J8Jy644WnOqZb3UJORLXbJuA.ID9O','delivery',NULL,NULL,NULL,0.00,'inactive',0,'2026-04-26 13:37:52','2026-04-26 13:37:52',NULL),(8,'Test user 336','test_user_8612@medimitra.com','$2y$10$EQDG64tYsz7ZTKMki5Q4N.qdZxKu.OK0v37MHC9QPVElVk5cGPLBO','user','9876543210',NULL,NULL,0.00,'active',0,'2026-04-26 13:38:53','2026-04-26 13:38:53',NULL),(9,'Tulsi Herbal Store','test_vendor_6111@medimitra.com','$2y$10$hKPqO07FkjixMrXzOGVcF.aJs1XhkpxyWpgLE.2phnwdw3SyVgxfK','vendor','9876543210',NULL,NULL,0.00,'active',0,'2026-04-26 13:38:53','2026-05-22 08:49:17',NULL),(10,'Test delivery 507','test_delivery_5318@medimitra.com','$2y$10$OHgWoU.J2azxFvULqXRmWuDUjgjw2CoRsvbI2UQCFFCJZ5a.kn6kq','delivery','9876543210',NULL,NULL,0.00,'inactive',0,'2026-04-26 13:38:53','2026-04-26 13:38:53',NULL),(11,'Generic Plus','testvendor@example.com','$2y$10$vlP.duNfX6seC.9qp0dfjOK3qOxAb4/iFPyV0mRjpKggfkzpXLuW6','vendor','1234567890',NULL,NULL,0.00,'active',0,'2026-04-26 13:48:30','2026-05-22 08:49:21',NULL),(13,'Test Patient','patient@mediflow.com','$2y$10$i45cl9TBSCzFb73.zOwmE.AYhZeLO.ujhbqSEOXlNVpz58kefYE9i','user','1234567890',NULL,NULL,0.00,'active',0,'2026-05-01 04:39:37','2026-05-01 04:39:37',NULL),(14,'QA Tester','qa_tester_1778998872366@mediflow.com','$2y$10$AN3l8aoMhWMAPBI8weXjWO7P48P8LhE.GZCi4RTTT.5HfHFH4cGG6','user','1234567890',NULL,NULL,0.00,'active',0,'2026-05-17 06:21:24','2026-05-17 06:21:24',NULL);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vendor_commissions`
--

DROP TABLE IF EXISTS `vendor_commissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vendor_commissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `vendor_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `commission_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','settled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `vendor_id` (`vendor_id`),
  KEY `order_id` (`order_id`),
  CONSTRAINT `vendor_commissions_ibfk_1` FOREIGN KEY (`vendor_id`) REFERENCES `users` (`id`),
  CONSTRAINT `vendor_commissions_ibfk_2` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vendor_commissions`
--

LOCK TABLES `vendor_commissions` WRITE;
/*!40000 ALTER TABLE `vendor_commissions` DISABLE KEYS */;
INSERT INTO `vendor_commissions` VALUES (1,2,5,196.61,'pending','2026-05-22 07:51:48');
/*!40000 ALTER TABLE `vendor_commissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vendor_payouts`
--

DROP TABLE IF EXISTS `vendor_payouts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vendor_payouts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `vendor_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `status` enum('pending','processed','failed') DEFAULT 'pending',
  `reference_id` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `vendor_id` (`vendor_id`),
  CONSTRAINT `vendor_payouts_ibfk_1` FOREIGN KEY (`vendor_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vendor_payouts`
--

LOCK TABLES `vendor_payouts` WRITE;
/*!40000 ALTER TABLE `vendor_payouts` DISABLE KEYS */;
INSERT INTO `vendor_payouts` VALUES (1,2,1114.12,'pending',NULL,'2026-05-22 08:13:51'),(2,2,1114.12,'pending',NULL,'2026-05-22 08:13:54'),(3,2,1114.12,'pending',NULL,'2026-05-22 08:13:55'),(4,2,100.00,'pending',NULL,'2026-05-22 08:29:42'),(5,2,10.00,'pending',NULL,'2026-05-22 08:29:52');
/*!40000 ALTER TABLE `vendor_payouts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vendor_profiles`
--

DROP TABLE IF EXISTS `vendor_profiles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vendor_profiles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `dob` date DEFAULT NULL,
  `pharmacy_name` varchar(255) DEFAULT NULL,
  `owner_name` varchar(255) DEFAULT NULL,
  `license_number` varchar(100) DEFAULT NULL,
  `pharmacist_reg_number` varchar(100) DEFAULT NULL,
  `pan_card_number` varchar(20) DEFAULT NULL,
  `aadhaar_number` varchar(20) DEFAULT NULL,
  `gst_number` varchar(20) DEFAULT NULL,
  `store_image` varchar(255) DEFAULT NULL,
  `opening_time` time DEFAULT NULL,
  `closing_time` time DEFAULT NULL,
  `address` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `bank_name` varchar(100) DEFAULT NULL,
  `account_number` varchar(50) DEFAULT NULL,
  `ifsc_code` varchar(20) DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `vendor_profiles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vendor_profiles`
--

LOCK TABLES `vendor_profiles` WRITE;
/*!40000 ALTER TABLE `vendor_profiles` DISABLE KEYS */;
INSERT INTO `vendor_profiles` VALUES (1,6,NULL,'Ganesh Medical',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Station Area, Phaltan, Maharashtra','2026-04-26 13:37:40','AXIS','998877','AXIS001',NULL,NULL),(2,9,NULL,'Tulsi Herbal Store',NULL,'DL-123456',NULL,NULL,NULL,'27AAAAA0000A1Z5',NULL,NULL,NULL,'Baramati Road, Phaltan, Maharashtra','2026-04-26 13:38:53','HDFC Bank','1234567890','HDFC0001234',NULL,NULL),(3,11,NULL,'Generic Plus',NULL,'DL-TEST-123',NULL,NULL,NULL,'',NULL,NULL,NULL,'Lonand Road, Phaltan, Maharashtra','2026-04-26 13:48:30','Test Bank','9876543210','TEST0001234',NULL,NULL),(5,2,NULL,'MedVault',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Phaltan City, Phaltan, Maharashtra','2026-05-20 19:04:39',NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `vendor_profiles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wallet_transactions`
--

DROP TABLE IF EXISTS `wallet_transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wallet_transactions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `type` enum('credit','debit') NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `wallet_transactions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wallet_transactions`
--

LOCK TABLES `wallet_transactions` WRITE;
/*!40000 ALTER TABLE `wallet_transactions` DISABLE KEYS */;
INSERT INTO `wallet_transactions` VALUES (1,3,1000.00,'credit','Wallet top-up via clinical gateway','2026-04-29 16:23:07'),(2,3,500.00,'credit','Wallet top-up via clinical gateway','2026-04-29 16:34:26');
/*!40000 ALTER TABLE `wallet_transactions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wishlists`
--

DROP TABLE IF EXISTS `wishlists`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wishlists` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `medicine_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`,`medicine_id`),
  KEY `medicine_id` (`medicine_id`),
  CONSTRAINT `wishlists_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `wishlists_ibfk_2` FOREIGN KEY (`medicine_id`) REFERENCES `medicines` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wishlists`
--

LOCK TABLES `wishlists` WRITE;
/*!40000 ALTER TABLE `wishlists` DISABLE KEYS */;
INSERT INTO `wishlists` VALUES (6,3,202,'2026-04-28 16:06:09'),(8,3,205,'2026-05-11 07:38:27');
/*!40000 ALTER TABLE `wishlists` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `worker_jobs`
--

DROP TABLE IF EXISTS `worker_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `worker_jobs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `queue` varchar(50) DEFAULT 'default',
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`payload`)),
  `attempts` tinyint(4) DEFAULT 0,
  `reserved_at` timestamp NULL DEFAULT NULL,
  `available_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `worker_jobs`
--

LOCK TABLES `worker_jobs` WRITE;
/*!40000 ALTER TABLE `worker_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `worker_jobs` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-05-23 15:10:11
