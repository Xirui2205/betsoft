START TRANSACTION;

INSERT INTO `vic_main`.`database_patch`(`revision`, `not_reapplicable`,
`note`) VALUES
 ('7256', NULL , 'Cancel of the ticket collection correction (mantis:482)');

UPDATE `vic_main`.`financial_transaction_type` SET `low_limit` = NULL ,
`high_limit` = '0.00' WHERE `financial_transaction_type`.`id` = 53;

COMMIT;
