START TRANSACTION;

-- aktualni maximalni preklad_id
SELECT @idPrekladyMax := MAX(preklad_id) FROM vic_main.preklady;

INSERT INTO `vic_main`.`preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`, `preklad_id`)
            VALUES (1, 'email_user_withdraw_cash_subject',    '','Informace o vyzvednuti peněz', 0, (@idPrekladyMax := @idPrekladyMax+1) ),
                   (1, 'email_user_withdraw_cash',		'',
            	      'Dobrý den,

peníze jsou připravené k vyzvednuti na pobočce

S pozdravem a přáním štěstí ve hře,
tým BetService',
                		0, (@idPrekladyMax := @idPrekladyMax+1));

COMMIT;