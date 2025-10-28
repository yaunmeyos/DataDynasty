-- MySQL dump 10.13  Distrib 8.0.43, for Win64 (x86_64)
--
-- Host: localhost    Database: esports_manager
-- ------------------------------------------------------
-- Server version	9.1.0

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `matches`
--

DROP TABLE IF EXISTS `matches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `matches` (
  `id` int NOT NULL AUTO_INCREMENT,
  `tournament_id` int DEFAULT NULL,
  `round_number` int DEFAULT '1',
  `match_number` int DEFAULT NULL,
  `team1_id` int DEFAULT NULL,
  `team2_id` int DEFAULT NULL,
  `winner_id` int DEFAULT NULL,
  `status` enum('scheduled','live','completed','cancelled') DEFAULT 'scheduled',
  `scheduled_time` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `tournament_id` (`tournament_id`),
  KEY `team1_id` (`team1_id`),
  KEY `team2_id` (`team2_id`),
  KEY `winner_id` (`winner_id`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `matches`
--

LOCK TABLES `matches` WRITE;
/*!40000 ALTER TABLE `matches` DISABLE KEYS */;
INSERT INTO `matches` VALUES (1,1,1,NULL,4,3,4,'completed',NULL,'2025-10-25 10:38:02'),(2,1,1,NULL,5,1,1,'completed',NULL,'2025-10-25 10:38:14'),(3,1,1,NULL,4,5,4,'completed',NULL,'2025-10-25 10:38:25'),(4,1,1,NULL,3,1,1,'completed',NULL,'2025-10-25 10:38:32'),(5,1,1,NULL,4,1,4,'completed',NULL,'2025-10-25 10:38:42'),(6,1,1,NULL,3,5,5,'completed',NULL,'2025-10-25 10:39:00'),(7,1,1,NULL,6,4,4,'completed',NULL,'2025-10-25 14:07:54'),(8,1,1,NULL,6,3,6,'completed',NULL,'2025-10-25 14:08:13'),(9,1,1,NULL,6,5,6,'completed',NULL,'2025-10-25 14:08:21'),(10,1,1,NULL,6,1,6,'completed',NULL,'2025-10-25 14:08:29'),(11,1,1,NULL,7,4,7,'completed',NULL,'2025-10-25 15:05:30'),(12,1,1,NULL,7,3,7,'completed',NULL,'2025-10-25 15:05:40'),(13,1,1,NULL,5,7,7,'completed',NULL,'2025-10-25 15:06:00'),(14,1,1,NULL,1,7,7,'completed',NULL,'2025-10-25 15:06:11'),(15,1,1,NULL,6,7,7,'completed',NULL,'2025-10-25 15:06:20');
/*!40000 ALTER TABLE `matches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `players`
--

DROP TABLE IF EXISTS `players`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `players` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `team_id` int DEFAULT NULL,
  `role` enum('captain','player','substitute') DEFAULT 'player',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `game_uid` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_game_uid` (`game_uid`),
  KEY `team_id` (`team_id`)
) ENGINE=MyISAM AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `players`
--

LOCK TABLES `players` WRITE;
/*!40000 ALTER TABLE `players` DISABLE KEYS */;
INSERT INTO `players` VALUES (1,'faker',1,'player','2025-10-25 10:24:32',NULL),(2,'doran',1,'player','2025-10-25 10:24:32',NULL),(3,'oner',1,'player','2025-10-25 10:24:32',NULL),(4,'gumayusi',1,'player','2025-10-25 10:24:32',NULL),(5,'keria',1,'player','2025-10-25 10:24:32',NULL),(6,'labrov',4,'player','2025-10-25 10:31:43',NULL),(7,'brokenblade',4,'player','2025-10-25 10:31:43','G2 BrokenBlade#LEC'),(8,'caps',4,'player','2025-10-25 10:31:43',NULL),(9,'hanssama',4,'player','2025-10-25 10:31:43',NULL),(10,'skewmond',4,'player','2025-10-25 10:31:43',NULL),(11,'cophi',3,'player','2025-10-25 10:34:15',NULL),(12,'neroleno',3,'player','2025-10-25 10:34:15',NULL),(13,'ars',3,'player','2025-10-25 10:34:15',NULL),(14,'lordnikke',3,'player','2025-10-25 10:34:15',NULL),(15,'yuha',3,'player','2025-10-25 10:34:15',NULL),(16,'yolly',5,'player','2025-10-25 10:36:39',NULL),(17,'dacs',5,'player','2025-10-25 10:36:39',NULL),(18,'ulysses',5,'player','2025-10-25 10:36:39',NULL),(19,'skelly',5,'player','2025-10-25 10:36:39',NULL),(20,'khelvin',5,'player','2025-10-25 10:36:39',NULL),(21,'Yuu',6,'player','2025-10-25 15:02:21','KjLovesRaze#143'),(22,'Zaxyn',6,'player','2025-10-25 15:02:21','Frost#Ph2'),(23,'Yam',6,'player','2025-10-25 15:02:21',NULL),(24,'Zeroo',6,'player','2025-10-25 15:02:21',NULL),(25,'Ming-Ming',6,'player','2025-10-25 15:02:21',NULL),(26,'jules',7,'player','2025-10-25 15:04:45',NULL),(27,'reuel',7,'player','2025-10-25 15:04:45',NULL),(28,'torms',7,'player','2025-10-25 15:04:45',NULL),(29,'jm',7,'player','2025-10-25 15:04:45',NULL),(30,'joszhua',7,'player','2025-10-25 15:04:45',NULL);
/*!40000 ALTER TABLE `players` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `teams`
--

DROP TABLE IF EXISTS `teams`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `teams` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `wins` int DEFAULT '0',
  `losses` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `teams`
--

LOCK TABLES `teams` WRITE;
/*!40000 ALTER TABLE `teams` DISABLE KEYS */;
INSERT INTO `teams` VALUES (1,'t1',2,3,'2025-10-25 10:23:34'),(3,'lev',0,5,'2025-10-25 10:23:46'),(4,'g2',4,1,'2025-10-25 10:23:51'),(5,'rge',1,4,'2025-10-25 10:24:08'),(6,'wittians',3,2,'2025-10-25 14:07:43'),(7,'jbt',5,0,'2025-10-25 15:04:02');
/*!40000 ALTER TABLE `teams` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tournament_teams`
--

DROP TABLE IF EXISTS `tournament_teams`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tournament_teams` (
  `id` int NOT NULL AUTO_INCREMENT,
  `tournament_id` int DEFAULT NULL,
  `team_id` int DEFAULT NULL,
  `seed_position` int DEFAULT NULL,
  `status` enum('registered','active','eliminated') DEFAULT 'registered',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_tournament_team` (`tournament_id`,`team_id`),
  KEY `team_id` (`team_id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tournament_teams`
--

LOCK TABLES `tournament_teams` WRITE;
/*!40000 ALTER TABLE `tournament_teams` DISABLE KEYS */;
INSERT INTO `tournament_teams` VALUES (1,1,1,NULL,'registered'),(2,1,2,NULL,'registered'),(3,1,3,NULL,'registered'),(4,1,4,NULL,'registered'),(5,1,5,NULL,'registered'),(6,1,6,NULL,'registered'),(7,1,7,NULL,'registered');
/*!40000 ALTER TABLE `tournament_teams` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tournaments`
--

DROP TABLE IF EXISTS `tournaments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tournaments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `game_title` varchar(100) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `status` enum('upcoming','active','completed') DEFAULT 'upcoming',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tournaments`
--

LOCK TABLES `tournaments` WRITE;
/*!40000 ALTER TABLE `tournaments` DISABLE KEYS */;
INSERT INTO `tournaments` VALUES (1,'Code Clash','League of Legends','2025-10-27','2025-10-31','active','2025-10-25 10:22:36'),(2,'LGX x RNFTC','mlbb',NULL,NULL,'upcoming','2025-10-25 10:23:11');
/*!40000 ALTER TABLE `tournaments` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-10-25 23:35:57
