start transaction;
set @a=(SELECT MAX(preklad_id) FROM vic_main.preklady);

INSERT INTO vic_main.preklady (`lang_id`, `index_pole`, `short_text`, `text`, `translate`, `preklad_id`) VALUES
(1, 'betting_statistics', '', 'Statistiky sázek', 1, @a + 1),
(2, 'betting_statistics', '', 'Betting statistics', 1, @a + 1),
(16, 'betting_statistics', '', '', 1, @a + 1),
(1, 'time_period', '', 'Časové období', 1, @a + 2),
(2, 'time_period', '', 'Time period', 1, @a + 2),
(16, 'time_period', '', '', 1, @a + 2),
(1, 'main_bet_id', '', 'ID hlavní sázky', 1, @a + 3),
(2, 'main_bet_id', '', 'Main bet ID', 1, @a + 3),
(16, 'main_bet_id', '', '', 1, @a + 3),
(1, 'main_bet_alias', '', 'Alias hlavní sázky', 1, @a + 4),
(2, 'main_bet_alias', '', 'Main bet alias', 1, @a + 4),
(16, 'main_bet_alias', '', '', 1, @a + 4),
(1, 'no_childbets', '', 'Žádné podsázky', 1, @a + 5),
(2, 'no_childbets', '', 'No childbets', 1, @a + 5),
(16, 'no_childbets', '', '', 1, @a + 5);

commit;
