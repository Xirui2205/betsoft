START TRANSACTION;

UPDATE `vic_main`.`udalost` SET 
	`sport_id` = 1037
WHERE
	`sport_id` = 1051;
	
DELETE FROM `vic_main`.`sport`
WHERE
	`sport_id` = 1051
LIMIT 1;

COMMIT;
