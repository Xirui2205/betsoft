start transaction;
set @a=(SELECT MAX(preklad_id) FROM vic_main.preklady);

INSERT INTO `preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`, `preklad_id`) VALUES 
(1, 'operation_time', '', 'Vánoční provozní doba', 1, @a + 1),
(2, 'operation_time', '', 'Christmas opening hours', 1, @a + 1),
(16, 'operation_time', '', 'Vianočná prevádzková doba', 1, @a + 1);

commit;
