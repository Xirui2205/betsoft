INSERT INTO `vic_main`.`database_patch`(`revision`, `not_reapplicable`,
`note`)
VALUES ('7320', null, 'Fixing automatic subtype names from betradar');

SET NAMES utf8;

UPDATE vic_main.podtyp SET interni_nazev="Střelci" WHERE interni_nazev='1';
