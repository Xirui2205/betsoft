CREATE TABLE `vic_ut_main`.`point_type` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` varchar(64) NOT NULL,
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE `vic_ut_main`.`point_account` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `user_id` int(10) unsigned NOT NULL,
  `point_type_id` int(10) unsigned NOT NULL,
  `balance` decimal(11,2) NOT NULL,
  CONSTRAINT `fk__point_account__user_id` FOREIGN KEY (`user_id`) REFERENCES `uzivatel` (`user_id`),
  CONSTRAINT `fk__point_account__point_type_id` FOREIGN KEY (`point_type_id`) REFERENCES `point_type` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE `vic_ut_main`.`point_transaction_type` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` varchar(64) NOT NULL,
  `point_type_id` int(10) unsigned NOT NULL,
  `note` text NOT NULL,
  `from` varchar(8) DEFAULT NULL,
  `thru` varchar(8) DEFAULT NULL,
  `to` varchar(8) DEFAULT NULL,
  `debiting` tinyint(1) NOT NULL,
  `low_limit` decimal(11,2) DEFAULT NULL,
  `high_limit` decimal(11,2) DEFAULT NULL,
  UNIQUE KEY `name` (`name`),
  CONSTRAINT `fk__point_transaction_type__point_type_id` FOREIGN KEY (`point_type_id`) REFERENCES `point_type` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


CREATE TABLE `vic_ut_main`.`point_transaction` (
  `transaction_id` int(11) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `user_id` int(11) unsigned DEFAULT NULL,
  `value` decimal(11,2) NOT NULL,
  `type_id` int(10) unsigned NOT NULL,
  `time` datetime NOT NULL,
  `branch_id` int(10) unsigned DEFAULT NULL,
  `ticket_id` bigint(20) unsigned DEFAULT NULL,
  `note` varchar(256) NOT NULL,
  `balance` decimal(11,2) NOT NULL,
  KEY `branch_id` (`branch_id`),
  KEY `time` (`time`),
  CONSTRAINT `fk__point_transaction__user_id` FOREIGN KEY (`user_id`) REFERENCES `uzivatel` (`user_id`),
  CONSTRAINT `fk__point_transaction__ticket_id` FOREIGN KEY (`ticket_id`) REFERENCES `ticket` (`ticket_id`),
  CONSTRAINT `fk__point_transaction__type_id` FOREIGN KEY (`type_id`) REFERENCES `point_transaction_type` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
