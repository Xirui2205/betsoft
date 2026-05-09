START TRANSACTION;

INSERT INTO `vic_admin`.`database_patch`(`revision`, `not_reapplicable`, `note`) VALUES
 ('7819', 1, 'Reverting (deleting) BM rights for access daybook accounting section, added right for balancing instead (mantis:429)');

SET @rid = (SELECT `acl_resource_id` FROM `vic_admin`.`sekce` WHERE `sekce_id`=212);
DELETE FROM `vic_admin`.`acl_role_resource_privilege` WHERE `acl_role_id` IN (3,4) AND `acl_resource_id`=@rid;

SET @rid = (SELECT `acl_resource_id` FROM `vic_admin`.`sekce` WHERE `sekce_id`=272);
REPLACE INTO `vic_admin`.`acl_role_resource_privilege` (`acl_role_id`, `acl_resource_id`, `privilege_name`, `privilege_set`) VALUES
 (3, @rid, 'read', 1),
 (4, @rid, 'read', 1);

COMMIT;
