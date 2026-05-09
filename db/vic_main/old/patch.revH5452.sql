START TRANSACTION;

INSERT INTO `database_patch`(`revision`, `not_reapplicable`, `note`)
VALUES ('H5452', 1, ' (mantis:258)');

-- aktualni maximalni preklad_id
SELECT @idPrekladyMax := MAX(preklad_id) FROM preklady;

INSERT INTO `preklady` (`lang_id`, `index_pole`,
`short_text`, `text`, `translate`, `preklad_id`) VALUES
(1,	'blocked_ips',		'',	'Blokované IP adresy',		0,	(@idPrekladyMax := @idPrekladyMax+1) ),
(1,	'remove_blocked_ips_info',		'',	'(zvolte k odblokování)',		0,	(@idPrekladyMax := @idPrekladyMax+1) ),
(1,	'time_remaining',		'',	'Zbývající čas [min]',		0,	(@idPrekladyMax := @idPrekladyMax+1) );

COMMIT;
