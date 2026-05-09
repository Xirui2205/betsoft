start transaction;

INSERT INTO `vic_main`.`database_patch`(`revision`, `not_reapplicable`, `note`)
VALUES ('H4347', 1, 'Crony pro narozeninovy a registracni bonus (mantis:147)');

INSERT INTO `cronjob_type` (`type_id`, `type_name`) VALUES (18, 'ApplyRegistrationBonus');
INSERT INTO `cronjob_type` (`type_id`, `type_name`) VALUES (19, 'ApplyBirthdayBonus');

INSERT INTO `cronjob` (`cronjob_id`, `cronjob_type`, `result`, `attempts`, `attempts_max`, `exec_at`, `executed_at`, `exec_constantly_at`) VALUES
(24, 18, NULL, 0, 0, NULL, NULL, '10 8 * * *'),
(25, 19, NULL, 0, 0, NULL, NULL, '20 8 * * *');

commit;
