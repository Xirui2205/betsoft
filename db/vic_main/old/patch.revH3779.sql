start transaction;
set @a=(SELECT MAX(preklad_id) FROM vic_main.preklady);

INSERT INTO `preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`, `preklad_id`) VALUES 
(1, ' risk_limit_exceeded', '', 'Risk limit překročen', 0, @a + 1),
(2, ' risk_limit_exceeded', '', 'Risk limit exceeded', 0, @a + 1),
(16, ' risk_limit_exceeded', '', '', 0, @a + 1);

commit;
