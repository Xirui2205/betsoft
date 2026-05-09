START TRANSACTION;

SET NAMES utf8;

-- INSERT SECTION 390 affiliate-partner/partner-users
-- ------------------------------------
INSERT INTO `acl_resource`(`acl_tree_id`, `acl_resource_name`, `acl_resource_parent_id`, `acl_resource_type_id`) VALUES (1, 'section:390', 1, 2);
SET @ar = LAST_INSERT_ID();
INSERT INTO `sekce` (`sekce_id`, `acl_resource_id`, `nazev`, `zobrazeno`, `controller`, `action`) VALUES (390, @ar, 'profile_users', 0, 'affiliate-partner', 'partner-users');
INSERT INTO `acl_role_resource_privilege` (`acl_role_id`, `acl_resource_id`, `privilege_name`, `privilege_set`) VALUES (19, @ar, 'read', 1);
INSERT INTO `acl_role_resource_privilege` (`acl_role_id`, `acl_resource_id`, `privilege_name`, `privilege_set`) VALUES (1, @ar, 'read', 0);
INSERT INTO `sekce_has_parent`(`sekce_id`, `parent_id`, `sekce_order`) VALUES (390, 373, 4);

-- INSERT SECTION 391 affiliate-partner/partner-actual-provision
-- ------------------------------------
INSERT INTO `acl_resource`(`acl_tree_id`, `acl_resource_name`, `acl_resource_parent_id`, `acl_resource_type_id`) VALUES (1, 'section:391', 1, 2);
SET @ar = LAST_INSERT_ID();
INSERT INTO `sekce` (`sekce_id`, `acl_resource_id`, `nazev`, `zobrazeno`, `controller`, `action`) VALUES (391, @ar, 'actual_provision', 0, 'affiliate-partner', 'partner-actual-provision');
INSERT INTO `acl_role_resource_privilege` (`acl_role_id`, `acl_resource_id`, `privilege_name`, `privilege_set`) VALUES (19, @ar, 'read', 1);
INSERT INTO `acl_role_resource_privilege` (`acl_role_id`, `acl_resource_id`, `privilege_name`, `privilege_set`) VALUES (1, @ar, 'read', 0);
INSERT INTO `sekce_has_parent`(`sekce_id`, `parent_id`, `sekce_order`) VALUES (391, 373, 4);

COMMIT;
