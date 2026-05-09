ALTER TABLE `vic_main`.`point_type` ADD `rate` DECIMAL( 11, 2 ) NOT NULL;

ALTER TABLE `vic_main`.`point_account`
    ADD `balance_get` DECIMAL( 11, 2 ) NOT NULL ,
    ADD `balance_spend` DECIMAL( 11, 2 ) NOT NULL ,
    ADD `balance_exchange` DECIMAL( 11, 2 ) NOT NULL;

ALTER TABLE `vic_main`.`point_transaction`
    ADD `balance_get` DECIMAL( 11, 2 ) NOT NULL ,
    ADD `balance_spend` DECIMAL( 11, 2 ) NOT NULL ,
    ADD `balance_exchange` DECIMAL( 11, 2 ) NOT NULL;

ALTER TABLE `vic_main`.`point_transaction_type` ADD `kind` ENUM( 'get', 'spend', 'exchange' ) NOT NULL AFTER `name`; 

START TRANSACTION;

UPDATE `vic_main`.`point_type` SET `rate` = 100 WHERE `name` = 'default';

INSERT INTO `vic_main`.`point_transaction_type`
    (`id`, `name`, `point_type_id`, `note`, `from`, `thru`, `to`, `debiting`, `low_limit`, `high_limit`)
  VALUES
    (6, 'user.activation', 1, '', NULL, NULL, NULL, 0, 0, NULL),
    (7, 'preferenceRate', 1, '', NULL, NULL, NULL, 0, NULL, 0),
    (8, 'exchangeToMoney', 1, '', NULL, NULL, NULL, 0, NULL, 0);

UPDATE `vic_main`.`point_transaction_type` SET `name` = 'pointTicket.create' WHERE `point_transaction_type`.`id` =1;
UPDATE `vic_main`.`point_transaction_type` SET `name` = 'pointTicket.cancel' WHERE `point_transaction_type`.`id` =2;
UPDATE `vic_main`.`point_transaction_type` SET `name` = 'pointTicket.payout' WHERE `point_transaction_type`.`id` =3;
UPDATE `vic_main`.`point_transaction_type` SET `name` = 'moneyTicket.create' WHERE `point_transaction_type`.`id` =4;
UPDATE `vic_main`.`point_transaction_type` SET `name` = 'branch.visit' WHERE `point_transaction_type`.`id` =5;

INSERT INTO `vic_main`.`financial_transaction_type`
        (`id`, `name`, `note`, `from`, `thru`, `to`, `need_confirm`, `late_deposit`, `debiting`, `account_type`, `low_limit`, `high_limit`, `has_fee`, `fee_fix`, `fee_rel`)
    VALUES
        (25, 'exchangeFromPoints', '', NULL, NULL, NULL, 0, 0, 0, 'user', 0, NULL, 'never', NULL, NULL);


UPDATE `vic_main`.`point_transaction_type` SET `kind` = 'spend' WHERE `point_transaction_type`.`id` =1;
UPDATE `vic_main`.`point_transaction_type` SET `kind` = 'spend' WHERE `point_transaction_type`.`id` =7;
UPDATE `vic_main`.`point_transaction_type` SET `kind` = 'exchange' WHERE `point_transaction_type`.`id` =8;


COMMIT;
