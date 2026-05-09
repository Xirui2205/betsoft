INSERT INTO `vic_main`.`database_patch`(`revision`, `not_reapplicable`, `note`)
VALUES (905, 1, "added support for score status and region for live matches");


ALTER TABLE `match_live`
ADD `score_home` VARCHAR( 50 ) NOT NULL ,
ADD `score_away` VARCHAR( 50 ) NOT NULL ,
ADD `region` VARCHAR( 255 ) NOT NULL 
