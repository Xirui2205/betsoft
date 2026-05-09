START TRANSACTION;

INSERT INTO `vic_admin`.`acl_resource` (`acl_resource_id`, `acl_tree_id`, `acl_resource_name`, `acl_resource_parent_id`, `acl_resource_type_id`) VALUES ('1194', '1', 'section:194', '1001', '2');
INSERT INTO `vic_admin`.`acl_resource` (`acl_resource_id`, `acl_tree_id`, `acl_resource_name`, `acl_resource_parent_id`, `acl_resource_type_id`) VALUES ('1196', '1', 'section:196', '1001', '2');
INSERT INTO `vic_admin`.`acl_resource` (`acl_resource_id`, `acl_tree_id`, `acl_resource_name`, `acl_resource_parent_id`, `acl_resource_type_id`) VALUES ('1197', '1', 'section:197', '1001', '2');
INSERT INTO `vic_admin`.`acl_resource` (`acl_resource_id`, `acl_tree_id`, `acl_resource_name`, `acl_resource_parent_id`, `acl_resource_type_id`) VALUES ('1198', '1', 'section:198', '1001', '2');
INSERT INTO `vic_admin`.`acl_resource` (`acl_resource_id`, `acl_tree_id`, `acl_resource_name`, `acl_resource_parent_id`, `acl_resource_type_id`) VALUES ('1199', '1', 'section:199', '1001', '2');
INSERT INTO `vic_admin`.`acl_resource` (`acl_resource_id`, `acl_tree_id`, `acl_resource_name`, `acl_resource_parent_id`, `acl_resource_type_id`) VALUES ('1200', '1', 'section:200', '1001', '2');
INSERT INTO `vic_admin`.`acl_resource` (`acl_resource_id`, `acl_tree_id`, `acl_resource_name`, `acl_resource_parent_id`, `acl_resource_type_id`) VALUES ('1201', '1', 'section:201', '1001', '2');
INSERT INTO `vic_admin`.`acl_resource` (`acl_resource_id`, `acl_tree_id`, `acl_resource_name`, `acl_resource_parent_id`, `acl_resource_type_id`) VALUES ('1202', '1', 'section:202', '1001', '2');

INSERT INTO `vic_admin`.`sekce` (
`sekce_id` ,
`acl_resource_id` ,
`nazev` ,
`zobrazeno` ,
`controller` ,
`action`
)
VALUES
('194', '1194', 'Approval Group', '0', 'approval-group', 'index'),
('196', '1196', 'Approval Group', '0', 'approval-group', 'insert'),
('197', '1197', 'Approval Group', '0', 'approval-group', 'update'),
('198', '1198', 'Approval Group', '0', 'approval-group', 'insert-threshold'),
('199', '1199', 'Approval Group', '0', 'approval-group', 'update-threshold'),
('200', '1200', 'Approval Group', '0', 'approval-group', 'delete-threshold'),
('201', '1201', 'Approval Group', '0', 'approval-group', 'assign-sport'),
('202', '1202', 'Approval Group', '0', 'approval-group', 'assign-event');

INSERT INTO `vic_admin`.`acl_role_resource_privilege` (`id`, `acl_role_id`, `acl_resource_id`, `privilege_name`, `privilege_set`) VALUES
(NULL, '3', '1194', 'read', '1'),
(NULL, '3', '1194', 'update', '1'),
(NULL, '3', '1194', 'delete', '1'),

(NULL, '3', '1196', 'read', '1'),
(NULL, '3', '1196', 'update', '1'),
(NULL, '3', '1196', 'delete', '1'),

(NULL, '3', '1197', 'read', '1'),
(NULL, '3', '1197', 'update', '1'),
(NULL, '3', '1197', 'delete', '1'),

(NULL, '3', '1198', 'read', '1'),
(NULL, '3', '1198', 'update', '1'),
(NULL, '3', '1198', 'delete', '1'),

(NULL, '3', '1199', 'read', '1'),
(NULL, '3', '1199', 'update', '1'),
(NULL, '3', '1199', 'delete', '1'),

(NULL, '3', '1200', 'read', '1'),
(NULL, '3', '1200', 'update', '1'),
(NULL, '3', '1200', 'delete', '1'),

(NULL, '3', '1201', 'read', '1'),
(NULL, '3', '1201', 'update', '1'),
(NULL, '3', '1201', 'delete', '1'),

(NULL, '3', '1202', 'read', '1'),
(NULL, '3', '1202', 'update', '1'),
(NULL, '3', '1202', 'delete', '1');

INSERT INTO `vic_admin`.`sekce_has_parent` (
`sekce_id` ,
`parent_id` ,
`sekce_order`
)
VALUES (
'194', '133', '0'
);

COMMIT;
