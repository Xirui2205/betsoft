ALTER TABLE `vic_main`.`sazky` ADD `alias_released` TINYINT( 1 ) NULL;
ALTER TABLE `vic_main`.`sazky` CHANGE `alias` `alias` INT( 11 ) NULL;
UPDATE `vic_main`.`sazky` SET  `alias` = NULL;

ALTER TABLE `vic_main`.`udalost` ADD `bet_alias_from` INT UNSIGNED NULL ,
ADD `bet_alias_to` INT UNSIGNED NULL;

ALTER TABLE `vic_main`.`sport` ADD `bet_alias_from` INT UNSIGNED NULL ,
ADD `bet_alias_to` INT UNSIGNED NULL;

CREATE TABLE `vic_main`.`bet_free_alias` (
`alias` INT UNSIGNED NOT NULL ,
PRIMARY KEY ( `alias` )
) ENGINE = MYISAM ;

DELIMITER ;;
CREATE PROCEDURE dowhile()
BEGIN
  DECLARE v1 INT DEFAULT 10000;
  WHILE v1 > 0 DO
    INSERT bet_free_alias VALUES (v1);
    SET v1 = v1 - 1;
  END WHILE;
END;;
DELIMITER ;

CALL dowhile();

DROP PROCEDURE dowhile;
