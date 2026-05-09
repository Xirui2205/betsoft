START TRANSACTION;

INSERT INTO `vic_main`.`database_patch`(`revision`, `not_reapplicable`, `note`)
VALUES ('7571', null, 'Transaction Confirmation Alert');
 
INSERT INTO `vic_main`.`cronjob` (`cronjob_type`,`exec_constantly_at`)
VALUES (2, '0 8,11,15 * * *');

SET @id = LAST_INSERT_ID();


INSERT INTO `vic_main`.`cronjob_param` (`cronjob_id`,`param_name`,`param_value`)
VALUES (@id, 'name', '"TransactionConfirmation"');

COMMIT;

