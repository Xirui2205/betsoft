DROP TABLE `vic_main`.`user_actions`;
DROP TABLE `vic_main`.`archive_user_actions`;

CREATE TABLE `vic_main`.`user_action` (
  `action_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(10) NOT NULL,
  `action_type` int(3) NOT NULL,
  `valid_from` datetime NOT NULL,
  `valid_until` datetime NOT NULL,
  `action_hash` char(32) NOT NULL,
  `date_created` datetime NOT NULL,
  `done` tinyint(1) NOT NULL DEFAULT '0',
  `date_executed` datetime DEFAULT NULL,
  PRIMARY KEY (`action_id`),
  CONSTRAINT `u__user_action__action_hash` UNIQUE(`action_hash`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `vic_main`.`archive_user_action` (
  `action_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) NOT NULL,
  `action_type` int(3) NOT NULL,
  `valid_from` datetime NOT NULL,
  `valid_until` datetime NOT NULL,
  `action_hash` char(32) NOT NULL,
  `date_created` datetime NOT NULL,
  `done` tinyint(1) NOT NULL DEFAULT '0',
  `date_executed` datetime DEFAULT NULL,
  PRIMARY KEY (`action_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


START TRANSACTION;
INSERT INTO `vic_main`.`controller_convert` (`c_id`,`lang_id`, `parent_id`, `poradi`, `zobrazeno`,
	`cols`, `left_side`, `right_side`, `title`, `description`, `text`, `req_controller`, `req_action`,
	`real_controller`, `real_action`, `after_login`, `actionless`)
VALUES(
	'64', '1', '1', '7', '1', '3', NULL, NULL, NULL, NULL, 'Akce uživatele',
	'uzivatel-akce', 'index', 'useraction', 'index', '0', '0'
), (
	'64', '2', '1', '7', '1', '3', NULL, NULL, NULL, NULL, 'User Actions',
	'useraction', 'index', 'useraction', 'index', '0', '0'
), (
	'64', '16', '1', '7', '1', '3', NULL, NULL, NULL, NULL, 'Akce uživatele',
	'uzivatel-akce', 'index', 'useraction', 'index', '0', '0'
),(
	'65', '1', '1', '7', '1', '3', NULL, NULL, NULL, NULL, 'proveď akci uživatele',
	'uzivatel-akce', 'vysledek', 'useraction', 'result', '0', '0'
), (
	'65', '2', '1', '7', '1', '3', NULL, NULL, NULL, NULL, 'Do User Action',
	'useraction', 'result', 'useraction', 'result', '0', '0'
), (
	'65', '16', '1', '7', '1', '3', NULL, NULL, NULL, NULL, 'proveď akci uživatele',
	'uzivatel-akce', 'vysledek', 'useraction', 'result', '0', '0'
);
COMMIT;
