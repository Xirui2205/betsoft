START TRANSACTION;

INSERT INTO `vic_admin`.`database_patch`(`revision`, `not_reapplicable`, `note`) VALUES
 ('H1919', 1, '"Limits" section privileges for superbookmaker (mantis:?)');

SET @ar = (SELECT `acl_resource_id` FROM `vic_admin`.`sekce` WHERE `sekce_id`=77);
INSERT INTO `vic_admin`.`acl_role_resource_privilege` (`acl_role_id`, `acl_resource_id`, `privilege_name`, `privilege_set`) VALUES
 (3, @ar, 'read', 1),
 (3, @ar, 'update', 1),
 (3, @ar, 'delete', 1);

COMMIT;
