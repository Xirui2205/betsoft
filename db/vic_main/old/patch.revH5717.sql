start transaction;
set @a=(SELECT MAX(preklad_id) FROM vic_main.preklady);

INSERT INTO `preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`, `preklad_id`) VALUES
(1, 'diff_collect_not_storno_amount', '', 'Rozdíl nezamítnuté a výplata', 1, @a + 1),
(2, 'diff_collect_not_storno_amount', '', '', 1, @a + 1),
(16, 'diff_collect_not_storno_amount', '', '', 1, @a + 1);

commit;
