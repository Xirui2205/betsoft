START TRANSACTION;

INSERT INTO `vic_admin`.`database_patch`(`revision`, `not_reapplicable`, `note`) VALUES
 ('8227', 1, 'Client card blocation (mantis:710)');


INSERT INTO `vic_admin`.`acl_resource`(`acl_tree_id`, `acl_resource_name`, `acl_resource_parent_id`, `acl_resource_type_id`) VALUES
 (1, 'section:314', NULL, 2);

SET @ar = LAST_INSERT_ID();

INSERT INTO `vic_admin`.`sekce` (`sekce_id`, `acl_resource_id`, `nazev`, `zobrazeno`, `controller`, `action`) VALUES
(314, @ar, 'Block client card', 0, 'user-profile', 'block-client-card');

INSERT INTO `vic_admin`.`sekce_has_parent` (`sekce_id`, `parent_id`, `sekce_order`) VALUES
(314, NULL, 0);

INSERT INTO `vic_admin`.`acl_role_resource_privilege` (`acl_role_id`, `acl_resource_id`, `privilege_name`, `privilege_set`) VALUES
(1, @ar, 'read', 1),
(1, @ar, 'write', 1),
(1, @ar, 'delete', 1),
(2, @ar, 'read', 1),
(2, @ar, 'write', 1),
(2, @ar, 'delete', 1);

COMMIT;
