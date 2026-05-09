ALTER TABLE `vic_main`.`financial_transaction_type`
 ADD COLUMN `inverse_accounting` BOOLEAN NOT NULL DEFAULT 0;

START TRANSACTION;

UPDATE `vic_main`.`financial_transaction_type` SET `status_changing_balance`='ok'
 WHERE `name`='branch.withdraw';

UPDATE `vic_main`.`financial_transaction_type` SET `daybook_collection`='POK'
 WHERE `name`='branch.deposit';

UPDATE `vic_main`.`financial_transaction_type` SET `inverse_accounting`=1
 WHERE `name` LIKE ('%cancel%') AND `from` IS NOT NULL AND `to` IS NOT NULL AND `id` NOT IN (40,41,42,43);

COMMIT;
