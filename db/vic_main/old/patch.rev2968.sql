START TRANSACTION;

INSERT INTO `vic_main`.`cronjob_type` (`type_id`, `type_name`) VALUES ('4', 'CleanDayRiskLimits');

DELETE FROM `vic_main`.`cronjob_param`
WHERE `cronjob_id` > 8;

DELETE FROM `vic_main`.`cronjob`
WHERE `cronjob_id` > 8;

INSERT INTO `vic_main`.`cronjob` 
	(`cronjob_id` ,`cronjob_type` ,`result` ,`attempts` ,`attempts_max` ,`exec_at` ,`executed_at` ,`exec_constantly_at`)
VALUES 
	('9', '4', NULL , '0', '0', NULL , NULL , '15 0 * * *');


COMMIT;
