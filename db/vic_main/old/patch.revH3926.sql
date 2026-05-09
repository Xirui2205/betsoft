start transaction;
set @a=(SELECT MAX(preklad_id) FROM vic_main.preklady);

INSERT INTO `preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`, `preklad_id`) VALUES
(1, 'day \'%value%\' is not valid for the choden month.', '', 'Den \'%value%\' není platný pro zvolený měsíc.', 1, @a + 1),
(2, 'day \'%value%\' is not valid for the choden month.', '', 'Day \'%value%\' is not valid for the choden month.', 1, @a + 1),
(16, 'day \'%value%\' is not valid for the choden month.', '', 'Deň \'%value%\' nie je platný pre zvolený mesiac.', 1, @a + 1),
(1, 'you must select a month.', '', 'Zvolte měsíc.', 1, @a + 2),
(2, 'you must select a month.', '', 'You must select a month.', 1, @a + 2),
(16, 'you must select a month.', '', 'Zvoľte mesiac.', 1, @a + 2),
(1, 'you must select year.', '', 'Zvolte rok.', 1, @a + 3),
(2, 'you must select year.', '', 'You must select year.', 1, @a + 3),
(16, 'you must select year.', '', 'Zvoľte rok.', 1, @a + 3);

commit;
