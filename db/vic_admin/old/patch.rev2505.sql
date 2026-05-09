START TRANSACTION;

INSERT INTO  `vic_admin`.`acl_resource` (
`acl_resource_id` ,
`acl_tree_id` ,
`acl_resource_name` ,
`acl_resource_parent_id` ,
`acl_resource_type_id`
)
VALUES (
1292 ,  '1',  'section:263',  '1001',  '2'
),(
1293 ,  '1',  'section:264',  '1001',  '2'
),(
1294 ,  '1',  'section:265',  '1001',  '2'
);


INSERT INTO  `vic_admin`.`acl_role_resource_privilege` (
`id` ,
`acl_role_id` ,
`acl_resource_id` ,
`privilege_name` ,
`privilege_set`
)
VALUES (
908 ,  '2',  '1292',  'read',  '1'
),(
909 ,  '2',  '1292',  'write',  '1'
),(
910 ,  '2',  '1292',  'delete',  '1'
),(
911 ,  '2',  '1293',  'read',  '1'
),(
912 ,  '2',  '1293',  'write',  '1'
),(
913 ,  '2',  '1293',  'delete',  '1'
),(
914 ,  '2',  '1294',  'read',  '1'
),(
915 ,  '2',  '1294',  'write',  '1'
),(
916 ,  '2',  '1294',  'delete',  '1'
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
'263',  '1292',  'Transaction types',  '1',  'transaction-type',  'index'
),(
'264',  '1293',  'Transaction types',  '0',  'transaction-type',  'update-fee'
),(
'265',  '1294',  'Transaction types',  '0',  'transaction-type',  'update-limit'
);


INSERT INTO  `vic_admin`.`sekce_has_parent` (
`sekce_id` ,
`parent_id` ,
`sekce_order`
)
VALUES (
'263',  '2',  '0'
);

COMMIT;
