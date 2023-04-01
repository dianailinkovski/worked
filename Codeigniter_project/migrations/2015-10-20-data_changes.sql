CREATE TABLE `data_changes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `record_id` int(11) NOT NULL,  
  `table` varchar(255) NOT NULL,
  `column` varchar(255) NOT NULL,
  `alt_column` varchar(255) NOT NULL,
  `prev_value` text NOT NULL,
  `new_value` text NOT NULL,
  `modified_by` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `record_id` (`record_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;