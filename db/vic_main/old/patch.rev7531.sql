START TRANSACTION;

INSERT INTO `vic_main`.`database_patch`(`revision`, `not_reapplicable`, `note`)
VALUES
	('7531', 1, 'Preklady');

SET NAMES UTF8;

INSERT `vic_main`.`preklady` (`lang_id`,`index_pole`,`short_text`,`text`,`translate`)
VALUES
	(1, 'branches_close_by', NULL, 'Nejbližší pobočky', '0');
	
COMMIT;
