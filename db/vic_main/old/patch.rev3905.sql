DROP VIEW `vic_main`.`ticket_pohled`;
DROP VIEW `vic_main`.`ticket_pohled_connect`;

CREATE VIEW `vic_main`.`ticket_pohled_connect` AS
SELECT
 `a`.`sazka_id` AS `sazka_id`,
 `a`.`live` AS `live`,
 `a`.`platna_do` AS `platna_do`,
 `d`.`stats` AS `stats`,
 `d`.`stats_user` AS `stats_user`,
 `d`.`user_id` AS `user_id`,
 `a`.`proplacena` AS `proplacena`,
 `a`.`proplatil_bookmaker` AS `sazka_proplatil_bookmaker_id`,
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
 `d`.`host_id` AS `ticket_branch_id`,
 `d`.`type` AS `type`,
 `d`.`win` AS `win`,
 `d`.`rate` AS `rate`,
 `d`.`win_real` AS `win_real`,
 `d`.`rate_real` AS `rate_real`,
 `d`.`free_bet_bonus` AS `free_bet_bonus`,
 `d`.`zalozen` AS `zalozen`,
 `d`.`vyplacen` AS `vyplacen`,
 `d`.`zruseno` AS `zruseno`,
 `d`.`is_loss` AS `is_loss`,
 `d`.`zrusil_bookmaker_id` AS `zrusil_bookmaker_id`,
 `d`.`duvod_zruseni` AS `duvod_zruseni`,
 `d`.`castka` AS `castka`,
 `d`.`system` AS `system`,
 `d`.`coupon_id` AS `coupon_id`,
 `d`.`mail` AS `mail`,
 `d`.`group_count` AS `group_count`,
 `d`.`group_t` AS `group_t`,
 `d`.`vyplacen_date` AS `vyplacen_date`,
 `d`.`vyplacen_bookmaker_id` AS `vyplacen_bookmaker_id`,
 `d`.`collection_time` AS `collection_time`,
 `d`.`collection_host_id` AS `collection_host_id`,
 `d`.`forced_collection_host_id` AS `forced_collection_host_id`,
 `d`.`cash` AS `cash`,
 `d`.`point_type_id` AS `point_type_id`,
 `d`.`rate_advance` AS `rate_advance`,
 `d`.`forfeit` AS `forfeit`,
 `d`.`mp` AS `mp`,
 `d`.`mp_win` AS `mp_win`,
 `d`.`cancel_allowed` AS `cancel_allowed`
FROM `vic_main`.`sazky` `a`
JOIN `vic_main`.`sazka_kurz` `b`
ON `a`.`sazka_id` = `b`.`sazka_id`
JOIN `vic_main`.`ticket_kurz` `c`
ON `b`.`sazka_id` = `c`.`sazka_id` and `b`.`sloupec_id` = `c`.`sloupec_id`
JOIN `vic_main`.`ticket` `d`
ON `c`.`ticket_id` = `d`.`ticket_id`;

CREATE VIEW `vic_main`.`ticket_pohled` AS
SELECT `b`.*
FROM `vic_main`.`ticket_pohled_connect` `b`
WHERE `b`.`platny_od` = (
 SELECT max(`a`.`platny_od`) AS `maxi_platny_od`
 FROM `ticket_pohled_connect` `a`
 WHERE (`a`.`zalozen` >= `a`.`platny_od`) AND (`a`.`sazka_id` = `b`.`sazka_id`)
  AND (`a`.`ticket_id` = `b`.`ticket_id`)
 GROUP BY `a`.`ticket_id`,`a`.`sazka_id`
);
