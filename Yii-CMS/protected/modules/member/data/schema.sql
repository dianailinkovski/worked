
CREATE TABLE IF NOT EXISTS `member` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(32) NOT NULL,
  `birth_date` date DEFAULT NULL,
  `gender` char(1) NOT NULL,
  `address` varchar(255) NOT NULL,
  `apartment` smallint(5) unsigned DEFAULT NULL,
  `rating` tinyint(1) unsigned DEFAULT NULL,
  `tel1` varchar(14) NOT NULL,
  `tel2` varchar(14) NOT NULL,
  `city` varchar(255) NOT NULL,
  `postal_code` varchar(7) NOT NULL,
  `vehicle_brand` varchar(255) NOT NULL,
  `vehicle_model` varchar(255) NOT NULL,
  `vehicle_year` smallint(4) unsigned DEFAULT NULL,
  `last_login_date` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `recover_hash` varchar(32) NOT NULL,
  `recover_time` datetime DEFAULT NULL,
  `activation_hash` varchar(32) NOT NULL,
  `activation_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT INTO `AuthItem` (`name`, `type`, `description`, `data`) VALUES ('Member', '2', 'Member', 'N;');