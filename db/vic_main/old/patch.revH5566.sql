UPDATE  `vic_main`.`preklady` SET  `text` =  'LIVE tikety' WHERE  `preklady`.`lang_id` =1 AND  `preklady`.`index_pole` =  'my_live_ticket';
UPDATE  `vic_main`.`preklady` SET  `text` =  'Šťastná hodina' WHERE  `preklady`.`lang_id` =1 AND  `preklady`.`index_pole` =  'visit_happy_hour';
insert into preklady(lang_id, index_pole, text) values(1, "entry_bonus_left", "Na prosázení zbývá");
insert into preklady(lang_id, index_pole, text) values(1, "happy_hour_not_active", "Štastná hodina není právě aktivní");