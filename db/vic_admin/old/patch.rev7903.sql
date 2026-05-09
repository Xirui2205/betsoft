START TRANSACTION;

INSERT INTO `vic_admin`.`database_patch`(`revision`, `not_reapplicable`, `note`) VALUES
 ('7903', 1, 'event position AJAX section (mantis:644)');

INSERT INTO `vic_admin`.`acl_resource`(`acl_tree_id`, `acl_resource_name`, `acl_resource_parent_id`, `acl_resource_type_id`) VALUES
 (1, 'section:305', null, 2);

SET @ar = LAST_INSERT_ID();

INSERT INTO `vic_admin`.`sekce` (`sekce_id`, `acl_resource_id`, `nazev`, `zobrazeno`, `controller`, `action`) VALUES
 (305, @ar, 'event position update AJAX', 0, 'event', 'update-positions');

INSERT INTO `vic_admin`.`sekce_has_parent` (`sekce_id`, `parent_id`, `sekce_order`) VALUES
 (305, null, 0);

INSERT INTO `vic_admin`.`acl_role_resource_privilege` (`acl_role_id`, `acl_resource_id`, `privilege_name`, `privilege_set`) VALUES
 (4, @ar, 'read', 1),
 (4, @ar, 'write', 1),
 (4, @ar, 'delete', 1),
 (3, @ar, 'read', 1),
 (3, @ar, 'write', 1),
 (3, @ar, 'delete', 1);

COMMIT;
