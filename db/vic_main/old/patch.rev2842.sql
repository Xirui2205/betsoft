ALTER TABLE `vic_main`.`financial_transaction` ADD `account_type` ENUM( 'user', 'host', 'other' ) NULL DEFAULT NULL;
