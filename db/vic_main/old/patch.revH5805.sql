start transaction;
set @a=(SELECT MAX(preklad_id) FROM vic_main.preklady);

INSERT INTO `preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`, `preklad_id`) VALUES
(1, 'filled', '', 'Vyplněno', 1, @a + 1),
(2, 'filled', '', 'Filled', 1, @a + 1),
(16, 'filled', '', 'Vyplnené', 1, @a + 1),
(1, 'no_filled', '', 'Nevyplněno', 1, @a + 2),
(2, 'no_filled', '', 'No filled', 1, @a + 2),
(16, 'no_filled', '', 'Nevyplnené', 1, @a + 2);

commit;
