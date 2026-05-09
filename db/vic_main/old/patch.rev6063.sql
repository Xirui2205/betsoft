START TRANSACTION;

INSERT INTO `vic_main`.`cronjob_type` (`type_id`, `type_name`) VALUES
 (7, 'CleanUpBetradarImportLog');

INSERT INTO `vic_main`.`cronjob` (`cronjob_id`, `cronjob_type`, `result`, `attempts`, `attempts_max`, `exec_at`, `executed_at`, `exec_constantly_at`) VALUES
 (12, 7, NULL, 0, 0, NULL, NULL, '24 2 * * * ');

COMMIT;
