INSERT INTO `vic_main`.`database_patch`(`revision`, `not_reapplicable`, `note`) VALUES
 ('H448', 1, 'More order types for bet type (mantis:874)');

ALTER TABLE `vic_main`.`typ` CHANGE `order_type` `order_type_old` SET('rate_asc') NULL DEFAULT NULL;
ALTER TABLE `vic_main`.`typ` ADD COLUMN `order_type` ENUM('rate_asc', 'alias_rate_asc') NULL DEFAULT NULL AFTER `order_type_old`;
UPDATE `vic_main`.`typ` SET order_type='rate_asc' WHERE order_type_old='rate_asc';
ALTER TABLE `vic_main`.`typ` DROP COLUMN `order_type_old`;

-- strelci
UPDATE `vic_main`.`typ` SET `order_type`='alias_rate_asc' WHERE `typ_alias_group`='strelci';
