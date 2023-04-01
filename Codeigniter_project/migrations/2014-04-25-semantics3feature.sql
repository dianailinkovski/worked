CREATE TABLE IF NOT EXISTS `crawler_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `datetime` datetime NOT NULL,
  `start_datetime` datetime NOT NULL,
  `end_datetime` datetime NOT NULL,
  `products_count` int(5) NOT NULL DEFAULT '0',
  `offerings_count` int(5) NOT NULL DEFAULT '0',
  `stores_count` int(5) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `crawlers` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `type` enum('api','file') NOT NULL DEFAULT 'api',
  `name` varchar(100) NOT NULL DEFAULT '',
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `key` varchar(255) NOT NULL DEFAULT '',
  `secret` varchar(255) NOT NULL DEFAULT '',
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `crawlers` (`id`, `type`, `name`, `active`, `key`, `secret`, `created_at`)
VALUES (1, 'api', 'Semantics', 1, 'SEM3EC25DAA035F699902D82525F503CA0A4', 'ZGZhMWRkMDNlNWYzNDQyNWVhMDcyNTA3ZjkyNmY4OWI', '2014-04-28 11:30:00');

CREATE TABLE `crawler_error_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `crawler_log_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `crawler_name` varchar(100) NOT NULL DEFAULT '',
  `message` text NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `cron_log` CHANGE `api_type` `api_type` VARCHAR(100)  CHARACTER SET latin1  NOT NULL  DEFAULT '';