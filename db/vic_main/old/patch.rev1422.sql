TRUNCATE TABLE `finacni_transakce`;
RENAME TABLE `vic_main`.`finacni_transakce`  TO `vic_main`.`financial_transaction` ;
ALTER TABLE `financial_transaction` DROP `poplatky`;
ALTER TABLE `financial_transaction` CHANGE `castka` `value` DECIMAL( 11, 2 ) NOT NULL DEFAULT '0.00', CHANGE `datum` `time` DATETIME NOT NULL ;

ALTER TABLE `financial_transaction` DROP FOREIGN KEY `financial_transaction_ibfk_1` ;
ALTER TABLE `financial_transaction` CHANGE `typ_platby` `type_id` INT( 10 ) UNSIGNED NOT NULL DEFAULT '1' COMMENT '1 vklad 2 vyber';
ALTER TABLE `financial_transaction` ADD FOREIGN KEY ( `type_id` ) REFERENCES `vic_main`.`financial_transaction_type` ( `id` );
ALTER TABLE `vic_main`.`financial_transaction` DROP INDEX `typ_platby` , ADD INDEX `type_id` ( `type_id` );

ALTER TABLE `financial_transaction` CHANGE `transakce_id` `transaction_id` INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `financial_transaction` ADD `status` ENUM( 'ok', 'pending', 'canceled' ) NOT NULL , ADD INDEX ( `status` );

ALTER TABLE `financial_transaction` DROP `account_type` ;
ALTER TABLE `financial_transaction` ADD `account_type` ENUM( 'user', 'branch', 'other' ) NOT NULL , ADD INDEX ( `account_type` ) ;

ALTER TABLE `financial_transaction_type` ADD `need_confirm` BOOLEAN NOT NULL , ADD `debiting` BOOLEAN NOT NULL ;

ALTER TABLE `financial_transaction` ADD `okTime` DATETIME NULL , ADD `cancelTime` DATETIME NULL ;

ALTER TABLE `financial_transaction` ADD INDEX ( `time` ) ;
