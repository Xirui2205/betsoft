CREATE TABLE `vic_main`.`sport_result_text`(
 `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
 `sport_id` INT(10) UNSIGNED NOT NULL,
 `lang_id` INT(10) UNSIGNED NOT NULL,
 `ot` VARCHAR(32) NULL DEFAULT NULL,
 `ap` VARCHAR(32) NULL DEFAULT NULL,
 CONSTRAINT `u__sport_result_text__sport_lang` UNIQUE (`sport_id`,`lang_id`),
 CONSTRAINT `fk__sport_result_text__sport_id` FOREIGN KEY (`sport_id`) REFERENCES `sport`(`sport_id`),
 CONSTRAINT `fk__sport_result_text__lang_id` FOREIGN KEY (`lang_id`) REFERENCES `jazyky`(`lang_id`)
) Engine=InnoDB DEFAULT CHARSET utf8 COLLATE utf8_general_ci;

INSERT INTO `vic_main`.`sport_result_text` (`id`, `sport_id`, `lang_id`, `ot`, `ap`) VALUES
(NULL, 1001, 1, 'p.p.', 'pen.'),
(NULL, 1011, 1, 'p.p.', 'náj.');
