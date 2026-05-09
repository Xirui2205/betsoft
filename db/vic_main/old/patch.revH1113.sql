INSERT INTO `vic_main`.`database_patch`(`revision`, `not_reapplicable`, `note`)
VALUES (1113, 1, 'renamed index on match_live');

ALTER TABLE `vic_main`.`match_live` DROP INDEX `sport_id` ,
ADD INDEX `i__match_live__sport_id` ( `sport_id` ) 