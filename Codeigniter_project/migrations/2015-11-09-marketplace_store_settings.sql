CREATE TABLE `marketplace_store_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `marketplace` varchar(50) NOT NULL,
  `store_id` int(11) NOT NULL,
  `name` varchar(250) NOT NULL,
  `value` text NOT NULL,
  `created` datetime NOT NULL,
  `created_by` int(11) NOT NULL,
  `modified` datetime NOT NULL,
  `modified_by` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `marketplace` (`marketplace`),
  KEY `store_id` (`store_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;