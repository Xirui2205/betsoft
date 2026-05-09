START TRANSACTION;

INSERT INTO vic_admin.`database_patch`(`revision`, `not_reapplicable`, `note`)
 VALUES ('H1342', 1, 'New section for showing preview for promo.');

SET NAMES utf8;

INSERT INTO `vic_admin`.`acl_resource`(`acl_tree_id`, `acl_resource_name`, `acl_resource_parent_id`, `acl_resource_type_id`) VALUES
(1, 'section:321', 1, 2);
SET @ar = LAST_INSERT_ID();

INSERT INTO `vic_admin`.`sekce` (`sekce_id`, `acl_resource_id`, `nazev`, `zobrazeno`, `controller`, `action`) VALUES
(321, @ar, 'Promo preview', 0, NULL, NULL);

INSERT INTO `vic_admin`.`acl_role_resource_privilege` (`acl_role_id`, `acl_resource_id`, `privilege_name`, `privilege_set`) VALUES
(1, @ar, 'read', 1),
(1, @ar, 'update', 1),
(1, @ar, 'delete', 1),
(2, @ar, 'read', 1),
(2, @ar, 'update', 1),
(2, @ar, 'delete', 1);



INSERT INTO `vic_admin`.`sekce_has_parent`(`sekce_id`, `parent_id`, `sekce_order`) VALUES
(321, 98, 2);


COMMIT;
