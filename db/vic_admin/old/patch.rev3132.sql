START TRANSACTION;
INSERT INTO `vic_admin`.`acl_role_resource_privilege` (`id`, `acl_role_id`, `acl_resource_id`, `privilege_name`, `privilege_set`) VALUES (NULL, '3', '1002', 'read', '1');

INSERT INTO `vic_admin`.`acl_role_resource_privilege` (`id`, `acl_role_id`, `acl_resource_id`, `privilege_name`, `privilege_set`) VALUES (NULL, '3', '1046', 'read', '1');
INSERT INTO `vic_admin`.`acl_role_resource_privilege` (`id`, `acl_role_id`, `acl_resource_id`, `privilege_name`, `privilege_set`) VALUES (NULL, '3', '1046', 'update', '1');
INSERT INTO `vic_admin`.`acl_role_resource_privilege` (`id`, `acl_role_id`, `acl_resource_id`, `privilege_name`, `privilege_set`) VALUES (NULL, '3', '1046', 'delete', '1');

INSERT INTO `vic_admin`.`acl_role_resource_privilege` (`id`, `acl_role_id`, `acl_resource_id`, `privilege_name`, `privilege_set`) VALUES (NULL, '3', '1072', 'read', '1');
INSERT INTO `vic_admin`.`acl_role_resource_privilege` (`id`, `acl_role_id`, `acl_resource_id`, `privilege_name`, `privilege_set`) VALUES (NULL, '3', '1072', 'update', '1');
INSERT INTO `vic_admin`.`acl_role_resource_privilege` (`id`, `acl_role_id`, `acl_resource_id`, `privilege_name`, `privilege_set`) VALUES (NULL, '3', '1072', 'delete', '1');

DELETE FROM `vic_admin`.`acl_role_resource_privilege` WHERE `acl_role_resource_privilege`.`acl_resource_id` = 1298 and `acl_role_resource_privilege`.`acl_role_id`=3 ;

COMMIT;
