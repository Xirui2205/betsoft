INSERT INTO `vic_main`.`database_patch`(`revision`, `not_reapplicable`, `note`)
VALUES ('H2097', 1, 'upravena mapa pobocek (mantis:1146)');

INSERT INTO `vic_main`.`preklady` (
`lang_id` ,
`index_pole` ,
`short_text` ,
`text` ,
`translate` ,
`preklad_id`
)
VALUES (
'1', 'pick_town', NULL , 'Město', '1', ''
);
UPDATE `vic_main`.`preklady` SET `text` = 'Zvolte město' WHERE `preklady`.`lang_id` =1 AND `preklady`.`index_pole` = 'select_town';