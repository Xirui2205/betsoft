START TRANSACTION;
INSERT INTO `sekce` (`sekce_id`, `parent_id`, `nazev`, `zobrazeno`, `poradi`, `controller`, `action`) VALUES
(211, 1, 'Listings', 1, 0, 'listings', 'day-book-accounting'),
(212, 211, 'Day book accounting', 1, 0, 'listings', 'day-book-accounting'),
(213, 211, 'Branch provisions accounting', 1, 0, 'listings', 'branch-provisions-accounting');

INSERT INTO `role_resource_privilege` (`id`, `role_id`, `section_id`, `privilege_name`, `privilege_set`) VALUES
(NULL, 2, 211, 'read', 1),
(NULL, 2, 212, 'read', 1),
(NULL, 2, 213, 'read', 1);
COMMIT;
