CREATE TABLE `merchant_do_not_sell_history_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `store_id` int(11) NOT NULL,
  `merchant_id` int(11) NOT NULL,
  `action_id` smallint(6) NOT NULL,
  `created` datetime NOT NULL,
  `created_by` int(11) NOT NULL,
  `modified` datetime NOT NULL,
  `modified_by` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `store_id` (`store_id`,`merchant_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `merchant_do_not_sell_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `store_id` int(11) NOT NULL,
  `merchant_id` int(11) NOT NULL,
  `num_of_times` tinyint(4) NOT NULL,
  `is_permanent` tinyint(4) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `store_id` (`store_id`,`merchant_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE `merchant_do_not_sell_periods` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `store_id` int(11) NOT NULL,
  `merchant_id` int(11) NOT NULL,
  `period_num` tinyint(4) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `added_by` int(11) NOT NULL,
  `removed_by` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `merchant_id` (`merchant_id`),
  KEY `store_id` (`store_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE `merchant_do_not_sell_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `store_id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `value` text NOT NULL,
  `created` datetime NOT NULL,
  `created_by` int(11) NOT NULL,
  `modified` datetime NOT NULL,
  `modified_by` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `store_id` (`store_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE `merchant_history_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `store_id` int(11) NOT NULL,
  `merchant_id` int(11) NOT NULL,
  `action_id` tinyint(4) NOT NULL,
  `action_text` text NOT NULL,
  `created` datetime NOT NULL,
  `created_by` int(11) NOT NULL,
  `modified` datetime NOT NULL,
  `modified_by` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `store_id` (`store_id`,`merchant_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;