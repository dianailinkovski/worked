ALTER TABLE `violator_notifications_history`   
  CHANGE `email_number` `email_level` TINYINT(2) UNSIGNED NOT NULL,
  ADD COLUMN `email_repeat` TINYINT(2) UNSIGNED DEFAULT 1  NOT NULL AFTER `email_level`,
  ADD COLUMN `is_exit` TINYINT(1) UNSIGNED DEFAULT 0  NOT NULL AFTER `email_repeat`;