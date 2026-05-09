START TRANSACTION;

INSERT INTO `vic_main`.`database_patch`(`revision`, `not_reapplicable`, `note`) VALUES
 ('H1706', NULL, 'Field hockey sport BR ID (mantis:1122)');

UPDATE sport SET betradar_sport_id=24 WHERE sport_id=1043;

COMMIT;
