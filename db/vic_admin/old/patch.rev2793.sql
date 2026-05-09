START TRANSACTION;
INSERT INTO `vic_admin`.`parameter` 
	(`id` ,`name` ,`value` ,`is_host` ,`is_branch` ,`is_user` ,`type` ,`mandatory` ,`is_admin` ,`description`)
VALUES
	(NULL , 'host.ban-all-in', '0', '0', '0', '0', NULL , '1', '0', ''),
	(NULL , 'host.ban-all-out', '0', '0', '0', '0', NULL , '1', '0', '');
COMMIT;
