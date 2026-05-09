ALTER TABLE `vic_main`.`financial_transaction` ADD `export_date` DATETIME NULL DEFAULT NULL;
ALTER TABLE `vic_main`.`user_bank_account` CHANGE `account_number` `account_number` BIGINT( 10 ) UNSIGNED NOT NULL 
