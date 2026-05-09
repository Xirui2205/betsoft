START TRANSACTION;

SET NAMES utf8;

-- INSERT SECTION 372 branch-parameter/delete
-- -------------------------------------------
INSERT INTO `acl_resource`(`acl_tree_id`, `acl_resource_name`, `acl_resource_parent_id`, `acl_resource_type_id`) VALUES
(1, 'section:372', null, 2);
SET @ar = LAST_INSERT_ID();

INSERT INTO `sekce` (`sekce_id`, `acl_resource_id`, `nazev`, `zobrazeno`, `controller`, `action`) VALUES
(372, @ar, 'Global Parameters', 0, 'branch-parameter', 'delete-param');

INSERT INTO `acl_role_resource_privilege` (`acl_role_id`, `acl_resource_id`, `privilege_name`, `privilege_set`) VALUES
(1, @ar, 'read', 1),
(1, @ar, 'update', 1),
(1, @ar, 'delete', 1),
(2, @ar, 'read', 1),
(2, @ar, 'update', 1),
(2, @ar, 'delete', 1);

INSERT INTO `sekce_has_parent`(`sekce_id`, `parent_id`, `sekce_order`) VALUES
(372, 2, 0);

ALTER TABLE `branch_has_parameter` ADD `branch_insert` BOOLEAN NULL;

COMMIT;