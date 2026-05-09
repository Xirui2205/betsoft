START TRANSACTION;

UPDATE `vic_admin`.`sekce` SET `action` = 'manual-deposit' WHERE `sekce`.`sekce_id` = 226;

INSERT INTO `vic_admin`.`acl_resource` (`acl_resource_id`, `acl_tree_id`, `acl_resource_name`, `acl_resource_parent_id`, `acl_resource_type_id`) VALUES
(1244, 1, 'section:244', 1058, 2),
(1245, 1, 'section:245', 1058, 2),
(1246, 1, 'section:246', 1058, 2);

INSERT INTO `vic_admin`.`acl_role_resource_privilege` (`acl_role_id`, `acl_resource_id`, `privilege_name`, `privilege_set`) VALUES
(2, 1244, 'read', 1),
(2, 1245, 'read', 1),
(2, 1246, 'read', 1);


INSERT INTO `vic_admin`.`sekce` (`sekce_id`, `acl_resource_id`, `nazev`, `zobrazeno`, `controller`, `action`) VALUES
(244, 1244, 'Manual', 0, 'finance', 'manual-withdraw'),
(245, 1245, 'Manual', 0, 'finance', 'manual-balance'),
(246, 1246, 'Manual', 0, 'finance', 'manual-points');

INSERT INTO `vic_admin`.`sekce_has_parent` (`sekce_id`, `parent_id`, `sekce_order`) VALUES
(244, 58, 0),
(245, 58, 0),
(246, 58, 0);

COMMIT;
