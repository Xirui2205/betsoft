START TRANSACTION;

INSERT INTO vic_admin.`database_patch`(`revision`, `not_reapplicable`, `note`)
 VALUES (3074, 1, 'Uprava sekci a prav klientskych karet');

SET NAMES utf8;


-- ROLES FOR BOOKMAKERS
INSERT INTO `vic_admin`.`acl_role_resource_privilege` (`acl_role_id`, `acl_resource_id`, `privilege_name`, `privilege_set`) VALUES
(3, 1371, 'read', 1),
(3, 1371, 'update', 1),
(3, 1371, 'delete', 1),
(4, 1371, 'read', 1),
(4, 1371, 'update', 1),
(4, 1371, 'delete', 1);


-- INSERT/UPDATE SECTION 340 client-card/view
-- ------------------------------------
INSERT INTO `vic_admin`.`acl_resource`(`acl_tree_id`, `acl_resource_name`, `acl_resource_parent_id`, `acl_resource_type_id`) VALUES
(1, 'section:340', 1, 2);
SET @ar = LAST_INSERT_ID();

UPDATE `vic_admin`.`sekce` SET `acl_resource_id` = @ar WHERE `sekce_id` = 340;

INSERT INTO `vic_admin`.`acl_role_resource_privilege` (`acl_role_id`, `acl_resource_id`, `privilege_name`, `privilege_set`) VALUES
(1, @ar, 'read', 1),
(1, @ar, 'update', 1),
(1, @ar, 'delete', 1),
(2, @ar, 'read', 1),
(2, @ar, 'update', 1),
(2, @ar, 'delete', 1),
(3, @ar, 'read', 1),
(3, @ar, 'update', 1),
(3, @ar, 'delete', 1),
(4, @ar, 'read', 1),
(4, @ar, 'update', 1),
(4, @ar, 'delete', 1);

INSERT INTO `vic_admin`.`sekce_has_parent`(`sekce_id`, `parent_id`, `sekce_order`) VALUES
(340, 334, 2);


-- INSERT/UPDATE SECTION 341 client-card/unblock
-- ------------------------------------
INSERT INTO `vic_admin`.`acl_resource`(`acl_tree_id`, `acl_resource_name`, `acl_resource_parent_id`, `acl_resource_type_id`) VALUES
(1, 'section:341', 1, 2);
SET @ar = LAST_INSERT_ID();

UPDATE `vic_admin`.`sekce` SET `acl_resource_id` = @ar WHERE `sekce_id` = 341;

INSERT INTO `vic_admin`.`acl_role_resource_privilege` (`acl_role_id`, `acl_resource_id`, `privilege_name`, `privilege_set`) VALUES
(1, @ar, 'read', 1),
(1, @ar, 'update', 1),
(1, @ar, 'delete', 1),
(2, @ar, 'read', 1),
(2, @ar, 'update', 1),
(2, @ar, 'delete', 1),
(3, @ar, 'read', 1),
(3, @ar, 'update', 1),
(3, @ar, 'delete', 1),
(4, @ar, 'read', 1),
(4, @ar, 'update', 1),
(4, @ar, 'delete', 1);

INSERT INTO `vic_admin`.`sekce_has_parent`(`sekce_id`, `parent_id`, `sekce_order`) VALUES
(341, 334, 2);


-- INSERT/UPDATE SECTION 342 client-card/block-more-cards
-- ------------------------------------
INSERT INTO `vic_admin`.`acl_resource`(`acl_tree_id`, `acl_resource_name`, `acl_resource_parent_id`, `acl_resource_type_id`) VALUES
(1, 'section:342', 1, 2);
SET @ar = LAST_INSERT_ID();

UPDATE `vic_admin`.`sekce` SET `acl_resource_id` = @ar WHERE `sekce_id` = 342;

INSERT INTO `vic_admin`.`acl_role_resource_privilege` (`acl_role_id`, `acl_resource_id`, `privilege_name`, `privilege_set`) VALUES
(1, @ar, 'read', 1),
(1, @ar, 'update', 1),
(1, @ar, 'delete', 1),
(2, @ar, 'read', 1),
(2, @ar, 'update', 1),
(2, @ar, 'delete', 1),
(3, @ar, 'read', 1),
(3, @ar, 'update', 1),
(3, @ar, 'delete', 1),
(4, @ar, 'read', 1),
(4, @ar, 'update', 1),
(4, @ar, 'delete', 1);

INSERT INTO `vic_admin`.`sekce_has_parent`(`sekce_id`, `parent_id`, `sekce_order`) VALUES
(342, 334, 2);


-- INSERT/UPDATE SECTION 343 client-card/unblock-more-cards
-- ------------------------------------
INSERT INTO `vic_admin`.`acl_resource`(`acl_tree_id`, `acl_resource_name`, `acl_resource_parent_id`, `acl_resource_type_id`) VALUES
(1, 'section:343', 1, 2);
SET @ar = LAST_INSERT_ID();

UPDATE `vic_admin`.`sekce` SET `acl_resource_id` = @ar WHERE `sekce_id` = 343;

INSERT INTO `vic_admin`.`acl_role_resource_privilege` (`acl_role_id`, `acl_resource_id`, `privilege_name`, `privilege_set`) VALUES
(1, @ar, 'read', 1),
(1, @ar, 'update', 1),
(1, @ar, 'delete', 1),
(2, @ar, 'read', 1),
(2, @ar, 'update', 1),
(2, @ar, 'delete', 1),
(3, @ar, 'read', 1),
(3, @ar, 'update', 1),
(3, @ar, 'delete', 1),
(4, @ar, 'read', 1),
(4, @ar, 'update', 1),
(4, @ar, 'delete', 1);

INSERT INTO `vic_admin`.`sekce_has_parent`(`sekce_id`, `parent_id`, `sekce_order`) VALUES
(343, 334, 2);

COMMIT;
