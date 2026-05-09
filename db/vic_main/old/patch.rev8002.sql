INSERT INTO `vic_main`.`database_patch`(`revision`, `not_reapplicable`, `note`)
VALUES (8002, 1, 'adding oddType 218 where there is oddType 23');


CREATE TEMPORARY TABLE `vic_main`.`tmp_typ_podtyp` (
  `typ_id` int(10) unsigned NOT NULL DEFAULT '0',
  `sport_id` int(10) unsigned NOT NULL DEFAULT '0',
  `podtyp_id` int(10) unsigned NOT NULL DEFAULT '0',
  `live_bet` smallint(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`typ_id`,`sport_id`,`podtyp_id`)
);


INSERT INTO `vic_main`.`tmp_typ_podtyp` (`typ_id`,`sport_id`,`podtyp_id`,`live_bet`)
 SELECT `typ_id`,`sport_id`,218, 0 FROM `vic_main`.`typ_podtyp`
 WHERE podtyp_id=23;


REPLACE INTO `vic_main`.`typ_podtyp` (`typ_id`,`sport_id`,`podtyp_id`,`live_bet`)
 SELECT `typ_id`,`sport_id`,`podtyp_id`,`live_bet` FROM `vic_main`.`tmp_typ_podtyp`;
 
 



CREATE TEMPORARY TABLE `vic_main`.`tmp_bet_settings` (
  `udalost_id` int(10) unsigned NOT NULL DEFAULT '0',
  `typ_id` int(10) unsigned NOT NULL DEFAULT '0',
  `podtyp_id` int(10) unsigned NOT NULL DEFAULT '0',
  `kurz_min` decimal(6,2) NOT NULL DEFAULT '0.00',
  `kurz_max` decimal(6,2) NOT NULL DEFAULT '0.00',
  `vyhernost_min` decimal(3,2) NOT NULL DEFAULT '0.00',
  `vyhernost_max` decimal(3,2) NOT NULL DEFAULT '0.00',
  `sport_id` int(10) unsigned NOT NULL DEFAULT '0',
  `risk_limit` int(10) unsigned NOT NULL DEFAULT '3000',
  PRIMARY KEY (`udalost_id`,`typ_id`,`podtyp_id`)
);


INSERT INTO `vic_main`.`tmp_bet_settings` (`udalost_id`,`typ_id`,`podtyp_id`,`kurz_min`,`kurz_max`,`vyhernost_min`,`vyhernost_max`,`sport_id`,`risk_limit`)
 SELECT bs1.udalost_id, bs1.typ_id, 218, bs1.kurz_min, bs1.kurz_max, bs1.vyhernost_min, bs1.vyhernost_max, bs1.sport_id, bs1.risk_limit
 FROM `vic_main`.`bet_settings` bs1
 LEFT JOIN bet_settings bs ON bs.udalost_id=bs1.udalost_id AND bs.typ_id=bs1.typ_id AND bs.podtyp_id=218
 WHERE bs.udalost_id IS NULL AND bs1.podtyp_id=23;


INSERT INTO `vic_main`.`bet_settings` (`udalost_id`,`typ_id`,`podtyp_id`,`kurz_min`,`kurz_max`,`vyhernost_min`,`vyhernost_max`,`sport_id`,`risk_limit`)
 SELECT `udalost_id`,`typ_id`, `podtyp_id`, `kurz_min`,`kurz_max`,`vyhernost_min`,`vyhernost_max`,`sport_id`,`risk_limit` FROM `vic_main`.`tmp_bet_settings`;











