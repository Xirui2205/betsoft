INSERT INTO `vic_main`.`database_patch`(`revision`, `not_reapplicable`,
`note`) VALUES
 ('7209', 1 , 'Cancel of the ticket collection (mantis:482)');

ALTER TABLE `vic_main`.`ticket` ADD `collection_account_user_id` INT NULL AFTER `collection_host_id` ;

START TRANSACTION;

INSERT INTO `vic_main`.`financial_transaction_type` (`id`, `name`, `note`, `from`, `from_sub`, `thru`, `thru_sub`, `to`, `to_sub`, `need_confirm`, `late_deposit`, `debiting`, `account_type`, `low_limit`, `high_limit`, `has_fee`, `fee_fix`, `fee_rel`, `is_limit_editable`, `is_fee_editable`, `daybook_collection`, `note_daybook`, `changing_bonus`, `status_changing_balance`, `status_for_accounting`, `inverse_accounting`) VALUES
(52, 'branch.ticket.collect-cancel', 'Storno vyplaty tiketu v hotovosti na pobocce', '211', '%branchHandle%', NULL, '999', '379', '999', 0, 0, 0, 'host', '0.00', NULL, 'never', NULL, NULL, 0, 0, 'POK', 'Storno výplaty sázenky: %ticketId%.', 0, 'pending', 'ok', 1),
(53, 'branch.user.deposit-cash-ticket-win-cancel', 'Storno vkladu uzivatele v hotovosti na pobocce za vyhrany tiket (transakce stran pobocky)', NULL, '998', NULL, '999', NULL, '998', 0, 0, 0, 'host', '0.00', NULL, 'never', NULL, NULL, 0, 0, NULL, 'Storno dotace konta uživatele: %userId%.', 0, 'pending', 'ok', 0),
(54, 'user.deposit.cash-ticket-win-cancel', 'Storno vkladu peněz v hotovosti na pobočce za vyhrany tiket', '324', '999', NULL, '999', '211', '%branchHandle%', 0, 0, 0, 'user', '0.00', NULL, 'never', NULL, NULL, 1, 0, 'POK', 'Storno dotace konta uživatele: %userId%.', 0, 'pending', 'ok', 1);
COMMIT;
