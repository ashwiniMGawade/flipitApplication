-- MySQL dump 10.13  Distrib 5.5.43, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: flipit_test
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
-- Table structure for table `about`
--

DROP TABLE IF EXISTS `about`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `about` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) DEFAULT NULL,
  `content` longblob,
  `status` tinyint(1) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `about`
--

LOCK TABLES `about` WRITE;
/*!40000 ALTER TABLE `about` DISABLE KEYS */;
/*!40000 ALTER TABLE `about` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `adminfavoriteshp`
--

DROP TABLE IF EXISTS `adminfavoriteshp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `adminfavoriteshp` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'PK',
  `shopId` int(11) NOT NULL COMMENT 'FK to shop.id',
  `userId` int(11) NOT NULL COMMENT 'FK to user.id',
  PRIMARY KEY (`id`),
  KEY `shopId_idx` (`shopId`)
) ENGINE=InnoDB AUTO_INCREMENT=177 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `adminfavoriteshp`
--

LOCK TABLES `adminfavoriteshp` WRITE;
/*!40000 ALTER TABLE `adminfavoriteshp` DISABLE KEYS */;
/*!40000 ALTER TABLE `adminfavoriteshp` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `affliate_network`
--

DROP TABLE IF EXISTS `affliate_network`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `affliate_network` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'PK',
  `name` text,
  `status` tinyint(1) DEFAULT NULL,
  `replacewithid` bigint(20) DEFAULT NULL COMMENT 'FK to affliate_network.id , Defines a network is merged or not',
  `subId` text,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `replacewithid_idx` (`replacewithid`),
  CONSTRAINT `affliate_network_replacewithid_affliate_network_id` FOREIGN KEY (`replacewithid`) REFERENCES `affliate_network` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `affliate_network`
--

LOCK TABLES `affliate_network` WRITE;
/*!40000 ALTER TABLE `affliate_network` DISABLE KEYS */;
/*!40000 ALTER TABLE `affliate_network` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `article_chapter`
--

DROP TABLE IF EXISTS `article_chapter`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `article_chapter` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `articleId` bigint(20) DEFAULT NULL,
  `title` text,
  `content` longblob,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1945 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `article_chapter`
--

LOCK TABLES `article_chapter` WRITE;
/*!40000 ALTER TABLE `article_chapter` DISABLE KEYS */;
/*!40000 ALTER TABLE `article_chapter` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `articlecategory`
--

DROP TABLE IF EXISTS `articlecategory`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `articlecategory` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'PK',
  `name` varchar(100) DEFAULT NULL,
  `permalink` varchar(255) DEFAULT NULL,
  `metatitle` text,
  `metadescription` text,
  `description` longblob,
  `status` tinyint(1) DEFAULT NULL,
  `categoryiconid` bigint(20) DEFAULT NULL COMMENT 'FK to image.id',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `categorytitlecolor` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `categoryiconid` (`categoryiconid`),
  KEY `categoryiconid_idx` (`categoryiconid`),
  CONSTRAINT `articlecategory_ibfk_1` FOREIGN KEY (`categoryiconid`) REFERENCES `image` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `articlecategory`
--

LOCK TABLES `articlecategory` WRITE;
/*!40000 ALTER TABLE `articlecategory` DISABLE KEYS */;
/*!40000 ALTER TABLE `articlecategory` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `articles`
--

DROP TABLE IF EXISTS `articles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `articles` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'PK',
  `title` varchar(255) DEFAULT NULL,
  `permalink` varchar(255) DEFAULT NULL,
  `thumbnailid` bigint(20) DEFAULT NULL,
  `metatitle` text,
  `metadescription` text,
  `content` text,
  `publish` tinyint(1) NOT NULL,
  `publishdate` datetime NOT NULL,
  `authorid` bigint(20) NOT NULL,
  `authorname` varchar(255) DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `thumbnailsmallid` bigint(20) DEFAULT NULL,
  `featuredImage` bigint(20) DEFAULT NULL,
  `featuredImageStatus` tinyint(4) DEFAULT NULL,
  `plusTitle` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `thumbnailid` (`thumbnailid`),
  CONSTRAINT `articles_ibfk_2` FOREIGN KEY (`thumbnailid`) REFERENCES `image` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `articles`
--

LOCK TABLES `articles` WRITE;
/*!40000 ALTER TABLE `articles` DISABLE KEYS */;
/*!40000 ALTER TABLE `articles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `articleviewcount`
--

DROP TABLE IF EXISTS `articleviewcount`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `articleviewcount` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `articleid` bigint(20) NOT NULL,
  `onclick` bigint(20) NOT NULL,
  `onload` bigint(20) DEFAULT NULL,
  `ip` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `articleid_idx` (`articleid`)
) ENGINE=InnoDB AUTO_INCREMENT=4814 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `articleviewcount`
--

LOCK TABLES `articleviewcount` WRITE;
/*!40000 ALTER TABLE `articleviewcount` DISABLE KEYS */;
/*!40000 ALTER TABLE `articleviewcount` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categories_offers`
--

DROP TABLE IF EXISTS `categories_offers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categories_offers` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `offerId` bigint(20) DEFAULT NULL,
  `categoryId` bigint(20) DEFAULT NULL,
  `position` bigint(20) DEFAULT NULL,
  `deleted` tinyint(1) DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories_offers`
--

LOCK TABLES `categories_offers` WRITE;
/*!40000 ALTER TABLE `categories_offers` DISABLE KEYS */;
/*!40000 ALTER TABLE `categories_offers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `category`
--

DROP TABLE IF EXISTS `category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `category` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'PK',
  `name` varchar(100) DEFAULT NULL,
  `permalink` varchar(255) DEFAULT NULL,
  `metatitle` text,
  `metadescription` text,
  `description` longblob,
  `status` tinyint(1) DEFAULT NULL,
  `categoryiconid` bigint(20) DEFAULT NULL COMMENT 'FK to image.id',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `featured_category` tinyint(1) DEFAULT NULL,
  `categoryFeaturedImageId` bigint(20) DEFAULT NULL,
  `categoryHeaderImageId` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `categoryiconid` (`categoryiconid`),
  KEY `categoryiconid_idx` (`categoryiconid`),
  KEY `name` (`name`),
  KEY `name_2` (`name`),
  KEY `name_3` (`name`),
  KEY `name_4` (`name`),
  KEY `name_5` (`name`),
  KEY `name_6` (`name`),
  KEY `name_7` (`name`),
  KEY `name_8` (`name`),
  KEY `name_9` (`name`),
  KEY `name_10` (`name`),
  KEY `categoryFeaturedImageId_foreign_key` (`categoryFeaturedImageId`),
  KEY `categoryHeaderImageId_foreign_key` (`categoryHeaderImageId`),
  CONSTRAINT `categoryFeaturedImageId_foreign_key` FOREIGN KEY (`categoryFeaturedImageId`) REFERENCES `image` (`id`) ON DELETE CASCADE,
  CONSTRAINT `categoryHeaderImageId_foreign_key` FOREIGN KEY (`categoryHeaderImageId`) REFERENCES `image` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `category`
--

LOCK TABLES `category` WRITE;
/*!40000 ALTER TABLE `category` DISABLE KEYS */;
/*!40000 ALTER TABLE `category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `code_alert_queue`
--

DROP TABLE IF EXISTS `code_alert_queue`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `code_alert_queue` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `offerId` bigint(20) DEFAULT NULL,
  `shopId` bigint(20) DEFAULT NULL,
  `deleted` tinyint(1) DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=317 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `code_alert_queue`
--

LOCK TABLES `code_alert_queue` WRITE;
/*!40000 ALTER TABLE `code_alert_queue` DISABLE KEYS */;
/*!40000 ALTER TABLE `code_alert_queue` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `code_alert_settings`
--

DROP TABLE IF EXISTS `code_alert_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `code_alert_settings` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `email_subject` varchar(255) DEFAULT NULL,
  `email_header` longblob,
  `deleted` tinyint(1) DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `code_alert_settings`
--

LOCK TABLES `code_alert_settings` WRITE;
/*!40000 ALTER TABLE `code_alert_settings` DISABLE KEYS */;
/*!40000 ALTER TABLE `code_alert_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `code_alert_visitors`
--

DROP TABLE IF EXISTS `code_alert_visitors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `code_alert_visitors` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `offerId` bigint(20) DEFAULT NULL,
  `visitorId` bigint(20) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10351 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `code_alert_visitors`
--

LOCK TABLES `code_alert_visitors` WRITE;
/*!40000 ALTER TABLE `code_alert_visitors` DISABLE KEYS */;
/*!40000 ALTER TABLE `code_alert_visitors` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `conversions`
--

DROP TABLE IF EXISTS `conversions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `conversions` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `IP` varchar(50) DEFAULT NULL,
  `subid` varchar(50) DEFAULT NULL,
  `utma` varchar(255) DEFAULT NULL,
  `utmz` varchar(255) DEFAULT NULL,
  `utmv` varchar(255) DEFAULT NULL,
  `utmx` varchar(255) DEFAULT NULL,
  `shopId` bigint(20) DEFAULT NULL,
  `offerId` bigint(20) DEFAULT NULL,
  `visitorId` bigint(20) DEFAULT NULL,
  `converted` tinyint(1) DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `offer_conversion_idx` (`offerId`,`converted`,`IP`),
  KEY `shop_conversion_idx` (`shopId`,`converted`,`IP`)
) ENGINE=InnoDB AUTO_INCREMENT=617960 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `conversions`
--

LOCK TABLES `conversions` WRITE;
/*!40000 ALTER TABLE `conversions` DISABLE KEYS */;
/*!40000 ALTER TABLE `conversions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `couponcode`
--

DROP TABLE IF EXISTS `couponcode`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `couponcode` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `offerid` bigint(20) DEFAULT NULL,
  `code` varchar(255) DEFAULT NULL,
  `status` tinyint(1) DEFAULT '1' COMMENT '1-available ,0-used',
  PRIMARY KEY (`id`),
  KEY `offerid_idx` (`offerid`),
  KEY `couponcode_idx` (`offerid`,`status`)
) ENGINE=InnoDB AUTO_INCREMENT=765 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `couponcode`
--

LOCK TABLES `couponcode` WRITE;
/*!40000 ALTER TABLE `couponcode` DISABLE KEYS */;
/*!40000 ALTER TABLE `couponcode` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dashboard`
--

DROP TABLE IF EXISTS `dashboard`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dashboard` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `message` text,
  `no_of_offers` bigint(20) DEFAULT NULL,
  `no_of_shops` bigint(20) DEFAULT NULL,
  `no_of_clickouts` bigint(20) DEFAULT NULL,
  `no_of_subscribers` bigint(20) DEFAULT NULL,
  `total_no_of_offers` bigint(20) DEFAULT NULL,
  `total_no_of_shops` bigint(20) DEFAULT NULL,
  `total_no_of_shops_online_code` bigint(20) DEFAULT NULL,
  `total_no_of_shops_online_code_lastweek` bigint(20) DEFAULT NULL,
  `total_no_members` bigint(20) DEFAULT NULL,
  `deleted` tinyint(1) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `total_no_of_shops_online_code_thisweek` int(11) NOT NULL DEFAULT '0',
  `money_shop_ratio` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dashboard`
--

LOCK TABLES `dashboard` WRITE;
/*!40000 ALTER TABLE `dashboard` DISABLE KEYS */;
/*!40000 ALTER TABLE `dashboard` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `disqus_comments`
--

DROP TABLE IF EXISTS `disqus_comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disqus_comments` (
  `id` bigint(20) NOT NULL,
  `thread_id` bigint(20) DEFAULT NULL,
  `author_name` varchar(255) DEFAULT NULL,
  `comment` text,
  `created` bigint(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `disqus_comments`
--

LOCK TABLES `disqus_comments` WRITE;
/*!40000 ALTER TABLE `disqus_comments` DISABLE KEYS */;
/*!40000 ALTER TABLE `disqus_comments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `disqus_thread`
--

DROP TABLE IF EXISTS `disqus_thread`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disqus_thread` (
  `id` bigint(20) NOT NULL,
  `title` varchar(255) NOT NULL,
  `link` varchar(255) NOT NULL,
  `created` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `disqus_thread`
--

LOCK TABLES `disqus_thread` WRITE;
/*!40000 ALTER TABLE `disqus_thread` DISABLE KEYS */;
/*!40000 ALTER TABLE `disqus_thread` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `editor_ballon_text`
--

DROP TABLE IF EXISTS `editor_ballon_text`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `editor_ballon_text` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `shopid` bigint(20) DEFAULT NULL,
  `ballontext` varchar(255) DEFAULT NULL,
  `deleted` tinyint(1) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `editor_ballon_text`
--

LOCK TABLES `editor_ballon_text` WRITE;
/*!40000 ALTER TABLE `editor_ballon_text` DISABLE KEYS */;
/*!40000 ALTER TABLE `editor_ballon_text` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `editorwidget`
--

DROP TABLE IF EXISTS `editorwidget`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `editorwidget` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `type` varchar(255) DEFAULT NULL,
  `description` text,
  `subtitle` varchar(255) DEFAULT NULL,
  `editorId` bigint(20) DEFAULT NULL,
  `status` tinyint(1) DEFAULT '1' COMMENT '1-on ,0-off',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `editorwidget`
--

LOCK TABLES `editorwidget` WRITE;
/*!40000 ALTER TABLE `editorwidget` DISABLE KEYS */;
/*!40000 ALTER TABLE `editorwidget` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `email_light_box`
--

DROP TABLE IF EXISTS `email_light_box`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `email_light_box` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'PK',
  `title` varchar(100) DEFAULT NULL,
  `content` longblob,
  `status` tinyint(1) DEFAULT '0',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `email_light_box`
--

LOCK TABLES `email_light_box` WRITE;
/*!40000 ALTER TABLE `email_light_box` DISABLE KEYS */;
/*!40000 ALTER TABLE `email_light_box` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `email_subscribe`
--

DROP TABLE IF EXISTS `email_subscribe`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `email_subscribe` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) DEFAULT NULL,
  `send` int(20) NOT NULL,
  `deleted` int(20) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `email_subscribe`
--

LOCK TABLES `email_subscribe` WRITE;
/*!40000 ALTER TABLE `email_subscribe` DISABLE KEYS */;
/*!40000 ALTER TABLE `email_subscribe` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `emails`
--

DROP TABLE IF EXISTS `emails`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `emails` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `type` text,
  `header` text,
  `body` text,
  `footer` text,
  `schedule` text,
  `test` text,
  `status` tinyint(4) DEFAULT NULL,
  `send_date` date DEFAULT NULL,
  `deleted` tinyint(1) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `send_counter` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `emails`
--

LOCK TABLES `emails` WRITE;
/*!40000 ALTER TABLE `emails` DISABLE KEYS */;
/*!40000 ALTER TABLE `emails` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `emailsettings`
--

DROP TABLE IF EXISTS `emailsettings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `emailsettings` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `email` text,
  `name` text,
  `locale` text,
  `timezone` text,
  `deleted` tinyint(1) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `emailsettings`
--

LOCK TABLES `emailsettings` WRITE;
/*!40000 ALTER TABLE `emailsettings` DISABLE KEYS */;
/*!40000 ALTER TABLE `emailsettings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `excluded_keyword`
--

DROP TABLE IF EXISTS `excluded_keyword`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `excluded_keyword` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `keyword` varchar(255) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `action` enum('0','1') DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=97 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `excluded_keyword`
--

LOCK TABLES `excluded_keyword` WRITE;
/*!40000 ALTER TABLE `excluded_keyword` DISABLE KEYS */;
/*!40000 ALTER TABLE `excluded_keyword` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `favorite_offer`
--

DROP TABLE IF EXISTS `favorite_offer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `favorite_offer` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `offerId` bigint(20) NOT NULL,
  `visitorId` bigint(20) NOT NULL,
  `deleted` tinyint(4) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `offer_visitor_id_idx` (`offerId`,`visitorId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `favorite_offer`
--

LOCK TABLES `favorite_offer` WRITE;
/*!40000 ALTER TABLE `favorite_offer` DISABLE KEYS */;
/*!40000 ALTER TABLE `favorite_offer` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `favorite_shop`
--

DROP TABLE IF EXISTS `favorite_shop`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `favorite_shop` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `shopId` bigint(20) NOT NULL,
  `visitorId` bigint(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted` int(11) NOT NULL DEFAULT '0',
  `code_alert_send_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fav_cascade` (`visitorId`),
  KEY `shop_visitor_id_idx` (`shopId`,`visitorId`),
  CONSTRAINT `fav_cascade` FOREIGN KEY (`visitorId`) REFERENCES `visitor` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5413 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `favorite_shop`
--

LOCK TABLES `favorite_shop` WRITE;
/*!40000 ALTER TABLE `favorite_shop` DISABLE KEYS */;
/*!40000 ALTER TABLE `favorite_shop` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `footer`
--

DROP TABLE IF EXISTS `footer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `footer` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `topfooter` longtext,
  `middlecolumn1` longtext,
  `middlecolumn2` longtext,
  `middlecolumn3` longtext,
  `middlecolumn4` longtext,
  `bottomfooter` longtext,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `footer`
--

LOCK TABLES `footer` WRITE;
/*!40000 ALTER TABLE `footer` DISABLE KEYS */;
/*!40000 ALTER TABLE `footer` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `image`
--

DROP TABLE IF EXISTS `image`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `image` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'PK',
  `ext` varchar(5) DEFAULT NULL,
  `type` varchar(10) DEFAULT NULL,
  `path` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `height` int(11) DEFAULT NULL,
  `width` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `type_idx` (`type`)
) ENGINE=InnoDB AUTO_INCREMENT=2477 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `image`
--

LOCK TABLES `image` WRITE;
/*!40000 ALTER TABLE `image` DISABLE KEYS */;
/*!40000 ALTER TABLE `image` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `interestingcategory`
--

DROP TABLE IF EXISTS `interestingcategory`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `interestingcategory` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'PK',
  `userId` int(11) NOT NULL COMMENT 'FK to user.id',
  `categoryid` bigint(20) NOT NULL COMMENT 'FK to category.id',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=159 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `interestingcategory`
--

LOCK TABLES `interestingcategory` WRITE;
/*!40000 ALTER TABLE `interestingcategory` DISABLE KEYS */;
/*!40000 ALTER TABLE `interestingcategory` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `locale_settings`
--

DROP TABLE IF EXISTS `locale_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `locale_settings` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `locale` varchar(10) DEFAULT NULL,
  `timezone` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `locale_settings`
--

LOCK TABLES `locale_settings` WRITE;
/*!40000 ALTER TABLE `locale_settings` DISABLE KEYS */;
/*!40000 ALTER TABLE `locale_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mainmenu`
--

DROP TABLE IF EXISTS `mainmenu`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mainmenu` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `parentId` bigint(20) DEFAULT NULL,
  `root_id` bigint(20) DEFAULT NULL,
  `lft` int(11) DEFAULT NULL,
  `rgt` int(11) DEFAULT NULL,
  `level` smallint(6) DEFAULT NULL,
  `iconId` bigint(20) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `position` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mainmenu`
--

LOCK TABLES `mainmenu` WRITE;
/*!40000 ALTER TABLE `mainmenu` DISABLE KEYS */;
/*!40000 ALTER TABLE `mainmenu` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `media`
--

DROP TABLE IF EXISTS `media`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `media` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `alternatetext` varchar(255) DEFAULT NULL,
  `caption` varchar(150) DEFAULT NULL,
  `fileurl` varchar(255) DEFAULT NULL,
  `mediaimageid` bigint(20) DEFAULT NULL COMMENT 'FK to image.id',
  `authorName` varchar(255) DEFAULT NULL,
  `authorId` int(11) NOT NULL,
  `description` text,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `mediaimageid_idx` (`mediaimageid`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `media`
--

LOCK TABLES `media` WRITE;
/*!40000 ALTER TABLE `media` DISABLE KEYS */;
/*!40000 ALTER TABLE `media` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `menu`
--

DROP TABLE IF EXISTS `menu`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `menu` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `parentId` bigint(20) DEFAULT NULL,
  `root_id` bigint(20) DEFAULT NULL,
  `lft` int(11) DEFAULT NULL,
  `rgt` int(11) DEFAULT NULL,
  `level` smallint(6) DEFAULT NULL,
  `iconId` bigint(20) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `position` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=68 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `menu`
--

LOCK TABLES `menu` WRITE;
/*!40000 ALTER TABLE `menu` DISABLE KEYS */;
/*!40000 ALTER TABLE `menu` ENABLE KEYS */;
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
-- Table structure for table `moneysaving`
--

DROP TABLE IF EXISTS `moneysaving`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `moneysaving` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `pageid` bigint(20) NOT NULL,
  `categoryid` bigint(20) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `pageid` (`pageid`),
  KEY `categoryid` (`categoryid`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `moneysaving`
--

LOCK TABLES `moneysaving` WRITE;
/*!40000 ALTER TABLE `moneysaving` DISABLE KEYS */;
/*!40000 ALTER TABLE `moneysaving` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `moneysaving_article`
--

DROP TABLE IF EXISTS `moneysaving_article`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `moneysaving_article` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `type` varchar(255) DEFAULT NULL,
  `position` bigint(20) DEFAULT NULL COMMENT 'Holds the code position among popular code list',
  `status` tinyint(1) DEFAULT '0' COMMENT '1 ? enable , 0 ? disable',
  `articleid` bigint(20) DEFAULT NULL COMMENT 'FK to articles.id',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `articleid` (`articleid`),
  KEY `articleid_idx` (`articleid`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `moneysaving_article`
--

LOCK TABLES `moneysaving_article` WRITE;
/*!40000 ALTER TABLE `moneysaving_article` DISABLE KEYS */;
/*!40000 ALTER TABLE `moneysaving_article` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `news_letter_cache`
--

DROP TABLE IF EXISTS `news_letter_cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `news_letter_cache` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `value` longblob,
  `status` tinyint(1) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `news_letter_cache`
--

LOCK TABLES `news_letter_cache` WRITE;
/*!40000 ALTER TABLE `news_letter_cache` DISABLE KEYS */;
/*!40000 ALTER TABLE `news_letter_cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `newsletterbanners`
--

DROP TABLE IF EXISTS `newsletterbanners`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `newsletterbanners` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `path` varchar(255) DEFAULT NULL,
  `imagetype` varchar(10) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted` tinyint(1) DEFAULT NULL,
  `footerurl` varchar(255) DEFAULT NULL,
  `headerurl` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `newsletterbanners`
--

LOCK TABLES `newsletterbanners` WRITE;
/*!40000 ALTER TABLE `newsletterbanners` DISABLE KEYS */;
/*!40000 ALTER TABLE `newsletterbanners` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `newslettersub`
--

DROP TABLE IF EXISTS `newslettersub`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `newslettersub` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `deleted` int(11) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `newslettersub`
--

LOCK TABLES `newslettersub` WRITE;
/*!40000 ALTER TABLE `newslettersub` DISABLE KEYS */;
/*!40000 ALTER TABLE `newslettersub` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `offer`
--

DROP TABLE IF EXISTS `offer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `offer` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'PK',
  `title` varchar(255) DEFAULT NULL,
  `visability` varchar(255) DEFAULT NULL,
  `discounttype` varchar(255) DEFAULT NULL,
  `couponcode` varchar(50) DEFAULT NULL,
  `refOfferUrl` text,
  `refurl` text,
  `startdate` datetime DEFAULT NULL,
  `enddate` datetime DEFAULT NULL,
  `exclusivecode` tinyint(1) DEFAULT '0',
  `editorpicks` tinyint(1) DEFAULT '0',
  `extendedoffer` tinyint(1) DEFAULT '0',
  `extendedtitle` varchar(255) DEFAULT NULL,
  `extendedurl` varchar(255) DEFAULT NULL,
  `extendedmetadescription` text,
  `extendedfulldescription` longblob,
  `discount` text,
  `discountvalueType` enum('0','1','2') DEFAULT NULL,
  `authorId` bigint(20) DEFAULT NULL,
  `authorName` varchar(255) DEFAULT NULL,
  `shopid` bigint(20) DEFAULT NULL COMMENT 'FK to shop.id',
  `offerlogoid` bigint(20) DEFAULT NULL COMMENT 'FK to image.id',
  `maxlimit` enum('0','1') DEFAULT NULL,
  `maxcode` int(11) NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `userGenerated` tinyint(1) NOT NULL DEFAULT '0',
  `approved` enum('0','1') NOT NULL DEFAULT '0',
  `offline` int(11) NOT NULL DEFAULT '0',
  `tilesId` bigint(20) DEFAULT NULL,
  `shopexist` tinyint(1) DEFAULT NULL,
  `totalviewcount` bigint(20) DEFAULT NULL,
  `popularityCount` bigint(20) DEFAULT NULL,
  `couponcodetype` varchar(255) DEFAULT 'GN' COMMENT 'GN-general ,UN-unique',
  `extendedoffertitle` text,
  `offerUrl` text,
  `nickname` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `offerlogoid` (`offerlogoid`),
  KEY `shopid_idx` (`shopid`),
  KEY `offerlogoid_idx` (`offerlogoid`)
) ENGINE=InnoDB AUTO_INCREMENT=13591 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `offer`
--

LOCK TABLES `offer` WRITE;
/*!40000 ALTER TABLE `offer` DISABLE KEYS */;
/*!40000 ALTER TABLE `offer` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `offer_news`
--

DROP TABLE IF EXISTS `offer_news`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `offer_news` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `shopId` bigint(20) DEFAULT NULL,
  `offerId` bigint(20) DEFAULT NULL,
  `title` varchar(225) DEFAULT NULL,
  `url` varchar(225) DEFAULT NULL,
  `content` text,
  `linkstatus` varchar(255) DEFAULT NULL,
  `startdate` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=473 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `offer_news`
--

LOCK TABLES `offer_news` WRITE;
/*!40000 ALTER TABLE `offer_news` DISABLE KEYS */;
/*!40000 ALTER TABLE `offer_news` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `offer_tiles`
--

DROP TABLE IF EXISTS `offer_tiles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `offer_tiles` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `label` varchar(255) DEFAULT NULL,
  `offerId` bigint(20) DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `ext` varchar(100) DEFAULT NULL,
  `path` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `position` bigint(20) DEFAULT NULL,
  `deleted` tinyint(1) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `offerId` (`offerId`)
) ENGINE=InnoDB AUTO_INCREMENT=116 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `offer_tiles`
--

LOCK TABLES `offer_tiles` WRITE;
/*!40000 ALTER TABLE `offer_tiles` DISABLE KEYS */;
/*!40000 ALTER TABLE `offer_tiles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `page`
--

DROP TABLE IF EXISTS `page`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `page` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'PK',
  `pagetype` varchar(10) DEFAULT NULL,
  `pagetitle` varchar(255) DEFAULT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `permalink` varchar(255) DEFAULT NULL,
  `metatitle` text,
  `metadescription` text,
  `content` longblob,
  `publish` tinyint(1) DEFAULT NULL,
  `pagelock` tinyint(1) DEFAULT '0',
  `pageattributeid` bigint(20) DEFAULT NULL COMMENT 'Fk to page_attribute.id',
  `contentManagerId` bigint(20) NOT NULL,
  `contentManagerName` varchar(256) DEFAULT NULL,
  `enabletimeconstraint` tinyint(1) DEFAULT NULL,
  `timenumberofdays` bigint(20) DEFAULT NULL,
  `timetype` bigint(20) DEFAULT NULL COMMENT '0 - no option selected',
  `timemaxoffer` bigint(20) DEFAULT NULL,
  `timeorder` tinyint(1) DEFAULT '0',
  `enablewordconstraint` tinyint(1) DEFAULT NULL,
  `wordtitle` varchar(100) DEFAULT NULL,
  `wordmaxoffer` bigint(20) DEFAULT NULL,
  `publishdate` datetime NOT NULL,
  `wordorder` tinyint(1) DEFAULT NULL,
  `awardconstratint` tinyint(1) DEFAULT NULL,
  `awardtype` varchar(5) DEFAULT NULL,
  `awardmaxoffer` bigint(20) DEFAULT NULL,
  `awardorder` tinyint(1) DEFAULT NULL,
  `enableclickconstraint` tinyint(1) DEFAULT NULL,
  `numberofclicks` bigint(20) DEFAULT NULL,
  `clickmaxoffer` bigint(20) DEFAULT NULL,
  `clickorder` tinyint(1) DEFAULT NULL,
  `maxOffers` int(11) NOT NULL,
  `oderOffers` int(11) NOT NULL,
  `couponregular` tinyint(1) DEFAULT NULL,
  `couponeditorpick` tinyint(1) DEFAULT NULL,
  `couponexclusive` tinyint(1) DEFAULT NULL,
  `saleregular` tinyint(1) DEFAULT NULL,
  `saleeditorpick` tinyint(1) DEFAULT NULL,
  `saleexclusive` tinyint(1) DEFAULT NULL,
  `printableregular` tinyint(1) DEFAULT NULL,
  `printableeditorpick` tinyint(1) DEFAULT NULL,
  `printableexclusive` tinyint(1) DEFAULT NULL,
  `showpage` tinyint(1) DEFAULT NULL COMMENT 'Show as page when creating offers.',
  `logoid` bigint(20) DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `customheader` text,
  `pageHeaderImageId` bigint(20) DEFAULT NULL,
  `showsitemap` tinyint(4) NOT NULL DEFAULT '0',
  `offersCount` bigint(20) DEFAULT NULL,
  `showinmobilemenu` tinyint(4) NOT NULL DEFAULT '0',
  `pageHomeImageId` bigint(20) DEFAULT NULL,
  `subtitle` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pageattributeid_idx` (`pageattributeid`),
  KEY `pageHeaderImageId_foreign_key` (`pageHeaderImageId`),
  KEY `pageHomeImageId_foreign_key` (`pageHomeImageId`),
  CONSTRAINT `pageHeaderImageId_foreign_key` FOREIGN KEY (`pageHeaderImageId`) REFERENCES `image` (`id`) ON DELETE CASCADE,
  CONSTRAINT `pageHomeImageId_foreign_key` FOREIGN KEY (`pageHomeImageId`) REFERENCES `image` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=100 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `page`
--

LOCK TABLES `page` WRITE;
/*!40000 ALTER TABLE `page` DISABLE KEYS */;
/*!40000 ALTER TABLE `page` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `page_attribute`
--

DROP TABLE IF EXISTS `page_attribute`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `page_attribute` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'PK',
  `name` varchar(255) DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `page_attribute`
--

LOCK TABLES `page_attribute` WRITE;
/*!40000 ALTER TABLE `page_attribute` DISABLE KEYS */;
/*!40000 ALTER TABLE `page_attribute` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `page_widgets`
--

DROP TABLE IF EXISTS `page_widgets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `page_widgets` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `widgetId` bigint(20) DEFAULT NULL,
  `widget_type` varchar(255) DEFAULT NULL,
  `position` bigint(20) DEFAULT NULL,
  `deleted` tinyint(1) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=101 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `page_widgets`
--

LOCK TABLES `page_widgets` WRITE;
/*!40000 ALTER TABLE `page_widgets` DISABLE KEYS */;
/*!40000 ALTER TABLE `page_widgets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `popular_articles`
--

DROP TABLE IF EXISTS `popular_articles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `popular_articles` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `articleId` bigint(20) DEFAULT NULL,
  `position` bigint(20) DEFAULT NULL,
  `deleted` tinyint(1) DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `popular_articles`
--

LOCK TABLES `popular_articles` WRITE;
/*!40000 ALTER TABLE `popular_articles` DISABLE KEYS */;
/*!40000 ALTER TABLE `popular_articles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `popular_category`
--

DROP TABLE IF EXISTS `popular_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `popular_category` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'PK',
  `type` varchar(255) DEFAULT NULL,
  `position` bigint(20) DEFAULT NULL COMMENT 'Holds the shop position among popular category list',
  `status` tinyint(1) DEFAULT NULL,
  `categoryid` bigint(20) DEFAULT NULL COMMENT 'FK to category.id',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `total_offers` int(11) NOT NULL DEFAULT '0',
  `total_coupons` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `categoryid` (`categoryid`),
  KEY `categoryid_idx` (`categoryid`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `popular_category`
--

LOCK TABLES `popular_category` WRITE;
/*!40000 ALTER TABLE `popular_category` DISABLE KEYS */;
/*!40000 ALTER TABLE `popular_category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `popular_code`
--

DROP TABLE IF EXISTS `popular_code`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `popular_code` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `type` varchar(255) DEFAULT NULL,
  `position` bigint(20) DEFAULT NULL COMMENT 'Holds the code position among popular code list',
  `status` tinyint(1) DEFAULT '0' COMMENT '1 ? enable , 0 ? disable',
  `offerid` bigint(20) DEFAULT NULL COMMENT 'FK to offer.id',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `offerid` (`offerid`),
  KEY `offerid_idx` (`offerid`)
) ENGINE=InnoDB AUTO_INCREMENT=69 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `popular_code`
--

LOCK TABLES `popular_code` WRITE;
/*!40000 ALTER TABLE `popular_code` DISABLE KEYS */;
/*!40000 ALTER TABLE `popular_code` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `popular_shop`
--

DROP TABLE IF EXISTS `popular_shop`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `popular_shop` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'PK',
  `type` varchar(255) DEFAULT NULL,
  `position` bigint(20) DEFAULT NULL,
  `status` tinyint(1) DEFAULT NULL,
  `shopid` bigint(20) DEFAULT NULL COMMENT 'FK to image.id',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `shopid` (`shopid`),
  KEY `shopid_idx` (`shopid`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `popular_shop`
--

LOCK TABLES `popular_shop` WRITE;
/*!40000 ALTER TABLE `popular_shop` DISABLE KEYS */;
/*!40000 ALTER TABLE `popular_shop` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `popular_vouchercodes`
--

DROP TABLE IF EXISTS `popular_vouchercodes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `popular_vouchercodes` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'PK',
  `type` varchar(255) DEFAULT NULL,
  `position` bigint(20) DEFAULT NULL COMMENT 'Holds the shop position among popular offer list',
  `status` tinyint(1) DEFAULT NULL,
  `vaoucherofferid` bigint(20) DEFAULT NULL COMMENT 'FK to offer.id',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `vaoucherofferid` (`vaoucherofferid`),
  KEY `vaoucherofferid_idx` (`vaoucherofferid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `popular_vouchercodes`
--

LOCK TABLES `popular_vouchercodes` WRITE;
/*!40000 ALTER TABLE `popular_vouchercodes` DISABLE KEYS */;
/*!40000 ALTER TABLE `popular_vouchercodes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ref_article_category`
--

DROP TABLE IF EXISTS `ref_article_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ref_article_category` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'PK',
  `articleid` bigint(20) NOT NULL COMMENT 'FK to articles.id',
  `relatedcategoryid` bigint(20) NOT NULL COMMENT 'FK to articlecatgory.id',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=373 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ref_article_category`
--

LOCK TABLES `ref_article_category` WRITE;
/*!40000 ALTER TABLE `ref_article_category` DISABLE KEYS */;
/*!40000 ALTER TABLE `ref_article_category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ref_article_store`
--

DROP TABLE IF EXISTS `ref_article_store`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ref_article_store` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'PK',
  `articleid` bigint(20) NOT NULL COMMENT 'FK to article.id',
  `storeid` bigint(20) NOT NULL COMMENT 'FK to shop.id',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `articleid` (`articleid`),
  KEY `storeid` (`storeid`),
  KEY `article_shop_id_idx` (`articleid`,`storeid`)
) ENGINE=InnoDB AUTO_INCREMENT=703 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ref_article_store`
--

LOCK TABLES `ref_article_store` WRITE;
/*!40000 ALTER TABLE `ref_article_store` DISABLE KEYS */;
/*!40000 ALTER TABLE `ref_article_store` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ref_articlecategory_relatedcategory`
--

DROP TABLE IF EXISTS `ref_articlecategory_relatedcategory`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ref_articlecategory_relatedcategory` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'PK',
  `articlecategoryid` bigint(20) NOT NULL COMMENT 'FK to articlecategory.id',
  `relatedcategoryid` bigint(20) NOT NULL COMMENT 'FK to category.id',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=112 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ref_articlecategory_relatedcategory`
--

LOCK TABLES `ref_articlecategory_relatedcategory` WRITE;
/*!40000 ALTER TABLE `ref_articlecategory_relatedcategory` DISABLE KEYS */;
/*!40000 ALTER TABLE `ref_articlecategory_relatedcategory` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ref_excludedkeyword_shop`
--

DROP TABLE IF EXISTS `ref_excludedkeyword_shop`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ref_excludedkeyword_shop` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `keywordid` int(11) NOT NULL,
  `keywordname` varchar(256) DEFAULT NULL,
  `shopid` int(11) NOT NULL,
  `deleted` int(1) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `keyword_shop_id_idx` (`keywordid`,`shopid`)
) ENGINE=InnoDB AUTO_INCREMENT=220 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ref_excludedkeyword_shop`
--

LOCK TABLES `ref_excludedkeyword_shop` WRITE;
/*!40000 ALTER TABLE `ref_excludedkeyword_shop` DISABLE KEYS */;
/*!40000 ALTER TABLE `ref_excludedkeyword_shop` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ref_offer_category`
--

DROP TABLE IF EXISTS `ref_offer_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ref_offer_category` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `offerid` bigint(20) NOT NULL COMMENT 'FK to offer.id',
  `categoryid` bigint(20) NOT NULL COMMENT 'FK to category.id',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `offerid_idx` (`offerid`),
  KEY `categoryid_idx` (`categoryid`),
  KEY `offer_category_id_idx` (`categoryid`,`offerid`)
) ENGINE=InnoDB AUTO_INCREMENT=21419 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ref_offer_category`
--

LOCK TABLES `ref_offer_category` WRITE;
/*!40000 ALTER TABLE `ref_offer_category` DISABLE KEYS */;
/*!40000 ALTER TABLE `ref_offer_category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ref_offer_page`
--

DROP TABLE IF EXISTS `ref_offer_page`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ref_offer_page` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `pageid` bigint(20) NOT NULL COMMENT 'FK to page.id',
  `offerid` bigint(20) NOT NULL COMMENT 'FK to offer.id',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `offerid_idx` (`offerid`),
  KEY `pageid_idx` (`pageid`),
  KEY `offer_page_id_idx` (`pageid`,`offerid`)
) ENGINE=InnoDB AUTO_INCREMENT=5690 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ref_offer_page`
--

LOCK TABLES `ref_offer_page` WRITE;
/*!40000 ALTER TABLE `ref_offer_page` DISABLE KEYS */;
/*!40000 ALTER TABLE `ref_offer_page` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ref_page_widget`
--

DROP TABLE IF EXISTS `ref_page_widget`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ref_page_widget` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `pageid` bigint(20) NOT NULL COMMENT 'FK to page.id',
  `widgetid` bigint(20) NOT NULL COMMENT 'FK to widget.id',
  `stauts` tinyint(1) DEFAULT '0' COMMENT 'if status true than widget is displayed on page',
  `position` bigint(20) DEFAULT NULL COMMENT 'display in which order',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `pageid_idx` (`pageid`),
  KEY `widgetid_idx` (`widgetid`)
) ENGINE=InnoDB AUTO_INCREMENT=857 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ref_page_widget`
--

LOCK TABLES `ref_page_widget` WRITE;
/*!40000 ALTER TABLE `ref_page_widget` DISABLE KEYS */;
/*!40000 ALTER TABLE `ref_page_widget` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ref_shop_category`
--

DROP TABLE IF EXISTS `ref_shop_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ref_shop_category` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `shopid` bigint(20) DEFAULT NULL COMMENT 'FK to shop.id',
  `categoryid` bigint(20) DEFAULT NULL COMMENT 'FK to category.id',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `shopid_idx` (`shopid`),
  KEY `categoryid_idx` (`categoryid`),
  KEY `shop_category_id_idx` (`shopid`,`categoryid`)
) ENGINE=InnoDB AUTO_INCREMENT=10742 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ref_shop_category`
--

LOCK TABLES `ref_shop_category` WRITE;
/*!40000 ALTER TABLE `ref_shop_category` DISABLE KEYS */;
/*!40000 ALTER TABLE `ref_shop_category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ref_shop_relatedshop`
--

DROP TABLE IF EXISTS `ref_shop_relatedshop`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ref_shop_relatedshop` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `shopId` bigint(20) NOT NULL COMMENT 'shop id forgien key to shop id',
  `relatedshopId` bigint(20) NOT NULL COMMENT 'related shop id forgien key to shop id',
  `position` int(5) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `shop_relatedshop_id_idx` (`shopId`,`relatedshopId`),
  KEY `shop_relatedshop_idx` (`shopId`,`relatedshopId`)
) ENGINE=InnoDB AUTO_INCREMENT=9138 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ref_shop_relatedshop`
--

LOCK TABLES `ref_shop_relatedshop` WRITE;
/*!40000 ALTER TABLE `ref_shop_relatedshop` DISABLE KEYS */;
/*!40000 ALTER TABLE `ref_shop_relatedshop` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `route_permalink`
--

DROP TABLE IF EXISTS `route_permalink`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `route_permalink` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `permalink` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `exactlink` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `permalink_idx` (`permalink`)
) ENGINE=InnoDB AUTO_INCREMENT=1601 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `route_permalink`
--

LOCK TABLES `route_permalink` WRITE;
/*!40000 ALTER TABLE `route_permalink` DISABLE KEYS */;
/*!40000 ALTER TABLE `route_permalink` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `route_redirect`
--

DROP TABLE IF EXISTS `route_redirect`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `route_redirect` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `orignalurl` text,
  `redirectto` text,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `orignalurl_idx` (`orignalurl`(255))
) ENGINE=InnoDB AUTO_INCREMENT=310 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `route_redirect`
--

LOCK TABLES `route_redirect` WRITE;
/*!40000 ALTER TABLE `route_redirect` DISABLE KEYS */;
/*!40000 ALTER TABLE `route_redirect` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `seen_in`
--

DROP TABLE IF EXISTS `seen_in`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `seen_in` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'PK',
  `name` varchar(50) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `toolltip` text,
  `status` tinyint(1) DEFAULT '1' COMMENT '1 = enable , 0 = disable',
  `logoid` bigint(20) DEFAULT NULL COMMENT 'FK to image.id',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `alttext` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `logoid` (`logoid`),
  KEY `logoid_idx` (`logoid`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `seen_in`
--

LOCK TABLES `seen_in` WRITE;
/*!40000 ALTER TABLE `seen_in` DISABLE KEYS */;
/*!40000 ALTER TABLE `seen_in` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `settings` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `value` text,
  `status` tinyint(1) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settings`
--

LOCK TABLES `settings` WRITE;
/*!40000 ALTER TABLE `settings` DISABLE KEYS */;
/*!40000 ALTER TABLE `settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shop`
--

DROP TABLE IF EXISTS `shop`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shop` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'PK',
  `name` varchar(255) DEFAULT NULL,
  `permalink` varchar(255) DEFAULT NULL,
  `metadescription` text,
  `usergenratedcontent` enum('0','1') DEFAULT NULL,
  `notes` text,
  `deeplink` varchar(255) DEFAULT NULL,
  `deeplinkstatus` tinyint(1) DEFAULT NULL,
  `refurl` text,
  `actualurl` text,
  `affliateprogram` tinyint(1) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `subTitle` varchar(255) DEFAULT NULL,
  `overritetitle` varchar(255) DEFAULT NULL,
  `overritesubtitle` varchar(255) DEFAULT NULL,
  `overritebrowsertitle` varchar(255) DEFAULT NULL,
  `shoptext` longblob,
  `views` bigint(20) DEFAULT NULL,
  `howtouse` tinyint(1) NOT NULL DEFAULT '0',
  `Deliverytime` varchar(255) DEFAULT NULL,
  `returnPolicy` varchar(255) DEFAULT NULL,
  `freeDelivery` enum('0','1','2','3') DEFAULT NULL,
  `deliveryCost` varchar(255) DEFAULT NULL,
  `status` tinyint(1) DEFAULT NULL,
  `offlinesicne` datetime DEFAULT NULL,
  `accoutmanagerid` bigint(20) DEFAULT NULL COMMENT 'associated account manager id',
  `accountManagerName` varchar(255) DEFAULT NULL,
  `contentmanagerid` bigint(20) DEFAULT NULL COMMENT 'associated content  manager id',
  `contentManagerName` varchar(255) DEFAULT NULL,
  `logoid` bigint(20) DEFAULT NULL COMMENT 'FK to image.id',
  `screenshotid` bigint(20) NOT NULL,
  `howtousesmallimageid` bigint(20) DEFAULT NULL COMMENT 'FK to image.id',
  `howtousebigimageid` bigint(20) DEFAULT NULL COMMENT 'FK to image.id',
  `affliatenetworkid` bigint(20) DEFAULT NULL COMMENT 'FK to affliate_network.id',
  `howtousepageid` bigint(20) DEFAULT NULL,
  `keywordlink` varchar(255) DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `howtoTitle` text,
  `howtoSubtitle` text,
  `howtoMetaTitle` text,
  `howtoMetaDescription` longblob,
  `ideal` tinyint(1) DEFAULT NULL,
  `qShops` tinyint(1) DEFAULT NULL,
  `freeReturns` tinyint(1) DEFAULT NULL,
  `pickupPoints` tinyint(1) DEFAULT NULL,
  `mobileShop` tinyint(1) DEFAULT NULL,
  `service` tinyint(1) DEFAULT NULL,
  `serviceNumber` text,
  `discussions` tinyint(1) DEFAULT NULL,
  `displayExtraProperties` tinyint(1) NOT NULL DEFAULT '1',
  `showsignupoption` tinyint(1) NOT NULL DEFAULT '0',
  `addtosearch` tinyint(1) NOT NULL DEFAULT '0',
  `customheader` text,
  `totalviewcount` bigint(20) DEFAULT NULL,
  `showSimliarShops` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'if true then displays same shops as well as shop related to same category',
  `showchains` tinyint(1) NOT NULL DEFAULT '0',
  `chainItemId` bigint(20) DEFAULT NULL,
  `chainId` bigint(20) DEFAULT NULL,
  `strictconfirmation` tinyint(1) NOT NULL DEFAULT '0',
  `howToIntroductionText` longblob,
  `brandingcss` text,
  `lightboxfirsttext` varchar(255) DEFAULT NULL,
  `lightboxsecondtext` varchar(255) DEFAULT NULL,
  `customtext` longblob,
  `showcustomtext` tinyint(1) NOT NULL DEFAULT '0',
  `customtextposition` bigint(20) DEFAULT NULL,
  `lastSevendayClickouts` bigint(20) DEFAULT NULL,
  `shopAndOfferClickouts` bigint(20) DEFAULT NULL,
  `shopsViewedIds` varchar(100) DEFAULT NULL,
  `howtoSubSubTitle` varchar(255) DEFAULT NULL,
  `moretextforshop` longblob,
  `howtoguideslug` varchar(100) DEFAULT NULL,
  `futurecode` tinyint(1) DEFAULT NULL,
  `code_alert_send_date` datetime DEFAULT NULL,
  `featuredtext` varchar(255) DEFAULT NULL,
  `featuredtextdate` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `logoid` (`logoid`),
  UNIQUE KEY `howtousesmallimageid` (`howtousesmallimageid`),
  UNIQUE KEY `howtousebigimageid` (`howtousebigimageid`) USING BTREE,
  KEY `logoid_idx` (`logoid`),
  KEY `affliatenetworkid_idx` (`affliatenetworkid`),
  KEY `howtousepageid_idx` (`howtousepageid`)
) ENGINE=InnoDB AUTO_INCREMENT=1112 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shop`
--

LOCK TABLES `shop` WRITE;
/*!40000 ALTER TABLE `shop` DISABLE KEYS */;
/*!40000 ALTER TABLE `shop` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shop_howto_chapter`
--

DROP TABLE IF EXISTS `shop_howto_chapter`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shop_howto_chapter` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `shopId` bigint(20) DEFAULT NULL,
  `chapterTitle` text,
  `chapterDescription` longblob,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1130 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shop_howto_chapter`
--

LOCK TABLES `shop_howto_chapter` WRITE;
/*!40000 ALTER TABLE `shop_howto_chapter` DISABLE KEYS */;
/*!40000 ALTER TABLE `shop_howto_chapter` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shopreasons`
--

DROP TABLE IF EXISTS `shopreasons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shopreasons` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `shopid` bigint(20) DEFAULT NULL,
  `fieldname` varchar(100) DEFAULT NULL,
  `fieldvalue` text,
  `deleted` tinyint(1) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1367 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shopreasons`
--

LOCK TABLES `shopreasons` WRITE;
/*!40000 ALTER TABLE `shopreasons` DISABLE KEYS */;
/*!40000 ALTER TABLE `shopreasons` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shopviewcount`
--

DROP TABLE IF EXISTS `shopviewcount`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shopviewcount` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `shopid` bigint(20) DEFAULT NULL,
  `onclick` bigint(20) DEFAULT NULL,
  `onload` bigint(20) DEFAULT NULL,
  `ip` varchar(255) DEFAULT NULL,
  `deleted` tinyint(1) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `shopid_idx` (`shopid`)
) ENGINE=InnoDB AUTO_INCREMENT=48862 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shopviewcount`
--

LOCK TABLES `shopviewcount` WRITE;
/*!40000 ALTER TABLE `shopviewcount` DISABLE KEYS */;
/*!40000 ALTER TABLE `shopviewcount` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `signupcodes`
--

DROP TABLE IF EXISTS `signupcodes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `signupcodes` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'PK',
  `entered_uid` bigint(20) NOT NULL,
  `code` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `signupcodes`
--

LOCK TABLES `signupcodes` WRITE;
/*!40000 ALTER TABLE `signupcodes` DISABLE KEYS */;
/*!40000 ALTER TABLE `signupcodes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `signupfavoriteshop`
--

DROP TABLE IF EXISTS `signupfavoriteshop`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `signupfavoriteshop` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'PK',
  `entered_uid` bigint(20) NOT NULL,
  `store_id` bigint(20) NOT NULL COMMENT 'FK to shop.id',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `store_id` (`store_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `signupfavoriteshop`
--

LOCK TABLES `signupfavoriteshop` WRITE;
/*!40000 ALTER TABLE `signupfavoriteshop` DISABLE KEYS */;
/*!40000 ALTER TABLE `signupfavoriteshop` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `signupmaxaccount`
--

DROP TABLE IF EXISTS `signupmaxaccount`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `signupmaxaccount` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'PK',
  `entered_uid` int(20) NOT NULL,
  `no_of_acc` varchar(255) DEFAULT NULL,
  `status` int(20) NOT NULL,
  `email_confirmation` tinyint(1) NOT NULL DEFAULT '0',
  `email_header` blob NOT NULL,
  `email_footer` blob NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `max_account` bigint(20) DEFAULT NULL,
  `emailperlocale` text NOT NULL,
  `sendername` varchar(255) NOT NULL DEFAULT '',
  `emailsubject` varchar(255) NOT NULL DEFAULT '',
  `testemail` varchar(255) NOT NULL DEFAULT '',
  `testimonial1` text,
  `testimonial2` text,
  `testimonial3` text,
  `showtestimonial` tinyint(1) NOT NULL DEFAULT '0',
  `homepagebanner_name` varchar(255) DEFAULT NULL,
  `homepagebanner_path` varchar(255) DEFAULT NULL,
  `homepage_widget_banner_name` varchar(255) DEFAULT NULL,
  `homepage_widget_banner_path` varchar(255) DEFAULT NULL,
  `newletter_is_scheduled` tinyint(1) DEFAULT '0' COMMENT '1-scheduled ,0-manual',
  `newletter_scheduled_time` datetime DEFAULT '2014-01-23 12:11:28' COMMENT 'newsletter scheduled timestamp',
  `newsletter_sent_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `signupmaxaccount`
--

LOCK TABLES `signupmaxaccount` WRITE;
/*!40000 ALTER TABLE `signupmaxaccount` DISABLE KEYS */;
/*!40000 ALTER TABLE `signupmaxaccount` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `special`
--

DROP TABLE IF EXISTS `special`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `special` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'PK',
  `title` text,
  `description` longblob,
  `status` tinyint(1) DEFAULT '1',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `special`
--

LOCK TABLES `special` WRITE;
/*!40000 ALTER TABLE `special` DISABLE KEYS */;
/*!40000 ALTER TABLE `special` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `special_list`
--

DROP TABLE IF EXISTS `special_list`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `special_list` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'PK',
  `type` varchar(255) DEFAULT NULL,
  `position` bigint(20) DEFAULT NULL COMMENT 'Holds the shop position among popular offer list',
  `status` tinyint(1) DEFAULT NULL,
  `specialpageid` bigint(20) DEFAULT NULL COMMENT 'FK to offer.id',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `total_offers` int(11) NOT NULL DEFAULT '0',
  `total_coupons` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `specialofferid` (`specialpageid`),
  KEY `specialofferid_idx` (`specialpageid`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `special_list`
--

LOCK TABLES `special_list` WRITE;
/*!40000 ALTER TABLE `special_list` DISABLE KEYS */;
/*!40000 ALTER TABLE `special_list` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `special_pages_offers`
--

DROP TABLE IF EXISTS `special_pages_offers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `special_pages_offers` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `offerId` bigint(20) DEFAULT NULL,
  `pageId` bigint(20) DEFAULT NULL,
  `position` bigint(20) DEFAULT NULL,
  `deleted` tinyint(1) DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=401 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `special_pages_offers`
--

LOCK TABLES `special_pages_offers` WRITE;
/*!40000 ALTER TABLE `special_pages_offers` DISABLE KEYS */;
/*!40000 ALTER TABLE `special_pages_offers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `term_and_condition`
--

DROP TABLE IF EXISTS `term_and_condition`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `term_and_condition` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `content` text,
  `offerid` bigint(20) DEFAULT NULL COMMENT 'FK to offer.id',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `offerid_idx` (`offerid`)
) ENGINE=InnoDB AUTO_INCREMENT=4906 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `term_and_condition`
--

LOCK TABLES `term_and_condition` WRITE;
/*!40000 ALTER TABLE `term_and_condition` DISABLE KEYS */;
/*!40000 ALTER TABLE `term_and_condition` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `translations`
--

DROP TABLE IF EXISTS `translations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `translations` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `translationKey` text,
  `translation` text,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=106 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `translations`
--

LOCK TABLES `translations` WRITE;
/*!40000 ALTER TABLE `translations` DISABLE KEYS */;
/*!40000 ALTER TABLE `translations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `varnish`
--

DROP TABLE IF EXISTS `varnish`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `varnish` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `url` text,
  `status` text,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `refresh_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18684 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `varnish`
--

LOCK TABLES `varnish` WRITE;
/*!40000 ALTER TABLE `varnish` DISABLE KEYS */;
/*!40000 ALTER TABLE `varnish` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `view_count`
--

DROP TABLE IF EXISTS `view_count`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `view_count` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `loadtime` bigint(20) DEFAULT NULL,
  `onclick` bigint(20) DEFAULT '0',
  `onload` bigint(20) NOT NULL DEFAULT '0',
  `onhover` bigint(20) DEFAULT NULL,
  `ip` varchar(255) DEFAULT NULL,
  `offerid` bigint(20) DEFAULT NULL COMMENT 'FK to offer.id',
  `memberid` bigint(20) DEFAULT NULL COMMENT 'FK of member who view offer (used for future reference)',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `counted` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `offerid_idx` (`offerid`),
  KEY `offer_click_count_idx` (`offerid`,`onclick`,`counted`),
  KEY `memberid_idx` (`memberid`)
) ENGINE=InnoDB AUTO_INCREMENT=1307742 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `view_count`
--

LOCK TABLES `view_count` WRITE;
/*!40000 ALTER TABLE `view_count` DISABLE KEYS */;
/*!40000 ALTER TABLE `view_count` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `visitor`
--

DROP TABLE IF EXISTS `visitor`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `visitor` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'PK',
  `firstname` text,
  `lastname` text,
  `email` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `username` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `pwd` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `status` tinyint(1) DEFAULT '1',
  `imageid` bigint(20) DEFAULT NULL COMMENT 'FK to image.id',
  `gender` tinyint(4) NOT NULL DEFAULT '0',
  `dateofbirth` date DEFAULT NULL,
  `postalcode` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `weeklynewsletter` tinyint(4) NOT NULL DEFAULT '0',
  `fashionnewsletter` tinyint(4) NOT NULL DEFAULT '0',
  `travelnewsletter` tinyint(4) NOT NULL DEFAULT '0',
  `codealert` tinyint(4) NOT NULL DEFAULT '0',
  `createdby` bigint(20) DEFAULT NULL COMMENT 'FK to user.id',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `currentlogin` datetime NOT NULL COMMENT 'current login timestamp',
  `lastlogin` datetime NOT NULL COMMENT 'last login timestamp',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `interested` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `profile_img` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `active_codeid` varchar(255) CHARACTER SET utf8 NOT NULL,
  `changepasswordrequest` tinyint(4) NOT NULL COMMENT 'true=1,false=0',
  `active` tinyint(4) NOT NULL,
  `code_alert_send_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `imageid_idx` (`imageid`),
  KEY `createdby_idx` (`createdby`)
) ENGINE=InnoDB AUTO_INCREMENT=3671 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `visitor`
--

LOCK TABLES `visitor` WRITE;
/*!40000 ALTER TABLE `visitor` DISABLE KEYS */;
/*!40000 ALTER TABLE `visitor` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `visitor_keyword`
--

DROP TABLE IF EXISTS `visitor_keyword`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `visitor_keyword` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `keyword` varchar(255) DEFAULT NULL,
  `visitorId` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `vis_cascade` (`visitorId`),
  KEY `visitorId_idx` (`visitorId`),
  CONSTRAINT `vis_cascade` FOREIGN KEY (`visitorId`) REFERENCES `visitor` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `visitor_keyword`
--

LOCK TABLES `visitor_keyword` WRITE;
/*!40000 ALTER TABLE `visitor_keyword` DISABLE KEYS */;
/*!40000 ALTER TABLE `visitor_keyword` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `votes`
--

DROP TABLE IF EXISTS `votes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `votes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `offerId` int(11) NOT NULL,
  `ipaddress` varchar(200) DEFAULT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `vote` varchar(200) DEFAULT NULL,
  `moneySaved` double NOT NULL,
  `product` varchar(255) DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted` tinyint(4) NOT NULL DEFAULT '0',
  `visitorid` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `offer_id_idx` (`offerId`)
) ENGINE=InnoDB AUTO_INCREMENT=144 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `votes`
--

LOCK TABLES `votes` WRITE;
/*!40000 ALTER TABLE `votes` DISABLE KEYS */;
/*!40000 ALTER TABLE `votes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `widget`
--

DROP TABLE IF EXISTS `widget`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `widget` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `title` text,
  `slug` varchar(255) DEFAULT NULL,
  `content` longblob,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `userdefined` tinyint(1) DEFAULT '0',
  `showwithdefault` tinyint(1) DEFAULT '0',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `function_name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `widget`
--

LOCK TABLES `widget` WRITE;
/*!40000 ALTER TABLE `widget` DISABLE KEYS */;
/*!40000 ALTER TABLE `widget` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `widget_location`
--

DROP TABLE IF EXISTS `widget_location`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `widget_location` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `position` bigint(20) DEFAULT NULL,
  `pagetype` varchar(100) DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL,
  `relatedid` bigint(20) DEFAULT NULL,
  `widgettype` varchar(100) DEFAULT NULL,
  `deleted` tinyint(1) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `widget_location`
--

LOCK TABLES `widget_location` WRITE;
/*!40000 ALTER TABLE `widget_location` DISABLE KEYS */;
/*!40000 ALTER TABLE `widget_location` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2015-07-02 16:07:19
