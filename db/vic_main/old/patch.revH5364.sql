START TRANSACTION;

INSERT INTO `database_patch`(`revision`, `not_reapplicable`, `note`) VALUES
 ('H5364', 1, 'preklady pro hlasky v metode Webservice_User::getByLoginNameOrCardNumber(loginNameOrCardNumber) (mantis:234)');

-- aktualni maximalni preklad_id
SELECT @idPrekladyMax := MAX(preklad_id) FROM preklady;

INSERT INTO `preklady` (`lang_id`, `index_pole`,
`short_text`, `text`, `translate`, `preklad_id`) VALUES
(1,	'branch_login_wrong_input',		'',	'Pole s přezdívkou / číslem karty nebylo správně nastaveno.',		0,	(@idPrekladyMax := @idPrekladyMax+1) ),
(1,	'branch_login_wrong_card_number',		'',	'Uživatel s daným číslem karty nebyl nalezen.',		0,	(@idPrekladyMax := @idPrekladyMax+1) ),
(1,	'branch_login_wrong_login_name',		'',	'Uživatel s danou přezdívkou nebyl nalezen.',		0,	(@idPrekladyMax := @idPrekladyMax+1) ),
(1,	'branch_login_forbidden_user',		'',	'Tomuto uživateli byl odepřen přístup k přihlášení.',		0,	(@idPrekladyMax := @idPrekladyMax+1) ),
(1,	'branch_login_user_not_activated',		'',	'Tento uživatel nemá dokončenou registraci.',		0,	(@idPrekladyMax := @idPrekladyMax+1) );

COMMIT;