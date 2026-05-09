
INSERT INTO  `acl_resource` (
`acl_resource_id` ,
`acl_tree_id` ,
`acl_resource_name` ,
`acl_resource_parent_id` ,
`acl_resource_type_id`
)
VALUES (
NULL ,  '1',  'section:395',  '1',  '2'
);

SET @ar = LAST_INSERT_ID();

INSERT INTO  `sekce` (
`sekce_id` ,
`acl_resource_id` ,
`nazev` ,
`zobrazeno` ,
`controller` ,
`action`
)
VALUES (
'395',  @ar ,  'Dokumenty',  '1',  'document',  'index'
);

INSERT INTO  `sekce_has_parent` (
`sekce_id` ,
`parent_id` ,
`sekce_order`
)
VALUES (
'395',  '2',  '0'
);


INSERT INTO  `acl_role_resource_privilege` (
`id` ,
`acl_role_id` ,
`acl_resource_id` ,
`privilege_name` ,
`privilege_set`
)
VALUES (
NULL ,  '1',  @ar,  'read',  '1'
);

INSERT INTO  `acl_resource` (
`acl_resource_id` ,
`acl_tree_id` ,
`acl_resource_name` ,
`acl_resource_parent_id` ,
`acl_resource_type_id`
)
VALUES (
NULL ,  '1',  'section:396',  '1',  '2'
);

SET @ar = LAST_INSERT_ID();

INSERT INTO  `sekce` (
`sekce_id` ,
`acl_resource_id` ,
`nazev` ,
`zobrazeno` ,
`controller` ,
`action`
)
VALUES (
'396',  @ar ,  'Dokumenty',  '0',  'document',  'insert'
);

INSERT INTO  `sekce_has_parent` (
`sekce_id` ,
`parent_id` ,
`sekce_order`
)
VALUES (
'396',  '395',  '0'
);


INSERT INTO  `acl_role_resource_privilege` (
`id` ,
`acl_role_id` ,
`acl_resource_id` ,
`privilege_name` ,
`privilege_set`
)
VALUES (
NULL ,  '1',  @ar,  'read',  '1'
);

INSERT INTO  `acl_resource` (
`acl_resource_id` ,
`acl_tree_id` ,
`acl_resource_name` ,
`acl_resource_parent_id` ,
`acl_resource_type_id`
)
VALUES (
NULL ,  '1',  'section:397',  '1',  '2'
);

SET @ar = LAST_INSERT_ID();

INSERT INTO  `sekce` (
`sekce_id` ,
`acl_resource_id` ,
`nazev` ,
`zobrazeno` ,
`controller` ,
`action`
)
VALUES (
'397',  @ar ,  'Dokumenty',  '0',  'document',  'view'
);

INSERT INTO  `sekce_has_parent` (
`sekce_id` ,
`parent_id` ,
`sekce_order`
)
VALUES (
'397',  '395',  '0'
);


INSERT INTO  `acl_role_resource_privilege` (
`id` ,
`acl_role_id` ,
`acl_resource_id` ,
`privilege_name` ,
`privilege_set`
)
VALUES (
NULL ,  '1',  @ar,  'read',  '1'
);

INSERT INTO  `acl_resource` (
`acl_resource_id` ,
`acl_tree_id` ,
`acl_resource_name` ,
`acl_resource_parent_id` ,
`acl_resource_type_id`
)
VALUES (
NULL ,  '1',  'section:398',  '1',  '2'
);

SET @ar = LAST_INSERT_ID();

INSERT INTO  `sekce` (
`sekce_id` ,
`acl_resource_id` ,
`nazev` ,
`zobrazeno` ,
`controller` ,
`action`
)
VALUES (
'398',  @ar ,  'Dokumenty',  '0',  'document',  'update'
);

INSERT INTO  `sekce_has_parent` (
`sekce_id` ,
`parent_id` ,
`sekce_order`
)
VALUES (
'398',  '395',  '0'
);


INSERT INTO  `acl_role_resource_privilege` (
`id` ,
`acl_role_id` ,
`acl_resource_id` ,
`privilege_name` ,
`privilege_set`
)
VALUES (
NULL ,  '1',  @ar,  'read',  '1'
);