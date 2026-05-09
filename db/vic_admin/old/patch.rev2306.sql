START TRANSACTION;

INSERT INTO `vic_admin`.`acl_resource` (`acl_resource_id`, `acl_tree_id`, `acl_resource_name`, `acl_resource_parent_id`, `acl_resource_type_id`) VALUES
(1284, 1, 'section:255', 1001, 2);

INSERT INTO `vic_admin`.`acl_role_resource_privilege` (`acl_role_id`, `acl_resource_id`, `privilege_name`, `privilege_set`) VALUES
(2, 1284, 'read', 1);


INSERT INTO `vic_admin`.`sekce` (`sekce_id`, `acl_resource_id`, `nazev`, `zobrazeno`, `controller`, `action`) VALUES
(255, 1284, 'Statistics', 1, 'statistics', 'view');

INSERT INTO `vic_admin`.`sekce_has_parent` (`sekce_id`, `parent_id`, `sekce_order`) VALUES
(255, null, 0);

COMMIT;
