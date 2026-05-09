ALTER TABLE `vic_main`.`ticket_kurz`
	ADD `rate` DECIMAL( 10, 2 ) NULL;
ALTER TABLE `vic_main`.`ticket_kurz`
	ADD `won` DECIMAL( 10, 2 ) NULL;
ALTER TABLE `vic_main`.`ticket_kurz`
	ADD `win` DECIMAL( 10, 2 ) NULL;
ALTER TABLE `vic_main`.`ticket_kurz`
	ADD `amount` DECIMAL( 10, 2 ) NULL;


CREATE TABLE `vic_main`.`ticket_statistics_cashflow` (
 `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
 `hour` int(11) UNSIGNED NOT NULL,
 
 `user_id` int(10) UNSIGNED NOT NULL,
 `host_id` int(10) UNSIGNED NOT NULL,
 `branch_id` int(10) UNSIGNED NOT NULL,
 `sport_id` int(10) UNSIGNED NOT NULL,
 `udalost_id` int(10) UNSIGNED NOT NULL,
 `typ_id` int(10) UNSIGNED NOT NULL,
 `type` varchar(48) NOT NULL,
 `location_id` int(10) UNSIGNED NOT NULL,
 
 `amount` DECIMAL( 10, 2 ) NOT NULL,
 `won` DECIMAL( 10, 2 ) NOT NULL,
 `win` DECIMAL( 10, 2 ) NOT NULL,
 `count` DECIMAL( 10, 2 ) NOT NULL,
 `win_tip_count` INT(10) UNSIGNED NOT NULL,
 `lost_tip_count` INT(10) UNSIGNED NOT NULL,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;

CREATE TABLE `vic_main`.`ticket_statistics_payout` (
 `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
 `hour` int(11) UNSIGNED NOT NULL,
 
 `user_id` int(10) UNSIGNED NOT NULL,
 `host_id` int(10) UNSIGNED NOT NULL,
 `branch_id` int(10) UNSIGNED NOT NULL,
 `sport_id` int(10) UNSIGNED NOT NULL,
 `udalost_id` int(10) UNSIGNED NOT NULL,
 `typ_id` int(10) UNSIGNED NOT NULL,
 `type` varchar(48) NOT NULL,
 `location_id` int(10) UNSIGNED NOT NULL,
 
 `amount` DECIMAL( 10, 2 ) NOT NULL,
 `won` DECIMAL( 10, 2 ) NOT NULL,
 `win` DECIMAL( 10, 2 ) NOT NULL,
 `count` DECIMAL( 10, 2 ) NOT NULL,
 `win_tip_count` INT(10) UNSIGNED NOT NULL,
 `lost_tip_count` INT(10) UNSIGNED NOT NULL,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;


DROP TRIGGER IF EXISTS `vic_main`.`ticket_kurz_insert_trg`;
DELIMITER $$
CREATE TRIGGER `vic_main`.`ticket_kurz_insert_trg`
   AFTER INSERT ON ticket_kurz
   FOR EACH ROW
BEGIN
   DECLARE userId INT(10) UNSIGNED;
   DECLARE hour INT(10) UNSIGNED;
   DECLARE hostId INT(10) UNSIGNED;
   DECLARE branchId INT(10) UNSIGNED;
   DECLARE locationId INT(10) UNSIGNED;
   DECLARE eventId INT(10) UNSIGNED;
   DECLARE sportId INT(10) UNSIGNED;
   DECLARE typId INT(10) UNSIGNED;
   DECLARE type VARCHAR(48);
   DECLARE count DECIMAL(10, 2) UNSIGNED;
   DECLARE tmp INT(10) UNSIGNED;

   SELECT
      `user_id`,
      UNIX_TIMESTAMP(`zalozen`)/3600,
      `host_id`,
      `branch_id`,
      `branch_location_id`,
      `vic_main`.`ticket`.`type`
   INTO
      userId,
      hour,
      hostId,
      branchId,
      locationId,
      type
   FROM `vic_main`.`ticket`
   JOIN `vic_admin`.`host`
      ON `vic_admin`.`host`.`id` = `vic_main`.`ticket`.`host_id`
   JOIN `vic_admin`.`branch`
      ON `vic_admin`.`branch`.`id` = `vic_admin`.`host`.`branch_id`
   WHERE `ticket_id` = NEW.ticket_id LIMIT 1;
   
      
   SELECT
      `vic_main`.`sazky`.`udalost_id`,
      `sport_id`,
      `typ_id`
   INTO
      eventId,
      sportId,
      typId
   FROM `vic_main`.`sazky`
   JOIN `vic_main`.`udalost`
      ON `vic_main`.`udalost`.`udalost_id` = `vic_main`.`sazky`.`udalost_id`
   WHERE `vic_main`.`sazky`.`sazka_id` = NEW.sazka_id;
   
   
   SELECT 1/COUNT(*)
   INTO count
   FROM `vic_main`.`ticket_kurz`
   WHERE 
     `ticket_id` = NEW.ticket_id;
   
   
   SELECT COUNT(*)
   INTO tmp
   FROM `vic_main`.`ticket_statistics_cashflow`
   WHERE 
     `hour` = hour AND
     `user_id` = userId AND 
     `host_id` = hostId AND 
     `udalost_id` = eventId AND
     `typ_id` = typId AND
     `type` = type
   LIMIT 1;
   
   IF tmp = 0 THEN       
     INSERT INTO `vic_main`.`ticket_statistics_cashflow` 
		  (`id`, `hour`, `user_id`,`host_id`,`branch_id`,`location_id`, `sport_id`, `udalost_id`, `typ_id`, `type`, `amount`, `won`, `win`, `count`, `win_tip_count`, `lost_tip_count`)
     VALUES
	     (NULL, hour, userId, hostId, branchId,locationId,sportId,eventId,typId, type, NEW.amount, 0, NEW.win, count, 0, 0);
	ELSE
	   UPDATE `vic_main`.`ticket_statistics_cashflow` SET
	     `amount` = `amount` + NEW.amount,	     
	     `win` = `win` + NEW.win,
	     `count` = `count` + count
	   WHERE
	     `hour` = hour AND
        `user_id` = userId AND 
        `host_id` = hostId AND 
        `udalost_id` = eventId AND
        `typ_id` = typId AND
        `type` = type
      LIMIT 1;
	END IF;
         
END$$
DELIMITER ;


DROP TRIGGER IF EXISTS `vic_main`.`ticket_kurz_update_trg`;
DELIMITER $$
CREATE TRIGGER `vic_main`.`ticket_kurz_update_trg`
   AFTER UPDATE ON ticket_kurz
   FOR EACH ROW
BEGIN
   DECLARE userId INT(10) UNSIGNED;
   DECLARE hour INT(10) UNSIGNED;
   DECLARE hostId INT(10) UNSIGNED;
   DECLARE branchId INT(10) UNSIGNED;
   DECLARE locationId INT(10) UNSIGNED;
   DECLARE eventId INT(10) UNSIGNED;
   DECLARE sportId INT(10) UNSIGNED;
   DECLARE typId INT(10) UNSIGNED;
   DECLARE type VARCHAR(48);
   DECLARE count DECIMAL(10, 2) UNSIGNED;
   DECLARE tmp INT(10) UNSIGNED;

   IF NEW.won != OLD.won THEN

      SELECT
         `user_id`,
         UNIX_TIMESTAMP(`vyplacen_date`)/3600,
         `host_id`,
         `branch_id`,
         `branch_location_id`,
         `vic_main`.`ticket`.`type`
      INTO
         userId,
         hour,
         hostId,
         branchId,
         locationId,
         type
      FROM `vic_main`.`ticket`
      JOIN `vic_admin`.`host`
         ON `vic_admin`.`host`.`id` = `vic_main`.`ticket`.`host_id`
      JOIN `vic_admin`.`branch`
         ON `vic_admin`.`branch`.`id` = `vic_admin`.`host`.`branch_id`
      WHERE `ticket_id` = NEW.ticket_id LIMIT 1;
      
         
      SELECT
         `vic_main`.`sazky`.`udalost_id`,
         `sport_id`,
         `typ_id`
      INTO
         eventId,
         sportId,
         typId
      FROM `vic_main`.`sazky`
      JOIN `vic_main`.`udalost`
         ON `vic_main`.`udalost`.`udalost_id` = `vic_main`.`sazky`.`udalost_id`
      WHERE `vic_main`.`sazky`.`sazka_id` = NEW.sazka_id;
      
      SELECT COUNT(*)
      INTO tmp
      FROM `vic_main`.`ticket_statistics_cashflow`
      WHERE 
        `hour` = hour AND
        `user_id` = userId AND 
        `host_id` = hostId AND 
        `udalost_id` = eventId AND
        `typ_id` = typId AND
        `type` = type
      LIMIT 1;
      
      IF tmp = 0 THEN       
        INSERT INTO `vic_main`.`ticket_statistics_cashflow` 
		     (`id`, `hour`, `user_id`,`host_id`,`branch_id`,`location_id`, `sport_id`, `udalost_id`, `typ_id`, `type`, `amount`, `won`, `win`, `count`, `win_tip_count`, `lost_tip_count`)
        VALUES
	        (NULL, hour, userId, hostId, branchId,locationId,sportId,eventId,typId, type, 0, NEW.won, 0, 0, 1, 0);
	   ELSE
	      UPDATE `vic_main`.`ticket_statistics_cashflow` SET	        
	        `won` = `won` + NEW.won,
	        `win_tip_count` = win_tip_count + 1
	      WHERE
	        `hour` = hour AND
           `user_id` = userId AND 
           `host_id` = hostId AND 
           `udalost_id` = eventId AND
           `typ_id` = typId AND
           `type` = type
         LIMIT 1;               
	   END IF;
	   
	   SELECT 1/COUNT(*)
      INTO count
      FROM `vic_main`.`ticket_kurz`
      WHERE 
     `ticket_id` = NEW.ticket_id;
	   
	   SELECT COUNT(*)
      INTO tmp
      FROM `vic_main`.`ticket_statistics_payout`
      WHERE 
        `hour` = hour AND
        `user_id` = userId AND 
        `host_id` = hostId AND 
        `udalost_id` = eventId AND
        `typ_id` = typId AND
        `type` = type
      LIMIT 1;
      
      IF tmp = 0 THEN       
        INSERT INTO `vic_main`.`ticket_statistics_payout` 
		     (`id`, `hour`, `user_id`,`host_id`,`branch_id`,`location_id`, `sport_id`, `udalost_id`, `typ_id`, `type`, `amount`, `won`, `win`, `count`, `win_tip_count`, `lost_tip_count`)
        VALUES
	        (NULL, hour, userId, hostId, branchId,locationId,sportId,eventId,typId, type, NEW.amount, 0, NEW.win, count, 1, 0);
	   ELSE
	      UPDATE `vic_main`.`ticket_statistics_payout` SET
	        `amount` = `amount` + NEW.amount,	     
	        `win` = `win` + NEW.win,
	        `won` = `won` + NEW.won,
	        `win_tip_count` = + win_tip_count + 1,
	        `count` = `count` + count
	      WHERE
	        `hour` = hour AND
           `user_id` = userId AND 
           `host_id` = hostId AND 
           `udalost_id` = eventId AND
           `typ_id` = typId AND
           `type` = type
         LIMIT 1;
	   END IF;
      
   END IF;
         
END$$
DELIMITER ;