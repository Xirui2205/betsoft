START TRANSACTION;
UPDATE `vic_main`.`preklady` SET `text`='Invalid bet' WHERE `index_pole`='ticket_er_7' AND `lang_id`=1;
UPDATE `vic_main`.`preklady` SET `text`='Sázka není platná' WHERE `index_pole`='ticket_er_7' AND `lang_id`=2;
UPDATE `vic_main`.`preklady` SET `text`='Invalid bet' WHERE `index_pole`='ticket_er_7' AND `lang_id`=16;
COMMIT;
