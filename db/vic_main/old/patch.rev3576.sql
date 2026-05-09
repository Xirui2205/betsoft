CREATE TABLE `vic_main`.`activity_log` (
	`activity_type` INT(10) UNSIGNED NOT NULL PRIMARY KEY,
	`last_at` DATETIME NULL DEFAULT NULL,
	`description` VARCHAR(512) NOT NULL
) Engine=MyISAM DEFAULT CHARSET 'utf8' COLLATE 'utf8_general_ci';

INSERT INTO `vic_main`.`activity_log`(`activity_type`, `last_at`, `description`) VALUES
 (1, NULL, 'CronJob');
