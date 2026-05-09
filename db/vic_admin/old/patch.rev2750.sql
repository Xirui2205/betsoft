START TRANSACTION;
INSERT INTO `vic_admin`.`acl_resource` (`acl_resource_id`, `acl_tree_id`, `acl_resource_name`, `acl_resource_parent_id`, `acl_resource_type_id`) VALUES
(1296, 1, 'section:267', 1058, 2);


INSERT INTO `vic_admin`.`sekce` (`sekce_id`, `acl_resource_id`, `nazev`, `zobrazeno`, `controller`, `action`) VALUES
(267, 1296, 'Cancel transaction', 1, 'finance', 'cancel-transaction');

INSERT INTO `vic_admin`.`sekce_has_parent` (
`sekce_id` ,
`parent_id` ,
`sekce_order`
)
VALUES (
'267', '58', '0'
);

INSERT INTO `vic_admin`.`acl_role_resource_privilege` (
`id` ,
`acl_role_id` ,
`acl_resource_id` ,
`privilege_name` ,
`privilege_set`
)
VALUES
(NULL , '2', '1296', 'read', '1');

COMMIT;



