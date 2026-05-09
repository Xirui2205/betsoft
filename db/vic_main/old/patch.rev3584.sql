START TRANSACTION;

INSERT INTO `vic_main`.`controller_convert` 
	(`c_id`, `lang_id`, `parent_id`, `poradi`, `zobrazeno`, `cols`, `left_side`, `right_side`, `title`, `description`, `text`, `req_controller`, `req_action`, `real_controller`, `real_action`, `after_login`, `actionless`)
VALUES
	(71,  1, 1, 7, 0, 3, NULL, NULL, NULL, NULL, 'live-calendar-small', 'global-cache-frame', 'live-calendar-small', 'index', 'live-calendar-small', 0, 0),
	(71,  2, 1, 7, 0, 3, NULL, NULL, NULL, NULL, 'live-calendar-small', 'global-cache-frame', 'live-calendar-small', 'index', 'live-calendar-small', 0, 0),
	(71, 16, 1, 7, 0, 3, NULL, NULL, NULL, NULL, 'live-calendar-small', 'global-cache-frame', 'live-calendar-small', 'index', 'live-calendar-small', 0, 0),
	(72,  1, 1, 7, 0, 3, NULL, NULL, NULL, NULL, 'live-calendar', 'global-cache-frame', 'live-calendar', 'index', 'live-calendar', 0, 0),
	(72,  2, 1, 7, 0, 3, NULL, NULL, NULL, NULL, 'live-calendar', 'global-cache-frame', 'live-calendar', 'index', 'live-calendar', 0, 0),
	(72, 16, 1, 7, 0, 3, NULL, NULL, NULL, NULL, 'live-calendar', 'global-cache-frame', 'live-calendar', 'index', 'live-calendar', 0, 0);

COMMIT;
