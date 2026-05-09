START TRANSACTION;

INSERT INTO vic_admin.`database_patch`(`revision`, `not_reapplicable`, `note`)
 VALUES ('H914', 1, 'New sections of user profile for BM (mantis:936)');

SET NAMES utf8;

INSERT INTO `vic_admin`.`acl_resource`(`acl_tree_id`, `acl_resource_name`, `acl_resource_parent_id`, `acl_resource_type_id`) VALUES
(1, 'section:318', 1, 2);
SET @ar = LAST_INSERT_ID();
SET @ar318 = @ar;

INSERT INTO `vic_admin`.`sekce` (`sekce_id`, `acl_resource_id`, `nazev`, `zobrazeno`, `controller`, `action`) VALUES
(318, @ar, 'Uživatelé (BM)', 1, 'user-profile', 'index-bm');

INSERT INTO `vic_admin`.`acl_role_resource_privilege` (`acl_role_id`, `acl_resource_id`, `privilege_name`, `privilege_set`) VALUES
(3, @ar, 'read', 1),
(3, @ar, 'update', 1),
(3, @ar, 'delete', 1),
(4, @ar, 'read', 1),
(4, @ar, 'update', 1),
(4, @ar, 'delete', 1);

INSERT INTO `vic_admin`.`acl_resource`(`acl_tree_id`, `acl_resource_name`, `acl_resource_parent_id`, `acl_resource_type_id`) VALUES
(1, 'section:319', @ar318, 2);
SET @ar = LAST_INSERT_ID();

INSERT INTO `vic_admin`.`sekce` (`sekce_id`, `acl_resource_id`, `nazev`, `zobrazeno`, `controller`, `action`) VALUES
(319, @ar, 'Uživatelé - náhled (BM)', 0, 'user-profile', 'view-bm');

INSERT INTO `vic_admin`.`acl_role_resource_privilege` (`acl_role_id`, `acl_resource_id`, `privilege_name`, `privilege_set`) VALUES
(3, @ar, 'read', 1),
(3, @ar, 'update', 1),
(3, @ar, 'delete', 1),
(4, @ar, 'read', 1),
(4, @ar, 'update', 1),
(4, @ar, 'delete', 1);

INSERT INTO `vic_admin`.`acl_resource`(`acl_tree_id`, `acl_resource_name`, `acl_resource_parent_id`, `acl_resource_type_id`) VALUES
(1, 'section:320', @ar318, 2);
SET @ar = LAST_INSERT_ID();

INSERT INTO `vic_admin`.`sekce` (`sekce_id`, `acl_resource_id`, `nazev`, `zobrazeno`, `controller`, `action`) VALUES
(320, @ar, 'Uživatelé - úpravy (BM)', 0, 'user-profile', 'update-bm');

INSERT INTO `vic_admin`.`acl_role_resource_privilege` (`acl_role_id`, `acl_resource_id`, `privilege_name`, `privilege_set`) VALUES
(3, @ar, 'read', 1),
(3, @ar, 'update', 1),
(3, @ar, 'delete', 1),
(4, @ar, 'read', 1),
(4, @ar, 'update', 1),
(4, @ar, 'delete', 1);

INSERT INTO `vic_admin`.`sekce_has_parent`(`sekce_id`, `parent_id`, `sekce_order`) VALUES
 (318, NULL, 0),
 (319, 318, 1),
 (320, 318, 2);
 
DELETE FROM `vic_admin`.`acl_role_resource_privilege` WHERE `acl_role_id` IN (3,4) AND `acl_resource_id` IN (
 SELECT `acl_resource_id` FROM `vic_admin`.`sekce` WHERE `sekce_id` IN (40,227,228,249,299)
);

COMMIT;
