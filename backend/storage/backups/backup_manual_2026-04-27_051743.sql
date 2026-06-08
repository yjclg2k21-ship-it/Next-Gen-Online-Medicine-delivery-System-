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
  `is_default` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `addresses_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `addresses`
--

LOCK TABLES `addresses` WRITE;
/*!40000 ALTER TABLE `addresses` DISABLE KEYS */;
/*!40000 ALTER TABLE `addresses` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=69 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `audit_logs`
--

LOCK TABLES `audit_logs` WRITE;
/*!40000 ALTER TABLE `audit_logs` DISABLE KEYS */;
INSERT INTO `audit_logs` VALUES (1,1,'GLOBAL_PULSE_TRIGGERED','System',0,'{\"sla_breaches\":0,\"inventory_reconciliation\":0,\"timestamp\":\"2026-04-26 06:55:39\"}',NULL,'2026-04-26 04:55:39'),(2,1,'ORDER_ASSIGNED','Order',1,'{\"partner_id\":4}',NULL,'2026-04-26 05:13:59'),(3,1,'PERMISSIONS_UPDATED','Role',16,'\"Access Control Matrix redefined\"',NULL,'2026-04-26 07:50:03'),(4,1,'PERMISSIONS_UPDATED','Role',16,'\"Access Control Matrix redefined\"',NULL,'2026-04-26 07:50:08'),(5,1,'PERMISSIONS_UPDATED','Role',16,'\"Access Control Matrix redefined\"',NULL,'2026-04-26 07:50:08'),(6,1,'PERMISSIONS_UPDATED','Role',16,'\"Access Control Matrix redefined\"',NULL,'2026-04-26 07:50:11'),(7,1,'PERMISSIONS_UPDATED','Role',16,'\"Access Control Matrix redefined\"',NULL,'2026-04-26 07:50:12'),(8,1,'PERMISSIONS_UPDATED','Role',16,'\"Access Control Matrix redefined\"',NULL,'2026-04-26 07:50:12'),(9,1,'PERMISSIONS_UPDATED','Role',16,'\"Access Control Matrix redefined\"',NULL,'2026-04-26 07:50:15'),(10,1,'PERMISSIONS_UPDATED','Role',16,'\"Access Control Matrix redefined\"',NULL,'2026-04-26 07:50:19'),(11,1,'PERMISSIONS_UPDATED','Role',16,'\"Access Control Matrix redefined\"',NULL,'2026-04-26 07:50:19'),(12,1,'PERMISSIONS_UPDATED','Role',16,'\"Access Control Matrix redefined\"',NULL,'2026-04-26 07:51:57'),(13,1,'PERMISSIONS_UPDATED','Role',16,'\"Access Control Matrix redefined\"',NULL,'2026-04-26 07:51:57'),(14,1,'PERMISSIONS_UPDATED','Role',16,'\"Access Control Matrix redefined\"',NULL,'2026-04-26 07:51:58'),(15,1,'PERMISSIONS_UPDATED','Role',16,'\"Access Control Matrix redefined\"',NULL,'2026-04-26 07:51:58'),(16,1,'PERMISSIONS_UPDATED','Role',16,'\"Access Control Matrix redefined\"',NULL,'2026-04-26 07:51:59'),(17,1,'PERMISSIONS_UPDATED','Role',16,'\"Access Control Matrix redefined\"',NULL,'2026-04-26 07:51:59'),(18,1,'PERMISSIONS_UPDATED','Role',16,'\"Access Control Matrix redefined\"',NULL,'2026-04-26 07:51:59'),(19,1,'PERMISSIONS_UPDATED','Role',16,'\"Access Control Matrix redefined\"',NULL,'2026-04-26 07:52:01'),(20,1,'PERMISSIONS_UPDATED','Role',16,'\"Access Control Matrix redefined\"',NULL,'2026-04-26 07:52:01'),(21,1,'PERMISSIONS_UPDATED','Role',16,'\"Access Control Matrix redefined\"',NULL,'2026-04-26 07:52:02'),(22,1,'PERMISSIONS_UPDATED','Role',16,'\"Access Control Matrix redefined\"',NULL,'2026-04-26 07:52:02'),(23,1,'PERMISSIONS_UPDATED','Role',16,'\"Access Control Matrix redefined\"',NULL,'2026-04-26 07:52:02'),(24,1,'PERMISSIONS_UPDATED','Role',16,'\"Access Control Matrix redefined\"',NULL,'2026-04-26 07:52:03'),(25,1,'PERMISSIONS_UPDATED','Role',16,'\"Access Control Matrix redefined\"',NULL,'2026-04-26 07:52:03'),(26,1,'PERMISSIONS_UPDATED','Role',16,'\"Access Control Matrix redefined\"',NULL,'2026-04-26 07:52:04'),(27,1,'PERMISSIONS_UPDATED','Role',16,'\"Access Control Matrix redefined\"',NULL,'2026-04-26 07:52:04'),(28,1,'PERMISSIONS_UPDATED','Role',16,'\"Access Control Matrix redefined\"',NULL,'2026-04-26 07:52:04'),(29,1,'PERMISSIONS_UPDATED','Role',16,'\"Access Control Matrix redefined\"',NULL,'2026-04-26 07:52:04'),(30,1,'PERMISSIONS_UPDATED','Role',16,'\"Access Control Matrix redefined\"',NULL,'2026-04-26 07:52:05'),(31,1,'PERMISSIONS_UPDATED','Role',16,'\"Access Control Matrix redefined\"',NULL,'2026-04-26 07:52:05'),(32,1,'PERMISSIONS_UPDATED','Role',16,'\"Access Control Matrix redefined\"',NULL,'2026-04-26 07:52:05'),(33,1,'PERMISSIONS_UPDATED','Role',16,'\"Access Control Matrix redefined\"',NULL,'2026-04-26 07:52:05'),(34,1,'PERMISSIONS_UPDATED','Role',16,'\"Access Control Matrix redefined\"',NULL,'2026-04-26 07:52:06'),(35,1,'PERMISSIONS_UPDATED','Role',16,'\"Access Control Matrix redefined\"',NULL,'2026-04-26 07:52:06'),(36,1,'PERMISSIONS_UPDATED','Role',16,'\"Access Control Matrix redefined\"',NULL,'2026-04-26 07:52:06'),(37,1,'PERMISSIONS_UPDATED','Role',16,'\"Access Control Matrix redefined\"',NULL,'2026-04-26 07:52:07'),(38,1,'PERMISSIONS_UPDATED','Role',16,'\"Access Control Matrix redefined\"',NULL,'2026-04-26 07:52:07'),(39,1,'PERMISSIONS_UPDATED','Role',16,'\"Access Control Matrix redefined\"',NULL,'2026-04-26 07:52:08'),(40,1,'PERMISSIONS_UPDATED','Role',16,'\"Access Control Matrix redefined\"',NULL,'2026-04-26 07:52:09'),(41,1,'PERMISSIONS_UPDATED','Role',16,'\"Access Control Matrix redefined\"',NULL,'2026-04-26 07:52:10'),(42,1,'PERMISSIONS_UPDATED','Role',16,'\"Access Control Matrix redefined\"',NULL,'2026-04-26 07:52:11'),(43,1,'PERMISSIONS_UPDATED','Role',16,'\"Access Control Matrix redefined\"',NULL,'2026-04-26 07:52:11'),(44,1,'PERMISSIONS_UPDATED','Role',16,'\"Access Control Matrix redefined\"',NULL,'2026-04-26 07:52:12'),(45,1,'PERMISSIONS_UPDATED','Role',16,'\"Access Control Matrix redefined\"',NULL,'2026-04-26 07:52:12'),(46,1,'PERMISSIONS_UPDATED','Role',16,'\"Access Control Matrix redefined\"',NULL,'2026-04-26 07:52:13'),(47,1,'PERMISSIONS_UPDATED','Role',16,'\"Access Control Matrix redefined\"',NULL,'2026-04-26 07:52:13'),(48,1,'PERMISSIONS_UPDATED','Role',16,'\"Access Control Matrix redefined\"',NULL,'2026-04-26 07:52:13'),(49,1,'PERMISSIONS_UPDATED','Role',16,'\"Access Control Matrix redefined\"',NULL,'2026-04-26 07:52:14'),(50,1,'PERMISSIONS_UPDATED','Role',16,'\"Access Control Matrix redefined\"',NULL,'2026-04-26 07:52:14'),(51,1,'PERMISSIONS_UPDATED','Role',16,'\"Access Control Matrix redefined\"',NULL,'2026-04-26 07:52:14'),(52,1,'PERMISSIONS_UPDATED','Role',16,'\"Access Control Matrix redefined\"',NULL,'2026-04-26 07:52:14'),(53,1,'PERMISSIONS_UPDATED','Role',16,'\"Access Control Matrix redefined\"',NULL,'2026-04-26 07:52:14'),(54,1,'PERMISSIONS_UPDATED','Role',16,'\"Access Control Matrix redefined\"',NULL,'2026-04-26 07:52:15'),(55,1,'PERMISSIONS_UPDATED','Role',16,'\"Access Control Matrix redefined\"',NULL,'2026-04-26 07:52:15'),(56,1,'PERMISSIONS_UPDATED','Role',16,'\"Access Control Matrix redefined\"',NULL,'2026-04-26 07:52:15'),(57,1,'PERMISSIONS_UPDATED','Role',16,'\"Access Control Matrix redefined\"',NULL,'2026-04-26 08:01:42'),(58,1,'PERMISSIONS_UPDATED','Role',16,'\"Access Control Matrix redefined\"',NULL,'2026-04-26 08:01:43'),(59,1,'PERMISSIONS_UPDATED','Role',16,'\"Access Control Matrix redefined\"',NULL,'2026-04-26 08:01:43'),(60,1,'PERMISSIONS_UPDATED','Role',16,'\"Access Control Matrix redefined\"',NULL,'2026-04-26 08:01:51'),(61,1,'PERMISSIONS_UPDATED','Role',16,'\"Access Control Matrix redefined\"',NULL,'2026-04-26 08:01:52'),(62,1,'PERMISSIONS_UPDATED','Role',16,'\"Access Control Matrix redefined\"',NULL,'2026-04-26 08:01:53'),(63,1,'PERMISSIONS_UPDATED','Role',16,'\"Access Control Matrix redefined\"',NULL,'2026-04-26 08:01:53'),(64,1,'PERMISSIONS_UPDATED','Role',16,'\"Access Control Matrix redefined\"',NULL,'2026-04-26 08:01:53'),(65,1,'PERMISSIONS_UPDATED','Role',16,'\"Access Control Matrix redefined\"',NULL,'2026-04-26 08:01:54'),(66,1,'PERMISSIONS_UPDATED','Role',16,'\"Access Control Matrix redefined\"',NULL,'2026-04-26 08:01:55'),(67,1,'GLOBAL_PULSE_TRIGGERED','System',0,'{\"sla_breaches\":0,\"inventory_reconciliation\":0,\"timestamp\":\"2026-04-26 10:45:00\"}',NULL,'2026-04-26 08:45:00'),(68,1,'KYC_STATUS_UPDATED','KYC',1,'{\"status\":\"verified\"}',NULL,'2026-04-26 14:29:25');
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
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `brands`
--

LOCK TABLES `brands` WRITE;
/*!40000 ALTER TABLE `brands` DISABLE KEYS */;
INSERT INTO `brands` VALUES (1,'Cipla','cipla',1,'2026-04-25 15:33:48','Phaltan, India','MNC'),(2,'Sun Pharma','sun-pharma',1,'2026-04-25 15:33:48','Phaltan, India','MNC'),(3,'Abbott','abbott',1,'2026-04-25 15:33:48','Chicago, USA','MNC'),(4,'Mankind','mankind',1,'2026-04-25 15:33:48','New Delhi, India','Domestic'),(5,'Pfizer','pfizer',1,'2026-04-26 04:37:34','New York, USA','MNC'),(6,'Dr. Reddys','dr.-reddys',1,'2026-04-26 04:37:34','Hyderabad, India','MNC'),(7,'Glenmark','glenmark',1,'2026-04-26 04:37:34','Phaltan, India','Generic'),(8,'Himalaya','himalaya',1,'2026-04-26 04:37:34','Bengaluru, India','Ayurvedic'),(9,'Zydus Cadila','zydus-cadila',1,'2026-04-26 04:37:34','Ahmedabad, India','MNC'),(10,'Patanjali','patanjali',1,'2026-04-26 05:49:07','Bjp-Office','Ayurvedic');
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cart_items`
--

LOCK TABLES `cart_items` WRITE;
/*!40000 ALTER TABLE `cart_items` DISABLE KEYS */;
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
INSERT INTO `delivery_methods` VALUES (1,'standard','Standard Delivery',30.00,'2-3 Days',1,72),(2,'express','Express Delivery',80.00,'Same Day',2,24),(3,'emergency','Emergency Delivery',150.00,'2 Hours',3,2);
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
  `vehicle_type` enum('cycle','bike','scooter','car') DEFAULT NULL,
  `vehicle_number` varchar(50) DEFAULT NULL,
  `license_number` varchar(50) DEFAULT NULL,
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
INSERT INTO `delivery_profiles` VALUES (1,7,'bike','MH-12-TEST',NULL,'active','2026-04-26 13:37:52','ICICI','112233','ICIC001',NULL,NULL),(2,10,'bike','MH-12-AB-1234','DL-99999','active','2026-04-26 13:38:53','SBI','0099887766','SBIN0001234','Kothrud, Pune','Mumbai, Maharashtra'),(3,4,'','MH-01-ED-1234','L-MUM-98765','','2026-04-26 15:22:42',NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `delivery_profiles` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `kyc_documents`
--

LOCK TABLES `kyc_documents` WRITE;
/*!40000 ALTER TABLE `kyc_documents` DISABLE KEYS */;
INSERT INTO `kyc_documents` VALUES (1,12,'pharmacy_license','','storage/uploads/kyc/1777212176_ChatGPT Image Apr 26, 2026, 10_58_53 AM.png','verified','Documents verified by Admin.','2026-04-26 14:29:25','2026-04-26 14:02:56');
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `medicine_substitutes`
--

LOCK TABLES `medicine_substitutes` WRITE;
/*!40000 ALTER TABLE `medicine_substitutes` DISABLE KEYS */;
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
  `image` varchar(255) DEFAULT NULL,
  `requires_prescription` tinyint(1) DEFAULT 0,
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
) ENGINE=InnoDB AUTO_INCREMENT=587 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `medicines`
--

LOCK TABLES `medicines` WRITE;
/*!40000 ALTER TABLE `medicines` DISABLE KEYS */;
INSERT INTO `medicines` VALUES (191,6,1,4,'Paracetamol 500mg','paracetamol-500mg-6-180','Paracetamol',62.00,71.30,899,'assets/images/medicines/paracetamol-500mg.jpg',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:17:10',NULL),(192,9,1,8,'Paracetamol 500mg','paracetamol-500mg-9-551','Paracetamol',244.75,281.46,104,'assets/images/medicines/paracetamol-500mg.jpg',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:17:10',NULL),(193,11,1,3,'Paracetamol 500mg','paracetamol-500mg-11-330','Paracetamol',210.50,242.08,928,'assets/images/medicines/paracetamol-500mg.jpg',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:17:10',NULL),(194,12,1,8,'Paracetamol 500mg','paracetamol-500mg-12-873','Paracetamol',246.25,283.19,331,'assets/images/medicines/paracetamol-500mg.jpg',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:17:10',NULL),(195,6,1,7,'Amoxicillin 500mg','amoxicillin-500mg-6-118','Amoxicillin',485.00,557.75,279,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(196,9,1,6,'Amoxicillin 500mg','amoxicillin-500mg-9-122','Amoxicillin',195.75,225.11,852,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(197,11,1,2,'Amoxicillin 500mg','amoxicillin-500mg-11-382','Amoxicillin',504.50,580.18,571,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(198,12,1,1,'Amoxicillin 500mg','amoxicillin-500mg-12-705','Amoxicillin',524.25,602.89,372,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(199,6,1,8,'Ibuprofen 400mg','ibuprofen-400mg-6-858','Ibuprofen',295.00,339.25,653,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(200,9,1,3,'Ibuprofen 400mg','ibuprofen-400mg-9-853','Ibuprofen',163.75,188.31,612,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(201,11,1,5,'Ibuprofen 400mg','ibuprofen-400mg-11-616','Ibuprofen',301.50,346.73,481,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(202,12,1,2,'Ibuprofen 400mg','ibuprofen-400mg-12-743','Ibuprofen',110.25,126.79,205,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(203,6,2,7,'Cetirizine 10mg','cetirizine-10mg-6-902','Cetirizine',419.00,481.85,946,'assets/images/syrup.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:17:10',NULL),(204,9,2,5,'Cetirizine 10mg','cetirizine-10mg-9-921','Cetirizine',460.75,529.86,916,'assets/images/syrup.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:17:10',NULL),(205,11,2,8,'Cetirizine 10mg','cetirizine-10mg-11-794','Cetirizine',399.50,459.43,269,'assets/images/syrup.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:17:10',NULL),(206,12,2,5,'Cetirizine 10mg','cetirizine-10mg-12-214','Cetirizine',184.25,211.89,460,'assets/images/syrup.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:17:10',NULL),(207,6,3,7,'Metformin 500mg','metformin-500mg-6-618','Metformin',433.00,497.95,160,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(208,9,3,2,'Metformin 500mg','metformin-500mg-9-616','Metformin',199.75,229.71,785,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(209,11,3,10,'Metformin 500mg','metformin-500mg-11-390','Metformin',183.50,211.03,739,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(210,12,3,6,'Metformin 500mg','metformin-500mg-12-311','Metformin',227.25,261.34,940,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(211,6,4,2,'Amlodipine 5mg','amlodipine-5mg-6-871','Amlodipine',289.00,332.35,467,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(212,9,4,8,'Amlodipine 5mg','amlodipine-5mg-9-762','Amlodipine',306.75,352.76,752,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(213,11,4,5,'Amlodipine 5mg','amlodipine-5mg-11-399','Amlodipine',192.50,221.38,403,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(214,12,4,1,'Amlodipine 5mg','amlodipine-5mg-12-190','Amlodipine',138.25,158.99,542,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(215,6,4,1,'Atorvastatin 10mg','atorvastatin-10mg-6-867','Atorvastatin',472.00,542.80,392,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(216,9,4,5,'Atorvastatin 10mg','atorvastatin-10mg-9-984','Atorvastatin',388.75,447.06,28,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(217,11,4,1,'Atorvastatin 10mg','atorvastatin-10mg-11-814','Atorvastatin',521.50,599.73,262,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(218,12,4,2,'Atorvastatin 10mg','atorvastatin-10mg-12-196','Atorvastatin',93.25,107.24,511,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(219,6,5,9,'Omeprazole 20mg','omeprazole-20mg-6-897','Omeprazole',437.00,502.55,236,'assets/images/syrup.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:17:10',NULL),(220,9,5,9,'Omeprazole 20mg','omeprazole-20mg-9-281','Omeprazole',72.75,83.66,861,'assets/images/syrup.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:17:10',NULL),(221,11,5,5,'Omeprazole 20mg','omeprazole-20mg-11-747','Omeprazole',508.50,584.78,873,'assets/images/syrup.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:17:10',NULL),(222,12,5,10,'Omeprazole 20mg','omeprazole-20mg-12-564','Omeprazole',435.25,500.54,806,'assets/images/syrup.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:17:10',NULL),(223,6,1,1,'Azithromycin 500mg','azithromycin-500mg-6-477','Azithromycin',376.00,432.40,92,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(224,9,1,4,'Azithromycin 500mg','azithromycin-500mg-9-329','Azithromycin',87.75,100.91,889,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(225,11,1,7,'Azithromycin 500mg','azithromycin-500mg-11-770','Azithromycin',263.50,303.03,112,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(226,12,1,4,'Azithromycin 500mg','azithromycin-500mg-12-832','Azithromycin',214.25,246.39,66,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(227,6,5,9,'Pantoprazole 40mg','pantoprazole-40mg-6-938','Pantoprazole',156.00,179.40,747,'assets/images/syrup.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:17:10',NULL),(228,9,5,10,'Pantoprazole 40mg','pantoprazole-40mg-9-384','Pantoprazole',60.75,69.86,617,'assets/images/syrup.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:17:10',NULL),(229,11,5,2,'Pantoprazole 40mg','pantoprazole-40mg-11-264','Pantoprazole',231.50,266.23,841,'assets/images/syrup.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:17:10',NULL),(230,12,5,5,'Pantoprazole 40mg','pantoprazole-40mg-12-730','Pantoprazole',469.25,539.64,929,'assets/images/syrup.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:17:10',NULL),(231,6,4,3,'Losartan 50mg','losartan-50mg-6-777','Losartan',198.00,227.70,988,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(232,9,4,6,'Losartan 50mg','losartan-50mg-9-639','Losartan',381.75,439.01,356,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(233,11,4,6,'Losartan 50mg','losartan-50mg-11-791','Losartan',182.50,209.88,148,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(234,12,4,6,'Losartan 50mg','losartan-50mg-12-191','Losartan',303.25,348.74,264,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(235,6,2,5,'Montelukast 10mg','montelukast-10mg-6-953','Montelukast',216.00,248.40,698,'assets/images/syrup.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:17:10',NULL),(236,9,2,2,'Montelukast 10mg','montelukast-10mg-9-774','Montelukast',283.75,326.31,934,'assets/images/syrup.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:17:10',NULL),(237,11,2,10,'Montelukast 10mg','montelukast-10mg-11-757','Montelukast',281.50,323.73,505,'assets/images/syrup.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:17:10',NULL),(238,12,2,2,'Montelukast 10mg','montelukast-10mg-12-627','Montelukast',434.25,499.39,973,'assets/images/syrup.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:17:10',NULL),(239,6,6,10,'Gabapentin 300mg','gabapentin-300mg-6-134','Gabapentin',235.00,270.25,784,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(240,9,6,9,'Gabapentin 300mg','gabapentin-300mg-9-881','Gabapentin',120.75,138.86,776,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(241,11,6,8,'Gabapentin 300mg','gabapentin-300mg-11-691','Gabapentin',380.50,437.58,765,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(242,12,6,4,'Gabapentin 300mg','gabapentin-300mg-12-619','Gabapentin',363.25,417.74,645,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(243,6,3,7,'Levothyroxine 50mcg','levothyroxine-50mcg-6-744','Levothyroxine',439.00,504.85,419,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(244,9,3,4,'Levothyroxine 50mcg','levothyroxine-50mcg-9-301','Levothyroxine',447.75,514.91,427,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(245,11,3,8,'Levothyroxine 50mcg','levothyroxine-50mcg-11-783','Levothyroxine',420.50,483.58,680,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(246,12,3,4,'Levothyroxine 50mcg','levothyroxine-50mcg-12-905','Levothyroxine',430.25,494.79,91,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(247,6,1,2,'Ciprofloxacin 500mg','ciprofloxacin-500mg-6-385','Ciprofloxacin',264.00,303.60,945,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(248,9,1,5,'Ciprofloxacin 500mg','ciprofloxacin-500mg-9-446','Ciprofloxacin',450.75,518.36,494,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(249,11,1,1,'Ciprofloxacin 500mg','ciprofloxacin-500mg-11-537','Ciprofloxacin',314.50,361.68,785,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(250,12,1,5,'Ciprofloxacin 500mg','ciprofloxacin-500mg-12-376','Ciprofloxacin',232.25,267.09,741,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(251,6,4,2,'Furosemide 40mg','furosemide-40mg-6-885','Furosemide',189.00,217.35,562,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(252,9,4,5,'Furosemide 40mg','furosemide-40mg-9-555','Furosemide',115.75,133.11,985,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(253,11,4,4,'Furosemide 40mg','furosemide-40mg-11-885','Furosemide',139.50,160.43,758,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(254,12,4,3,'Furosemide 40mg','furosemide-40mg-12-595','Furosemide',192.25,221.09,788,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(255,6,4,7,'Lisinopril 10mg','lisinopril-10mg-6-208','Lisinopril',433.00,497.95,75,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(256,9,4,7,'Lisinopril 10mg','lisinopril-10mg-9-898','Lisinopril',304.75,350.46,191,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(257,11,4,6,'Lisinopril 10mg','lisinopril-10mg-11-248','Lisinopril',173.50,199.53,898,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(258,12,4,5,'Lisinopril 10mg','lisinopril-10mg-12-512','Lisinopril',457.25,525.84,120,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(259,6,4,8,'Simvastatin 20mg','simvastatin-20mg-6-977','Simvastatin',279.00,320.85,206,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(260,9,4,1,'Simvastatin 20mg','simvastatin-20mg-9-151','Simvastatin',305.75,351.61,400,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(261,11,4,1,'Simvastatin 20mg','simvastatin-20mg-11-946','Simvastatin',243.50,280.03,771,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(262,12,4,2,'Simvastatin 20mg','simvastatin-20mg-12-909','Simvastatin',299.25,344.14,434,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(263,6,4,1,'Hydrochlorothiazide 25mg','hydrochlorothiazide-25mg-6-722','Hydrochlorothiazide',160.00,184.00,832,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(264,9,4,3,'Hydrochlorothiazide 25mg','hydrochlorothiazide-25mg-9-355','Hydrochlorothiazide',446.75,513.76,357,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(265,11,4,2,'Hydrochlorothiazide 25mg','hydrochlorothiazide-25mg-11-882','Hydrochlorothiazide',87.50,100.63,495,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(266,12,4,5,'Hydrochlorothiazide 25mg','hydrochlorothiazide-25mg-12-350','Hydrochlorothiazide',201.25,231.44,525,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(267,6,6,8,'Alprazolam 0.5mg','alprazolam-0.5mg-6-175','Alprazolam',60.00,69.00,777,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(268,9,6,7,'Alprazolam 0.5mg','alprazolam-0.5mg-9-101','Alprazolam',67.75,77.91,162,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(269,11,6,9,'Alprazolam 0.5mg','alprazolam-0.5mg-11-516','Alprazolam',176.50,202.98,678,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(270,12,6,7,'Alprazolam 0.5mg','alprazolam-0.5mg-12-730','Alprazolam',292.25,336.09,232,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(271,6,1,10,'Doxycycline 100mg','doxycycline-100mg-6-329','Doxycycline',179.00,205.85,960,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(272,9,1,7,'Doxycycline 100mg','doxycycline-100mg-9-299','Doxycycline',212.75,244.66,887,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(273,11,1,8,'Doxycycline 100mg','doxycycline-100mg-11-692','Doxycycline',162.50,186.88,295,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(274,12,1,3,'Doxycycline 100mg','doxycycline-100mg-12-602','Doxycycline',242.25,278.59,699,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(275,6,6,1,'Sertraline 50mg','sertraline-50mg-6-602','Sertraline',471.00,541.65,965,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(276,9,6,1,'Sertraline 50mg','sertraline-50mg-9-863','Sertraline',463.75,533.31,97,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(277,11,6,4,'Sertraline 50mg','sertraline-50mg-11-505','Sertraline',395.50,454.83,984,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(278,12,6,3,'Sertraline 50mg','sertraline-50mg-12-518','Sertraline',339.25,390.14,100,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(279,6,6,6,'Fluoxetine 20mg','fluoxetine-20mg-6-560','Fluoxetine',378.00,434.70,285,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(280,9,6,8,'Fluoxetine 20mg','fluoxetine-20mg-9-959','Fluoxetine',228.75,263.06,999,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(281,11,6,4,'Fluoxetine 20mg','fluoxetine-20mg-11-649','Fluoxetine',233.50,268.53,495,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(282,12,6,8,'Fluoxetine 20mg','fluoxetine-20mg-12-935','Fluoxetine',528.25,607.49,537,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(283,6,6,9,'Citalopram 20mg','citalopram-20mg-6-342','Citalopram',286.00,328.90,920,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(284,9,6,1,'Citalopram 20mg','citalopram-20mg-9-214','Citalopram',224.75,258.46,652,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(285,11,6,9,'Citalopram 20mg','citalopram-20mg-11-982','Citalopram',434.50,499.68,77,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(286,12,6,3,'Citalopram 20mg','citalopram-20mg-12-692','Citalopram',205.25,236.04,708,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(287,6,4,9,'Warfarin 5mg','warfarin-5mg-6-273','Warfarin',475.00,546.25,29,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(288,9,4,8,'Warfarin 5mg','warfarin-5mg-9-594','Warfarin',477.75,549.41,257,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(289,11,4,7,'Warfarin 5mg','warfarin-5mg-11-276','Warfarin',111.50,128.23,251,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(290,12,4,8,'Warfarin 5mg','warfarin-5mg-12-382','Warfarin',216.25,248.69,664,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(291,6,4,2,'Clopidogrel 75mg','clopidogrel-75mg-6-654','Clopidogrel',400.00,460.00,85,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(292,9,4,1,'Clopidogrel 75mg','clopidogrel-75mg-9-881','Clopidogrel',334.75,384.96,674,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(293,11,4,10,'Clopidogrel 75mg','clopidogrel-75mg-11-594','Clopidogrel',192.50,221.38,622,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(294,12,4,10,'Clopidogrel 75mg','clopidogrel-75mg-12-851','Clopidogrel',166.25,191.19,109,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(295,6,7,10,'Prednisone 5mg','prednisone-5mg-6-326','Prednisone',487.00,560.05,178,'assets/images/medicines/ointment_tube.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(296,9,7,7,'Prednisone 5mg','prednisone-5mg-9-798','Prednisone',421.75,485.01,569,'assets/images/medicines/ointment_tube.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(297,11,7,3,'Prednisone 5mg','prednisone-5mg-11-176','Prednisone',410.50,472.08,596,'assets/images/medicines/ointment_tube.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(298,12,7,9,'Prednisone 5mg','prednisone-5mg-12-691','Prednisone',311.25,357.94,704,'assets/images/medicines/ointment_tube.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(299,6,5,9,'Ranitidine 150mg','ranitidine-150mg-6-309','Ranitidine',192.00,220.80,852,'assets/images/syrup.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:17:10',NULL),(300,9,5,2,'Ranitidine 150mg','ranitidine-150mg-9-238','Ranitidine',472.75,543.66,534,'assets/images/syrup.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:17:10',NULL),(301,11,5,1,'Ranitidine 150mg','ranitidine-150mg-11-546','Ranitidine',110.50,127.08,90,'assets/images/syrup.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:17:10',NULL),(302,12,5,3,'Ranitidine 150mg','ranitidine-150mg-12-866','Ranitidine',368.25,423.49,210,'assets/images/syrup.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:17:10',NULL),(303,6,3,8,'Glimepiride 2mg','glimepiride-2mg-6-382','Glimepiride',99.00,113.85,229,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(304,9,3,3,'Glimepiride 2mg','glimepiride-2mg-9-277','Glimepiride',370.75,426.36,390,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(305,11,3,9,'Glimepiride 2mg','glimepiride-2mg-11-781','Glimepiride',337.50,388.13,573,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(306,12,3,3,'Glimepiride 2mg','glimepiride-2mg-12-952','Glimepiride',491.25,564.94,464,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(307,6,4,9,'Spironolactone 25mg','spironolactone-25mg-6-143','Spironolactone',185.00,212.75,449,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(308,9,4,10,'Spironolactone 25mg','spironolactone-25mg-9-846','Spironolactone',430.75,495.36,946,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(309,11,4,10,'Spironolactone 25mg','spironolactone-25mg-11-727','Spironolactone',105.50,121.33,989,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(310,12,4,9,'Spironolactone 25mg','spironolactone-25mg-12-317','Spironolactone',260.25,299.29,202,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(311,6,7,10,'Allopurinol 100mg','allopurinol-100mg-6-684','Allopurinol',160.00,184.00,990,'assets/images/medicines/ointment_tube.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(312,9,7,9,'Allopurinol 100mg','allopurinol-100mg-9-465','Allopurinol',162.75,187.16,665,'assets/images/medicines/ointment_tube.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(313,11,7,10,'Allopurinol 100mg','allopurinol-100mg-11-509','Allopurinol',390.50,449.08,555,'assets/images/medicines/ointment_tube.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(314,12,7,10,'Allopurinol 100mg','allopurinol-100mg-12-362','Allopurinol',101.25,116.44,890,'assets/images/medicines/ointment_tube.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(315,6,8,9,'Tamsulosin 0.4mg','tamsulosin-0.4mg-6-435','Tamsulosin',155.00,178.25,996,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(316,9,8,6,'Tamsulosin 0.4mg','tamsulosin-0.4mg-9-963','Tamsulosin',199.75,229.71,604,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(317,11,8,6,'Tamsulosin 0.4mg','tamsulosin-0.4mg-11-223','Tamsulosin',241.50,277.73,948,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(318,12,8,6,'Tamsulosin 0.4mg','tamsulosin-0.4mg-12-972','Tamsulosin',187.25,215.34,406,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(319,6,8,2,'Finasteride 5mg','finasteride-5mg-6-577','Finasteride',486.00,558.90,666,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(320,9,8,7,'Finasteride 5mg','finasteride-5mg-9-318','Finasteride',473.75,544.81,31,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(321,11,8,4,'Finasteride 5mg','finasteride-5mg-11-983','Finasteride',502.50,577.88,974,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(322,12,8,5,'Finasteride 5mg','finasteride-5mg-12-119','Finasteride',345.25,397.04,596,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(323,6,7,1,'Meloxicam 15mg','meloxicam-15mg-6-518','Meloxicam',330.00,379.50,807,'assets/images/medicines/ointment_tube.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(324,9,7,5,'Meloxicam 15mg','meloxicam-15mg-9-244','Meloxicam',370.75,426.36,721,'assets/images/medicines/ointment_tube.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(325,11,7,8,'Meloxicam 15mg','meloxicam-15mg-11-518','Meloxicam',381.50,438.73,84,'assets/images/medicines/ointment_tube.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(326,12,7,8,'Meloxicam 15mg','meloxicam-15mg-12-307','Meloxicam',264.25,303.89,309,'assets/images/medicines/ointment_tube.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(327,6,1,4,'Tramadol 50mg','tramadol-50mg-6-434','Tramadol',345.00,396.75,987,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(328,9,1,8,'Tramadol 50mg','tramadol-50mg-9-163','Tramadol',496.75,571.26,175,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(329,11,1,6,'Tramadol 50mg','tramadol-50mg-11-252','Tramadol',81.50,93.73,595,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(330,12,1,6,'Tramadol 50mg','tramadol-50mg-12-356','Tramadol',417.25,479.84,157,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(331,6,6,4,'Zolpidem 10mg','zolpidem-10mg-6-227','Zolpidem',79.00,90.85,804,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(332,9,6,2,'Zolpidem 10mg','zolpidem-10mg-9-541','Zolpidem',298.75,343.56,493,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(333,11,6,4,'Zolpidem 10mg','zolpidem-10mg-11-324','Zolpidem',399.50,459.43,989,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(334,12,6,7,'Zolpidem 10mg','zolpidem-10mg-12-764','Zolpidem',259.25,298.14,602,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(335,6,5,3,'Ondansetron 4mg','ondansetron-4mg-6-354','Ondansetron',480.00,552.00,146,'assets/images/syrup.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:17:10',NULL),(336,9,5,7,'Ondansetron 4mg','ondansetron-4mg-9-319','Ondansetron',301.75,347.01,922,'assets/images/syrup.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:17:10',NULL),(337,11,5,9,'Ondansetron 4mg','ondansetron-4mg-11-823','Ondansetron',467.50,537.63,86,'assets/images/syrup.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:17:10',NULL),(338,12,5,10,'Ondansetron 4mg','ondansetron-4mg-12-964','Ondansetron',493.25,567.24,119,'assets/images/syrup.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:17:10',NULL),(339,6,6,7,'Escitalopram 10mg','escitalopram-10mg-6-129','Escitalopram',117.00,134.55,772,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(340,9,6,10,'Escitalopram 10mg','escitalopram-10mg-9-689','Escitalopram',85.75,98.61,640,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(341,11,6,5,'Escitalopram 10mg','escitalopram-10mg-11-834','Escitalopram',452.50,520.38,496,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(342,12,6,9,'Escitalopram 10mg','escitalopram-10mg-12-234','Escitalopram',323.25,371.74,810,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(343,6,6,1,'Venlafaxine 75mg','venlafaxine-75mg-6-265','Venlafaxine',277.00,318.55,928,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(344,9,6,8,'Venlafaxine 75mg','venlafaxine-75mg-9-592','Venlafaxine',480.75,552.86,448,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(345,11,6,10,'Venlafaxine 75mg','venlafaxine-75mg-11-327','Venlafaxine',495.50,569.83,846,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(346,12,6,4,'Venlafaxine 75mg','venlafaxine-75mg-12-319','Venlafaxine',136.25,156.69,133,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(347,6,6,6,'Bupropion 150mg','bupropion-150mg-6-317','Bupropion',259.00,297.85,680,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(348,9,6,2,'Bupropion 150mg','bupropion-150mg-9-505','Bupropion',220.75,253.86,966,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(349,11,6,5,'Bupropion 150mg','bupropion-150mg-11-794','Bupropion',182.50,209.88,951,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(350,12,6,7,'Bupropion 150mg','bupropion-150mg-12-682','Bupropion',306.25,352.19,352,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(351,6,4,3,'Rosuvastatin 10mg','rosuvastatin-10mg-6-415','Rosuvastatin',159.00,182.85,168,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(352,9,4,6,'Rosuvastatin 10mg','rosuvastatin-10mg-9-214','Rosuvastatin',144.75,166.46,115,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(353,11,4,3,'Rosuvastatin 10mg','rosuvastatin-10mg-11-451','Rosuvastatin',148.50,170.78,544,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(354,12,4,8,'Rosuvastatin 10mg','rosuvastatin-10mg-12-399','Rosuvastatin',135.25,155.54,753,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(355,6,4,10,'Metoprolol 50mg','metoprolol-50mg-6-961','Metoprolol',171.00,196.65,951,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(356,9,4,2,'Metoprolol 50mg','metoprolol-50mg-9-616','Metoprolol',189.75,218.21,137,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(357,11,4,6,'Metoprolol 50mg','metoprolol-50mg-11-695','Metoprolol',167.50,192.63,629,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(358,12,4,1,'Metoprolol 50mg','metoprolol-50mg-12-385','Metoprolol',298.25,342.99,780,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(359,6,6,2,'Duloxetine 30mg','duloxetine-30mg-6-386','Duloxetine',111.00,127.65,348,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(360,9,6,5,'Duloxetine 30mg','duloxetine-30mg-9-355','Duloxetine',139.75,160.71,915,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(361,11,6,5,'Duloxetine 30mg','duloxetine-30mg-11-674','Duloxetine',186.50,214.48,894,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(362,12,6,6,'Duloxetine 30mg','duloxetine-30mg-12-413','Duloxetine',325.25,374.04,690,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(363,6,4,4,'Carvedilol 6.25mg','carvedilol-6.25mg-6-802','Carvedilol',147.00,169.05,325,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(364,9,4,2,'Carvedilol 6.25mg','carvedilol-6.25mg-9-248','Carvedilol',269.75,310.21,790,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(365,11,4,2,'Carvedilol 6.25mg','carvedilol-6.25mg-11-792','Carvedilol',420.50,483.58,103,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(366,12,4,1,'Carvedilol 6.25mg','carvedilol-6.25mg-12-519','Carvedilol',312.25,359.09,681,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(367,6,6,10,'Clonazepam 0.5mg','clonazepam-0.5mg-6-963','Clonazepam',387.00,445.05,67,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(368,9,6,3,'Clonazepam 0.5mg','clonazepam-0.5mg-9-668','Clonazepam',460.75,529.86,743,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(369,11,6,6,'Clonazepam 0.5mg','clonazepam-0.5mg-11-696','Clonazepam',486.50,559.48,600,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(370,12,6,6,'Clonazepam 0.5mg','clonazepam-0.5mg-12-972','Clonazepam',80.25,92.29,226,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(371,6,1,2,'Acyclovir 400mg','acyclovir-400mg-6-657','Acyclovir',224.00,257.60,531,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(372,9,1,4,'Acyclovir 400mg','acyclovir-400mg-9-235','Acyclovir',339.75,390.71,274,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(373,11,1,4,'Acyclovir 400mg','acyclovir-400mg-11-459','Acyclovir',211.50,243.23,323,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(374,12,1,9,'Acyclovir 400mg','acyclovir-400mg-12-356','Acyclovir',475.25,546.54,915,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(375,6,1,8,'Levofloxacin 500mg','levofloxacin-500mg-6-141','Levofloxacin',218.00,250.70,711,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(376,9,1,6,'Levofloxacin 500mg','levofloxacin-500mg-9-774','Levofloxacin',352.75,405.66,197,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(377,11,1,4,'Levofloxacin 500mg','levofloxacin-500mg-11-195','Levofloxacin',280.50,322.58,816,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(378,12,1,6,'Levofloxacin 500mg','levofloxacin-500mg-12-726','Levofloxacin',257.25,295.84,126,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(379,6,5,8,'Lansoprazole 30mg','lansoprazole-30mg-6-840','Lansoprazole',126.00,144.90,762,'assets/images/syrup.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:17:10',NULL),(380,9,5,5,'Lansoprazole 30mg','lansoprazole-30mg-9-781','Lansoprazole',305.75,351.61,942,'assets/images/syrup.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:17:10',NULL),(381,11,5,10,'Lansoprazole 30mg','lansoprazole-30mg-11-638','Lansoprazole',486.50,559.48,890,'assets/images/syrup.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:17:10',NULL),(382,12,5,3,'Lansoprazole 30mg','lansoprazole-30mg-12-585','Lansoprazole',481.25,553.44,266,'assets/images/syrup.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:17:10',NULL),(383,6,4,10,'Valsartan 80mg','valsartan-80mg-6-326','Valsartan',329.00,378.35,344,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(384,9,4,3,'Valsartan 80mg','valsartan-80mg-9-509','Valsartan',322.75,371.16,517,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(385,11,4,3,'Valsartan 80mg','valsartan-80mg-11-469','Valsartan',172.50,198.38,696,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(386,12,4,4,'Valsartan 80mg','valsartan-80mg-12-924','Valsartan',356.25,409.69,543,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(387,6,1,7,'Clarithromycin 500mg','clarithromycin-500mg-6-774','Clarithromycin',477.00,548.55,246,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(388,9,1,1,'Clarithromycin 500mg','clarithromycin-500mg-9-499','Clarithromycin',366.75,421.76,219,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(389,11,1,8,'Clarithromycin 500mg','clarithromycin-500mg-11-805','Clarithromycin',478.50,550.28,792,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(390,12,1,10,'Clarithromycin 500mg','clarithromycin-500mg-12-187','Clarithromycin',178.25,204.99,812,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(391,6,1,8,'Fluconazole 150mg','fluconazole-150mg-6-332','Fluconazole',76.00,87.40,457,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(392,9,1,2,'Fluconazole 150mg','fluconazole-150mg-9-631','Fluconazole',299.75,344.71,772,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(393,11,1,2,'Fluconazole 150mg','fluconazole-150mg-11-643','Fluconazole',113.50,130.53,62,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(394,12,1,8,'Fluconazole 150mg','fluconazole-150mg-12-461','Fluconazole',120.25,138.29,994,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(395,6,6,2,'Ritalin 10mg','ritalin-10mg-6-881','Methylphenidate',469.00,539.35,893,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(396,9,6,1,'Ritalin 10mg','ritalin-10mg-9-916','Methylphenidate',501.75,577.01,663,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(397,11,6,1,'Ritalin 10mg','ritalin-10mg-11-768','Methylphenidate',437.50,503.13,817,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(398,12,6,5,'Ritalin 10mg','ritalin-10mg-12-131','Methylphenidate',524.25,602.89,331,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(399,6,6,7,'Adderall 10mg','adderall-10mg-6-624','Amphetamine',401.00,461.15,709,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(400,9,6,3,'Adderall 10mg','adderall-10mg-9-151','Amphetamine',410.75,472.36,739,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(401,11,6,7,'Adderall 10mg','adderall-10mg-11-867','Amphetamine',392.50,451.38,126,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(402,12,6,6,'Adderall 10mg','adderall-10mg-12-357','Amphetamine',313.25,360.24,323,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(403,6,1,2,'Vicodin 5mg','vicodin-5mg-6-599','Hydrocodone/Acetaminophen',194.00,223.10,73,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(404,9,1,3,'Vicodin 5mg','vicodin-5mg-9-988','Hydrocodone/Acetaminophen',402.75,463.16,743,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(405,11,1,10,'Vicodin 5mg','vicodin-5mg-11-629','Hydrocodone/Acetaminophen',160.50,184.58,363,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(406,12,1,10,'Vicodin 5mg','vicodin-5mg-12-944','Hydrocodone/Acetaminophen',527.25,606.34,977,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(407,6,6,7,'Xanax 0.25mg','xanax-0.25mg-6-739','Alprazolam',75.00,86.25,858,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(408,9,6,2,'Xanax 0.25mg','xanax-0.25mg-9-330','Alprazolam',399.75,459.71,338,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(409,11,6,10,'Xanax 0.25mg','xanax-0.25mg-11-691','Alprazolam',413.50,475.53,813,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(410,12,6,8,'Xanax 0.25mg','xanax-0.25mg-12-570','Alprazolam',286.25,329.19,790,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(411,6,6,4,'Valium 5mg','valium-5mg-6-589','Diazepam',449.00,516.35,163,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(412,9,6,4,'Valium 5mg','valium-5mg-9-178','Diazepam',94.75,108.96,996,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(413,11,6,7,'Valium 5mg','valium-5mg-11-454','Diazepam',176.50,202.98,796,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(414,12,6,4,'Valium 5mg','valium-5mg-12-938','Diazepam',397.25,456.84,253,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(415,6,1,2,'Percocet 5mg','percocet-5mg-6-726','Oxycodone/Acetaminophen',104.00,119.60,35,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(416,9,1,6,'Percocet 5mg','percocet-5mg-9-351','Oxycodone/Acetaminophen',461.75,531.01,234,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(417,11,1,1,'Percocet 5mg','percocet-5mg-11-512','Oxycodone/Acetaminophen',172.50,198.38,260,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(418,12,1,5,'Percocet 5mg','percocet-5mg-12-593','Oxycodone/Acetaminophen',363.25,417.74,912,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(419,6,3,2,'Synthroid 75mcg','synthroid-75mcg-6-565','Levothyroxine',78.00,89.70,369,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(420,9,3,5,'Synthroid 75mcg','synthroid-75mcg-9-706','Levothyroxine',348.75,401.06,444,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(421,11,3,8,'Synthroid 75mcg','synthroid-75mcg-11-444','Levothyroxine',392.50,451.38,288,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(422,12,3,8,'Synthroid 75mcg','synthroid-75mcg-12-358','Levothyroxine',201.25,231.44,33,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(423,6,4,9,'Zestril 20mg','zestril-20mg-6-353','Lisinopril',458.00,526.70,850,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(424,9,4,4,'Zestril 20mg','zestril-20mg-9-462','Lisinopril',194.75,223.96,674,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(425,11,4,8,'Zestril 20mg','zestril-20mg-11-643','Lisinopril',442.50,508.88,701,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(426,12,4,1,'Zestril 20mg','zestril-20mg-12-840','Lisinopril',344.25,395.89,426,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(427,6,4,1,'Lipitor 20mg','lipitor-20mg-6-767','Atorvastatin',58.00,66.70,780,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(428,9,4,3,'Lipitor 20mg','lipitor-20mg-9-798','Atorvastatin',53.75,61.81,839,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(429,11,4,8,'Lipitor 20mg','lipitor-20mg-11-234','Atorvastatin',383.50,441.03,763,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(430,12,4,10,'Lipitor 20mg','lipitor-20mg-12-939','Atorvastatin',332.25,382.09,968,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(431,6,5,4,'Nexium 40mg','nexium-40mg-6-875','Esomeprazole',422.00,485.30,87,'assets/images/syrup.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:17:10',NULL),(432,9,5,5,'Nexium 40mg','nexium-40mg-9-976','Esomeprazole',446.75,513.76,230,'assets/images/syrup.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:17:10',NULL),(433,11,5,8,'Nexium 40mg','nexium-40mg-11-512','Esomeprazole',447.50,514.63,59,'assets/images/syrup.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:17:10',NULL),(434,12,5,10,'Nexium 40mg','nexium-40mg-12-464','Esomeprazole',429.25,493.64,348,'assets/images/syrup.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:17:10',NULL),(435,6,2,2,'Singulair 5mg','singulair-5mg-6-286','Montelukast',352.00,404.80,868,'assets/images/syrup.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:17:10',NULL),(436,9,2,8,'Singulair 5mg','singulair-5mg-9-128','Montelukast',212.75,244.66,137,'assets/images/syrup.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:17:10',NULL),(437,11,2,3,'Singulair 5mg','singulair-5mg-11-827','Montelukast',118.50,136.28,470,'assets/images/syrup.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:17:10',NULL),(438,12,2,2,'Singulair 5mg','singulair-5mg-12-149','Montelukast',276.25,317.69,369,'assets/images/syrup.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:17:10',NULL),(439,6,4,10,'Plavix 75mg','plavix-75mg-6-842','Clopidogrel',124.00,142.60,57,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(440,9,4,6,'Plavix 75mg','plavix-75mg-9-861','Clopidogrel',280.75,322.86,317,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(441,11,4,4,'Plavix 75mg','plavix-75mg-11-839','Clopidogrel',472.50,543.38,699,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(442,12,4,1,'Plavix 75mg','plavix-75mg-12-203','Clopidogrel',514.25,591.39,55,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(443,6,2,10,'Advair Diskus','advair-diskus-6-983','Fluticasone/Salmeterol',44.00,50.60,898,'assets/images/syrup.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:17:10',NULL),(444,9,2,7,'Advair Diskus','advair-diskus-9-431','Fluticasone/Salmeterol',76.75,88.26,750,'assets/images/syrup.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:17:10',NULL),(445,11,2,7,'Advair Diskus','advair-diskus-11-611','Fluticasone/Salmeterol',393.50,452.53,256,'assets/images/syrup.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:17:10',NULL),(446,12,2,3,'Advair Diskus','advair-diskus-12-101','Fluticasone/Salmeterol',481.25,553.44,294,'assets/images/syrup.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:17:10',NULL),(447,6,6,7,'Abilify 5mg','abilify-5mg-6-380','Aripiprazole',464.00,533.60,239,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(448,9,6,5,'Abilify 5mg','abilify-5mg-9-226','Aripiprazole',211.75,243.51,672,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(449,11,6,3,'Abilify 5mg','abilify-5mg-11-559','Aripiprazole',463.50,533.03,923,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(450,12,6,3,'Abilify 5mg','abilify-5mg-12-489','Aripiprazole',471.25,541.94,298,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(451,6,6,10,'Seroquel 25mg','seroquel-25mg-6-301','Quetiapine',168.00,193.20,502,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(452,9,6,4,'Seroquel 25mg','seroquel-25mg-9-200','Quetiapine',189.75,218.21,527,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(453,11,6,1,'Seroquel 25mg','seroquel-25mg-11-113','Quetiapine',344.50,396.18,646,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(454,12,6,9,'Seroquel 25mg','seroquel-25mg-12-708','Quetiapine',126.25,145.19,983,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(455,6,6,8,'Cymbalta 60mg','cymbalta-60mg-6-558','Duloxetine',373.00,428.95,619,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(456,9,6,3,'Cymbalta 60mg','cymbalta-60mg-9-174','Duloxetine',344.75,396.46,82,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(457,11,6,9,'Cymbalta 60mg','cymbalta-60mg-11-935','Duloxetine',228.50,262.78,725,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(458,12,6,7,'Cymbalta 60mg','cymbalta-60mg-12-323','Duloxetine',472.25,543.09,522,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(459,6,6,1,'Lyrica 75mg','lyrica-75mg-6-356','Pregabalin',130.00,149.50,939,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(460,9,6,3,'Lyrica 75mg','lyrica-75mg-9-744','Pregabalin',234.75,269.96,952,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(461,11,6,10,'Lyrica 75mg','lyrica-75mg-11-484','Pregabalin',315.50,362.83,132,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(462,12,6,2,'Lyrica 75mg','lyrica-75mg-12-828','Pregabalin',243.25,279.74,327,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(463,6,7,9,'Celebrex 200mg','celebrex-200mg-6-944','Celecoxib',462.00,531.30,345,'assets/images/medicines/ointment_tube.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(464,9,7,5,'Celebrex 200mg','celebrex-200mg-9-147','Celecoxib',318.75,366.56,707,'assets/images/medicines/ointment_tube.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(465,11,7,9,'Celebrex 200mg','celebrex-200mg-11-814','Celecoxib',135.50,155.83,693,'assets/images/medicines/ointment_tube.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(466,12,7,7,'Celebrex 200mg','celebrex-200mg-12-275','Celecoxib',82.25,94.59,806,'assets/images/medicines/ointment_tube.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(467,6,3,2,'Januvia 100mg','januvia-100mg-6-364','Sitagliptin',405.00,465.75,332,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(468,9,3,8,'Januvia 100mg','januvia-100mg-9-667','Sitagliptin',187.75,215.91,47,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(469,11,3,4,'Januvia 100mg','januvia-100mg-11-940','Sitagliptin',481.50,553.73,397,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(470,12,3,9,'Januvia 100mg','januvia-100mg-12-709','Sitagliptin',491.25,564.94,869,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(471,6,6,10,'Namenda 10mg','namenda-10mg-6-903','Memantine',227.00,261.05,730,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(472,9,6,8,'Namenda 10mg','namenda-10mg-9-372','Memantine',297.75,342.41,343,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(473,11,6,10,'Namenda 10mg','namenda-10mg-11-714','Memantine',251.50,289.23,989,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(474,12,6,5,'Namenda 10mg','namenda-10mg-12-769','Memantine',197.25,226.84,654,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(475,6,6,8,'Vyvanse 30mg','vyvanse-30mg-6-491','Lisdexamfetamine',301.00,346.15,776,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(476,9,6,5,'Vyvanse 30mg','vyvanse-30mg-9-913','Lisdexamfetamine',171.75,197.51,110,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(477,11,6,3,'Vyvanse 30mg','vyvanse-30mg-11-927','Lisdexamfetamine',183.50,211.03,749,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(478,12,6,6,'Vyvanse 30mg','vyvanse-30mg-12-263','Lisdexamfetamine',258.25,296.99,847,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(479,6,3,6,'Humalog 100u/ml','humalog-100u-ml-6-761','Insulin Lispro',374.00,430.10,31,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(480,9,3,1,'Humalog 100u/ml','humalog-100u-ml-9-509','Insulin Lispro',357.75,411.41,190,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(481,11,3,8,'Humalog 100u/ml','humalog-100u-ml-11-583','Insulin Lispro',447.50,514.63,376,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(482,12,3,5,'Humalog 100u/ml','humalog-100u-ml-12-898','Insulin Lispro',358.25,411.99,175,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(483,6,3,3,'Lantus 100u/ml','lantus-100u-ml-6-717','Insulin Glargine',117.00,134.55,76,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(484,9,3,10,'Lantus 100u/ml','lantus-100u-ml-9-153','Insulin Glargine',216.75,249.26,261,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(485,11,3,2,'Lantus 100u/ml','lantus-100u-ml-11-773','Insulin Glargine',450.50,518.08,500,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(486,12,3,10,'Lantus 100u/ml','lantus-100u-ml-12-296','Insulin Glargine',403.25,463.74,756,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(487,6,3,8,'Victoza 6mg/ml','victoza-6mg-ml-6-457','Liraglutide',244.00,280.60,729,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(488,9,3,4,'Victoza 6mg/ml','victoza-6mg-ml-9-964','Liraglutide',224.75,258.46,413,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(489,11,3,4,'Victoza 6mg/ml','victoza-6mg-ml-11-678','Liraglutide',283.50,326.03,652,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(490,12,3,8,'Victoza 6mg/ml','victoza-6mg-ml-12-447','Liraglutide',482.25,554.59,411,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(491,6,3,7,'Trulicity 0.75mg','trulicity-0.75mg-6-508','Dulaglutide',204.00,234.60,781,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(492,9,3,3,'Trulicity 0.75mg','trulicity-0.75mg-9-174','Dulaglutide',135.75,156.11,753,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(493,11,3,3,'Trulicity 0.75mg','trulicity-0.75mg-11-751','Dulaglutide',462.50,531.88,36,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(494,12,3,5,'Trulicity 0.75mg','trulicity-0.75mg-12-766','Dulaglutide',200.25,230.29,645,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(495,6,3,10,'Ozempic 0.25mg','ozempic-0.25mg-6-207','Semaglutide',66.00,75.90,99,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(496,9,3,8,'Ozempic 0.25mg','ozempic-0.25mg-9-833','Semaglutide',443.75,510.31,719,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(497,11,3,5,'Ozempic 0.25mg','ozempic-0.25mg-11-774','Semaglutide',198.50,228.28,430,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(498,12,3,9,'Ozempic 0.25mg','ozempic-0.25mg-12-708','Semaglutide',473.25,544.24,525,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(499,6,3,2,'Jardiance 10mg','jardiance-10mg-6-245','Empagliflozin',455.00,523.25,834,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(500,9,3,9,'Jardiance 10mg','jardiance-10mg-9-583','Empagliflozin',255.75,294.11,426,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(501,11,3,5,'Jardiance 10mg','jardiance-10mg-11-847','Empagliflozin',399.50,459.43,969,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(502,12,3,2,'Jardiance 10mg','jardiance-10mg-12-452','Empagliflozin',438.25,503.99,858,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(503,6,3,8,'Farxiga 5mg','farxiga-5mg-6-758','Dapagliflozin',90.00,103.50,532,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(504,9,3,3,'Farxiga 5mg','farxiga-5mg-9-309','Dapagliflozin',445.75,512.61,305,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(505,11,3,1,'Farxiga 5mg','farxiga-5mg-11-255','Dapagliflozin',300.50,345.58,925,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(506,12,3,7,'Farxiga 5mg','farxiga-5mg-12-322','Dapagliflozin',398.25,457.99,199,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(507,6,4,5,'Entresto 24/26mg','entresto-24-26mg-6-947','Sacubitril/Valsartan',433.00,497.95,945,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(508,9,4,6,'Entresto 24/26mg','entresto-24-26mg-9-399','Sacubitril/Valsartan',324.75,373.46,522,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(509,11,4,8,'Entresto 24/26mg','entresto-24-26mg-11-385','Sacubitril/Valsartan',201.50,231.73,405,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(510,12,4,6,'Entresto 24/26mg','entresto-24-26mg-12-631','Sacubitril/Valsartan',453.25,521.24,734,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(511,6,4,7,'Eliquis 2.5mg','eliquis-2.5mg-6-537','Apixaban',320.00,368.00,741,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(512,9,4,5,'Eliquis 2.5mg','eliquis-2.5mg-9-761','Apixaban',66.75,76.76,325,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(513,11,4,9,'Eliquis 2.5mg','eliquis-2.5mg-11-582','Apixaban',343.50,395.03,755,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(514,12,4,5,'Eliquis 2.5mg','eliquis-2.5mg-12-472','Apixaban',341.25,392.44,187,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(515,6,4,9,'Xarelto 10mg','xarelto-10mg-6-304','Rivaroxaban',83.00,95.45,207,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(516,9,4,3,'Xarelto 10mg','xarelto-10mg-9-424','Rivaroxaban',471.75,542.51,214,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(517,11,4,1,'Xarelto 10mg','xarelto-10mg-11-883','Rivaroxaban',486.50,559.48,40,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(518,12,4,5,'Xarelto 10mg','xarelto-10mg-12-250','Rivaroxaban',351.25,403.94,52,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(519,6,4,10,'Brilinta 60mg','brilinta-60mg-6-147','Ticagrelor',346.00,397.90,937,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(520,9,4,2,'Brilinta 60mg','brilinta-60mg-9-294','Ticagrelor',404.75,465.46,394,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(521,11,4,2,'Brilinta 60mg','brilinta-60mg-11-847','Ticagrelor',304.50,350.18,794,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(522,12,4,10,'Brilinta 60mg','brilinta-60mg-12-261','Ticagrelor',157.25,180.84,518,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(523,6,4,8,'Repatha 140mg','repatha-140mg-6-112','Evolocumab',285.00,327.75,48,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(524,9,4,6,'Repatha 140mg','repatha-140mg-9-526','Evolocumab',439.75,505.71,65,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(525,11,4,6,'Repatha 140mg','repatha-140mg-11-707','Evolocumab',103.50,119.03,449,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(526,12,4,4,'Repatha 140mg','repatha-140mg-12-484','Evolocumab',475.25,546.54,717,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(527,6,7,6,'Cosentyx 150mg','cosentyx-150mg-6-806','Secukinumab',99.00,113.85,379,'assets/images/medicines/ointment_tube.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(528,9,7,8,'Cosentyx 150mg','cosentyx-150mg-9-733','Secukinumab',113.75,130.81,359,'assets/images/medicines/ointment_tube.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(529,11,7,10,'Cosentyx 150mg','cosentyx-150mg-11-905','Secukinumab',223.50,257.03,121,'assets/images/medicines/ointment_tube.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(530,12,7,5,'Cosentyx 150mg','cosentyx-150mg-12-915','Secukinumab',392.25,451.09,539,'assets/images/medicines/ointment_tube.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(531,6,7,1,'Humira 40mg','humira-40mg-6-986','Adalimumab',299.00,343.85,944,'assets/images/medicines/ointment_tube.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(532,9,7,4,'Humira 40mg','humira-40mg-9-167','Adalimumab',262.75,302.16,897,'assets/images/medicines/ointment_tube.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(533,11,7,10,'Humira 40mg','humira-40mg-11-375','Adalimumab',342.50,393.88,261,'assets/images/medicines/ointment_tube.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(534,12,7,4,'Humira 40mg','humira-40mg-12-221','Adalimumab',478.25,549.99,272,'assets/images/medicines/ointment_tube.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(535,6,7,1,'Enbrel 50mg','enbrel-50mg-6-197','Etanercept',220.00,253.00,535,'assets/images/medicines/ointment_tube.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(536,9,7,7,'Enbrel 50mg','enbrel-50mg-9-311','Etanercept',270.75,311.36,654,'assets/images/medicines/ointment_tube.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(537,11,7,3,'Enbrel 50mg','enbrel-50mg-11-713','Etanercept',169.50,194.93,477,'assets/images/medicines/ointment_tube.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(538,12,7,2,'Enbrel 50mg','enbrel-50mg-12-414','Etanercept',130.25,149.79,806,'assets/images/medicines/ointment_tube.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(539,6,7,3,'Stelara 45mg','stelara-45mg-6-370','Ustekinumab',94.00,108.10,481,'assets/images/medicines/ointment_tube.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(540,9,7,3,'Stelara 45mg','stelara-45mg-9-493','Ustekinumab',464.75,534.46,319,'assets/images/medicines/ointment_tube.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(541,11,7,8,'Stelara 45mg','stelara-45mg-11-762','Ustekinumab',367.50,422.63,340,'assets/images/medicines/ointment_tube.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(542,12,7,10,'Stelara 45mg','stelara-45mg-12-222','Ustekinumab',258.25,296.99,221,'assets/images/medicines/ointment_tube.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(543,6,7,9,'Tremfya 100mg','tremfya-100mg-6-386','Guselkumab',484.00,556.60,382,'assets/images/medicines/ointment_tube.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(544,9,7,6,'Tremfya 100mg','tremfya-100mg-9-236','Guselkumab',281.75,324.01,378,'assets/images/medicines/ointment_tube.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(545,11,7,10,'Tremfya 100mg','tremfya-100mg-11-412','Guselkumab',124.50,143.18,230,'assets/images/medicines/ointment_tube.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(546,12,7,9,'Tremfya 100mg','tremfya-100mg-12-401','Guselkumab',261.25,300.44,177,'assets/images/medicines/ointment_tube.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(547,6,7,4,'Skyrizi 150mg','skyrizi-150mg-6-144','Risankizumab',413.00,474.95,619,'assets/images/medicines/ointment_tube.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(548,9,7,1,'Skyrizi 150mg','skyrizi-150mg-9-523','Risankizumab',470.75,541.36,813,'assets/images/medicines/ointment_tube.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(549,11,7,9,'Skyrizi 150mg','skyrizi-150mg-11-407','Risankizumab',264.50,304.18,826,'assets/images/medicines/ointment_tube.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(550,12,7,7,'Skyrizi 150mg','skyrizi-150mg-12-313','Risankizumab',521.25,599.44,410,'assets/images/medicines/ointment_tube.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(551,6,2,8,'Dupixent 200mg','dupixent-200mg-6-773','Dupilumab',462.00,531.30,726,'assets/images/syrup.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:17:11',NULL),(552,9,2,10,'Dupixent 200mg','dupixent-200mg-9-261','Dupilumab',301.75,347.01,363,'assets/images/syrup.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:17:11',NULL),(553,11,2,9,'Dupixent 200mg','dupixent-200mg-11-710','Dupilumab',302.50,347.88,858,'assets/images/syrup.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:17:11',NULL),(554,12,2,10,'Dupixent 200mg','dupixent-200mg-12-897','Dupilumab',469.25,539.64,795,'assets/images/syrup.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:17:11',NULL),(555,6,2,9,'Xolair 150mg','xolair-150mg-6-937','Omalizumab',299.00,343.85,909,'assets/images/syrup.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:17:11',NULL),(556,9,2,3,'Xolair 150mg','xolair-150mg-9-236','Omalizumab',223.75,257.31,481,'assets/images/syrup.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:17:11',NULL),(557,11,2,10,'Xolair 150mg','xolair-150mg-11-995','Omalizumab',346.50,398.48,723,'assets/images/syrup.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:17:11',NULL),(558,12,2,4,'Xolair 150mg','xolair-150mg-12-102','Omalizumab',481.25,553.44,57,'assets/images/syrup.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:17:11',NULL),(559,6,2,1,'Nucala 100mg','nucala-100mg-6-692','Mepolizumab',390.00,448.50,97,'assets/images/syrup.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:17:11',NULL),(560,9,2,10,'Nucala 100mg','nucala-100mg-9-303','Mepolizumab',139.75,160.71,949,'assets/images/syrup.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:17:11',NULL),(561,11,2,10,'Nucala 100mg','nucala-100mg-11-254','Mepolizumab',396.50,455.98,692,'assets/images/syrup.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:17:11',NULL),(562,12,2,10,'Nucala 100mg','nucala-100mg-12-742','Mepolizumab',365.25,420.04,817,'assets/images/syrup.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:17:11',NULL),(563,6,2,3,'Fasenra 30mg','fasenra-30mg-6-335','Benralizumab',131.00,150.65,280,'assets/images/syrup.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:17:11',NULL),(564,9,2,5,'Fasenra 30mg','fasenra-30mg-9-718','Benralizumab',78.75,90.56,993,'assets/images/syrup.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:17:11',NULL),(565,11,2,9,'Fasenra 30mg','fasenra-30mg-11-518','Benralizumab',241.50,277.73,596,'assets/images/syrup.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:17:11',NULL),(566,12,2,4,'Fasenra 30mg','fasenra-30mg-12-334','Benralizumab',508.25,584.49,974,'assets/images/syrup.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:17:11',NULL),(567,6,2,10,'Tezspire 210mg','tezspire-210mg-6-796','Tezepelumab',230.00,264.50,481,'assets/images/syrup.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:17:11',NULL),(568,9,2,4,'Tezspire 210mg','tezspire-210mg-9-469','Tezepelumab',218.75,251.56,751,'assets/images/syrup.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:17:11',NULL),(569,11,2,9,'Tezspire 210mg','tezspire-210mg-11-372','Tezepelumab',333.50,383.53,398,'assets/images/syrup.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:17:11',NULL),(570,12,2,1,'Tezspire 210mg','tezspire-210mg-12-889','Tezepelumab',155.25,178.54,336,'assets/images/syrup.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:17:11',NULL),(571,6,3,9,'Zepbound 2.5mg','zepbound-2.5mg-6-429','Tirzepatide',319.00,366.85,335,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(572,9,3,10,'Zepbound 2.5mg','zepbound-2.5mg-9-446','Tirzepatide',474.75,545.96,467,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(573,11,3,10,'Zepbound 2.5mg','zepbound-2.5mg-11-152','Tirzepatide',406.50,467.48,119,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(574,12,3,6,'Zepbound 2.5mg','zepbound-2.5mg-12-962','Tirzepatide',398.25,457.99,278,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(575,6,3,9,'Wegovy 0.25mg','wegovy-0.25mg-6-550','Semaglutide',345.00,396.75,882,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(576,9,3,2,'Wegovy 0.25mg','wegovy-0.25mg-9-288','Semaglutide',118.75,136.56,238,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(577,11,3,2,'Wegovy 0.25mg','wegovy-0.25mg-11-384','Semaglutide',489.50,562.93,204,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(578,12,3,9,'Wegovy 0.25mg','wegovy-0.25mg-12-550','Semaglutide',443.25,509.74,417,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(579,6,3,4,'Saxenda 6mg/ml','saxenda-6mg-ml-6-302','Liraglutide',315.00,362.25,861,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(580,9,3,2,'Saxenda 6mg/ml','saxenda-6mg-ml-9-842','Liraglutide',240.75,276.86,891,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(581,11,3,5,'Saxenda 6mg/ml','saxenda-6mg-ml-11-753','Liraglutide',102.50,117.88,975,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(582,12,3,10,'Saxenda 6mg/ml','saxenda-6mg-ml-12-797','Liraglutide',413.25,475.24,480,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(583,6,3,9,'Mounjaro 2.5mg','mounjaro-2.5mg-6-963','Tirzepatide',322.00,370.30,543,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(584,9,3,1,'Mounjaro 2.5mg','mounjaro-2.5mg-9-879','Tirzepatide',373.75,429.81,232,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(585,11,3,6,'Mounjaro 2.5mg','mounjaro-2.5mg-11-827','Tirzepatide',143.50,165.03,948,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL),(586,12,3,8,'Mounjaro 2.5mg','mounjaro-2.5mg-12-445','Tirzepatide',238.25,273.99,54,'assets/images/medicines/tablet_blister.png',0,'approved',NULL,NULL,NULL,NULL,0,'2026-04-26 18:06:04','2026-04-26 18:21:48',NULL);
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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_items`
--

LOCK TABLES `order_items` WRITE;
/*!40000 ALTER TABLE `order_items` DISABLE KEYS */;
INSERT INTO `order_items` VALUES (1,1,1,1,450.00,450.00),(2,1,1,1,450.00,450.00);
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
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  CONSTRAINT `order_status_logs_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_status_logs`
--

LOCK TABLES `order_status_logs` WRITE;
/*!40000 ALTER TABLE `order_status_logs` DISABLE KEYS */;
INSERT INTO `order_status_logs` VALUES (1,1,'placed',NULL,'2026-04-25 21:51:00'),(2,1,'confirmed',NULL,'2026-04-25 21:51:00');
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
  `status` enum('pending','confirmed','processing','dispatched','delivered','cancelled','returned') DEFAULT 'pending',
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
INSERT INTO `orders` VALUES (1,3,2,4,1,NULL,NULL,450.00,0.00,500.00,0,'processing','paid','card',NULL,'2026-04-26 01:43:58',0,NULL,'2026-04-25 21:50:38','2026-04-26 05:13:58',NULL,NULL);
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payments`
--

LOCK TABLES `payments` WRITE;
/*!40000 ALTER TABLE `payments` DISABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permissions`
--

LOCK TABLES `permissions` WRITE;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
INSERT INTO `permissions` VALUES (1,'Manage Users','manage-users','admin','2026-04-25 15:33:48'),(2,'Manage Inventory','manage-inventory','vendor','2026-04-25 15:33:48'),(3,'Fulfill Orders','fulfill-orders','vendor','2026-04-25 15:33:48'),(4,'Dispatch Orders','dispatch-orders','delivery','2026-04-25 15:33:48');
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pos_sale_items`
--

LOCK TABLES `pos_sale_items` WRITE;
/*!40000 ALTER TABLE `pos_sale_items` DISABLE KEYS */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pos_sales`
--

LOCK TABLES `pos_sales` WRITE;
/*!40000 ALTER TABLE `pos_sales` DISABLE KEYS */;
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
  `doctor_name` varchar(100) DEFAULT NULL,
  `image_path` varchar(255) NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `prescriptions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `prescriptions`
--

LOCK TABLES `prescriptions` WRITE;
/*!40000 ALTER TABLE `prescriptions` DISABLE KEYS */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `return_requests`
--

LOCK TABLES `return_requests` WRITE;
/*!40000 ALTER TABLE `return_requests` DISABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `revoked_tokens`
--

LOCK TABLES `revoked_tokens` WRITE;
/*!40000 ALTER TABLE `revoked_tokens` DISABLE KEYS */;
INSERT INTO `revoked_tokens` VALUES (1,'b9d8c0762c90b4da02bbe27a1a4cdb8c4b4e03fa1f8816ea60a1f5baf6a9f3f1','2026-04-27 08:15:16','2026-04-26 08:33:00'),(2,'3653c34815a398308fc1d537920e1ef3ca3f6e453b5e48f88aa7faf1d25e8e44','2026-04-27 13:20:43','2026-04-26 13:39:34'),(3,'8cf52bc96f1b5f83d06ed69213ba889fdda177c52ebda9d7cebb3e40e3263499','2026-04-27 14:03:08','2026-04-26 14:03:23'),(4,'efd7f053abeae3e35596eaff48821601b50e417fb5019643aa14c0e6f9bc0e0e','2026-04-27 14:04:14','2026-04-26 14:04:18'),(5,'c8c1173b445d7ad5fff30f2380d93cb86f2ad6ab8ac626f060a7bbe2b7cc4d07','2026-04-27 14:23:27','2026-04-26 15:03:13'),(6,'8fa7097ce61d46aef7bead4cb19c8856c1a1f5cf4c869c7789e0e55a2ebd106b','2026-04-27 15:54:43','2026-04-26 17:40:22');
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
) ENGINE=InnoDB AUTO_INCREMENT=350 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settings`
--

LOCK TABLES `settings` WRITE;
/*!40000 ALTER TABLE `settings` DISABLE KEYS */;
INSERT INTO `settings` VALUES (1,'site_name','MediMitra',NULL,'2026-04-25 15:33:48'),(2,'emergency_contact','+91 90000 00000',NULL,'2026-04-25 15:33:48'),(3,'free_delivery_threshold','1000.00',NULL,'2026-04-25 15:33:48'),(4,'vendor_commission_percent','10.0',NULL,'2026-04-25 15:33:48'),(5,'two_fa','1',NULL,'2026-04-26 07:51:39'),(6,'force_https','1',NULL,'2026-04-26 07:51:39'),(7,'email_verification','1',NULL,'2026-04-26 07:51:39'),(8,'allow_registration','1',NULL,'2026-04-26 07:51:39'),(9,'maintenance_mode','',NULL,'2026-04-26 07:51:39'),(10,'rate_limit_api','100',NULL,'2026-04-26 07:51:39'),(11,'rate_limit_login','5',NULL,'2026-04-26 07:51:39'),(12,'lockout_duration','30',NULL,'2026-04-26 07:51:39'),(13,'jwt_expiry','24',NULL,'2026-04-26 07:51:39'),(14,'cors_origins',', https://medimitra.in',NULL,'2026-04-26 07:51:39'),(15,'ip_whitelist','',NULL,'2026-04-26 07:51:39'),(16,'debug_mode','',NULL,'2026-04-26 07:51:39'),(17,'audit_admin','1',NULL,'2026-04-26 07:51:39'),(18,'log_failed_logins','1',NULL,'2026-04-26 07:51:39'),(19,'log_retention','90',NULL,'2026-04-26 07:51:39');
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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_profiles`
--

LOCK TABLES `user_profiles` WRITE;
/*!40000 ALTER TABLE `user_profiles` DISABLE KEYS */;
INSERT INTO `user_profiles` VALUES (1,5,NULL,'1990-01-01','male',NULL,NULL,'2026-04-26 13:36:47'),(2,8,NULL,'1990-01-01','male',NULL,NULL,'2026-04-26 13:38:53');
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
  `wallet_balance` decimal(10,2) DEFAULT 0.00,
  `status` enum('active','inactive','suspended') DEFAULT 'active',
  `is_verified` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Admin Super','admin@mediflow.com','$2y$10$y6XccsTsxLCgy3N2WNUgruU7OdMKjIlhaAPjYwr.yFNagRDLnvjRm','admin','9000000001',0.00,'active',0,'2026-04-25 15:33:48','2026-04-26 15:06:02',NULL),(2,'City Medicals','vendor@mediflow.com','$2y$10$y6XccsTsxLCgy3N2WNUgruU7OdMKjIlhaAPjYwr.yFNagRDLnvjRm','vendor','9000000002',0.00,'active',0,'2026-04-25 15:33:48','2026-04-26 15:06:02',NULL),(3,'Patient John','user@mediflow.com','$2y$10$y6XccsTsxLCgy3N2WNUgruU7OdMKjIlhaAPjYwr.yFNagRDLnvjRm','user','9000000003',0.00,'active',0,'2026-04-25 15:33:48','2026-04-26 15:06:02',NULL),(4,'Suresh Delivery','delivery@mediflow.com','$2y$10$y6XccsTsxLCgy3N2WNUgruU7OdMKjIlhaAPjYwr.yFNagRDLnvjRm','delivery','9000000004',0.00,'active',0,'2026-04-25 15:33:48','2026-04-26 15:06:02',NULL),(5,'Test user 160','test_user_7616@medimitra.com','$2y$10$VRjXi7gXUgcufLIFQI1vIur7IbvlA0wUUC659bfLir.VL7J25FWGy','user','9876543210',0.00,'active',0,'2026-04-26 13:36:47','2026-04-26 13:36:47',NULL),(6,'Vendor Test 97','vendor171@test.com','$2y$10$AeBWvy0MJ0JGKFS/MJ6H3ucIF2.T3KtIxnH6jGZlmXlP2Xs.h36gS','vendor',NULL,0.00,'inactive',0,'2026-04-26 13:37:40','2026-04-26 13:37:40',NULL),(7,'Delivery Test 71','delivery544@test.com','$2y$10$HfbF8FOZG35j/fggMz6SGuG5J8Jy644WnOqZb3UJORLXbJuA.ID9O','delivery',NULL,0.00,'inactive',0,'2026-04-26 13:37:52','2026-04-26 13:37:52',NULL),(8,'Test user 336','test_user_8612@medimitra.com','$2y$10$EQDG64tYsz7ZTKMki5Q4N.qdZxKu.OK0v37MHC9QPVElVk5cGPLBO','user','9876543210',0.00,'active',0,'2026-04-26 13:38:53','2026-04-26 13:38:53',NULL),(9,'Test vendor 368','test_vendor_6111@medimitra.com','$2y$10$hKPqO07FkjixMrXzOGVcF.aJs1XhkpxyWpgLE.2phnwdw3SyVgxfK','vendor','9876543210',0.00,'inactive',0,'2026-04-26 13:38:53','2026-04-26 13:38:53',NULL),(10,'Test delivery 507','test_delivery_5318@medimitra.com','$2y$10$OHgWoU.J2azxFvULqXRmWuDUjgjw2CoRsvbI2UQCFFCJZ5a.kn6kq','delivery','9876543210',0.00,'inactive',0,'2026-04-26 13:38:53','2026-04-26 13:38:53',NULL),(11,'Test Vendor','testvendor@example.com','$2y$10$vlP.duNfX6seC.9qp0dfjOK3qOxAb4/iFPyV0mRjpKggfkzpXLuW6','vendor','1234567890',0.00,'inactive',0,'2026-04-26 13:48:30','2026-04-26 13:48:30',NULL),(12,'amul','test2@mediflow.com','$2y$10$oiPjWdwmxdori61lbTQumuuNuajbiYa4SVu7eRmlOZjR85RI.XvTy','vendor','7777777777',0.00,'active',0,'2026-04-26 14:02:56','2026-04-26 14:29:25',NULL);
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vendor_commissions`
--

LOCK TABLES `vendor_commissions` WRITE;
/*!40000 ALTER TABLE `vendor_commissions` DISABLE KEYS */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vendor_payouts`
--

LOCK TABLES `vendor_payouts` WRITE;
/*!40000 ALTER TABLE `vendor_payouts` DISABLE KEYS */;
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
  `pharmacy_name` varchar(255) DEFAULT NULL,
  `license_number` varchar(100) DEFAULT NULL,
  `gst_number` varchar(20) DEFAULT NULL,
  `store_image` varchar(255) DEFAULT NULL,
  `opening_time` time DEFAULT NULL,
  `closing_time` time DEFAULT NULL,
  `address` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `bank_name` varchar(100) DEFAULT NULL,
  `account_number` varchar(50) DEFAULT NULL,
  `ifsc_code` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `vendor_profiles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vendor_profiles`
--

LOCK TABLES `vendor_profiles` WRITE;
/*!40000 ALTER TABLE `vendor_profiles` DISABLE KEYS */;
INSERT INTO `vendor_profiles` VALUES (1,6,'City Health',NULL,NULL,NULL,NULL,NULL,NULL,'2026-04-26 13:37:40','AXIS','998877','AXIS001'),(2,9,'City Pharma','DL-123456','27AAAAA0000A1Z5',NULL,NULL,NULL,'Pune, Maharashtra','2026-04-26 13:38:53','HDFC Bank','1234567890','HDFC0001234'),(3,11,'Test Pharmacy','DL-TEST-123','',NULL,NULL,NULL,'123 Test Street, Test City','2026-04-26 13:48:30','Test Bank','9876543210','TEST0001234'),(4,12,'MediMitra','DL-12545','',NULL,NULL,NULL,'Phaltan','2026-04-26 14:02:56','IDBI','041711101110','IBKL0000468');
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wallet_transactions`
--

LOCK TABLES `wallet_transactions` WRITE;
/*!40000 ALTER TABLE `wallet_transactions` DISABLE KEYS */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wishlists`
--

LOCK TABLES `wishlists` WRITE;
/*!40000 ALTER TABLE `wishlists` DISABLE KEYS */;
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

-- Dump completed on 2026-04-27  8:47:53
