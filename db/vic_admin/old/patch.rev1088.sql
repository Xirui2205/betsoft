DELETE FROM `role_resource_privilege` WHERE `section_id` IN
(165,166,167,168,169,180,181,182,183,184,185,186);

REPLACE INTO `sekce` (`sekce_id`, `parent_id`, `nazev`, `zobrazeno`, `poradi`, `controller`, `action`) VALUES
(180, 0, 'Branch host', 1, 0, 'branch-host', 'view'),
(181, 0, 'Branch host', 1, 0, 'branch-host', 'update'),
(182, 0, 'Branch host', 1, 0, 'branch-host', 'insert'),
(183, 0, 'Branch user', 1, 0, 'branch-user', 'view'),
(184, 0, 'Branch user', 1, 0, 'branch-user', 'assign'),
(185, 0, 'Branch employee', 1, 0, 'branch-employee', 'view'),
(186, 0, 'Branch employee', 1, 0, 'branch-employee', 'get-timesheet'),
(165, 0, 'Branch crud', 1, 0, 'branch', 'route'),
(166, 0, 'Branch crud', 1, 0, 'branch-info', 'insert-update'),
(167, 0, 'Branch crud', 1, 0, 'branch-param', 'insert-update'),
(168, 0, 'Branch crud', 1, 0, 'branch-contract', 'insert-update'),
(169, 0, 'Branch crud', 1, 0, 'branch-bank', 'insert-update');


REPLACE INTO `role_resource_privilege` (`id`, `role_id`, `section_id`, `privilege_name`, `privilege_set`) VALUES
(NULL, 2, 165, 'update', 1),
(NULL, 2, 165, 'delete', 1),
(NULL, 2, 165, 'read', 1),
(NULL, 2, 166, 'update', 1),
(NULL, 2, 166, 'delete', 1),
(NULL, 2, 166, 'read', 1),
(NULL, 2, 167, 'read', 1),
(NULL, 2, 167, 'delete', 1),
(NULL, 2, 167, 'update', 1),
(NULL, 2, 168, 'update', 1),
(NULL, 2, 168, 'delete', 1),
(NULL, 2, 168, 'read', 1),
(NULL, 2, 169, 'update', 1),
(NULL, 2, 169, 'delete', 1),
(NULL, 2, 169, 'read', 1),
(NULL, 2, 180, 'read', 1),
(NULL, 2, 180, 'update', 1),
(NULL, 2, 180, 'delete', 1),
(NULL, 2, 181, 'read', 1),
(NULL, 2, 181, 'update', 1),
(NULL, 2, 181, 'delete', 1),
(NULL, 2, 182, 'read', 1),
(NULL, 2, 182, 'update', 1),
(NULL, 2, 182, 'delete', 1),
(NULL, 2, 183, 'read', 1),
(NULL, 2, 183, 'update', 1),
(NULL, 2, 183, 'delete', 1),
(NULL, 2, 184, 'read', 1),
(NULL, 2, 185, 'read', 1),
(NULL, 2, 186, 'read', 1);
