START TRANSACTION;

INSERT INTO `vic_main`.`preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`)
 VALUES (1, 'ticket_er_29', NULL, 'Max.počet za 24 hod překročen: kombinace {0}', '0');
SET @pid = LAST_INSERT_ID();
INSERT INTO `vic_main`.`preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`, `preklad_id`)
 VALUES (2, 'ticket_er_29', NULL, 'Max.count for 24 hours exceeded: combination {0}', '0', @pid);
INSERT INTO `vic_main`.`preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`, `preklad_id`)
 VALUES (16, 'ticket_er_29', NULL, '', '0', @pid);

INSERT INTO `vic_main`.`translate_missing` (`translate_key`,`lang_id`,`controller`,`action`,`status`) VALUES
('ticket_er_29', 1, 'DICTIONARY', 'TICKET', 'missing'),
('ticket_er_29', 2, 'DICTIONARY', 'TICKET', 'missing'),
('ticket_er_29', 16, 'DICTIONARY', 'TICKET', 'missing');

COMMIT;
