INSERT INTO `vic_main`.`database_patch`(`revision`, `not_reapplicable`, `note`) VALUES
 ('7564', 1, 'Bet column risk limit');

CREATE TABLE `vic_main`.`bet_column`(
 `sazka_id` INT(10) UNSIGNED NOT NULL,
 `sloupec_id` INT(10) UNSIGNED NOT NULL,
 `risk_limit_balance` DECIMAL(10,2) UNSIGNED NOT NULL DEFAULT 0,
 PRIMARY KEY (`sazka_id`, `sloupec_id`),
 CONSTRAINT `fk__bet_type_column__sazka_id` FOREIGN KEY (`sazka_id`) REFERENCES `sazky`(`sazka_id`),
 CONSTRAINT `fk__bet_type_column__sloupec_id` FOREIGN KEY (`sloupec_id`) REFERENCES `podtyp_sloupce`(`sloupec_id`)
) Engine=MyISAM DEFAULT CHARSET utf8 COLLATE utf8_general_ci;

