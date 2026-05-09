set foreign_key_checks=0;
drop table if exists contract_status;
drop table if exists  contract;

CREATE TABLE IF NOT EXISTS `contract` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `date_valid_from` date NOT NULL,
  `date_valid_to` date NOT NULL,
  `date_signed` date DEFAULT NULL,
  `template_id` int(10) unsigned NOT NULL,
  `branch_id` int(10) unsigned NOT NULL,
  `date_canceled` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `template_id` (`template_id`),
  KEY `branch_id` (`branch_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;

drop table if exists   contract_parameter;
CREATE TABLE IF NOT EXISTS `contract_parameter` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  `template_id` int(10) unsigned NOT NULL,
  `type` varchar(32) NOT NULL,
  `mandatory` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `template_id` (`template_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=12 ;


drop table if exists  contract_parameter_value;
CREATE TABLE IF NOT EXISTS `contract_parameter_value` (
  `contract_id` int(10) unsigned NOT NULL,
  `parameter_id` int(10) unsigned NOT NULL,
  `value` varchar(64) NOT NULL,
  PRIMARY KEY (`contract_id`,`parameter_id`),
  KEY `contract_id` (`contract_id`),
  KEY `parameter_id` (`parameter_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

drop table if exists   contract_template;
CREATE TABLE IF NOT EXISTS `contract_template` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

ALTER TABLE `contract`
  ADD CONSTRAINT `contract_ibfk_2` FOREIGN KEY (`template_id`) REFERENCES `contract_template` (`id`),
  ADD CONSTRAINT `contract_ibfk_3` FOREIGN KEY (`branch_id`) REFERENCES `branch` (`id`);

--
-- Constraints for table `contract_parameter_value`
--
ALTER TABLE `contract_parameter_value`
  ADD CONSTRAINT `contract_parameter_value_ibfk_1` FOREIGN KEY (`contract_id`) REFERENCES `contract` (`id`),
  ADD CONSTRAINT `contract_parameter_value_ibfk_2` FOREIGN KEY (`parameter_id`) REFERENCES `parameter` (`id`);
set foreign_key_checks=1;
