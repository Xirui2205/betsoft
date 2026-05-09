INSERT INTO `vic_main`.`database_patch`(`revision`, `not_reapplicable`, `note`) VALUES
 ('H988', 1, 'New ordering type for bets (mantis:923)');

ALTER TABLE `vic_main`.`typ` CHANGE `order_type` `order_type` ENUM( 'rate_asc', 'alias_rate_asc', 'threshold_asc' ) NULL DEFAULT NULL;

UPDATE `vic_main`.`typ` SET `order_type`='threshold_asc'
 WHERE typ_id IN (20, 25, 45, 46, 47, 48, 51, 88, 89, 90, 157, 163);
