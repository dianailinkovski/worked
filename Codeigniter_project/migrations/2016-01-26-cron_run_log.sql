CREATE TABLE `cron_run_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `function_name` varchar(200) NOT NULL,
  `run_start_time` datetime NOT NULL,
  `run_end_time` datetime NOT NULL,
  `output` text NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;