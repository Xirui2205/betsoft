INSERT INTO vic_main.`database_patch`(`revision`, `not_reapplicable`, `note`)
VALUES (2001, 1, 'Engin for database_patch from MyISAM to InnoDb');

ALTER TABLE `vic_main`.`database_patch` ENGINE = InnoDB;
