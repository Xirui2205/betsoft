start transaction;
set @a=(SELECT MAX(preklad_id) FROM vic_main.preklady);

INSERT INTO `preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`, `preklad_id`) VALUES
(1, '\'%hostname%\' is no valid hostname for email address \'%value%\'', '', '\'%hostname%\' není platný název hosta pro e-mailovou adresu \'%value%\'', 1, @a + 1),
(2, '\'%hostname%\' is no valid hostname for email address \'%value%\'', '', '\'%hostname%\' is no valid hostname for email address \'%value%\'', 1, @a + 1),
(16, '\'%hostname%\' is no valid hostname for email address \'%value%\'', '', '\'%hostname%\' nie je platný názov hosta pre e-mailovú adresu \'%hodnota%\'', 1, @a + 1),
(1, '\'%value%\' appears to be a DNS hostname but cannot match TLD against known list', '', 'Název hostname \'%value%\' neodpovídá známým hostname z TLD seznamu', 1, @a + 2),
(2, '\'%value%\' appears to be a DNS hostname but cannot match TLD against known list', '', '\'%value%\' appears to be a DNS hostname but cannot match TLD against known list', 1, @a + 2),
(16, '\'%value%\' appears to be a DNS hostname but cannot match TLD against known list', '', 'Názov hostname \'%value%\' nezodpovedá známym hostname z TLD zoznamu', 1, @a + 2);

commit;
