START TRANSACTION;

INSERT INTO `vic_ut_main`.`acl_resource` (`acl_resource_id`, `acl_tree_id`, `acl_resource_name`, `acl_resource_parent_id`, `acl_resource_type_id`) VALUES
(1278, 1, 'section:249', 1040, 2);

INSERT INTO `vic_ut_main`.`sekce` (`sekce_id`, `acl_resource_id`, `nazev`, `zobrazeno`, `controller`, `action`) VALUES
(249, 1278, 'user-insert', 1, 'user-profile', 'insert');

INSERT INTO  `vic_ut_main`.`acl_role_resource_privilege` (
`id` ,
`acl_role_id` ,
`acl_resource_id` ,
`privilege_name` ,
`privilege_set`
)
VALUES (
NULL ,  '1',  '1278',  'read',  '1'
), (
NULL ,  '1',  '1278',  'update',  '1'
), (
NULL ,  '1',  '1278',  'delete',  '1'
);

COMMIT;
