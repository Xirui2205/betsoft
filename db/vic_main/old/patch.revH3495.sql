start transaction;
set @a=(SELECT MAX(preklad_id) FROM vic_main.preklady);

INSERT INTO vic_main.preklady (`lang_id`, `index_pole`, `short_text`, `text`, `translate`, `preklad_id`) VALUES
(1, 'limit_amount', '', 'Částka limitu', 0, @a + 1),
(2, 'limit_amount', '', 'Limit amount', 0, @a + 1),
(16, 'limit_amount', '', '', 0, @a + 1),
(1, 'time_from', '', 'Čas od', 0, @a + 2),
(2, 'time_from', '', 'Time from', 0, @a + 2),
(16, 'time_from', '', '', 0, @a + 2),
(1, 'time_to', '', 'Čas do', 0, @a + 3),
(2, 'time_to', '', 'Time to', 0, @a + 3),
(16, 'time_to', '', '', 0, @a + 3),
(1, 'setting_limits', '', 'Nastavení limitů', 0, @a + 4),
(2, 'setting_limits', '', 'Setting limits', 0, @a + 4),
(16, 'setting_limits', '', '', 0, @a + 4),
(1, 'setting_limits_info', '', 'Zde si můžete nastavit limit na sázení zadáním částky limitu a období, ve kterém má být limit aktivní. Maximální délka období je 30 dní. Nastavení limitu je nevratné. Po uplynutí doby limitu je možno nastavit limit znovu.', 0, @a + 5),
(2, 'setting_limits_info', '', '', 0, @a + 5),
(16, 'setting_limits_info', '', '', 0, @a + 5),
(1, 'actual_limits_info', '', 'Momentálně již máte nastaven svůj limit, který vypší %s. Částka počítaná v období tohoto limitu je %s.', 0, @a + 6),
(2, 'actual_limits_info', '', '', 0, @a + 6),
(16, 'actual_limits_info', '', '', 0, @a + 6),
(1, 'date must be greater than \'%value%\'', '', 'Datum musí být větší než \'%value%\'', 0, @a + 7),
(2, 'date must be greater than \'%value%\'', '', 'Date must be greater then \'%date%\'', 0, @a + 7),
(16, 'date must be greater than \'%value%\'', '', '', 0, @a + 7),
(1, 'date must be lower than \'%value%\'', '', 'Datum musí být menší než \'%value%\'', 0, @a + 8),
(2, 'date must be lower than \'%value%\'', '', 'Date must be lower then \'%date%\'', 0, @a + 8),
(16, 'date must be lower than \'%value%\'', '', '', 0, @a + 8),
(1, 'limit_insert_ok', '', 'Limit na částku \'%s\', platný do \'%s\', byl úspěšně nastaven.', 0, @a + 9),
(2, 'limit_insert_ok', '', '', 0, @a + 9),
(16, 'limit_insert_ok', '', '', 0, @a + 9);

commit;
