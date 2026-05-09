START TRANSACTION;

-- zaznam do database_patch
INSERT INTO `vic_main`.`database_patch`(`revision`, `not_reapplicable`, `note`)
VALUES ('H4105', 1, 'Preklady pro global balancing (mantis:98)');

-- preklady
SELECT @idPrekladyMax := MAX(preklad_id) FROM vic_main.preklady;
INSERT INTO `vic_main`.`preklady` (`lang_id`, `index_pole`,
`short_text`, `text`, `translate`, `preklad_id`) VALUES

(1,	'live_ticket_count',		'',	'Počet tiketů (live)',		0,	(@idPrekladyMax := @idPrekladyMax+1) ),
(1,	'live_ticket_amount',		'',	'Přijaté tikety částka (live)',		0,	(@idPrekladyMax := @idPrekladyMax+1) ),
(1,	'live_collect_ticket_count',		'',	'Vyplacené tikety částka (live)',		0,	(@idPrekladyMax := @idPrekladyMax+1) ),

(1,	'point_ticket_count',		'',	'Počet ticketů (bodové)',		0,	(@idPrekladyMax := @idPrekladyMax+1) ),
(1,	'point_ticket_amount',		'',	'Vyplacená částka (bodové tickety)',		0,	(@idPrekladyMax := @idPrekladyMax+1) ),
(1,	'point_storno_ticket_count',		'',	'Počet stornovaných tiketů (bodové)',		0,	(@idPrekladyMax := @idPrekladyMax+1) ),
(1,	'point_storno_ticket_amount',		'',	'Zamítnuté- bodové tickety (částka)',		0,	(@idPrekladyMax := @idPrekladyMax+1) );

COMMIT;