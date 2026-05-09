START TRANSACTION;

DELETE FROM `vic_main`.`cronjob_param`
WHERE `param_id` > 4;	

DELETE FROM `vic_main`.`cronjob`
WHERE `cronjob_id` > 5;	


INSERT INTO `vic_main`.`cronjob` (
`cronjob_id` ,
`cronjob_type` ,
`result` ,
`attempts` ,
`exec_at` ,
`executed_at` ,
`exec_constantly_at`
)
VALUES
	('6' , '2', NULL , '0', NULL , NULL , '44 1 * * *'),
	('7' , '2', NULL , '0', NULL , NULL , '54 1 * * *'),
	('8' , '2', NULL , '0', NULL , NULL , '04 2 * * *');

INSERT INTO `vic_main`.`cronjob_param` (
`param_id` ,
`cronjob_id` ,
`param_name` ,
`param_value`
)
VALUES
	('5' , '6', 'name', 'BranchBlackist'),
	('6' , '7', 'name', 'BranchTicketsCount'),
	('7' , '8', 'name', 'HostDeposits');

COMMIT;
