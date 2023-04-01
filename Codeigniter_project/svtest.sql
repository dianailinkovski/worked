#************************************************************
# Sequel Pro SQL dump
# Version 4004
#
# http://www.sequelpro.com/
# http://code.google.com/p/sequel-pro/
#
# Host: 127.0.0.1 (MySQL 5.1.67)
# Database: svtest
# Generation Time: 2013-04-15 17:16:16 -0700
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table brand_columns
# ------------------------------------------------------------

DROP TABLE IF EXISTS `brand_columns`;

CREATE TABLE `brand_columns` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `column_id` int(11) NOT NULL,
  `user_store_id` int(11) NOT NULL,
  `sort` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `column_id` (`column_id`),
  KEY `user_store_id` (`user_store_id`),
  KEY `sort` (`sort`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table brand_product
# ------------------------------------------------------------

DROP TABLE IF EXISTS `brand_product`;

CREATE TABLE `brand_product` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `store_id` int(11) unsigned NOT NULL,
  `product_id` int(11) unsigned NOT NULL,
  `owner` tinyint(1) unsigned zerofill NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `brand_product` (`store_id`,`product_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table brand_product_product
# ------------------------------------------------------------

DROP TABLE IF EXISTS `brand_product_product`;

CREATE TABLE `brand_product_product` (
  `owner_brand_product` int(11) unsigned NOT NULL,
  `competitor_brand_product` int(11) unsigned NOT NULL,
  PRIMARY KEY (`owner_brand_product`,`competitor_brand_product`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table cms_pages
# ------------------------------------------------------------

DROP TABLE IF EXISTS `cms_pages`;

CREATE TABLE `cms_pages` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `meta` text NOT NULL,
  `meta_description` text NOT NULL,
  `body` text NOT NULL,
  `type` enum('terms','privacy','contact','faqs','help','legal','about','sitemap') DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `type` (`type`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table columns
# ------------------------------------------------------------

DROP TABLE IF EXISTS `columns`;

CREATE TABLE `columns` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `display_name` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `db_name` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `date_created` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table cron_graph_data
# ------------------------------------------------------------

DROP TABLE IF EXISTS `cron_graph_data`;

CREATE TABLE `cron_graph_data` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `data` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table cron_log
# ------------------------------------------------------------

DROP TABLE IF EXISTS `cron_log`;

CREATE TABLE `cron_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `datetime` datetime NOT NULL,
  `key` varchar(30) NOT NULL,
  `api_type` enum('google','amazon','shopping','pricegrabber','livamed','vitacost','iherb','vitaminshoppe','vitanherbs','luckyvitamin','swansonvitamins') DEFAULT NULL,
  `start_datetime` datetime NOT NULL,
  `end_datetime` datetime NOT NULL,
  `google_count` int(11) NOT NULL DEFAULT '0',
  `last_UPC` varchar(255) DEFAULT NULL,
  `run_from` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `datetime` (`datetime`),
  KEY `key` (`key`),
  KEY `api_type` (`api_type`),
  KEY `start_datetime` (`start_datetime`),
  KEY `end_datetime` (`end_datetime`),
  KEY `google_count` (`google_count`),
  KEY `last_UPC` (`last_UPC`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table cron_process_stats
# ------------------------------------------------------------

DROP TABLE IF EXISTS `cron_process_stats`;

CREATE TABLE `cron_process_stats` (
  `today` date NOT NULL DEFAULT '0000-00-00',
  `pid` varchar(15) DEFAULT NULL,
  `process_no` tinyint(4) NOT NULL DEFAULT '0',
  `process_time` int(11) DEFAULT NULL,
  `process` varchar(50) DEFAULT NULL,
  `relaunch_count` int(11) DEFAULT NULL,
  `errors` text,
  `wkhtml_error_count` int(11) DEFAULT NULL,
  `phantomjs_error_count` int(11) DEFAULT NULL,
  `cli` tinytext,
  PRIMARY KEY (`today`,`process_no`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table crowl_merchant_name_new
# ------------------------------------------------------------

DROP TABLE IF EXISTS `crowl_merchant_name_new`;

CREATE TABLE `crowl_merchant_name_new` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `merchant_name` varchar(255) DEFAULT NULL,
  `original_name` varchar(255) DEFAULT NULL,
  `marketplace` varchar(50) DEFAULT NULL,
  `seller_id` varchar(50) DEFAULT NULL,
  `merchant_url` varchar(511) DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `merchant_name` (`merchant_name`),
  KEY `original_name` (`original_name`),
  KEY `marketplace` (`marketplace`),
  KEY `seller_id` (`seller_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table crowl_product_list_new
# ------------------------------------------------------------

DROP TABLE IF EXISTS `crowl_product_list_new`;

CREATE TABLE `crowl_product_list_new` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `upc` varchar(50) DEFAULT NULL,
  `marketplace` varchar(15) DEFAULT NULL,
  `merchant_name_id` int(11) DEFAULT NULL,
  `violated` tinyint(1) DEFAULT '0',
  `last_date` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `upc` (`upc`),
  KEY `marketplace` (`marketplace`),
  KEY `merchant_name_id` (`merchant_name_id`),
  KEY `last_date` (`last_date`),
  KEY `violated` (`violated`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table csv_default_columns
# ------------------------------------------------------------

DROP TABLE IF EXISTS `csv_default_columns`;

CREATE TABLE `csv_default_columns` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `array_values` text NOT NULL,
  `add_date_time` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table daily_price_average
# ------------------------------------------------------------

DROP TABLE IF EXISTS `daily_price_average`;

CREATE TABLE `daily_price_average` (
  `upc` varchar(255) NOT NULL,
  `marketplace` varchar(255) NOT NULL,
  `date` date NOT NULL,
  `price_total` float NOT NULL,
  `seller_total` int(11) NOT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`),
  KEY `upc` (`upc`),
  KEY `marketplace` (`marketplace`),
  KEY `date` (`date`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table email_messages
# ------------------------------------------------------------

DROP TABLE IF EXISTS `email_messages`;

CREATE TABLE `email_messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `datetime` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table email_reference
# ------------------------------------------------------------

DROP TABLE IF EXISTS `email_reference`;

CREATE TABLE `email_reference` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `message_id` int(11) NOT NULL,
  `is_message_checked` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `sender_id` (`sender_id`),
  KEY `receiver_id` (`receiver_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table email_templates
# ------------------------------------------------------------

DROP TABLE IF EXISTS `email_templates`;

CREATE TABLE `email_templates` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `bc` text NOT NULL,
  `cc` text NOT NULL,
  `from_name` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `body` text NOT NULL,
  `type` enum('signup','signupadmin','fpassword','summaries','violation','general') DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `type` (`type`),
  KEY `is_active` (`is_active`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table global_settings
# ------------------------------------------------------------

DROP TABLE IF EXISTS `global_settings`;

CREATE TABLE `global_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `search_in_days` int(11) NOT NULL DEFAULT '0',
  `search_in_hours` int(11) NOT NULL DEFAULT '4',
  `search_in_minutes` int(11) NOT NULL DEFAULT '0',
  `api_settings` enum('google','amazon','shopping','pricegrabber') NOT NULL,
  `is_active` enum('0','1') NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table group_products
# ------------------------------------------------------------

DROP TABLE IF EXISTS `group_products`;

CREATE TABLE `group_products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `_group_id_` (`group_id`),
  KEY `product_id` (`product_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table groups
# ------------------------------------------------------------

DROP TABLE IF EXISTS `groups`;

CREATE TABLE `groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `store_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `store_id` (`store_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table marketplaces
# ------------------------------------------------------------

DROP TABLE IF EXISTS `marketplaces`;

CREATE TABLE `marketplaces` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(63) NOT NULL DEFAULT '',
  `display_name` varchar(63) NOT NULL DEFAULT '',
  `is_retailer` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `is_active` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `upc_lookup` tinyint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table news
# ------------------------------------------------------------

DROP TABLE IF EXISTS `news`;

CREATE TABLE `news` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `heading` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `datetime` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `datetime` (`datetime`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table news_messages
# ------------------------------------------------------------

DROP TABLE IF EXISTS `news_messages`;

CREATE TABLE `news_messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `subject` varchar(255) NOT NULL,
  `news` varchar(255) NOT NULL,
  `datetime` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table news_reference
# ------------------------------------------------------------

DROP TABLE IF EXISTS `news_reference`;

CREATE TABLE `news_reference` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `news_id` int(11) NOT NULL,
  `is_news_valid` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table notifications_setting
# ------------------------------------------------------------

DROP TABLE IF EXISTS `notifications_setting`;

CREATE TABLE `notifications_setting` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `section` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


# Dump of table products
# ------------------------------------------------------------

DROP TABLE IF EXISTS `products`;

CREATE TABLE `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `store_id` int(11) NOT NULL,
  `title` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `upc_code` varchar(255) CHARACTER SET latin1 NOT NULL,
  `brand` varchar(100) COLLATE latin1_general_ci NOT NULL,
  `sku` varchar(100) COLLATE latin1_general_ci NOT NULL,
  `retail_price` float(9,2) NOT NULL,
  `price_floor` float(9,2) NOT NULL,
  `is_tracked` tinyint(1) DEFAULT '1',
  `created_at` datetime NOT NULL,
  `wholesale_price` float(9,2) NOT NULL DEFAULT '0.00',
  `status` tinyint(4) NOT NULL DEFAULT '1',
  `is_archived` tinyint(1) DEFAULT '0',
  `is_violated` tinyint(4) NOT NULL DEFAULT '0',
  `deleted_at` datetime DEFAULT NULL,
  `is_processed` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `_title_` (`title`),
  KEY `idx_upc_store` (`upc_code`,`store_id`),
  KEY `idx_is_tracked` (`is_tracked`),
  KEY `idx_store_id` (`store_id`),
  KEY `idx_upc_only` (`upc_code`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;



# Dump of table products_deleted
# ------------------------------------------------------------

DROP TABLE IF EXISTS `products_deleted`;

CREATE TABLE `products_deleted` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `store_id` int(11) NOT NULL,
  `products_id` text NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `store_id` (`store_id`),
  KEY `store_id_2` (`store_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table products_history
# ------------------------------------------------------------

DROP TABLE IF EXISTS `products_history`;

CREATE TABLE `products_history` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int(11) unsigned NOT NULL,
  `store_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `field` varchar(63) NOT NULL DEFAULT '',
  `old_value` varchar(1023) DEFAULT NULL,
  `new_value` varchar(1023) DEFAULT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `store_id` (`store_id`),
  KEY `user_id` (`user_id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table products_lookup
# ------------------------------------------------------------

DROP TABLE IF EXISTS `products_lookup`;

CREATE TABLE `products_lookup` (
  `marketplace_id` int(11) unsigned NOT NULL,
  `product_id` int(11) unsigned NOT NULL,
  `url` varchar(1023) DEFAULT NULL,
  PRIMARY KEY (`marketplace_id`,`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table products_pricing
# ------------------------------------------------------------

DROP TABLE IF EXISTS `products_pricing`;

CREATE TABLE `products_pricing` (
  `pricing_id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) DEFAULT '0',
  `pricing_type` varchar(32) DEFAULT '0',
  `pricing_value` decimal(9,2) DEFAULT '0.00',
  `pricing_start` datetime DEFAULT NULL,
  `pricing_end` datetime DEFAULT NULL,
  PRIMARY KEY (`pricing_id`),
  KEY `product_id` (`product_id`),
  KEY `pricing_type` (`pricing_type`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table proxy_ips
# ------------------------------------------------------------

DROP TABLE IF EXISTS `proxy_ips`;

CREATE TABLE `proxy_ips` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `proxy_host` varchar(255) NOT NULL,
  `proxy_port` int(11) NOT NULL,
  `scheme` varchar(10) DEFAULT NULL,
  `use_flag` tinyint(4) NOT NULL DEFAULT '0',
  `fails` bigint(11) NOT NULL DEFAULT '0',
  `connects` bigint(11) NOT NULL DEFAULT '0',
  `warns` bigint(11) NOT NULL DEFAULT '0',
  `last_warn_time` timestamp NULL DEFAULT NULL,
  `ban_source` varchar(255) DEFAULT NULL,
  `ban_type` varchar(15) DEFAULT NULL,
  `ban_agent` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `proxy_host` (`proxy_host`),
  KEY `use_flag` (`use_flag`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table saved_reports
# ------------------------------------------------------------

DROP TABLE IF EXISTS `saved_reports`;

CREATE TABLE `saved_reports` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `report_name` varchar(255) NOT NULL,
  `report_where` text NOT NULL,
  `user_id` int(11) NOT NULL,
  `store_id` int(11) NOT NULL,
  `controller` varchar(255) NOT NULL DEFAULT '',
  `controller_function` varchar(255) NOT NULL DEFAULT '',
  `datetime` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `_store_id_` (`store_id`),
  KEY `user_id` (`user_id`),
  KEY `controller` (`controller`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table saved_reports_schedule
# ------------------------------------------------------------

DROP TABLE IF EXISTS `saved_reports_schedule`;

CREATE TABLE `saved_reports_schedule` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `saved_reports_id` int(11) NOT NULL,
  `report_datetime` datetime DEFAULT NULL,
  `report_recursive_frequency` int(3) NOT NULL DEFAULT '0',
  `email_addresses` text NOT NULL,
  `datetime` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `datetime` (`datetime`),
  KEY `report_datetime` (`report_datetime`),
  KEY `report_recursive_frequency` (`report_recursive_frequency`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table screen_shot_stats
# ------------------------------------------------------------

DROP TABLE IF EXISTS `screen_shot_stats`;

CREATE TABLE `screen_shot_stats` (
  `today` date NOT NULL DEFAULT '0000-00-00',
  `wkhtml` int(11) DEFAULT '0',
  `bluga` int(11) DEFAULT '0',
  `phantomjs` int(11) DEFAULT '0',
  `success` int(11) DEFAULT '0',
  `wkhtml_success` int(11) DEFAULT '0',
  `wkhtml_fail` int(11) DEFAULT '0',
  `bluga_success` int(11) DEFAULT '0',
  `bluga_fail` int(11) DEFAULT '0',
  `phantomjs_success` int(11) DEFAULT '0',
  `phantomjs_fail` int(11) DEFAULT '0',
  `count` int(11) DEFAULT '0',
  `fail` int(11) DEFAULT '0',
  `last_url` varchar(500) DEFAULT NULL,
  `last_id` int(11) DEFAULT '0',
  `last_image` varchar(50) DEFAULT NULL,
  `last_upload_image` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`today`),
  KEY `success` (`success`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table screen_shots
# ------------------------------------------------------------

DROP TABLE IF EXISTS `screen_shots`;

CREATE TABLE `screen_shots` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `url` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `name` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `queue_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` enum('pending','processing') NOT NULL DEFAULT 'pending',
  `price` double NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `status` (`status`),
  KEY `queue_time` (`queue_time`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table shortcuts
# ------------------------------------------------------------

DROP TABLE IF EXISTS `shortcuts`;

CREATE TABLE `shortcuts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `store_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `shortcut_name` varchar(255) NOT NULL,
  `shortcut_url` text NOT NULL,
  `shortcut_add_date` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `_store_id_` (`store_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table store
# ------------------------------------------------------------

DROP TABLE IF EXISTS `store`;

CREATE TABLE `store` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `store_name` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `has_product` enum('0','1') COLLATE latin1_general_ci NOT NULL DEFAULT '1',
  `store_enable` enum('0','1') COLLATE latin1_general_ci NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL,
  `tracked_at` datetime NOT NULL,
  `last_violation_count` int(11) NOT NULL DEFAULT '0',
  `last_violation_product_count` int(11) NOT NULL DEFAULT '0',
  `man_id` int(11) unsigned DEFAULT NULL,
  `brand_logo` varchar(255) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `has_product` (`has_product`),
  KEY `man_id` (`man_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;



# Dump of table store_smtp
# ------------------------------------------------------------

DROP TABLE IF EXISTS `store_smtp`;

CREATE TABLE `store_smtp` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `store_id` int(11) unsigned NOT NULL,
  `host` varchar(511) NOT NULL DEFAULT '',
  `port` int(11) unsigned NOT NULL DEFAULT '25',
  `use_ssl` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `username` varchar(511) NOT NULL DEFAULT '',
  `password` tinyblob NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `store_id` (`store_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table users
# ------------------------------------------------------------

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_name` varchar(255) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `email` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `last_login` datetime NOT NULL,
  `signup_date` datetime NOT NULL,
  `user_active` int(1) NOT NULL DEFAULT '0',
  `first_name` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `last_name` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `global_user_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `global_user_id` (`global_user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;



# Dump of table users_store
# ------------------------------------------------------------

DROP TABLE IF EXISTS `users_store`;

CREATE TABLE `users_store` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `store_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `store_user` (`store_id`,`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table violation_streaks
# ------------------------------------------------------------

DROP TABLE IF EXISTS `violation_streaks`;

CREATE TABLE `violation_streaks` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `store_id` int(11) unsigned NOT NULL,
  `crowl_merchant_name_id` int(11) unsigned NOT NULL,
  `streak_start` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table violator_notification_emails
# ------------------------------------------------------------

DROP TABLE IF EXISTS `violator_notification_emails`;

CREATE TABLE `violator_notification_emails` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `store_id` int(11) unsigned NOT NULL,
  `email_type` varchar(20) DEFAULT NULL,
  `html_body` text,
  `txt_body` text,
  PRIMARY KEY (`id`),
  KEY `store_id` (`store_id`),
  KEY `email_type` (`email_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table violator_notification_settings
# ------------------------------------------------------------

DROP TABLE IF EXISTS `violator_notification_settings`;

CREATE TABLE `violator_notification_settings` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `store_id` int(11) unsigned NOT NULL,
  `email_from` varchar(255) NOT NULL,
  `name_from` varchar(255) DEFAULT NULL,
  `company` varchar(255) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `title1` varchar(255) DEFAULT NULL,
  `message1` text,
  `title2` varchar(255) DEFAULT NULL,
  `message2` text,
  `days_to_warning1` int(11) unsigned NOT NULL DEFAULT '1',
  `days_to_warning2` int(11) unsigned NOT NULL DEFAULT '1',
  `warning1_repetitions` int(11) unsigned NOT NULL DEFAULT '1',
  `warning2_repetitions` int(10) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `store_id` (`store_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table violator_notifications
# ------------------------------------------------------------

DROP TABLE IF EXISTS `violator_notifications`;

CREATE TABLE `violator_notifications` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `store_id` int(11) unsigned NOT NULL,
  `crowl_merchant_name_id` int(11) unsigned NOT NULL,
  `email_to` varchar(255) DEFAULT NULL,
  `email_from` varchar(255) DEFAULT NULL,
  `name_to` varchar(255) DEFAULT NULL,
  `name_from` varchar(255) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `notification_type` varchar(20) NOT NULL DEFAULT 'unknown_seller',
  `title` varchar(255) DEFAULT NULL,
  `message` text,
  `days_to_warning1` int(11) unsigned NOT NULL DEFAULT '1',
  `days_to_warning2` int(11) unsigned NOT NULL DEFAULT '1',
  `warning1_repetitions` int(11) unsigned NOT NULL DEFAULT '1',
  `warning2_repetitions` int(10) unsigned NOT NULL DEFAULT '1',
  `active` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `store_id` (`store_id`,`crowl_merchant_name_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table violator_notifications_history
# ------------------------------------------------------------

DROP TABLE IF EXISTS `violator_notifications_history`;

CREATE TABLE `violator_notifications_history` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `store_id` int(11) unsigned NOT NULL,
  `crowl_merchant_name_id` int(11) unsigned NOT NULL,
  `email_to` varchar(255) NOT NULL DEFAULT '',
  `email_from` varchar(255) NOT NULL DEFAULT '',
  `name_to` varchar(255) DEFAULT NULL,
  `name_from` varchar(255) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `email_number` tinyint(1) unsigned DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `regarding` text,
  `date` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- phpMyAdmin SQL Dump
-- version 3.4.5
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Feb 18, 2014 at 12:34 PM
-- Server version: 5.5.16
-- PHP Version: 5.3.8

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `market-vision`
--

-- --------------------------------------------------------

--
-- Table structure for table `violator_notification_email_settings`
--

CREATE TABLE IF NOT EXISTS `violator_notification_email_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `store_id` int(11) NOT NULL,
  `notification_levels` int(11) NOT NULL,
  `email_from` varchar(50) NOT NULL,
  `name_from` varchar(50) NOT NULL,
  `company` varchar(50) NOT NULL,
  `phone` varchar(30) NOT NULL,
  `smtp_host` varchar(100) NOT NULL,
  `smtp_port` int(11) NOT NULL,
  `smtp_ssl` enum('YES','NO') NOT NULL DEFAULT 'NO',
  `smtp_username` varchar(100) NOT NULL,
  `smtp_password` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `violator_notification_email_templates`
--

CREATE TABLE IF NOT EXISTS `violator_notification_email_templates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email_settings_id` int(11) NOT NULL,
  `notification_level` int(11) NOT NULL,
  `subject` varchar(100) NOT NULL,
  `no_of_days_to_repeat` int(11) NOT NULL DEFAULT '1',
  `notify_after_days` int(11) NOT NULL DEFAULT '1',
  `known_seller_html_body` text NOT NULL,
  `unknown_seller_html_body` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;



/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
