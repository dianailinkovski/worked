ALTER TABLE `users` ADD `uuid` VARCHAR( 200 ) NOT NULL AFTER `id`;
ALTER TABLE `users` ADD `phone_number` VARCHAR( 30 ) NOT NULL AFTER `last_name`;
ALTER TABLE `users` ADD `role_id` TINYINT NOT NULL AFTER `last_name`;
ALTER TABLE `users` ADD `password` VARCHAR( 100 ) NOT NULL AFTER `email`; 
ALTER TABLE `users` ADD `salt` VARCHAR( 100 ) NOT NULL AFTER `email`; 
ALTER TABLE `users` ADD `terms_accepted` TINYINT NOT NULL AFTER `email`;
ALTER TABLE `users` ADD `modified` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `global_user_id`;
ALTER TABLE `users` ADD `created` DATETIME NOT NULL AFTER `global_user_id`;
  
CREATE TABLE `application_terms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `terms` text NOT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `date_time` int(11) NOT NULL DEFAULT '0',
  `terms_type` tinyint(1) NOT NULL DEFAULT '0',
  `terms_length` varchar(20) NOT NULL,
  `version` varchar(25) NOT NULL,
  `date` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE `user_login_records` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `login_datetime` datetime NOT NULL,
  `ip` varchar(150) DEFAULT NULL,
  `user_agent` varchar(300) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;