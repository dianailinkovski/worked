ALTER TABLE `crowl_merchant_name_new` ADD `created_at` DATETIME NOT NULL AFTER `created`;

ALTER TABLE `crowl_merchant_name_new` ADD `modified_by` INT NOT NULL AFTER `user_id`,
ADD `modified_at` DATETIME NOT NULL AFTER `modified_by`;