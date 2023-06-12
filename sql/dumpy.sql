-- MySQL dump 10.13  Distrib 8.0.32, for Linux (x86_64)
--
-- Host: localhost    Database: hellenic
-- ------------------------------------------------------
-- Server version	8.0.32-0ubuntu0.20.04.2

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
-- Table structure for table `customers`
--

DROP TABLE IF EXISTS `customers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customers` (
  `id` int NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `forename` varchar(255) NOT NULL COMMENT 'First Name',
  `surname` varchar(255) NOT NULL COMMENT 'Last Name',
  `phone_number_primary` varchar(20) NOT NULL COMMENT 'Primary Phone',
  `phone_number_secondary` varchar(20) DEFAULT NULL COMMENT 'Secondary Phone',
  `email` varchar(255) NOT NULL COMMENT 'Email',
  `customer_type` varchar(255) NOT NULL COMMENT 'Customer Type (Wholesale / Retail)',
  `discount` int DEFAULT NULL COMMENT 'Discount',
  `outstanding_balance` int DEFAULT NULL COMMENT 'Outstanding Balance',
  `last_payment_date` timestamp NULL DEFAULT NULL COMMENT 'Last Payment Date',
  `created_at` timestamp NOT NULL DEFAULT (now()) COMMENT 'Created At',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customers`
--

LOCK TABLES `customers` WRITE;
/*!40000 ALTER TABLE `customers` DISABLE KEYS */;
INSERT INTO `customers` VALUES (1,'Martin','ConDoin','07580634262',NULL,'martincondoin75@gmail.com','Wholesale',10,NULL,NULL,'2023-05-03 22:36:58'),(3,'jack','benning','test',NULL,'test','test',NULL,NULL,NULL,'2023-05-03 23:12:18'),(4,'Kristian','Smedegaard','453346509812',NULL,'@bank','wholesale',NULL,NULL,NULL,'2023-05-03 23:43:20'),(6,'Back','Jenning','1234',NULL,'@bong','retail',NULL,NULL,NULL,'2023-05-06 15:23:32');
/*!40000 ALTER TABLE `customers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `invoices`
--

DROP TABLE IF EXISTS `invoices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `invoices` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `customer_id` int DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `net_value` int DEFAULT NULL,
  `VAT` int DEFAULT NULL,
  `total` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `invoices`
--

LOCK TABLES `invoices` WRITE;
/*!40000 ALTER TABLE `invoices` DISABLE KEYS */;
/*!40000 ALTER TABLE `invoices` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `suppliers`
--

DROP TABLE IF EXISTS `suppliers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `suppliers` (
  `id` int NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `forename` varchar(255) NOT NULL COMMENT 'First Name',
  `surname` varchar(255) NOT NULL COMMENT 'Last Name',
  `phone_number_primary` varchar(20) NOT NULL COMMENT 'Primary Phone',
  `phone_number_secondary` varchar(20) DEFAULT NULL COMMENT 'Secondary Phone',
  `email` varchar(255) NOT NULL COMMENT 'Email',
  `address` varchar(255) NOT NULL COMMENT 'Address',
  `created_at` timestamp NOT NULL DEFAULT (now()) COMMENT 'Created At',
  `updated_at` timestamp NULL DEFAULT (now()) COMMENT 'Updated At',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `suppliers`
--

LOCK TABLES `suppliers` WRITE;
/*!40000 ALTER TABLE `suppliers` DISABLE KEYS */;
/*!40000 ALTER TABLE `suppliers` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2023-05-09  9:56:08
