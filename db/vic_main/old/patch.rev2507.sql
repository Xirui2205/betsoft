ALTER TABLE `vic_main`.`banner` ADD `valid_from` DATETIME NULL DEFAULT NULL AFTER `target`,
ADD `valid_to` DATETIME NULL DEFAULT NULL AFTER `valid_from`;
