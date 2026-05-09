START TRANSACTION;

INSERT INTO `vic_main`.`preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`)
 VALUES (1, 'ticket_er_27', NULL, 'Limit byl vyčerpán', '0');
SET @pid = LAST_INSERT_ID();
INSERT INTO `vic_main`.`preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`, `preklad_id`)
 VALUES (2, 'ticket_er_27', NULL, 'Limit was exhausted', '0', @pid);
INSERT INTO `vic_main`.`preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`, `preklad_id`)
 VALUES (16, 'ticket_er_27', NULL, '', '0', @pid);

UPDATE `vic_main`.`preklady` SET `text`='Maximální sázka: {0}{1}' WHERE `index_pole`='ticket_er_2' AND `lang_id`=1;
UPDATE `vic_main`.`preklady` SET `text`='Max amount: {0}{1}' WHERE `index_pole`='ticket_er_2' AND `lang_id`=2;
UPDATE `vic_main`.`preklady` SET `text`='Maximálna sázka: {0}{1}' WHERE `index_pole`='ticket_er_2' AND `lang_id`=16;

COMMIT;
