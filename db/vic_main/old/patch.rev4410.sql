CREATE TABLE `vic_main`.`daily_sequence`(
`id` SMALLINT(5) UNSIGNED NOT NULL PRIMARY KEY,
`seq_number` INT(10) UNSIGNED NOT NULL,
`export_date` DATE NOT NULL
) Engine=MyISAM;
-- id=1 for bank export sequence
INSERT INTO `vic_main`.`daily_sequence`(`id`,`seq_number`,`export_date`) VALUES
(1, 1, CURRENT_DATE);
