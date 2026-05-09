START TRANSACTION;

INSERT INTO `vic_main`.`preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`)
 VALUES (1, 'ticket_er_30', NULL, 'Min.vklad na kombinaci: {0}', '0');
SET @pid = LAST_INSERT_ID();
INSERT INTO `vic_main`.`preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`, `preklad_id`)
 VALUES (2, 'ticket_er_30', NULL, 'Min.stake for combination: {0}', '0', @pid);
INSERT INTO `vic_main`.`preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`, `preklad_id`)
 VALUES (16, 'ticket_er_30', NULL, '', '0', @pid);

INSERT INTO `vic_main`.`translate_missing` (`translate_key`,`lang_id`,`controller`,`action`,`status`) VALUES
('ticket_er_30', 1, 'DICTIONARY', 'TICKET', 'missing'),
('ticket_er_30', 2, 'DICTIONARY', 'TICKET', 'missing'),
('ticket_er_30', 16, 'DICTIONARY', 'TICKET', 'missing');

COMMIT;
