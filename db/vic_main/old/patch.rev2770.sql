ALTER TABLE `vic_main`.`typ` ADD `typ_alias_id` INT( 10 ) NOT NULL AFTER `typ_id`;
UPDATE `vic_main`.`typ` set `typ_alias_id`=typ_id-15;

DROP VIEW `vic_main`.`sazka_pohled`;
CREATE VIEW `vic_main`.`sazka_pohled` AS select `a`.`sazka_id` AS `sazka_id`,`a`.`proplatil_bookmaker` AS `proplatil_bookmaker`,`a`.`proplacena` AS `proplacena`,`a`.`info` AS `info`,`a`.`betradar_sazka_id` AS `betradar_sazka_id`,`a`.`platna_od` AS `platna_od`,`a`.`platna_do` AS `platna_do`,`a`.`status` AS `status`,`a`.`live` AS `live`,`a`.`bookmaker_id` AS `bookmaker_id`,`a`.`vysledek` AS `vysledek`,`a`.`udalost_id` AS `udalost_id`,`a`.`typ_id` AS `typ_id`,`t`.`typ_alias_id` AS `typ_alias_id`,`a`.`podtyp_id` AS `podtyp_id`,`a`.`overena` AS `overena`,`a`.`text` AS `text`,`a`.`ako` AS `ako`,`a`.`jednoducha` AS `jednoducha`,`a`.`risk_limit` AS `risk_limit`,`a`.`risk_limit_balance` AS `risk_limit_balance`,`b`.`poradi` AS `poradi`,`b`.`kurz` AS `kurz`,`b`.`kurz_zmena` AS `kurz_zmena`,`b`.`platny_od` AS `platny_od`,`c`.`nazev` AS `nazev`,`c`.`sloupec_id` AS `sloupec_id`,`c`.`poradi` AS `poradi_sloupec`,`a`.`betradar_autoupdate` AS `betradar_autoupdate`,`a`.`score` AS `score`,`a`.`parent_id` AS `parent_id`,`a`.`alias` AS `alias`,`a`.`alias_released` AS `alias_released`
from (`sazky` `a`
	join (`vic_main`.`sazka_kurz` `b`
		join `vic_main`.`podtyp_sloupce` `c` on ((`b`.`sloupec_id` = `c`.`sloupec_id`))) on ((`a`.`sazka_id` = `b`.`sazka_id`))
	join typ t on (t.typ_id = a.typ_id)
);

ALTER TABLE `vic_main`.`sazky` ADD `betradar_match_id` INT( 10 ) UNSIGNED NULL AFTER `betradar_sazka_id` ;
