START TRANSACTION;

UPDATE `vic_main`.`financial_transaction_type` SET `from`='548',`from_sub`='998',`to`='324',`to_sub`='998'
 WHERE `name` IN ('user.bonus.entry', 'user.bonus.entry-cancel');

COMMIT;
