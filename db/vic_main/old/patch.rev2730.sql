ALTER TABLE `vic_main`.`financial_transaction` ADD `canceled_transaction_id` INT (11) UNSIGNED NULL AFTER `transaction_id` ,
ADD INDEX ( `canceled_transaction_id` );

ALTER TABLE `vic_main`.`financial_transaction` ADD UNIQUE (
`canceled_transaction_id`
);

ALTER TABLE `vic_main`.`financial_transaction` ADD FOREIGN KEY ( `canceled_transaction_id` ) REFERENCES `vic_main`.`financial_transaction` (
`transaction_id`
);



