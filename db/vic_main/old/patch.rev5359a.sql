START TRANSACTION;
DELETE FROM `vic_main`.`bet_settings` WHERE udalost_id NOT IN (SELECT udalost_id FROM `vic_main`.`udalost`);
COMMIT;
