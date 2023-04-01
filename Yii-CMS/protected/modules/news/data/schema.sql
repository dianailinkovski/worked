-- phpMyAdmin SQL Dump
-- version 3.4.10.1deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jun 11, 2014 at 02:15 PM
-- Server version: 5.5.35
-- PHP Version: 5.3.10-1ubuntu3.9

-- --------------------------------------------------------

--
-- Table structure for table `news`
--

CREATE TABLE IF NOT EXISTS `news` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `date` datetime NOT NULL,
  `summary` text NOT NULL,
  `image` varchar(255) NOT NULL,
  `title_url` varchar(255) NOT NULL,
  `image_label` varchar(255) NOT NULL,
  `source` varchar(255) NOT NULL,
  `source_url` varchar(255) NOT NULL,
  `section_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `title_url` (`title_url`),
  KEY `section_id` (`section_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `news_lang`
--

CREATE TABLE IF NOT EXISTS `news_lang` (
  `l_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `news_id` int(10) unsigned NOT NULL,
  `lang_id` varchar(2) NOT NULL,
  `l_title` varchar(255) NOT NULL,
  `l_summary` text NOT NULL,
  `l_title_url` varchar(255) NOT NULL,
  `l_image` varchar(255) NOT NULL,
  `l_image_label` varchar(255) NOT NULL,
  `l_source` varchar(255) NOT NULL,
  `l_source_url` varchar(255) NOT NULL,
  PRIMARY KEY (`l_id`),
  KEY `news_id` (`news_id`),
  KEY `l_title_url` (`l_title_url`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `news`
--
ALTER TABLE `news`
  ADD CONSTRAINT `news_ibfk_1` FOREIGN KEY (`section_id`) REFERENCES `cms_section` (`id`);

--
-- Constraints for table `news_lang`
--
ALTER TABLE `news_lang`
  ADD CONSTRAINT `news_lang_ibfk_1` FOREIGN KEY (`news_id`) REFERENCES `news` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
