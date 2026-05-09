start transaction;
set @a=(SELECT MAX(preklad_id) FROM vic_main.preklady);

INSERT INTO `preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`, `preklad_id`) VALUES
(1, 'users_balance_body_text', '', 'Přehled výhernosti hráčů na internetu v přiloženém csv za měsíc', 1, @a + 1),
(2, 'users_balance_body_text', '', '', 1, @a + 1),
(16, 'users_balance_body_text', '', '', 1, @a + 1),
(1, 'users_balance_monthly', '', 'Výhernosti hráčů na internetu', 1, @a + 2),
(2, 'users_balance_monthly', '', '', 1, @a + 2),
(16, 'users_balance_monthly', '', '', 1, @a + 2);

commit;
