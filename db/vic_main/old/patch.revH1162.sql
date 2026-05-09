INSERT INTO `vic_main`.`database_patch`(`revision`, `not_reapplicable`, `note`) VALUES
 ('H1162', 1, 'New column vic_main.controller_convert.nonassoc_params (mantis:990)');

ALTER TABLE `vic_main`.`controller_convert` ADD COLUMN `nonassoc_params` BOOLEAN NOT NULL DEFAULT 0;

UPDATE `vic_main`.`controller_convert` SET `nonassoc_params`=1 WHERE `c_id` IN (2,84);
