-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: qat_erp
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
-- Table structure for table `advertisements`
--

DROP TABLE IF EXISTS `advertisements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `advertisements` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_name` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `image_url` text DEFAULT NULL,
  `link_url` text DEFAULT NULL,
  `status` enum('Active','Inactive') DEFAULT 'Active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `media_path` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `advertisements`
--

LOCK TABLES `advertisements` WRITE;
/*!40000 ALTER TABLE `advertisements` DISABLE KEYS */;
/*!40000 ALTER TABLE `advertisements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `customers`
--

DROP TABLE IF EXISTS `customers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `customers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `total_debt` decimal(10,2) DEFAULT 0.00,
  `debt_limit` decimal(10,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_deleted` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customers`
--

LOCK TABLES `customers` WRITE;
/*!40000 ALTER TABLE `customers` DISABLE KEYS */;
INSERT INTO `customers` VALUES (1,'ctest','772166545',4000.00,NULL,'2026-03-12 14:19:16',0),(2,'ctest2','0772166545',8000.00,NULL,'2026-03-12 14:21:36',0),(3,'ltest','10772166545',0.00,NULL,'2026-03-12 14:53:55',0),(4,'بكيل','',4990.00,NULL,'2026-03-17 14:23:32',1),(5,'بكيل','50772166545',8000.00,NULL,'2026-03-17 15:44:29',0),(6,'4بكيل','550772166545',1000.00,NULL,'2026-03-18 11:32:15',0),(7,'ظافر','1772166545',8000.00,NULL,'2026-03-18 14:03:31',0),(8,'1ظافر','11772166545',5000.00,NULL,'2026-03-18 14:09:23',0),(10,'11ظافر','111772166545',10000.00,NULL,'2026-03-19 03:51:04',0),(11,'abood','773469224',10000.00,NULL,'2026-03-19 03:52:46',0);
/*!40000 ALTER TABLE `customers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `expenses`
--

DROP TABLE IF EXISTS `expenses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `expenses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `expense_date` date DEFAULT curdate(),
  `description` varchar(255) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `category` varchar(100) NOT NULL,
  `staff_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `expenses`
--

LOCK TABLES `expenses` WRITE;
/*!40000 ALTER TABLE `expenses` DISABLE KEYS */;
INSERT INTO `expenses` VALUES (1,'2026-03-17','',1000.00,'كهرباء',NULL,'2026-03-17 14:12:26',31);
/*!40000 ALTER TABLE `expenses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `leftovers`
--

DROP TABLE IF EXISTS `leftovers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `leftovers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `source_date` date NOT NULL,
  `purchase_id` int(11) DEFAULT NULL,
  `qat_type_id` int(11) DEFAULT NULL,
  `unit_type` enum('weight','قبضة','قرطاس') NOT NULL DEFAULT 'weight',
  `weight_kg` decimal(10,2) NOT NULL,
  `quantity_units` int(11) NOT NULL DEFAULT 0,
  `status` enum('Pending','Dropped','Transferred_Next_Day','Sold','Auto_Momsi','Auto_Dropped') DEFAULT 'Pending',
  `decision_date` date DEFAULT NULL,
  `sale_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `qat_type_id` (`qat_type_id`),
  CONSTRAINT `leftovers_ibfk_1` FOREIGN KEY (`qat_type_id`) REFERENCES `qat_types` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=67 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `leftovers`
--

LOCK TABLES `leftovers` WRITE;
/*!40000 ALTER TABLE `leftovers` DISABLE KEYS */;
INSERT INTO `leftovers` VALUES (1,'2026-03-12',1,1,'weight',0.25,0,'Dropped','2026-03-12','2026-03-13','2026-03-12 14:53:14'),(2,'2026-03-12',2,3,'قبضة',0.00,85,'Dropped','2026-03-12','2026-03-13','2026-03-12 14:53:14'),(3,'2026-03-12',3,7,'قرطاس',0.00,95,'Dropped','2026-03-12','2026-03-13','2026-03-12 14:53:14'),(4,'2026-03-12',1,1,'weight',0.25,0,'Auto_Dropped','2026-03-12','2026-03-12','2026-03-12 15:06:46'),(5,'2026-03-12',2,3,'قبضة',0.00,85,'Auto_Dropped','2026-03-12','2026-03-12','2026-03-12 15:06:46'),(6,'2026-03-12',3,7,'قرطاس',0.00,95,'Auto_Dropped','2026-03-12','2026-03-12','2026-03-12 15:06:46'),(7,'2026-03-13',4,1,'weight',0.25,0,'Auto_Dropped','2026-03-13','2026-03-13','2026-03-12 15:06:46'),(8,'2026-03-13',5,3,'قبضة',0.00,85,'Auto_Dropped','2026-03-13','2026-03-13','2026-03-12 15:06:46'),(9,'2026-03-13',6,7,'قرطاس',0.00,95,'Auto_Dropped','2026-03-13','2026-03-13','2026-03-12 15:06:46'),(10,'2026-03-12',7,2,'weight',0.25,0,'Dropped','2026-03-12','2026-03-13','2026-03-12 15:08:56'),(11,'2026-03-12',7,2,'weight',0.25,0,'Auto_Dropped','2026-03-12','2026-03-12','2026-03-12 15:09:53'),(12,'2026-03-13',8,2,'weight',0.25,0,'Auto_Dropped','2026-03-13','2026-03-13','2026-03-12 15:09:53'),(13,'2026-03-12',9,4,'قبضة',0.00,100,'Dropped','2026-03-12','2026-03-13','2026-03-12 15:09:53'),(14,'2026-03-12',9,4,'قبضة',0.00,100,'Auto_Dropped','2026-03-12','2026-03-12','2026-03-16 17:19:55'),(15,'2026-03-13',10,4,'قبضة',0.00,76,'Auto_Dropped','2026-03-13','2026-03-13','2026-03-16 17:19:56'),(16,'2026-03-17',12,4,'قبضة',0.00,96,'Dropped','2026-03-17','2026-03-18','2026-03-17 17:29:36'),(17,'2026-03-17',13,7,'قرطاس',0.00,20,'Dropped','2026-03-17','2026-03-18','2026-03-17 17:29:36'),(18,'2026-03-17',12,4,'قبضة',0.00,96,'Auto_Dropped','2026-03-12','2026-03-12','2026-03-17 17:29:36'),(19,'2026-03-17',13,7,'قرطاس',0.00,20,'Auto_Dropped','2026-03-12','2026-03-12','2026-03-17 17:29:36'),(20,'2026-03-18',14,4,'قبضة',0.00,96,'Auto_Dropped','2026-03-12','2026-03-12','2026-03-17 17:29:36'),(21,'2026-03-18',15,7,'قرطاس',0.00,20,'Auto_Dropped','2026-03-12','2026-03-12','2026-03-17 17:29:36'),(22,'2026-03-17',17,4,'قرطاس',0.00,100,'Dropped','2026-03-17','2026-03-18','2026-03-17 17:39:58'),(23,'2026-03-17',18,2,'قبضة',0.00,120,'Dropped','2026-03-17','2026-03-18','2026-03-17 17:39:58'),(24,'2026-03-17',21,7,'weight',0.50,0,'Dropped','2026-03-17','2026-03-18','2026-03-17 17:42:19'),(43,'2026-03-17',17,4,'قرطاس',0.00,100,'Auto_Dropped','2026-03-12','2026-03-12','2026-03-18 11:31:36'),(44,'2026-03-17',18,2,'قبضة',0.00,120,'Auto_Dropped','2026-03-12','2026-03-12','2026-03-18 11:31:36'),(45,'2026-03-17',21,7,'weight',0.50,0,'Auto_Dropped','2026-03-12','2026-03-12','2026-03-18 11:31:36'),(46,'2026-03-18',19,4,'قرطاس',0.00,100,'Auto_Dropped','2026-03-12','2026-03-12','2026-03-18 11:31:36'),(47,'2026-03-18',20,2,'قبضة',0.00,120,'Auto_Dropped','2026-03-12','2026-03-12','2026-03-18 11:31:36'),(48,'2026-03-18',22,7,'weight',0.50,0,'Auto_Dropped','2026-03-12','2026-03-12','2026-03-18 11:31:36'),(49,'2026-03-18',23,1,'weight',0.40,0,'Dropped','2026-03-18','2026-03-19','2026-03-18 11:32:28'),(50,'2026-03-18',23,1,'weight',0.40,0,'Auto_Dropped','2026-03-18','2026-03-18','2026-03-18 11:34:20'),(51,'2026-03-18',24,4,'قرطاس',0.00,5,'Dropped','2026-03-18','2026-03-19','2026-03-18 11:34:20'),(52,'2026-03-18',24,4,'قرطاس',0.00,5,'Auto_Dropped','2026-03-18','2026-03-18','2026-03-18 11:35:02'),(53,'2026-03-18',26,2,'قبضة',0.00,980,'Dropped','2026-03-18','2026-03-19','2026-03-18 14:06:14'),(54,'2026-03-18',27,7,'قرطاس',0.00,94,'Dropped','2026-03-18','2026-03-19','2026-03-18 14:06:14'),(55,'2026-03-18',26,2,'قبضة',0.00,976,'Auto_Dropped','2026-03-18','2026-03-18','2026-03-18 14:09:36'),(56,'2026-03-18',27,7,'قرطاس',0.00,94,'Auto_Dropped','2026-03-18','2026-03-18','2026-03-18 14:09:36'),(57,'2026-03-18',28,4,'weight',0.45,0,'Dropped','2026-03-18','2026-03-19','2026-03-18 14:09:36'),(58,'2026-03-18',28,4,'weight',0.45,0,'Auto_Dropped','2026-03-18','2026-03-18','2026-03-18 14:09:59'),(59,'2026-03-19',29,2,'weight',1.50,0,'Dropped','2026-03-19','2026-03-20','2026-03-19 03:51:27'),(60,'2026-03-19',30,3,'قبضة',0.00,992,'Dropped','2026-03-19','2026-03-20','2026-03-19 03:51:27'),(61,'2026-03-19',31,3,'قرطاس',0.00,100,'Dropped','2026-03-19','2026-03-20','2026-03-19 03:51:27'),(62,'2026-03-19',29,2,'weight',1.50,0,'Auto_Dropped','2026-03-19','2026-03-19','2026-03-19 03:53:27'),(63,'2026-03-19',30,3,'قبضة',0.00,992,'Auto_Dropped','2026-03-19','2026-03-19','2026-03-19 03:53:27'),(64,'2026-03-19',31,3,'قرطاس',0.00,100,'Auto_Dropped','2026-03-19','2026-03-19','2026-03-19 03:53:27'),(65,'2026-03-19',32,1,'weight',0.50,0,'Dropped','2026-03-19','2026-03-20','2026-03-19 03:53:27'),(66,'2026-03-19',32,1,'weight',0.50,0,'Auto_Dropped','2026-03-19','2026-03-19','2026-03-19 03:54:33');
/*!40000 ALTER TABLE `leftovers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `payment_date` date DEFAULT curdate(),
  `customer_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` enum('Cash','Transfer') DEFAULT 'Cash',
  `note` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `customer_id` (`customer_id`),
  CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payments`
--

LOCK TABLES `payments` WRITE;
/*!40000 ALTER TABLE `payments` DISABLE KEYS */;
INSERT INTO `payments` VALUES (1,'2026-03-18',6,10000.00,'Cash','','2026-03-18 14:05:25');
/*!40000 ALTER TABLE `payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `providers`
--

DROP TABLE IF EXISTS `providers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `providers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `providers`
--

LOCK TABLES `providers` WRITE;
/*!40000 ALTER TABLE `providers` DISABLE KEYS */;
INSERT INTO `providers` VALUES (1,'Rtest','772166545','2026-03-12 14:16:10',31),(3,'بكيل1','077216654','2026-03-19 03:45:59',31);
/*!40000 ALTER TABLE `providers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `purchases`
--

DROP TABLE IF EXISTS `purchases`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `purchases` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `purchase_date` date NOT NULL,
  `qat_type_id` int(11) DEFAULT NULL,
  `source_weight_grams` decimal(10,2) DEFAULT 0.00,
  `received_weight_grams` decimal(10,2) DEFAULT 0.00,
  `provider_id` int(11) DEFAULT NULL,
  `expected_quantity_kg` decimal(10,2) DEFAULT 0.00,
  `vendor_name` varchar(100) DEFAULT NULL,
  `agreed_price` decimal(10,2) NOT NULL,
  `price_per_kilo` decimal(10,2) DEFAULT 0.00,
  `unit_type` enum('weight','قبضة','قرطاس') NOT NULL DEFAULT 'weight',
  `source_units` int(11) NOT NULL DEFAULT 0,
  `received_units` int(11) DEFAULT 0,
  `price_per_unit` decimal(10,2) NOT NULL DEFAULT 0.00,
  `discount` decimal(10,2) DEFAULT 0.00,
  `net_cost` decimal(10,2) GENERATED ALWAYS AS (`agreed_price` - `discount`) STORED,
  `quantity_kg` decimal(10,2) DEFAULT NULL,
  `status` enum('Fresh','Momsi','Closed') DEFAULT 'Fresh',
  `media_path` varchar(255) DEFAULT NULL,
  `is_received` tinyint(1) DEFAULT 1,
  `received_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `original_purchase_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `qat_type_id` (`qat_type_id`),
  KEY `idx_purchases_date` (`purchase_date`),
  KEY `fk_original_purchase` (`original_purchase_id`),
  CONSTRAINT `fk_original_purchase` FOREIGN KEY (`original_purchase_id`) REFERENCES `purchases` (`id`) ON DELETE SET NULL,
  CONSTRAINT `purchases_ibfk_1` FOREIGN KEY (`qat_type_id`) REFERENCES `qat_types` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `purchases`
--

LOCK TABLES `purchases` WRITE;
/*!40000 ALTER TABLE `purchases` DISABLE KEYS */;
INSERT INTO `purchases` VALUES (1,'2026-03-12',1,500.00,500.00,1,0.00,NULL,500.00,1000.00,'weight',0,0,0.00,0.00,500.00,0.50,'Closed',NULL,1,'2026-03-12 17:18:19','2026-03-12 14:16:40',31,NULL),(2,'2026-03-12',3,0.00,0.00,1,0.00,NULL,100000.00,0.00,'قبضة',100,100,1000.00,0.00,100000.00,0.00,'Closed',NULL,1,'2026-03-12 17:18:25','2026-03-12 14:17:25',31,NULL),(3,'2026-03-12',7,0.00,0.00,1,0.00,NULL,1000000.00,0.00,'قرطاس',100,100,10000.00,0.00,1000000.00,0.00,'Closed',NULL,1,'2026-03-12 17:18:31','2026-03-12 14:17:47',31,NULL),(4,'2026-03-13',1,0.00,250.00,1,0.00,NULL,0.00,0.00,'weight',0,0,0.00,0.00,0.00,0.25,'Closed',NULL,1,'2026-03-13 00:00:01','2026-03-12 14:53:14',NULL,1),(5,'2026-03-13',3,0.00,0.00,1,0.00,NULL,0.00,0.00,'قبضة',0,85,0.00,0.00,0.00,0.00,'Closed',NULL,1,'2026-03-13 00:00:01','2026-03-12 14:53:14',NULL,2),(6,'2026-03-13',7,0.00,0.00,1,0.00,NULL,0.00,0.00,'قرطاس',0,95,0.00,0.00,0.00,0.00,'Closed',NULL,1,'2026-03-13 00:00:01','2026-03-12 14:53:14',NULL,3),(7,'2026-03-12',2,500.00,500.00,1,0.00,NULL,500.00,1000.00,'weight',0,0,0.00,0.00,500.00,0.50,'Closed',NULL,1,'2026-03-12 18:07:36','2026-03-12 15:07:16',31,NULL),(8,'2026-03-13',2,0.00,250.00,1,0.00,NULL,0.00,0.00,'weight',0,0,0.00,0.00,0.00,0.25,'Closed',NULL,1,'2026-03-13 00:00:01','2026-03-12 15:08:56',NULL,7),(9,'2026-03-12',4,0.00,0.00,1,0.00,NULL,99999999.99,0.00,'قبضة',100,100,1000000.00,0.00,99999999.99,0.00,'Closed',NULL,1,'2026-03-12 18:09:42','2026-03-12 15:09:26',31,NULL),(10,'2026-03-13',4,0.00,0.00,1,0.00,NULL,0.00,0.00,'قبضة',0,100,0.00,0.00,0.00,0.00,'Closed',NULL,1,'2026-03-13 00:00:01','2026-03-12 15:09:53',NULL,9),(11,'2026-03-17',1,500.00,500.00,1,0.00,NULL,500.00,1000.00,'weight',0,0,0.00,0.00,500.00,0.50,'Closed',NULL,1,'2026-03-17 17:23:03','2026-03-17 14:16:00',31,NULL),(12,'2026-03-17',4,0.00,0.00,1,0.00,NULL,100000.00,0.00,'قبضة',100,100,1000.00,0.00,100000.00,0.00,'Closed',NULL,1,'2026-03-17 17:23:06','2026-03-17 14:16:15',31,NULL),(13,'2026-03-17',7,0.00,0.00,1,0.00,NULL,40000.00,0.00,'قرطاس',20,20,2000.00,0.00,40000.00,0.00,'Closed',NULL,1,'2026-03-17 17:23:10','2026-03-17 14:16:39',31,NULL),(14,'2026-03-18',4,0.00,0.00,1,0.00,NULL,0.00,0.00,'قبضة',0,96,0.00,0.00,0.00,0.00,'Closed',NULL,1,'2026-03-18 00:00:01','2026-03-17 17:29:36',NULL,12),(15,'2026-03-18',7,0.00,0.00,1,0.00,NULL,0.00,0.00,'قرطاس',0,20,0.00,0.00,0.00,0.00,'Closed',NULL,1,'2026-03-18 00:00:01','2026-03-17 17:29:36',NULL,13),(16,'2026-03-17',2,500.00,500.00,1,0.00,NULL,500.00,1000.00,'weight',0,0,0.00,0.00,500.00,0.50,'Closed',NULL,1,'2026-03-17 20:39:07','2026-03-17 17:38:13',31,NULL),(17,'2026-03-17',4,0.00,0.00,1,0.00,NULL,100000.00,0.00,'قرطاس',100,100,1000.00,0.00,100000.00,0.00,'Closed',NULL,1,'2026-03-17 20:39:21','2026-03-17 17:38:29',31,NULL),(18,'2026-03-17',2,0.00,0.00,1,0.00,NULL,144000.00,0.00,'قبضة',120,120,1200.00,0.00,144000.00,0.00,'Closed',NULL,1,'2026-03-17 20:39:26','2026-03-17 17:38:46',31,NULL),(19,'2026-03-18',4,0.00,0.00,1,0.00,NULL,0.00,0.00,'قرطاس',0,100,0.00,0.00,0.00,0.00,'Closed',NULL,1,'2026-03-18 00:00:01','2026-03-17 17:39:58',NULL,17),(20,'2026-03-18',2,0.00,0.00,1,0.00,NULL,0.00,0.00,'قبضة',0,120,0.00,0.00,0.00,0.00,'Closed',NULL,1,'2026-03-18 00:00:01','2026-03-17 17:39:58',NULL,18),(21,'2026-03-17',7,500.00,500.00,1,0.00,NULL,500.00,1000.00,'weight',0,0,0.00,0.00,500.00,0.50,'Closed',NULL,1,'2026-03-17 20:42:10','2026-03-17 17:41:46',31,NULL),(22,'2026-03-18',7,0.00,500.00,1,0.00,NULL,0.00,0.00,'weight',0,0,0.00,0.00,0.00,0.50,'Closed',NULL,1,'2026-03-18 00:00:01','2026-03-17 17:42:19',NULL,21),(23,'2026-03-18',1,500.00,500.00,1,0.00,NULL,500.00,1000.00,'weight',0,0,0.00,0.00,500.00,0.50,'Closed',NULL,1,'2026-03-18 03:57:57','2026-03-18 00:57:44',31,NULL),(24,'2026-03-18',4,0.00,0.00,1,0.00,NULL,10000.00,0.00,'قرطاس',10,10,1000.00,0.00,10000.00,0.00,'Closed',NULL,1,'2026-03-18 14:33:55','2026-03-18 11:33:38',31,NULL),(25,'2026-03-18',1,500.00,500.00,1,0.00,NULL,500.00,1000.00,'weight',0,0,0.00,0.00,500.00,0.50,'Closed',NULL,1,'2026-03-18 17:02:59','2026-03-18 14:02:02',31,NULL),(26,'2026-03-18',2,0.00,0.00,1,0.00,NULL,100000.00,0.00,'قبضة',100,1000,1000.00,0.00,100000.00,0.00,'Closed',NULL,1,'2026-03-18 17:03:04','2026-03-18 14:02:27',31,NULL),(27,'2026-03-18',7,0.00,0.00,1,0.00,NULL,1100000.00,0.00,'قرطاس',100,100,11000.00,0.00,1100000.00,0.00,'Closed',NULL,1,'2026-03-18 17:03:08','2026-03-18 14:02:38',31,NULL),(28,'2026-03-18',4,500.00,500.00,1,0.00,NULL,500.00,1000.00,'weight',0,0,0.00,0.00,500.00,0.50,'Closed',NULL,1,'2026-03-18 17:09:08','2026-03-18 14:08:47',31,NULL),(29,'2026-03-19',2,1500.00,1500.00,1,0.00,NULL,1500.00,1000.00,'weight',0,0,0.00,0.00,1500.00,1.50,'Closed',NULL,1,'2026-03-19 06:50:18','2026-03-19 03:44:41',31,NULL),(30,'2026-03-19',3,0.00,0.00,1,0.00,NULL,1000000.00,0.00,'قبضة',1000,1000,1000.00,0.00,1000000.00,0.00,'Closed',NULL,1,'2026-03-19 06:50:23','2026-03-19 03:44:56',31,NULL),(31,'2026-03-19',3,0.00,0.00,1,0.00,NULL,100000.00,0.00,'قرطاس',100,100,1000.00,0.00,100000.00,0.00,'Closed',NULL,1,'2026-03-19 06:50:29','2026-03-19 03:45:10',31,NULL),(32,'2026-03-19',1,1500.00,1500.00,3,0.00,NULL,1500.00,1000.00,'weight',0,0,0.00,0.00,1500.00,1.50,'Closed',NULL,1,'2026-03-19 06:52:23','2026-03-19 03:52:08',31,NULL);
/*!40000 ALTER TABLE `purchases` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `qat_deposits`
--

DROP TABLE IF EXISTS `qat_deposits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `qat_deposits` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `deposit_date` date NOT NULL,
  `currency` enum('YER','SAR','USD') NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `recipient` varchar(100) NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `qat_deposits`
--

LOCK TABLES `qat_deposits` WRITE;
/*!40000 ALTER TABLE `qat_deposits` DISABLE KEYS */;
/*!40000 ALTER TABLE `qat_deposits` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `qat_types`
--

DROP TABLE IF EXISTS `qat_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `qat_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `is_deleted` tinyint(1) DEFAULT 0,
  `media_path` varchar(255) DEFAULT NULL,
  `price_weight` decimal(10,2) DEFAULT 0.00,
  `price_qabdah` decimal(10,2) DEFAULT 0.00,
  `price_qartas` decimal(10,2) DEFAULT 0.00,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `qat_types`
--

LOCK TABLES `qat_types` WRITE;
/*!40000 ALTER TABLE `qat_types` DISABLE KEYS */;
INSERT INTO `qat_types` VALUES (1,'جمام نقوة',NULL,0,NULL,0.00,0.00,0.00),(2,'جمام كالف',NULL,0,'uploads/1771796307_WIN_20250204_08_35_49_Pro.jpg',0.00,0.00,0.00),(3,'جمام سمين',NULL,0,'uploads/1772219687_Screenshot_20251209_155907_ .jpg',0.00,0.00,0.00),(4,'جمام قصار',NULL,0,NULL,0.00,0.00,0.00),(5,'صدور نقوة',NULL,0,NULL,0.00,0.00,0.00),(6,'صدور عادي',NULL,0,NULL,0.00,0.00,0.00),(7,'قطل',NULL,0,NULL,0.00,0.00,0.00);
/*!40000 ALTER TABLE `qat_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `refunds`
--

DROP TABLE IF EXISTS `refunds`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `refunds` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `refund_type` enum('Cash','Debt') NOT NULL,
  `reason` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `customer_id` (`customer_id`),
  CONSTRAINT `refunds_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `refunds`
--

LOCK TABLES `refunds` WRITE;
/*!40000 ALTER TABLE `refunds` DISABLE KEYS */;
INSERT INTO `refunds` VALUES (7,1,1000.00,'Debt','jhj','2026-03-19 03:42:46',32),(8,7,2000.00,'Debt','ل','2026-03-19 03:43:41',32);
/*!40000 ALTER TABLE `refunds` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sales`
--

DROP TABLE IF EXISTS `sales`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sales` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sale_date` date DEFAULT curdate(),
  `due_date` date DEFAULT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `qat_type_id` int(11) DEFAULT NULL,
  `purchase_id` int(11) DEFAULT NULL,
  `leftover_id` int(11) DEFAULT NULL,
  `qat_status` enum('Tari','Momsi','Leftover') DEFAULT 'Tari',
  `weight_grams` decimal(10,2) NOT NULL,
  `weight_kg` decimal(10,3) GENERATED ALWAYS AS (`weight_grams` / 1000) STORED,
  `unit_type` enum('weight','قبضة','قرطاس') NOT NULL DEFAULT 'weight',
  `quantity_units` int(11) NOT NULL DEFAULT 0,
  `price` decimal(10,2) NOT NULL,
  `paid_amount` decimal(10,2) DEFAULT 0.00,
  `discount` decimal(10,2) DEFAULT 0.00,
  `refund_amount` decimal(10,2) DEFAULT 0.00,
  `payment_method` enum('Cash','Debt','Internal Transfer','Kuraimi Deposit','Jayb Deposit') NOT NULL,
  `transfer_sender` varchar(100) DEFAULT NULL,
  `transfer_receiver` varchar(100) DEFAULT NULL,
  `transfer_number` varchar(100) DEFAULT NULL,
  `transfer_company` varchar(100) DEFAULT NULL,
  `is_paid` tinyint(1) DEFAULT 1,
  `debt_type` enum('Daily','Monthly','Yearly','Deferred') DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `customer_id` (`customer_id`),
  KEY `qat_type_id` (`qat_type_id`),
  KEY `idx_sales_date` (`sale_date`),
  KEY `fk_sales_leftover` (`leftover_id`),
  CONSTRAINT `fk_sales_leftover` FOREIGN KEY (`leftover_id`) REFERENCES `leftovers` (`id`),
  CONSTRAINT `sales_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`),
  CONSTRAINT `sales_ibfk_2` FOREIGN KEY (`qat_type_id`) REFERENCES `qat_types` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sales`
--

LOCK TABLES `sales` WRITE;
/*!40000 ALTER TABLE `sales` DISABLE KEYS */;
INSERT INTO `sales` VALUES (1,'2026-03-12','2026-03-12',1,3,2,NULL,'Tari',0.00,0.000,'قبضة',5,10000.00,0.00,0.00,0.00,'Cash',NULL,NULL,NULL,NULL,1,NULL,'','2026-03-12 14:19:23'),(2,'2026-03-12','2026-03-19',2,3,2,NULL,'Tari',0.00,0.000,'قبضة',10,5000.00,0.00,0.00,0.00,'Debt',NULL,NULL,NULL,NULL,0,'Deferred',' [ترحيل آلي من 2026-03-12] [ترحيل آلي من 2026-03-13] [ترحيل آلي من 2026-03-14] [ترحيل آلي من 2026-03-15] [ترحيل آلي من 2026-03-16] [ترحيل آلي من 2026-03-17]','2026-03-12 14:22:15'),(3,'2026-03-12','2026-03-12',1,1,1,NULL,'Tari',250.00,0.250,'weight',0,3000.00,0.00,0.00,0.00,'Internal Transfer','علي','ماجد القادري','45565445','الكريمي',1,NULL,'','2026-03-12 14:50:07'),(4,'2026-03-12','2026-03-12',2,7,3,NULL,'Tari',0.00,0.000,'قرطاس',5,5000.00,0.00,0.00,0.00,'Cash',NULL,NULL,NULL,NULL,1,NULL,'','2026-03-12 14:50:37'),(5,'2026-03-12','2026-03-19',1,2,7,NULL,'Tari',250.00,0.250,'weight',0,5000.00,0.00,0.00,1000.00,'Debt',NULL,NULL,NULL,NULL,0,'Deferred',' [ترحيل آلي من 2026-03-12] [ترحيل آلي من 2026-03-13] [ترحيل آلي من 2026-03-14] [ترحيل آلي من 2026-03-15] [ترحيل آلي من 2026-03-16] [ترحيل آلي من 2026-03-17]','2026-03-12 15:07:45'),(6,'2026-03-12','2026-03-12',3,4,10,NULL,'Momsi',0.00,0.000,'قبضة',4,5000.00,0.00,0.00,0.00,'Cash',NULL,NULL,NULL,NULL,1,NULL,'','2026-03-12 15:10:03'),(7,'2026-03-12','2026-03-12',2,4,10,NULL,'Momsi',0.00,0.000,'قبضة',20,20000.00,0.00,0.00,0.00,'Cash',NULL,NULL,NULL,NULL,1,NULL,'','2026-03-12 15:10:40'),(8,'2026-03-17','2026-03-19',4,1,11,NULL,'Tari',500.00,0.500,'weight',0,5000.00,0.00,0.00,10.00,'Debt',NULL,NULL,NULL,NULL,0,'Deferred',' [ترحيل آلي من 2026-03-17]','2026-03-17 14:23:41'),(9,'2026-03-17','2026-03-19',5,4,12,NULL,'Tari',0.00,0.000,'قبضة',4,3000.00,0.00,0.00,0.00,'Debt',NULL,NULL,NULL,NULL,0,'Deferred',' [ترحيل آلي من 2026-03-17]','2026-03-17 15:45:04'),(10,'2026-03-17','2026-03-19',5,2,16,NULL,'Tari',500.00,0.500,'weight',0,5000.00,0.00,0.00,0.00,'Debt',NULL,NULL,NULL,NULL,0,'Deferred',' [ترحيل آلي من 2026-03-17]','2026-03-17 17:39:39'),(11,'2026-03-18','2026-03-19',2,1,23,NULL,'Tari',50.00,0.050,'weight',0,3000.00,0.00,0.00,0.00,'Debt',NULL,NULL,NULL,NULL,0,'Deferred','','2026-03-18 00:58:11'),(12,'2026-03-18','2026-03-19',6,1,23,NULL,'Tari',50.00,0.050,'weight',0,1000.00,1000.00,0.00,0.00,'Debt',NULL,NULL,NULL,NULL,1,'Deferred','','2026-03-18 11:32:20'),(13,'2026-03-18','2026-03-19',6,4,24,NULL,'Tari',0.00,0.000,'قرطاس',5,10000.00,9000.00,0.00,0.00,'Debt',NULL,NULL,NULL,NULL,0,'Deferred','','2026-03-18 11:34:15'),(14,'2026-03-18','2026-03-19',7,7,27,NULL,'Tari',0.00,0.000,'قرطاس',6,10000.00,0.00,0.00,2000.00,'Debt',NULL,NULL,NULL,NULL,0,'Deferred','','2026-03-18 14:03:35'),(15,'2026-03-18','2026-03-18',1,1,25,NULL,'Tari',500.00,0.500,'weight',0,10000.00,0.00,0.00,0.00,'Internal Transfer','علي','ماجد القادري','123456','الكريمي',1,NULL,'','2026-03-18 14:03:49'),(16,'2026-03-18','2026-03-18',3,2,26,NULL,'Tari',0.00,0.000,'قبضة',20,500000.00,0.00,0.00,0.00,'Cash',NULL,NULL,NULL,NULL,1,NULL,'','2026-03-18 14:04:10'),(17,'2026-03-18','2026-03-18',1,2,NULL,53,'Leftover',0.00,0.000,'قبضة',4,5000.00,0.00,0.00,0.00,'Cash',NULL,NULL,NULL,NULL,1,NULL,'','2026-03-18 14:06:33'),(18,'2026-03-18','2026-03-19',8,4,28,NULL,'Tari',50.00,0.050,'weight',0,5000.00,0.00,0.00,0.00,'Debt',NULL,NULL,NULL,NULL,0,'Deferred','','2026-03-18 14:09:27'),(19,'2026-03-19','2026-03-20',10,3,30,NULL,'Tari',0.00,0.000,'قبضة',8,10000.00,0.00,0.00,0.00,'Debt',NULL,NULL,NULL,NULL,0,'Deferred','','2026-03-19 03:51:11'),(20,'2026-03-19','2026-03-20',11,1,32,NULL,'Tari',1000.00,1.000,'weight',0,10000.00,0.00,0.00,0.00,'Debt',NULL,NULL,NULL,NULL,0,'Deferred','','2026-03-19 03:53:20');
/*!40000 ALTER TABLE `sales` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `staff`
--

DROP TABLE IF EXISTS `staff`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `staff` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `role` varchar(50) DEFAULT NULL,
  `daily_salary` decimal(10,2) DEFAULT 0.00,
  `withdrawal_limit` decimal(10,2) DEFAULT NULL,
  `base_salary` decimal(10,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `staff`
--

LOCK TABLES `staff` WRITE;
/*!40000 ALTER TABLE `staff` DISABLE KEYS */;
INSERT INTO `staff` VALUES (1,'قصي','sales',5000.00,NULL,0.00,'2026-03-17 14:12:56',31),(2,'abood ','none',10000.00,NULL,0.00,'2026-03-19 04:17:20',32);
/*!40000 ALTER TABLE `staff` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `unknown_transfers`
--

DROP TABLE IF EXISTS `unknown_transfers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `unknown_transfers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `transfer_date` date NOT NULL,
  `receipt_number` varchar(100) DEFAULT NULL,
  `sender_name` varchar(100) NOT NULL,
  `amount` decimal(10,2) DEFAULT 0.00,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `currency` varchar(5) DEFAULT 'YER',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `unknown_transfers`
--

LOCK TABLES `unknown_transfers` WRITE;
/*!40000 ALTER TABLE `unknown_transfers` DISABLE KEYS */;
/*!40000 ALTER TABLE `unknown_transfers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `display_name` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','super_admin','user') NOT NULL,
  `sub_role` varchar(50) DEFAULT 'full',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'super admin','null','null','123456','super_admin','full','2026-01-19 19:20:42'),(3,'admin1','abdulqawi mohammed','0772166545','$2y$10$nXfGuul5Yo08xojUUJvLWOpfz6wf/kIqqe1m0haDVUko68f90jo3e','user','full','2026-02-21 20:52:31'),(4,'abood','عبد القوي','7721665','$2y$10$jQXWWPSW7HtQZqVT4Z9dnO6kajO0xLODBOv2LGgJ3i1ZJiWPnWbmy','user','full','2026-02-22 21:31:51'),(5,'Abdul',NULL,NULL,'$2y$10$QWHZUCDODeXxdsh.ydGcIeSUBatiqlEw8BL37XjKhWcCUpaOtcVz2','super_admin','reports','2026-02-23 11:14:39'),(6,'Mohammed',NULL,NULL,'$2y$10$8htdjtFEYV9Ryopnbn9L9u5XI1m7MGitzJG8TYUnBWO772166545','super_admin','full','2026-02-23 11:14:39'),(7,'Abdullah',NULL,NULL,'$2y$10$4.8UPgvvvzK1rPXuBsLSxO07K1d8c9ZFcBFLByMiE2Q/1hZqC9XEy','super_admin','sales_debts','2026-02-23 11:14:39'),(8,'Aham',NULL,NULL,'$2y$10$LQC/gZsvzoLh.uoygVIod.j4X1gdKNmx9YQghJhyF8K9uLBXc4RlG','super_admin','receiving','2026-02-23 11:14:39'),(9,'202310400240','عبد القوي','admin','$2y$10$6y9FA.fHNOOW67yxfDJqrOWqbv5Zsn.N5NwUea386tpm4hddFXs7.','user','full','2026-02-27 19:31:06'),(10,'2023104002','عبد القوي','admin','$2y$10$Eo9GytjYblfsobZBHNH8venN/tTOK31WrWPq0I0hraHgYW8QfUgvG','user','full','2026-02-27 19:32:52'),(11,'ali','عبد القوي','admin','$2y$10$KqRzLNxGspTw/63AsyNCl.lOKGGa79gHwPnLp9RSkTeoABAwpC2mO','user','full','2026-02-27 19:33:54'),(12,'ad','عبد القوي','test','$2y$10$MsoGM/z62ATebE2YGKCmg.BRu3QKVRK3/pv9iV6DGgUC4hC0eVhJq','user','full','2026-03-02 10:48:29'),(14,'four',NULL,NULL,'$2y$10$H/cgwxYUh2P3yOH36aAdH.WzTzVZBawG7RHdjDJ8Hn0rpHaK7xDAC','user','full','2026-03-04 14:10:45'),(15,'three',NULL,NULL,'$2y$10$H/cgwxYUh2P3yOH36aAdH.WzTzVZBawG7RHdjDJ8Hn0rpHaK7xDAC','user','full','2026-03-04 14:10:45'),(16,'two',NULL,NULL,'$2y$10$H/cgwxYUh2P3yOH36aAdH.WzTzVZBawG7RHdjDJ8Hn0rpHaK7xDAC','user','full','2026-03-04 14:10:45'),(17,'one',NULL,NULL,'$2y$10$H/cgwxYUh2P3yOH36aAdH.WzTzVZBawG7RHdjDJ8Hn0rpHaK7xDAC','user','full','2026-03-04 14:10:45'),(19,'test_sourcing_admin',NULL,NULL,'$2y$10$lzAb7mowXYO64YLBj4Uk9ODIeyR.hHmSByj6jp2k6oKZDQJEZhvUm','admin','full','2026-03-07 09:11:03'),(21,'super','عبد القوي','0772166545','$2y$10$zWqyjEI/1VOvs88GCR7Q7u0WeSzk733CCzWqSdg1MTBB1VtPp5sue','super_admin','verifier','2026-03-07 09:22:29'),(22,'sales','عبد القوي','0772166545','$2y$10$8htdjtFEYV9Ryopnbn9L9u5XI1m7MGitzJG8TYUnBWOuW5OBi5pZS','super_admin','seller','2026-03-07 09:25:00'),(23,'accountant','عبد القوي','0772166545','$2y$10$tqEmBeZU.0HPglSELrd2pecXVjZea6/1rwzP7z.YuHDPifSLhojaa','super_admin','accountant','2026-03-07 09:32:27'),(24,'partner','عبد القوي','0772166545','$2y$10$fwTIGz3oVCcpWloStSEsxO5R5B6trKGCorx03jnxwhjOcHXDTH.8m','super_admin','partner','2026-03-07 09:37:07'),(25,'test','test','772166545','$2y$10$6sBEYOlEmwgO/zH91dfFveoY6O6fjK4xiRM1D1IQdsT7shlZ7r3Ym','super_admin','partner','2026-03-07 09:41:51'),(26,'moha','mohammed','772166545','$2y$10$mMPFKu.yGPxa2OADERU1relcz/RXESHbFSuvPvGK2ditEcaDQ.n2e','super_admin','full','2026-03-07 13:49:04'),(27,'',NULL,NULL,'','super_admin','full','2026-03-08 12:17:33'),(31,'admin','admin',NULL,'$2y$10$Q6eryJl/c7NpXLr5dey9j.6s2vCWCw9.5hc6zZZLEV9UMKdWg.1Wa','admin','full','2026-03-08 19:21:26'),(32,'superadmin','superadmin',NULL,'$2y$10$VmbDnpsvfPT5iF2WcVg2jexJn/5Ub7sxKqG70QExxaHXlbNtOJqk2','super_admin','full','2026-03-08 19:21:26'),(33,'est','test','0772166545','$2y$10$nlu2UNdumm8JbdSZM.zdMuljC2nhzGdxHAL.k2J9jjHTy2TlpnJzm','super_admin','verifier','2026-03-09 17:08:27');
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

-- Dump completed on 2026-03-18 21:21:00
