START TRANSACTION;

SET @maxPrekladId = ( SELECT MAX(`preklad_id`) + 1 FROM `vic_main`.`preklady` );
INSERT INTO `vic_main`.`preklady` (`lang_id`,`index_pole`,`text`,`preklad_id`)
VALUES
('1','bet_name','Název sázky', @maxPrekladId),
('2','bet_name','Bet Name', @maxPrekladId),
('16','bet_name','text sk', @maxPrekladId);

SET @maxPrekladId = ( SELECT MAX(`preklad_id`) + 1 FROM `vic_main`.`preklady` );
INSERT INTO `vic_main`.`preklady` (`lang_id`,`index_pole`,`text`,`preklad_id`)
VALUES
('1','bet_type','Typ sázky', @maxPrekladId),
('2','bet_type','Bet Type', @maxPrekladId),
('16','bet_type','text sk', @maxPrekladId);

SET @maxPrekladId = ( SELECT MAX(`preklad_id`) + 1 FROM `vic_main`.`preklady` );
INSERT INTO `vic_main`.`preklady` (`lang_id`,`index_pole`,`text`,`preklad_id`)
VALUES
('1','ticket_no','Číslo tiketu', @maxPrekladId),
('2','ticket_no','Ticket number', @maxPrekladId),
('16','ticket_no','text sk', @maxPrekladId);

SET @maxPrekladId = ( SELECT MAX(`preklad_id`) + 1 FROM `vic_main`.`preklady` );
INSERT INTO `vic_main`.`preklady` (`lang_id`,`index_pole`,`text`,`preklad_id`)
VALUES
('1','hot_bets','Terno sázky', @maxPrekladId),
('2','hot_bets','Hot Bets', @maxPrekladId),
('16','hot_bets','text sk', @maxPrekladId);

SET @maxPrekladId = ( SELECT MAX(`preklad_id`) + 1 FROM `vic_main`.`preklady` );
INSERT INTO `vic_main`.`preklady` (`lang_id`,`index_pole`,`text`,`preklad_id`)
VALUES
('1','event','Událost', @maxPrekladId),
('2','event','Event', @maxPrekladId),
('16','event','text sk', @maxPrekladId);

SET @maxPrekladId = ( SELECT MAX(`preklad_id`) + 1 FROM `vic_main`.`preklady` );
INSERT INTO `vic_main`.`preklady` (`lang_id`,`index_pole`,`text`,`preklad_id`)
VALUES
('1','bet_ends','Konec sázky', @maxPrekladId),
('2','bet_ends','Bet Expires', @maxPrekladId),
('16','bet_ends','text sk', @maxPrekladId);

SET @maxPrekladId = ( SELECT MAX(`preklad_id`) + 1 FROM `vic_main`.`preklady` );
INSERT INTO `vic_main`.`preklady` (`lang_id`,`index_pole`,`text`,`preklad_id`)
VALUES
('1','evaluated_bets','Vyhodnocené sázky', @maxPrekladId),
('2','evaluated_bets','Evaluated Bets', @maxPrekladId),
('16','evaluated_bets','text sk', @maxPrekladId);

SET @maxPrekladId = ( SELECT MAX(`preklad_id`) + 1 FROM `vic_main`.`preklady` );
INSERT INTO `vic_main`.`preklady` (`lang_id`,`index_pole`,`text`,`preklad_id`)
VALUES
('1','maxikombi','AKO/Kombi', @maxPrekladId),
('2','maxikombi','text eng', @maxPrekladId),
('16','maxikombi','AKO/Kombi', @maxPrekladId);

COMMIT;
