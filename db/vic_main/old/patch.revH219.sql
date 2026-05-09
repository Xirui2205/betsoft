INSERT INTO `vic_main`.`database_patch`(`revision`, `not_reapplicable`, `note`)
VALUES ('h219', 1, 'removing unnecessary banner from superhome');

DELETE FROM `vic_main`.`banner` WHERE banner_id IN (8,9);
