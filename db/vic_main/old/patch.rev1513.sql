ALTER TABLE `cronjob` ADD `exec_constantly_at` VARCHAR( 64 ) NULL;
ALTER TABLE `cronjob` CHANGE `exec_at` `exec_at` DATETIME NULL;
INSERT INTO `vic_main`.`cronjob_type` (
`type_id` ,
`type_name`
)
VALUES (
'2', 'Alert'
);

