INSERT INTO `vic_admin`.`database_patch`(`revision`, `not_reapplicable`, `note`)
VALUES (642, 1, 'adding is_top parameter to branch');


ALTER TABLE `branch` ADD `is_top` BOOLEAN NOT NULL DEFAULT '0';
