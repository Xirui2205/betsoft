START TRANSACTION;
INSERT INTO `cronjob_type` (`type_id`, `type_name`) VALUES
(25, 'CleanSavedCoupons');
INSERT INTO `cronjob` (`cronjob_id`, `cronjob_type`, `result`, `attempts`, `attempts_max`, `exec_at`, `executed_at`, `exec_constantly_at`) VALUES
(32, 25, NULL, 0, 0, NULL,NULL, '0 3 * * *');
COMMIT;