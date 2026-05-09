start transaction;
set @a=(SELECT MAX(preklad_id) FROM vic_main.preklady);

INSERT INTO vic_main.preklady (`lang_id`, `index_pole`, `short_text`, `text`, `translate`, `preklad_id`) VALUES
(1, 'Competition', '', 'Soutěže', 1, @a + 1),
(2, 'Competition', '', '', 1, @a + 1),
(16, 'Competition', '', '', 1, @a + 1);

INSERT INTO vic_main.preklady (`lang_id`, `index_pole`, `short_text`, `text`, `translate`, `preklad_id`) VALUES
(1, 'cancell_ticket', '', 'Zakázat ticket v soutěži', 1, @a + 2),
(2, 'cancell_ticket', '', '', 1, @a + 2),
(16, 'cancell_ticket', '', '', 1, @a + 2);

INSERT INTO vic_main.preklady (`lang_id`, `index_pole`, `short_text`, `text`, `translate`, `preklad_id`) VALUES
(1, 'allow_ticket', '', 'Povolit ticket v soutěži', 1, @a + 3),
(2, 'allow_ticket', '', '', 1, @a + 3),
(16, 'allow_ticket', '', '', 1, @a + 3);

INSERT INTO vic_main.preklady (`lang_id`, `index_pole`, `short_text`, `text`, `translate`, `preklad_id`) VALUES
(1, 'question_allow_ticket', '', 'Chcete opravdu povolit tento ticket?', 1, @a + 4),
(2, 'question_allow_ticket', '', '', 1, @a + 4),
(16, 'question_allow_ticket', '', '', 1, @a + 4);

INSERT INTO vic_main.preklady (`lang_id`, `index_pole`, `short_text`, `text`, `translate`, `preklad_id`) VALUES
(1, 'question_cancell_ticket', '', 'Chcete opravdu zakázat tento ticket?', 1, @a + 5),
(2, 'question_cancell_ticket', '', '', 1, @a + 5),
(16, 'question_cancell_ticket', '', '', 1, @a + 5);

ALTER TABLE vic_main.ticket_game_result ADD `cancelled` TINYINT NOT NULL DEFAULT '0' COMMENT 'priznak o odstraneni tiketu ze souteze';

commit;
