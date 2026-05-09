START TRANSACTION;

UPDATE `vic_main`.`financial_transaction_type`
SET
	`daybook_collection` = 'POK'
WHERE
	`name` IN (
		'branch.ticket.create-cash',
		'branch.ticket.cancel-cash',
		'user.deposit.cash',
		'branch.ticket.collect',
		'branch.user.withdraw-cash',
		'branch.withdraw'
	);

UPDATE `vic_main`.`financial_transaction_type`
SET
	`daybook_collection` = 'WEB'
WHERE
	`name` IN (
		'user.ticket.create',
		'user.ticket.cancel',
		'user.ticket.renew-canceled',
		'user.ticket.payout',
		'user.ticket.payout-cancel'
	);

	
UPDATE `vic_main`.`financial_transaction_type`
SET
	`daybook_collection` = 'BNK'
WHERE
	`name` IN (
		'user.deposit.card',
		'user.deposit.bank'
	);
	
UPDATE `vic_main`.`financial_transaction_type`
SET
	`daybook_collection` = 'INT'
WHERE
	`name` IN (
		'user.withdraw.cash',
		'user.withdraw.bank',
		'fee.general'
	);

COMMIT;

