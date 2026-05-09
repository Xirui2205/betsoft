start transaction;
set @a=(SELECT MAX(preklad_id) FROM vic_main.preklady);

INSERT INTO vic_main.preklady (`lang_id`, `index_pole`, `short_text`, `text`, `translate`, `preklad_id`) VALUES
(1, 'ticket_of_month', '', 'Tiket měsíce', 1, @a + 1),
(2, 'ticket_of_month', '', '', 1, @a + 1),
(16, 'ticket_of_month', '', '', 1, @a + 1);

INSERT INTO vic_main.preklady (`lang_id`, `index_pole`, `short_text`, `text`, `translate`, `preklad_id`) VALUES
(1, 'ticket_win_of_month', '', 'Sázkař měsíce', 1, @a + 2),
(2, 'ticket_win_of_month', '', '', 1, @a + 2),
(16, 'ticket_win_of_month', '', '', 1, @a + 2);

commit;
