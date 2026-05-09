START TRANSACTION;

UPDATE `vic_main`.`financial_transaction_type` SET `from_sub` = '999' 
WHERE `financial_transaction_type`.`name` IN
	(
		'user.ticket.create',
		'other.ticket.collect-individual',
		'branch.ticket.collect',
		'branch.user.withdraw-cash',
		'user.ticket.create-mp',
		'user.ticket.renew-canceled',
		'user.ticket.renew-canceled-mp',
		'other.ticket.payout'
	);
	
UPDATE `vic_main`.`financial_transaction_type` SET `to_sub` = '999' 
WHERE `financial_transaction_type`.`name` IN
	(
		'user.ticket.cancel',
		'user.deposit.card',
		'user.deposit.cash',
		'user.ticket.cancel-mp',
		'user.deposit.cash-ticket-win'
	);
	
UPDATE `vic_main`.`financial_transaction_type` SET `to_sub` = '500' 
WHERE `financial_transaction_type`.`name` IN
	(
		'branch.deposit'
	);

UPDATE `vic_main`.`financial_transaction_type` SET `from_sub` = '500' 
WHERE `financial_transaction_type`.`name` IN
	(
		'branch.withdraw'
	);

UPDATE `vic_main`.`financial_transaction_type` SET `from_sub` = '999',`to_sub` = '999' 
WHERE `financial_transaction_type`.`name` IN 
	(
		'user.ticket.collect-nocash',
		'user.ticket.collect-nocash-cancel',
		'user.withdraw.cash',
		'user.withdraw.bank',
		'other.ticket.payout-cash',
		'other.ticket.payout-cash-cancel',
		'user.exchange-from-points',
		'user.ticket.collect-nocash-mp',
		'user.ticket.collect-nocash-cancel-mp',
		'other.ticket.payout-cash-mp',
		'other.ticket.payout-cash-cancel-mp',
		'other.ticket.payout-cancel'
	);

COMMIT;
