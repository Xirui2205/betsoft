ALTER TABLE `vic_main`.`ticket_live` ADD `confirmed` TINYINT( 1 ) NOT NULL AFTER `id`;
ALTER TABLE `vic_main`.`ticket_live` ADD INDEX ( `confirmed` );

START TRANSACTION;

UPDATE `vic_main`.`ticket_live` SET `confirmed` = 1;

INSERT INTO `vic_main`.`cronjob` (
`cronjob_id` ,
`cronjob_type` ,
`result` ,
`attempts` ,
`exec_at` ,
`executed_at` ,
`exec_constantly_at`
)
VALUES (
'13' , '2', NULL , '0', NULL , NULL , '1,11,21,31,41,51 * * * *'
);

INSERT INTO `vic_main`.`cronjob_param` (
`param_id` ,
`cronjob_id` ,
`param_name` ,
`param_value`
)
VALUES (
'8' , '13', 'name', 'LiveTicketNotConfirmed'
);

COMMIT;
