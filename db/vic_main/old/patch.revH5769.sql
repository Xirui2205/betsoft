start transaction;
set @a=(SELECT MAX(preklad_id) FROM vic_main.preklady);

INSERT INTO `preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`, `preklad_id`) VALUES
(1, 'mobile_betting', '', 'Mobilní sázení', 1, @a + 1),
(2, 'mobile_betting', '', 'Mobile betting', 1, @a + 1),
(16, 'mobile_betting', '', 'Mobilné stávkovanie', 1, @a + 1);

commit;
