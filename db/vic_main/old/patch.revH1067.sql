START TRANSACTION;

INSERT INTO `vic_main`.`database_patch`(`revision`, `not_reapplicable`, `note`) VALUES
 ('H1067', 1, 'New translation resources for bet "valid from/to" hints (mantis:960)');

INSERT INTO `vic_main`.`preklady`(`lang_id`, `index_pole`, `short_text`, `text`, `translate`) VALUES
 (1, 'bet_valid_from_hint', NULL, 'Kdy byla sázka vytvořena', 0),
 (2, 'bet_valid_from_hint', NULL, 'Time when was bet created', 0),
 (16, 'bet_valid_from_hint', NULL, 'Kdy bola sázka vytvorena', 0),
 (1, 'bet_valid_to_hint', NULL, 'Kdy začíná příležitost', 0),
 (2, 'bet_valid_to_hint', NULL, 'Time when market begins', 0),
 (16, 'bet_valid_to_hint', NULL, 'Kdy začíná príležitosť', 0);

COMMIT;
