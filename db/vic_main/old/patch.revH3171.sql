start transaction;
set @a=(SELECT MAX(preklad_id) FROM vic_main.preklady);

INSERT INTO vic_main.preklady (`lang_id`, `index_pole`, `short_text`, `text`, `translate`, `preklad_id`) VALUES
(1, 'count', '', 'Počet', 1, @a + 1),
(2, 'count', '', 'Count', 1, @a + 1),
(16, 'count', '', '', 1, @a + 1),
(1, 'bet_status', '', 'Status sázky', 1, @a + 2),
(2, 'bet_status', '', 'Bet status', 1, @a + 2),
(16, 'bet_status', '', '', 1, @a + 2);

commit;
