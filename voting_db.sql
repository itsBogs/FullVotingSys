-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: 127.0.0.1    Database: voting_db
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
-- Current Database: `voting_db`
--

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `voting_db` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;

USE `voting_db`;

--
-- Table structure for table `admins`
--

DROP TABLE IF EXISTS `admins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admins` (
  `admin_ID` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `status` enum('active','archived') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`admin_ID`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admins`
--

LOCK TABLES `admins` WRITE;
/*!40000 ALTER TABLE `admins` DISABLE KEYS */;
INSERT INTO `admins` VALUES (2,'admin123','$2y$10$Y3rGwaKw9IhLZgfPq9Urz.X3tfDdX4v4Uw3Pxk8VAC/6cyL7Bh0Re','active','2025-03-29 02:30:11'),(3,'Haruto1234','$2y$10$4OYF0L2zTLPiIi2/UnjmgO.sIa0WryijgpfLsMEIBD/0wqyugXO16','active','2025-04-07 05:19:29'),(4,'batch2','$2y$10$AMLRHf05uG6ZQe7ZgDMVL.neuJuIClWNZbiZ0DHu9yTkmyYbeWS/W','active','2025-04-24 02:58:02');
/*!40000 ALTER TABLE `admins` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `batch`
--

DROP TABLE IF EXISTS `batch`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `batch` (
  `batchID` int(11) NOT NULL AUTO_INCREMENT,
  `batch_number` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`batchID`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `batch`
--

LOCK TABLES `batch` WRITE;
/*!40000 ALTER TABLE `batch` DISABLE KEYS */;
INSERT INTO `batch` VALUES (1,1,'2025-04-24 14:03:49');
/*!40000 ALTER TABLE `batch` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `batch_history`
--

DROP TABLE IF EXISTS `batch_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `batch_history` (
  `batch_historyID` int(11) NOT NULL AUTO_INCREMENT,
  `batch` varchar(255) NOT NULL,
  `election_date` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`batch_historyID`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `batch_history`
--

LOCK TABLES `batch_history` WRITE;
/*!40000 ALTER TABLE `batch_history` DISABLE KEYS */;
INSERT INTO `batch_history` VALUES (1,'2025','2025-04-25 04:38:58'),(2,'2026','2025-04-25 04:38:58'),(3,'2027','2025-04-25 04:50:36'),(4,'2028','2025-05-20 11:06:22'),(15,'2029','2025-05-22 17:38:59');
/*!40000 ALTER TABLE `batch_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `candidate_history`
--

DROP TABLE IF EXISTS `candidate_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `candidate_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `candidate_id` int(11) DEFAULT NULL,
  `position` varchar(255) DEFAULT NULL,
  `first_name` varchar(255) DEFAULT NULL,
  `middle_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `batch` int(11) DEFAULT NULL,
  `date_added` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `candidate_history`
--

LOCK TABLES `candidate_history` WRITE;
/*!40000 ALTER TABLE `candidate_history` DISABLE KEYS */;
/*!40000 ALTER TABLE `candidate_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `candidates`
--

DROP TABLE IF EXISTS `candidates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `candidates` (
  `candidateID` int(11) NOT NULL AUTO_INCREMENT,
  `last_name` varchar(255) DEFAULT NULL,
  `first_name` varchar(255) DEFAULT NULL,
  `middle_name` varchar(255) DEFAULT NULL,
  `position` varchar(50) NOT NULL,
  `grade` int(20) NOT NULL,
  `section` varchar(50) NOT NULL,
  `lrn` varchar(12) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `status` enum('active','archived') NOT NULL DEFAULT 'active',
  `general_average` float NOT NULL DEFAULT 0,
  `archived` tinyint(1) DEFAULT 0,
  `batch` int(11) DEFAULT 1,
  PRIMARY KEY (`candidateID`),
  UNIQUE KEY `lrn` (`lrn`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `candidates`
--

LOCK TABLES `candidates` WRITE;
/*!40000 ALTER TABLE `candidates` DISABLE KEYS */;
INSERT INTO `candidates` VALUES (1,'Arenas','Mark Lhemuel','Galang','President',11,'Nickel','004703925926','user.jpg','active',88,0,2025),(2,'Arenas','Mike Dominic','Galang','President',10,'H','742622485144','user.jpg','active',90,0,2025),(3,'Arenas','Michelle','Galang','Vice President',9,'A','699000678676','user.jpg','active',89,0,2025),(4,'Melican','Jimboy','Menor','Secretary',8,'B','267135968680','user.jpg','active',91,0,2025),(5,'Garcia','Emmanuel','Galpao','Auditor',9,'B','238677838108','user.jpg','active',87,0,2025),(6,'Arenas','Manuel','Gumaro','President',7,'B','391981315232','user.jpg','active',90,0,2025),(7,'Javier','Harold','Johna','President',10,'H','243873092573','user.jpg','active',88,0,2025),(8,'Brown','Lyns','Valerio','President',7,'A','043421547900','user.jpg','active',90,0,2026),(9,'Wallmart','Harold','Galang','P.I.O.',7,'B','485488492516','user.jpg','active',88,0,2025),(10,'Baustista','John','Valerio','Treasurer',7,'A','297177849614','user.jpg','active',91,0,2025),(11,'Baustista','Grace','Valerio','Treasurer',7,'A','029423801255','user.jpg','active',91,0,2026),(12,'Wallmart','Mark','Valerio','G9 representative',8,'A','255585488169','user.jpg','active',90,0,2026),(14,'Arenas','Mark Lhemuel','Galang','President',12,'STEM-B','189656067816','user.jpg','active',91,0,2027),(15,'Arenas','Mike Dominic','Galang','President',11,'STEM-A','123456789021','user.jpg','active',90,0,2027),(17,'Valerio','Josephine','Yolando','President',8,'Granite','231233252534','user.jpg','active',91,0,2026);
/*!40000 ALTER TABLE `candidates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `current_batch`
--

DROP TABLE IF EXISTS `current_batch`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `current_batch` (
  `current_batchID` int(11) NOT NULL DEFAULT 1,
  `batch_number` int(11) NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`current_batchID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `current_batch`
--

LOCK TABLES `current_batch` WRITE;
/*!40000 ALTER TABLE `current_batch` DISABLE KEYS */;
INSERT INTO `current_batch` VALUES (1,2025,'2025-05-22 16:29:30');
/*!40000 ALTER TABLE `current_batch` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sections`
--

DROP TABLE IF EXISTS `sections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sections` (
  `sectionID` int(11) NOT NULL AUTO_INCREMENT,
  `grade` varchar(10) NOT NULL,
  `section` varchar(10) NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'active',
  `batch` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`sectionID`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sections`
--

LOCK TABLES `sections` WRITE;
/*!40000 ALTER TABLE `sections` DISABLE KEYS */;
INSERT INTO `sections` VALUES (1,'7','A','active','1'),(2,'7','B','active','1'),(3,'7','C','active','1'),(4,'8','D','active','1'),(5,'8','E','active','1'),(6,'8','F','active','1'),(7,'9','G','active','1'),(8,'9','H','active','1'),(9,'9','I','active','1'),(10,'10','J','active','1'),(11,'10','K','active','1'),(12,'10','L','active','1'),(13,'11','STEM-A','active','1'),(14,'11','ABM-A','active','1'),(15,'12','STEM-B','active','1'),(16,'12','ABM-B','active','1'),(17,'8','Granite','active','1'),(18,'8','A','active','1'),(19,'10','Glowin','archived','1'),(20,'7','Glock','active','1'),(21,'7','F','active','1'),(22,'8','Cloud','active','1'),(23,'8','A','active','1'),(24,'11','Animation','active','1'),(25,'8','Aa','active','1');
/*!40000 ALTER TABLE `sections` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `voting_status` enum('open','closed') NOT NULL DEFAULT 'closed',
  `setting_name` varchar(50) DEFAULT NULL,
  `setting_value` varchar(50) DEFAULT NULL,
  `key_name` varchar(50) DEFAULT NULL,
  `value` varchar(50) DEFAULT NULL,
  `setting_key` varchar(255) DEFAULT NULL,
  `current_batch` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settings`
--

LOCK TABLES `settings` WRITE;
/*!40000 ALTER TABLE `settings` DISABLE KEYS */;
INSERT INTO `settings` VALUES (1,'open',NULL,NULL,NULL,NULL,NULL,1),(2,'closed','current_batch','2025',NULL,NULL,NULL,NULL),(3,'closed',NULL,NULL,'current_batch','2025',NULL,NULL);
/*!40000 ALTER TABLE `settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `student_history`
--

DROP TABLE IF EXISTS `student_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `student_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) DEFAULT NULL,
  `first_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `lrn` varchar(255) DEFAULT NULL,
  `grade` varchar(255) DEFAULT NULL,
  `section` varchar(255) DEFAULT NULL,
  `voted` tinyint(1) DEFAULT NULL,
  `batch` int(11) DEFAULT NULL,
  `date_added` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `student_history`
--

LOCK TABLES `student_history` WRITE;
/*!40000 ALTER TABLE `student_history` DISABLE KEYS */;
/*!40000 ALTER TABLE `student_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `students`
--

DROP TABLE IF EXISTS `students`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `students` (
  `student_ID` int(11) NOT NULL AUTO_INCREMENT,
  `last_name` varchar(255) DEFAULT NULL,
  `first_name` varchar(255) DEFAULT NULL,
  `middle_name` varchar(255) DEFAULT NULL,
  `grade` int(10) NOT NULL,
  `section` varchar(50) NOT NULL,
  `lrn` varchar(20) NOT NULL,
  `status` enum('active','archived') DEFAULT 'active',
  `voted` tinyint(1) DEFAULT 0,
  `batch` int(11) DEFAULT NULL,
  PRIMARY KEY (`student_ID`),
  UNIQUE KEY `unique_student` (`lrn`,`batch`),
  UNIQUE KEY `unique_lrn_batch` (`lrn`,`batch`),
  UNIQUE KEY `lrn` (`lrn`,`batch`)
) ENGINE=InnoDB AUTO_INCREMENT=59 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `students`
--

LOCK TABLES `students` WRITE;
/*!40000 ALTER TABLE `students` DISABLE KEYS */;
INSERT INTO `students` VALUES (1,'Smith','John','Michael',7,'A','123456789101','active',1,2025),(2,'Johnson','Emily','Rose',7,'A','123456789102','active',1,2025),(3,'Williams','Michael','David',8,'A','123456789103','active',1,2025),(4,'Jones','Sarah','Marie',9,'A','123456789104','active',1,2025),(5,'Brown','David','Lucas',7,'A','123456789105','active',1,2025),(6,'Davis','Sophia','Grace',7,'A','123456789106','active',1,2025),(7,'Miller','James','Thomas',8,'A','123456789181','active',1,2025),(8,'Wilson','Olivia','Mae',7,'B','123456789110','active',1,2025),(9,'Moore','Liam','William',11,'B','123456789112','active',1,2025),(10,'Taylor','Ava','Elizabeth',10,'Sapphire','123456789012','active',0,2025),(11,'Anderson','Benjamin','Joseph',9,'Ruby','234567890123','active',0,2025),(12,'Thomas','Charlotte','Anna',11,'Emerald','345678901234','active',0,2025),(13,'Jackson','Ethan','Henry',10,'Sapphire','123456789099','active',0,2025),(14,'White','Amelia','Grace',9,'A','123456789098','active',0,2025),(16,'Williams','Jack','Diones',10,'D','456789012345','active',0,2025),(17,'Brown','Jill','Evans',11,'E','567890123456','active',0,2025),(18,'Jones','Jake','Franks',12,'F','678901234567','active',0,2025),(19,'Garcia','Jackie','Garcia',7,'A','789012345678','active',0,2025),(20,'Martinez','Julio','Hernandez',8,'B','890123456789','active',0,2025),(21,'Hernandez','Helen','Inkao',9,'C','901234567890','active',0,2025),(22,'Lopez','Leo','Johnson',10,'D','123456789013','active',0,2025),(23,'Gonzalez','Gina','King',11,'E','234567890124','active',0,2025),(24,'Wilson','Wendy','Lopez',12,'F','345678901235','active',0,2025),(25,'Anderson','Albert','Martinez',8,'E','456789012346','active',1,2025),(26,'Thomas','Tina','Nguyen',8,'B','567890123457','active',0,2025),(27,'Javier','Jensen','Johna',7,'A','123456789111','active',0,2025),(28,'Baustista','Jensen','Johna',9,'H','123456711112','active',0,2025),(29,'Tatsuki','Mark Lhemuel','Galang',7,'A','111111111111','active',0,2025),(30,'Baustista','Harold','Javier',10,'K','212212112121','active',1,2025),(31,'Wallmart','Harold','Johna',8,'D','313131313132','active',0,2026),(32,'Wallmart','Lyns','Javier',7,'A','313131313131','active',0,2026),(34,'Wilson','Wendy','Ligua',7,'A','345678901235','active',0,2027),(35,'Doe','John','Anderson',7,'A','123456789012','active',0,2027),(36,'Smith','Jane','B',8,'B','234567890123','active',0,2027),(37,'Johnson','Jim','C',9,'C','345678901234','active',0,2027),(38,'Williams','Jack','Davis',10,'D','456789012345','active',0,2027),(39,'Brown','Jill','Evans',11,'E','567890123456','active',0,2027),(40,'Jones','Jake','F',12,'F','678901234567','active',0,2027),(41,'Garcia','Jackie','Garcia',7,'A','789012345678','active',0,2027),(42,'Martinez','Julio','Hernandez',8,'B','890123456789','active',0,2027),(43,'Hernandez','Helen','I',9,'C','901234567890','active',0,2027),(44,'Lopez','Leo','Johnson',10,'D','123456789013','active',0,2027),(45,'Gonzalez','Gina','King',11,'E','234567890124','active',0,2027),(46,'Anderson','Albert','Martinez',7,'A','456789012346','active',0,2027),(47,'Thomas','Tina','Nguyen',8,'B','567890123457','active',0,2027),(48,'Garcia','Juan','Cruz',10,'A','123456789012','active',0,2026),(49,'Dela Cruz','Ana','Maria',10,'Glowing','123456789013','active',0,2026),(50,'Reyes','Mark','Antonio',11,'C','123456789014','active',0,2026),(51,'Santos','Liza','Valentina',10,'A','123456789015','active',0,2026),(52,'Torres','David','Emmanuel',8,'D','123456789016','active',0,2026),(53,'Lopez','Karen','Beatriz',9,'C','123456789017','active',0,2026),(54,'Ramos','Carlos','Ignacio',12,'B','123456789018','active',0,2026),(55,'Gonzales','Mary','Francesca',11,'A','123456789019','active',0,2026),(56,'Navarro','Jake','Alexander',10,'B','123456789020','active',0,2026),(57,'Mendoza','Sophia','Camille',9,'D','123456789021','active',0,2026),(58,'Maswa','Jarit','Yamada',11,'ABM-A','232332111121','active',0,2026);
/*!40000 ALTER TABLE `students` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `super_admins`
--

DROP TABLE IF EXISTS `super_admins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `super_admins` (
  `super_adminID` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  PRIMARY KEY (`super_adminID`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `super_admins`
--

LOCK TABLES `super_admins` WRITE;
/*!40000 ALTER TABLE `super_admins` DISABLE KEYS */;
INSERT INTO `super_admins` VALUES (1,'superadmin123','$2y$10$zPQIBbNhkzu7fyfkNyD0iOlUlLELSnIPL8MiDQfRS.Stj7/kBvgGe','systemssgvoting@gmail.com');
/*!40000 ALTER TABLE `super_admins` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `transaction_history`
--

DROP TABLE IF EXISTS `transaction_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `transaction_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_username` varchar(255) DEFAULT NULL,
  `action` text DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('active','archived') DEFAULT 'active',
  `batch` int(11) DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=120 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `transaction_history`
--

LOCK TABLES `transaction_history` WRITE;
/*!40000 ALTER TABLE `transaction_history` DISABLE KEYS */;
INSERT INTO `transaction_history` VALUES (8,'admin123','Added candidate: Haruto Hensuki (Position: Auditor, Grade: 9, Section: B, Average: 87)','2025-03-31 06:12:41','active',1),(9,'admin123','Archived student: Jackie (Grade: 7 - Section: A)','2025-03-31 06:14:25','active',1),(10,'admin123','Restored student: Jackie (Grade: 7 - Section: A)','2025-03-31 06:14:42','active',1),(11,'admin123','Restored candidate: Mike Dominic Arenas (Vice President)','2025-03-31 06:51:54','active',1),(12,'admin123','Archived candidate: Mike Dominic Arenas (Vice President)','2025-03-31 06:53:09','active',1),(13,'admin123','Restored candidate: Mike Dominic Arenas (Vice President)','2025-03-31 06:53:19','active',1),(14,'admin123','Archived candidate: Mark Lhemuel Arenas (President)','2025-03-31 06:53:24','active',1),(15,'admin123','Added student: Hiro kun (Grade: 7 - Section: A, LRN: 123456789105)','2025-03-31 06:53:58','active',1),(16,'admin123','Added student: Tatsuki (Grade: 7 - Section: A, LRN: 123456789106)','2025-03-31 07:14:36','active',1),(17,'admin123','Archived student: Tatsuki (Grade: 7 - Section: A)','2025-03-31 07:14:46','active',1),(18,'admin123','Restored candidate: Mark Lhemuel Arenas (President)','2025-04-06 14:03:22','active',1),(19,'admin123','Restored student: Tatsuki (Grade: 7 - Section: A)','2025-04-06 14:04:10','active',1),(20,'admin123','Added student: Hiro kun (Grade: 8 - Section: A, LRN: 123456789181)','2025-04-06 14:20:36','active',1),(21,'admin123','Edited student: Hiro kun (LRN: 123456789105) → Hiro kun (LRN: 123456789105)','2025-04-06 14:25:32','active',1),(22,'admin123','Edited student: Hiro kun (LRN: 123456789105) → Hiro Bautista (LRN: 123456789105)','2025-04-06 14:26:06','active',1),(23,'admin123','Edited student: Jackie (LRN: 123456789101) → Jackie Chans (LRN: 123456789101)','2025-04-06 14:26:29','active',1),(24,'admin123','Edited student: Mark  (LRN: 123456789102) → Mark Ryan Quintos (LRN: 123456789102)','2025-04-06 14:26:59','active',1),(25,'admin123','Edited student: Tatsuki (LRN: 123456789106) → Natsuki Subaro (LRN: 123456789106)','2025-04-06 14:27:13','active',1),(26,'admin123','Edited student: Matthew (LRN: 123456789181) → Matthew Tamondong (LRN: 123456789181)','2025-04-06 14:27:48','active',1),(27,'admin123','Edited student: Mc Lester (LRN: 123456789103) → Mc Lester Soriano (LRN: 123456789103)','2025-04-06 14:27:57','active',1),(28,'admin123','Edited student: Mark Rejie (LRN: 123456789104) → Mark Rejie Rosario (LRN: 123456789104)','2025-04-06 14:28:05','active',1),(29,'admin123','Added new admin: Haruto1234','2025-04-07 05:19:29','active',1),(30,'Haruto1234','Archived candidate: Haruto Hensuki (Auditor)','2025-04-07 05:20:15','active',1),(31,'Haruto1234','Restored candidate: Haruto Hensuki (Auditor)','2025-04-07 05:20:20','active',1),(32,'Haruto1234','Added student: Fubuki Jetsu (Grade: 7 - Section: B, LRN: 123456789110)','2025-04-10 08:25:02','active',1),(33,'Haruto1234','Unarchived admin with ID: 2','2025-04-10 09:27:25','active',1),(34,'Haruto1234','Unarchived admin with ID: 2','2025-04-10 09:27:31','active',1),(35,'Haruto1234','Added student: Zenitsu Kamado (Grade: 11 - Section: B, LRN: 123456789112)','2025-04-10 09:47:36','active',1),(36,'admin123','Added new admin: batch2','2025-04-24 02:58:02','active',1),(37,'batch2','Added candidate: Haruto Agudo (Position: President, Grade: 7, Section: B, Average: 90)','2025-04-24 03:18:56','active',1),(38,'admin123','Added student: Javier, Jensen Johna (Grade: 7 - Section: A, LRN: 123456789111)','2025-04-24 05:02:39','active',1),(39,'admin123','Archived student: Anderson, Albert M (Grade: 7 - Section: A)','2025-04-24 05:06:40','active',1),(40,'admin123','Archived student: Wilson, Wendy L (Grade: 12 - Section: F)','2025-04-24 05:39:46','active',1),(41,'admin123','Restored student: Anderson, Albert M (Grade: 7 - Section: A)','2025-04-24 05:42:20','active',1),(42,'admin123','Restored student: Wilson, Wendy L (Grade: 12 - Section: F)','2025-04-24 05:42:23','active',1),(43,'admin123','Archived candidate: Garcia, Emmanuel Galpao (Auditor)','2025-04-24 05:43:41','active',1),(44,'admin123','Restored candidate: Garcia, Emmanuel Galpao (Auditor)','2025-04-24 05:44:31','active',1),(45,'admin123','Archived student: Wilson, Olivia Mae (Grade: 7 - Section: B)','2025-04-24 13:47:24','active',1),(46,'admin123','Restored student: Wilson, Olivia Mae (Grade: 7 - Section: B)','2025-04-24 13:47:26','active',1),(47,'admin123','Added student: Baustista, Jensen Johna (Grade: 9 - Section: H, LRN: 123456711112)','2025-04-24 15:05:56','active',1),(48,'admin123','Added student: Tatsuki, Mark Lhemuel Galang (Grade: 7 - Section: A, LRN: 111111111111)','2025-04-24 15:10:15','active',1),(49,'admin123','Archived student: Tatsuki, Mark Lhemuel Galang (Grade: 7 - Section: A)','2025-04-24 15:11:05','active',1),(50,'admin123','Restored student: Tatsuki, Mark Lhemuel Galang (Grade: 7 - Section: A)','2025-04-24 15:11:08','active',1),(51,'admin123','Added student: Baustista, Harold Javier (Grade: 10 - Section: K, LRN: 212212112121)','2025-04-24 15:14:42','active',1),(52,'admin123','Added student: Wallmart, Harold Johna (Grade: 8 - Section: D, LRN: 313131313132)','2025-04-24 15:30:22','active',1),(53,'admin123','Archived student: Anderson, Albert M (Grade: 7 - Section: A)','2025-04-24 15:35:11','active',1),(54,'admin123','Restored student: Anderson, Albert M (Grade: 7 - Section: A)','2025-04-24 15:35:14','active',1),(55,'admin123','Added student: Wallmart, Lyns Javier (Grade: 7 - Section: A, LRN: 313131313131)','2025-04-24 15:43:01','active',1),(56,'admin123','Added student: Wilson, Wendy Ligua (Grade: 7 - Section: A, LRN: 345678901235)','2025-04-25 05:24:01','active',1),(57,'admin123','Unarchived admin with ID: 3','2025-04-25 06:21:08','active',1),(58,'admin123','Added new section: Grade 8 - Granite','2025-04-29 03:13:32','active',1),(59,'admin123','Archived section with ID: 17','2025-04-29 03:19:41','active',1),(60,'admin123','Unarchived section with ID: 17','2025-04-29 03:19:45','active',1),(61,'admin123','Archived section with ID: 1','2025-04-29 03:20:18','active',1),(62,'admin123','Unarchived section with ID: 1','2025-04-29 03:20:24','active',1),(63,'admin123','Archived section with ID: 1','2025-04-29 03:23:27','active',1),(64,'admin123','Archived section with ID: 2','2025-04-29 03:26:15','active',1),(65,'admin123','Archived section ID: 3','2025-04-29 03:30:37','active',1),(66,'admin123','Edited section: Grade 11 - ABM-B','2025-04-29 03:44:57','active',1),(67,'admin123','Edited section: Grade 11 - ABM-A','2025-04-29 04:02:27','active',1),(68,'admin123','Archived student: Anderson, Albert M (Grade: 7 - Section: A)','2025-05-14 13:22:50','active',1),(69,'admin123','Restored student: Anderson, Albert M (Grade: 7 - Section: A)','2025-05-14 13:22:55','active',1),(70,'admin123','Added new section: Grade 8 - A','2025-05-14 14:21:56','active',1),(71,'admin123','Edited section: Grade 9 - Is','2025-05-14 14:22:09','active',1),(72,'admin123','Edited section: Grade 9 - I','2025-05-14 14:22:46','active',1),(73,'admin123','Edited section: Grade 9 - I','2025-05-14 14:23:50','active',1),(74,'admin123','Edited section: Grade 9 - I','2025-05-14 14:23:54','active',1),(75,'admin123','Edited section: Grade 9 - I','2025-05-14 14:24:42','active',1),(76,'admin123','Added new section: Grade 10 - Glowing','2025-05-14 14:24:59','active',1),(77,'admin123','Unarchived admin with ID: 2','2025-05-14 14:36:44','active',1),(78,'admin123','Archived admin with ID: 2','2025-05-14 14:47:46','active',1),(79,'admin123','Unarchived admin with ID: 2','2025-05-14 14:50:12','active',1),(80,'admin123','Archived admin with ID: 2','2025-05-15 10:45:36','active',1),(81,'admin123','Unarchived admin with ID: 2','2025-05-15 10:45:37','active',1),(82,'admin123','Archived candidate: Garcia, Emmanuel Galpao (Auditor)','2025-05-15 10:51:49','active',1),(83,'admin123','Restored candidate: Garcia, Emmanuel Galpao (Auditor)','2025-05-15 10:53:05','active',1),(84,'admin123','Archived candidate: Garcia, Emmanuel Galpao (Auditor)','2025-05-15 10:53:13','active',1),(85,'admin123','Restored candidate: Garcia, Emmanuel Galpao (Auditor)','2025-05-15 10:53:17','active',1),(86,'admin123','Archived student: Anderson, Albert M (Grade: 7 - Section: A)','2025-05-15 11:51:13','active',1),(87,'admin123','Restored student: Anderson, Albert M (Grade: 7 - Section: A)','2025-05-15 11:51:16','active',1),(88,'admin123','Added new section: Grade 7 - Glock','2025-05-20 08:58:14','active',1),(89,'admin123','Archived candidate: Arenas, Manuel Gumaro (President)','2025-05-22 11:36:19','active',1),(90,'admin123','Restored candidate: Arenas, Manuel Gumaro (President)','2025-05-22 11:36:25','active',1),(91,'admin123','Archived candidate: Wallmart, Mark Valerio (G12 representative)','2025-05-22 12:22:56','active',1),(92,'admin123','Restored candidate: Wallmart, Mark Valerio (G12 representative)','2025-05-22 12:23:00','active',1),(93,'admin123','Archived candidate: Wallmart, Mark Valerio (G12 representative)','2025-05-22 12:23:54','active',1),(94,'admin123','Restored candidate: Wallmart, Mark Valerio (G12 representative)','2025-05-22 12:23:58','active',1),(95,'admin123','Archived candidate: Wallmart, Mark Valerio (G12 representative)','2025-05-22 14:21:28','active',1),(96,'admin123','Restored candidate: Wallmart, Mark Valerio (G12 representative)','2025-05-22 14:21:32','active',1),(97,'admin123','Edited student: Dela Cruz, Ana Maria → Dela Cruz, Ana Maria','2025-05-22 15:07:44','active',1),(98,'admin123','Edited student: Dela Cruz, Ana Maria → Dela Cruz, Ana Maria','2025-05-22 15:07:54','active',1),(99,'admin123','Edited student: Dela Cruz, Ana Maria → Dela Cruz, Ana Maria','2025-05-22 15:15:27','active',1),(100,'admin123','Edited student: Dela Cruz, Ana Maria → Dela Cruz, Ana Maria','2025-05-22 15:15:29','active',1),(101,'admin123','Edited student: Dela Cruz, Ana Maria → Dela Cruz, Ana Maria','2025-05-22 15:15:40','active',1),(102,'admin123','Edited student: Dela Cruz, Ana Maria → Dela Cruz, Ana Maria','2025-05-22 15:15:42','active',1),(103,'admin123','Edited student: Dela Cruz, Ana Maria → Dela Cruz, Ana Maria','2025-05-22 15:19:36','active',1),(104,'admin123','Added student: Maswa, Jarit Yamada (Grade: 11 - Section: ABM-A, LRN: 232332111121)','2025-05-22 16:09:00','active',1),(105,'admin123','Added new section: Grade 7 - F','2025-05-22 16:26:40','active',1),(106,'admin123','Added new section: Grade 8 - Cloud','2025-05-22 16:27:08','active',1),(107,'admin123','Added new section: Grade 8 - A','2025-05-22 16:34:38','active',1),(108,'admin123','Added new section: Grade 12 - Programming','2025-05-22 16:34:58','active',1),(109,'admin123','Edited section: Grade 12 - Programming','2025-05-22 16:43:03','active',1),(110,'admin123','Edited section: Grade 12 - Programming','2025-05-22 16:43:27','active',1),(111,'admin123','Edited section: Grade 12 - Programming','2025-05-22 16:43:34','active',1),(112,'admin123','Edited section: Grade 7 - Aa','2025-05-22 16:43:42','active',1),(113,'admin123','Edited section: Grade 7 - A','2025-05-22 16:43:51','active',1),(114,'admin123','Edited section: Grade 11 - Animation','2025-05-22 16:47:35','active',1),(115,'admin123','Edited section: Grade 12 - ABM-B','2025-05-22 17:07:53','active',1),(116,'admin123','Edited student: Anderson, Albert Martinez → Anderson, Albert Martinez','2025-05-22 17:08:58','active',1),(117,'admin123','Edited section: Grade 10 - Glowin','2025-05-22 17:09:11','active',1),(118,'admin123','Added new section: Grade 8 - Aa','2025-05-22 17:09:20','active',1),(119,'admin123','Edited student: Anderson, Albert Martinez → Anderson, Albert Martinez','2025-05-22 17:47:20','active',1);
/*!40000 ALTER TABLE `transaction_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vote_history`
--

DROP TABLE IF EXISTS `vote_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vote_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `voter_id` int(11) DEFAULT NULL,
  `candidate_id` int(11) DEFAULT NULL,
  `vote_date` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vote_history`
--

LOCK TABLES `vote_history` WRITE;
/*!40000 ALTER TABLE `vote_history` DISABLE KEYS */;
/*!40000 ALTER TABLE `vote_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `votes`
--

DROP TABLE IF EXISTS `votes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `votes` (
  `voteID` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) NOT NULL,
  `candidate_id` int(11) DEFAULT NULL,
  `position` varchar(255) NOT NULL,
  `batch` int(11) DEFAULT 1,
  PRIMARY KEY (`voteID`),
  KEY `student_id` (`student_id`),
  KEY `candidate_id` (`candidate_id`),
  CONSTRAINT `votes_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_ID`),
  CONSTRAINT `votes_ibfk_2` FOREIGN KEY (`candidate_id`) REFERENCES `candidates` (`candidateID`)
) ENGINE=InnoDB AUTO_INCREMENT=77 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `votes`
--

LOCK TABLES `votes` WRITE;
/*!40000 ALTER TABLE `votes` DISABLE KEYS */;
INSERT INTO `votes` VALUES (52,25,1,'President',2025),(53,25,3,'Vice President',2025),(54,25,4,'Secretary',2025),(55,5,NULL,'President',2025),(56,5,NULL,'Vice President',2025),(57,5,NULL,'Secretary',2025),(58,5,NULL,'Auditor',2025),(59,5,NULL,'P.I.O.',2025),(60,5,NULL,'Treasurer',2025),(61,5,NULL,'G12 representative',2025),(62,5,NULL,'',2025),(63,6,1,'President',2025),(64,6,3,'Vice President',2025),(65,6,NULL,'Secretary',2025),(66,6,5,'Auditor',2025),(67,6,9,'P.I.O.',2025),(68,6,NULL,'Treasurer',2025),(69,6,NULL,'G12 representative',2025),(70,6,NULL,'',2025),(71,9,1,'President',2025),(72,9,3,'Vice President',2025),(73,9,NULL,'Secretary',2025),(74,9,5,'Auditor',2025),(75,9,NULL,'P.I.O.',2025),(76,9,10,'Treasurer',2025);
/*!40000 ALTER TABLE `votes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `voting_status`
--

DROP TABLE IF EXISTS `voting_status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `voting_status` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `status` enum('open','closed') NOT NULL DEFAULT 'closed',
  `batch` int(11) DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `voting_status`
--

LOCK TABLES `voting_status` WRITE;
/*!40000 ALTER TABLE `voting_status` DISABLE KEYS */;
INSERT INTO `voting_status` VALUES (1,'open',1);
/*!40000 ALTER TABLE `voting_status` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping events for database 'voting_db'
--

--
-- Dumping routines for database 'voting_db'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-06-23  9:06:46
