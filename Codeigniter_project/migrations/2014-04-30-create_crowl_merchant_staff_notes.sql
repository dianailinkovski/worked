DROP TABLE IF EXISTS `crowl_merchant_staff_notes`;

CREATE TABLE `crowl_merchant_staff_notes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `merchant_name_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `entry` varchar(500) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;