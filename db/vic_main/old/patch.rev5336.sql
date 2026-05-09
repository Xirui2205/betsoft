START TRANSACTION;

UPDATE `vic_main`.`financial_transaction_type` SET `daybook_collection` = 'POK' WHERE `financial_transaction_type`.`name` = 'other.ticket.payout-cash';
UPDATE `vic_main`.`financial_transaction_type` SET `daybook_collection` = 'WEB' WHERE `financial_transaction_type`.`name` = 'user.exchange-from-points';

COMMIT;
