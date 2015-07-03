-- MySQL dump 10.13  Distrib 5.5.43, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: flipit_test_user
-- ------------------------------------------------------
-- Server version	5.5.43-0ubuntu0.14.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `chain`
--

DROP TABLE IF EXISTS `chain`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `chain` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=580 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `chain`
--

LOCK TABLES `chain` WRITE;
/*!40000 ALTER TABLE `chain` DISABLE KEYS */;
/*!40000 ALTER TABLE `chain` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `chain_item`
--

DROP TABLE IF EXISTS `chain_item`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `chain_item` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `shopname` varchar(255) DEFAULT NULL,
  `websiteid` bigint(20) DEFAULT NULL,
  `chainid` bigint(20) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `permalink` varchar(255) DEFAULT NULL,
  `locale` varchar(255) DEFAULT NULL,
  `status` tinyint(1) DEFAULT '1',
  `shopId` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_shopname_website_idx` (`shopname`,`websiteid`),
  KEY `ref_chain_items` (`chainid`),
  KEY `ref_chain_website` (`websiteid`),
  CONSTRAINT `ref_chain_items` FOREIGN KEY (`chainid`) REFERENCES `chain` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ref_chain_website` FOREIGN KEY (`websiteid`) REFERENCES `website` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2418 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `chain_item`
--

LOCK TABLES `chain_item` WRITE;
/*!40000 ALTER TABLE `chain_item` DISABLE KEYS */;
/*!40000 ALTER TABLE `chain_item` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `global_export_password`
--

DROP TABLE IF EXISTS `global_export_password`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `global_export_password` (
  `id` smallint(6) NOT NULL AUTO_INCREMENT,
  `password` varchar(255) DEFAULT NULL,
  `exportType` varchar(50) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `global_export_password`
--

LOCK TABLES `global_export_password` WRITE;
/*!40000 ALTER TABLE `global_export_password` DISABLE KEYS */;
/*!40000 ALTER TABLE `global_export_password` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ip_addresses`
--

DROP TABLE IF EXISTS `ip_addresses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ip_addresses` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `ipaddress` varchar(15) DEFAULT NULL,
  `deleted` tinyint(1) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ip_addresses`
--

LOCK TABLES `ip_addresses` WRITE;
/*!40000 ALTER TABLE `ip_addresses` DISABLE KEYS */;
INSERT INTO `ip_addresses` VALUES (17,'test','192.168.56.1',0,'2015-07-02 13:35:28','2015-07-02 13:35:28');
/*!40000 ALTER TABLE `ip_addresses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migration_version`
--

DROP TABLE IF EXISTS `migration_version`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migration_version` (
  `version` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migration_version`
--

LOCK TABLES `migration_version` WRITE;
/*!40000 ALTER TABLE `migration_version` DISABLE KEYS */;
/*!40000 ALTER TABLE `migration_version` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `profile_image`
--

DROP TABLE IF EXISTS `profile_image`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `profile_image` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'PK',
  `ext` varchar(5) DEFAULT NULL,
  `path` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=373 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `profile_image`
--

LOCK TABLES `profile_image` WRITE;
/*!40000 ALTER TABLE `profile_image` DISABLE KEYS */;
INSERT INTO `profile_image` VALUES (372,'jpg','images/upload/shop/','1409026126_Jellyfish.jpg',0,'2015-07-02 13:35:27','2015-07-02 13:35:27');
/*!40000 ALTER TABLE `profile_image` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ref_user_website`
--

DROP TABLE IF EXISTS `ref_user_website`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ref_user_website` (
  `userid` bigint(20) NOT NULL DEFAULT '0' COMMENT 'FK to user.id',
  `websiteid` bigint(20) NOT NULL DEFAULT '0' COMMENT 'FK to website.id',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`),
  KEY `userid_idx` (`userid`),
  KEY `websiteid_idx` (`websiteid`),
  CONSTRAINT `ref_user_website_userid_user_id` FOREIGN KEY (`userid`) REFERENCES `user` (`id`),
  CONSTRAINT `ref_user_website_websiteid_website_id` FOREIGN KEY (`websiteid`) REFERENCES `website` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5557 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ref_user_website`
--

LOCK TABLES `ref_user_website` WRITE;
/*!40000 ALTER TABLE `ref_user_website` DISABLE KEYS */;
INSERT INTO `ref_user_website` VALUES (354,39,'2015-07-02 13:35:28','2015-07-02 13:35:28',5556);
/*!40000 ALTER TABLE `ref_user_website` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rights`
--

DROP TABLE IF EXISTS `rights`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rights` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'PK',
  `name` varchar(255) DEFAULT NULL,
  `rights` mediumint(9) DEFAULT NULL COMMENT 'It describe permission of the role over sites, contents, administration, statistic, system manager',
  `description` varchar(512) DEFAULT NULL COMMENT 'right description',
  `roleid` bigint(20) DEFAULT NULL COMMENT 'FK to role.id',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `roleid_idx` (`roleid`),
  CONSTRAINT `rights_roleid_role_id` FOREIGN KEY (`roleid`) REFERENCES `role` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rights`
--

LOCK TABLES `rights` WRITE;
/*!40000 ALTER TABLE `rights` DISABLE KEYS */;
INSERT INTO `rights` VALUES (21,'administration',1,'test desc',5,'2015-07-02 13:35:28','2015-07-02 13:35:28');
/*!40000 ALTER TABLE `rights` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `robot`
--

DROP TABLE IF EXISTS `robot`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `robot` (
  `id` smallint(6) NOT NULL AUTO_INCREMENT,
  `website` text,
  `content` longblob,
  `deleted` tinyint(1) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `robot`
--

LOCK TABLES `robot` WRITE;
/*!40000 ALTER TABLE `robot` DISABLE KEYS */;
/*!40000 ALTER TABLE `robot` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role`
--

DROP TABLE IF EXISTS `role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `role` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'PK',
  `name` varchar(255) DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role`
--

LOCK TABLES `role` WRITE;
/*!40000 ALTER TABLE `role` DISABLE KEYS */;
INSERT INTO `role` VALUES (5,'administration',0,'2015-07-02 13:35:27','2015-07-02 13:35:27');
/*!40000 ALTER TABLE `role` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `splash`
--

DROP TABLE IF EXISTS `splash`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `splash` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `locale` text,
  `offerId` bigint(20) DEFAULT NULL,
  `deleted` tinyint(1) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `splash`
--

LOCK TABLES `splash` WRITE;
/*!40000 ALTER TABLE `splash` DISABLE KEYS */;
/*!40000 ALTER TABLE `splash` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'PK',
  `firstname` text,
  `lastname` text,
  `email` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `status` tinyint(1) DEFAULT '1',
  `google` text,
  `twitter` text,
  `pinterest` text,
  `likes` text,
  `dislike` text,
  `mainText` text NOT NULL,
  `roleid` bigint(20) DEFAULT NULL COMMENT 'FK to role.id',
  `profileimageid` bigint(20) DEFAULT NULL COMMENT 'FK to profile_image.id',
  `createdby` bigint(20) DEFAULT NULL COMMENT 'FK to user.id',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `currentlogin` datetime NOT NULL COMMENT 'current login timestamp',
  `lastlogin` datetime NOT NULL COMMENT 'last login timestamp',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `showInAboutListing` tinyint(1) DEFAULT NULL,
  `slug` text,
  `addtosearch` tinyint(1) NOT NULL DEFAULT '0',
  `popularkortingscode` bigint(20) DEFAULT NULL,
  `passwordchangetime` datetime DEFAULT '2013-12-12 12:50:20',
  `countryLocale` varchar(10) DEFAULT NULL,
  `editorText` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `roleid_idx` (`roleid`),
  KEY `profileimageid_idx` (`profileimageid`),
  CONSTRAINT `user_profileimageid_profile_image_id` FOREIGN KEY (`profileimageid`) REFERENCES `profile_image` (`id`),
  CONSTRAINT `user_roleid_role_id` FOREIGN KEY (`roleid`) REFERENCES `role` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=355 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES (354,'test','user','test@flipit.com','5f4dcc3b5aa765d61d8327deb882cf99',1,NULL,NULL,NULL,NULL,NULL,'test',5,372,NULL,0,'2015-07-02 14:49:26','2015-07-02 14:49:07','2015-07-02 13:35:27','2015-07-02 13:35:27',NULL,'test-user',0,NULL,'2015-09-17 00:00:00',NULL,NULL);
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_session`
--

DROP TABLE IF EXISTS `user_session`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_session` (
  `id` bigint(20) NOT NULL DEFAULT '0' COMMENT 'PK',
  `userid` bigint(20) DEFAULT NULL COMMENT 'LoggedIn User''s Id',
  `sessionid` varchar(255) DEFAULT NULL COMMENT 'LoggedIn User''s Session Id',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_session`
--

LOCK TABLES `user_session` WRITE;
/*!40000 ALTER TABLE `user_session` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_session` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `website`
--

DROP TABLE IF EXISTS `website`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `website` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'PK',
  `name` varchar(100) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `status` varchar(10) DEFAULT NULL,
  `chain` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `website`
--

LOCK TABLES `website` WRITE;
/*!40000 ALTER TABLE `website` DISABLE KEYS */;
INSERT INTO `website` VALUES (39,'kortingscode.nl','http://www.kortingscode.nl','2015-07-02 13:35:28','2015-07-02 13:35:28',0,'online',NULL);
/*!40000 ALTER TABLE `website` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2015-07-02 16:07:08
