START TRANSACTION;

SET NAMES utf8;

INSERT INTO `acl_resource`(`acl_tree_id`, `acl_resource_name`, `acl_resource_parent_id`, `acl_resource_type_id`) VALUES
(1, 'section:394', null, 2);
SET @ar = LAST_INSERT_ID();

INSERT INTO `sekce` (`sekce_id`, `acl_resource_id`, `nazev`, `zobrazeno`, `controller`, `action`) VALUES
(394, @ar, 'user-provision', 1, 'users-provision', 'provision');

INSERT INTO `acl_role_resource_privilege` (`acl_role_id`, `acl_resource_id`, `privilege_name`, `privilege_set`) VALUES
(1, @ar, 'read', 1),
(1, @ar, 'update', 1),
(1, @ar, 'delete', 1),
(2, @ar, 'read', 1),
(2, @ar, 'update', 1),
(2, @ar, 'delete', 1);

INSERT INTO `sekce_has_parent`(`sekce_id`, `parent_id`, `sekce_order`) VALUES
(394, 211, 0);

INSERT INTO `parameter` (`name`, `value`, `is_host`, `is_branch`, `is_user`, `mandatory`, `is_admin`, `is_editable`, `description`) VALUES
('limit.rewards.for.service', '200', 0, 1, 0, 1, 0, 1, 'Limit odměny pro obsluhu.');

INSERT INTO `parameter` (`name`, `value`, `is_host`, `is_branch`, `is_user`, `mandatory`, `is_admin`, `is_editable`, `description`) VALUES
('amount.of.remuneration.for.service', '50', 0, 1, 0, 1, 0, 1, 'Částka odměny pro obsluhu.');

COMMIT;
