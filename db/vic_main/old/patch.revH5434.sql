START TRANSACTION;

INSERT INTO `database_patch`(`revision`, `not_reapplicable`, `note`) VALUES
 ('H5434', 0, 'unsubscribe section (mantis261)');

INSERT INTO `controller_convert` (`c_id`, `lang_id`, `parent_id`, `poradi`, `zobrazeno`, `cols`, `left_side`, `right_side`, `title`, `description`, `keywords`, `text`, `req_controller`, `req_action`, `real_controller`, `real_action`, `after_login`, `actionless`, `nonassoc_params`)
VALUES ('115', '1', '0', '0', '1', '3', NULL, NULL, 'Odhlásit se z newsletteru', NULL, NULL, '', 'newsletter', 'odhlasit', 'unsubscribe', 'index', '1', '0', '0'),
('115', '2', '0', '0', '1', '3', NULL, NULL, 'Unsubscribe', NULL, NULL, '', 'newsletter', 'unsubscribe', 'unsubscribe', 'index', '1', '0', '0'),
('115', '16', '0', '0', '1', '3', NULL, NULL, 'Odhlásiť sa z newslettera', NULL, NULL, '', 'newsletter', 'odhlasit', 'unsubscribe', 'index', '1', '0', '0');

-- aktualni maximalni preklad_id
SELECT @idPrekladyMax := MAX(preklad_id) FROM vic_main.preklady;

INSERT INTO `preklady` (`lang_id`, `index_pole`,
`short_text`, `text`, `translate`, `preklad_id`) VALUES
(1,	'unsubscribe',		'',	'Odhlásit',		0,	(@idPrekladyMax := @idPrekladyMax+1) ),
(1,	'unsubscribe_title',		'',	'Odhlásit se z newsletteru',		0,	(@idPrekladyMax := @idPrekladyMax+1) ),
(1,	'unsubscribe_ok',		'',	'Odhlásili jste se z odběru newsletteru.',		0,	(@idPrekladyMax := @idPrekladyMax+1) ),
(1,	'unsubscribe_wrong',		'',	'Neplatný požadavek.',		0,	(@idPrekladyMax := @idPrekladyMax+1) );

COMMIT;