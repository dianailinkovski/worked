ALTER TABLE `crowl_merchant_name_new` ADD `status` TINYINT NOT NULL DEFAULT '1' AFTER `logo_img_url`; 

ALTER TABLE `crowl_merchant_name_new` ADD `phone` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `logo_img_url` ,
ADD `fax` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `phone` ,
ADD `address_1` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `fax` ,
ADD `address_2` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `address_1` ,
ADD `city` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `address_2` ,
ADD `state` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `city` ,
ADD `zip` VARCHAR( 20 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `state` ,
ADD `contact_email` VARCHAR( 150 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `zip` ,
ADD `contact_url` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `contact_email`;