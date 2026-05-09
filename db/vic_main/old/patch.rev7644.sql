START TRANSACTION;

INSERT INTO `vic_main`.`database_patch`(`revision`, `not_reapplicable`,
`note`) VALUES
 ('7644', 1, 'preklad your_home');

SET NAMES UTF8;

INSERT INTO `vic_main`.`preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`)
 VALUES (1, 'your_home', NULL, 'Vaše bydliště', '0');


COMMIT;
