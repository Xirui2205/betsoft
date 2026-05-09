-- aktualni maximalni preklad_id
SELECT @idPrekladyMax := MAX(preklad_id) FROM preklady;

INSERT INTO `preklady` (`lang_id`, `index_pole`,
`short_text`, `text`, `translate`, `preklad_id`) VALUES
(1,	'activation_authorized_title',		'',	'Aktivace účtu proběhla úspěšně',		0,	(@idPrekladyMax := @idPrekladyMax+1) ),
(1,	'activation_authorized_text',		'',	'Děkujeme Vám, Váš účet je aktivován. Nyní se můžete přihlásit, vložit peníze a začít sázet.',		0,	(@idPrekladyMax := @idPrekladyMax+1) ),
(1,	'activation_already_authorized_title',		'',	'Aktivace účtu již proběhla',		0,	(@idPrekladyMax := @idPrekladyMax+1) ),
(1,	'activation_email_1',		'',	'Dobrý den',		0,	(@idPrekladyMax := @idPrekladyMax+1) ),
(1,	'activation_email_2',		'',	'pro aktivaci účtu prosím klikněte na následující odkaz',		0,	(@idPrekladyMax := @idPrekladyMax+1) ),
(1,	'activation_email_3',		'',	'Děkujeme Vám a přejeme Vám hodně štěstí ve hře!',		0,	(@idPrekladyMax := @idPrekladyMax+1) ),
(1,	'activation_email_subject',		'',	'Aktivace účtu',		0,	(@idPrekladyMax := @idPrekladyMax+1) ),
(1,	'forbidden_title',		'',	'Nepovolená operace',		0,	(@idPrekladyMax := @idPrekladyMax+1) ),
(1,	'forbidden_text',		'',	'Bohužel, k tomuto požadavku nemáte oprávnění.',		0,	(@idPrekladyMax := @idPrekladyMax+1) );