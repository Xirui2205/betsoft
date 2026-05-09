INSERT INTO vic_admin.`database_patch`(`revision`, `not_reapplicable`, `note`) VALUES (3453, 1, 'Nova sekce noviny victorka');

INSERT INTO  `vic_admin`.`acl_resource` (
`acl_resource_id` ,
`acl_tree_id` ,
`acl_resource_name` ,
`acl_resource_parent_id` ,
`acl_resource_type_id`
)
VALUES (
NULL ,  '1',  'section:351', NULL ,  '2'
);

SET @ar = LAST_INSERT_ID();

INSERT INTO  `vic_admin`.`sekce` (
`sekce_id` ,
`acl_resource_id` ,
`nazev` ,
`zobrazeno` ,
`controller` ,
`action`
)
VALUES (
'351',  @ar,  'Noviny Victorka',  '1',  'newspapers',  'index'
);

INSERT INTO  `vic_admin`.`sekce_has_parent` (
`sekce_id` ,
`parent_id` ,
`sekce_order`
)
VALUES (
'351',  '160',  '0'
);

INSERT INTO `vic_admin`.`acl_role_resource_privilege` (`id`, `acl_role_id`, `acl_resource_id`, `privilege_name`, `privilege_set`) VALUES (NULL, '1', @ar, 'read', '1');
INSERT INTO `vic_admin`.`acl_role_resource_privilege` (`id`, `acl_role_id`, `acl_resource_id`, `privilege_name`, `privilege_set`) VALUES (NULL, '2', @ar, 'read', '1');
INSERT INTO `vic_admin`.`acl_role_resource_privilege` (`id`, `acl_role_id`, `acl_resource_id`, `privilege_name`, `privilege_set`) VALUES (NULL, '17', @ar, 'read', '1');
