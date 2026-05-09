START TRANSACTION;

SET NAMES utf8;

-- INSERT SECTION 389 affiliate-partner/partner-tickets
-- ------------------------------------
INSERT INTO `acl_resource`(`acl_tree_id`, `acl_resource_name`, `acl_resource_parent_id`, `acl_resource_type_id`) VALUES (1, 'section:389', 1, 2);
SET @ar = LAST_INSERT_ID();
INSERT INTO `sekce` (`sekce_id`, `acl_resource_id`, `nazev`, `zobrazeno`, `controller`, `action`) VALUES (389, @ar, 'profile_tickets', 1, 'affiliate-partner', 'partner-tickets');
INSERT INTO `acl_role_resource_privilege` (`acl_role_id`, `acl_resource_id`, `privilege_name`, `privilege_set`) VALUES (19, @ar, 'read', 1);
INSERT INTO `acl_role_resource_privilege` (`acl_role_id`, `acl_resource_id`, `privilege_name`, `privilege_set`) VALUES (1, @ar, 'read', 0);
INSERT INTO `sekce_has_parent`(`sekce_id`, `parent_id`, `sekce_order`) VALUES (389, 373, 4);

COMMIT;
