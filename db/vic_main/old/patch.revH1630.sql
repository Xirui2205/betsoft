START TRANSACTION;

INSERT INTO `vic_main`.`database_patch`(`revision`, `not_reapplicable`, `note`)
VALUES ('H1630', 1, 'translations for (mantis:1056)');

SET NAMES utf8;

INSERT INTO `vic_main`.`preklady` (
`lang_id` ,
`index_pole` ,
`short_text` ,
`text` ,
`translate` ,
`preklad_id`
)
VALUES (
'1', 'stem-name', NULL , 'Název kmene', '1', ''
), (
'1', 'stem-admin', NULL , 'Vedoucí kmene', '1', ''
), (
'1', 'stem-desc', NULL , 'Popis kmenu', '1', ''
), (
'1', 'add_stem', NULL , 'Přidat kmen', '1', ''
);

COMMIT;
