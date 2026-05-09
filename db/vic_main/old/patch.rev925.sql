DROP TABLE IF EXISTS `archive_cronjob_has_param`;
DROP TABLE IF EXISTS `archive_cronjob_param`;
DROP TABLE IF EXISTS `archive_cronjob`;
DROP TABLE IF EXISTS `cronjob_has_param`;
DROP TABLE IF EXISTS `cronjob_param`;
DROP TABLE IF EXISTS `cronjob`;
DROP TABLE IF EXISTS `cronjob_type`;

CREATE TABLE IF NOT EXISTS `archive_cronjob` (
  `cronjob_id` int(11) NOT NULL,
  `cronjob_type` int(10) NOT NULL,
  `result` int(10) DEFAULT NULL,
  `attempts` int(2) NOT NULL,
  `exec_at` datetime NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  PRIMARY KEY (`cronjob_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE 'utf8_general_ci';

CREATE TABLE IF NOT EXISTS `archive_cronjob_param` (
  `param_id` int(11) NOT NULL,
  `cronjob_id` int(11) NOT NULL,
  `param_name` varchar(255) NOT NULL,
  `param_value` text NOT NULL,
  PRIMARY KEY (`param_id`),
  FOREIGN KEY (`cronjob_id`) REFERENCES `archive_cronjob` (`cronjob_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE 'utf8_general_ci';

CREATE TABLE IF NOT EXISTS `cronjob_type` (
  `type_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type_name` varchar(100) NOT NULL,
  PRIMARY KEY (`type_id`),
  UNIQUE KEY `type_name` (`type_name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE 'utf8_general_ci';

CREATE TABLE IF NOT EXISTS `cronjob` (
  `cronjob_id` int(11) NOT NULL AUTO_INCREMENT,
  `cronjob_type` int(10) unsigned NOT NULL,
  `result` int(10) DEFAULT NULL,
  `attempts` int(2) NOT NULL DEFAULT '0',
  `exec_at` datetime NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  PRIMARY KEY (`cronjob_id`),
  FOREIGN KEY (`cronjob_type`) REFERENCES `cronjob_type` (`type_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE 'utf8_general_ci';

CREATE TABLE IF NOT EXISTS `cronjob_param` (
  `param_id` int(11) NOT NULL AUTO_INCREMENT,
  `cronjob_id` int(11) NOT NULL,
  `param_name` varchar(255) NOT NULL,
  `param_value` text NOT NULL,
  PRIMARY KEY (`param_id`),
  FOREIGN KEY (`cronjob_id`) REFERENCES `cronjob` (`cronjob_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE 'utf8_general_ci';

INSERT INTO `vic_main`.`cronjob_type` (
`type_id` ,
`type_name`
)
VALUES (
NULL , 'email'
);

