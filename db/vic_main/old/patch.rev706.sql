DROP VIEW IF EXISTS `ticket_pohled_connect`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `ticket_pohled_connect` AS
select
 `a`.`sazka_id` AS `sazka_id`,
 `a`.`live` AS `live`,
 `a`.`platna_do` AS `platna_do`,
 `d`.`stats` AS `stats`,
 `d`.`stats_user` AS `stats_user`,
 `d`.`user_id` AS `user_id`,
 `a`.`proplacena` AS `proplacena`,
 `a`.`typ_id` AS `typ_id`,
 `a`.`text` AS `text`,
 `a`.`vysledek` AS `vysledek`,
 `a`.`status` AS `status`,
 `a`.`udalost_id` AS `udalost_id`,
 `b`.`kurz` AS `kurz`,
 `b`.`platny_od` AS `platny_od`,
 `b`.`kurz_zmena` AS `kurz_zmena`,
 `b`.`sloupec_id` AS `sloupec_id`,
 `b`.`poradi` AS `poradi`,
 `c`.`ticket_sazka_zrusena` AS `ticket_sazka_zrusena`,
 `c`.`ticket_sazka_zrusil_bookmaker_id` AS `ticket_sazka_zrusil_bookmaker_id`,
 `c`.`ticket_sazka_duvod_zruseni` AS `ticket_sazka_duvod_zruseni`,
 `c`.`banker` AS `banker`,
 `c`.`group_id`,
 `d`.`ticket_id` AS `ticket_id`,
 `d`.`type` AS `type`,
 `d`.`win` AS `win`,
 `d`.`rate` AS `rate`,
 `d`.`win_real` AS `win_real`,
 `d`.`rate_real` AS `rate_real`,
 `d`.`free_bet_bonus` AS `free_bet_bonus`,
 `d`.`zalozen` AS `zalozen`,
 `d`.`vyplacen` AS `vyplacen`,
 `d`.`zruseno` AS `zruseno`,
 `d`.`zrusil_bookmaker_id` AS `zrusil_bookmaker_id`,
 `d`.`duvod_zruseni` AS `duvod_zruseni`,
 `d`.`castka` AS `castka`,
 `d`.`system` AS `system`,
 `d`.`cupon_id` AS `cupon_id`,
 `d`.`mail` AS `mail`,
 `d`.`group_count`,
 `d`.`group_t`
from (
 `sazky` `a`
 join (
 `sazka_kurz` `b`
 join (
 `ticket_kurz` `c` 
 join `ticket` `d`
 on (`c`.`ticket_id` = `d`.`ticket_id`)
 )
 on ((`b`.`sazka_id` = `c`.`sazka_id`) and (`b`.`sloupec_id` = `c`.`sloupec_id`))
 )
 on (`a`.`sazka_id` = `b`.`sazka_id`)
);
DROP VIEW IF EXISTS `ticket_pohled`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `ticket_pohled` AS
select
 `b`.`sazka_id` AS `sazka_id`,
 `b`.`live` AS `live`,
 `b`.`platna_do` AS `platna_do`,
 `b`.`user_id` AS `user_id`,
 `b`.`proplacena` AS `proplacena`,
 `b`.`typ_id` AS `typ_id`,
 `b`.`text` AS `text`,
 `b`.`vysledek` AS `vysledek`,
 `b`.`status` AS `status`,
 `b`.`udalost_id` AS `udalost_id`,
 `b`.`kurz` AS `kurz`,
 `b`.`platny_od` AS `platny_od`,
 `b`.`sloupec_id` AS `sloupec_id`,
 `b`.`poradi` AS `poradi`,
 `b`.`ticket_sazka_zrusena` AS `ticket_sazka_zrusena`,
 `b`.`ticket_sazka_zrusil_bookmaker_id` AS `ticket_sazka_zrusil_bookmaker_id`,
 `b`.`ticket_sazka_duvod_zruseni` AS `ticket_sazka_duvod_zruseni`,
 `b`.`ticket_id` AS `ticket_id`,
 `b`.`zalozen` AS `zalozen`,
 `b`.`vyplacen` AS `vyplacen`,
 `b`.`type` AS `type`,
 `b`.`win` AS `win`,
 `b`.`rate` AS `rate`,
 `b`.`win_real` AS `win_real`,
 `b`.`rate_real` AS `rate_real`,
 `b`.`zruseno` AS `zruseno`,
 `b`.`kurz_zmena` AS `kurz_zmena`,
 `b`.`zrusil_bookmaker_id` AS `zrusil_bookmaker_id`,
 `b`.`duvod_zruseni` AS `duvod_zruseni`,
 `b`.`castka` AS `castka`,
 `b`.`free_bet_bonus` AS `free_bet_bonus`,
 `b`.`system` AS `system`,
 `b`.`stats` AS `stats`,
 `b`.`stats_user` AS `stats_user`,
 `b`.`banker` AS `banker`,
 `b`.`cupon_id` AS `cupon_id`,
 `b`.`mail` AS `mail`,
 `b`.`group_count`,
 `b`.`group_t`,
 `b`.`group_id`
from `ticket_pohled_connect` `b`
where (
 `b`.`platny_od` = (
 select max(`a`.`platny_od`) AS `maxi_platny_od` from `ticket_pohled_connect` `a`
 where ((`a`.`zalozen` >= `a`.`platny_od`) and (`a`.`sazka_id` = `b`.`sazka_id`) and (`a`.`ticket_id` = `b`.`ticket_id`))
 group by `a`.`ticket_id`,`a`.`sazka_id`
 )
);


CREATE TABLE `vic_main`.`ticket_combination` (
`k` INT( 10 ) UNSIGNED NOT NULL COMMENT 'pozadovany pocet _k_ vyher v kombinaci _n_ nad _k_',
`ticket_id` BIGINT( 20 ) UNSIGNED NOT NULL ,
`stake` DECIMAL( 10, 2 ) NOT NULL ,
PRIMARY KEY ( `ticket_id` , `k` ),
KEY  ( `ticket_id` ),
FOREIGN KEY ( `ticket_id` ) REFERENCES `vic_main`.`ticket` ( `ticket_id` )
) ENGINE = INNODB CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT = 'data pro kombinace skupin maxikombinatoru';

ALTER TABLE `ticket_kurz` ADD `group_id` INT( 10 ) UNSIGNED NULL;
ALTER TABLE `ticket` ADD `group_count` INT( 10 ) UNSIGNED NULL DEFAULT NULL;
ALTER TABLE `ticket` ADD `group_t` BOOLEAN NULL DEFAULT '0';
