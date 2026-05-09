ALTER TABLE `vic_admin`.`host` ADD `out_allowed` TINYINT( 1 ) NOT NULL AFTER `allowed` ,
ADD INDEX ( `out_allowed` ) ;

ALTER TABLE `vic_admin`.`host` ADD `in_allowed` TINYINT( 1 ) NOT NULL AFTER `allowed` ,
ADD INDEX ( `in_allowed` ) ;


start transaction;
UPDATE `vic_admin`.`host` SET out_allowed = 1;
UPDATE `vic_admin`.`host` SET in_allowed = 1;

INSERT INTO `vic_admin`.`parameter` 
	(`id` ,`name` ,`value` ,`is_host` ,`is_branch` ,`is_user` ,`type` ,`mandatory` ,`is_admin` ,`description`)
VALUES
	(NULL , 'host.ban-all', '0', '0', '0', '0', NULL , '1', '0', '');


commit;
