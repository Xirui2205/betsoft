INSERT INTO `vic_main`.`database_patch`(`revision`, `not_reapplicable`, `note`) VALUES
 ('7896', 1, 'New column vic_main.sazky.status_ext (mantis:646)');

ALTER TABLE `vic_main`.`sazky` ADD COLUMN `status_ext` SMALLINT(5) UNSIGNED NULL DEFAULT NULL AFTER `status`;

INSERT INTO `vic_main`.`preklady`(`lang_id`,`index_pole`,`short_text`,`text`,`translate`) VALUES
 (1, 'bet_status_suspended_br', '', 'Pozastavená (BR)', 0),
 (2, 'bet_status_suspended_br', '', 'Suspended (BR)', 0),
 (16, 'bet_status_suspended_br', '', 'Pozastavená (BR)', 0);
