START TRANSACTION;

UPDATE `vic_main`.`financial_transaction_type`
SET 
`from` = NULL,
`from_sub` = NULL,
`to` = NULL,
`to_sub` = NULL
WHERE `from` = 221 || `to` = 221;

COMMIT;