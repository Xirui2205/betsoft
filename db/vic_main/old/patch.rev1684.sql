START TRANSACTION;

INSERT INTO `vic_main`.`cronjob_type` (
`type_id` ,
`type_name`
)
VALUES (
3 , 'ReleaseAliases'
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

ALTER TABLE `cronjob` AUTO_INCREMENT =1000;
ALTER TABLE `cronjob_param` AUTO_INCREMENT =1000;
