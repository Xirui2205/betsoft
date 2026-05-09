start transaction;

INSERT INTO `cronjob_type` (`type_id`, `type_name`) VALUES
(12, 'weeklyReport');

INSERT INTO `cronjob` (`cronjob_id`, `cronjob_type`, `result`, `attempts`, `attempts_max`, `exec_at`, `executed_at`, `exec_constantly_at`) VALUES
(18, 12, NULL, 0, 0, NULL, NULL, NULL);

commit;
