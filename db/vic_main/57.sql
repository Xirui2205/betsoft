
UPDATE `preklady` SET `text` = 'Heslo' WHERE `text` = 'Heslo (min. 6 znaků)';
UPDATE `preklady` SET `text` = 'Adresa' WHERE `text` = 'Adresa (ulice číslo, město,...)';
UPDATE `preklady` SET `text` = 'Telefon' WHERE `text` = 'Telefon (bez předvolby)';
UPDATE `preklady` SET `text` = 'Heslo' WHERE `text` = 'Heslo (min. 6 znaků)';
UPDATE `preklady` SET `text` = 'Odebírat novinky e-mailem' WHERE `text` = 'Chci odebírat novinky';
UPDATE `preklady` SET `text` = 'Souhlasím s' WHERE `text` = 'Souhlasím s obchodními podmínkami';

SELECT @idPrekladyMax := MAX(preklad_id) FROM preklady;
INSERT INTO `preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`, `preklad_id`) VALUES
(1,	'reg_address_placeholder',		'',	'Ulice číslo, město,...',		0,	(@idPrekladyMax := @idPrekladyMax+1) ),
(1,	'reg_terms_2',		'',	'obchodními podmínkami',		0,	(@idPrekladyMax := @idPrekladyMax+1) ),
(1,	'reg_phone_placeholder',		'',	'Telefon (bez předvolby)',		0,	(@idPrekladyMax := @idPrekladyMax+1) ),
(1,	'invalid_address_alert',		'',	'Neplatná adresa.',		0,	(@idPrekladyMax := @idPrekladyMax+1) );

UPDATE `preklady` SET `text` = 'Odebírat novinky e-mailem' WHERE `lang_id` = '1' AND `index_pole` = 'reg_news' COLLATE utf8_bin;