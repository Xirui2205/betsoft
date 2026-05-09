START TRANSACTION;

INSERT INTO `vic_main`.`database_patch`(`revision`, `not_reapplicable`, `note`) VALUES
 ('7829', 1, 'Fixed missing group subtypes (mantis:310)');


UPDATE `vic_main`.`typ` SET `typ_alias_id`=97 WHERE `typ_id`=164;
UPDATE `vic_main`.`typ` SET `typ_alias_id`=98 WHERE `typ_id`=165;

UPDATE `vic_main`.`typ` SET `typ_alias_group`='1.polocas_asijsky-handicap', `group_master`=1 WHERE `typ_id`=163;
UPDATE `vic_main`.`typ` SET `typ_alias_group`='vice-mene_hry', `group_master`=1 WHERE `typ_id`=45;
UPDATE `vic_main`.`typ` SET `typ_alias_group`='vice-mene_sety', `group_master`=1 WHERE `typ_id`=46;

INSERT INTO `vic_main`.`typ`(`typ_id`,`typ_alias_id`,`nazev`,`zobrazeno`,`poradi`,`offer_category_id`,`typ_alias_group`,`group_master`,`order_type`) VALUES
 (166, 93, '1.polocas_asijsky-handicap_b', 1, 1, 2, '1.polocas_asijsky-handicap', 0, NULL),
 (167, 94, '1.polocas_asijsky-handicap_c', 1, 1, 2, '1.polocas_asijsky-handicap', 0, NULL),
 (168, 95, 'vice-mene_hry_b', 1, 26, 2, 'vice-mene_hry', 0, NULL),
 (169, 96, 'vice-mene_sety_b', 1, 27, 2, 'vice-mene_sety', 0, NULL);

COMMIT;
