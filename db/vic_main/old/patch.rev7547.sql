START TRANSANCTION;

INSERT INTO `vic_main`.`database_patch`(`revision`, `not_reapplicable`, `note`) VALUES
 ('7547', 1, 'New bet type');

INSERT INTO `vic_main`.`typ` (`typ_id`, `typ_alias_id`, `nazev`, `zobrazeno`, `poradi`, `offer_category_id`, `typ_alias_group`, `group_master`) VALUES
 ('163', '92', '1.polocas_asijsky-handicap', '1', '1', '2', NULL, 0);

INSERT INTO `vic_main`.`typ_sport`(`typ_id`, `sport_id`, `vychozi`, `poradi`, `vychozi_mobil`) VALUES
 (163, 1006, 0, 2007, 0);

INSERT INTO `vic_main`.`typ_podtyp`(`typ_id`, `sport_id`, `podtyp_id`, `live_bet`) VALUES
 (163, 1006, 29, 0);

COMMIT;