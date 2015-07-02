-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jul 01, 2015 at 04:46 PM
-- Server version: 5.5.43-0ubuntu0.14.04.1
-- PHP Version: 5.5.9-1ubuntu4.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `flipit_in`
--

-- --------------------------------------------------------

--
-- Table structure for table `about`
--

CREATE TABLE IF NOT EXISTS `about` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) DEFAULT NULL,
  `content` longblob,
  `status` tinyint(1) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=15 ;

-- --------------------------------------------------------

--
-- Table structure for table `adminfavoriteshp`
--

CREATE TABLE IF NOT EXISTS `adminfavoriteshp` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'PK',
  `shopId` int(11) NOT NULL COMMENT 'FK to shop.id',
  `userId` int(11) NOT NULL COMMENT 'FK to user.id',
  PRIMARY KEY (`id`),
  KEY `shopId_idx` (`shopId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=177 ;

-- --------------------------------------------------------

--
-- Table structure for table `affliate_network`
--

CREATE TABLE IF NOT EXISTS `affliate_network` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'PK',
  `name` text,
  `status` tinyint(1) DEFAULT NULL,
  `replacewithid` bigint(20) DEFAULT NULL COMMENT 'FK to affliate_network.id , Defines a network is merged or not',
  `subId` text,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `replacewithid_idx` (`replacewithid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=30 ;

-- --------------------------------------------------------

--
-- Table structure for table `articlecategory`
--

CREATE TABLE IF NOT EXISTS `articlecategory` (
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
  KEY `categoryiconid_idx` (`categoryiconid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=12 ;

-- --------------------------------------------------------

--
-- Table structure for table `articles`
--

CREATE TABLE IF NOT EXISTS `articles` (
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
  KEY `thumbnailid` (`thumbnailid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=32 ;

-- --------------------------------------------------------

--
-- Table structure for table `articleviewcount`
--

CREATE TABLE IF NOT EXISTS `articleviewcount` (
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4814 ;

-- --------------------------------------------------------

--
-- Table structure for table `article_chapter`
--

CREATE TABLE IF NOT EXISTS `article_chapter` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `articleId` bigint(20) DEFAULT NULL,
  `title` text,
  `content` longblob,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1945 ;

-- --------------------------------------------------------

--
-- Table structure for table `categories_offers`
--

CREATE TABLE IF NOT EXISTS `categories_offers` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `offerId` bigint(20) DEFAULT NULL,
  `categoryId` bigint(20) DEFAULT NULL,
  `position` bigint(20) DEFAULT NULL,
  `deleted` tinyint(1) DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE IF NOT EXISTS `category` (
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
  KEY `categoryHeaderImageId_foreign_key` (`categoryHeaderImageId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=45 ;

-- --------------------------------------------------------

--
-- Table structure for table `code_alert_queue`
--

CREATE TABLE IF NOT EXISTS `code_alert_queue` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `offerId` bigint(20) DEFAULT NULL,
  `shopId` bigint(20) DEFAULT NULL,
  `deleted` tinyint(1) DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=317 ;

-- --------------------------------------------------------

--
-- Table structure for table `code_alert_settings`
--

CREATE TABLE IF NOT EXISTS `code_alert_settings` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `email_subject` varchar(255) DEFAULT NULL,
  `email_header` longblob,
  `deleted` tinyint(1) DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Table structure for table `code_alert_visitors`
--

CREATE TABLE IF NOT EXISTS `code_alert_visitors` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `offerId` bigint(20) DEFAULT NULL,
  `visitorId` bigint(20) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10351 ;

-- --------------------------------------------------------

--
-- Table structure for table `conversions`
--

CREATE TABLE IF NOT EXISTS `conversions` (
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=617960 ;

-- --------------------------------------------------------

--
-- Table structure for table `couponcode`
--

CREATE TABLE IF NOT EXISTS `couponcode` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `offerid` bigint(20) DEFAULT NULL,
  `code` varchar(255) DEFAULT NULL,
  `status` tinyint(1) DEFAULT '1' COMMENT '1-available ,0-used',
  PRIMARY KEY (`id`),
  KEY `offerid_idx` (`offerid`),
  KEY `couponcode_idx` (`offerid`,`status`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=765 ;

-- --------------------------------------------------------

--
-- Table structure for table `dashboard`
--

CREATE TABLE IF NOT EXISTS `dashboard` (
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Table structure for table `disqus_comments`
--

CREATE TABLE IF NOT EXISTS `disqus_comments` (
  `id` bigint(20) NOT NULL,
  `thread_id` bigint(20) DEFAULT NULL,
  `author_name` varchar(255) DEFAULT NULL,
  `comment` text,
  `created` bigint(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `disqus_thread`
--

CREATE TABLE IF NOT EXISTS `disqus_thread` (
  `id` bigint(20) NOT NULL,
  `title` varchar(255) NOT NULL,
  `link` varchar(255) NOT NULL,
  `created` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `editorwidget`
--

CREATE TABLE IF NOT EXISTS `editorwidget` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `type` varchar(255) DEFAULT NULL,
  `description` text,
  `subtitle` varchar(255) DEFAULT NULL,
  `editorId` bigint(20) DEFAULT NULL,
  `status` tinyint(1) DEFAULT '1' COMMENT '1-on ,0-off',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `editor_ballon_text`
--

CREATE TABLE IF NOT EXISTS `editor_ballon_text` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `shopid` bigint(20) DEFAULT NULL,
  `ballontext` varchar(255) DEFAULT NULL,
  `deleted` tinyint(1) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=12 ;

-- --------------------------------------------------------

--
-- Table structure for table `emails`
--

CREATE TABLE IF NOT EXISTS `emails` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `emailsettings`
--

CREATE TABLE IF NOT EXISTS `emailsettings` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `email` text,
  `name` text,
  `locale` text,
  `timezone` text,
  `deleted` tinyint(1) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `email_light_box`
--

CREATE TABLE IF NOT EXISTS `email_light_box` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'PK',
  `title` varchar(100) DEFAULT NULL,
  `content` longblob,
  `status` tinyint(1) DEFAULT '0',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `email_subscribe`
--

CREATE TABLE IF NOT EXISTS `email_subscribe` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) DEFAULT NULL,
  `send` int(20) NOT NULL,
  `deleted` int(20) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `excluded_keyword`
--

CREATE TABLE IF NOT EXISTS `excluded_keyword` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `keyword` varchar(255) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `action` enum('0','1') DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=97 ;

-- --------------------------------------------------------

--
-- Table structure for table `favorite_offer`
--

CREATE TABLE IF NOT EXISTS `favorite_offer` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `offerId` bigint(20) NOT NULL,
  `visitorId` bigint(20) NOT NULL,
  `deleted` tinyint(4) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `offer_visitor_id_idx` (`offerId`,`visitorId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `favorite_shop`
--

CREATE TABLE IF NOT EXISTS `favorite_shop` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `shopId` bigint(20) NOT NULL,
  `visitorId` bigint(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted` int(11) NOT NULL DEFAULT '0',
  `code_alert_send_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fav_cascade` (`visitorId`),
  KEY `shop_visitor_id_idx` (`shopId`,`visitorId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5413 ;

-- --------------------------------------------------------

--
-- Table structure for table `footer`
--

CREATE TABLE IF NOT EXISTS `footer` (
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Table structure for table `image`
--

CREATE TABLE IF NOT EXISTS `image` (
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2477 ;

-- --------------------------------------------------------

--
-- Table structure for table `interestingcategory`
--

CREATE TABLE IF NOT EXISTS `interestingcategory` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'PK',
  `userId` int(11) NOT NULL COMMENT 'FK to user.id',
  `categoryid` bigint(20) NOT NULL COMMENT 'FK to category.id',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=159 ;

-- --------------------------------------------------------

--
-- Table structure for table `locale_settings`
--

CREATE TABLE IF NOT EXISTS `locale_settings` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `locale` varchar(10) DEFAULT NULL,
  `timezone` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Table structure for table `mainmenu`
--

CREATE TABLE IF NOT EXISTS `mainmenu` (
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

-- --------------------------------------------------------

--
-- Table structure for table `media`
--

CREATE TABLE IF NOT EXISTS `media` (
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=21 ;

-- --------------------------------------------------------

--
-- Table structure for table `menu`
--

CREATE TABLE IF NOT EXISTS `menu` (
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=68 ;

-- --------------------------------------------------------

--
-- Table structure for table `migration_version`
--

CREATE TABLE IF NOT EXISTS `migration_version` (
  `version` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `moneysaving`
--

CREATE TABLE IF NOT EXISTS `moneysaving` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `pageid` bigint(20) NOT NULL,
  `categoryid` bigint(20) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `pageid` (`pageid`),
  KEY `categoryid` (`categoryid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=41 ;

-- --------------------------------------------------------

--
-- Table structure for table `moneysaving_article`
--

CREATE TABLE IF NOT EXISTS `moneysaving_article` (
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=16 ;

-- --------------------------------------------------------

--
-- Table structure for table `newsletterbanners`
--

CREATE TABLE IF NOT EXISTS `newsletterbanners` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `newslettersub`
--

CREATE TABLE IF NOT EXISTS `newslettersub` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `deleted` int(11) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `news_letter_cache`
--

CREATE TABLE IF NOT EXISTS `news_letter_cache` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `value` longblob,
  `status` tinyint(1) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `offer`
--

CREATE TABLE IF NOT EXISTS `offer` (
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=13591 ;

-- --------------------------------------------------------

--
-- Table structure for table `offer_news`
--

CREATE TABLE IF NOT EXISTS `offer_news` (
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=473 ;

-- --------------------------------------------------------

--
-- Table structure for table `offer_tiles`
--

CREATE TABLE IF NOT EXISTS `offer_tiles` (
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=116 ;

-- --------------------------------------------------------

--
-- Table structure for table `page`
--

CREATE TABLE IF NOT EXISTS `page` (
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
  KEY `pageHomeImageId_foreign_key` (`pageHomeImageId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=100 ;

-- --------------------------------------------------------

--
-- Table structure for table `page_attribute`
--

CREATE TABLE IF NOT EXISTS `page_attribute` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'PK',
  `name` varchar(255) DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

--
-- Table structure for table `page_widgets`
--

CREATE TABLE IF NOT EXISTS `page_widgets` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `widgetId` bigint(20) DEFAULT NULL,
  `widget_type` varchar(255) DEFAULT NULL,
  `position` bigint(20) DEFAULT NULL,
  `deleted` tinyint(1) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=101 ;

-- --------------------------------------------------------

--
-- Table structure for table `popular_articles`
--

CREATE TABLE IF NOT EXISTS `popular_articles` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `articleId` bigint(20) DEFAULT NULL,
  `position` bigint(20) DEFAULT NULL,
  `deleted` tinyint(1) DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=28 ;

-- --------------------------------------------------------

--
-- Table structure for table `popular_category`
--

CREATE TABLE IF NOT EXISTS `popular_category` (
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;

-- --------------------------------------------------------

--
-- Table structure for table `popular_code`
--

CREATE TABLE IF NOT EXISTS `popular_code` (
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=69 ;

-- --------------------------------------------------------

--
-- Table structure for table `popular_shop`
--

CREATE TABLE IF NOT EXISTS `popular_shop` (
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=31 ;

-- --------------------------------------------------------

--
-- Table structure for table `popular_vouchercodes`
--

CREATE TABLE IF NOT EXISTS `popular_vouchercodes` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `ref_articlecategory_relatedcategory`
--

CREATE TABLE IF NOT EXISTS `ref_articlecategory_relatedcategory` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'PK',
  `articlecategoryid` bigint(20) NOT NULL COMMENT 'FK to articlecategory.id',
  `relatedcategoryid` bigint(20) NOT NULL COMMENT 'FK to category.id',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=112 ;

-- --------------------------------------------------------

--
-- Table structure for table `ref_article_category`
--

CREATE TABLE IF NOT EXISTS `ref_article_category` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'PK',
  `articleid` bigint(20) NOT NULL COMMENT 'FK to articles.id',
  `relatedcategoryid` bigint(20) NOT NULL COMMENT 'FK to articlecatgory.id',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=373 ;

-- --------------------------------------------------------

--
-- Table structure for table `ref_article_store`
--

CREATE TABLE IF NOT EXISTS `ref_article_store` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'PK',
  `articleid` bigint(20) NOT NULL COMMENT 'FK to article.id',
  `storeid` bigint(20) NOT NULL COMMENT 'FK to shop.id',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `articleid` (`articleid`),
  KEY `storeid` (`storeid`),
  KEY `article_shop_id_idx` (`articleid`,`storeid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=703 ;

-- --------------------------------------------------------

--
-- Table structure for table `ref_excludedkeyword_shop`
--

CREATE TABLE IF NOT EXISTS `ref_excludedkeyword_shop` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `keywordid` int(11) NOT NULL,
  `keywordname` varchar(256) DEFAULT NULL,
  `shopid` int(11) NOT NULL,
  `deleted` int(1) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `keyword_shop_id_idx` (`keywordid`,`shopid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=220 ;

-- --------------------------------------------------------

--
-- Table structure for table `ref_offer_category`
--

CREATE TABLE IF NOT EXISTS `ref_offer_category` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `offerid` bigint(20) NOT NULL COMMENT 'FK to offer.id',
  `categoryid` bigint(20) NOT NULL COMMENT 'FK to category.id',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `offerid_idx` (`offerid`),
  KEY `categoryid_idx` (`categoryid`),
  KEY `offer_category_id_idx` (`categoryid`,`offerid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=21419 ;

-- --------------------------------------------------------

--
-- Table structure for table `ref_offer_page`
--

CREATE TABLE IF NOT EXISTS `ref_offer_page` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `pageid` bigint(20) NOT NULL COMMENT 'FK to page.id',
  `offerid` bigint(20) NOT NULL COMMENT 'FK to offer.id',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `offerid_idx` (`offerid`),
  KEY `pageid_idx` (`pageid`),
  KEY `offer_page_id_idx` (`pageid`,`offerid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5690 ;

-- --------------------------------------------------------

--
-- Table structure for table `ref_page_widget`
--

CREATE TABLE IF NOT EXISTS `ref_page_widget` (
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=857 ;

-- --------------------------------------------------------

--
-- Table structure for table `ref_shop_category`
--

CREATE TABLE IF NOT EXISTS `ref_shop_category` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `shopid` bigint(20) DEFAULT NULL COMMENT 'FK to shop.id',
  `categoryid` bigint(20) DEFAULT NULL COMMENT 'FK to category.id',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `shopid_idx` (`shopid`),
  KEY `categoryid_idx` (`categoryid`),
  KEY `shop_category_id_idx` (`shopid`,`categoryid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10742 ;

-- --------------------------------------------------------

--
-- Table structure for table `ref_shop_relatedshop`
--

CREATE TABLE IF NOT EXISTS `ref_shop_relatedshop` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `shopId` bigint(20) NOT NULL COMMENT 'shop id forgien key to shop id',
  `relatedshopId` bigint(20) NOT NULL COMMENT 'related shop id forgien key to shop id',
  `position` int(5) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `shop_relatedshop_id_idx` (`shopId`,`relatedshopId`),
  KEY `shop_relatedshop_idx` (`shopId`,`relatedshopId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9138 ;

-- --------------------------------------------------------

--
-- Table structure for table `route_permalink`
--

CREATE TABLE IF NOT EXISTS `route_permalink` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `permalink` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `exactlink` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `permalink_idx` (`permalink`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1601 ;

-- --------------------------------------------------------

--
-- Table structure for table `route_redirect`
--

CREATE TABLE IF NOT EXISTS `route_redirect` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `orignalurl` text,
  `redirectto` text,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `orignalurl_idx` (`orignalurl`(255))
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=310 ;

-- --------------------------------------------------------

--
-- Table structure for table `seen_in`
--

CREATE TABLE IF NOT EXISTS `seen_in` (
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE IF NOT EXISTS `settings` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `value` text,
  `status` tinyint(1) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=41 ;

-- --------------------------------------------------------

--
-- Table structure for table `shop`
--

CREATE TABLE IF NOT EXISTS `shop` (
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1112 ;

-- --------------------------------------------------------

--
-- Table structure for table `shopreasons`
--

CREATE TABLE IF NOT EXISTS `shopreasons` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `shopid` bigint(20) DEFAULT NULL,
  `fieldname` varchar(100) DEFAULT NULL,
  `fieldvalue` text,
  `deleted` tinyint(1) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1367 ;

-- --------------------------------------------------------

--
-- Table structure for table `shopviewcount`
--

CREATE TABLE IF NOT EXISTS `shopviewcount` (
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=48862 ;

-- --------------------------------------------------------

--
-- Table structure for table `shop_howto_chapter`
--

CREATE TABLE IF NOT EXISTS `shop_howto_chapter` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `shopId` bigint(20) DEFAULT NULL,
  `chapterTitle` text,
  `chapterDescription` longblob,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1130 ;

-- --------------------------------------------------------

--
-- Table structure for table `signupcodes`
--

CREATE TABLE IF NOT EXISTS `signupcodes` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'PK',
  `entered_uid` bigint(20) NOT NULL,
  `code` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `signupfavoriteshop`
--

CREATE TABLE IF NOT EXISTS `signupfavoriteshop` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'PK',
  `entered_uid` bigint(20) NOT NULL,
  `store_id` bigint(20) NOT NULL COMMENT 'FK to shop.id',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `store_id` (`store_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `signupmaxaccount`
--

CREATE TABLE IF NOT EXISTS `signupmaxaccount` (
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Table structure for table `special`
--

CREATE TABLE IF NOT EXISTS `special` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'PK',
  `title` text,
  `description` longblob,
  `status` tinyint(1) DEFAULT '1',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `special_list`
--

CREATE TABLE IF NOT EXISTS `special_list` (
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=19 ;

-- --------------------------------------------------------

--
-- Table structure for table `special_pages_offers`
--

CREATE TABLE IF NOT EXISTS `special_pages_offers` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `offerId` bigint(20) DEFAULT NULL,
  `pageId` bigint(20) DEFAULT NULL,
  `position` bigint(20) DEFAULT NULL,
  `deleted` tinyint(1) DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=401 ;

-- --------------------------------------------------------

--
-- Table structure for table `term_and_condition`
--

CREATE TABLE IF NOT EXISTS `term_and_condition` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `content` text,
  `offerid` bigint(20) DEFAULT NULL COMMENT 'FK to offer.id',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `offerid_idx` (`offerid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4906 ;

-- --------------------------------------------------------

--
-- Table structure for table `translations`
--

CREATE TABLE IF NOT EXISTS `translations` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `translationKey` text,
  `translation` text,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=106 ;

-- --------------------------------------------------------

--
-- Table structure for table `varnish`
--

CREATE TABLE IF NOT EXISTS `varnish` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `url` text,
  `status` text,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `refresh_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=18684 ;

-- --------------------------------------------------------

--
-- Table structure for table `view_count`
--

CREATE TABLE IF NOT EXISTS `view_count` (
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1307742 ;

-- --------------------------------------------------------

--
-- Table structure for table `visitor`
--

CREATE TABLE IF NOT EXISTS `visitor` (
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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3671 ;

-- --------------------------------------------------------

--
-- Table structure for table `visitor_keyword`
--

CREATE TABLE IF NOT EXISTS `visitor_keyword` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `keyword` varchar(255) DEFAULT NULL,
  `visitorId` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `vis_cascade` (`visitorId`),
  KEY `visitorId_idx` (`visitorId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `votes`
--

CREATE TABLE IF NOT EXISTS `votes` (
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=144 ;

-- --------------------------------------------------------

--
-- Table structure for table `widget`
--

CREATE TABLE IF NOT EXISTS `widget` (
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;

-- --------------------------------------------------------

--
-- Table structure for table `widget_location`
--

CREATE TABLE IF NOT EXISTS `widget_location` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `affliate_network`
--
ALTER TABLE `affliate_network`
  ADD CONSTRAINT `affliate_network_replacewithid_affliate_network_id` FOREIGN KEY (`replacewithid`) REFERENCES `affliate_network` (`id`);

--
-- Constraints for table `articlecategory`
--
ALTER TABLE `articlecategory`
  ADD CONSTRAINT `articlecategory_ibfk_1` FOREIGN KEY (`categoryiconid`) REFERENCES `image` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `articles`
--
ALTER TABLE `articles`
  ADD CONSTRAINT `articles_ibfk_2` FOREIGN KEY (`thumbnailid`) REFERENCES `image` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `category`
--
ALTER TABLE `category`
  ADD CONSTRAINT `categoryFeaturedImageId_foreign_key` FOREIGN KEY (`categoryFeaturedImageId`) REFERENCES `image` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `categoryHeaderImageId_foreign_key` FOREIGN KEY (`categoryHeaderImageId`) REFERENCES `image` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `favorite_shop`
--
ALTER TABLE `favorite_shop`
  ADD CONSTRAINT `fav_cascade` FOREIGN KEY (`visitorId`) REFERENCES `visitor` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `page`
--
ALTER TABLE `page`
  ADD CONSTRAINT `pageHeaderImageId_foreign_key` FOREIGN KEY (`pageHeaderImageId`) REFERENCES `image` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pageHomeImageId_foreign_key` FOREIGN KEY (`pageHomeImageId`) REFERENCES `image` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `visitor_keyword`
--
ALTER TABLE `visitor_keyword`
  ADD CONSTRAINT `vis_cascade` FOREIGN KEY (`visitorId`) REFERENCES `visitor` (`id`) ON DELETE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
