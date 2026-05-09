CREATE TABLE `vic_main`.`financial_transaction_history` (
	`id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT ,
	`transaction_id` INT( 11 ) UNSIGNED NOT NULL ,
	`time` DATETIME NOT NULL ,
	`start_balance` DECIMAL( 11, 2 ) NOT NULL ,
	`end_balance` DECIMAL( 11, 2 ) NOT NULL ,
PRIMARY KEY ( `id` )
) ENGINE = InnoDB;

ALTER TABLE `vic_main`.`financial_transaction_history`
	ADD `status` ENUM( 'ok', 'pending', 'canceled', 'pre-deposit', 'no-deposit' ) NULL AFTER `transaction_id`;

ALTER TABLE `vic_main`.`financial_transaction_history` ADD INDEX ( `transaction_id` );

ALTER TABLE `vic_main`.`financial_transaction_history` ADD FOREIGN KEY ( `transaction_id` ) REFERENCES `vic_main`.`financial_transaction` (
`transaction_id`
);

START TRANSACTION;
INSERT INTO `vic_main`.`financial_transaction_history` (transaction_id,status,time,start_balance,end_balance) 
	(SELECT transaction_id, status, time, (balance - value) AS start_balance, balance FROM `vic_main`.`financial_transaction` WHERE 1);
COMMIT;
