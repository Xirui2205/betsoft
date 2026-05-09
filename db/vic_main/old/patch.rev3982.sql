ALTER TABLE `vic_main`.`financial_transaction_type` ADD `daybook_collection` VARCHAR( 16 ) NULL;
ALTER TABLE `vic_main`.`financial_transaction_type` ADD `note_daybook` TEXT NULL AFTER `daybook_collection`;

START TRANSACTION;

UPDATE `vic_main`.`financial_transaction_type`
SET
	`daybook_collection` = 'POK'
WHERE
	`name` IN (
		'branch.ticket.create-cash',
		'branch.ticket.cancel-cash',
		'branch.ticket.cancel-cash',
		'user.deposit.branch-cash',
		'branch.ticket.payout-cash',
		'branch.user-withdraw.cash',
		'branch.withdraw'
	);

UPDATE `vic_main`.`financial_transaction_type`
SET
	`daybook_collection` = 'WEB'
WHERE
	`name` IN (
		'user.ticket.create-online'
		'user.ticket.cancel-online'
		'user.ticket.payout-online'
		'user.ticket.payout-storno'
	);
	
UPDATE `vic_main`.`financial_transaction_type`
SET
	`daybook_collection` = 'BNK'
WHERE
	`name` IN (
		'user.deposit.online-card'
		'user.deposit.bank-transfer'
	);
	
UPDATE `vic_main`.`financial_transaction_type`
SET
	`daybook_collection` = 'INT'
WHERE
	`name` IN (
		'user.withdraw.cash-branch',
		'user.withdraw.bank-transfer',
		'fee.general'
	);

UPDATE `vic_main`.`financial_transaction_type` SET `note_daybook` = 'Výplata sázenky: %ticketId%' WHERE `financial_transaction_type`.`id` =18;
UPDATE `vic_main`.`financial_transaction_type` SET `note_daybook` = 'Výplata sázenky: %ticketId%' WHERE `financial_transaction_type`.`id` =6;
UPDATE `vic_main`.`financial_transaction_type` SET `note_daybook` = 'Výherní manipulační poplatek' WHERE `financial_transaction_type`.`id` =33;
UPDATE `vic_main`.`financial_transaction_type` SET `note_daybook` = 'Manipulační poplatek' WHERE `financial_transaction_type`.`id` =30;


COMMIT;

