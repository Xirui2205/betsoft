CREATE TABLE `vic_main`.`ticket_sequence` (
`id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY
) ENGINE = MYISAM ;

CREATE TABLE `vic_main`.`ticket_live` (
`id` BIGINT( 20 ) UNSIGNED NOT NULL ,
`handle` CHAR( 9 ) CHARACTER SET ascii COLLATE ascii_bin NULL ,
`user_id` INT( 10 ) UNSIGNED NOT NULL ,
`stake` DECIMAL( 10, 2 ) NOT NULL ,
`rate` DECIMAL( 10, 2 ) NOT NULL ,
`win` DECIMAL( 10, 2 ) NOT NULL ,
`status` ENUM( 'new', 'paid', 'canceled', 'resettled' ) NOT NULL ,
`type` ENUM( 'simple', 'kombi' ) NOT NULL ,
`time_created` DATETIME NOT NULL ,
`time_canceled` DATETIME NULL ,
`time_paid` DATETIME NULL ,
`time_resettled` DATETIME NULL ,
PRIMARY KEY ( `id` ) ,
INDEX ( `handle` , `user_id` , `status` )
) ENGINE = InnoDB;

ALTER TABLE `vic_main`.`ticket_live` ADD INDEX ( `user_id` ) ;

CREATE TABLE `vic_main`.`ticket_live_bet` (
`id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`ticket_live_id` BIGINT( 20 ) UNSIGNED NOT NULL ,
`players` VARCHAR( 256 ) NOT NULL ,
`tip` VARCHAR( 128 ) NOT NULL ,
`sport` VARCHAR( 128 ) NOT NULL ,
`market` VARCHAR( 128 ) NOT NULL ,
`result_valid` TINYINT( 1 ) NOT NULL ,
`result` VARCHAR( 256 ) NOT NULL ,
`rate` DOUBLE( 10, 2 ) NOT NULL ,
`canceled` TINYINT( 1 ) NOT NULL ,
INDEX ( `ticket_live_id` , `result_valid` , `canceled` )
) ENGINE = InnoDB;

ALTER TABLE `vic_main`.`ticket_live_bet` ADD FOREIGN KEY ( `ticket_live_id` ) REFERENCES `vic_main`.`ticket_live` (
`id`
);

ALTER TABLE `vic_main`.`ticket_live` ADD FOREIGN KEY ( `user_id` ) REFERENCES `vic_main`.`uzivatel` (
`user_id`
);

ALTER TABLE `vic_main`.`ticket` CHANGE `ticket_id` `ticket_id` BIGINT( 20 ) UNSIGNED NOT NULL;

ALTER TABLE `vic_main`.`ticket_sequence` AUTO_INCREMENT = 5000;

CREATE TABLE `vic_main`.`ticket_live_transaction` (
`id` VARCHAR( 32 ) NOT NULL ,
PRIMARY KEY ( `id` )
) ENGINE = MYISAM ;

ALTER TABLE `vic_main`.`financial_transaction` DROP FOREIGN KEY `financial_transaction_ibfk_2` ;


START TRANSACTION;
INSERT INTO `vic_main`.`controller_convert` (`c_id`,`lang_id`, `parent_id`, `poradi`, `zobrazeno`,
	`cols`, `left_side`, `right_side`, `title`, `description`, `text`, `req_controller`, `req_action`,
	`real_controller`, `real_action`, `after_login`, `actionless`)
VALUES (
	'70', '1', '16', '2', '1', '3', NULL, NULL, NULL, NULL, 'Live tikety',
	'muj-ucet', 'live-tiket', 'myaccount', 'live-ticket', '0', '0'
), (
	'70', '2', '16', '2', '1', '3', NULL, NULL, NULL, NULL, 'Live tickets',
	'my-account', 'live-ticket', 'myaccount', 'live-ticket', '0', '0'
), (
	'70', '16', '16', '2', '1', '3', NULL, NULL, NULL, NULL, 'Live tikety',
	'muj-ucet', 'live-tiket', 'myaccount', 'live-ticket', '0', '0'
);
COMMIT;

