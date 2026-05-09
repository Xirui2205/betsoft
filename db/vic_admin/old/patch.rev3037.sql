ALTER TABLE `vic_admin`.`branch` ADD `mp_win` DECIMAL NOT NULL DEFAULT '0',
ADD `mp` DECIMAL NOT NULL DEFAULT '0';

START TRANSACTION;
UPDATE `vic_admin`.`contract_parameter` SET `value` = '0.21' WHERE `contract_parameter`.`id` =2;
UPDATE `vic_admin`.`contract_parameter` SET `value` = '0.5' WHERE `contract_parameter`.`id` =1;
UPDATE `vic_admin`.`contract_parameter` SET `value` = '0.21' WHERE `contract_parameter`.`id` =8;
UPDATE `vic_admin`.`contract_parameter` SET `value` = '0.5' WHERE `contract_parameter`.`id` =5;
UPDATE `vic_admin`.`contract_parameter` SET `value` = '0.01' WHERE `contract_parameter`.`id` =7;
UPDATE `vic_admin`.`contract_parameter` SET `value` = '1000' WHERE `contract_parameter`.`id` =6;
UPDATE `vic_admin`.`contract_parameter` SET `value` = '2000' WHERE `contract_parameter`.`id` =9;
UPDATE `vic_admin`.`contract_parameter` SET `value` = '0.03' WHERE `contract_parameter`.`id` =11;



COMMIT;
