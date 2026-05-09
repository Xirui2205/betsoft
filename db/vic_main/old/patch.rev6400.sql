ALTER TABLE `vic_main`.`uzivatel`
 ADD COLUMN `entry_bonus_base` DECIMAL (10,2) NULL DEFAULT NULL,
 ADD COLUMN `entry_bonus_from` DATETIME NULL DEFAULT NULL,
 ADD COLUMN `entry_bonus_balance` DECIMAL (10,2) NOT NULL DEFAULT 0,
 ADD COLUMN `entry_bonus_applied` DECIMAL (10,2) NULL DEFAULT NULL,
 ADD COLUMN `entry_bonus_version` INT(10) UNSIGNED NOT NULL DEFAULT 1;

CREATE TABLE `vic_main`.`uzivatel_eb_balance` (
 `user_id` INT(10) UNSIGNED NOT NULL,
 `balance_day` DATETIME NOT NULL,
 `balance` DECIMAL(10,2) NOT NULL,
 PRIMARY KEY (`user_id`, `balance_day`)
) Engine=MyISAM DEFAULT CHARSET utf8 COLLATE utf8_general_ci;

ALTER TABLE `vic_main`.`financial_transaction_type`
 ADD COLUMN `changing_bonus` BOOLEAN NOT NULL DEFAULT 0;

START TRANSACTION;

INSERT INTO `vic_main`.`financial_transaction_type` (`id`, `name`, `note`, `from`, `from_sub`, `thru`, `thru_sub`, `to`, `to_sub`, `need_confirm`, `late_deposit`, `debiting`, `account_type`, `low_limit`, `high_limit`, `has_fee`, `fee_fix`, `fee_rel`, `is_limit_editable`, `is_fee_editable`, `daybook_collection`, `note_daybook`, `changing_bonus`) VALUES
(50, 'user.bonus.entry', 'Připsání vstupního bonusu uživateli', NULL, '999', NULL, '999', NULL, '999', 0, 0, 0, 'user', NULL, '0.00', 'never', NULL, NULL, 0, 0, 'WEB', 'Připsání vst.bonusu uživateli: %userId%.', 0),
(51, 'user.bonus.entry-cancel', 'Storno vstupního bonusu uživateli', NULL, '999', NULL, '999', NULL, '999', 0, 0, 1, 'user', NULL, '0.00', 'never', NULL, NULL, 0, 0, 'WEB', 'Storno vst.bonusu uživateli: %userId%.', 0);

UPDATE `vic_main`.`financial_transaction_type` SET `changing_bonus`=1
 WHERE `name` IN ('user.deposit.card', 'user.deposit.cash', 'user.deposit.bank');

INSERT INTO `vic_main`.`cronjob_type`(`type_id`, `type_name`) VALUES
 (8, 'ApplyEntryBonus');

INSERT INTO `vic_main`.`cronjob` (
`cronjob_id` ,
`cronjob_type` ,
`result` ,
`attempts` ,
`exec_at` ,
`executed_at` ,
`exec_constantly_at`
) VALUES
 (14, 8, NULL, 0, NULL , NULL , '30 0 * * *');

COMMIT;
