-- phpMyAdmin SQL Dump
-- version 3.3.10deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Dec 04, 2013 at 12:57 PM
-- Server version: 5.1.63
-- PHP Version: 5.3.5-1ubuntu7.11

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `cms`
--

-- --------------------------------------------------------

--
-- Table structure for table `AuthAssignment`
--

CREATE TABLE IF NOT EXISTS `AuthAssignment` (
  `itemname` varchar(64) NOT NULL,
  `userid` varchar(64) NOT NULL,
  `bizrule` text,
  `data` text,
  PRIMARY KEY (`itemname`,`userid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `AuthItem`
--

CREATE TABLE IF NOT EXISTS `AuthItem` (
  `name` varchar(64) NOT NULL,
  `type` int(11) NOT NULL,
  `description` text,
  `bizrule` text,
  `data` text,
  PRIMARY KEY (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `AuthItemChild`
--

CREATE TABLE IF NOT EXISTS `AuthItemChild` (
  `parent` varchar(64) NOT NULL,
  `child` varchar(64) NOT NULL,
  PRIMARY KEY (`parent`,`child`),
  KEY `child` (`child`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


--
-- Table structure for table `cms_alias`
--

CREATE TABLE IF NOT EXISTS `cms_alias` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `alias` varchar(100) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `section_id` int(10) unsigned DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `keyword` varchar(100) NOT NULL,
  `allow_children` tinyint(1) unsigned NOT NULL,
  `lft` int(10) unsigned NOT NULL,
  `rgt` int(10) unsigned NOT NULL,
  `level` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `section_id` (`section_id`),
  KEY `lft` (`lft`),
  KEY `rgt` (`rgt`),
  KEY `level` (`level`),
  KEY `alias` (`alias`),
  UNIQUE `keyword` (`keyword`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;

-- --------------------------------------------------------

--
-- Table structure for table `cms_alias_lang`
--

CREATE TABLE IF NOT EXISTS `cms_alias_lang` (
  `l_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `cms_alias_id` int(10) unsigned NOT NULL,
  `lang_id` varchar(6) NOT NULL,
  `l_alias` varchar(100) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `l_title` varchar(255) NOT NULL,
  PRIMARY KEY (`l_id`),
  KEY `cms_alias_id` (`cms_alias_id`),
  KEY `level` (`l_alias`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;

-- --------------------------------------------------------

--
-- Table structure for table `cms_alias_route`
--

CREATE TABLE IF NOT EXISTS `cms_alias_route` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `alias_id` int(10) unsigned NOT NULL,
  `route` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`),
  KEY `alias_id` (`alias_id`),
  KEY `level` (`route`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `cms_section`
--

CREATE TABLE IF NOT EXISTS `cms_section` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `module` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `cms_section_lang`
--

CREATE TABLE IF NOT EXISTS `cms_section_lang` (
  `l_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `cms_section_id` int(10) unsigned NOT NULL,
  `lang_id` varchar(6) NOT NULL,
  `l_name` text NOT NULL,
  PRIMARY KEY (`l_id`),
  KEY `cms_section_id` (`cms_section_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `key_value`
--

CREATE TABLE IF NOT EXISTS `key_value` (
  `key` varchar(255) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `Rights`
--

CREATE TABLE IF NOT EXISTS `Rights` (
  `itemname` varchar(64) NOT NULL,
  `type` int(11) NOT NULL,
  `weight` int(11) NOT NULL,
  PRIMARY KEY (`itemname`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` text NOT NULL,
  `password` varchar(32) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `flickr_user` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `user_id` varchar(60) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


-- --------------------------------------------------------

--
-- Table structure for table `bloc`
--

CREATE TABLE IF NOT EXISTS `bloc` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `unique_id` varchar(60) NOT NULL,
  `bloc_id` int(10) unsigned NOT NULL,
  `bloc_type` varchar(255) NOT NULL,
  `parent_id` int(10) unsigned NOT NULL,
  `rank` int(10) unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  `title_anchor` tinyint(1) unsigned NOT NULL,
  `title_page` tinyint(1) unsigned NOT NULL,
  `title_url` varchar(255) NOT NULL,
  `last_modified` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `bloc_achievement`
--

CREATE TABLE IF NOT EXISTS `bloc_achievement` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` varchar(60) NOT NULL,
  `set_id` bigint(20) unsigned,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `bloc_achievement_lang`
--

CREATE TABLE IF NOT EXISTS `bloc_achievement_lang` (
  `l_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `bloc_achievement_id` int(10) unsigned NOT NULL,
  `lang_id` varchar(2) NOT NULL,
  `l_name` varchar(255) NOT NULL,
  `l_description` text NOT NULL,
  PRIMARY KEY (`l_id`),
  KEY `bloc_achievement_id` (`bloc_achievement_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `bloc_citation`
--

CREATE TABLE IF NOT EXISTS `bloc_citation` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `bloc_citation_citation`
--

CREATE TABLE IF NOT EXISTS `bloc_citation_citation` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `bloc_citation_id` int(10) unsigned NOT NULL,
  `citation` text NOT NULL,
  `name` varchar(255) NOT NULL,
  `rank` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `bloc_citation_id` (`bloc_citation_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `bloc_citation_citation_lang`
--

CREATE TABLE IF NOT EXISTS `bloc_citation_citation_lang` (
  `l_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `bloc_citation_citation_id` int(10) unsigned NOT NULL,
  `lang_id` varchar(2) NOT NULL,
  `l_citation` text NOT NULL,
  `l_name` varchar(255) NOT NULL,
  PRIMARY KEY (`l_id`),
  KEY `bloc_citation_citation_id` (`bloc_citation_citation_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `bloc_contact`
--

CREATE TABLE IF NOT EXISTS `bloc_contact` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `province` varchar(255) NOT NULL,
  `postal_code` varchar(255) NOT NULL,
  `country` varchar(255) NOT NULL,
  `phone1` varchar(255) NOT NULL,
  `phone2` varchar(255) NOT NULL,
  `phone_toll_free` varchar(255) NOT NULL,
  `fax` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `google_maps` text NOT NULL,
  `image` varchar(255) NOT NULL,
  `image_title` varchar(255) NOT NULL,
  `comment` varchar(255) NOT NULL,
  `display_contact_form` tinyint(1) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `bloc_contact_lang`
--

CREATE TABLE IF NOT EXISTS `bloc_contact_lang` (
  `l_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `bloc_contact_id` int(10) unsigned NOT NULL,
  `lang_id` varchar(2) NOT NULL,
  `l_comment` text NOT NULL,
  `l_image_title` varchar(255) NOT NULL,
  PRIMARY KEY (`l_id`),
  KEY `bloc_contact_id` (`bloc_contact_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `bloc_document`
--

CREATE TABLE IF NOT EXISTS `bloc_document` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `bloc_document_document`
--

CREATE TABLE IF NOT EXISTS `bloc_document_document` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `bloc_document_id` int(10) unsigned NOT NULL,
  `file` varchar(255) CHARACTER SET utf8 NOT NULL,
  `mime_type` varchar(255) CHARACTER SET utf8 NOT NULL,
  `title` varchar(255) CHARACTER SET utf8 NOT NULL,
  `description` varchar(500) CHARACTER SET utf8 NOT NULL,
  `datetime` datetime NOT NULL,
  `rank` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `bloc_document_id` (`bloc_document_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `bloc_document_document_lang`
--

CREATE TABLE IF NOT EXISTS `bloc_document_document_lang` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `bloc_document_document_id` int(10) unsigned NOT NULL,
  `lang_id` varchar(2) CHARACTER SET utf8 NOT NULL,
  `l_title` varchar(255) CHARACTER SET utf8 NOT NULL,
  `l_file` varchar(255) CHARACTER SET utf8 NOT NULL,
  `l_description` varchar(500) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`id`),
  KEY `bloc_document_document_id` (`bloc_document_document_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `bloc_editor`
--

CREATE TABLE IF NOT EXISTS `bloc_editor` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `html` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `bloc_editor_lang`
--

CREATE TABLE IF NOT EXISTS `bloc_editor_lang` (
  `l_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `bloc_editor_id` int(10) unsigned NOT NULL,
  `lang_id` varchar(2) NOT NULL,
  `l_html` text NOT NULL,
  PRIMARY KEY (`l_id`),
  KEY `bloc_editor_id` (`bloc_editor_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `bloc_flickr`
--

CREATE TABLE IF NOT EXISTS `bloc_flickr` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` varchar(60) NOT NULL,
  `set_id` bigint(20) unsigned,
  `nbr_images` tinyint(3) unsigned NOT NULL,
  `show_as_carrousel` tinyint(1) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `bloc_googlemap`
--

CREATE TABLE IF NOT EXISTS `bloc_googlemap` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `iframe` text CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `bloc_lang`
--

CREATE TABLE IF NOT EXISTS `bloc_lang` (
  `l_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `bloc_id` int(10) unsigned NOT NULL,
  `lang_id` varchar(2) CHARACTER SET utf8 NOT NULL,
  `l_title` varchar(255) CHARACTER SET utf8 NOT NULL,
  `l_title_url` varchar(255) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`l_id`),
  KEY `bloc_id` (`bloc_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `bloc_people`
--

CREATE TABLE IF NOT EXISTS `bloc_people` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `columns` tinyint(1) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `bloc_people_people`
--

CREATE TABLE IF NOT EXISTS `bloc_people_people` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `bloc_people_id` int(10) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `telephone` varchar(255) NOT NULL,
  `telephone2` varchar(255) NOT NULL,
  `fax` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `department` varchar(255) NOT NULL,
  `description` varchar(500) NOT NULL,
  `image` varchar(255) NOT NULL,
  `rank` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `bloc_people_id` (`bloc_people_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `bloc_people_people_lang`
--

CREATE TABLE IF NOT EXISTS `bloc_people_people_lang` (
  `l_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `lang_id` varchar(2) NOT NULL,
  `bloc_people_people_id` int(10) unsigned NOT NULL,
  `l_title` varchar(255) NOT NULL,
  `l_department` varchar(255) NOT NULL,
  `l_description` varchar(500) NOT NULL,
  PRIMARY KEY (`l_id`),
  KEY `bloc_people_people_id` (`bloc_people_people_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `bloc_youtube`
--

CREATE TABLE IF NOT EXISTS `bloc_youtube` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `link` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `bloc_youtube_lang`
--

CREATE TABLE IF NOT EXISTS `bloc_youtube_lang` (
  `l_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `bloc_youtube_id` int(10) unsigned NOT NULL,
  `lang_id` varchar(2) NOT NULL,
  `l_link` text NOT NULL,
  PRIMARY KEY (`l_id`),
  KEY `bloc_youtube_id` (`bloc_youtube_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `bloc_feature`
--

CREATE TABLE IF NOT EXISTS `bloc_feature` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `layout` tinyint(1) unsigned NOT NULL COMMENT '1=colonnes, 2=lignes',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `bloc_feature_feature`
--

CREATE TABLE IF NOT EXISTS `bloc_feature_feature` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `bloc_feature_id` int(10) unsigned NOT NULL,
  `image` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `rank` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `bloc_feature_id` (`bloc_feature_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `bloc_feature_feature_lang`
--

CREATE TABLE IF NOT EXISTS `bloc_feature_feature_lang` (
  `l_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `bloc_feature_feature_id` int(10) unsigned NOT NULL,
  `lang_id` varchar(2) NOT NULL,
  `l_title` varchar(255) NOT NULL,
  `l_description` text NOT NULL,
  PRIMARY KEY (`l_id`),
  KEY `bloc_feature_feature_id` (`bloc_feature_feature_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


-- --------------------------------------------------------

--
-- Table structure for table `bloc_image`
--

CREATE TABLE IF NOT EXISTS `bloc_image` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `image` varchar(255) NOT NULL,
  `image_title` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `bloc_image_lang`
--

CREATE TABLE IF NOT EXISTS `bloc_image_lang` (
  `l_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `bloc_image_id` int(10) unsigned NOT NULL,
  `lang_id` varchar(2) NOT NULL,
  `l_image_title` varchar(255) NOT NULL,
  `l_image` varchar(255) NOT NULL,
  PRIMARY KEY (`l_id`),
  KEY `bloc_image_id` (`bloc_image_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


-- --------------------------------------------------------

--
-- Table structure for table `bloc_clouddocument`
--

CREATE TABLE `bloc_clouddocument` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `path` varchar(255) NOT NULL,
  `previous_folder_hash` varchar(255) NOT NULL,
  `previous_folder` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bloc_feature_feature`
--
ALTER TABLE `bloc_feature_feature`
  ADD CONSTRAINT `bloc_feature_feature_ibfk_1` FOREIGN KEY (`bloc_feature_id`) REFERENCES `bloc_feature` (`id`);

--
-- Constraints for table `bloc_feature_feature_lang`
--
ALTER TABLE `bloc_feature_feature_lang`
  ADD CONSTRAINT `bloc_feature_feature_lang_ibfk_1` FOREIGN KEY (`bloc_feature_feature_id`) REFERENCES `bloc_feature_feature` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `bloc_achievement_lang`
--
ALTER TABLE `bloc_achievement_lang`
  ADD CONSTRAINT `bloc_achievement_lang_ibfk_1` FOREIGN KEY (`bloc_achievement_id`) REFERENCES `bloc_achievement` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `bloc_citation_citation`
--
ALTER TABLE `bloc_citation_citation`
  ADD CONSTRAINT `bloc_citation_citation_ibfk_1` FOREIGN KEY (`bloc_citation_id`) REFERENCES `bloc_citation` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `bloc_citation_citation_lang`
--
ALTER TABLE `bloc_citation_citation_lang`
  ADD CONSTRAINT `bloc_citation_citation_lang_ibfk_1` FOREIGN KEY (`bloc_citation_citation_id`) REFERENCES `bloc_citation_citation` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `bloc_contact_lang`
--
ALTER TABLE `bloc_contact_lang`
  ADD CONSTRAINT `bloc_contact_lang_ibfk_1` FOREIGN KEY (`bloc_contact_id`) REFERENCES `bloc_contact` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `bloc_document_document`
--
ALTER TABLE `bloc_document_document`
  ADD CONSTRAINT `bloc_document_document_ibfk_1` FOREIGN KEY (`bloc_document_id`) REFERENCES `bloc_document` (`id`);

--
-- Constraints for table `bloc_document_document_lang`
--
ALTER TABLE `bloc_document_document_lang`
  ADD CONSTRAINT `bloc_document_document_lang_ibfk_1` FOREIGN KEY (`bloc_document_document_id`) REFERENCES `bloc_document_document` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `bloc_editor_lang`
--
ALTER TABLE `bloc_editor_lang`
  ADD CONSTRAINT `bloc_editor_lang_ibfk_1` FOREIGN KEY (`bloc_editor_id`) REFERENCES `bloc_editor` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `bloc_lang`
--
ALTER TABLE `bloc_lang`
  ADD CONSTRAINT `bloc_lang_ibfk_1` FOREIGN KEY (`bloc_id`) REFERENCES `bloc` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `bloc_people_people`
--
ALTER TABLE `bloc_people_people`
  ADD CONSTRAINT `bloc_people_people_ibfk_1` FOREIGN KEY (`bloc_people_id`) REFERENCES `bloc_people` (`id`);

--
-- Constraints for table `bloc_people_people_lang`
--
ALTER TABLE `bloc_people_people_lang`
  ADD CONSTRAINT `bloc_people_people_lang_ibfk_1` FOREIGN KEY (`bloc_people_people_id`) REFERENCES `bloc_people_people` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `bloc_youtube_lang`
--
ALTER TABLE `bloc_youtube_lang`
  ADD CONSTRAINT `bloc_youtube_lang_ibfk_1` FOREIGN KEY (`bloc_youtube_id`) REFERENCES `bloc_youtube` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
  
--
-- Constraints for table `bloc_image_lang`
--
ALTER TABLE `bloc_image_lang`
  ADD CONSTRAINT `bloc_image_lang_ibfk_1` FOREIGN KEY (`bloc_image_id`) REFERENCES `bloc_image` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `cms_alias`
--
ALTER TABLE `cms_alias`
  ADD CONSTRAINT `cms_alias_ibfk_1` FOREIGN KEY (`section_id`) REFERENCES `cms_section` (`id`);

--
-- Constraints for table `cms_alias_lang`
--
ALTER TABLE `cms_alias_lang`
  ADD CONSTRAINT `cms_alias_lang_ibfk_1` FOREIGN KEY (`cms_alias_id`) REFERENCES `cms_alias` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `cms_alias_route`
--
ALTER TABLE `cms_alias_route`
  ADD CONSTRAINT `cms_alias_route_ibfk_1` FOREIGN KEY (`alias_id`) REFERENCES `cms_alias` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `cms_section_lang`
--
ALTER TABLE `cms_section_lang`
  ADD CONSTRAINT `cms_section_lang_ibfk_1` FOREIGN KEY (`cms_section_id`) REFERENCES `cms_section` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
  
  
INSERT INTO `cms_alias` (
`id` ,
`alias` ,
`section_id` ,
`title` ,
`allow_children` ,
`lft` ,
`rgt` ,
`level`,
`keyword`
)
VALUES (
NULL , '', NULL , '', '0', '1', '2', '1', 'home'
);

INSERT INTO `cms_alias_lang` (
`l_id` ,
`cms_alias_id` ,
`lang_id` ,
`l_alias` ,
`l_title`
)
VALUES (
NULL , '1', 'fr', '', 'Accueil'
), (
NULL , '1', 'en', '', 'Home'
);

INSERT INTO `AuthAssignment` (`itemname`, `userid`, `bizrule`, `data`) VALUES
('Dev', '1', NULL, NULL),
('Admin', 'admin-1', NULL, 'N\;');


INSERT INTO `AuthItem` (`name`, `type`, `description`, `bizrule`, `data`) VALUES
('adminSections', 0, 'Administration des sections', NULL, 'N\;'),
('adminUsers', 0, 'Administration des Comptes', NULL, 'N\;'),
('adminSettings', 0, 'Administration des paramètres', NULL, 'N\;'),
('Admin', 2, 'Administration', NULL, 'N\;'),
('adminAlias', 0, 'Administration des alias', NULL, 'N\;');


INSERT INTO `AuthItemChild` (`parent`, `child`) VALUES
('Admin', 'adminAlias'),
('Admin', 'adminSections'),
('Admin', 'adminSettings'),
('Admin', 'adminUsers');

INSERT INTO `user` (`id`, `username`, `password`) VALUES
(1, 'Administrator', 'd5197d93c063a2b1e22d1630a39b7aef');

