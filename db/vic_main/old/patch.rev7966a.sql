START TRANSACTION;

INSERT INTO `vic_main`.`database_patch`(`revision`, `not_reapplicable`, `note`) VALUES
 ('7966', 1, 'Bbas starting points (mantis:566)');
 

UPDATE `vic_main`.voucher SET
 point_transaction_id = NULL,
 user_id = NULL,
 used_time = NULL,
 used = 0;


DELETE FROM `vic_main`.point_transaction;
DELETE FROM `vic_main`.point_transaction_handle_sequence;


UPDATE `vic_main`.point_account SET
balance = 0,
balance_get = 0,
balance_spend = 0,
balance_exchange = 0;


UPDATE `vic_main`.`point_transaction_type` SET `limited_by_day` = '0' WHERE `point_transaction_type`.`id` =14;
INSERT INTO `vic_main`.`point_transaction_type` (
`id` ,
`name` ,
`kind` ,
`point_type_id` ,
`note` ,
`from` ,
`thru` ,
`to` ,
`debiting` ,
`low_limit` ,
`high_limit` ,
`limited_by_day`
)
VALUES (
'15', 'bewaStartingPoints', 'get', '1', 'Uvodni body dane pri spusteni vernostniho programu', NULL , NULL , NULL , '0', NULL , NULL , '0'
);




COMMIT;
