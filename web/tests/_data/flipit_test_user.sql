-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jun 29, 2015 at 12:53 PM
-- Server version: 5.5.43-0ubuntu0.14.04.1
-- PHP Version: 5.5.9-1ubuntu4.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `kortingscode_user`
--

-- --------------------------------------------------------

--
-- Table structure for table `chain`
--

CREATE TABLE IF NOT EXISTS `chain` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=580 ;

-- --------------------------------------------------------

--
-- Table structure for table `chain_item`
--

CREATE TABLE IF NOT EXISTS `chain_item` (
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
  KEY `ref_chain_website` (`websiteid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2418 ;

-- --------------------------------------------------------

--
-- Table structure for table `global_export_password`
--

CREATE TABLE IF NOT EXISTS `global_export_password` (
  `id` smallint(6) NOT NULL AUTO_INCREMENT,
  `password` varchar(255) DEFAULT NULL,
  `exportType` varchar(50) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Table structure for table `ip_addresses`
--

CREATE TABLE IF NOT EXISTS `ip_addresses` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `ipaddress` varchar(15) DEFAULT NULL,
  `deleted` tinyint(1) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=21 ;

-- --------------------------------------------------------

--
-- Table structure for table `migration_version`
--

CREATE TABLE IF NOT EXISTS `migration_version` (
  `version` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `profile_image`
--

CREATE TABLE IF NOT EXISTS `profile_image` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'PK',
  `ext` varchar(5) DEFAULT NULL,
  `path` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=374 ;

-- --------------------------------------------------------

--
-- Table structure for table `ref_user_website`
--

CREATE TABLE IF NOT EXISTS `ref_user_website` (
  `userid` bigint(20) NOT NULL DEFAULT '0' COMMENT 'FK to user.id',
  `websiteid` bigint(20) NOT NULL DEFAULT '0' COMMENT 'FK to website.id',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`),
  KEY `userid_idx` (`userid`),
  KEY `websiteid_idx` (`websiteid`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5673 ;

-- --------------------------------------------------------

--
-- Table structure for table `rights`
--

CREATE TABLE IF NOT EXISTS `rights` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'PK',
  `name` varchar(255) DEFAULT NULL,
  `rights` mediumint(9) DEFAULT NULL COMMENT 'It describe permission of the role over sites, contents, administration, statistic, system manager',
  `description` varchar(512) DEFAULT NULL COMMENT 'right description',
  `roleid` bigint(20) DEFAULT NULL COMMENT 'FK to role.id',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `roleid_idx` (`roleid`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=27 ;

-- --------------------------------------------------------

--
-- Table structure for table `robot`
--

CREATE TABLE IF NOT EXISTS `robot` (
  `id` smallint(6) NOT NULL AUTO_INCREMENT,
  `website` text,
  `content` longblob,
  `deleted` tinyint(1) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Table structure for table `role`
--

CREATE TABLE IF NOT EXISTS `role` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'PK',
  `name` varchar(255) DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `splash`
--

CREATE TABLE IF NOT EXISTS `splash` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `locale` text,
  `offerId` bigint(20) DEFAULT NULL,
  `deleted` tinyint(1) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE IF NOT EXISTS `user` (
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
  KEY `profileimageid_idx` (`profileimageid`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=364 ;

-- --------------------------------------------------------

--
-- Table structure for table `user_session`
--

CREATE TABLE IF NOT EXISTS `user_session` (
  `id` bigint(20) NOT NULL DEFAULT '0' COMMENT 'PK',
  `userid` bigint(20) DEFAULT NULL COMMENT 'LoggedIn User''s Id',
  `sessionid` varchar(255) DEFAULT NULL COMMENT 'LoggedIn User''s Session Id',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `website`
--

CREATE TABLE IF NOT EXISTS `website` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'PK',
  `name` varchar(100) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `status` varchar(10) DEFAULT NULL,
  `chain` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=39 ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `chain_item`
--
ALTER TABLE `chain_item`
  ADD CONSTRAINT `ref_chain_items` FOREIGN KEY (`chainid`) REFERENCES `chain` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ref_chain_website` FOREIGN KEY (`websiteid`) REFERENCES `website` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `ref_user_website`
--
ALTER TABLE `ref_user_website`
  ADD CONSTRAINT `ref_user_website_userid_user_id` FOREIGN KEY (`userid`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `ref_user_website_websiteid_website_id` FOREIGN KEY (`websiteid`) REFERENCES `website` (`id`);

--
-- Constraints for table `rights`
--
ALTER TABLE `rights`
  ADD CONSTRAINT `rights_roleid_role_id` FOREIGN KEY (`roleid`) REFERENCES `role` (`id`);

--
-- Constraints for table `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `user_profileimageid_profile_image_id` FOREIGN KEY (`profileimageid`) REFERENCES `profile_image` (`id`),
  ADD CONSTRAINT `user_roleid_role_id` FOREIGN KEY (`roleid`) REFERENCES `role` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
