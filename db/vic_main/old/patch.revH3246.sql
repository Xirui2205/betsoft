start transaction;

INSERT INTO `cronjob_type` (`type_id`, `type_name`) VALUES
(13, 'dailyReport');

INSERT INTO `cronjob` (`cronjob_id`, `cronjob_type`, `result`, `attempts`, `attempts_max`, `exec_at`, `executed_at`, `exec_constantly_at`) VALUES
(19, 13, NULL, 0, 0, NULL, NULL, NULL);

commit;
