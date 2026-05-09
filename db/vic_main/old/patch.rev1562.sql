ALTER TABLE `vic_main`.`cronjob`  AUTO_INCREMENT =1000;
ALTER TABLE `vic_main`.`cronjob_param`  AUTO_INCREMENT =1000;

START TRANSACTION;

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


COMMIT;


