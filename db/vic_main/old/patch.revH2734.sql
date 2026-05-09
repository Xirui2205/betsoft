start transaction;
set @a=(SELECT MAX(preklad_id) FROM vic_main.preklady);

INSERT INTO vic_main.preklady (`lang_id`, `index_pole`, `short_text`, `text`, `translate`, `preklad_id`) VALUES
(1, 'overall_state_user_accounts', '', 'Celkový stav na účtech uživatelů', 1, @a + 1),
(2, 'overall_state_user_accounts', '', '', 1, @a + 1),
(16, 'overall_state_user_accounts', '', '', 1, @a + 1);

commit;
