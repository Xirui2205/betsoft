START TRANSACTION;

INSERT INTO `vic_main`.`database_patch`(`revision`, `not_reapplicable`, `note`) VALUES
 ('7732', 1, 'New bet subtype 1X2/1X12X2 (mantis:310)');

INSERT INTO `vic_main`.`podtyp`(`podtyp_id`, `radek_sloupec`, `sloupec_pocet_max`, `text`, `interni_nazev`) VALUES
(218, 0, 0, '', '1X2/1X12X2');

INSERT INTO `vic_main`.`podtyp_sloupce`(`sloupec_id`, `podtyp_id`, `nazev`, `poradi`) VALUES
(1857, 218, '1', 1),
(1858, 218, 'X', 2),
(1859, 218, '2', 3),
(1860, 218, '1X', 4),
(1861, 218, '12', 5),
(1862, 218, 'X2', 6);

INSERT INTO `vic_main`.`typ_podtyp`(`typ_id`, `sport_id`, `podtyp_id`, `live_bet`) VALUES
 (19, 1001, 218, 0),
 (19, 1011, 218, 0);

COMMIT;
