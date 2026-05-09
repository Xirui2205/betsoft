START TRANSACTION;

SET NAMES utf8;

INSERT INTO `acl_resource`(`acl_tree_id`, `acl_resource_name`, `acl_resource_parent_id`, `acl_resource_type_id`) VALUES
(1, 'section:399', null, 2);
SET @ar = LAST_INSERT_ID();

INSERT INTO `sekce` (`sekce_id`, `acl_resource_id`, `nazev`, `zobrazeno`, `controller`, `action`) VALUES
(399, @ar, 'dailyReportExport', 0, 'listings', 'daily-report-export');

INSERT INTO `acl_role_resource_privilege` (`acl_role_id`, `acl_resource_id`, `privilege_name`, `privilege_set`) VALUES
(1, @ar, 'read', 1),
(2, @ar, 'read', 1),
(13, @ar, 'read', 1),
(13, 1306, 'read', 1);

COMMIT;
