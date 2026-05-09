START TRANSACTION;

-- vlozit zaznam do database_patch
INSERT INTO `vic_main`.`database_patch`(`revision`, `not_reapplicable`, `note`)
VALUES ('H4233', 1, 'Supertip tab action (mantis:84)');

-- sekce pro vypis supertip tab
INSERT INTO `controller_convert`
(`c_id`, `lang_id`, `parent_id`, `poradi`, `zobrazeno`, `cols`, `left_side`, `right_side`, `title`, `description`, `keywords`, `text`, `req_controller`, `req_action`, `real_controller`, `real_action`, `after_login`, `actionless`, `nonassoc_params`) VALUES
(110,	1,		0,	1,		1,	3,		'',	'',		NULL,NULL,		NULL,	'',	'ajax',		'supertip-tab',	  'ajax',	'supertip-tab',		1,		0,	0),
(110,	2,		0,	1,		1,	3,		'',	'',		NULL,NULL,		NULL,	'',	'ajax',		'supertip-tab',	  'ajax',	'supertip-tab',		1,		0,	0),
(110,	16,		0,	1,		1,	3,		'',	'',		NULL,NULL,		NULL,	'',	'ajax',		'supertip-tab',   'ajax',	'supertip-tab',		1,		0,	0);

COMMIT;