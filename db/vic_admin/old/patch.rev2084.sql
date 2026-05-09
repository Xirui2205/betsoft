START TRANSACTION;
INSERT INTO `vic_main`.`financial_transaction_type` (`id`, `name`, `note`, `from`, `thru`, `to`, `need_confirm`, `late_deposit`, `debiting`, `account_type`, `low_limit`, `high_limit`, `has_fee`, `fee_fix`, `fee_rel`) VALUES
(26, 'user.manual-balance', '', NULL, NULL, NULL, 0, 0, 0, 'user', NULL, NULL, 'never', NULL, NULL);
COMMIT;

