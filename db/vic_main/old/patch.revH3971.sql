start transaction;

INSERT INTO `financial_transaction_type` (`name`, `note`, `from`, `from_sub`, `thru`, `thru_sub`, `to`, `to_sub`, `need_confirm`, `late_deposit`, `debiting`, `account_type`, `low_limit`, `high_limit`, `has_fee`, `fee_fix`, `fee_rel`, `is_limit_editable`, `is_fee_editable`, `daybook_collection`, `note_daybook`, `changing_bonus`, `status_changing_balance`, `status_for_accounting`, `inverse_accounting`) VALUES
('crown.ticket.collect-nocash', 'Výplata tiketu korunky v příkazem na účet přimo od společnosti Korunka.', '379', '999', NULL, '999', '211', '%branchHandle%', 0, 0, 0, 'host', NULL, 0.00, 'never', NULL, NULL, 0, 0, 'POK', 'Výplata sázenky: %ticketId%.', 0, 'pending', 'ok', 1),
('crown.ticket.collect-cash', 'Výplata tiketu korunky v hotovosti na pobočce.', '379', '999', NULL, '999', '211', '%branchHandle%', 0, 0, 0, 'host', NULL, 3000.00, 'never', NULL, NULL, 0, 0, 'POK', 'Výplata sázenky: %ticketId%.', 0, 'pending', 'ok', 1),
('crown.ticket.cancel', 'Storno tiketu korunky zalozeneho na poboce.', '602', '998', NULL, '999', '211', '%branchHandle%', 0, 0, 0, 'host', NULL, 0.00, 'never', NULL, NULL, 0, 0, 'POK', 'Zrušení sázenky: %ticketId%.', 0, 'pending', 'ok', 1),
('crown.ticket.create', 'Založení tiketu korunky na pobočce za peníze.', '211', '%branchHandle%', NULL, '999', '602', '998', 0, 0, 0, 'host', 0.00, NULL, 'never', NULL, NULL, 0, 0, 'POK', 'Sázenka: %ticketId%.', 0, 'pending', 'ok', 0);

commit;
