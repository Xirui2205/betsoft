START TRANSACTION;

INSERT INTO `vic_admin`.`acl_resource` (`acl_resource_id`, `acl_tree_id`, `acl_resource_name`, `acl_resource_parent_id`, `acl_resource_type_id`) VALUES
(1281, 1, 'section:252', 1058, 2),
(1282, 1, 'section:253', 1058, 2);

INSERT INTO `vic_admin`.`acl_role_resource_privilege` (`acl_role_id`, `acl_resource_id`, `privilege_name`, `privilege_set`) VALUES
(2, 1281, 'read', 1),
(2, 1282, 'read', 1);


INSERT INTO `vic_admin`.`sekce` (`sekce_id`, `acl_resource_id`, `nazev`, `zobrazeno`, `controller`, `action`) VALUES
(252, 1281, 'Branch funding', 1, 'finance', 'branch-funding'),
(253, 1282, 'Predeposits', 1, 'finance', 'predeposits');

INSERT INTO `vic_admin`.`sekce_has_parent` (`sekce_id`, `parent_id`, `sekce_order`) VALUES
(252, 58, 0),
(253, 58, 0);

INSERT INTO `vic_admin`.`parameter` 
	(`id` , `name` , `value` , `is_host` , `is_branch` , `is_user` , `type` , `mandatory` , `is_admin`)
VALUES 
	(NULL , 'branch.fundReserve', '1000', '0', '1', '0', NULL , '1', '0'),
	(NULL , 'branch.minimalDeposit', '200', '0', '1', '0', NULL , '1', '0'),
	(NULL , 'branch.minimalWithdraw', '200', '0', '1', '0', NULL , '1', '0');

COMMIT;
