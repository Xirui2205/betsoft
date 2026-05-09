START TRANSACTION;

INSERT INTO `vic_main`.`database_patch`(`revision`, `not_reapplicable`,
`note`) VALUES
 ('7617', 1, 'PreferenceRate. (mantis:566)');

INSERT INTO `vic_main`.`point_transaction_type` (`id`, `name`, `kind`, `point_type_id`, `note`, `from`, `thru`, `to`, `debiting`, `low_limit`, `high_limit`) VALUES
('13', 'preferenceRate.cancel', 'spend', 1, '', NULL, NULL, NULL, 0, '0.00', NULL);

COMMIT;
