DROP TABLE `vic_main`.`archive_cronjob_param`;
DROP TABLE `vic_main`.`archive_cronjob`;
DROP TABLE `vic_main`.`cronjob_param`;
DROP TABLE `vic_main`.`cronjob`;

CREATE TABLE `vic_main`.`archive_cronjob` (
 `archive_cronjob_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
 `cronjob_id` int(11) UNSIGNED NOT NULL,
 `cronjob_type` int(10) UNSIGNED NOT NULL,
 `result` int(10) DEFAULT NULL,
 `attempts` int(2) NOT NULL DEFAULT '0',
 `attempts_max` INT( 2 ) NOT NULL DEFAULT '0',
 `exec_at` datetime DEFAULT NULL,
 `executed_at` datetime DEFAULT NULL,
 `exec_constantly_at` varchar(64) DEFAULT NULL,
 PRIMARY KEY (`archive_cronjob_id`),
 KEY (`cronjob_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;

CREATE TABLE `vic_main`.`archive_cronjob_param` (
 `archive_param_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
 `archive_cronjob_id` int(11) UNSIGNED NOT NULL,
 `cronjob_id` int(11) UNSIGNED NOT NULL,
 `param_name` varchar(255) NOT NULL,
 `param_value` text NOT NULL,
 PRIMARY KEY (`archive_param_id`),
 KEY `archive_cronjob_id` (`archive_cronjob_id`),
 KEY `cronjob_id` (`cronjob_id`),
 CONSTRAINT `fk__archive_cronjob_param__archive_cronjob_id` FOREIGN KEY (`archive_cronjob_id`) REFERENCES `vic_main`.`archive_cronjob`(`archive_cronjob_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;

CREATE TABLE `vic_main`.`cronjob` (
 `cronjob_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
 `cronjob_type` int(10) UNSIGNED NOT NULL,
 `result` int(10) DEFAULT NULL,
 `attempts` int(2) NOT NULL DEFAULT '0',
 `attempts_max` INT( 2 ) NOT NULL DEFAULT '0', 
 `exec_at` datetime DEFAULT NULL,
 `executed_at` datetime DEFAULT NULL,
 `exec_constantly_at` varchar(64) DEFAULT NULL,
 PRIMARY KEY (`cronjob_id`),
 KEY `cronjob_type` (`cronjob_type`),
 CONSTRAINT `fk__cronjob__cronjob_type` FOREIGN KEY (`cronjob_type`) REFERENCES `vic_main`.`cronjob_type`(`type_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;

CREATE TABLE `vic_main`.`cronjob_sequence` (
 `cronjob_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
 PRIMARY KEY(`cronjob_id`)
) Engine=MyISAM;

CREATE TABLE `vic_main`.`cronjob_param` (
 `param_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
 `cronjob_id` int(11) UNSIGNED NOT NULL,
 `param_name` varchar(255) NOT NULL,
 `param_value` text NOT NULL,
 PRIMARY KEY (`param_id`),
 KEY `cronjob_id` (`cronjob_id`),
 CONSTRAINT `fk__cronjob_param__cronjob_id` FOREIGN KEY (`cronjob_id`) REFERENCES `vic_main`.`cronjob`(`cronjob_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;

START TRANSACTION;
INSERT INTO `vic_main`.`cronjob` (`cronjob_id`, `cronjob_type`, `result`, `attempts`, `exec_at`, `executed_at`, `exec_constantly_at`) VALUES
(1, 2, NULL, 0, NULL, NULL, '34 1 * * *'),
(2, 2, NULL, 0, NULL, NULL, '12 * * * *'),
(3, 2, NULL, 0, NULL, NULL, '42 1 * * *'),
(4, 2, NULL, 0, NULL, NULL, '56 1 * * *'),
(5, 3, NULL, 0, NULL, NULL, '4,16,28,40,52 * * * *'),
(6, 2, NULL, 0, NULL, NULL, '44 1 * * *'),
(7, 2, NULL, 0, NULL, NULL, '54 1 * * *'),
(8, 2, NULL, 0, NULL, NULL, '04 2 * * *');

INSERT INTO `vic_main`.`cronjob_param` (`param_id`, `cronjob_id`, `param_name`, `param_value`) VALUES
(1, 1, 'name', '"UserBlackList"'),
(2, 2, 'name', '"NewUsers"'),
(3, 3, 'name', '"CanceledTickets"'),
(4, 4, 'name', '"WinRatio"'),
(5, 6, 'name', '"BranchBlackist"'),
(6, 7, 'name', '"BranchTicketsCount"'),
(7, 8, 'name', '"HostDeposits"');
COMMIT;

SET @ai = (SELECT (MAX(`cronjob_id`)+1) FROM `cronjob`);
SET @sql = CONCAT('ALTER TABLE `cronjob_sequence` AUTO_INCREMENT=', @ai);
PREPARE stmt FROM @sql;
EXECUTE stmt;
