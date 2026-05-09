START TRANSACTION;

UPDATE `vic_admin`.`sekce` SET `nazev` = 'Manual deposit',`action` = 'manual-deposit' WHERE `sekce`.`sekce_id` =226;

INSERT INTO `sekce` (`sekce_id`, `parent_id`, `nazev`, `zobrazeno`, `poradi`, `controller`, `action`) VALUES
(227, 58, 'Manual withdraw', 1, 0, 'finance', 'manual-withdraw');

INSERT INTO `sekce` (`sekce_id`, `parent_id`, `nazev`, `zobrazeno`, `poradi`, `controller`, `action`) VALUES
(228, 58, 'Manual balance', 1, 0, 'finance', 'manual-balance');

INSERT INTO `sekce` (`sekce_id`, `parent_id`, `nazev`, `zobrazeno`, `poradi`, `controller`, `action`) VALUES
(229, 58, 'Manual freebet', 1, 0, 'finance', 'manual-freebet');


INSERT INTO `role_resource_privilege` (`id`, `role_id`, `section_id`, `privilege_name`, `privilege_set`) VALUES
(NULL, 2, 227, 'read', 1),
(NULL, 2, 228, 'read', 1),
(NULL, 2, 229, 'read', 1);

COMMIT;
