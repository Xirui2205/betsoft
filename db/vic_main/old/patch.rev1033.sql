
CREATE TABLE IF NOT EXISTS `bookmaker_has_role` (
  `bookmaker_id` int(10) unsigned NOT NULL,
  `bookmaker_role_id` int(10) unsigned NOT NULL,
  KEY `bookmaker_id` (`bookmaker_id`),
  KEY `bookmaker_role_id` (`bookmaker_role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `bookmaker_role`
--

CREATE TABLE IF NOT EXISTS `bookmaker_role` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `role` varchar(64) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

ALTER TABLE `bookmaker_has_role` ADD INDEX ( `bookmaker_id` );
ALTER TABLE `bookmaker_has_role` ADD INDEX ( `bookmaker_role_id` ) ;

ALTER TABLE `bookmaker_has_role`
  ADD CONSTRAINT `bookmaker_has_role_ibfk_1` FOREIGN KEY (`bookmaker_id`) REFERENCES `bookmaker` (`bookmaker_id`),
  ADD CONSTRAINT `bookmaker_has_role_ibfk_2` FOREIGN KEY (`bookmaker_role_id`) REFERENCES `bookmaker_role` (`id`);

INSERT INTO `bookmaker_role` (`id`, `role`) VALUES
(1, 'admin'),
(2, 'bookmaker'),
(3, 'manager'),
(4, 'technician'),
(5, 'branch-employee');


CREATE TABLE IF NOT EXISTS `bookmaker_timesheet` (
  `bookmaker_id` int(10) unsigned NOT NULL,
  `day` date NOT NULL,
  `arrival` time NOT NULL,
  `departure` time DEFAULT NULL,
  PRIMARY KEY (`bookmaker_id`,`day`),
  KEY `bookmaker_id` (`bookmaker_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


ALTER TABLE `bookmaker_timesheet`
  ADD CONSTRAINT `bookmaker_timesheet_ibfk_1` FOREIGN KEY (`bookmaker_id`) REFERENCES `bookmaker` (`bookmaker_id`);

update cronjob_type set type_name='Email' where type_id = 1;

ALTER TABLE `bookmaker` ADD `branch_id` INT( 10 ) UNSIGNED NOT NULL AFTER `bookmaker_id`;
ALTER TABLE `uzivatel` ADD `branch_id` INT UNSIGNED NOT NULL AFTER `user_id`;
