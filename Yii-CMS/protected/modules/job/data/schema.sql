-- phpMyAdmin SQL Dump
-- version 3.4.10.1deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Nov 12, 2014 at 09:30 PM
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
-- Table structure for table `job`
--

CREATE TABLE IF NOT EXISTS `job` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `category_id` int(10) unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  `publication_date` date NOT NULL,
  `start_date` date NULL,
  `type` tinyint(1) unsigned NOT NULL,
  `title_url` varchar(255) NOT NULL,
  `nb_available` smallint(5) unsigned NOT NULL,
  `postulation_end_date` datetime NOT NULL,
  `active` tinyint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=86 ;

-- --------------------------------------------------------

--
-- Table structure for table `job_category`
--

CREATE TABLE IF NOT EXISTS `job_category` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `name_url` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=19 ;

-- --------------------------------------------------------

--
-- Table structure for table `job_category_lang`
--

CREATE TABLE IF NOT EXISTS `job_category_lang` (
  `l_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `lang_id` varchar(2) NOT NULL,
  `job_category_id` int(10) unsigned NOT NULL,
  `l_name` varchar(255) NOT NULL,
  `l_name_url` varchar(255) NOT NULL,
  PRIMARY KEY (`l_id`),
  KEY `job_category_id` (`job_category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `job_cv`
--

CREATE TABLE IF NOT EXISTS `job_cv` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `cv` varchar(255) NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=59 ;

-- --------------------------------------------------------

--
-- Table structure for table `job_job_cv`
--

CREATE TABLE IF NOT EXISTS `job_job_cv` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `job_id` int(10) unsigned NOT NULL,
  `job_cv_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `job_id` (`job_id`),
  KEY `job_cv_id` (`job_cv_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=53 ;

-- --------------------------------------------------------

--
-- Table structure for table `job_lang`
--

CREATE TABLE IF NOT EXISTS `job_lang` (
  `l_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `job_id` int(10) unsigned NOT NULL,
  `lang_id` varchar(2) NOT NULL,
  `l_title` varchar(255) NOT NULL,
  `l_title_url` varchar(255) NOT NULL,
  PRIMARY KEY (`l_id`),
  KEY `job_id` (`job_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `job`
--
ALTER TABLE `job`
  ADD CONSTRAINT `job_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `job_category` (`id`);

--
-- Constraints for table `job_category_lang`
--
ALTER TABLE `job_category_lang`
  ADD CONSTRAINT `job_category_lang_ibfk_1` FOREIGN KEY (`job_category_id`) REFERENCES `job_category` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `job_job_cv`
--
ALTER TABLE `job_job_cv`
  ADD CONSTRAINT `job_job_cv_ibfk_2` FOREIGN KEY (`job_cv_id`) REFERENCES `job_cv` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `job_job_cv_ibfk_1` FOREIGN KEY (`job_id`) REFERENCES `job` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `job_lang`
--
ALTER TABLE `job_lang`
  ADD CONSTRAINT `job_lang_ibfk_1` FOREIGN KEY (`job_id`) REFERENCES `job` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
