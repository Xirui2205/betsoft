INSERT INTO `vic_main`.`database_patch`(`revision`, `not_reapplicable`, `note`) VALUES
 ('7953', 1, 'Vouchers (mantis:566)');

CREATE TABLE `vic_main`.`voucher_set` (
 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
 `name` varchar(32) NOT NULL,
 `valid_from` datetime NOT NULL,
 `valid_to` datetime NOT NULL,
 `amount` float NOT NULL,
 `count` int(10) unsigned NOT NULL,
 `multiuse` smallint(5) unsigned NOT NULL,
 `handle_size` tinyint(3) unsigned NOT NULL,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB;

CREATE TABLE `vic_main`.`voucher` (
 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
 `voucher_set_id` int(10) unsigned DEFAULT NULL,
 `handle` varchar(16) NOT NULL,
 `created` datetime NOT NULL,
 `valid_to` datetime NOT NULL,
 `valid_from` datetime NOT NULL,
 `used` tinyint(1) NOT NULL,
 `used_time` datetime DEFAULT NULL,
 `amount` decimal(10,2) NOT NULL,
 `point_type_id` int(10) unsigned NOT NULL,
 `user_id` int(10) unsigned DEFAULT NULL,
 `point_transaction_id` int(10) unsigned DEFAULT NULL,
 PRIMARY KEY (`id`),
 UNIQUE KEY `u__voucher__handle` (`handle`),
 KEY `i__voucher__voucher_set_id` (`voucher_set_id`),
 KEY `i__voucher__valid_to` (`valid_to`),
 KEY `i__voucher__valid_from` (`valid_from`),
 KEY `i__voucher__used` (`used`),
 KEY `i__voucher__user_id` (`user_id`),
 KEY `i__voucher__point_type_id` (`point_type_id`),
 KEY `i__voucher__point_transaction_id` (`point_transaction_id`),
 CONSTRAINT `voucher_ibfk_4` FOREIGN KEY (`voucher_set_id`) REFERENCES `voucher_set` (`id`),
 CONSTRAINT `voucher_ibfk_3` FOREIGN KEY (`point_transaction_id`) REFERENCES `point_transaction` (`transaction_id`),
 CONSTRAINT `voucher_ibfk_1` FOREIGN KEY (`point_type_id`) REFERENCES `point_type` (`id`),
 CONSTRAINT `voucher_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `uzivatel` (`user_id`)
) ENGINE=InnoDB;

START TRANSACTION;

INSERT INTO `vic_main`.`point_transaction_type` (
`id` ,
`name` ,
`kind` ,
`point_type_id` ,
`note` ,
`from` ,
`thru` ,
`to` ,
`debiting` ,
`low_limit` ,
`high_limit` ,
`limited_by_day`
)
VALUES (
'14', 'voucher', 'get', '1', 'Body za voucher.', NULL , NULL , NULL , '0', '0', NULL , '1'
);


INSERT INTO `vic_main`.`controller_convert` 
	(`c_id`, `lang_id`, `parent_id`, `poradi`, `zobrazeno`, `cols`, `left_side`, `right_side`, `title`, `description`, `text`, `req_controller`, `req_action`, `real_controller`, `real_action`, `after_login`, `actionless`)
VALUES
	(91, 1, 1, 7, 1, 3, NULL, NULL, NULL, NULL, 'Voucher', 'voucher', 'use', 'voucher', 'use', 0, 0),
	(91, 2, 1, 7, 1, 3, NULL, NULL, NULL, NULL, 'Voucher', 'voucher', 'use', 'voucher', 'use', 0, 0),
	(91, 16, 1, 7, 1, 3, NULL, NULL, NULL, NULL, 'Voucher','voucher', 'use', 'voucher', 'use', 0, 0);


COMMIT;
