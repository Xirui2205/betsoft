ALTER TABLE `vic_main`.`financial_transaction`
 ADD COLUMN `balance_changed` BOOLEAN NOT NULL DEFAULT 0;

ALTER TABLE `vic_main`.`financial_transaction_type`
 ADD COLUMN `status_changing_balance` ENUM('ok', 'pending', 'pre-deposit') DEFAULT 'pending',
 ADD column `status_for_accounting` ENUM('ok', 'pending', 'pre-deposit') DEFAULT 'ok';

START TRANSACTION;

UPDATE `vic_main`.`financial_transaction` t
 JOIN `vic_main`.`financial_transaction_type` tt
 ON t.`type_id`=tt.`id`
 SET t.`balance_changed`=1
 WHERE t.`status`='ok' OR t.`status`='pending'
  OR (t.`status`='pre-deposit' AND tt.`need_confirm`=1);

UPDATE `vic_main`.`financial_transaction_type`
 SET `status_changing_balance`='ok'
 WHERE `name`='branch.deposit';

UPDATE `vic_main`.`financial_transaction_type`
 SET `status_for_accounting`='pre-deposit'
 WHERE `name`='user.withdraw.cash';

COMMIT;
