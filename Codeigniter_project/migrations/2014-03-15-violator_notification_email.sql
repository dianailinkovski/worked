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
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;