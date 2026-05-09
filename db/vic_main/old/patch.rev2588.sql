
CREATE TABLE sazka_kurz_aktualni AS
SELECT SK.`sazka_id` AS `sazka_id`,SK.`sloupec_id` AS `sloupec_id`, MAX(`poradi`) as `poradi` ,(SELECT `kurz`
   FROM `sazka_kurz` AS SK2
   WHERE SK.`sazka_id` = SK2.`sazka_id` AND SK.`sloupec_id` = SK2.`sloupec_id`
   ORDER BY `platny_od` DESC
   LIMIT 1) AS `kurz`,
 MAX(`platny_od`) as `platny_od`,
(SELECT `kurz_zmena`
   FROM `sazka_kurz` AS SK2
   WHERE SK.`sazka_id` = SK2.`sazka_id` AND SK.`sloupec_id` = SK2.`sloupec_id`
   ORDER BY `platny_od` DESC
   LIMIT 1) AS `kurz_zmena`
FROM sazka_kurz AS SK
GROUP BY `sazka_id`,`sloupec_id`;

ALTER TABLE `sazka_kurz_aktualni` ADD PRIMARY KEY ( `sazka_id` , `sloupec_id` ) ;


CREATE TRIGGER sazka_kurz_insert_trg
   AFTER INSERT ON sazka_kurz
   FOR EACH ROW

   REPLACE INTO sazka_kurz_aktualni
         (`sazka_id`,`sloupec_id`,`poradi`,`kurz`,`platny_od`,`kurz_zmena`)
      VALUES
         (NEW.`sazka_id`,NEW.`sloupec_id`,NEW.`poradi`,NEW.`kurz`,NEW.`platny_od`,NEW.`kurz_zmena`);


CREATE TRIGGER sazka_kurz_update_trg
   AFTER UPDATE ON sazka_kurz
   FOR EACH ROW

   UPDATE sazka_kurz_aktualni SET
        `sazka_id` = NEW.`sazka_id`,
        `sloupec_id` = NEW.`sloupec_id`,
        `poradi` = NEW.`poradi`,
        `kurz` = NEW.`kurz`,
        `platny_od` = NEW.`platny_od`,
        `kurz_zmena` = NEW.`kurz_zmena`
      WHERE `sazka_id` = NEW.`sazka_id`
         AND  `sloupec_id` = NEW.`sloupec_id`
         AND `platny_od` = NEW.`platny_od`;
         
CREATE TRIGGER sazka_kurz_delete_trg
   AFTER DELETE ON sazka_kurz
   FOR EACH ROW

   DELETE FROM sazka_kurz_aktualni
      WHERE `sazka_id` = OLD.`sazka_id`
         AND  `sloupec_id` = OLD.`sloupec_id`
         AND `platny_od` = OLD.`platny_od`
      

