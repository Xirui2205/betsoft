START TRANSACTION;
UPDATE `vic_main`.`financial_transaction_type` SET `to` = '548' WHERE `financial_transaction_type`.`id` =7;
UPDATE `vic_main`.`financial_transaction_type` SET `note` = 'Poplatky jdoucí do výnosu',`debiting`=1 WHERE `financial_transaction_type`.`id` =24;
COMMIT;
