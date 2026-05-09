ALTER TABLE  `vic_main`.`jazyky` ADD  `collation` VARCHAR( 100 ) NOT NULL;


UPDATE  `vic_main`.`jazyky` SET  `collation` =  'utf8_czech_ci' WHERE  `jazyky`.`lang_id` =1;
UPDATE  `vic_main`.`jazyky` SET  `collation` =  'utf8_general_ci' WHERE  `jazyky`.`lang_id` =2;
UPDATE  `vic_main`.`jazyky` SET  `collation` =  'utf8_slovak_ci' WHERE  `jazyky`.`lang_id` =16;
