start transaction;

INSERT INTO `cronjob_type` (`type_id`, `type_name`) VALUES (16, 'branchesDailyReport');
INSERT INTO `cronjob_type` (`type_id`, `type_name`) VALUES (17, 'branchesMonthlyReport');

INSERT INTO `cronjob` (`cronjob_id`, `cronjob_type`, `result`, `attempts`, `attempts_max`, `exec_at`, `executed_at`, `exec_constantly_at`) VALUES
(22, 16, NULL, 0, 0, NULL, NULL, '* 8 * * *'),
(23, 17, NULL, 0, 0, NULL, NULL, '50 7 1 * *');

commit;
