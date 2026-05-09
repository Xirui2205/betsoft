START TRANSACTION;
UPDATE `vic_main`.`financial_transaction_type` SET `from_sub`='%branchHandle%' WHERE `from_sub`='%providerIc%';
UPDATE `vic_main`.`financial_transaction_type` SET `to_sub`='%branchHandle%' WHERE `to_sub`='%providerIc%';
COMMIT;
