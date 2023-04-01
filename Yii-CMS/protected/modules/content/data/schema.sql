--
-- Table structure for table `content_page`
--

CREATE TABLE IF NOT EXISTS `content_page` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `alias_id` int(10) unsigned NOT NULL,
  `layout` varchar(60) NOT NULL,
  `last_modified` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `alias_id` (`alias_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------
--
-- Constraints for table `content_page`
--
ALTER TABLE `content_page`
  ADD CONSTRAINT `content_page_ibfk_1` FOREIGN KEY (`alias_id`) REFERENCES `cms_alias` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
