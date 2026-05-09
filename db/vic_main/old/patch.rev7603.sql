START TRANSACTION;

INSERT INTO `vic_main`.`database_patch`(`revision`, `not_reapplicable`, `note`) VALUES
 ('7603', 1, 'New bet types/subtypes/columns');

INSERT INTO `vic_main`.`typ` (`typ_id`, `typ_alias_id`, `nazev`, `zobrazeno`, `poradi`, `offer_category_id`, `typ_alias_group`, `group_master`) VALUES
 ('164', '93', 'prodlouzeni', '1', '1', '2', NULL, 0),
 ('165', '94', 'zapas_ft', '1', '1', '2', NULL, 0);

INSERT INTO `vic_main`.`typ_sport`(`typ_id`, `sport_id`, `vychozi`, `poradi`, `vychozi_mobil`) VALUES
 (164, 1025, 0, 2008, 0),
 (165, 1006, 0, 2009, 0),
 (165, 1015, 0, 2009, 0),
 (165, 1026, 0, 2009, 0);

INSERT INTO `vic_main`.`typ_podtyp`(`typ_id`, `sport_id`, `podtyp_id`, `live_bet`) VALUES
 (164, 1025, 27, 0),
 (165, 1006, 23, 0),
 (165, 1015, 23, 0),
 (165, 1026, 23, 0);

COMMIT;
 