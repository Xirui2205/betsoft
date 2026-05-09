SET foreign_key_checks=0;
START TRANSACTION;
DELETE FROM `vic_admin`.`branch_status`;
INSERT INTO `vic_admin`.`branch_status`(`id`, `name`) VALUES
(1, 'offline'),
(2, 'online');
COMMIT;
SET foreign_key_checks=1;
ALTER TABLE `vic_admin`.`branch` CHANGE `status_id` `status_id` INT( 10 ) NOT NULL DEFAULT '1';
