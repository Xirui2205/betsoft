START TRANSACTION;

INSERT INTO `vic_main`.`database_patch`(`revision`, `not_reapplicable`, `note`) VALUES
 ('H1262', NULL, 'Fixed ajax menu controller convert data (mantis:974)');

UPDATE vic_main.controller_convert SET nonassoc_params=1 WHERE c_id=86;

COMMIT;
