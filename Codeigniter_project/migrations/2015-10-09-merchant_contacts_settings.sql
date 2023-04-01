CREATE TABLE `merchant_contacts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` varchar(200) NOT NULL,
  `merchant_id` int(11) NOT NULL,
  `store_id` int(11) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(50) NOT NULL,
  `type_id` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `created_by` int(11) NOT NULL,
  `modified` datetime NOT NULL,
  `modified_by` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `merchant_id` (`merchant_id`,`store_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE `merchant_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `merchant_id` int(11) NOT NULL,
  `store_id` int(11) NOT NULL,
  `name` varchar(250) NOT NULL,
  `value` text NOT NULL,
  `created` datetime NOT NULL,
  `created_by` int(11) NOT NULL,
  `modified` datetime NOT NULL,
  `modified_by` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `merchant_id` (`merchant_id`,`store_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
