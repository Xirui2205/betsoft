DELIMITER ;;
CREATE PROCEDURE dowhile()
BEGIN
  DECLARE v1 INT DEFAULT 10000;
  WHILE v1 > 0 DO
    INSERT vic_main.bet_free_alias VALUES (v1);
    SET v1 = v1 - 1;
  END WHILE;
END;;
DELIMITER ;

START TRANSACTION;

DELETE FROM vic_main.bet_free_alias
WHERE 1;

CALL dowhile();

DELETE FROM vic_main.bet_free_alias
WHERE `alias` IN (SELECT DISTINCT alias FROM sazky WHERE alias IS NOT NULL AND platna_do > now());

UPDATE vic_main.sazky SET
alias_released = 1
WHERE alias IS NOT NULL AND platna_do < now();

UPDATE vic_main.sazky SET
alias_released = NULL
WHERE alias IS NOT NULL AND platna_do > now();

COMMIT;

DROP PROCEDURE dowhile;
