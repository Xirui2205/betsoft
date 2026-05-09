DROP TRIGGER `sazka_kurz_update_trg`;
DELIMITER ;;
CREATE TRIGGER `sazka_kurz_update_trg` AFTER UPDATE ON `sazka_kurz` FOR EACH ROW
BEGIN
 DECLARE slid INT(10) UNSIGNED;
 DECLARE sid INT(10) UNSIGNED;
 DECLARE pod DATETIME;
 DECLARE pr INT(10) UNSIGNED;
 DECLARE kr DECIMAL(10,2);
 DECLARE krz TINYINT(1);

 SELECT sazka_id, sloupec_id, platny_od, poradi, kurz, kurz_zmena
  INTO sid, slid, pod, pr, kr, krz
  FROM `vic_main`.`sazka_kurz`
  WHERE `sazka_id`=NEW.`sazka_id` AND `sloupec_id`=NEW.`sloupec_id`
  ORDER BY `platny_od` DESC
  LIMIT 1;

 REPLACE INTO `vic_main`.`sazka_kurz_aktualni`
  (`sazka_id`,`sloupec_id`,`poradi`,`kurz`,`platny_od`,`kurz_zmena`)
 VALUES
  (sid,slid,pr,kr,pod,krz);
END;;
