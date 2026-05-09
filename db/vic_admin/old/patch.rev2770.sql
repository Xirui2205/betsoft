START TRANSACTION;

INSERT INTO `vic_admin`.`acl_resource` (
`acl_resource_id` ,
`acl_tree_id` ,
`acl_resource_name` ,
`acl_resource_parent_id` ,
`acl_resource_type_id`
)
VALUES (
1298 , '1', 'section:269', '1', '2'
);


INSERT INTO  `vic_admin`.`acl_role_resource_privilege` (
`id` ,
`acl_role_id` ,
`acl_resource_id` ,
`privilege_name` ,
`privilege_set`
)
VALUES (
NULL,  '2',  1298,  'read',  '1'
);
INSERT INTO  `vic_admin`.`acl_role_resource_privilege` (
`id` ,
`acl_role_id` ,
`acl_resource_id` ,
`privilege_name` ,
`privilege_set`
)
VALUES (
NULL,  '3',  1298,  'read',  '1'
);

INSERT INTO  `vic_admin`.`sekce` (
`sekce_id` ,
`acl_resource_id` ,
`nazev` ,
`zobrazeno` ,
`controller` ,
`action`
)
VALUES (
'269',1298,  'Číselník kombinací',  '0',  'catalog',  'combination'
);

COMMIT;
