INSERT INTO `vic_main`.`database_patch`(`revision`, `not_reapplicable`,
`note`) VALUES
 ('7270', NULL, NULL);

ALTER TABLE `vic_main`.`match_live` ADD `live_sport_id` INT NOT NULL AFTER `sport` ,
ADD INDEX ( `live_sport_id` ); 

UPDATE `vic_main`.`match_live` SET `live_sport_id` = 1006 WHERE sport="Basketbal";
UPDATE `vic_main`.`match_live` SET `live_sport_id` = 1006 WHERE sport="Fotbal";
UPDATE `vic_main`.`match_live` SET `live_sport_id` = 1020 WHERE sport="Házená";
UPDATE `vic_main`.`match_live` SET `live_sport_id` = 1011 WHERE sport="Lední hokej";
UPDATE `vic_main`.`match_live` SET `live_sport_id` = 1003 WHERE sport="Tenis";
UPDATE `vic_main`.`match_live` SET `live_sport_id` = 1013 WHERE sport="Volejbal";


ALTER TABLE `vic_main`.`sport` ADD `live_id` INT NULL DEFAULT NULL AFTER `nazev` ,
ADD UNIQUE (
`live_id`
);

UPDATE `vic_main`.`sport` SET `live_id`=`sport_id` WHERE `sport_id` IN (SELECT `live_sport_id` FROM `match_live` GROUP BY `sport`);

ALTER TABLE `match_live` ADD FOREIGN KEY ( `live_sport_id` ) REFERENCES `vic_main`.`sport` (
`live_id`
) ON DELETE RESTRICT ON UPDATE RESTRICT;
