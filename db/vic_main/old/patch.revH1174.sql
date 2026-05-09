START TRANSACTION;

INSERT INTO `vic_main`.`database_patch`(`revision`, `not_reapplicable`, `note`)
VALUES (1174, 1, 'changing bet parent_id from 0 to NULL');


UPDATE `vic_main`.`sazky` SET parent_id=NULL WHERE parent_id=0;


COMMIT;