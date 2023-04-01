#ALTER TABLE `crowl_merchant_name_new` ADD `notification_level` TINYINT(1)  NOT NULL  DEFAULT '1'  AFTER `created`;
ALTER TABLE `violator_notification_email_settings` ADD `reset_after_reaching` TINYINT(1)  NOT NULL  DEFAULT '0'  AFTER `email_to`;
