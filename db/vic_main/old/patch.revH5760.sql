start transaction;
set @a=(SELECT MAX(preklad_id) FROM vic_main.preklady);

INSERT INTO `preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`, `preklad_id`) VALUES
(1, 'on', '', 'na', 1, @a + 1),
(2, 'on', '', 'on', 1, @a + 1),
(16, 'on', '', 'sk', 1, @a + 1);

commit;
