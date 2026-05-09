START TRANSACTION;

INSERT INTO `vic_main`.`preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`)
 VALUES (1, 'reject', NULL, 'Odmítnout', '0');
SET @pid = LAST_INSERT_ID();
INSERT INTO `vic_main`.`preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`, `preklad_id`)
 VALUES (2, 'reject', NULL, 'Reject', '0', @pid);
INSERT INTO `vic_main`.`preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`, `preklad_id`)
 VALUES (16, 'reject', NULL, '', '0', @pid);

INSERT INTO `vic_main`.`preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`)
 VALUES (1, 'ticket_to_correct', NULL, 'Opravit', '0');
SET @pid = LAST_INSERT_ID();
INSERT INTO `vic_main`.`preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`, `preklad_id`)
 VALUES (2, 'ticket_to_correct', NULL, 'Fix', '0', @pid);
INSERT INTO `vic_main`.`preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`, `preklad_id`)
 VALUES (16, 'ticket_to_correct', NULL, '', '0', @pid);

COMMIT;
