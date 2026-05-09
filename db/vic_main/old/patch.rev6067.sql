
START TRANSACTION;
UPDATE financial_transaction_type SET name='user.ticket.collect-nocash' WHERE name='user.ticket.collect-noncash';
UPDATE financial_transaction_type SET name='user.ticket.collect-nocash-mp' WHERE name='user.ticket.collect-noncash-mp';
COMMIT;