START TRANSACTION;

INSERT INTO `vic_main`.`database_patch`(`revision`, `not_reapplicable`, `note`) VALUES
 ('7458', 1, 'New bet subtypes for score in sets');

 INSERT INTO `vic_main`.`podtyp`(`podtyp_id`, `radek_sloupec`, `sloupec_pocet_max`, `text`, `interni_nazev`) VALUES
(210, 1, 4, '', 'Přesný výsledek 3 sety'),
(211, 1, 6, '', 'Přesný výsledek 5 setů');

INSERT INTO `vic_main`.`typ_podtyp`(`typ_id`, `sport_id`, `podtyp_id`, `live_bet`) VALUES
(23, 1003, 210, 0),
(23, 1003, 211, 0);

INSERT INTO `vic_main`.`podtyp_sloupce`(`sloupec_id`, `podtyp_id`, `nazev`, `poradi`) VALUES
(1828, 210, '2:0', 1),
(1829, 210, '2:1', 1),
(1830, 210, '1:2', 1),
(1831, 210, '0:2', 1),
(1832, 211, '3:0', 1),
(1833, 211, '3:1', 1),
(1834, 211, '3:2', 1),
(1835, 211, '2:3', 1),
(1836, 211, '1:3', 1),
(1837, 211, '0:3', 1);

INSERT INTO `vic_main`.`typ_sport_note`(`typ_id`, `sport_id`, `text_note`) VALUES
 (23, 1003, 'sety');

COMMIT;
