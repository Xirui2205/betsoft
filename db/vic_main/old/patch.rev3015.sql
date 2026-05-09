START TRANSACTION;
INSERT INTO `vic_main`.`preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`)
 VALUES ('1', 'ticket_er_22', NULL, 'Již je schvalován jiný tiket', '0');
SET @pid = LAST_INSERT_ID();
INSERT INTO `vic_main`.`preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`, `preklad_id`)
 VALUES ('2', 'ticket_er_22', NULL, 'Another ticket is already being confirmed', '0', @pid);
INSERT INTO `vic_main`.`preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`, `preklad_id`)
 VALUES ('16', 'ticket_er_22', NULL, '', '0', @pid);

INSERT INTO `vic_main`.`preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`)
 VALUES ('1', 'ticket_er_23', NULL, 'Tiket již není platný', '0');
SET @pid = LAST_INSERT_ID();
INSERT INTO `vic_main`.`preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`, `preklad_id`)
 VALUES ('2', 'ticket_er_23', NULL, 'Ticket is not valid anymore', '0', @pid);
INSERT INTO `vic_main`.`preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`, `preklad_id`)
 VALUES ('16', 'ticket_er_23', NULL, '', '0', @pid);
COMMIT;
