

INSERT INTO `sekce` (`sekce_id`, `parent_id`, `nazev`, `zobrazeno`, `poradi`, `controller`) VALUES
(159, 1, 'Branch', 1, 0, 'branch'),
(160, 1, 'Obsah', 1, 0, NULL),
(161, 160, 'Superhome', 1, 0, NULL),
(162, 98, 'Superhome', 1, 0, NULL),
(163, 0, 'Branch detail', 1, 0, 'branch-detail'),
(164, 0, 'Branch crud', 1, 0, 'branch-crud');


INSERT INTO `role_resource_privilege` (`id`, `role_id`, `section_id`, `privilege_name`, `privilege_set`) VALUES
(342, 2, 164, 'update', 1),
(341, 2, 164, 'read', 1),
(337, 2, 163, 'read', 1),
(336, 2, 162, 'delete', 1),
(335, 2, 161, 'delete', 1),
(334, 2, 162, 'update', 1),
(333, 2, 161, 'update', 1),
(332, 2, 162, 'read', 1),
(331, 2, 161, 'read', 1),
(330, 2, 160, 'read', 1),
(329, 2, 159, 'delete', 1),
(327, 2, 159, 'update', 1),
(326, 2, 159, 'read', 1);
