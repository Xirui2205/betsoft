START TRANSACTION;
ALTER TABLE `branch` ADD `currency_id` INT UNSIGNED NOT NULL, ADD INDEX ( `currency_id` );
UPDATE `vic_admin`.`branch` SET `currency_id` = '8' WHERE `branch`.`id` =1;
UPDATE `vic_admin`.`branch` SET `currency_id` = '8' WHERE `branch`.`id` =2;
COMMIT;
