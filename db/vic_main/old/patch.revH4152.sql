START TRANSACTION;

-- vlozit zaznam do database_patch
INSERT INTO `vic_main`.`database_patch`(`revision`, `not_reapplicable`, `note`)
VALUES ('H4152', 1, 'Live calendar small  (mantis:84)');

-- aktualni maximalni preklad_id
SELECT @idPrekladyMax := MAX(preklad_id) FROM vic_main.preklady;

INSERT INTO `vic_main`.`preklady` (`lang_id`, `index_pole`,
`short_text`, `text`, `translate`, `preklad_id`) VALUES
(1,	'comming_up_2',		'',	'Již brzy',		0,	(@idPrekladyMax := @idPrekladyMax+1) );

COMMIT;