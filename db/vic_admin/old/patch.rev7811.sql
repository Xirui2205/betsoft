START TRANSACTION;

INSERT INTO `vic_admin`.`database_patch`(`revision`, `not_reapplicable`, `note`) VALUES
 ('7811', 1, 'BM rights for access daybook accounting section (mantis:429)');

SET @rid = (SELECT `acl_resource_id` FROM `vic_admin`.`sekce` WHERE `sekce_id`=212);
INSERT INTO `vic_admin`.`acl_role_resource_privilege` (`acl_role_id`, `acl_resource_id`, `privilege_name`, `privilege_set`) VALUES
 (3, @rid, 'read', 1),
 (4, @rid, 'read', 1);

COMMIT;
