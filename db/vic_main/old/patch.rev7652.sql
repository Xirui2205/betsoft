INSERT INTO `vic_main`.`database_patch`(`revision`, `not_reapplicable`, `note`) VALUES
 ('7652', 1, 'Bet user history');

ALTER TABLE `vic_main`.`bet_column` ENGINE=InnoDb;
