START TRANSACTION;

INSERT INTO `sekce` (`sekce_id`, `acl_resource_id`, `nazev`, `zobrazeno`, `controller`, `action`) VALUES
(340, 1371, 'view client card', 0, 'client-card', 'view'),
(341, 1371, 'unblock client card', 0, 'client-card', 'unblock'),
(342, 1371, 'block more client card', 0, 'client-card', 'block-more-cards'),
(343, 1371, 'unblock more client card', 0, 'client-card', 'unblock-more-cards');

commit;
