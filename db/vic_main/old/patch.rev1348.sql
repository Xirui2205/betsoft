ALTER TABLE `finacni_transakce` ADD `account_type` INT UNSIGNED NOT NULL;
UPDATE `vic_admin`.`branch` SET `currency_id` = '8' WHERE `branch`.`id` =1;
