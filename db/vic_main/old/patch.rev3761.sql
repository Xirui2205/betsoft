START TRANSACTION;
UPDATE `vic_main`.`preklady` SET `text`='Max.čistá výhra: {0}'
 WHERE `preklady`.`lang_id`=1 AND `preklady`.`index_pole`='ticket_er_24';
UPDATE `vic_main`.`preklady` SET `text`='Max.netto win: {0}'
 WHERE `preklady`.`lang_id`=2 AND `preklady`.`index_pole`='ticket_er_24';
UPDATE `vic_main`.`preklady` SET `text`=''
 WHERE `preklady`.`lang_id`=16 AND `preklady`.`index_pole`='ticket_er_24';
COMMIT;
