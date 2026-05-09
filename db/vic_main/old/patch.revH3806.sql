START TRANSACTION;

-- vlozit zaznam do database_patch
INSERT INTO `vic_main`.`database_patch`(`revision`, `not_reapplicable`, `note`)
VALUES ('H3806', 1, 'SMS - translation (mantis:105)');

-- preklady pro sms

-- aktualni maximalni preklad_id
SELECT @idPrekladyMax := MAX(preklad_id) FROM vic_main.preklady;

INSERT INTO `vic_main`.`preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`, `preklad_id`) VALUES
(1,	'sms_view',		'',	'SMS (detail)',		    0,	(@idPrekladyMax := @idPrekladyMax+1)),
(1,	'sms_send_to',		'',	'Odesláno do',		0,	(@idPrekladyMax := @idPrekladyMax+1)),
(1,	'sms_send_from',	'',	'Odesláno od',		0,	(@idPrekladyMax := @idPrekladyMax+1)),
(1,	'sms_send',		'',	'Odesláno',             0,	(@idPrekladyMax := @idPrekladyMax+1)),
(1,	'sms_message_text',	'',	'Text zprávy',		0,	(@idPrekladyMax := @idPrekladyMax+1)),
(1,	'sms_phone_number',	'',	'Telefonní číslo',	0,	(@idPrekladyMax := @idPrekladyMax+1)),
(1,	'sms_type_name',	'',	'Typ SMS',          0,	(@idPrekladyMax := @idPrekladyMax+1));

COMMIT;