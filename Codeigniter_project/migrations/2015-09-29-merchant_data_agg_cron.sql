CREATE TABLE `merchant_agg_cron_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `run_date` date NOT NULL,
  `trend_rows_processed` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;


CREATE TABLE `products_per_merchant_per_day` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `store_id` int(11) NOT NULL,
  `merchant_id` int(11) NOT NULL,
  `product_count` int(11) NOT NULL,
  `select_date` date NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `store_id` (`store_id`,`merchant_id`,`select_date`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE `violations_per_merchant_per_day` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `store_id` int(11) NOT NULL,
  `merchant_id` int(11) NOT NULL,
  `violation_count` int(11) NOT NULL,
  `select_date` date NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `store_id` (`store_id`,`merchant_id`,`select_date`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;