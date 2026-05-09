INSERT INTO `vic_main`.`database_patch`(`revision`, `not_reapplicable`,
`note`)
VALUES ('7315', null, 'Fixing automatic subtype names from betradar');

UPDATE `vic_main`.`podtyp` SET `interni_nazev` = 'Varianta V' WHERE `podtyp`.`podtyp_id` = 94;
UPDATE `vic_main`.`podtyp` SET `interni_nazev` = 'Varianta W' WHERE `podtyp`.`podtyp_id` = 98;
UPDATE `vic_main`.`podtyp` SET `interni_nazev` = 'Varianta X' WHERE `podtyp`.`podtyp_id` = 99;
UPDATE `vic_main`.`podtyp` SET `interni_nazev` = 'Varianta Y' WHERE `podtyp`.`podtyp_id` = 101;
UPDATE `vic_main`.`podtyp` SET `interni_nazev` = 'Varianta Z' WHERE `podtyp`.`podtyp_id` = 102;
UPDATE `vic_main`.`podtyp` SET `interni_nazev` = 'Varianta AA' WHERE `podtyp`.`podtyp_id` = 103;
UPDATE `vic_main`.`podtyp` SET `interni_nazev` = 'Varianta AB' WHERE `podtyp`.`podtyp_id` = 104;
UPDATE `vic_main`.`podtyp` SET `interni_nazev` = 'Varianta AC' WHERE `podtyp`.`podtyp_id` = 120;
UPDATE `vic_main`.`podtyp` SET `interni_nazev` = 'Varianta AD' WHERE `podtyp`.`podtyp_id` = 122;
UPDATE `vic_main`.`podtyp` SET `interni_nazev` = 'Varianta AE' WHERE `podtyp`.`podtyp_id` = 123;
UPDATE `vic_main`.`podtyp` SET `interni_nazev` = 'Varianta AF' WHERE `podtyp`.`podtyp_id` = 124;
UPDATE `vic_main`.`podtyp` SET `interni_nazev` = 'Varianta AG' WHERE `podtyp`.`podtyp_id` = 125;
UPDATE `vic_main`.`podtyp` SET `interni_nazev` = 'Varianta AH' WHERE `podtyp`.`podtyp_id` = 126;
UPDATE `vic_main`.`podtyp` SET `interni_nazev` = 'Varianta AI' WHERE `podtyp`.`podtyp_id` = 127;
