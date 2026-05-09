START TRANSACTION;

INSERT INTO `vic_main`.`database_patch`(`revision`, `not_reapplicable`, `note`) VALUES
 ('7893', 1, '1X2/1X12X2 for remaining bet types (mantis:310)');


INSERT INTO `vic_main`.`typ_podtyp`(`typ_id`,`sport_id`,`podtyp_id`,`live_bet`) VALUES
 (26, 1011, 218, 0),
 (27, 1011, 218, 0),
 (28, 1011, 218, 0);

COMMIT;
