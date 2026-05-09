START TRANSACTION;

INSERT INTO `vic_main`.`database_patch`(`revision`, `not_reapplicable`, `note`) VALUES
 ('8100', 1, 'Translation resource for one error message (mantis:722)');

INSERT INTO `vic_main`.`preklady`(`lang_id`, `index_pole`, `short_text`, `text`, `translate`) VALUES
 (1, 'error_duplicate_event_br_id', NULL, 'Betradar ID události je již použito', 0),
 (2, 'error_duplicate_event_br_id', NULL, 'Betradar ID of event is already used', 0),
 (16, 'error_duplicate_event_br_id', NULL, 'Betradar ID události je již použito', 0);

COMMIT;
