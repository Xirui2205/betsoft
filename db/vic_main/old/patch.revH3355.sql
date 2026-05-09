start transaction;
set @a=(SELECT MAX(preklad_id) FROM vic_main.preklady);

INSERT INTO vic_main.preklady (`lang_id`, `index_pole`, `short_text`, `text`, `translate`, `preklad_id`) VALUES
(1, 'total_tickets', '', 'Všechny tikety', 1, @a + 1),
(2, 'total_tickets', '', 'All tickets', 1, @a + 1),
(16, 'total_tickets', '', '', 1, @a + 1),
(1, 'internet_tickets', '', 'Tikety z internetu', 1, @a + 2),
(2, 'internet_tickets', '', 'Internet tickets', 1, @a + 2),
(16, 'internet_tickets', '', '', 1, @a + 2),
(1, 'branch_tickets', '', 'Tikety z poboček', 1, @a + 3),
(2, 'branch_tickets', '', 'Branch tickets', 1, @a + 3),
(16, 'branch_tickets', '', '', 1, @a + 3);

commit;
