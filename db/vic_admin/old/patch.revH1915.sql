START TRANSACTION;

INSERT INTO `vic_admin`.`database_patch`(`revision`, `not_reapplicable`, `note`) VALUES
 ('H1915', 1, 'Node control centre section privileges for content-manager and bookmaker (mantis:1163)');

SET @ar = (SELECT `acl_resource_id` FROM `vic_admin`.`sekce` WHERE `sekce_id`=331);
INSERT INTO `vic_admin`.`acl_role_resource_privilege` (`acl_role_id`, `acl_resource_id`, `privilege_name`, `privilege_set`) VALUES
 (3, @ar, 'read', 1),
 (3, @ar, 'update', 1),
 (3, @ar, 'delete', 1),
 (4, @ar, 'read', 1),
 (4, @ar, 'update', 1),
 (4, @ar, 'delete', 1),
 (17, @ar, 'read', 1),
 (17, @ar, 'update', 1),
 (17, @ar, 'delete', 1);

SET @ar = (SELECT `acl_resource_id` FROM `vic_admin`.`acl_resource` WHERE `acl_resource_name`='general:translation');
INSERT INTO `vic_admin`.`acl_role_resource_privilege` (`acl_role_id`, `acl_resource_id`, `privilege_name`, `privilege_set`) VALUES
 (3, @ar, 'read', 1),
 (3, @ar, 'update', 1),
 (3, @ar, 'delete', 1),
 (4, @ar, 'read', 1),
 (4, @ar, 'update', 1),
 (4, @ar, 'delete', 1),
 (17, @ar, 'read', 1),
 (17, @ar, 'update', 1),
 (17, @ar, 'delete', 1);

SET @ar = (SELECT `acl_resource_id` FROM `vic_admin`.`acl_resource` WHERE `acl_resource_name`='general:cache-web-content');
INSERT INTO `vic_admin`.`acl_role_resource_privilege` (`acl_role_id`, `acl_resource_id`, `privilege_name`, `privilege_set`) VALUES
 (3, @ar, 'read', 1),
 (3, @ar, 'update', 1),
 (3, @ar, 'delete', 1),
 (4, @ar, 'read', 1),
 (4, @ar, 'update', 1),
 (4, @ar, 'delete', 1),
 (17, @ar, 'read', 1),
 (17, @ar, 'update', 1),
 (17, @ar, 'delete', 1);

COMMIT;
