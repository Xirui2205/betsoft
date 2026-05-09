
START TRANSACTION;

INSERT INTO `vic_main`.`controller_convert` 
	(`c_id`, `lang_id`, `parent_id`, `poradi`, `zobrazeno`, `cols`, `left_side`, `right_side`, `title`, `description`, `text`, `req_controller`, `req_action`, `real_controller`, `real_action`, `after_login`, `actionless`)
VALUES
	(66, 1, 1, 7, 0, 3, NULL, NULL, NULL, NULL, 'logout', 'global-cache-frame', 'logout', 'index', 'logout', 0, 0),
	(66, 2, 1, 7, 0, 3, NULL, NULL, NULL, NULL, 'logout', 'global-cache-frame', 'logout', 'index', 'logout', 0, 0),
	(66, 16, 1, 7, 0, 3, NULL, NULL, NULL, NULL, 'logout', 'global-cache-frame', 'logout', 'index', 'logout', 0, 0),
	(67, 1, 1, 7, 0, 3, NULL, NULL, NULL, NULL, 'ticket', 'global-cache-frame', 'ticket', 'sportsbook', 'ticket', 0, 0),
	(67, 2, 1, 7, 0, 3, NULL, NULL, NULL, NULL, 'ticket', 'global-cache-frame', 'ticket', 'sportsbook', 'ticket', 0, 0),
	(67, 16, 1, 7, 0, 3, NULL, NULL, NULL, NULL, 'ticket', 'global-cache-frame', 'ticket', 'sportsbook', 'ticket', 0, 0),
	(68, 1, 1, 7, 0, 3, NULL, NULL, NULL, NULL, 'sport-menu', 'global-cache-frame', 'sport-menu', 'sportsbook', 'sport-menu', 0, 0),
	(68, 2, 1, 7, 0, 3, NULL, NULL, NULL, NULL, 'sport-menu', 'global-cache-frame', 'sport-menu', 'sportsbook', 'sport-menu', 0, 0),
	(68, 16, 1, 7, 0, 3, NULL, NULL, NULL, NULL, 'sport-menu', 'global-cache-frame', 'sport-menu', 'sportsbook', 'sport-menu', 0, 0),
	(69, 1, 1, 7, 0, 3, NULL, NULL, NULL, NULL, 'sport-odds', 'global-cache-frame', 'sport-odds', 'sportsbook', 'sport-odds', 0, 0),
	(69, 2, 1, 7, 0, 3, NULL, NULL, NULL, NULL, 'sport-odds', 'global-cache-frame', 'sport-odds', 'sportsbook', 'sport-odds', 0, 0),
	(69, 16, 1, 7, 0, 3, NULL, NULL, NULL, NULL, 'sport-odds', 'global-cache-frame', 'sport-odds', 'sportsbook', 'sport-odds', 0, 0);


COMMIT;
