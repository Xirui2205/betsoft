START TRANSACTION;

INSERT INTO `vic_admin`.`acl_resource`(`acl_tree_id`, `acl_resource_name`, `acl_resource_parent_id`, `acl_resource_type_id`) VALUES
 (1, 'section:288', NULL, 2);

SET @ar = LAST_INSERT_ID();

INSERT INTO `vic_admin`.`sekce` (`sekce_id`, `acl_resource_id`, `nazev`, `zobrazeno`, `controller`, `action`) VALUES
(288, @ar, 'Event', 0, 'event', 'view');

INSERT INTO `vic_admin`.`sekce_has_parent` (`sekce_id`, `parent_id`, `sekce_order`) VALUES
(288, NULL, 0);

INSERT INTO `vic_admin`.`acl_role_resource_privilege` (`acl_role_id`, `acl_resource_id`, `privilege_name`, `privilege_set`) VALUES
(3, @ar, 'read', 1),
(3, @ar, 'write', 1),
(3, @ar, 'delete', 1),
(4, @ar, 'read', 1),
(4, @ar, 'write', 1),
(4, @ar, 'delete', 1);


INSERT INTO `vic_admin`.`acl_resource`(`acl_tree_id`, `acl_resource_name`, `acl_resource_parent_id`, `acl_resource_type_id`) VALUES
 (1, 'section:289', NULL, 2);

SET @ar = LAST_INSERT_ID();

INSERT INTO `vic_admin`.`sekce` (`sekce_id`, `acl_resource_id`, `nazev`, `zobrazeno`, `controller`, `action`) VALUES
(289, @ar, 'Event', 0, 'event', 'update');

INSERT INTO `vic_admin`.`sekce_has_parent` (`sekce_id`, `parent_id`, `sekce_order`) VALUES
(289, NULL, 0);

INSERT INTO `vic_admin`.`acl_role_resource_privilege` (`acl_role_id`, `acl_resource_id`, `privilege_name`, `privilege_set`) VALUES
(3, @ar, 'read', 1),
(3, @ar, 'write', 1),
(3, @ar, 'delete', 1),
(4, @ar, 'read', 1),
(4, @ar, 'write', 1),
(4, @ar, 'delete', 1);
INSERT INTO `vic_admin`.`acl_resource`(`acl_tree_id`, `acl_resource_name`, `acl_resource_parent_id`, `acl_resource_type_id`) VALUES
 (1, 'section:290', NULL, 2);

SET @ar = LAST_INSERT_ID();

INSERT INTO `vic_admin`.`sekce` (`sekce_id`, `acl_resource_id`, `nazev`, `zobrazeno`, `controller`, `action`) VALUES
(290, @ar, 'Event', 1, 'event', 'index');

INSERT INTO `vic_admin`.`sekce_has_parent` (`sekce_id`, `parent_id`, `sekce_order`) VALUES
(290, NULL, 0);

INSERT INTO `vic_admin`.`acl_role_resource_privilege` (`acl_role_id`, `acl_resource_id`, `privilege_name`, `privilege_set`) VALUES
(3, @ar, 'read', 1),
(3, @ar, 'write', 1),
(3, @ar, 'delete', 1),
(4, @ar, 'read', 1),
(4, @ar, 'write', 1),
(4, @ar, 'delete', 1);


INSERT INTO `vic_admin`.`acl_resource`(`acl_tree_id`, `acl_resource_name`, `acl_resource_parent_id`, `acl_resource_type_id`) VALUES
 (1, 'section:291', NULL, 2);

SET @ar = LAST_INSERT_ID();

INSERT INTO `vic_admin`.`sekce` (`sekce_id`, `acl_resource_id`, `nazev`, `zobrazeno`, `controller`, `action`) VALUES
(291, @ar, 'Event', 0, 'event', 'insert');

INSERT INTO `vic_admin`.`sekce_has_parent` (`sekce_id`, `parent_id`, `sekce_order`) VALUES
(291, NULL, 0);

INSERT INTO `vic_admin`.`acl_role_resource_privilege` (`acl_role_id`, `acl_resource_id`, `privilege_name`, `privilege_set`) VALUES
(3, @ar, 'read', 1),
(3, @ar, 'write', 1),
(3, @ar, 'delete', 1),
(4, @ar, 'read', 1),
(4, @ar, 'write', 1),
(4, @ar, 'delete', 1);


UPDATE  `vic_admin`.`sekce` SET  `zobrazeno` =  '0' WHERE  `sekce`.`sekce_id` =136;
UPDATE `vic_admin`.`acl_role_resource_privilege` SET privilege_set=0 WHERE acl_resource_id=1136;

COMMIT;
