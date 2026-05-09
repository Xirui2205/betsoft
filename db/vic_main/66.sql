UPDATE `preklady` SET `text` = 'Formulář obsahuje chyby, data nebyla uložena.' WHERE `text` = 'Registrační formulář obsahuje chyby';

UPDATE `preklady` SET
`lang_id` = '1',
`index_pole` = 'homepage',
`short_text` = '',
`text` = 'Úvodní stránka',
`translate` = '1',
`preklad_id` = '5570'
WHERE `lang_id` = '1' AND `index_pole` = 'homepage' AND `index_pole` = 'homepage' COLLATE utf8_bin;

SELECT @idPrekladyMax := MAX(preklad_id) FROM preklady;

INSERT INTO `preklady` (`lang_id`, `index_pole`,
`short_text`, `text`, `translate`, `preklad_id`) VALUES
(1,	'your_current_balance_br',		'',	'Váš<br>aktuální zůstatek',		0,	(@idPrekladyMax := @idPrekladyMax+1) ),
(1,	'save',		'',	'Uložit',		0,	(@idPrekladyMax := @idPrekladyMax+1) ),
(1,	'point_acount_limits',		'',	'Limity',		0,	(@idPrekladyMax := @idPrekladyMax+1) ),
(1,	'voucher_handle',		'',	'Kód voucheru',		0,	(@idPrekladyMax := @idPrekladyMax+1) ),
(1,	'voucher_used_successfuly',		'',	'Kód voucheru byl úspěšně uplatněn.',		0,	(@idPrekladyMax := @idPrekladyMax+1) ),
(1,	'(webservice_voucher::validate) voucher is not valid, or out of date.',		'',	'Kód voucheru není platný nebo již byl uplatněn.',		0,	(@idPrekladyMax := @idPrekladyMax+1) ),
(1,	'(webservice_voucher::validate) voucher is already used.',		'',	'Kód voucheru již byl uplatněn.',		0,	(@idPrekladyMax := @idPrekladyMax+1) ),
(1,	'voucher_screen_is_blocked',		'',	'Nyní není možné uplatnit další kód.',		0,	(@idPrekladyMax := @idPrekladyMax+1) ),
(1,	'alert-success-msg',		'',	'Úspěch!',		0,	(@idPrekladyMax := @idPrekladyMax+1) ),
(1,	'alert-warning-msg',		'',	'Varování!',		0,	(@idPrekladyMax := @idPrekladyMax+1) ),
(1,	'ticket_detail',		'',	'Detail tiketu',		0,	(@idPrekladyMax := @idPrekladyMax+1) ),
(1,	'ticket_competition',		'',	'Soutěž',		0,	(@idPrekladyMax := @idPrekladyMax+1) );
