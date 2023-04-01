DROP TABLE IF EXISTS `amazon_violator_proxy`;

CREATE TABLE `amazon_violator_proxy` (
  `proxy_address` varchar(100) NOT NULL,
  `proxy_port` varchar(3) NOT NULL,
  `proxy_user` varchar(20) NOT NULL,
  `proxy_password` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;