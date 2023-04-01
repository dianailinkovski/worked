INSERT INTO `key_value` (
`key` ,
`value`
)
VALUES (
'newsletter_execution_time', '0000-00-00 00:00:00'
), (
'newsletter_frequency', '2592000'
);

CREATE TABLE IF NOT EXISTS `newsletter_subscription` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `language` varchar(2) NOT NULL,
  `datetime` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;