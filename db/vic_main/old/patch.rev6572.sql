START TRANSACTION;

UPDATE vic_main.financial_transaction_type SET `to`='324',to_sub='999',`from`='379',from_sub='999'  WHERE name='user.deposit.card';
UPDATE vic_main.financial_transaction_type SET `daybook_collection`='INT' WHERE `from`='548';

COMMIT;
