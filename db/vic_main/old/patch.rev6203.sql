ALTER TABLE `vic_main`.`financial_transaction`
 ADD `admin_create` INT(10) UNSIGNED NULL DEFAULT NULL,
 ADD `admin_confirm` INT(10) UNSIGNED NULL DEFAULT NULL,
 ADD `admin_deposit` INT(10) UNSIGNED NULL DEFAULT NULL;

ALTER TABLE `vic_main`.`coupon_data`
 ADD `confirm_date` DATETIME NULL DEFAULT NULL,
 ADD `bookmaker_id` INT(10) UNSIGNED NULL DEFAULT NULL;
ALTER TABLE `vic_main`.`coupon_data_archive`
 ADD `confirm_date` DATETIME NULL DEFAULT NULL,
 ADD `bookmaker_id` INT(10) UNSIGNED NULL DEFAULT NULL;

ALTER TABLE `vic_main`.`ticket`
 ADD `coupon_admin_id` INT(10) UNSIGNED NULL DEFAULT NULL,
 ADD `confirm_date` DATETIME NULL DEFAULT NULL,
 ADD `confirm_bookmaker_id` INT(10) UNSIGNED NULL DEFAULT NULL;
