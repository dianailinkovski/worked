ALTER TABLE `crowl_product_list_new`   
  ADD COLUMN `sent_email_to_amazon` TINYINT(1) DEFAULT 0  NOT NULL AFTER `last_date`;
  
DROP TABLE IF EXISTS `violator_notification_email_histories`;

CREATE TABLE `violator_notification_email_histories` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `store_id` int(11) unsigned NOT NULL,
  `crowl_merchant_name_id` int(11) unsigned NOT NULL,
  `crowl_product_list_id` int(11) unsigned NOT NULL,
  `product_id` int(11) unsigned NOT NULL,
  `email_level` tinyint(1) unsigned NOT NULL,
  `email_repeat` tinyint(2) unsigned NOT NULL,
  `date` datetime NOT NULL,
  `is_last` tinyint(1) NOT NULL DEFAULT '0',
  `is_exit` tinyint(1) NOT NULL DEFAULT '0',
  `email_from` varchar(255) NOT NULL DEFAULT '',
  `name_from` varchar(255) DEFAULT NULL,
  `email_to` varchar(255) NOT NULL DEFAULT '',
  `name_to` varchar(255) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `regarding` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `violator_notification_email_settings` */

DROP TABLE IF EXISTS `violator_notification_email_settings`;

CREATE TABLE `violator_notification_email_settings` (
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
  `name_to` varchar(50) NOT NULL DEFAULT '',
  `email_to` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

/*Table structure for table `violator_notification_email_templates` */

DROP TABLE IF EXISTS `violator_notification_email_templates`;

CREATE TABLE `violator_notification_email_templates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email_settings_id` int(11) NOT NULL,
  `notification_level` int(11) NOT NULL,
  `subject` varchar(100) NOT NULL,
  `no_of_days_to_repeat` int(11) NOT NULL DEFAULT '1',
  `notify_after_days` int(11) NOT NULL DEFAULT '1',
  `known_seller_html_body` text NOT NULL,
  `unknown_seller_html_body` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=utf8;  