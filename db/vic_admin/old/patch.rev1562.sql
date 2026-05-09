START TRANSACTION;
INSERT INTO `vic_admin`.`parameter` ( `id` ,`name` ,`value` ,`is_host` ,`is_branch` ,`is_user` ,`type` ,`mandatory` )
VALUES 
	(NULL , 'alert.WinRatio.limit', '2', '0', '0', '0', NULL , '1'),
	(NULL , 'alert.WinRatio.duration', '30', '0', '0', '0', NULL , '1');
COMMIT;

