ALTER TABLE `vic_admin`.`branch` ADD  `is_testing` BOOLEAN NOT NULL DEFAULT  '0';

START TRANSACTION;
UPDATE  `vic_admin`.`branch` SET  `is_testing` =  '1' WHERE  `branch`.`id` = 5000;
UPDATE  `vic_admin`.`branch` SET  `is_testing` =  '1' WHERE  `branch`.`id` = 5001;
UPDATE  `vic_admin`.`branch` SET  `is_testing` =  '1' WHERE  `branch`.`id` = 5002;
UPDATE  `vic_admin`.`branch` SET  `is_testing` =  '1' WHERE  `branch`.`id` = 5003;
UPDATE  `vic_admin`.`branch` SET  `is_testing` =  '1' WHERE  `branch`.`id` = 5004;
UPDATE  `vic_admin`.`branch` SET  `is_testing` =  '1' WHERE  `branch`.`id` = 5005;
COMMIT;
