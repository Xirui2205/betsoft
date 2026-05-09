INSERT INTO `vic_main`.`database_patch`(`revision`, `not_reapplicable`, `note`) VALUES
 ('7371', 1, 'New betradar implementation');

CREATE TABLE `vic_main`.`team_old` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `betradar_id` INT(10) UNSIGNED DEFAULT NULL,
  `sport_id` INT(10) UNSIGNED NOT NULL,
  `name` VARCHAR(128) NOT NULL,
  `short_name` varchar(64) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `betradar_id` (`betradar_id`),
  KEY `sport_id` (`sport_id`),
  CONSTRAINT `fk__team_old__sport_id` FOREIGN KEY (`sport_id`) REFERENCES `sport`(`sport_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;
INSERT INTO `vic_main`.`team_old`(`id`,`betradar_id`,`sport_id`,`name`,`short_name`)
 SELECT `id`,`betradar_id`,`sport_id`,`name`,`short_name` FROM `vic_main`.`team`;
TRUNCATE `vic_main`.`team`;

ALTER TABLE `vic_main`.`sazky`
 ADD COLUMN `betradar_bet_type` SET ('match','outright') NULL DEFAULT NULL AFTER `betradar_sazka_id`,
 ADD COLUMN `betradar_odds_type` VARCHAR(32) NULL DEFAULT NULL AFTER `betradar_match_id`,
 ADD COLUMN `betradar_special_value` VARCHAR(32) NULL DEFAULT NULL AFTER `betradar_odds_type`,
 ADD COLUMN `betradar_competitor_id` VARCHAR(32) NULL DEFAULT NULL AFTER `betradar_special_value`,
 ADD COLUMN `betradar_params` VARCHAR(64) NULL DEFAULT NULL AFTER `betradar_competitor_id`;

UPDATE `vic_main`.`sazky` SET `betradar_bet_type`='match' WHERE `betradar_match_id` IS NOT NULL AND `betradar_bet_type` IS NULL;

ALTER TABLE `vic_main`.`sazky`
 ADD INDEX `i__sazky__br_type_match` (`betradar_bet_type`,`betradar_match_id`),
 ADD INDEX `i__sazky__br_type_match_oddstype_specval_comp` (`betradar_bet_type`,`betradar_match_id`,`betradar_odds_type`,`betradar_special_value`, `betradar_competitor_id`);

CREATE TABLE `vic_main`.`team_sazky`(
 `betradar_team_id` INT(10) UNSIGNED NOT NULL,
 `sazka_id` INT(10) UNSIGNED NOT NULL,
 PRIMARY KEY (`betradar_team_id`, `sazka_id`),
 CONSTRAINT `i__team_sazky__betradar_team_id` FOREIGN KEY (`betradar_team_id`) REFERENCES `team`(`betradar_id`),
 CONSTRAINT `i__team_sazky__sazka_id` FOREIGN KEY (`sazka_id`) REFERENCES `sazky`(`sazka_id`)
) Engine=InnoDB DEFAULT CHARSET utf8 COLLATE utf8_general_ci;

-- apply this after using convert_br_bets.php database patch
ALTER TABLE `vic_main`.`sazky`
 DROP INDEX `betradar_sazka_id`,
 DROP INDEX `betradar_sazka_id_2`,
 DROP INDEX `betradar_sazka_id_3`,
 DROP COLUMN `betradar_sazka_id`;
