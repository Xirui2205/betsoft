DROP TRIGGER IF EXISTS `vic_main`.`sazka_kurz_delete_trg`;
DELIMITER $$
CREATE TRIGGER `vic_main`.`sazka_kurz_delete_trg`
   AFTER DELETE ON sazka_kurz
   FOR EACH ROW
BEGIN
   DECLARE slid INT(10) UNSIGNED;
   DECLARE sid INT(10) UNSIGNED;
   DECLARE pod DATETIME;
   DECLARE pr INT(10) UNSIGNED;
   DECLARE kr DECIMAL(10,2);
   DECLARE krz TINYINT(1);

   SELECT sazka_id, sloupec_id, platny_od,poradi,kurz,kurz_zmena INTO sid, slid, pod, pr, kr, krz
   FROM `vic_main`.`sazka_kurz`
   WHERE sazka_id = OLD.sazka_id AND sloupec_id = OLD.sloupec_id
   ORDER BY platny_od DESC
   LIMIT 1;
   
   IF ISNULL(sid) THEN
      DELETE FROM `vic_main`.`sazka_kurz_aktualni`
      WHERE sazka_id = OLD.sazka_id AND sloupec_id = OLD.sloupec_id;
   ELSE
      REPLACE INTO `vic_main`.`sazka_kurz_aktualni`
         (`sazka_id`,`sloupec_id`,`poradi`,`kurz`,`platny_od`,`kurz_zmena`)
      VALUES
         (sid,slid,pr,kr,pod,krz);
   END IF;

END$$
DELIMITER ;


DROP TRIGGER IF EXISTS `vic_main`.`sazka_kurz_update_trg`;
DELIMITER $$
CREATE TRIGGER `vic_main`.`sazka_kurz_update_trg`
   AFTER UPDATE ON sazka_kurz
   FOR EACH ROW
BEGIN
   DECLARE slid INT(10) UNSIGNED;
   DECLARE sid INT(10) UNSIGNED;
   DECLARE pod DATETIME;
   DECLARE pr INT(10) UNSIGNED;
   DECLARE kr DECIMAL(10,2);
   DECLARE krz TINYINT(1);

   SELECT sazka_id, sloupec_id, platny_od,poradi,kurz,kurz_zmena INTO sid, slid, pod, pr, kr, krz
   FROM `vic_main`.`sazka_kurz`
   WHERE sazka_id = NEW.sazka_id AND NEW.sloupec_id = NEW.sloupec_id
   ORDER BY platny_od DESC
   LIMIT 1;
   
   REPLACE INTO `vic_main`.`sazka_kurz_aktualni`
      (`sazka_id`,`sloupec_id`,`poradi`,`kurz`,`platny_od`,`kurz_zmena`)
   VALUES
      (sid,slid,pr,kr,pod,krz);
END$$
DELIMITER ;

DROP TRIGGER IF EXISTS `vic_main`.`sazka_kurz_insert_trg`;
DELIMITER $$
CREATE TRIGGER `vic_main`.`sazka_kurz_insert_trg`
   AFTER INSERT ON sazka_kurz
   FOR EACH ROW
BEGIN
   DECLARE slid INT(10) UNSIGNED;
   DECLARE sid INT(10) UNSIGNED;
   DECLARE pod DATETIME;
   DECLARE pr INT(10) UNSIGNED;
   DECLARE kr DECIMAL(10,2);
   DECLARE krz TINYINT(1);

   SELECT sazka_id, sloupec_id, platny_od,poradi,kurz,kurz_zmena INTO sid, slid, pod, pr, kr, krz
   FROM `vic_main`.`sazka_kurz`
   WHERE sazka_id = NEW.sazka_id AND sloupec_id = NEW.sloupec_id
   ORDER BY platny_od DESC
   LIMIT 1;
   
   REPLACE INTO `vic_main`.`sazka_kurz_aktualni`
      (`sazka_id`,`sloupec_id`,`poradi`,`kurz`,`platny_od`,`kurz_zmena`)
   VALUES
      (sid,slid,pr,kr,pod,krz);

END$$
DELIMITER ;
