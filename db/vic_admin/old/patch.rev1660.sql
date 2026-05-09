CREATE TABLE IF NOT EXISTS `vic_admin`.`admin_has_parameter` (
  `admin_id` int(10) unsigned NOT NULL DEFAULT '0',
  `parameter_id` int(10) unsigned NOT NULL,
  `value` varchar(32) NOT NULL,
  PRIMARY KEY (`admin_id`,`parameter_id`),
  CONSTRAINT `fk__admin_has_parameter__admin_id` FOREIGN KEY `admin_id` (`admin_id`) REFERENCES `admin` (`admin_id`),
  CONSTRAINT `fk__admin_has_parameter__paremeter_id` FOREIGN KEY (`parameter_id`) REFERENCES `parameter` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `vic_admin`.`parameter` ADD COLUMN `is_admin` TINYINT( 1 ) NOT NULL AFTER `is_user`;
