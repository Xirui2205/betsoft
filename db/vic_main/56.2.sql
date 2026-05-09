-- sportsbook home mimo sportsbook controller (do indexu) - problémy s odkazy sazky/<sport>
DELETE FROM `controller_convert` WHERE `c_id` = 117;
INSERT INTO `controller_convert` (`c_id`, `lang_id`, `parent_id`, `poradi`, `zobrazeno`, `cols`, `left_side`, `right_side`, `title`, `description`, `keywords`, `text`, `req_controller`, `req_action`, `real_controller`, `real_action`, `after_login`, `actionless`, `nonassoc_params`) VALUES
(117,	1,	2,	5,	1,	3,	NULL,	NULL,	NULL,	NULL,	NULL,	'Kurzové sázky - úvod',	'index',	'sportsbook',	'Index',	'sportsbook',	0,	1,	0),
(117,	2,	2,	5,	1,	3,	NULL,	NULL,	NULL,	NULL,	NULL,	'Sportsbook - home',	'index',	'sportsbook',	'Index',	'sportsbook',	0,	1,	0),
(117,	16,	2,	5,	1,	3,	NULL,	NULL,	NULL,	NULL,	NULL,	'Kurzové stávky - úvod', 'index',	'sportsbook',	'Index',	'sportsbook',	0,	1,	0);