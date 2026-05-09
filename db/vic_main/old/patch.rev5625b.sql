START TRANSACTION;

UPDATE `vic_main`.`financial_transaction_type`
SET
	`name` = 'user.ticket.collect-noncash'
WHERE
	`name` = 'user.ticket.collect-nocash';

UPDATE `vic_main`.`financial_transaction_type`
SET
	`name` = 'user.ticket.collect-noncash-mp'
WHERE
	`name` = 'user.ticket.collect-nocash-mp';

COMMIT;
