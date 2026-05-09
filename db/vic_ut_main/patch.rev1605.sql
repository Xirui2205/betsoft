-- all related to PayMUZO implementation (other related patches rev1596, rev1600)

CREATE TABLE `vic_ut_main`.`webpay_order` (
`order_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
`user_id` INT(10) UNSIGNED NOT NULL,
`transaction_id` INT(11) UNSIGNED NULL DEFAULT NULL,
`amount` DECIMAL(11,2) NOT NULL,
`fee` DECIMAL(11,2) NOT NULL,
`currency_id` SMALLINT(5) UNSIGNED NOT NULL,
`created` DATETIME NOT NULL,
`updated` DATETIME NULL DEFAULT NULL,
`status` SMALLINT(5) NOT NULL COMMENT '-1=failed, 0=pre-request, 1=in progress, 2=deposited, 3=rejected',
`result_text` TEXT NULL DEFAULT NULL,
FOREIGN KEY (`user_id`) REFERENCES `vic_ut_main`.`uzivatel` (`user_id`),
FOREIGN KEY (`transaction_id`) REFERENCES `vic_ut_main`.`financial_transaction` (`transaction_id`),
FOREIGN KEY (`currency_id`) REFERENCES `vic_ut_main`.`mena` (`mena_id`),
INDEX (`status` )
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_czech_ci;

ALTER TABLE `vic_ut_main`.`mena` ADD `webpay_merchant` INT(10) NULL DEFAULT NULL, ADD INDEX (`webpay_merchant`);
ALTER TABLE `vic_ut_main`.`mena` ADD `smallest_unit` DECIMAL(11,2) DEFAULT 100.0 COMMENT 'eg. 100 = cents for EUR or halers for CZK';

ALTER TABLE `vic_ut_main`.`financial_transaction` CHANGE `status` `status` ENUM( 'ok', 'pending', 'canceled', 'pre-deposit', 'no-deposit') CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL;
ALTER TABLE `vic_ut_main`.`financial_transaction` ADD `deposit_time` DATETIME NULL DEFAULT NULL AFTER `cancelTime`;
ALTER TABLE `vic_ut_main`.`financial_transaction` ADD `fee` DECIMAL (11,2) NULL DEFAULT NULL;
ALTER TABLE `vic_ut_main`.`financial_transaction` ADD `fee_transaction_id` INT(11) UNSIGNED NULL DEFAULT NULL, ADD FOREIGN KEY (`fee_transaction_id`) REFERENCES `financial_transaction` (`transaction_id`);
ALTER TABLE `vic_ut_main`.`financial_transaction_type` ADD `late_deposit` TINYINT(1) NOT NULL DEFAULT '0' AFTER `need_confirm`;
ALTER TABLE `vic_ut_main`.`financial_transaction_type` ADD `has_fee` ENUM ('never', 'sometimes', 'always') DEFAULT 'never';
ALTER TABLE `vic_ut_main`.`financial_transaction_type` ADD `fee_fix` DECIMAL(11,2) NULL DEFAULT NULL;
ALTER TABLE `vic_ut_main`.`financial_transaction_type` ADD `fee_rel` DECIMAL(11,2) NULL DEFAULT NULL;

