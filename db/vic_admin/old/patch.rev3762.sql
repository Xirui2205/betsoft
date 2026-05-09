START TRANSACTION;

SET NAMES utf8;

INSERT INTO `vic_admin`.`parameter` (`id` ,`name` ,`value` ,`is_host` ,`is_branch` ,`is_user` ,`type` ,`mandatory` ,
                                     `is_admin` ,`is_editable` ,`description`)
VALUES (NULL , 'maps.region.name', 'Czech Republic', '', '', '1', NULL , '1', '1', '1', 'Jmeno regionu. napr. Czech Republic'),
       (NULL , 'maps.region.code', 'CZE', '', '', '1', NULL , '1', '1', '1', 'Kod regionu. napr. CZE');

COMMIT;