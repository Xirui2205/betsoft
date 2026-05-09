START TRANSACTION;

UPDATE `vic_main`.`financial_transaction_type`
 SET `status_for_accounting`='ok'
 WHERE `name`='branch.deposit';

COMMIT;
