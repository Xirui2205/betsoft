TRUNCATE vic_main.archive_cronjob_param;
TRUNCATE vic_main.archive_cronjob;

START TRANSACTION;
DELETE FROM `vic_main`.`cronjob_param` WHERE `cronjob_id` IN (
 SELECT `cronjob_id` FROM `vic_main`.`cronjob` WHERE `exec_constantly_at` IS NULL
); 
DELETE FROM `vic_main`.`cronjob` WHERE `exec_constantly_at` IS NULL;
COMMIT;
