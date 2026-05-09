SET foreign_key_checks=0;

--
-- Table structure for table `bank_account`
--

DROP TABLE IF EXISTS `bank_account`;
CREATE TABLE IF NOT EXISTS `bank_account` (
  `account_id` int(11) NOT NULL AUTO_INCREMENT,
  `bank_name` varchar(255) NOT NULL,
  `bank_branch` varchar(255) NOT NULL,
  `account_prefix` varchar(20) DEFAULT NULL,
  `account_number` varchar(20) NOT NULL,
  `bank_code` varchar(20) NOT NULL,
  `specific_symbol` varchar(20) NOT NULL,
  `currency` int(11) NOT NULL,
  `note` text,
  PRIMARY KEY (`account_id`),
  KEY `currency` (`currency`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `bank_account`
--

INSERT INTO `bank_account` (`account_id`, `bank_name`, `bank_branch`, `account_prefix`, `account_number`, `bank_code`, `specific_symbol`, `currency`, `note`) VALUES
(1, 'CSOB', 'Na Porici', NULL, '212228949', '0300', '345583', 1, NULL),
(2, 'CSOB', 'sofijske namesti', NULL, '874847875', '0300', '39843847', 2, NULL),
(3, 'eBanka', 'na netu preciii, ne?', '34343', '874847875', '0500', '39843847', 1, 'bach sparta jede pro titul kohoutkova uz v nedeli rano!');

-- --------------------------------------------------------

--
-- Table structure for table `bank_account_type`
--

DROP TABLE IF EXISTS `bank_account_type`;
CREATE TABLE IF NOT EXISTS `bank_account_type` (
  `type_id` int(11) NOT NULL AUTO_INCREMENT,
  `type_name` varchar(255) NOT NULL,
  PRIMARY KEY (`type_id`),
  UNIQUE KEY `type_name` (`type_name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `bank_account_type`
--

INSERT INTO `bank_account_type` (`type_id`, `type_name`) VALUES
(2, 'balance'),
(1, 'provision');

-- --------------------------------------------------------

--
-- Table structure for table `branch_has_bank_account`
--

DROP TABLE IF EXISTS `branch_has_bank_account`;
CREATE TABLE IF NOT EXISTS `branch_has_bank_account` (
  `branch_id` int(11) unsigned NOT NULL,
  `bank_account_id` int(11) NOT NULL,
  `bank_account_type` int(11) NOT NULL,
  `is_current` tinyint(1) NOT NULL,
  PRIMARY KEY (`branch_id`,`bank_account_type`,`is_current`),
  KEY `bank_account_id` (`bank_account_id`),
  KEY `bank_account_type` (`bank_account_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `branch_has_bank_account`
--

INSERT INTO `branch_has_bank_account` (`branch_id`, `bank_account_id`, `bank_account_type`, `is_current`) VALUES
(1, 1, 1, 1),
(1, 2, 2, 0),
(1, 3, 2, 1);

-- --------------------------------------------------------

--
-- Table structure for table `currency`
--

DROP TABLE IF EXISTS `currency`;
CREATE TABLE IF NOT EXISTS `currency` (
  `currency_id` int(11) NOT NULL AUTO_INCREMENT,
  `currency_iso` varchar(255) NOT NULL,
  PRIMARY KEY (`currency_id`),
  UNIQUE KEY `currency_iso` (`currency_iso`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `currency`
--

INSERT INTO `currency` (`currency_id`, `currency_iso`) VALUES
(1, 'CZK'),
(2, 'EUR');



--
-- Constraints for table `bank_account`
--
ALTER TABLE `bank_account`
  ADD CONSTRAINT `bank_account_ibfk_1` FOREIGN KEY (`currency`) REFERENCES `currency` (`currency_id`);


--
-- Constraints for table `branch_has_bank_account`
--
ALTER TABLE `branch_has_bank_account`
  ADD CONSTRAINT `branch_has_bank_account_ibfk_1` FOREIGN KEY (`bank_account_id`) REFERENCES `bank_account` (`account_id`),
  ADD CONSTRAINT `branch_has_bank_account_ibfk_2` FOREIGN KEY (`bank_account_type`) REFERENCES `bank_account_type` (`type_id`),
  ADD CONSTRAINT `branch_has_bank_account_ibfk_3` FOREIGN KEY (`branch_id`) REFERENCES `branch` (`id`);

SET foreign_key_checks=0;
