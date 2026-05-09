START TRANSACTION;

INSERT INTO `vic_main`.`financial_transaction_type` (`id`, `name`, `note`, `from`, `from_sub`, `thru`, `thru_sub`, `to`, `to_sub`, `need_confirm`, `late_deposit`, `debiting`, `account_type`, `low_limit`, `high_limit`, `has_fee`, `fee_fix`, `fee_rel`, `is_limit_editable`, `is_fee_editable`, `daybook_collection`, `note_daybook`) VALUES
(46, 'other.ticket.payout', 'Vyplaceni necash tiketu. (Přededepsaná výhra)', '548', '999', NULL, '999', '379', '999', 0, 0, 0, 'other', '0.00', NULL, 'never', NULL, NULL, 0, 0, 'WEB', 'Předepsaná výhra: %ticketId%.'),
(47, 'other.ticket.payout-mp', 'Vyherni manipulacni poplatek pri vyplaceni necash tiketu.  (Přededepsaná výhra)', '548', '999', NULL, '999', '379', '999', 0, 0, 0, 'other', NULL, '0.00', 'never', NULL, NULL, 0, 0, 'INT', 'Výherní manipulační poplatek sázenky: %ticketId%.');

UPDATE `vic_main`.`financial_transaction_type`
SET
	`name` = 'user.ticket.collect-nocash',
	`from` = '379',
	`thru` = NULL ,
	`note_daybook` = 'převod výhry na konto uživatele: Ticket: %ticketId%.' 
WHERE
	`financial_transaction_type`.`name` = 'user.ticket.payout';

UPDATE `vic_main`.`financial_transaction_type` 
SET
	`name` = 'user.ticket.collect-nocash-mp',
	`from` = '379',
	`thru` = NULL
WHERE
	`financial_transaction_type`.`name` = 'user.ticket.payout-mp';

INSERT INTO `vic_main`.`financial_transaction_type` (`id`, `name`, `note`, `from`, `from_sub`, `thru`, `thru_sub`, `to`, `to_sub`, `need_confirm`, `late_deposit`, `debiting`, `account_type`, `low_limit`, `high_limit`, `has_fee`, `fee_fix`, `fee_rel`, `is_limit_editable`, `is_fee_editable`, `daybook_collection`, `note_daybook`) VALUES
(48, 'other.ticket.payout-cancel', 'Zruseni vyplaceni necash tiketu', '379', '999', NULL, '999', '548', '999', 0, 0, 1, 'other', NULL, '0.00', 'never', NULL, NULL, 0, 0, 'WEB', 'Zrušení výplaty sázenky: %ticketId%.'),
(49, 'other.ticket.payout-cancel-mp', 'Zruseni vyherniho manipulacniho poplatku pri zruseni vyplaceni necash tiketu', '379', '999', NULL, '999', '548', '999', 0, 0, 1, 'other', '0.00', NULL, 'never', NULL, NULL, 0, 0, 'INT', 'Vrácení výherního manipulačního poplatku sázenky: %ticketId%.');

UPDATE `vic_main`.`financial_transaction_type` SET `name` = 'user.ticket.collect-nocash-cancel',
`thru` = NULL ,
`to` = '379' WHERE `financial_transaction_type`.`id` =7;

UPDATE `vic_main`.`financial_transaction_type` SET `name` = 'user.ticket.collect-nocash-cancel-mp',
`thru` = NULL ,
`to` = '379' WHERE `financial_transaction_type`.`id` =34;

COMMIT;
