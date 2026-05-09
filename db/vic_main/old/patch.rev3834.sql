START TRANSACTION;
UPDATE `vic_main`.`financial_transaction_type` SET `from` = NULL ,
`to` = NULL WHERE `financial_transaction_type`.`id` =26;

UPDATE `vic_main`.`financial_transaction_type` SET `from` = NULL ,
`to` = NULL WHERE `financial_transaction_type`.`id` =27;
COMMIT;
