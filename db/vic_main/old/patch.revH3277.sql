start transaction;

INSERT INTO `cronjob_type` (`type_id`, `type_name`) VALUES (14, 'stemDailyReport');
INSERT INTO `cronjob_type` (`type_id`, `type_name`) VALUES (15, 'stemMonthlyReport');

INSERT INTO `cronjob` (`cronjob_id`, `cronjob_type`, `result`, `attempts`, `attempts_max`, `exec_at`, `executed_at`, `exec_constantly_at`) VALUES
(20, 14, NULL, 0, 0, NULL, NULL, NULL),
(21, 15, NULL, 0, 0, NULL, NULL, NULL);

commit;
