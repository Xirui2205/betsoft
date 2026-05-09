start transaction;
set @a=(SELECT MAX(preklad_id) FROM vic_main.preklady);

INSERT INTO `preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`, `preklad_id`) VALUES 
(1, 'monthly_total_report', '', 'Měsíční celkový report', 0, @a + 1),
(2, 'monthly_total_report', '', 'Monthly total report', 0, @a + 1),
(16, 'monthly_total_report', '', '', 0, @a + 1);

commit;
