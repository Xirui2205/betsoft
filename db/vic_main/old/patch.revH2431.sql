START TRANSACTION;

INSERT INTO `vic_main`.`database_patch`(`revision`, `not_reapplicable`, `note`) VALUES
 ('2131', NULL, 'vic_main.log cleanup (mantis:1337)');

INSERT INTO `vic_main`.`cronjob_type` (`type_name`) VALUES ('CleanDbLog');
SET @typ_id = LAST_INSERT_ID(); 

INSERT INTO `vic_main`.`cronjob` (`cronjob_type`, `exec_constantly_at`) VALUES (@typ_id, '11 3 * * *');
 
COMMIT;