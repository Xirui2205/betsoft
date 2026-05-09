START TRANSACTION;

INSERT INTO `vic_main`.`database_patch`(`revision`, `not_reapplicable`, `note`)
VALUES ('7538', null, 'Branch In/Out Alert');
 
INSERT INTO `vic_main`.`cronjob` (`cronjob_type`,`exec_constantly_at`)
VALUES (2, '0 7 * * *');

SET @id = LAST_INSERT_ID();


INSERT INTO `vic_main`.`cronjob_param` (`cronjob_id`,`param_name`,`param_value`)
VALUES (@id, 'name', '"HostInOutStatus"');



COMMIT;

ALTER TABLE `cronjob_param` CHANGE `param_value` `param_value` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `archive_cronjob_param` CHANGE `param_value` `param_value` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL 
