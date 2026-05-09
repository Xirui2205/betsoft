START TRANSACTION;

-- vlozit zaznam do database_patch
INSERT INTO `vic_admin`.`database_patch`(`revision`, `not_reapplicable`, `note`)
VALUES ('H4152', 1, 'Upravy v live calendar small (mantis:84)');

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
(NULL , 'web.liveCalendarSmallCommingToOnlineCount', '30', '0', '0', '0', NULL , '1', '0', 'Pokud je počet záznamů v záložce "Právě se hraje" menší než tento parametr, tak se do tohoto počtu přidají záznamy ze záložky "Připravujeme".');

COMMIT;