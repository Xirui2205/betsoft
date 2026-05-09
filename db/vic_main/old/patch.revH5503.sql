START TRANSACTION;

INSERT INTO `database_patch`(`revision`, `not_reapplicable`, `note`) VALUES
 ('H5503', 1, 'Cron for cleaning offergen old files');

INSERT INTO  `cronjob_type` (
`type_id` ,
`type_name`
)
VALUES (
'24',  'MobileAppNewsletter'
);

INSERT INTO  `cronjob` (
`cronjob_id` ,
`cronjob_type` ,
`result` ,
`attempts` ,
`attempts_max` ,
`exec_at` ,
`executed_at` ,
`exec_constantly_at`
)
VALUES (
'30',  '24', NULL ,  '0',  '0', NULL , NULL ,  '0 14 * * *'
);


COMMIT;