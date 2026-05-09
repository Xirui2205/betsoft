START TRANSACTION;
INSERT INTO `vic_admin`.`acl_resource` (`acl_resource_id`, `acl_tree_id`, `acl_resource_name`, `acl_resource_parent_id`, `acl_resource_type_id`) VALUES
(null, 1, 'section:271', 1058, 2);

SET @ar = LAST_INSERT_ID();


INSERT INTO `vic_admin`.`sekce` (`sekce_id`, `acl_resource_id`, `nazev`, `zobrazeno`, `controller`, `action`) VALUES
(271, @ar, 'Point transactions', 1, 'finance', 'point-transactions');

INSERT INTO `vic_admin`.`sekce_has_parent` (
`sekce_id` ,
`parent_id` ,
`sekce_order`
)
VALUES (
'271', '58', '1'
);

INSERT INTO `vic_admin`.`acl_role_resource_privilege` (
`id` ,
`acl_role_id` ,
`acl_resource_id` ,
`privilege_name` ,
`privilege_set`
)
VALUES
(NULL , '2', @ar, 'read', '1');

COMMIT;

