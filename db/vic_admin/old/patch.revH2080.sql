START TRANSACTION;

INSERT INTO vic_admin.`database_patch`(`revision`, `not_reapplicable`, `note`)
 VALUES (2080, 1, 'Bet types tied to event as opposed to sport');

SET NAMES utf8;



-- INSERT SECTION 328 type/edit-detail
-- ------------------------------------
INSERT INTO `vic_admin`.`acl_resource`(`acl_tree_id`, `acl_resource_name`, `acl_resource_parent_id`, `acl_resource_type_id`) VALUES
(1, 'section:328', 1, 2);
SET @ar = LAST_INSERT_ID();

INSERT INTO `vic_admin`.`sekce` (`sekce_id`, `acl_resource_id`, `nazev`, `zobrazeno`, `controller`, `action`) VALUES
(328, @ar, 'edit bet type detail', 0, 'type', 'edit-detail');

INSERT INTO `vic_admin`.`acl_role_resource_privilege` (`acl_role_id`, `acl_resource_id`, `privilege_name`, `privilege_set`) VALUES
(3, @ar, 'read', 1),
(3, @ar, 'update', 1),
(3, @ar, 'delete', 1),
(4, @ar, 'read', 1),
(4, @ar, 'update', 1),
(4, @ar, 'delete', 1);

INSERT INTO `vic_admin`.`sekce_has_parent`(`sekce_id`, `parent_id`, `sekce_order`) VALUES
(328, 138, 2);



-- INSERT SECTION 330 event/update-type-event
-- -------------------------------------------
INSERT INTO `vic_admin`.`acl_resource`(`acl_tree_id`, `acl_resource_name`, `acl_resource_parent_id`, `acl_resource_type_id`) VALUES
(1, 'section:330', 1, 2);
SET @ar = LAST_INSERT_ID();

INSERT INTO `vic_admin`.`sekce` (`sekce_id`, `acl_resource_id`, `nazev`, `zobrazeno`, `controller`, `action`) VALUES
(330, @ar, 'edit type-event ', 0, 'event', 'update-type-event');

INSERT INTO `vic_admin`.`acl_role_resource_privilege` (`acl_role_id`, `acl_resource_id`, `privilege_name`, `privilege_set`) VALUES
(3, @ar, 'read', 1),
(3, @ar, 'update', 1),
(3, @ar, 'delete', 1),
(4, @ar, 'read', 1),
(4, @ar, 'update', 1),
(4, @ar, 'delete', 1);

INSERT INTO `vic_admin`.`sekce_has_parent`(`sekce_id`, `parent_id`, `sekce_order`) VALUES
(330, 290, 3);



-- INSERT SECTION 332 type/insert
-- ------------------------------------
INSERT INTO `vic_admin`.`acl_resource`(`acl_tree_id`, `acl_resource_name`, `acl_resource_parent_id`, `acl_resource_type_id`) VALUES
(1, 'section:332', 1, 2);
SET @ar = LAST_INSERT_ID();

INSERT INTO `vic_admin`.`sekce` (`sekce_id`, `acl_resource_id`, `nazev`, `zobrazeno`, `controller`, `action`) VALUES
(332, @ar, 'insert type', 0, 'type', 'insert');

INSERT INTO `vic_admin`.`acl_role_resource_privilege` (`acl_role_id`, `acl_resource_id`, `privilege_name`, `privilege_set`) VALUES
(3, @ar, 'read', 1),
(3, @ar, 'update', 1),
(3, @ar, 'delete', 1),
(4, @ar, 'read', 1),
(4, @ar, 'update', 1),
(4, @ar, 'delete', 1);

INSERT INTO `vic_admin`.`sekce_has_parent`(`sekce_id`, `parent_id`, `sekce_order`) VALUES
(332, 138, 3);



-- INSERT SECTION 332 type/insert
-- ------------------------------------
INSERT INTO `vic_admin`.`acl_resource`(`acl_tree_id`, `acl_resource_name`, `acl_resource_parent_id`, `acl_resource_type_id`) VALUES
(1, 'section:333', 1, 2);
SET @ar = LAST_INSERT_ID();

INSERT INTO `vic_admin`.`sekce` (`sekce_id`, `acl_resource_id`, `nazev`, `zobrazeno`, `controller`, `action`) VALUES
(333, @ar, 'delete type', 0, 'type', 'delete');

INSERT INTO `vic_admin`.`acl_role_resource_privilege` (`acl_role_id`, `acl_resource_id`, `privilege_name`, `privilege_set`) VALUES
(3, @ar, 'read', 1),
(3, @ar, 'update', 1),
(3, @ar, 'delete', 1),
(4, @ar, 'read', 1),
(4, @ar, 'update', 1),
(4, @ar, 'delete', 1);

INSERT INTO `vic_admin`.`sekce_has_parent`(`sekce_id`, `parent_id`, `sekce_order`) VALUES
(333, 138, 3);


-- RENAME MENU ENTRIES TO BE COMPATIBEL WITH THE REST OF THE SYSTEM
UPDATE `vic_admin`.`sekce` SET nazev='Podtypy sazek' WHERE sekce_id=139;
UPDATE `vic_admin`.`sekce` SET nazev='Typy sazek', controller='type', action='index' WHERE sekce_id=138;



COMMIT;