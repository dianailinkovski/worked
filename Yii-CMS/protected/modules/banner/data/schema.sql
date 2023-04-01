--
-- Table structure for table `banner`
--

CREATE TABLE `banner` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `active` tinyint(1) unsigned NOT NULL,
  `text` varchar(500) DEFAULT NULL,
  `link` varchar(255) DEFAULT NULL,
  `link_type` varchar(60) DEFAULT NULL,
  `color` varchar(6) DEFAULT NULL,
  `location` varchar(60) DEFAULT NULL,
  `presence` varchar(60) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `banner_lang`
--

CREATE TABLE `banner_lang` (
  `l_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `banner_id` int(10) unsigned NOT NULL,
  `lang_id` varchar(2) NOT NULL,
  `l_text` varchar(500) DEFAULT NULL,
  `l_link` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`l_id`),
  UNIQUE KEY `l_id_UNIQUE` (`l_id`),
  KEY `banner_id` (`banner_id`),
  CONSTRAINT `banner_id_fk` FOREIGN KEY (`banner_id`) REFERENCES `banner` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;