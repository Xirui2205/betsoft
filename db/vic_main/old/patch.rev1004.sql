ALTER TABLE `vic_main`.`uzivatel` ADD COLUMN `branch_id` INT(10) UNSIGNED NOT NULL AFTER `user_id`;
UPDATE uzivatel SET branch_id =1;
