ALTER TABLE `ticket` ADD `branch_id` INT(10) UNSIGNED NULL DEFAULT NULL;
UPDATE `vic_main`.`ticket` SET `branch_id`=1;
ALTER TABLE `ticket` MODIFY `branch_id` INT(10) UNSIGNED NOT NULL, ADD INDEX (`branch_id`);
