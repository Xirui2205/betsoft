START TRANSACTION;
UPDATE `vic_admin`.`sekce` SET `nazev` = 'Branch management' WHERE `sekce`.`sekce_id` =159;

INSERT INTO `acl_resource` (`acl_resource_id`, `acl_tree_id`, `acl_resource_name`, `acl_resource_parent_id`, `acl_resource_type_id`) VALUES
(1285, 1, 'section:257', 1001, 2);


INSERT INTO `sekce` (`sekce_id`, `acl_resource_id`, `nazev`, `zobrazeno`, `controller`, `action`) VALUES
(257, 1285, 'Host dashboard', 1, 'host-dashboard', 'view');


INSERT INTO `acl_role_resource_privilege` (`id`, `acl_role_id`, `acl_resource_id`, `privilege_name`, `privilege_set`) VALUES
(892, 2, 1285, 'read', 1);
COMMIT;