START TRANSACTION;

UPDATE `vic_main`.`financial_transaction_type` SET `daybook_collection` = 'INT',`note_daybook`='Předepsaná výhra: %ticketId%.' WHERE `name`='other.ticket.payout';

UPDATE `vic_main`.`financial_transaction_type` SET `from_sub` = '999',`to_sub` = '999' WHERE `name`='fee.general';

UPDATE  `vic_main`.`financial_transaction_type` SET `to`='324',to_sub='999',`from`='379',from_sub='999'  WHERE `name`='user.deposit.bank';

COMMIT;
