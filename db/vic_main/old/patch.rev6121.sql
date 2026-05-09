START TRANSACTION;

UPDATE `vic_main`.`financial_transaction_type`
SET
	`from_sub` = '998'
WHERE
	`from_sub` = '999';
	
UPDATE `vic_main`.`financial_transaction_type`
SET
	`to_sub` = '998'
WHERE
	`to_sub` = '999';

UPDATE `vic_main`.`financial_transaction_type`
SET
	`from_sub` = '%providerIc%'
WHERE
	`from_sub` = '%hostId%';
	
UPDATE `vic_main`.`financial_transaction_type`
SET
	`to_sub` = '%providerIc%'
WHERE
	`to_sub` = '%hostId%';

UPDATE `vic_main`.`financial_transaction_type`
SET
	`note_daybook` = 'Předpis výběru uživatele: %userId%'
WHERE
	`name` IN ('user.withdraw.cash','user.withdraw.bank');

UPDATE `vic_main`.`financial_transaction_type`
SET
	`to` = '602',
	`to_sub` = '997'
WHERE
	`name` = 'other.ticket.forfeit';


UPDATE `vic_main`.`financial_transaction_type`
SET
	`to` = '379',
	`from` = '324'
WHERE
	`name` = 'user.ticket.collect-nocash-mp';

UPDATE `vic_main`.`financial_transaction_type`
SET
	`to` = '324',
	`from` = '379'
WHERE
	`name` = 'user.ticket.collect-nocash-cancel-mp';
	
UPDATE `vic_main`.`financial_transaction_type`
SET
	`from` = '602',
	`from_sub` = '999',
	`to` = '602',
	`to_sub` = '997'
WHERE
	`name` = 'other.ticket.forfeit-mp';

UPDATE `vic_main`.`financial_transaction_type`
SET
	`from` = '379',
	`from_sub` = '999',
	`to` = '602',
	`to_sub` = '999'
WHERE
	`name` = 'other.ticket.payout-mp';
	
UPDATE `vic_main`.`financial_transaction_type`
SET
	`note_daybook` = 'Storno výběru výhry %ticketId%'
WHERE
	`name` = 'other.ticket.payout-cancel';
	
UPDATE `vic_main`.`financial_transaction_type`
SET
	`from` = '602',
	`from_sub` = '999',
	`to` = '379',
	`to_sub` = '999'
WHERE
	`name` = 'other.ticket.payout-cancel-mp';

UPDATE `vic_main`.`financial_transaction_type`
SET
	`from_sub` = '999',
	`to_sub` = '999'
WHERE
	`name` IN ('user.ticket.create-mp','user.ticket.cancel-mp','branch.ticket.cancel-cash-mp');


COMMIT;