DROP TABLE IF EXISTS `archive_cronjob_has_param`;
DROP TABLE IF EXISTS `archive_cronjob_param`;
DROP TABLE IF EXISTS `archive_cronjob`;
DROP TABLE IF EXISTS `cronjob_has_param`;
DROP TABLE IF EXISTS `cronjob_param`;
DROP TABLE IF EXISTS `cronjob`;
DROP TABLE IF EXISTS `cronjob_type`;

CREATE TABLE `archive_cronjob` (
  `cronjob_id` int(11) NOT NULL AUTO_INCREMENT,
  `cronjob_type` int(10) NOT NULL,
  `sent` tinyint(1) NOT NULL,
  `attempts` int(2) NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY (`cronjob_id`)
) ENGINE=InnoDb CHARACTER SET='utf8' COLLATE 'utf8_general_ci';

CREATE TABLE `archive_cronjob_param` (
  `param_id` int(11) NOT NULL AUTO_INCREMENT,
  `param_name` varchar(255) NOT NULL,
  `param_value` text NOT NULL,
  PRIMARY KEY (`param_id`)
) ENGINE=InnoDB CHARACTER SET='utf8' COLLATE 'utf8_general_ci';

CREATE TABLE `archive_cronjob_has_param` (
  `cronjob_id` int(11) NOT NULL,
  `param_id` int(11) NOT NULL,
  PRIMARY KEY (`cronjob_id`,`param_id`),
  FOREIGN KEY (`cronjob_id`) REFERENCES `archive_cronjob` (`cronjob_id`),
  FOREIGN KEY (`param_id`) REFERENCES `archive_cronjob_param` (`param_id`)
) ENGINE=InnoDb CHARACTER SET='utf8' COLLATE 'utf8_general_ci';

CREATE TABLE `cronjob_type` (
  `type_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type_name` varchar(100) NOT NULL,
  PRIMARY KEY (`type_id`),
  UNIQUE KEY (`type_name`)
) ENGINE=InnoDB CHARACTER SET='utf8' COLLATE 'utf8_general_ci';

CREATE TABLE `cronjob` (
  `cronjob_id` int(11) NOT NULL AUTO_INCREMENT,
  `cronjob_type` int(10) unsigned NOT NULL,
  `sent` tinyint(1) NOT NULL,
  `attempts` int(2) NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY (`cronjob_id`),
  KEY (`cronjob_type`),
  FOREIGN KEY (`cronjob_type`) REFERENCES `cronjob_type` (`type_id`)
) ENGINE=InnoDB CHARACTER SET='utf8' COLLATE 'utf8_general_ci';

CREATE TABLE `cronjob_param` (
  `param_id` int(11) NOT NULL AUTO_INCREMENT,
  `param_name` varchar(255) NOT NULL,
  `param_value` text NOT NULL,
  PRIMARY KEY (`param_id`)
) ENGINE=InnoDb CHARACTER SET='utf8' COLLATE 'utf8_general_ci';

CREATE TABLE `cronjob_has_param` (
  `cronjob_id` int(11) NOT NULL,
  `param_id` int(11) NOT NULL,
  PRIMARY KEY (`cronjob_id`,`param_id`),
  FOREIGN KEY (`cronjob_id`) REFERENCES `cronjob` (`cronjob_id`),
  FOREIGN KEY (`param_id`) REFERENCES `cronjob_param` (`param_id`)
) ENGINE=InnoDb CHARACTER SET='utf8' COLLATE 'utf8_general_ci';

INSERT INTO `cronjob_type` (`type_id`, `type_name`) VALUES (1, 'Email');
