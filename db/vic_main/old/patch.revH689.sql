INSERT INTO `vic_main`.`database_patch`(`revision`, `not_reapplicable`, `note`) VALUES
 ('H689', 1, 'Bet changelog (mantis:884)');

CREATE TABLE `vic_main`.`bet_changelog` (
 `change_id` BIGINT(14) UNSIGNED NOT NULL AUTO_INCREMENT,
 `change_type` SET('new','status','validity','text','multiplicity','rates','correlated') NOT NULL,
 `change_time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
 `sazka_id` INT(10) UNSIGNED NOT NULL,
 `status` SMALLINT(5) UNSIGNED NULL DEFAULT NULL,
 `platna_od` DATETIME NULL DEFAULT NULL,
 `platna_do` DATETIME NULL DEFAULT NULL,
 `text` VARCHAR(255) NULL DEFAULT NULL COLLATE utf8_general_ci,
 `ticket_text` VARCHAR(64) NULL DEFAULT NULL COLLATE utf8_general_ci,
 `text_note` VARCHAR(16) NULL DEFAULT NULL COLLATE utf8_general_ci,
 `jednoducha` SMALLINT(5) UNSIGNED NOT NULL DEFAULT 0,
 `ako` TINYINT(4) UNSIGNED NOT NULL DEFAULT 0,
 `rates` TEXT NULL DEFAULT NULL COLLATE ascii_bin,
 `correlated` TEXT NULL DEFAULT NULL COLLATE ascii_bin,
 PRIMARY KEY (`change_id`),
 KEY `i__bet_changelog__change_type`(`change_type`),
 KEY `i__bet_changelog__sazka_id`(`sazka_id`)
 -- FOREIGN KEY `fk__bet_changelog__sazka_id`(`sazka_id`) REFERENCES `sazky`(`sazka_id`),
) Engine=InnoDb DEFAULT CHARSET utf8 COLLATE utf8_general_ci;
