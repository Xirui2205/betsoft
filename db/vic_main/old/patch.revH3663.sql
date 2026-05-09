start transaction;
set @a=(SELECT MAX(preklad_id) FROM vic_main.preklady);

INSERT INTO `preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`, `preklad_id`) VALUES 
(1, 'error_check_user', '', 'Tento uživatel v systému již existuje.', 0, @a + 1),
(2, 'error_check_user', '', 'The user in the system already exists.', 0, @a + 1),
(16, 'error_check_user', '', '', 0, @a + 1),
(1, 'error_form', '', 'Nastala chyba, opakujte akci.', 0, @a + 2),
(2, 'error_form', '', 'An error has occurred, please try again.', 0, @a + 2),
(16, 'error_form', '', '', 0, @a + 2);

commit;
