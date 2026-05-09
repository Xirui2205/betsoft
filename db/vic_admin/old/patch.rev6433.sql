START TRANSACTION;

UPDATE `vic_main`.`financial_transaction_type` SET `from_sub` = '999',
`note_daybook` = 'Propadlá výhra: %ticketId%.'
WHERE `financial_transaction_type`.`name` = 'other.ticket.forfeit';

UPDATE `vic_main`.`financial_transaction_type` SET
`note_daybook` = 'Propadlý výherní poplatek sázenky: %ticketId%.'
WHERE `financial_transaction_type`.`name` = 'other.ticket.forfeit-mp';

UPDATE `vic_main`.`financial_transaction_type` SET `to_sub` = '999',
`daybook_collection` = 'POK' WHERE `financial_transaction_type`.`name` = 'other.ticket.payout';


COMMIT;