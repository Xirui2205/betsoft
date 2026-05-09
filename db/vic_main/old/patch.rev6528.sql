START TRANSACTION;

UPDATE `vic_main`.`financial_transaction_type` SET `daybook_collection` = 'INT',`note_daybook`='Předepsaná výhra: %ticketId%.' WHERE `financial_transaction_type`.`name` IN ('other.ticket.payout');

UPDATE `vic_main`.`financial_transaction_type` SET `from_sub` = '999',`to_sub` = '999' WHERE `financial_transaction_type`.`name` IN ('fee.general');

COMMIT;
