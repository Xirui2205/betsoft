START TRANSACTION;
UPDATE `vic_main`.`preklady` SET `text`='Minimální vklad na kupon: {0}' WHERE `index_pole`='ticket_er_17' AND `lang_id`=1;
UPDATE `vic_main`.`preklady` SET `text`='Minimal stake per ticket: {0}' WHERE `index_pole`='ticket_er_17' AND `lang_id`=2;

DELETE FROM `vic_main`.`preklady` WHERE `index_pole` IN ('ticket_er_19','ticket_er_20');
INSERT INTO `vic_main`.`preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`)
 VALUES (1, 'ticket_er_19', NULL, 'Nulový vklad', '0');
SET @pid = LAST_INSERT_ID();
INSERT INTO `vic_main`.`preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`, `preklad_id`)
 VALUES (2, 'ticket_er_19', NULL, 'Zero stake', '0', @pid);
INSERT INTO `vic_main`.`preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`, `preklad_id`)
 VALUES (16, 'ticket_er_19', NULL, '', '0', @pid);

INSERT INTO `vic_main`.`preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`)
 VALUES (1, 'ticket_er_20', NULL, 'Nulový počet sázek', '0');
SET @pid = LAST_INSERT_ID();
INSERT INTO `vic_main`.`preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`, `preklad_id`)
 VALUES (2, 'ticket_er_20', NULL, 'Zero count of bet', '0', @pid);
INSERT INTO `vic_main`.`preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`, `preklad_id`)
 VALUES (16, 'ticket_er_20', NULL, 'Zero count of bet', '0', @pid);

COMMIT;
