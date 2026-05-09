ALTER TABLE `vic_admin`.`admin` ADD COLUMN `branch_id` INT(10) UNSIGNED NULL DEFAULT NULL, ADD FOREIGN KEY `branch_id` (`branch_id`) REFERENCES `branch`(`id`);
