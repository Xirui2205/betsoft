START TRANSACTION;
INSERT INTO `sekce` (`sekce_id`, `parent_id`, `nazev`, `zobrazeno`, `poradi`, `controller`, `action`) VALUES
(214, 1, 'Team (NEW)', 1, 0, 'team', ''),
(215, 0, 'Team', 1, 0, 'team', 'insert'),
(216, 0, 'Team', 1, 0, 'team', 'update'),
(217, 0, 'Team', 1, 0, 'team', 'delete'),
(218, 1, 'Bet Handle Range (NEW)', 1, 0, 'bet-handle-range', ''),
(219, 0, 'Bet Handle Range', 0, 0, 'bet-handle-range', 'insert'),
(220, 0, 'Bet Handle Range', 0, 0, 'bet-handle-range', 'update'),
(221, 0, 'Bet Handle Range', 1, 0, 'bet-handle-range', 'delete');
INSERT INTO `role_resource_privilege` (`id`, `role_id`, `section_id`, `privilege_name`, `privilege_set`) VALUES
(NULL, 4, 214, 'read', 1),
(NULL, 4, 215, 'insert', 1),
(NULL, 4, 216, 'update', 1),
(NULL, 4, 218, 'read', 1),
(NULL, 4, 219, 'insert', 1),
(NULL, 4, 220, 'update', 1);
COMMIT;
