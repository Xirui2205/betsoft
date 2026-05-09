START TRANSACTION;

TRUNCATE `vic_main`.`cronjob_param`;
TRUNCATE `vic_main`.`cronjob`;

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
'1' , '2', NULL , '0', NULL , NULL , '34 1 * * *'
);

INSERT INTO `vic_main`.`cronjob_param` (
`param_id` ,
`cronjob_id` ,
`param_name` ,
`param_value`
)
VALUES (
'1' , '1', 'name', 'UserBlackList'
);


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
'2' , '2', NULL , '0', NULL , NULL , '12 * * * *'
);

INSERT INTO `vic_main`.`cronjob_param` (
`param_id` ,
`cronjob_id` ,
`param_name` ,
`param_value`
)
VALUES (
'2' , '2', 'name', 'NewUsers'
);

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
'3' , '2', NULL , '0', NULL , NULL , '42 1 * * *'
);

INSERT INTO `vic_main`.`cronjob_param` (
`param_id` ,
`cronjob_id` ,
`param_name` ,
`param_value`
)
VALUES (
'3' , '3', 'name', 'CanceledTickets'
);

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
'4' , '2', NULL , '0', NULL , NULL , '56 1 * * *'
);


INSERT INTO `vic_main`.`cronjob_param` (
`param_id` ,
`cronjob_id` ,
`param_name` ,
`param_value`
)
VALUES (
'4', '4', 'name', 'WinRatio'
);



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
'5' , '3', NULL , '0', NULL , NULL , '4,16,28,40,52 * * * *'
);


COMMIT;
