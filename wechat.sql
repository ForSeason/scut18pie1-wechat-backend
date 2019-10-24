-- MySQL dump 10.13  Distrib 8.0.15, for linux-glibc2.12 (x86_64)
--
-- Host: localhost    Database: wechat
-- ------------------------------------------------------
-- Server version	8.0.15

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
 SET NAMES utf8mb4 ;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `dp`
--

DROP TABLE IF EXISTS `dp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `dp` (
  `dpID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weixinID` varchar(30) NOT NULL,
  `point` varchar(5) NOT NULL,
  `info` varchar(50) NOT NULL,
  `year` int(11) DEFAULT NULL,
  PRIMARY KEY (`dpID`)
) ENGINE=InnoDB AUTO_INCREMENT=385 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dp`
--

LOCK TABLES `dp` WRITE;
/*!40000 ALTER TABLE `dp` DISABLE KEYS */;
/*!40000 ALTER TABLE `dp` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jw`
--

DROP TABLE IF EXISTS `jw`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `jw` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `account` varchar(20) NOT NULL,
  `password` varchar(100) NOT NULL,
  `weixinID` char(80) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `account` (`account`),
  UNIQUE KEY `weixinID` (`weixinID`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jw`
--

LOCK TABLES `jw` WRITE;
/*!40000 ALTER TABLE `jw` DISABLE KEYS */;
INSERT INTO `jw` VALUES (2,'201730411368','404notfound','oKldy55in0SQLxDEjV4j-GU6VBeU'),(3,'201836481111','20050830Wu','oKldy5_pjlrMmHxvtpSSo0ihbaOs'),(9,'201866481204','zhaorong1218','oKldy56IkVwQ-qfNWmijjLdJUEq4'),(10,'201830480189','ysy604822','oKldy5yGdkZnvBJOKH8C7hYRql4U'),(11,'201830481018','scutshdshd19153X','oKldy5wtvEt2dC_37PCG0zRcsD5E'),(13,'学号','密码','oKldy5w1ytq1xQIBfNXNBwO8aJqo'),(16,'201830470135','Aa686910','oKldy5wXr5_hUUQwGJRy3neybxgM'),(18,'201864120372','8744ZhouTx@','oKldy52a5fXZqcXYS50OD58ed-K0'),(20,'201830480363','15256947285Aa','oKldy5wCd1sbJ8CjZnaX1u5OvR4c'),(21,'201865481236','wasd106369','oKldy58jLTQoRMGEcRHZGqqPSkgA'),(24,'201830480073','CQL7288933','oKldy56xMVZkpsv9t_rFvGlP4S1Y'),(26,'201866480382','+YANG.1999.9.28','oKldy55cY2X_XY0iQvOzbod--Cy0'),(27,'201830480417','Yeluo09545x','oKldy5zIrH0XrWftICBqIQsierK4'),(28,'201830481124','xxx2372660','oKldy5yqmCXw5hBPqdHL04ROV_bc');
/*!40000 ALTER TABLE `jw` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `log`
--

DROP TABLE IF EXISTS `log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `time` varchar(20) NOT NULL,
  `weixinID` char(70) NOT NULL,
  `content` varchar(200) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4069 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `log`
--

LOCK TABLES `log` WRITE;
/*!40000 ALTER TABLE `log` DISABLE KEYS */;
/*!40000 ALTER TABLE `log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `schedule`
--

DROP TABLE IF EXISTS `schedule`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `schedule` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `weixinID` char(70) NOT NULL,
  `week` int(11) NOT NULL,
  `day` varchar(10) NOT NULL,
  `name` varchar(50) NOT NULL,
  `room` varchar(20) NOT NULL,
  `period` varchar(10) NOT NULL,
  `teacher` varchar(50) DEFAULT NULL,
  `region` varchar(15) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4692 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `schedule`
--

LOCK TABLES `schedule` WRITE;
/*!40000 ALTER TABLE `schedule` DISABLE KEYS */;
/*!40000 ALTER TABLE `schedule` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `user` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(20) NOT NULL,
  `sex` varchar(8) NOT NULL,
  `ethnicity` varchar(20) NOT NULL,
  `weixinID` char(70) NOT NULL,
  `studentID` varchar(12) NOT NULL,
  `ID_card` varchar(18) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `domitory` varchar(6) NOT NULL,
  `room` varchar(6) NOT NULL,
  `parentPhone` varchar(15) NOT NULL,
  `fromWhere` varchar(20) NOT NULL,
  `politicalStatus` varchar(20) NOT NULL,
  `domitoryMaster` varchar(4) NOT NULL,
  `adress` varchar(200) NOT NULL,
  `postalcode` varchar(10) NOT NULL,
  `parentName` varchar(10) NOT NULL,
  `job` varchar(8) NOT NULL DEFAULT '无',
  PRIMARY KEY (`ID`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=54 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vote`
--

DROP TABLE IF EXISTS `vote`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `vote` (
  `class` varchar(40) NOT NULL,
  `json` varchar(2000) NOT NULL,
  `creator` varchar(20) NOT NULL,
  PRIMARY KEY (`class`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vote`
--

LOCK TABLES `vote` WRITE;
/*!40000 ALTER TABLE `vote` DISABLE KEYS */;
/*!40000 ALTER TABLE `vote` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wp`
--

DROP TABLE IF EXISTS `wp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `wp` (
  `wpID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weixinID` varchar(30) NOT NULL,
  `point` varchar(5) NOT NULL,
  `info` varchar(50) NOT NULL,
  `year` int(11) DEFAULT NULL,
  PRIMARY KEY (`wpID`)
) ENGINE=InnoDB AUTO_INCREMENT=144 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wp`
--

LOCK TABLES `wp` WRITE;
/*!40000 ALTER TABLE `wp` DISABLE KEYS */;
/*!40000 ALTER TABLE `wp` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `zp`
--

DROP TABLE IF EXISTS `zp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `zp` (
  `zpID` int(11) NOT NULL AUTO_INCREMENT,
  `weixinID` varchar(30) NOT NULL,
  `point` varchar(5) NOT NULL,
  `info` varchar(50) NOT NULL,
  `year` int(11) DEFAULT NULL,
  PRIMARY KEY (`zpID`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `zp`
--

LOCK TABLES `zp` WRITE;
/*!40000 ALTER TABLE `zp` DISABLE KEYS */;
/*!40000 ALTER TABLE `zp` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2019-10-24 20:06:39
