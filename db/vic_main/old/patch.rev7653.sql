INSERT INTO `vic_main`.`database_patch`(`revision`, `not_reapplicable`, `note`) VALUES
 ('7653', 1, 'Bet user history');

CREATE TABLE `vic_main`.`bet_user_history`(
 `sazka_id` INT(10) UNSIGNED NOT NULL,
 `user_id`  INT(10) UNSIGNED NOT NULL,
 `ticket_count` INT(10) UNSIGNED NOT NULL DEFAULT 0,
 `stake_balance` DECIMAL(10,2) UNSIGNED NOT NULL DEFAULT 0,
 PRIMARY KEY (`sazka_id`, `user_id`),
 CONSTRAINT `fk__bet_user_history__sazka_id` FOREIGN KEY (`sazka_id`) REFERENCES `sazky`(`sazka_id`),
 CONSTRAINT `fk__bet_user_history__user_id` FOREIGN KEY (`user_id`) REFERENCES `uzivatel`(`user_id`)
) Engine=InnoDb DEFAULT CHARSET utf8 COLLATE utf8_general_ci;
