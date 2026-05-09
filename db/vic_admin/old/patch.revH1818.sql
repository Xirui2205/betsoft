START TRANSACTION;

INSERT INTO `vic_admin`.`database_patch`(`revision`, `not_reapplicable`, `note`) VALUES
 ('H1818', 1, 'New node control centre section (mantis:1032)');

INSERT INTO `vic_admin`.`acl_resource_type` (`acl_resource_type_id`, `acl_resource_type_name`) VALUES ('4', 'general');

INSERT INTO `vic_admin`.`acl_resource`(`acl_tree_id`, `acl_resource_name`, `acl_resource_parent_id`, `acl_resource_type_id`) VALUES
 (1, 'section:331', NULL, 2);
SET @ar = LAST_INSERT_ID();

INSERT INTO `vic_admin`.`sekce` (`sekce_id`, `acl_resource_id`, `nazev`, `zobrazeno`, `controller`, `action`) VALUES
 (331, @ar, 'Node control centre', 1, 'node-comm', 'index');

INSERT INTO `vic_admin`.`acl_role_resource_privilege` (`acl_role_id`, `acl_resource_id`, `privilege_name`, `privilege_set`) VALUES
 (1, @ar, 'read', 1),
 (1, @ar, 'update', 1),
 (1, @ar, 'delete', 1);


INSERT INTO `vic_admin`.`acl_resource`(`acl_tree_id`, `acl_resource_name`, `acl_resource_parent_id`, `acl_resource_type_id`) VALUES
 (1, 'general:translation', NULL, 4);
SET @ar = LAST_INSERT_ID();
INSERT INTO `vic_admin`.`acl_role_resource_privilege` (`acl_role_id`, `acl_resource_id`, `privilege_name`, `privilege_set`) VALUES
 (1, @ar, 'read', 1),
 (1, @ar, 'update', 1),
 (1, @ar, 'delete', 1);

INSERT INTO `vic_admin`.`acl_resource`(`acl_tree_id`, `acl_resource_name`, `acl_resource_parent_id`, `acl_resource_type_id`) VALUES
 (1, 'general:memcache', NULL, 4);
SET @ar = LAST_INSERT_ID();
INSERT INTO `vic_admin`.`acl_role_resource_privilege` (`acl_role_id`, `acl_resource_id`, `privilege_name`, `privilege_set`) VALUES
 (1, @ar, 'read', 1),
 (1, @ar, 'update', 1),
 (1, @ar, 'delete', 1);

INSERT INTO `vic_admin`.`acl_resource`(`acl_tree_id`, `acl_resource_name`, `acl_resource_parent_id`, `acl_resource_type_id`) VALUES
 (1, 'general:apc', NULL, 4);
SET @ar = LAST_INSERT_ID();
INSERT INTO `vic_admin`.`acl_role_resource_privilege` (`acl_role_id`, `acl_resource_id`, `privilege_name`, `privilege_set`) VALUES
 (1, @ar, 'read', 1),
 (1, @ar, 'update', 1),
 (1, @ar, 'delete', 1);

INSERT INTO `vic_admin`.`acl_resource`(`acl_tree_id`, `acl_resource_name`, `acl_resource_parent_id`, `acl_resource_type_id`) VALUES
 (1, 'general:cache-web-content', NULL, 4);
SET @ar = LAST_INSERT_ID();
INSERT INTO `vic_admin`.`acl_role_resource_privilege` (`acl_role_id`, `acl_resource_id`, `privilege_name`, `privilege_set`) VALUES
 (1, @ar, 'read', 1),
 (1, @ar, 'update', 1),
 (1, @ar, 'delete', 1);

INSERT INTO `vic_admin`.`acl_resource`(`acl_tree_id`, `acl_resource_name`, `acl_resource_parent_id`, `acl_resource_type_id`) VALUES
 (1, 'general:cache-acl', NULL, 4);
SET @ar = LAST_INSERT_ID();
INSERT INTO `vic_admin`.`acl_role_resource_privilege` (`acl_role_id`, `acl_resource_id`, `privilege_name`, `privilege_set`) VALUES
 (1, @ar, 'read', 1),
 (1, @ar, 'update', 1),
 (1, @ar, 'delete', 1);


COMMIT;
