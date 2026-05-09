START TRANSACTION;

INSERT INTO `vic_main`.`database_patch`(`revision`, `not_reapplicable`, `note`)
VALUES
	('7532', null, 'Initial record');

SET NAMES UTF8;

INSERT `vic_main`.`preklady` (`lang_id`,`index_pole`,`short_text`,`text`,`translate`)
VALUES
	(1, 'pick_location', NULL, 'Region', '0'),
	(1, 'pick_town_initial', NULL, 'Místo', '0'),
	(1, 'select_location', NULL, 'Zvolte region', '0'),
	(1, 'select_town', NULL, 'Zvolte místo', '0');
	
COMMIT;
