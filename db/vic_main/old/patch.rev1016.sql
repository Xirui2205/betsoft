ALTER TABLE `ticket` ADD `tickethash` VARCHAR(32) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL,
	ADD INDEX (`tickethash`);

CREATE TABLE `vic_main`.`tickethash` (
`tickethash` VARCHAR(32) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL,
`user_id` INT(10) UNSIGNED NOT NULL,
`tickethash_count` INT(10) UNSIGNED NOT NULL,
PRIMARY KEY (`tickethash`),
FOREIGN KEY (`user_id`) REFERENCES `uzivatel` (`user_id`)
) ENGINE = InnoDB DEFAULT CHARACTER SET utf8 COLLATE 'utf8_general_ci';

CREATE TABLE `vic_main`.`tickethash_ticket` (
`tickethash` VARCHAR(32) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL,
`user_id` INT(10) UNSIGNED NOT NULL,
`ticket_id` BIGINT(20) UNSIGNED NOT NULL,
PRIMARY KEY ( `tickethash` , `user_id`, `ticket_id`),
FOREIGN KEY (`tickethash`) REFERENCES `tickethash` (`tickethash`),
FOREIGN KEY (`user_id`) REFERENCES `uzivatel` (`user_id`),
FOREIGN KEY (`ticket_id`) REFERENCES `ticket` (`ticket_id`)
) ENGINE = InnoDB DEFAULT CHARACTER SET utf8 COLLATE 'utf8_general_ci';
