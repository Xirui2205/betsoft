INSERT INTO `vic_main`.`database_patch`(`revision`, `not_reapplicable`, `note`) VALUES
 ('7397', 1, 'New column vic_main.sazky_result.all_scores');

ALTER TABLE vic_main.sazky_result ADD COLUMN all_scores TEXT NULL DEFAULT NULL;
