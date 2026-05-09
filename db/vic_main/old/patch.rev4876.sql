START TRANSACTION;

UPDATE `vic_main`.`financial_transaction_type` SET
	note_daybook = 'Sázenka: %ticketId%.'
WHERE
	name = 'user.ticket.create' OR name = 'branch.ticket.create-cash';
	
UPDATE `vic_main`.`financial_transaction_type` SET
	note_daybook = 'Manipulační poplatek sázenky: %ticketId%.'
WHERE
	name = 'user.ticket.create-mp' OR name = 'branch.ticket.create-cash-mp';

UPDATE `vic_main`.`financial_transaction_type` SET
	note_daybook = 'Zrušení sázenky: %ticketId%.'
WHERE
	name = 'user.ticket.cancel' OR name = 'branch.ticket.cancel-cash';

UPDATE `vic_main`.`financial_transaction_type` SET
	note_daybook = 'Vracení manipulačního poplatku zrušené sázenky: %ticketId%.'
WHERE
	name = 'user.ticket.cancel-mp' OR name = 'branch.ticket.cancel-cash-mp';


UPDATE `vic_main`.`financial_transaction_type` SET
	note_daybook = 'Znovuobnovení zrušené sázenky: %ticketId%.'
WHERE
	name = 'user.ticket.renew-canceled';
	
UPDATE `vic_main`.`financial_transaction_type` SET
	note_daybook = 'Znovuobnovení manipulačního poplatku znovuotevřené sázenky: %ticketId%.'
WHERE
	name = 'user.ticket.renew-canceled-mp';

UPDATE `vic_main`.`financial_transaction_type` SET
	note_daybook = 'Výplata sázenky: %ticketId%.'
WHERE
	name = 'user.ticket.payout' OR name = 'branch.ticket.collect' OR name = 'other.ticket.collect-individual' OR name = 'other.ticket.payout-cash';

UPDATE `vic_main`.`financial_transaction_type` SET
	note_daybook = 'Výherní manipulační poplatek sázenky: %ticketId%.'
WHERE
	name = 'user.ticket.payout-mp' OR name = 'branch.ticket.collect-mp' OR name = 'other.ticket.collect-individual-mp' OR name = 'other.ticket.payout-cash-mp';

UPDATE `vic_main`.`financial_transaction_type` SET
	note_daybook = 'Zrušení výplaty sázenky: %ticketId%.'
WHERE
	name = 'user.ticket.payout-cancel' OR name = 'other.ticket.payout-cash-cancel';

UPDATE `vic_main`.`financial_transaction_type` SET
	note_daybook = 'Vrácení výherního manipulačního poplatku sázenky: %ticketId%.'
WHERE
	name = 'user.ticket.payout-cancel-mp' OR name = 'other.ticket.payout-cash-cancel-mp';

UPDATE `vic_main`.`financial_transaction_type` SET
	note_daybook = 'Propadnutí sázenky: %ticketId%.'
WHERE
	name = 'other-ticket-forfeit';
	
UPDATE `vic_main`.`financial_transaction_type` SET
	note_daybook = 'Propadnutí výherního manipulačního poplatku sázenky: %ticketId%.'
WHERE
	name = 'other-ticket-forfeit-mp';

UPDATE `vic_main`.`financial_transaction_type` SET
	note_daybook = 'Dotace konta uživatele: %userId%.'
WHERE
	name = 'user.deposit.card' OR name = 'user.deposit.bank' OR name = 'user.deposit.cash' OR name = 'user.deposit.manual' OR name = 'branch.user.deposit-cash';

UPDATE `vic_main`.`financial_transaction_type` SET
	note_daybook = 'Směna bodů za peníze uživatele: %userId%'
WHERE
	name = 'user.exchange-from-points';

UPDATE `vic_main`.`financial_transaction_type` SET
	note_daybook = 'Výběr z konta uživatele: %userId%.'
WHERE
	name = 'user.withdraw.cash' OR name = 'user.withdraw.bank' OR name = 'user.withdraw.manual' OR name = 'branch.user.withdraw-cash';

UPDATE `vic_main`.`financial_transaction_type` SET
	note_daybook = 'Převod z pokladny: %date%.'
WHERE
	name = 'branch.withdraw';
	
UPDATE `vic_main`.`financial_transaction_type` SET
	note_daybook = 'Dotace pokladny: %date%.'
WHERE
	name = 'branch.deposit';

UPDATE `vic_main`.`financial_transaction_type` SET
	note_daybook = 'Poplatek'
WHERE
	name = 'fee.general';

COMMIT;
