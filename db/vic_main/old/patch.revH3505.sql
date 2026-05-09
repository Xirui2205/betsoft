start transaction;
set @a=(SELECT MAX(preklad_id) FROM vic_main.preklady);

INSERT INTO `preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`, `preklad_id`) VALUES
(1, 'ticket_er_32', NULL, 'Byl překročen Vámi nastavený uživatelský limit {0}{1}.', 0, @a + 1),
(2, 'ticket_er_32', NULL, 'You\'ve exceeded the limit set by you the user {0} {1}.', 0, @a + 1),
(16, 'ticket_er_32', NULL, '', 0, @a + 1);

commit;
