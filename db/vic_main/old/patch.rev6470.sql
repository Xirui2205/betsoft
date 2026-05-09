ALTER TABLE `vic_main`.`financial_transaction`
 CHANGE `status` `status` ENUM( 'pending', 'canceled', 'pre-deposit', 'no-deposit', 'ok' ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;

ALTER TABLE `vic_main`.`financial_transaction_type`
 CHANGE `status_for_accounting` `status_for_accounting` ENUM( 'pending', 'canceled', 'pre-deposit', 'no-deposit', 'ok' ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT 'ok';

START TRANSACTION;

UPDATE `vic_main`.`financial_transaction_type`
 SET `status_for_accounting`='pre-deposit'
 WHERE `name`='branch.deposit';

COMMIT;
