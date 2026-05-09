START TRANSACTION;

-- aktualni maximalni preklad_id
SELECT @idPrekladyMax := MAX(preklad_id) FROM vic_main.preklady;

INSERT INTO `vic_main`.`preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`, `preklad_id`)
            VALUES (1, 'sms_user_withdraw_cash',    '','Dobry den, penize jsou pripravene k vyzvednuti na pobocce. S pozdravem a pranim stesti ve hre, tym BetService', 0, (@idPrekladyMax := @idPrekladyMax+1) );

COMMIT;