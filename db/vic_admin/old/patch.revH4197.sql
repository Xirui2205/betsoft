ALTER TABLE `branch` ADD `calculation` TINYINT NOT NULL DEFAULT '0';
ALTER TABLE `branch` ADD `calculation_net` TINYINT NOT NULL DEFAULT '0';
ALTER TABLE `branch` ADD `correspondence_address` TEXT NULL ;
ALTER TABLE `branch` ADD `calculation_net_type` ENUM( 'NEW', 'OLD' ) NOT NULL ;

-- section 349 --
INSERT INTO `acl_resource` (
`acl_resource_id` ,
`acl_tree_id` ,
`acl_resource_name` ,
`acl_resource_parent_id` ,
`acl_resource_type_id`
)
VALUES (
NULL , '1', 'section:349', NULL , '2'
);
SET @acl_resource_id = LAST_INSERT_ID();
INSERT INTO `vic_admin`.`sekce` (
`sekce_id` ,
`acl_resource_id` ,
`nazev` ,
`zobrazeno` ,
`controller` ,
`action`
)
VALUES (
'349', @acl_resource_id, 'Provize poboček', '1', 'calculation', 'branch'
);
INSERT INTO `vic_admin`.`acl_role_resource_privilege` (
`id` ,
`acl_role_id` ,
`acl_resource_id` ,
`privilege_name` ,
`privilege_set`
)
VALUES (
NULL , '1', @acl_resource_id, 'read', '1'
);
INSERT INTO `vic_admin`.`sekce_has_parent` (
`sekce_id` ,
`parent_id` ,
`sekce_order`
)
VALUES (
'349', '213', '1'
);

-- section 350 --
INSERT INTO `vic_admin`.`acl_resource` (
`acl_resource_id` ,
`acl_tree_id` ,
`acl_resource_name` ,
`acl_resource_parent_id` ,
`acl_resource_type_id`
)
VALUES (
NULL , '1', 'section:350', NULL , '2'
);
SET @acl_resource_id = LAST_INSERT_ID();
INSERT INTO `vic_admin`.`sekce` (
`sekce_id` ,
`acl_resource_id` ,
`nazev` ,
`zobrazeno` ,
`controller` ,
`action`
)
VALUES (
'350', @acl_resource_id, 'Provize poboček (internetové sázení)', '1', 'calculation', 'branch-internet'
);
INSERT INTO `vic_admin`.`acl_role_resource_privilege` (
`id` ,
`acl_role_id` ,
`acl_resource_id` ,
`privilege_name` ,
`privilege_set`
)
VALUES (
NULL , '1', @acl_resource_id, 'read', '1'
);
INSERT INTO `vic_admin`.`sekce_has_parent` (
`sekce_id` ,
`parent_id` ,
`sekce_order`
)
VALUES (
'350', '213', '2'
);