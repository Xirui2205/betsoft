ALTER TABLE `vic_main`.`financial_transaction` 
  CHANGE `branch_id` `host_id` INT( 10 ) UNSIGNED NULL DEFAULT NULL;
  
ALTER TABLE `vic_main`.`point_transaction`
  CHANGE `branch_id` `host_id` INT( 10 ) UNSIGNED NULL DEFAULT NULL;
  
ALTER TABLE `vic_main`.`financial_transaction_type` 
  CHANGE `account_type` `account_type`
    ENUM( 'user', 'host', 'other' )
    CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;


START TRANSACTION;    
UPDATE `vic_main`.`financial_transaction_type`
SET
`account_type` = 'host'
WHERE `account_type` = '';
COMMIT;
