START TRANSACTION;

-- vlozit zaznam do database_patch
INSERT INTO `vic_main`.`database_patch`(`revision`, `not_reapplicable`, `note`)
VALUES ('H4421', 1, 'Articles - paging (mantis:158)');

-- parametr - pocet clanku v archivu na strance
INSERT INTO `vic_admin`.`parameter` (
`id` ,
`name` ,
`value` ,
`is_host` ,
`is_branch` ,
`is_user` ,
`type` ,
`mandatory` ,
`is_admin`,
`description`
)
VALUES
(NULL , 'web.articlesArchivePageCount', '12', '0', '0', '0', NULL , '1', '0', 'Počet článků na stránce v archivu článků.');

COMMIT;