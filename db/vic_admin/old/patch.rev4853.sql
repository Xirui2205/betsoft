START TRANSACTION;

SET @par = (SELECT `acl_resource_id` FROM `vic_admin`.`acl_resource` WHERE `acl_resource_name`='section:270');

INSERT INTO `vic_admin`.`acl_resource` (`acl_resource_id`, `acl_tree_id`, `acl_resource_name`, `acl_resource_parent_id`, `acl_resource_type_id`) VALUES
(null, 1, 'section:281', @par, 2);

SET @ar = LAST_INSERT_ID();

INSERT INTO `vic_admin`.`sekce` (`sekce_id`, `acl_resource_id`, `nazev`, `zobrazeno`, `controller`, `action`) VALUES
(281, @ar, 'Admins', 1, 'admin-main', 'index');

INSERT INTO `vic_admin`.`sekce_has_parent` (
`sekce_id` ,
`parent_id` ,
`sekce_order`
)
VALUES (
'281', '270', 0
);

INSERT INTO `vic_admin`.`acl_role_resource_privilege` (
`id` ,
`acl_role_id` ,
`acl_resource_id` ,
`privilege_name` ,
`privilege_set`
)
VALUES
(NULL , '2', @ar, 'read', '1'),
(NULL , '3', @ar, 'read', '1');

SET @par = (SELECT `acl_resource_id` FROM `vic_admin`.`acl_resource` WHERE `acl_resource_name`='section:281');

INSERT INTO `vic_admin`.`acl_resource` (`acl_resource_id`, `acl_tree_id`, `acl_resource_name`, `acl_resource_parent_id`, `acl_resource_type_id`) VALUES
(null, 1, 'section:282', @par, 2);

SET @ar = LAST_INSERT_ID();

INSERT INTO `vic_admin`.`sekce` (`sekce_id`, `acl_resource_id`, `nazev`, `zobrazeno`, `controller`, `action`) VALUES
(282, @ar, 'Admin detail', 0, 'admin-main', 'view');

INSERT INTO `vic_admin`.`sekce_has_parent` (
`sekce_id` ,
`parent_id` ,
`sekce_order`
)
VALUES (
'282', '281', 0
);

INSERT INTO `vic_admin`.`acl_role_resource_privilege` (
`id` ,
`acl_role_id` ,
`acl_resource_id` ,
`privilege_name` ,
`privilege_set`
)
VALUES
(NULL , '2', @ar, 'read', '1'),
(NULL , '3', @ar, 'read', '1');


INSERT INTO `vic_admin`.`acl_resource` (`acl_resource_id`, `acl_tree_id`, `acl_resource_name`, `acl_resource_parent_id`, `acl_resource_type_id`) VALUES
(null, 1, 'section:283', @par, 2);

SET @ar = LAST_INSERT_ID();

INSERT INTO `vic_admin`.`sekce` (`sekce_id`, `acl_resource_id`, `nazev`, `zobrazeno`, `controller`, `action`) VALUES
(283, @ar, 'Admin insert', 0, 'admin-main', 'insert');

INSERT INTO `vic_admin`.`sekce_has_parent` (
`sekce_id` ,
`parent_id` ,
`sekce_order`
)
VALUES (
'283', '281', 0
);

INSERT INTO `vic_admin`.`acl_role_resource_privilege` (
`id` ,
`acl_role_id` ,
`acl_resource_id` ,
`privilege_name` ,
`privilege_set`
)
VALUES
(NULL , '2', @ar, 'read', '1'),
(NULL , '3', @ar, 'read', '1');


INSERT INTO `vic_admin`.`acl_resource` (`acl_resource_id`, `acl_tree_id`, `acl_resource_name`, `acl_resource_parent_id`, `acl_resource_type_id`) VALUES
(null, 1, 'section:284', @par, 2);

SET @ar = LAST_INSERT_ID();

INSERT INTO `vic_admin`.`sekce` (`sekce_id`, `acl_resource_id`, `nazev`, `zobrazeno`, `controller`, `action`) VALUES
(284, @ar, 'Admin update', 0, 'admin-main', 'update');

INSERT INTO `vic_admin`.`sekce_has_parent` (
`sekce_id` ,
`parent_id` ,
`sekce_order`
)
VALUES (
'284', '281', 0
);

INSERT INTO `vic_admin`.`acl_role_resource_privilege` (
`id` ,
`acl_role_id` ,
`acl_resource_id` ,
`privilege_name` ,
`privilege_set`
)
VALUES
(NULL , '2', @ar, 'read', '1'),
(NULL , '3', @ar, 'read', '1');

INSERT INTO `vic_admin`.`acl_resource` (`acl_resource_id`, `acl_tree_id`, `acl_resource_name`, `acl_resource_parent_id`, `acl_resource_type_id`) VALUES
(null, 1, 'section:285', @par, 2);

SET @ar = LAST_INSERT_ID();

INSERT INTO `vic_admin`.`sekce` (`sekce_id`, `acl_resource_id`, `nazev`, `zobrazeno`, `controller`, `action`) VALUES
(285, @ar, 'Admin privileges', 0, 'admin-privileges', 'index');

INSERT INTO `vic_admin`.`sekce_has_parent` (
`sekce_id` ,
`parent_id` ,
`sekce_order`
)
VALUES (
'285', '281', 0
);

INSERT INTO `vic_admin`.`acl_role_resource_privilege` (
`id` ,
`acl_role_id` ,
`acl_resource_id` ,
`privilege_name` ,
`privilege_set`
)
VALUES
(NULL , '2', @ar, 'read', '1'),
(NULL , '3', @ar, 'read', '1');

COMMIT;
