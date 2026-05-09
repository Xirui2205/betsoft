INSERT INTO `sekce` (`sekce_id`, `parent_id`, `nazev`, `zobrazeno`, `poradi`, `controller`, `action`) VALUES
(194, 2, 'Approval Group (NEW)', 1, 0, 'approval-group', NULL),
(196, 0, 'Approval Group', 0, 0, 'approval-group', 'insert'),
(197, 0, 'Approval Group', 0, 0, 'approval-group', 'update'),
(198, 0, 'Approval Group', 0, 0, 'approval-group', 'insert-threshold'),
(199, 0, 'Approval Group', 0, 0, 'approval-group', 'update-threshold'),
(200, 0, 'Approval Group', 0, 0, 'approval-group', 'delete-threshold'),
(201, 0, 'Approval Group', 0, 0, 'approval-group', 'assign-sport'),
(202, 0, 'Approval Group', 0, 0, 'approval-group', 'assign-event');

INSERT INTO `role_resource_privilege` (`id`, `role_id`, `section_id`, `privilege_name`, `privilege_set`) VALUES
(395, 2, 196, 'update', 1),
(398, 2, 198, 'insert', 1),
(399, 2, 197, 'insert', 1),
(400, 2, 200, 'delete', 1),
(401, 2, 199, 'update', 1),
(402, 2, 201, 'update', 1),
(403, 2, 202, 'update', 1),
(NULL, 2, 194, 'read', 1);
