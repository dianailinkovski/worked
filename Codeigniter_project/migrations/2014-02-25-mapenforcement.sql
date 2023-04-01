/* Code for the MAP enforcement */

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

ALTER TABLE `violator_notification_email_settings` ADD `crowl_merchant_name_id` INT(11)  NULL  DEFAULT NULL  AFTER `notification_levels`;
ALTER TABLE `violator_notification_email_settings` ADD `name_to` VARCHAR(50)  NOT NULL  DEFAULT ''  AFTER `smtp_password`;
ALTER TABLE `violator_notification_email_settings` ADD `email_to` VARCHAR(50)  NOT NULL  DEFAULT ''  AFTER `name_to`;