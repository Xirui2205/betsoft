INSERT INTO `vic_main`.`database_patch`(`revision`, `not_reapplicable`, `note`) VALUES
 ('7825', 1, 'Novy vernostni program happy hour. (mantis:566)');

CREATE TABLE `vic_main`.`happy_hour` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` enum('visit') NOT NULL,
  `time_from` datetime NOT NULL,
  `time_to` datetime DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `users_count` int(10) unsigned NOT NULL,
  `users_init_count` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `i__happy_hour__time_from` (`time_from`),
  KEY `i__happy_hour__time_to` (`time_to`),
  KEY `i__happy_hour__users_count` (`users_count`),
  KEY `i__happy_hour__type` (`type`)
) ENGINE=InnoDB;


START TRANSACTION;

INSERT INTO `vic_main`.`point_transaction_type`
	(`id` ,`name` ,`kind` ,`point_type_id` ,`note` ,`from` ,`thru` ,`to` ,`debiting` ,`low_limit` ,`high_limit`)
VALUES 
	('11', 'happyHourVisit', 'get', '1', 'Body za navstevu internetu v dany cas.', NULL , NULL , NULL , '0', '0', NULL);

INSERT INTO `vic_main`.`controller_convert` 
	(`c_id`, `lang_id`, `parent_id`, `poradi`, `zobrazeno`, `cols`, `left_side`, `right_side`, `title`, `description`, `text`, `req_controller`, `req_action`, `real_controller`, `real_action`, `after_login`, `actionless`)
VALUES
	(90, 1, 1, 7, 1, 3, NULL, NULL, NULL, NULL, 'Visit happy hour', 'happy-hour', 'visit', 'happy-hour', 'visit', 0, 0),
	(90, 2, 1, 7, 1, 3, NULL, NULL, NULL, NULL, 'Visit happy hour', 'happy-hour', 'visit', 'happy-hour', 'visit', 0, 0),
	(90, 16, 1, 7, 1, 3, NULL, NULL, NULL, NULL, 'Visit happy hour','happy-hour', 'visit', 'happy-hour', 'visit', 0, 0);


INSERT INTO `vic_main`.`cronjob_type` (`type_id`, `type_name`)
VALUES (9, 'GenerateVisitHappyHour');

INSERT INTO `vic_main`.`cronjob` (`cronjob_id`, `cronjob_type`,`exec_constantly_at`)
VALUES (17, 9, '07 3 * * *');


COMMIT;
