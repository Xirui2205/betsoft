START TRANSACTION;

INSERT INTO `vic_main`.`database_patch`(`revision`, `not_reapplicable`, `note`) VALUES
 ('7740', 1, 'Fixing HostInOutStatus cronjob ID');

SET @id=(SELECT cronjob_id FROM vic_main.cronjob_param WHERE param_name='name' AND param_value='"HostInOutStatus"');
DELETE FROM vic_main.cronjob_param WHERE cronjob_id=@id;
DELETE FROM vic_main.cronjob WHERE cronjob_id=@id;

INSERT INTO `vic_main`.`cronjob` (`cronjob_id`, `cronjob_type`,`exec_constantly_at`)
VALUES (15, 2, '0 7 * * *');

INSERT INTO `vic_main`.`cronjob_param` (`cronjob_id`,`param_name`,`param_value`)
 VALUES (15, 'name', '"HostInOutStatus"');

COMMIT;