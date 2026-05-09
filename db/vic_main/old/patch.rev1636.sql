-- pridani sloupce `vic_main`.`ticket`.`cash` a jeho vyber v pohledech `vic_main`.`ticket_pohled` a `vic_main`.`ticket_pohled_connect`

ALTER TABLE `vic_main`.`ticket` ADD `cash` BOOLEAN NOT NULL;

DROP VIEW `vic_main`.`ticket_pohled`;
DROP VIEW `vic_main`.`ticket_pohled_connect`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vic_main`.`ticket_pohled_connect` AS SELECT
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
`c`.`group_id` AS `group_id`,
`d`.`ticket_id` AS `ticket_id`,
`d`.`handle` AS `ticket_handle`,
`d`.`branch_id` AS `ticket_branch_id`,
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
`d`.`group_count` AS `group_count`,
`d`.`group_t` AS `group_t`,
`d`.`vyplacen_date` AS `vyplacen_date`,
`d`.`vyplacen_bookmaker_id` AS `vyplacen_bookmaker_id`,
`d`.`cash` AS `cash`
FROM (`sazky` `a`
 JOIN (`sazka_kurz` `b`
  JOIN (`ticket_kurz` `c`
   JOIN `ticket` `d`
   ON `c`.`ticket_id`=`d`.`ticket_id`
  ) ON `b`.`sazka_id`=`c`.`sazka_id` AND `b`.`sloupec_id`=`c`.`sloupec_id`
 ) ON `a`.`sazka_id`=`b`.`sazka_id`
);

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vic_main`.`ticket_pohled` AS SELECT
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
`b`.`ticket_handle` AS `ticket_handle`,
`b`.`ticket_branch_id` AS `ticket_branch_id`,
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
`b`.`group_count` AS `group_count`,
`b`.`group_t` AS `group_t`,
`b`.`group_id` AS `group_id`,
`b`.`vyplacen_date` AS `vyplacen_date`,
`b`.`vyplacen_bookmaker_id` AS `vyplacen_bookmaker_id`,
`b`.`cash` AS `cash`
FROM `ticket_pohled_connect` `b`
WHERE `b`.`platny_od` = (
 SELECT MAX(`a`.`platny_od`) AS `maxi_platny_od`
 FROM `ticket_pohled_connect` `a`
 WHERE (`a`.`zalozen` >= `a`.`platny_od`) AND (`a`.`sazka_id` = `b`.`sazka_id`) AND (`a`.`ticket_id` = `b`.`ticket_id`)
 GROUP BY `a`.`ticket_id`,`a`.`sazka_id`
);
