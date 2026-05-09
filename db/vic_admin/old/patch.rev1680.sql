START TRANSACTION;
INSERT INTO `vic_admin`.`parameter` (
`id` ,
`name` ,
`value` ,
`is_host` ,
`is_branch` ,
`is_user` ,
`type` ,
`mandatory` ,
`is_admin`
)
VALUES
(NULL , 'rootBetAliasInterval.from', '8000', '0', '0', '0', NULL , '1', '0'),
(NULL , 'rootBetAliasInterval.to', '9999', '0', '0', '0', NULL , '1', '0');
COMMIT;
