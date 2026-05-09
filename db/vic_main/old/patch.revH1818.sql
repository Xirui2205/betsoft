START TRANSACTION;

INSERT INTO `vic_main`.`database_patch`(`revision`, `not_reapplicable`, `note`) VALUES
 ('H1818', 1, 'New translations for Node control centre (mantis:1032)');

INSERT INTO `vic_main`.`preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`) VALUES
(1, 'node_control_centre', NULL, 'Ovládací centrum nodů', 0);

INSERT INTO `vic_main`.`preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`) VALUES
(1, 'this_node_name', NULL, 'Jméno tohoto nodu', 0);

INSERT INTO `vic_main`.`preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`) VALUES
(1, 'nodecomm-cmd-ping', NULL, 'Ping', 0);
INSERT INTO `vic_main`.`preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`) VALUES
(1, 'nodecomm-cmd-ping-legend', NULL, 'Jednoduchý test dostupnosti ostatních nodů ve skupině. ', 0);

INSERT INTO `vic_main`.`preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`) VALUES
(1, 'nodecomm-cmd-identify', NULL, 'Identifikace nodů', 0);
INSERT INTO `vic_main`.`preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`) VALUES
(1, 'nodecomm-cmd-identify-legend', NULL, 'Zjištění jmen nodů ve skupině. ', 0);

INSERT INTO `vic_main`.`preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`) VALUES
(1, 'nodecomm-cmd-trans', NULL, 'Přegenerování překladů', 0);
INSERT INTO `vic_main`.`preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`) VALUES
(1, 'nodecomm-cmd-trans-legend', NULL, 'Regeneruje slovníkové soubory na tomto nodu i na ostatních nodech ve skupině.<br />\nCache s obsahem stránek apod. není mazána.', 0);

INSERT INTO `vic_main`.`preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`) VALUES
(1, 'nodecomm-cmd-memc-flush', NULL, 'Vymazání společné cache', 0);
INSERT INTO `vic_main`.`preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`) VALUES
(1, 'nodecomm-cmd-memc-flush-legend', NULL, 'Kompletně vymaže cache společnou pro všechny nody.<br />\nPozor, pokud na jednom Memcached serveru běží více aplikačních prostředí, bude vymazána cache všech (měl by to být případ pouze testovacích prostředí a nemělo by to způsobovat problémy). ', 0);

INSERT INTO `vic_main`.`preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`) VALUES
(1, 'nodecomm-cmd-apc-flush', NULL, 'Vymazání lokální cache nodů', 0);
INSERT INTO `vic_main`.`preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`) VALUES
(1, 'nodecomm-cmd-apc-flush-legend', NULL, 'Vymaže obsah APC na tomto nodu a na všech ostatních nodech ve skupině.', 0);

INSERT INTO `vic_main`.`preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`) VALUES
(1, 'nodecomm-cmd-invalidate-wc', NULL, 'Smazání cache všech stránek', 0);
INSERT INTO `vic_main`.`preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`) VALUES
(1, 'nodecomm-cmd-invalidate-wc-legend', NULL, 'Vymaže všechen nacacheovaný obsah webu.', 0);

INSERT INTO `vic_main`.`preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`) VALUES
(1, 'nodecomm-cmd-invalidate-wp', NULL, 'Smazání cache konkrétní stránky', 0);
INSERT INTO `vic_main`.`preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`) VALUES
(1, 'nodecomm-cmd-invalidate-wp-legend', NULL, 'Vymaže cache jedné nebo více zvolených webových stránek. ', 0);
INSERT INTO `vic_main`.`preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`) VALUES
(1, 'nodecomm-cmd-invalidate-wp-count', NULL, 'Počet obnovených stránek: {0}', 0);

INSERT INTO `vic_main`.`preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`) VALUES
(1, 'nodecomm-cmd-invalidate-acl', NULL, 'Smazání cache ACL', 0);
INSERT INTO `vic_main`.`preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`) VALUES
(1, 'nodecomm-cmd-invalidate-acl-legend', NULL, 'Vymaže cache ACL (kontrola oprávnění). ', 0);

COMMIT;
