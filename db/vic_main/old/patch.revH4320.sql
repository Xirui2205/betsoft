START TRANSACTION;

set @a=(SELECT MAX(preklad_id) FROM vic_main.preklady);

INSERT INTO `preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`, `preklad_id`) VALUES
(1, 'nextstep_ulice', '', 'Ulice', 1, @a + 1),
(1, 'nextstep_popisne', '', 'Číslo popisné', 1, @a + 2),
(1, 'nextstep_psc', '', 'PSČ', 1, @a + 3),
(1, 'nextstep_mesto', '', 'Město', 1, @a + 4),
(1, 'start_betting', '', 'Vsaďte si', 1, @a + 5);


REPLACE INTO `preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`, `preklad_id`) VALUES
(1, 'reg_op', '', 'Číslo OP/č. pasu', 1, 2585);

COMMIT;
