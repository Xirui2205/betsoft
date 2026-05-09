start transaction;
set @a=(SELECT MAX(preklad_id) FROM vic_main.preklady);

INSERT INTO vic_main.preklady (`lang_id`, `index_pole`, `short_text`, `text`, `translate`, `preklad_id`) VALUES
(1, 'stem-filename', '', 'Název reportu', 1, @a + 1),
(2, 'stem-filename', '', 'Report name', 1, @a + 1),
(16, 'stem-filename', '', '', 1, @a + 1),
(1, 'stem-sendmail', '', 'Posílat email?', 1, @a + 2),
(2, 'stem-sendmail', '', 'Send email?', 1, @a + 2),
(16, 'stem-sendmail', '', '', 1, @a + 2);

commit;
