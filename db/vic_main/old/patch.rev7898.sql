INSERT INTO `vic_main`.`database_patch`(`revision`, `not_reapplicable`, `note`) VALUES
 ('7898', 1, 'New column sazka_kombinace(_archive).update_platna_do (mantis:636)');

ALTER TABLE `vic_main`.`sazka_kombinace` ADD COLUMN `update_platna_do` BOOLEAN NOT NULL DEFAULT 1;
ALTER TABLE `vic_main`.`sazka_kombinace_archive` ADD COLUMN `update_platna_do` BOOLEAN NOT NULL DEFAULT 1;
