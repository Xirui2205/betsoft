START TRANSACTION;
INSERT INTO `vic_admin`.`acl_resource` (`acl_resource_id`, `acl_tree_id`, `acl_resource_name`, `acl_resource_parent_id`, `acl_resource_type_id`) VALUES
(1277, 1, 'section:248', 1001, 2);


INSERT INTO `vic_admin`.`sekce` (`sekce_id`, `acl_resource_id`, `nazev`, `zobrazeno`, `controller`, `action`) VALUES
(248, 1277, 'Turnaj/Událost', 1, NULL, NULL);

INSERT INTO `vic_admin`.`sekce_has_parent` (
`sekce_id` ,
`parent_id` ,
`sekce_order`
)
VALUES (
'248', '133', '0'
);

INSERT INTO `vic_admin`.`acl_role_resource_privilege` (
`id` ,
`acl_role_id` ,
`acl_resource_id` ,
`privilege_name` ,
`privilege_set`
)
VALUES
(NULL , '1', '1277', 'read', '1'),
(NULL , '3', '1277', 'read', '1'),
(NULL , '1', '1277', 'update', '1'),
(NULL , '3', '1277', 'update', '1'),
(NULL , '1', '1277', 'delete', '1'),
(NULL , '3', '1277', 'delete', '1');

UPDATE `vic_admin`.`sekce_has_parent` SET `sekce_order` = '1' WHERE `sekce_has_parent`.`sekce_id` =137;

UPDATE `vic_admin`.`sekce_has_parent` SET `parent_id` = 248 WHERE `sekce_has_parent`.`sekce_id` =136;
UPDATE `vic_admin`.`sekce_has_parent` SET `parent_id` = 248 WHERE `sekce_has_parent`.`sekce_id` =137;

UPDATE `vic_admin`.`acl_resource` SET `acl_resource_parent_id` = '1277' WHERE `acl_resource`.`acl_resource_id` =1136;

UPDATE `vic_admin`.`acl_resource` SET `acl_resource_parent_id` = '1277' WHERE `acl_resource`.`acl_resource_id` =1137;

COMMIT;


