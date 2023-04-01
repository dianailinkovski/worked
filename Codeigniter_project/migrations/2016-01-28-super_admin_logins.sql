CREATE TABLE `super_admin_store_user_logins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `super_admin_uuid` varchar(150) NOT NULL,
  `store_user_id` int(11) NOT NULL,
  `ip_address` varchar(150) NOT NULL,
  `http_cookie` text NOT NULL,
  `http_referrer` varchar(300) NOT NULL,
  `http_user_agent` text NOT NULL,
  `login_attempt` varchar(50) NOT NULL,
  `bad_login_reason` text NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;