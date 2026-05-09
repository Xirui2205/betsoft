START TRANSACTION;

INSERT INTO `vic_main`.`database_patch`(`revision`, `not_reapplicable`, `note`) VALUES
 ('8037', NULL, 'Nastaveni parametru sazka/body (mantis:665)');
 

UPDATE `vic_main`.`point_type` SET
 rate = 20
WHERE id = 1;

COMMIT;
