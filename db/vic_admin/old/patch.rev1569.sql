START TRANSACTION;
INSERT INTO `vic_admin`.`parameter` (`id` ,`name` ,`value` ,`is_host` ,`is_branch` ,`is_user` ,`type` ,`mandatory`)
	VALUES 
		(NULL , 'alert.Transaction.limit', '3', '0', '1', '1', NULL , '1'),
		(NULL , 'alert.Transaction.duration', '10', '0', '1', '1', NULL , '1');
		
UPDATE `vic_admin`.`parameter` SET `is_branch` = '1',
`is_user` = '1' WHERE `parameter`.`name` = 'alert.to';

COMMIT;

