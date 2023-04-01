CREATE TABLE IF NOT EXISTS `event` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `title_url` varchar(255) NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_start` datetime NOT NULL,
  `date_end` datetime NOT NULL,
  `summary` text NOT NULL,
  `location` varchar(255) NOT NULL,
  `location_map` text NOT NULL,
  `image` varchar(255) NOT NULL,
  `image_label` varchar(255) NOT NULL,
  `section_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `title_url` (`title_url`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;



CREATE TABLE IF NOT EXISTS `event_lang` (
  `l_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `event_id` int(10) unsigned NOT NULL,
  `lang_id` varchar(2) NOT NULL,
  `l_title` varchar(255) NOT NULL,
  `l_title_url` varchar(255) NOT NULL,
  `l_summary` text NOT NULL,
  `l_image_label` varchar(255) NOT NULL,
  `l_location` varchar(255) NOT NULL,
  PRIMARY KEY (`l_id`),
  KEY `event_id` (`event_id`),
  KEY `l_title_url` (`l_title_url`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `event_lang`
--
ALTER TABLE `event_lang`
  ADD CONSTRAINT `event_lang_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
