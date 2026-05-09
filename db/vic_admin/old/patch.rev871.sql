-- phpMyAdmin SQL Dump
-- version 3.3.2deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jul 30, 2010 at 06:21 PM
-- Server version: 5.1.41
-- PHP Version: 5.3.2-1ubuntu4.2

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `vic_admin`
--

-- --------------------------------------------------------

--
-- Table structure for table `branch`
--

CREATE TABLE IF NOT EXISTS `branch` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type_id` int(10) unsigned NOT NULL,
  `region_id` int(10) unsigned NOT NULL,
  `name` varchar(256) NOT NULL,
  `handle` varchar(16) NOT NULL,
  `street` text NOT NULL,
  `town` varchar(64) NOT NULL,
  `zip` varchar(32) NOT NULL,
  `ticket_header` varchar(256) NOT NULL,
  `phone` varchar(64) NOT NULL,
  `email` varchar(64) NOT NULL,
  `status_id` int(10) NOT NULL,
  `provider_name` varchar(64) NOT NULL,
  `provider_address` text NOT NULL,
  `provider_ic` varchar(32) NOT NULL,
  `provider_dic` varchar(32) NOT NULL,
  `provider_email` varchar(64) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `type_id` (`type_id`),
  KEY `region_id` (`region_id`),
  KEY `status_id` (`status_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=123456790 ;

--
-- Dumping data for table `branch`
--

INSERT INTO `branch` (`id`, `type_id`, `region_id`, `name`, `handle`, `street`, `town`, `zip`, `ticket_header`, `phone`, `email`, `status_id`, `provider_name`, `provider_address`, `provider_ic`, `provider_dic`, `provider_email`) VALUES
(1, 1, 1, 'Bohdalec', 'asd', 'Bohdalecká 8', 'Praha', '0098', 'CompBet Bohdalec', '6088212781', 'vtbohdalec@compbet.com', 1, 'Filip Veselý', 'blabla...', '789456123', 'CZ78945613', 'vesely@compbet.com'),
(2, 2, 2, 'Palmovkayyzzaa', 'qwe', 'Bohumila Hrabala 123', 'Praha', '18000', 'PalmiceFFx', '325654264lfyj', 'palmovka@compbet.com', 2, 'Franta Pepa', 'Sterboholy 13, Praha', '23565236', 'CZ252353', 'franta@compbet.com');

-- --------------------------------------------------------

--
-- Table structure for table `branch_has_param`
--

CREATE TABLE IF NOT EXISTS `branch_has_param` (
  `branch_id` int(10) unsigned DEFAULT NULL,
  `branch_param_id` int(10) unsigned NOT NULL,
  `value` varchar(32) NOT NULL,
  KEY `branch_id` (`branch_id`,`branch_param_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `branch_has_param`
--

INSERT INTO `branch_has_param` (`branch_id`, `branch_param_id`, `value`) VALUES
(NULL, 1, '1'),
(NULL, 2, '1'),
(NULL, 3, '1'),
(NULL, 4, '1'),
(NULL, 5, '1'),
(NULL, 6, '1'),
(NULL, 7, '10000'),
(1, 7, '15000'),
(1, 1, '0'),
(0, 8, 'nazdar'),
(NULL, 8, 'nazdar');

-- --------------------------------------------------------

--
-- Table structure for table `branch_param`
--

CREATE TABLE IF NOT EXISTS `branch_param` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;

--
-- Dumping data for table `branch_param`
--

INSERT INTO `branch_param` (`id`, `name`) VALUES
(1, 'client-deposit'),
(3, 'client-withdraw'),
(4, 'branch-deposit'),
(5, 'branch-withdraw'),
(2, 'client-deposit-online'),
(6, 'client-bonus-program'),
(7, 'cash-desk-limit'),
(8, 'pozdrav');

-- --------------------------------------------------------

--
-- Table structure for table `branch_status`
--

CREATE TABLE IF NOT EXISTS `branch_status` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `branch_status`
--

INSERT INTO `branch_status` (`id`, `name`) VALUES
(1, 'online'),
(2, 'offline');

-- --------------------------------------------------------

--
-- Table structure for table `branch_type`
--

CREATE TABLE IF NOT EXISTS `branch_type` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `branch_type`
--

INSERT INTO `branch_type` (`id`, `name`) VALUES
(1, 'herna'),
(2, 'kasino'),
(3, 'trafika');

-- --------------------------------------------------------

--
-- Table structure for table `region`
--

CREATE TABLE IF NOT EXISTS `region` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `region`
--

INSERT INTO `region` (`id`, `name`) VALUES
(1, 'Praha'),
(2, 'Středočeský kraj');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `branch`
--
ALTER TABLE `branch`
  ADD CONSTRAINT `branch_ibfk_1` FOREIGN KEY (`type_id`) REFERENCES `branch_type` (`id`),
  ADD CONSTRAINT `branch_ibfk_2` FOREIGN KEY (`status_id`) REFERENCES `branch_status` (`id`),
  ADD CONSTRAINT `branch_ibfk_3` FOREIGN KEY (`region_id`) REFERENCES `region` (`id`);
