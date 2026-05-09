CREATE TEMPORARY TABLE `vic_admin`.`tmp_role_id`(
 `id` INT(10) UNSIGNED
) Engine=MyISAM;

SET foreign_key_checks=0;

START TRANSACTION;
INSERT INTO `vic_admin`.`tmp_role_id`
 SELECT `acl_role_id`
 FROM `vic_admin`.`acl_role`
 WHERE (`acl_role_name` LIKE 'branch%'
  OR `acl_role_name` LIKE 'homebranch%')
  AND NOT(`acl_role_name` = 'branch-employee');

DELETE FROM `vic_admin`.`acl_role_resource_privilege`
 WHERE `acl_role_id` IN (SELECT `id` FROM `vic_admin`.`tmp_role_id`);

DELETE FROM `vic_admin`.`acl_role_has_parent`
 WHERE `parent_id` IN (SELECT `id` FROM `vic_admin`.`tmp_role_id`);

DELETE FROM `vic_admin`.`acl_role_has_parent`
 WHERE `acl_role_id` IN (SELECT `id` FROM `vic_admin`.`tmp_role_id`);

DELETE FROM `vic_admin`.`acl_role`
 WHERE `acl_role_id` IN (SELECT `id` FROM `vic_admin`.`tmp_role_id`);

INSERT INTO `vic_admin`.`acl_role`(`acl_role_id`,`acl_role_name`,`assignable`) VALUES
 (9, 'branch-admin', 1);

COMMIT;

SET foreign_key_checks=1;

DROP TABLE `vic_admin`.`tmp_role_id`;
