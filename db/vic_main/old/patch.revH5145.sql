start transaction;
set @a=(SELECT MAX(preklad_id) FROM vic_main.preklady);

INSERT INTO `preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`, `preklad_id`) VALUES
(1, 'date_from_payout', '', 'Datum vyplacení od', 1, @a + 1),
(2, 'date_from_payout', '', 'Date from payout', 1, @a + 1),
(16, 'date_from_payout', '', 'Dátum vyplatenia od', 1, @a + 1),
(1, 'date_to_payout', '', 'Datum vyplacení do', 1, @a + 2),
(2, 'date_to_payout', '', 'Date to payout', 1, @a + 2),
(16, 'date_to_payout', '', 'Dátum vyplatenia do', 1, @a + 2),
(1, 'provision', '', 'Provize', 1, @a + 3),
(2, 'provision', '', 'Provision', 1, @a + 3),
(16, 'provision', '', 'Provize', 1, @a + 3);

commit;
