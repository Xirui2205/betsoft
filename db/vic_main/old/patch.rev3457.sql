start transaction;

UPDATE `vic_main`.`financial_transaction_type` SET `from` = '211',
`to` = '261' WHERE `financial_transaction_type`.`id` =22;

UPDATE `vic_main`.`financial_transaction_type` SET `from` = '261',
`to` = '211',`late_deposit` = '0' WHERE `financial_transaction_type`.`id` =23;

commit;
