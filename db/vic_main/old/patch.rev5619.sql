START TRANSACTION;

INSERT INTO `vic_main`.`financial_transaction_type` (`id`, `name`, `note`, `from`, `from_sub`, `thru`, `thru_sub`, `to`, `to_sub`, `need_confirm`, `late_deposit`, `debiting`, `account_type`, `low_limit`, `high_limit`, `has_fee`, `fee_fix`, `fee_rel`, `is_limit_editable`, `is_fee_editable`, `daybook_collection`, `note_daybook`) VALUES
(44, 'branch.user.deposit-cash-ticket-win', 'Vklad uzivatele v hotovosti na pobocce za vyhrany tiket (transakce stran pobocky)', NULL, '999', NULL, '999', NULL, '999', 0, 0, 0, 'host', '0.00', NULL, 'never', NULL, NULL, 0, 0, NULL, 'Dotace konta uživatele: %userId%.'),
(45, 'user.deposit.cash-ticket-win', 'Vklad peněz v hotovosti na pobočce za vyhrany tiket', '211', '%hostId%', NULL, '999', '324', '999', 0, 0, 0, 'user', '0.00', NULL, 'never', NULL, NULL, 1, 0, 'POK', 'Dotace konta uživatele: %userId%.');

COMMIT;
