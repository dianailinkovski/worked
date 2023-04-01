-- phpMyAdmin SQL Dump
-- version 3.4.10.1deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jun 11, 2014 at 06:40 PM
-- Server version: 5.5.35
-- PHP Version: 5.3.10-1ubuntu3.9

-- --------------------------------------------------------

--
-- Table structure for table `message`
--

CREATE TABLE IF NOT EXISTS `message` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `message` text NOT NULL,
  `datetime` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `message_assoc`
--

CREATE TABLE IF NOT EXISTS `message_assoc` (
  `message_id` int(10) unsigned NOT NULL,
  `member_id` int(10) unsigned NOT NULL,
  `seen` tinyint(1) unsigned NOT NULL,
  PRIMARY KEY (`message_id`,`member_id`),
  KEY `message_id` (`message_id`),
  KEY `member_id` (`member_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `message_assoc`
--
ALTER TABLE `message_assoc`
  ADD CONSTRAINT `message_assoc_ibfk_4` FOREIGN KEY (`member_id`) REFERENCES `member` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `message_assoc_ibfk_3` FOREIGN KEY (`message_id`) REFERENCES `message` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;