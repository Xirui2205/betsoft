ALTER TABLE `vic_main`.`ticket` ADD `forfeit` DATETIME NULL DEFAULT NULL ,
ADD INDEX ( `forfeit` );

START TRANSACTION;

INSERT INTO `vic_main`.`cronjob_type` (`type_id`, `type_name`) VALUES ('5', 'ForfeitNotCollectedTikckets');

DELETE FROM `vic_main`.`cronjob_param`
WHERE `cronjob_id` > 9;
DELETE FROM `vic_main`.`cronjob`
WHERE `cronjob_id` > 9;

INSERT INTO `vic_main`.`cronjob` 
	(`cronjob_id` ,`cronjob_type` ,`result` ,`attempts` ,`attempts_max` ,`exec_at` ,`executed_at` ,`exec_constantly_at`)
VALUES 
	('10', '5', NULL , '0', '0', NULL , NULL , '40 3 * * *');


COMMIT;
