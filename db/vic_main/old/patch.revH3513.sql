start transaction;
set @a=(SELECT MAX(preklad_id) FROM vic_main.preklady);

INSERT INTO `preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`, `preklad_id`) VALUES
(1, 'amount_limit_reached', NULL, 'Částka limitu již byla dosažena. Další sázení již nebude možné až do vypršení platnosti limitu.', 0, @a + 1),
(2, 'amount_limit_reached', NULL, 'The amount of the limit has already been reached. Additional betting will not be possible until the expiration limit.', 0, @a + 1),
(16, 'amount_limit_reached', NULL, '', 0, @a + 1);

commit;
