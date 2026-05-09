START TRANSACTION;

UPDATE  `vic_admin`.`sekce` SET  `controller` =  'banner-superhome',
`action` =  'index' WHERE  `sekce`.`sekce_id` =161;

INSERT INTO  `vic_admin`.`acl_resource` (
`acl_resource_id` ,
`acl_tree_id` ,
`acl_resource_name` ,
`acl_resource_parent_id` ,
`acl_resource_type_id`
)
VALUES  (
'1290',  '1',  'section:262',  '1160',  '2'
), (
'1286',  '1',  'section:258',  '1290',  '2'
), (
'1287',  '1',  'section:259',  '1290',  '2'
), (
'1288',  '1',  'section:260',  '1290',  '2'
), (
'1289',  '1',  'section:261',  '1290',  '2'
);


INSERT INTO  `vic_admin`.`acl_role_resource_privilege` (
`id` ,
`acl_role_id` ,
`acl_resource_id` ,
`privilege_name` ,
`privilege_set`
)
VALUES (
'893',  '2',  '1286',  'delete',  '1'
), (
'894',  '2',  '1286',  'update',  '1'
), (
'895',  '2',  '1286',  'read',  '1'
), (
'896',  '2',  '1287',  'delete',  '1'
), (
'897',  '2',  '1287',  'update',  '1'
), (
'898',  '2',  '1287',  'read',  '1'
), (
'899',  '2',  '1288',  'delete',  '1'
), (
'900',  '2',  '1288',  'update',  '1'
), (
'901',  '2',  '1288',  'read',  '1'
), (
'902',  '2',  '1289',  'delete',  '1'
), (
'903',  '2',  '1289',  'update',  '1'
), (
'904',  '2',  '1289',  'read',  '1'
), (
'905',  '2',  '1290',  'delete',  '1'
), (
'906',  '2',  '1290',  'update',  '1'
), (
'907',  '2',  '1290',  'read',  '1'
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
'258',  '1286',  'Update',  '0',  'banner-superhome',  'update'
), (
'259',  '1287',  'Index',  '1',  'banner-index',  'index'
), (
'260',  '1288',  'Update',  '0',  'banner-index',  'update'
), (
'261',  '1289',  'Insert',  '0',  'banner-index',  'insert'
), (
'262',  '1290',  'Banner',  '1',  'null',  'null'
);

UPDATE  `vic_admin`.`sekce_has_parent` SET  `parent_id` =  '262' WHERE  `sekce_has_parent`.`sekce_id` =161;
INSERT INTO  `vic_admin`.`sekce_has_parent` (
`sekce_id` ,
`parent_id` ,
`sekce_order`
)
VALUES (
'262',  '160',  '0'
), (
'259',  '262',  '0'
);

COMMIT;
