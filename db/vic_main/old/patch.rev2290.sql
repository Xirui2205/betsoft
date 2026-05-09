START TRANSACTION;

UPDATE `vic_main`.`financial_transaction_type` SET `low_limit` = '0',
`high_limit` = NULL WHERE `financial_transaction_type`.`id` =22;

UPDATE `vic_main`.`financial_transaction_type` SET `low_limit` = NULL ,
`high_limit` = '0' WHERE `financial_transaction_type`.`id` =23;

COMMIT;
