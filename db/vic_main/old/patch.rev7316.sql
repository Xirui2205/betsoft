INSERT INTO `vic_main`.`database_patch`(`revision`, `not_reapplicable`,
`note`) VALUES
 ('7316', null, 'Internal name for subtype changed');

SET NAMES utf8;
UPDATE vic_main.podtyp SET interni_nazev='Přesný výsledek' WHERE podtyp_id IN (SELECT podtyp_id FROM vic_main.typ_podtyp WHERE typ_id=23);
