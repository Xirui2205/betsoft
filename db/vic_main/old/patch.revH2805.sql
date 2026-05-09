start transaction;
set @a=(SELECT MAX(preklad_id) FROM vic_main.preklady);

INSERT INTO vic_main.preklady (`lang_id`, `index_pole`, `short_text`, `text`, `translate`, `preklad_id`) VALUES
(1, 'back_to_transactions', '', 'Zpět na přehled transakcí', 1, @a + 1),
(2, 'back_to_transactions', '', '', 1, @a + 1),
(16, 'back_to_transactions', '', '', 1, @a + 1);

commit;
