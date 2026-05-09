INSERT INTO `vic_main`.`database_patch`(`revision`, `not_reapplicable`, `note`) VALUES
 ('7917', 1, 'Dnem nelimitovane bodove transakce (mantis:663)');

ALTER TABLE `vic_main`.`point_transaction_type` ADD `limited_by_day` TINYINT( 1 ) NOT NULL DEFAULT '1';

UPDATE `vic_main`.`point_transaction_type` SET `limited_by_day` = '0' WHERE `point_transaction_type`.`id` =9;

