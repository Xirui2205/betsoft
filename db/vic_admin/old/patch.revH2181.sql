INSERT INTO `vic_admin`.`database_patch`(`revision`, `not_reapplicable`, `note`) VALUES
 ('H2181', 1, 'Added place to branches (mantis: 1234)');
ALTER TABLE `branch` ADD `place` VARCHAR( 200 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ;

