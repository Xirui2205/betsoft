start transaction;
set @a=(SELECT MAX(preklad_id) FROM vic_main.preklady);

INSERT INTO `preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`, `preklad_id`) VALUES
(1, 'Others', NULL, 'Ostatní výsledky', 0, @a + 1),
(2, 'Others', NULL, 'Other results', 0, @a + 1),
(16, 'Others', NULL, '', 0, @a + 1);

commit;
