ALTER TABLE `vic_main`.`coupon_data` ADD `host_id` INT( 10 ) UNSIGNED NOT NULL AFTER `admin_id`;
ALTER TABLE `vic_main`.`coupon_data_archive` ADD `host_id` INT( 10 ) UNSIGNED NOT NULL AFTER `admin_id`;

START TRANSACTION;
UPDATE `vic_main`.`coupon_data` SET `host_id`=1;
UPDATE `vic_main`.`coupon_data_archive` SET `host_id`=1;
COMMIT;
