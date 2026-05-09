DELIMITER ;;
CREATE DEFINER=CURRENT_USER TRIGGER `t_preklady_after_insert` BEFORE INSERT ON `vic_main`.`preklady`
FOR EACH ROW
BEGIN
 DECLARE id INT(11) UNSIGNED;
 IF NOT(NEW.`preklad_id`>0) THEN
  SELECT COALESCE(MAX(`preklad_id`),0) INTO id FROM `preklady` WHERE `index_pole`=NEW.`index_pole`;
  IF id=0 THEN
   SELECT COALESCE(MAX(`preklad_id`), 0)+1 INTO id FROM `preklady`;
  END IF;
  SET NEW.`preklad_id`=id;
 END IF;
END;;
DELIMITER ;
