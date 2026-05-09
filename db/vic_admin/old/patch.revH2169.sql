START TRANSACTION;

INSERT INTO vic_admin.`database_patch`(`revision`, `not_reapplicable`, `note`)
 VALUES (2169, 1, 'Bet types tied to event as opposed to sport');

SET NAMES utf8;



-- INSERT SECTION 334 client-card/index
-- ------------------------------------
INSERT INTO `vic_admin`.`acl_resource`(`acl_tree_id`, `acl_resource_name`, `acl_resource_parent_id`, `acl_resource_type_id`) VALUES
(1, 'section:334', 1, 2);
SET @ar = LAST_INSERT_ID();

INSERT INTO `vic_admin`.`sekce` (`sekce_id`, `acl_resource_id`, `nazev`, `zobrazeno`, `controller`, `action`) VALUES
(334, @ar, 'client cards', 1, 'client-card', 'index');

INSERT INTO `vic_admin`.`acl_role_resource_privilege` (`acl_role_id`, `acl_resource_id`, `privilege_name`, `privilege_set`) VALUES
(1, @ar, 'read', 1),
(1, @ar, 'update', 1),
(1, @ar, 'delete', 1),
(2, @ar, 'read', 1),
(2, @ar, 'update', 1),
(2, @ar, 'delete', 1);

INSERT INTO `vic_admin`.`sekce_has_parent`(`sekce_id`, `parent_id`, `sekce_order`) VALUES
(334, NULL, 2);


-- INSERT SECTION 335 client-card/edit
-- ------------------------------------
INSERT INTO `vic_admin`.`acl_resource`(`acl_tree_id`, `acl_resource_name`, `acl_resource_parent_id`, `acl_resource_type_id`) VALUES
(1, 'section:335', 1, 2);
SET @ar = LAST_INSERT_ID();

INSERT INTO `vic_admin`.`sekce` (`sekce_id`, `acl_resource_id`, `nazev`, `zobrazeno`, `controller`, `action`) VALUES
(335, @ar, 'edit client card', 0, 'client-card', 'edit');

INSERT INTO `vic_admin`.`acl_role_resource_privilege` (`acl_role_id`, `acl_resource_id`, `privilege_name`, `privilege_set`) VALUES
(1, @ar, 'read', 1),
(1, @ar, 'update', 1),
(1, @ar, 'delete', 1),
(2, @ar, 'read', 1),
(2, @ar, 'update', 1),
(2, @ar, 'delete', 1);

INSERT INTO `vic_admin`.`sekce_has_parent`(`sekce_id`, `parent_id`, `sekce_order`) VALUES
(335, 334, 2);


-- INSERT SECTION 336 client-card/insert
-- ------------------------------------
INSERT INTO `vic_admin`.`acl_resource`(`acl_tree_id`, `acl_resource_name`, `acl_resource_parent_id`, `acl_resource_type_id`) VALUES
(1, 'section:336', 1, 2);
SET @ar = LAST_INSERT_ID();

INSERT INTO `vic_admin`.`sekce` (`sekce_id`, `acl_resource_id`, `nazev`, `zobrazeno`, `controller`, `action`) VALUES
(336, @ar, 'insert client card', 0, 'client-card', 'insert');

INSERT INTO `vic_admin`.`acl_role_resource_privilege` (`acl_role_id`, `acl_resource_id`, `privilege_name`, `privilege_set`) VALUES
(1, @ar, 'read', 1),
(1, @ar, 'update', 1),
(1, @ar, 'delete', 1),
(2, @ar, 'read', 1),
(2, @ar, 'update', 1),
(2, @ar, 'delete', 1);

INSERT INTO `vic_admin`.`sekce_has_parent`(`sekce_id`, `parent_id`, `sekce_order`) VALUES
(336, 334, 2);


-- INSERT SECTION 337 client-card/block
-- ------------------------------------
INSERT INTO `vic_admin`.`acl_resource`(`acl_tree_id`, `acl_resource_name`, `acl_resource_parent_id`, `acl_resource_type_id`) VALUES
(1, 'section:337', 1, 2);
SET @ar = LAST_INSERT_ID();

INSERT INTO `vic_admin`.`sekce` (`sekce_id`, `acl_resource_id`, `nazev`, `zobrazeno`, `controller`, `action`) VALUES
(337, @ar, 'block client card', 0, 'client-card', 'block');

INSERT INTO `vic_admin`.`acl_role_resource_privilege` (`acl_role_id`, `acl_resource_id`, `privilege_name`, `privilege_set`) VALUES
(1, @ar, 'read', 1),
(1, @ar, 'update', 1),
(1, @ar, 'delete', 1),
(2, @ar, 'read', 1),
(2, @ar, 'update', 1),
(2, @ar, 'delete', 1);

INSERT INTO `vic_admin`.`sekce_has_parent`(`sekce_id`, `parent_id`, `sekce_order`) VALUES
(337, 334, 2);


-- INSERT SECTION 338 client-card/cards-to-branch
-- ------------------------------------
INSERT INTO `vic_admin`.`acl_resource`(`acl_tree_id`, `acl_resource_name`, `acl_resource_parent_id`, `acl_resource_type_id`) VALUES
(1, 'section:338', 1, 2);
SET @ar = LAST_INSERT_ID();

INSERT INTO `vic_admin`.`sekce` (`sekce_id`, `acl_resource_id`, `nazev`, `zobrazeno`, `controller`, `action`) VALUES
(338, @ar, 'block client card', 0, 'client-card', 'cards-to-branch');

INSERT INTO `vic_admin`.`acl_role_resource_privilege` (`acl_role_id`, `acl_resource_id`, `privilege_name`, `privilege_set`) VALUES
(1, @ar, 'read', 1),
(1, @ar, 'update', 1),
(1, @ar, 'delete', 1),
(2, @ar, 'read', 1),
(2, @ar, 'update', 1),
(2, @ar, 'delete', 1);

INSERT INTO `vic_admin`.`sekce_has_parent`(`sekce_id`, `parent_id`, `sekce_order`) VALUES
(338, 334, 2);



COMMIT;