ALTER TABLE `contract_parameter_value` DROP FOREIGN KEY `contract_parameter_value_ibfk_2` ;

ALTER TABLE `contract_parameter_value` ADD FOREIGN KEY ( `parameter_id` ) REFERENCES `vic_admin`.`contract_parameter` (
`id`
);
