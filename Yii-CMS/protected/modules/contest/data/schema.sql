-- phpMyAdmin SQL Dump
-- version 3.4.10.1deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Nov 12, 2014 at 09:29 PM
-- Server version: 5.5.35
-- PHP Version: 5.3.10-1ubuntu3.9

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `villestfelicien`
--

-- --------------------------------------------------------

--
-- Table structure for table `contest`
--

CREATE TABLE IF NOT EXISTS `contest` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `title_url` varchar(255) NOT NULL,
  `summary` varchar(500) NOT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime DEFAULT NULL,
  `image` varchar(255) NOT NULL,
  `max_participation` int(10) unsigned DEFAULT NULL,
  `status` varchar(60) NOT NULL,
  `multiple_entries` tinyint(1) unsigned NOT NULL,
  `send_notification_email` tinyint(1) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;

-- --------------------------------------------------------

--
-- Table structure for table `contest_entry`
--

CREATE TABLE IF NOT EXISTS `contest_entry` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `contest_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ip` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `contest_id` (`contest_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=46 ;

-- --------------------------------------------------------

--
-- Table structure for table `contest_entry_item`
--

CREATE TABLE IF NOT EXISTS `contest_entry_item` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `contest_entry_id` int(10) unsigned NOT NULL,
  `contest_field_id` int(10) unsigned NOT NULL,
  `content` varchar(1000) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `contest_field_id` (`contest_field_id`),
  KEY `contest_entry_id` (`contest_entry_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=286 ;

-- --------------------------------------------------------

--
-- Table structure for table `contest_field`
--

CREATE TABLE IF NOT EXISTS `contest_field` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `contest_id` int(10) unsigned NOT NULL,
  `title` varchar(500) NOT NULL,
  `type` varchar(60) NOT NULL,
  `required` tinyint(1) unsigned NOT NULL,
  `result` tinyint(1) unsigned NOT NULL,
  `rank` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `contest_id` (`contest_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=38 ;

-- --------------------------------------------------------

--
-- Table structure for table `contest_field_lang`
--

CREATE TABLE IF NOT EXISTS `contest_field_lang` (
  `l_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `contest_field_id` int(10) unsigned NOT NULL,
  `lang_id` varchar(2) NOT NULL,
  `l_title` varchar(500) NOT NULL,
  PRIMARY KEY (`l_id`),
  KEY `contest_field_id` (`contest_field_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=38 ;

-- --------------------------------------------------------

--
-- Table structure for table `contest_field_multi`
--

CREATE TABLE IF NOT EXISTS `contest_field_multi` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `contest_field_id` int(10) unsigned NOT NULL,
  `title` varchar(500) NOT NULL,
  `rank` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `contest_field_id` (`contest_field_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=20 ;

-- --------------------------------------------------------

--
-- Table structure for table `contest_field_multi_lang`
--

CREATE TABLE IF NOT EXISTS `contest_field_multi_lang` (
  `l_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `contest_field_multi_id` int(10) unsigned NOT NULL,
  `lang_id` varchar(2) NOT NULL,
  `l_title` varchar(500) NOT NULL,
  PRIMARY KEY (`l_id`),
  KEY `contest_field_multi_id` (`contest_field_multi_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=20 ;

-- --------------------------------------------------------

--
-- Table structure for table `contest_lang`
--

CREATE TABLE IF NOT EXISTS `contest_lang` (
  `l_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `contest_id` int(10) unsigned NOT NULL,
  `lang_id` varchar(2) NOT NULL,
  `l_title` varchar(255) NOT NULL,
  `l_summary` varchar(500) NOT NULL,
  `l_title_url` varchar(255) NOT NULL,
  PRIMARY KEY (`l_id`),
  KEY `contest_id` (`contest_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `contest_entry`
--
ALTER TABLE `contest_entry`
  ADD CONSTRAINT `contest_entry_ibfk_2` FOREIGN KEY (`contest_id`) REFERENCES `contest` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `contest_entry_item`
--
ALTER TABLE `contest_entry_item`
  ADD CONSTRAINT `contest_entry_item_ibfk_2` FOREIGN KEY (`contest_field_id`) REFERENCES `contest_field` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `contest_entry_item_ibfk_1` FOREIGN KEY (`contest_entry_id`) REFERENCES `contest_entry` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `contest_field`
--
ALTER TABLE `contest_field`
  ADD CONSTRAINT `contest_field_ibfk_1` FOREIGN KEY (`contest_id`) REFERENCES `contest` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `contest_field_lang`
--
ALTER TABLE `contest_field_lang`
  ADD CONSTRAINT `contest_field_lang_ibfk_1` FOREIGN KEY (`contest_field_id`) REFERENCES `contest_field` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `contest_field_multi`
--
ALTER TABLE `contest_field_multi`
  ADD CONSTRAINT `contest_field_multi_ibfk_1` FOREIGN KEY (`contest_field_id`) REFERENCES `contest_field` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `contest_field_multi_lang`
--
ALTER TABLE `contest_field_multi_lang`
  ADD CONSTRAINT `contest_field_multi_lang_ibfk_1` FOREIGN KEY (`contest_field_multi_id`) REFERENCES `contest_field_multi` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `contest_lang`
--
ALTER TABLE `contest_lang`
  ADD CONSTRAINT `contest_lang_ibfk_1` FOREIGN KEY (`contest_id`) REFERENCES `contest` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
