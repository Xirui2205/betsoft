START TRANSACTION;

TRUNCATE vic_main.ticket_statistics_cashflow;
TRUNCATE vic_main.ticket_statistics_payout;
TRUNCATE vic_main.ticket_combination;
DELETE FROM vic_main.point_transaction WHERE ticket_id IS NOT NULL;

DELETE fth 
FROM vic_main.financial_transaction_history fth 
INNER JOIN vic_main.financial_transaction ft
WHERE fth.transaction_id = ft.transaction_id AND ft.ticket_id IS NOT NULL;

DELETE ft2 
FROM vic_main.financial_transaction ft2 
INNER JOIN vic_main.financial_transaction ft
WHERE ft2.canceled_transaction_id = ft.transaction_id AND ft.ticket_id IS NOT NULL;

DELETE FROM vic_main.financial_transaction WHERE ticket_id IS NOT NULL;

TRUNCATE vic_main.ticket;

COMMIT;

