INSERT INTO `vic_main`.`database_patch`(`revision`, `not_reapplicable`, `note`) VALUES
 ('H1094', 1, 'Index over vic_main.match_live.special_id (mantis:975)');

CREATE INDEX `i__match_live__special_id` ON `vic_main`.`match_live` (`special_id`);
