ALTER TABLE `vic_main`.`controller_convert` ADD `actionless` BOOLEAN NOT NULL DEFAULT 0;
START TRANSACTION;
UPDATE `vic_main`.`controller_convert` SET `actionless`=1 WHERE `c_id` IN (2,4,5,6,7,9);
COMMIT;
