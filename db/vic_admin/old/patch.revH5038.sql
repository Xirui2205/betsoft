START TRANSACTION;

SET NAMES utf8;

INSERT INTO `acl_role`(`acl_role_id`, `acl_role_name`, `assignable`) VALUES (19, 'affiliate-partner', 1);

-- INSERT SECTION 386 affiliate-partner/partner-index
-- ------------------------------------
INSERT INTO `acl_resource`(`acl_tree_id`, `acl_resource_name`, `acl_resource_parent_id`, `acl_resource_type_id`) VALUES (1, 'section:386', 1, 2);
SET @ar = LAST_INSERT_ID();
INSERT INTO `sekce` (`sekce_id`, `acl_resource_id`, `nazev`, `zobrazeno`, `controller`, `action`) VALUES (386, @ar, 'affiliate-partner partner-index', 1, 'affiliate-partner', 'partner-index');
INSERT INTO `acl_role_resource_privilege` (`acl_role_id`, `acl_resource_id`, `privilege_name`, `privilege_set`) VALUES (19, @ar, 'read', 1);
INSERT INTO `sekce_has_parent`(`sekce_id`, `parent_id`, `sekce_order`) VALUES (386, 373, 4);

-- INSERT SECTION 387 affiliate-partner/partner-view
-- ------------------------------------
INSERT INTO `acl_resource`(`acl_tree_id`, `acl_resource_name`, `acl_resource_parent_id`, `acl_resource_type_id`) VALUES (1, 'section:387', 1, 2);
SET @ar = LAST_INSERT_ID();
INSERT INTO `sekce` (`sekce_id`, `acl_resource_id`, `nazev`, `zobrazeno`, `controller`, `action`) VALUES (387, @ar, 'affiliate-partner partner-view', 0, 'affiliate-partner', 'partner-view');
INSERT INTO `acl_role_resource_privilege` (`acl_role_id`, `acl_resource_id`, `privilege_name`, `privilege_set`) VALUES (19, @ar, 'read', 1);
INSERT INTO `sekce_has_parent`(`sekce_id`, `parent_id`, `sekce_order`) VALUES (387, 373, 4);

-- INSERT SECTION 388 affiliate-partner/partner-banners
-- ------------------------------------
INSERT INTO `acl_resource`(`acl_tree_id`, `acl_resource_name`, `acl_resource_parent_id`, `acl_resource_type_id`) VALUES (1, 'section:388', 1, 2);
SET @ar = LAST_INSERT_ID();
INSERT INTO `sekce` (`sekce_id`, `acl_resource_id`, `nazev`, `zobrazeno`, `controller`, `action`) VALUES (388, @ar, 'affiliate-partner partner-banners', 0, 'affiliate-partner', 'partner-banners');
INSERT INTO `acl_role_resource_privilege` (`acl_role_id`, `acl_resource_id`, `privilege_name`, `privilege_set`) VALUES (19, @ar, 'read', 1);
INSERT INTO `sekce_has_parent`(`sekce_id`, `parent_id`, `sekce_order`) VALUES (388, 373, 4);

COMMIT;
