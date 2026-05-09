ALTER TABLE `vic_admin`.`host` ADD `balance` DECIMAL( 60, 4 ) NOT NULL DEFAULT '0';
ALTER TABLE `vic_admin`.`host` CHANGE `balance` `balance` DECIMAL( 60, 4 ) NOT NULL;
ALTER TABLE `vic_admin`.`branch` DROP `balance`;

UPDATE `vic_admin`.`branch` SET `currency_id` = '8' WHERE `branch`.`id` =2;
