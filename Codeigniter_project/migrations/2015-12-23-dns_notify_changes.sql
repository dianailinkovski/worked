ALTER TABLE `merchant_do_not_sell_notify` ADD `first_name` VARCHAR( 200 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `email` ,
ADD `last_name` VARCHAR( 200 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `first_name`;