START TRANSACTION;

SET NAMES utf8;

-- INSERT SECTION 360 global-parameters/insert
-- -------------------------------------------
INSERT INTO `vic_admin`.`acl_resource`(`acl_tree_id`, `acl_resource_name`, `acl_resource_parent_id`, `acl_resource_type_id`) VALUES
(1, 'section:360', null, 2);
SET @ar = LAST_INSERT_ID();

INSERT INTO `vic_admin`.`sekce` (`sekce_id`, `acl_resource_id`, `nazev`, `zobrazeno`, `controller`, `action`) VALUES
(360, @ar, 'Global Parameters', 0, 'settings-global-parameters', 'insert-branch');

INSERT INTO `vic_admin`.`acl_role_resource_privilege` (`acl_role_id`, `acl_resource_id`, `privilege_name`, `privilege_set`) VALUES
(1, @ar, 'read', 1),
(1, @ar, 'update', 1),
(1, @ar, 'delete', 1),
(2, @ar, 'read', 1),
(2, @ar, 'update', 1),
(2, @ar, 'delete', 1);

INSERT INTO `vic_admin`.`sekce_has_parent`(`sekce_id`, `parent_id`, `sekce_order`) VALUES
(360, 2, 0);

-- INSERT SECTION 361 global-parameters/delete
-- -------------------------------------------
INSERT INTO `vic_admin`.`acl_resource`(`acl_tree_id`, `acl_resource_name`, `acl_resource_parent_id`, `acl_resource_type_id`) VALUES
(1, 'section:361', null, 2);
SET @ar = LAST_INSERT_ID();

INSERT INTO `vic_admin`.`sekce` (`sekce_id`, `acl_resource_id`, `nazev`, `zobrazeno`, `controller`, `action`) VALUES
(361, @ar, 'Global Parameters', 0, 'settings-global-parameters', 'delete-branch');

INSERT INTO `vic_admin`.`acl_role_resource_privilege` (`acl_role_id`, `acl_resource_id`, `privilege_name`, `privilege_set`) VALUES
(1, @ar, 'read', 1),
(1, @ar, 'update', 1),
(1, @ar, 'delete', 1),
(2, @ar, 'read', 1),
(2, @ar, 'update', 1),
(2, @ar, 'delete', 1);

INSERT INTO `vic_admin`.`sekce_has_parent`(`sekce_id`, `parent_id`, `sekce_order`) VALUES
(361, 2, 0);

-- INSERT SECTION 362 global-parameters/info
-- -----------------------------------------
INSERT INTO `vic_admin`.`acl_resource`(`acl_tree_id`, `acl_resource_name`, `acl_resource_parent_id`, `acl_resource_type_id`) VALUES
(1, 'section:362', null, 2);
SET @ar = LAST_INSERT_ID();

INSERT INTO `vic_admin`.`sekce` (`sekce_id`, `acl_resource_id`, `nazev`, `zobrazeno`, `controller`, `action`) VALUES
(362, @ar, 'Global Parameters', 0, 'settings-global-parameters', 'info-branch');

INSERT INTO `vic_admin`.`acl_role_resource_privilege` (`acl_role_id`, `acl_resource_id`, `privilege_name`, `privilege_set`) VALUES
(1, @ar, 'read', 1),
(1, @ar, 'update', 1),
(1, @ar, 'delete', 1),
(2, @ar, 'read', 1),
(2, @ar, 'update', 1),
(2, @ar, 'delete', 1);

INSERT INTO `vic_admin`.`sekce_has_parent`(`sekce_id`, `parent_id`, `sekce_order`) VALUES
(362, 2, 0);

--
-- Struktura tabulky `log`
--
CREATE TABLE IF NOT EXISTS `parameter_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ip` char(39) DEFAULT NULL,
  `admin_id` int(10) unsigned DEFAULT NULL,
  `param_id` int(10) DEFAULT NULL,
  `time` datetime NOT NULL,
  `old_value` text,
  `new_value` text,
  `action` text,
  `message` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;


--
-- Zmena struktury tabulky `parameter`
--
ALTER TABLE `parameter` ADD `manually_inserted` TINYINT( 1 )  NULL 

-- INSERT SECTION 363 branch-parameters/insert
-- -------------------------------------------
INSERT INTO `vic_admin`.`acl_resource`(`acl_tree_id`, `acl_resource_name`, `acl_resource_parent_id`, `acl_resource_type_id`) VALUES
(1, 'section:363', null, 2);
SET @ar = LAST_INSERT_ID();

INSERT INTO `vic_admin`.`sekce` (`sekce_id`, `acl_resource_id`, `nazev`, `zobrazeno`, `controller`, `action`) VALUES
(363, @ar, 'Branch parameter', 0, 'branch-parameter', 'insert');

INSERT INTO `vic_admin`.`acl_role_resource_privilege` (`acl_role_id`, `acl_resource_id`, `privilege_name`, `privilege_set`) VALUES
(1, @ar, 'read', 1),
(1, @ar, 'update', 1),
(1, @ar, 'delete', 1),
(2, @ar, 'read', 1),
(2, @ar, 'update', 1),
(2, @ar, 'delete', 1);

INSERT INTO `vic_admin`.`sekce_has_parent`(`sekce_id`, `parent_id`, `sekce_order`) VALUES
(363, 2, 0);

-- INSERT SECTION 364 branch-parameters/info
-- -----------------------------------------
INSERT INTO `vic_admin`.`acl_resource`(`acl_tree_id`, `acl_resource_name`, `acl_resource_parent_id`, `acl_resource_type_id`) VALUES
(1, 'section:364', null, 2);
SET @ar = LAST_INSERT_ID();

INSERT INTO `vic_admin`.`sekce` (`sekce_id`, `acl_resource_id`, `nazev`, `zobrazeno`, `controller`, `action`) VALUES
(364, @ar, 'Branch parameter', 0, 'branch-parameter', 'info');

INSERT INTO `vic_admin`.`acl_role_resource_privilege` (`acl_role_id`, `acl_resource_id`, `privilege_name`, `privilege_set`) VALUES
(1, @ar, 'read', 1),
(1, @ar, 'update', 1),
(1, @ar, 'delete', 1),
(2, @ar, 'read', 1),
(2, @ar, 'update', 1),
(2, @ar, 'delete', 1);

INSERT INTO `vic_admin`.`sekce_has_parent`(`sekce_id`, `parent_id`, `sekce_order`) VALUES
(364, 2, 0);

--- DATA ---

COMMIT;