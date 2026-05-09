INSERT INTO `vic_main`.`database_patch`(`revision`, `not_reapplicable`, `note`) VALUES
 ('7553', 1, 'Day and total point transaction limit. (mantis:566)');


ALTER TABLE `vic_main`.`point_transaction`
	ADD `value_before_limitation` DECIMAL( 11, 2 ) NULL AFTER `value`,
	ADD `limited` ENUM( 'no', 'total', 'day' ) NOT NULL DEFAULT 'no';


START TRANSACTION;

INSERT INTO `vic_main`.`point_transaction_type` (`id`, `name`, `kind`, `point_type_id`, `note`, `from`, `thru`, `to`, `debiting`, `low_limit`, `high_limit`) VALUES
('12', 'moneyTicket.cancel', 'get', 1, '', NULL, NULL, NULL, 1, NULL, '0.00');


UPDATE `vic_main`.`controller_convert` SET `req_controller` = 'vklad-vyber', `req_action` = 'bodove-transakce',
`real_controller` = 'Deposit' WHERE `controller_convert`.`c_id` =58 AND `controller_convert`.`lang_id` =1;


COMMIT;
