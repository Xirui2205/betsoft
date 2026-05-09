START TRANSACTION;

INSERT INTO `vic_main`.`database_patch`(`revision`, `not_reapplicable`, `note`) VALUES
 ('7524', 1, 'New bet types/subtypes/columns');

INSERT INTO `vic_main`.`podtyp`(`podtyp_id`, `radek_sloupec`, `sloupec_pocet_max`, `text`, `interni_nazev`) VALUES
 (217, 0, 0, '', 'X/1/2/12');

INSERT INTO `vic_main`.`typ_podtyp`(`typ_id`, `sport_id`, `podtyp_id`, `live_bet`) VALUES
 (116, 1001, 217, 0),
 (116, 1011, 217, 0);

INSERT INTO `vic_main`.`podtyp_sloupce`(`sloupec_id`, `podtyp_id`, `nazev`, `poradi`) VALUES
 (1853, 217, 'X', 1),
 (1854, 217, '1', 1),
 (1855, 217, '2', 1),
 (1856, 217, '12', 1);

INSERT INTO `vic_main`.`typ` (`typ_id`, `typ_alias_id`, `nazev`, `zobrazeno`, `poradi`, `offer_category_id`, `typ_alias_group`, `group_master`) VALUES
 ('157', '86', '2.polocas_vice-mene', '1', '1', '2', '2.polocas_vice-mene', '1'),
 ('158', '87', '2.polocas_vice-mene_b', '0', '1', '2', '2.polocas_vice-mene', '0'),
 ('159', '88', '2.polocas_vice-mene_c', '0', '1', '2', '2.polocas_vice-mene', '0'),
 ('160', '89', '1.tretina_dvojita-sance', '1', '1', '2', NULL, 0),
 ('161', '90', '2.tretina_dvojita-sance', '1', '1', '2', NULL, 0),
 ('162', '91', '3.tretina_dvojita-sance', '1', '1', '2', NULL, 0);

INSERT INTO `vic_main`.`typ_sport`(`typ_id`, `sport_id`, `vychozi`, `poradi`, `vychozi_mobil`) VALUES
 (157, 1001, 0, 2001, 0),
 (158, 1001, 0, 2002, 0),
 (159, 1001, 0, 2003, 0),
 (160, 1003, 0, 2004, 0),
 (161, 1003, 0, 2005, 0),
 (162, 1003, 0, 2006, 0);

INSERT INTO `vic_main`.`typ_podtyp`(`typ_id`, `sport_id`, `podtyp_id`, `live_bet`) VALUES
 (157, 1001, 25, 0),
 (158, 1001, 25, 0),
 (159, 1001, 25, 0),
 (160, 1003, 24, 0),
 (161, 1003, 24, 0),
 (162, 1003, 24, 0);

COMMIT;
