START TRANSACTION;


INSERT INTO `vic_admin`.`acl_resource`(`acl_tree_id`, `acl_resource_name`, `acl_resource_parent_id`, `acl_resource_type_id`) VALUES
(1, 'section:298', 1098, 2);

SET @ar = LAST_INSERT_ID();

INSERT INTO `vic_admin`.`sekce` (`sekce_id`, `acl_resource_id`, `nazev`, `zobrazeno`, `controller`, `action`) VALUES
(298, @ar, 'Default Promos', 1, NULL, NULL);

INSERT INTO `vic_admin`.`sekce_has_parent` (`sekce_id`, `parent_id`, `sekce_order`) VALUES
(298, 98, 0);

INSERT INTO `vic_admin`.`acl_role_resource_privilege` (`acl_role_id`, `acl_resource_id`, `privilege_name`, `privilege_set`) VALUES
(3, @ar, 'read', 1),
(3, @ar, 'update', 1),
(3, @ar, 'delete', 1),
(4, @ar, 'read', 1),
(4, @ar, 'update', 1),
(4, @ar, 'delete', 1);


COMMIT;
