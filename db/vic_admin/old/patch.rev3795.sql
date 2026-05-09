START TRANSACTION;

SET NAMES utf8;

-- INSERT SECTION 352 sounds-settings/index
-- ------------------------------------
INSERT INTO `vic_admin`.`acl_resource`(`acl_tree_id`, `acl_resource_name`, `acl_resource_parent_id`, `acl_resource_type_id`) VALUES
(1, 'section:352', null, 2);
SET @ar = LAST_INSERT_ID();

INSERT INTO `vic_admin`.`sekce` (`sekce_id`, `acl_resource_id`, `nazev`, `zobrazeno`, `controller`, `action`) VALUES
(352, @ar, 'Systémové zvuky', 1, 'sounds-settings', 'index');

INSERT INTO `vic_admin`.`acl_role_resource_privilege` (`acl_role_id`, `acl_resource_id`, `privilege_name`, `privilege_set`) VALUES
(1, @ar, 'read', 1),
(1, @ar, 'update', 1),
(1, @ar, 'delete', 1),
(2, @ar, 'read', 1),
(2, @ar, 'update', 1),
(2, @ar, 'delete', 1);

INSERT INTO `vic_admin`.`sekce_has_parent`(`sekce_id`, `parent_id`, `sekce_order`) VALUES
(352, 160, 0);


INSERT INTO `vic_admin`.`parameter` (`id` ,`name` ,`value` ,`is_host` ,`is_branch` ,`is_user` ,`type` ,`mandatory` ,
                                     `is_admin` ,`is_editable` ,`description`)
VALUES (NULL , 'confirmation.sound.level1', 'cat_meow.ogg', '', '', '1', NULL , '1', '1', '1', 'zvuk pri povolovani tiketu'),
       (NULL , 'confirmation.sound.level2', 'cannon_fire.ogg', '', '', '1', NULL , '1', '1', '1', 'zvuk pri povolovani tiketu'),
       (NULL , 'confirmation.sound.level3', 'siren.ogg', '', '', '1', NULL , '1', '1', '1', 'zvuk pri povolovani tiketu');

COMMIT;