START TRANSACTION;

INSERT INTO `vic_main`.`preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`)
 VALUES (1, 'January', NULL, 'Leden', '0');
SET @pid = LAST_INSERT_ID();
INSERT INTO `vic_main`.`preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`, `preklad_id`)
 VALUES (2, 'january', NULL, 'January', '0', @pid);
INSERT INTO `vic_main`.`preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`, `preklad_id`)
 VALUES (16, 'january', NULL, 'Január', '0', @pid);

INSERT INTO `vic_main`.`preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`)
 VALUES (1, 'February', NULL, 'Únor', '0');
SET @pid = LAST_INSERT_ID();
INSERT INTO `vic_main`.`preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`, `preklad_id`)
 VALUES (2, 'February', NULL, 'February', '0', @pid);
INSERT INTO `vic_main`.`preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`, `preklad_id`)
 VALUES (16, 'February', NULL, 'Február', '0', @pid);

INSERT INTO `vic_main`.`preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`)
 VALUES (1, 'March', NULL, 'Březen', '0');
SET @pid = LAST_INSERT_ID();
INSERT INTO `vic_main`.`preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`, `preklad_id`)
 VALUES (2, 'March', NULL, 'March', '0', @pid);
INSERT INTO `vic_main`.`preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`, `preklad_id`)
 VALUES (16, 'March', NULL, 'Marec', '0', @pid);

INSERT INTO `vic_main`.`preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`)
 VALUES (1, 'April', NULL, 'Duben', '0');
SET @pid = LAST_INSERT_ID();
INSERT INTO `vic_main`.`preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`, `preklad_id`)
 VALUES (2, 'April', NULL, 'April', '0', @pid);
INSERT INTO `vic_main`.`preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`, `preklad_id`)
 VALUES (16, 'April', NULL, 'Apríl', '0', @pid);

INSERT INTO `vic_main`.`preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`)
 VALUES (1, 'May', NULL, 'Květen', '0');
SET @pid = LAST_INSERT_ID();
INSERT INTO `vic_main`.`preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`, `preklad_id`)
 VALUES (2, 'May', NULL, 'May', '0', @pid);
INSERT INTO `vic_main`.`preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`, `preklad_id`)
 VALUES (16, 'May', NULL, 'Máj', '0', @pid);

INSERT INTO `vic_main`.`preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`)
 VALUES (1, 'June', NULL, 'Červen', '0');
SET @pid = LAST_INSERT_ID();
INSERT INTO `vic_main`.`preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`, `preklad_id`)
 VALUES (2, 'June', NULL, 'June', '0', @pid);
INSERT INTO `vic_main`.`preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`, `preklad_id`)
 VALUES (16, 'June', NULL, 'Jún', '0', @pid);

INSERT INTO `vic_main`.`preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`)
 VALUES (1, 'July', NULL, 'Červenec', '0');
SET @pid = LAST_INSERT_ID();
INSERT INTO `vic_main`.`preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`, `preklad_id`)
 VALUES (2, 'July', NULL, 'July', '0', @pid);
INSERT INTO `vic_main`.`preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`, `preklad_id`)
 VALUES (16, 'July', NULL, 'Jul', '0', @pid);

INSERT INTO `vic_main`.`preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`)
 VALUES (1, 'August', NULL, 'Srpen', '0');
SET @pid = LAST_INSERT_ID();
INSERT INTO `vic_main`.`preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`, `preklad_id`)
 VALUES (2, 'August', NULL, 'August', '0', @pid);
INSERT INTO `vic_main`.`preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`, `preklad_id`)
 VALUES (16, 'August', NULL, 'August', '0', @pid);

INSERT INTO `vic_main`.`preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`)
 VALUES (1, 'September', NULL, 'Září', '0');
SET @pid = LAST_INSERT_ID();
INSERT INTO `vic_main`.`preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`, `preklad_id`)
 VALUES (2, 'September', NULL, 'September', '0', @pid);
INSERT INTO `vic_main`.`preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`, `preklad_id`)
 VALUES (16, 'September', NULL, 'September', '0', @pid);

INSERT INTO `vic_main`.`preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`)
 VALUES (1, 'October', NULL, 'Říjen', '0');
SET @pid = LAST_INSERT_ID();
INSERT INTO `vic_main`.`preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`, `preklad_id`)
 VALUES (2, 'October', NULL, 'October', '0', @pid);
INSERT INTO `vic_main`.`preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`, `preklad_id`)
 VALUES (16, 'October', NULL, 'Október', '0', @pid);

INSERT INTO `vic_main`.`preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`)
 VALUES (1, 'November', NULL, 'Listopad', '0');
SET @pid = LAST_INSERT_ID();
INSERT INTO `vic_main`.`preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`, `preklad_id`)
 VALUES (2, 'November', NULL, 'November', '0', @pid);
INSERT INTO `vic_main`.`preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`, `preklad_id`)
 VALUES (16, 'November', NULL, 'November', '0', @pid);

INSERT INTO `vic_main`.`preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`)
 VALUES (1, 'December', NULL, 'Prosinec', '0');
SET @pid = LAST_INSERT_ID();
INSERT INTO `vic_main`.`preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`, `preklad_id`)
 VALUES (2, 'December', NULL, 'December', '0', @pid);
INSERT INTO `vic_main`.`preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`, `preklad_id`)
 VALUES (16, 'December', NULL, 'December', '0', @pid);

INSERT INTO `vic_main`.`preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`)
 VALUES (1, 'select', NULL, 'Vyberte', '0');
SET @pid = LAST_INSERT_ID();
INSERT INTO `vic_main`.`preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`, `preklad_id`)
 VALUES (2, 'select', NULL, 'Select', '0', @pid);
INSERT INTO `vic_main`.`preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`, `preklad_id`)
 VALUES (16, 'select', NULL, 'Vyberte', '0', @pid);

INSERT INTO `vic_main`.`preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`)
 VALUES (1, 'day', NULL, 'Den', '0');
SET @pid = LAST_INSERT_ID();
INSERT INTO `vic_main`.`preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`, `preklad_id`)
 VALUES (2, 'day', NULL, 'Day', '0', @pid);
INSERT INTO `vic_main`.`preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`, `preklad_id`)
 VALUES (16, 'day', NULL, 'Den', '0', @pid);

INSERT INTO `vic_main`.`preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`)
 VALUES (1, 'month', NULL, 'Měsíc', '0');
SET @pid = LAST_INSERT_ID();
INSERT INTO `vic_main`.`preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`, `preklad_id`)
 VALUES (2, 'month', NULL, 'Month', '0', @pid);
INSERT INTO `vic_main`.`preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`, `preklad_id`)
 VALUES (16, 'month', NULL, 'Měsíc', '0', @pid);

INSERT INTO `vic_main`.`preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`)
 VALUES (1, 'year', NULL, 'Rok', '0');
SET @pid = LAST_INSERT_ID();
INSERT INTO `vic_main`.`preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`, `preklad_id`)
 VALUES (2, 'year', NULL, 'Year', '0', @pid);
INSERT INTO `vic_main`.`preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`, `preklad_id`)
 VALUES (16, 'year', NULL, 'Rok', '0', @pid);

COMMIT;
