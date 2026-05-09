START TRANSACTION;

INSERT INTO `vic_main`.`database_patch`(`revision`, `not_reapplicable`, `note`) VALUES
 ('7756', 1, 'Fixing TransactionConfirmation cronjob ID');

SET @id=(SELECT cronjob_id FROM vic_main.cronjob_param WHERE param_name='name' AND param_value='"TransactionConfirmation"');
DELETE FROM vic_main.cronjob_param WHERE cronjob_id=@id;
DELETE FROM vic_main.cronjob WHERE cronjob_id=@id;

INSERT INTO `vic_main`.`cronjob` (`cronjob_id`, `cronjob_type`,`exec_constantly_at`)
VALUES (16, 2, '0 8,11,15 * * *');

INSERT INTO `vic_main`.`cronjob_param` (`cronjob_id`,`param_name`,`param_value`)
 VALUES (16, 'name', '"TransactionConfirmation"');

COMMIT;