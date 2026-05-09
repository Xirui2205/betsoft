START TRANSACTION;

INSERT INTO `vic_main`.`database_patch`(`revision`, `not_reapplicable`,
`note`) VALUES
 ('7335', null, 'Added login page');


INSERT INTO `vic_main`.`controller_convert` (`c_id`, `lang_id`, `parent_id`, `poradi`, `zobrazeno`, `cols`, `left_side`, `right_side`, `title`, `description`, `text`, `req_controller`, `req_action`, `real_controller`, `real_action`, `after_login`, `actionless`)
	VALUES ('88', '1', '0', '0', '1', '3', NULL, NULL, NULL, NULL, '', 'prihlaseni', 'index', 'login', 'index', '0', '0');
INSERT INTO `vic_main`.`controller_convert` (`c_id`, `lang_id`, `parent_id`, `poradi`, `zobrazeno`, `cols`, `left_side`, `right_side`, `title`, `description`, `text`, `req_controller`, `req_action`, `real_controller`, `real_action`, `after_login`, `actionless`)
	VALUES ('88', '2', '0', '0', '1', '3', NULL, NULL, NULL, NULL, '', 'login', 'index', 'login', 'index', '0', '0');
INSERT INTO `vic_main`.`controller_convert` (`c_id`, `lang_id`, `parent_id`, `poradi`, `zobrazeno`, `cols`, `left_side`, `right_side`, `title`, `description`, `text`, `req_controller`, `req_action`, `real_controller`, `real_action`, `after_login`, `actionless`)
	VALUES ('88', '16', '0', '0', '1', '3', NULL, NULL, NULL, NULL, '', 'prihlaseni', 'index', 'login', 'index', '0', '0');

COMMIT;
