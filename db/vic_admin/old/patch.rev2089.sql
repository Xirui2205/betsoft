START TRANSACTION;


INSERT INTO  `vic_admin`.`acl_resource` (
`acl_resource_id` ,
`acl_tree_id` ,
`acl_resource_name` ,
`acl_resource_parent_id` ,
`acl_resource_type_id`
)
VALUES (
'1279',  '1',  'section:250',  '1002',  '2'
), (
'1280',  '1',  'section:251',  '1002',  '2'
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
'250',  '1279',  'Global Parameters',  '0',  'settings-global-parameters',  'view-system'
), (
'251',  '1280',  'Global Parameters',  '0',  'settings-global-parameters',  'update-system'
);


INSERT INTO  `vic_admin`.`acl_role_resource_privilege` (
`id` ,
`acl_role_id` ,
`acl_resource_id` ,
`privilege_name` ,
`privilege_set`
)
VALUES (
'879',  '2',  '1279',  'read',  '1'
), (
'880',  '2',  '1279',  'update',  '1'
), (
'881',  '2',  '1279',  'delete',  '1'
);


INSERT INTO  `vic_admin`.`acl_role_resource_privilege` (
`id` ,
`acl_role_id` ,
`acl_resource_id` ,
`privilege_name` ,
`privilege_set`
)
VALUES (
'882',  '2',  '1280',  'read',  '1'
), (
'883',  '2',  '1280',  'update',  '1'
), (
'884',  '2',  '1280',  'delete',  '1'
);


COMMIT;
