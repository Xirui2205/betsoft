-- nejsem si jisty, jestli primary key ve vic_main.tymy nema jen sloupec betradar_tym_id, ale predpokladam, ze ano
CREATE TABLE IF NOT EXISTS `tymy` (
  `betradar_tym_id` int(10) unsigned NOT NULL,
  `udalost_id` int(10) unsigned NOT NULL,
  `name` varchar(128) NOT NULL,
  PRIMARY KEY (`betradar_tym_id`,`udalost_id`),
  FOREIGN KEY (`udalost_id`) REFERENCES `udalost` (`udalost_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE 'utf8_general_ci';

CREATE TABLE IF NOT EXISTS `tymy_sazky` (
  `betradar_tym_id` int(10) unsigned NOT NULL,
  `sazka_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`betradar_tym_id`,`sazka_id`),
  FOREIGN KEY (`sazka_id`) REFERENCES `sazky` (`sazka_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE 'utf8_general_ci';
