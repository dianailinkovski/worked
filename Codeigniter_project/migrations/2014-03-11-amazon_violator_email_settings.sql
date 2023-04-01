DROP TABLE IF EXISTS `amazon_violator_email_settings`;

CREATE TABLE `amazon_violator_email_settings` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `store_id` bigint(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(100) NOT NULL,
  `message` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;