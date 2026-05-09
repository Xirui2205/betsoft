ALTER TABLE `vic_ut_main`.`point_type` ADD `rate` DECIMAL( 11, 2 ) NOT NULL;

ALTER TABLE `vic_ut_main`.`point_account`
	ADD `balance_get` DECIMAL( 11, 2 ) NOT NULL ,
	ADD `balance_spend` DECIMAL( 11, 2 ) NOT NULL ,
	ADD `balance_exchange` DECIMAL( 11, 2 ) NOT NULL;

ALTER TABLE `vic_ut_main`.`point_transaction` 
	ADD `balance_get` DECIMAL( 11, 2 ) NOT NULL ,
	ADD `balance_spend` DECIMAL( 11, 2 ) NOT NULL ,
	ADD `balance_exchange` DECIMAL( 11, 2 ) NOT NULL;
	
ALTER TABLE `vic_ut_main`.`point_transaction_type` ADD `kind` ENUM( 'get', 'spend', 'exchange' ) NOT NULL AFTER `name`; 
