START TRANSACTION;

INSERT INTO `database_patch`(`revision`, `not_reapplicable`, `note`) VALUES
 ('H4953', 1, 'Cron for cleaning offergen old files');

INSERT INTO  `cronjob_type` (
`type_id` ,
`type_name`
)
VALUES (
'23',  'OffergenClean'
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
'29',  '23', NULL ,  '0',  '0', NULL , NULL ,  '0 4 1 * *'
);


COMMIT;