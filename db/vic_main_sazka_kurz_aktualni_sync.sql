TRUNCATE `vic_main`.`sazka_kurz_aktualni`;
INSERT INTO `vic_main`.`sazka_kurz_aktualni`(`sazka_id`,`sloupec_id`,`poradi`,`kurz`,`platny_od`,`kurz_zmena`)
SELECT k1.`sazka_id`,k1.`sloupec_id`,k1.`poradi`,k1.`kurz`,k1.`platny_od`,k1.`kurz_zmena`
   FROM `vic_main`.`sazka_kurz` k1
   JOIN (
      SELECT `sazka_id`,`sloupec_id`, MAX(`platny_od`) AS `platny_od_max` FROM `vic_main`.`sazka_kurz` 
      GROUP BY `sazka_id`,`sloupec_id`
   ) k2
   ON k1.`sazka_id`=k2.`sazka_id` AND k1.`sloupec_id`=k2.`sloupec_id` AND k1.`platny_od`=k2.`platny_od_max`;
