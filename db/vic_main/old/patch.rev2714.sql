-- it is recommended to run trans.php after applying this patch

START TRANSACTION;
UPDATE `vic_main`.`preklady` SET `text`='Maximální sázka: {0}' WHERE `index_pole`='ticket_er_2' AND `lang_id`=1;
UPDATE `vic_main`.`preklady` SET `text`='Max amount: {0}' WHERE `index_pole`='ticket_er_2' AND `lang_id`=2;
UPDATE `vic_main`.`preklady` SET `text`='Maximálna sázka: {0}' WHERE `index_pole`='ticket_er_2' AND `lang_id`=16;
UPDATE `vic_main`.`preklady` SET `text`='Kurz byl změněn: {0}' WHERE `index_pole`='ticket_er_12' AND `lang_id`=1;
UPDATE `vic_main`.`preklady` SET `text`='Rate changed: {0}' WHERE `index_pole`='ticket_er_12' AND `lang_id`=2;
UPDATE `vic_main`.`preklady` SET `text`='Rate changed: {0}' WHERE `index_pole`='ticket_er_12' AND `lang_id`=16;
UPDATE `vic_main`.`preklady` SET `text`='AKO sázka minumum sázek: {0}' WHERE `index_pole`='ticket_er_13' AND `lang_id`=1;
UPDATE `vic_main`.`preklady` SET `text`='AKO bet - min bet count: {0}' WHERE `index_pole`='ticket_er_13' AND `lang_id`=2;
UPDATE `vic_main`.`preklady` SET `text`='AKO bet - min bet count: {0}' WHERE `index_pole`='ticket_er_13' AND `lang_id`=16;
UPDATE `vic_main`.`preklady` SET `text`='Maximální sázka: {0}' WHERE `index_pole`='ticket_othersum' AND `lang_id`=1;
UPDATE `vic_main`.`preklady` SET `text`='Max amount: {0}' WHERE `index_pole`='ticket_othersum' AND `lang_id`=2;
UPDATE `vic_main`.`preklady` SET `text`='Maximálna sázka: {0}' WHERE `index_pole`='ticket_othersum' AND `lang_id`=16;
COMMIT;
