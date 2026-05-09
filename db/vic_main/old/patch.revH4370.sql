START TRANSACTION;

INSERT INTO `vic_main`.`database_patch`(`revision`, `not_reapplicable`, `note`)
VALUES ('H4370', 0, 'Supertip zpatky do menu (mantis:153)');

UPDATE `vic_main`.`sport` SET `hide_in_sportmenu` = '0' WHERE `sport`.`sport_id` =1056;

COMMIT;
