CREATE TABLE `vic_main`.`financial_transaction_type_currency` (
`financial_transaction_type_id` INT UNSIGNED NOT NULL ,
`currency_id` SMALLINT(5) UNSIGNED NOT NULL ,
`low_limit` DECIMAL( 11, 2 ) NULL ,
`high_limit` DECIMAL( 11, 2 ) NULL ,
`fee_fix` DECIMAL( 11, 2 ) NULL ,
`fee_rel` DECIMAL( 11, 2 ) NULL ,
PRIMARY KEY ( `financial_transaction_type_id` , `currency_id` )
) ENGINE = InnoDB;


ALTER TABLE `financial_transaction_type_currency` ADD FOREIGN KEY ( `financial_transaction_type_id` ) REFERENCES `vic_main`.`financial_transaction_type` (
`id`
);

ALTER TABLE `financial_transaction_type_currency` ADD FOREIGN KEY ( `currency_id` ) REFERENCES `vic_main`.`mena` (
`mena_id`
);
