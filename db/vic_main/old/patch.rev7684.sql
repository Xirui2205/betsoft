START TRANSACTION;

INSERT INTO `vic_main`.`database_patch`(`revision`, `not_reapplicable`,
`note`) VALUES
 ('7684', 1, 'nejake preklady typu sazek');

SET NAMES UTF8;

INSERT INTO `vic_main`.`preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`) VALUES
 (1, '1.tretina_dvojita-sance', NULL, '1. třetina dvojitá šance', '0'),
 (1, '2.tretina_dvojita-sance', NULL, '2. třetina dvojitá šance', '0'),
 (1, '3.tretina_dvojita-sance', NULL, '3. třetina dvojitá šance', '0'),
 (1, '2.polocas_vice-mene', NULL, '1. poločas více/méně', '0'),
 (1, 'zapas_ft', NULL, 'Zápas v zákl.hrací době', '0'),
 (1, '1.polocas_asijsky-handicap', NULL, '1. poločas asijský handicap', '0');

COMMIT;
