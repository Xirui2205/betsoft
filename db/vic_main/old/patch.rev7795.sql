ALTER TABLE `match_live` ADD `sport_id` INT UNSIGNED NOT NULL AFTER `sport` ,
ADD INDEX ( `sport_id` );

START TRANSACTION;


SET NAMES UTF8;

INSERT INTO `vic_main`.`database_patch`(`revision`, `not_reapplicable`,
`note`) VALUES
 ('0000', null, 'Added support for sport ids for live tickets');



UPDATE `vic_main`.`match_live` SET sport_id = 1001 WHERE sport ='Fotbal';
UPDATE `vic_main`.`match_live` SET sport_id = 1003 WHERE sport ='Tenis';
UPDATE `vic_main`.`match_live` SET sport_id = 1006 WHERE sport ='Basketbal';
UPDATE `vic_main`.`match_live` SET sport_id = 1011 WHERE sport ='Lední hokej';
UPDATE `vic_main`.`match_live` SET sport_id = 1001 WHERE sport ='Fotbal';
UPDATE `vic_main`.`match_live` SET sport_id = 1020 WHERE sport ='Házená';


COMMIT;


