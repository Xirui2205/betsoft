START TRANSACTION;

INSERT INTO `cronjob_type` (`type_id`, `type_name`) VALUES (21, 'monthlyUsersBalanceReport');
INSERT INTO `cronjob` (`cronjob_id`, `cronjob_type`, `result`, `attempts`, `attempts_max`, `exec_at`, `executed_at`, `exec_constantly_at`) VALUES 
(26, 21, NULL, 0, 0, NULL, '2013-11-01 14:57:23', '25 7 1 * *');

COMMIT;
