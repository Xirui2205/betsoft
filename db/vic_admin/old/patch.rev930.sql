-- phpMyAdmin SQL Dump
-- version 3.3.2deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 18, 2010 at 06:12 PM
-- Server version: 5.1.41
-- PHP Version: 5.3.2-1ubuntu4.2

SET foreign_key_checks=0;
SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `vic_admin`
--

-- --------------------------------------------------------

--
-- Table structure for table `contract`
--
DROP TABLE IF EXISTS `contract`;
CREATE TABLE `contract` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `date_valid_from` date NOT NULL,
  `date_valid_to` date NOT NULL,
  `date_signed` date NOT NULL,
  `contract_status_id` int(10) unsigned NOT NULL,
  `template_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `contract_status_id` (`contract_status_id`),
  KEY `template_id` (`template_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `contract`
--


-- --------------------------------------------------------

--
-- Table structure for table `contract_parameter`
--
DROP TABLE IF EXISTS `contract_parameter`;
CREATE TABLE `contract_parameter` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  `template_id` int(10) unsigned NOT NULL,
  `validator_class` varchar(32) NOT NULL,
  `validator_params` varchar(32) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `template_id` (`template_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `contract_parameter`
--


-- --------------------------------------------------------

--
-- Table structure for table `contract_parameter_value`
--
DROP TABLE IF EXISTS `contract_parameter_value`;
CREATE TABLE `contract_parameter_value` (
  `contract_id` int(10) unsigned NOT NULL,
  `parameter_id` int(10) unsigned NOT NULL,
  `value` varchar(64) NOT NULL,
  PRIMARY KEY (`contract_id`,`parameter_id`),
  KEY `contract_id` (`contract_id`),
  KEY `parameter_id` (`parameter_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `contract_parameter_value`
--


-- --------------------------------------------------------

--
-- Table structure for table `contract_status`
--
DROP TABLE IF EXISTS `contract_status`;
CREATE TABLE `contract_status` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `contract_status`
--

INSERT INTO `contract_status` (`id`, `name`) VALUES
(1, 'opened'),
(2, 'active'),
(3, 'expired'),
(4, 'suspended');

-- --------------------------------------------------------

--
-- Table structure for table `contract_template`
--
DROP TABLE IF EXISTS `contract_template`;
CREATE TABLE `contract_template` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `contract_template`
--


-- --------------------------------------------------------

--
-- Table structure for table `host`
--
DROP TABLE IF EXISTS `host`;
CREATE TABLE `host` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` int(10) unsigned NOT NULL,
  `hardware` text NOT NULL,
  `display` varchar(256) CHARACTER SET utf8 COLLATE utf8_hungarian_ci NOT NULL,
  `printer` varchar(256) NOT NULL,
  `ip` varchar(64) NOT NULL,
  `win_sn` varchar(64) NOT NULL,
  `provider_dns` varchar(64) NOT NULL,
  `provider_gateway` varchar(64) NOT NULL,
  `provider_ip` varchar(32) NOT NULL,
  `provider_username` varchar(32) NOT NULL,
  `provider_password` varchar(32) NOT NULL,
  `vic_email` varchar(32) NOT NULL,
  `vic_email_password` varchar(32) NOT NULL,
  `vic_admin_password` varchar(32) NOT NULL,
  `vic_employee_password_1` varchar(32) NOT NULL,
  `vic_employee_password_2` varchar(32) NOT NULL,
  `note` text,
  PRIMARY KEY (`id`),
  KEY `branch_id` (`branch_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `host`
--

INSERT INTO `host` (`id`, `branch_id`, `hardware`, `display`, `printer`, `ip`, `win_sn`, `provider_dns`, `provider_gateway`, `provider_ip`, `provider_username`, `provider_password`, `vic_email`, `vic_email_password`, `vic_admin_password`, `vic_employee_password_1`, `vic_employee_password_2`, `note`) VALUES
(1, 1, '386 DX2', '14 EIZO', 'canon', '155.155.155.155', 'asfasdfasd', '155.155.155.155', '155.155.155.155', '56465', 'franta', '1234', 'franta@vasdfasd.cz', 'asdf', 'asdf', 'asdf', 'adsf', 'aaa');

-- --------------------------------------------------------

--
-- Dumping data for table `parameter`
--
DROP TABLE IF EXISTS `parameter`;
CREATE TABLE `parameter` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  `value` varchar(32) NOT NULL,
  `is_host` tinyint(1) NOT NULL,
  `is_branch` tinyint(1) NOT NULL,
  `is_user` tinyint(1) NOT NULL,
  `type` varchar(32),
  `mandatory` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=12 ;

--
-- Dumping data for table `parameter`
--

INSERT INTO `parameter` (`id`, `name`, `value`, `is_host`, `is_branch`, `is_user`, `type`, `mandatory`) VALUES
(1, 'branch.clientDeposit', '1', 0, 1, 0, '', 1),
(2, 'branch.clientDepositOnline', '1', 0, 1, 0, '', 1),
(3, 'branch.clientWithdraw', '1', 0, 1, 0, '', 1),
(4, 'branch.branchDeposit', '1', 0, 1, 0, '', 1),
(5, 'branch.branchWithdraw', '1', 0, 1, 0, '', 1),
(6, 'branch.clientBonusProgram', '1', 0, 1, 0, '', 1),
(7, 'branch.cashDeskLimit', '10000', 0, 1, 0, '', 1),
(10, 'duplicateTicketCount', '3', 0, 1, 1, '', 1),
(11, 'user.greeting', 'Dobrý den', 0, 0, 1, '', 1);

DROP TABLE IF EXISTS `branch_has_parameter`;
CREATE TABLE `branch_has_parameter` (
  `branch_id` int(10) unsigned NOT NULL DEFAULT '0',
  `parameter_id` int(10) unsigned NOT NULL,
  `value` varchar(32) NOT NULL,
  PRIMARY KEY (`branch_id`,`parameter_id`),
  KEY `branch_id_2` (`branch_id`),
  KEY `parameter_id` (`parameter_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `host_has_parameter`
--
DROP TABLE IF EXISTS `host_has_parameter`;
CREATE TABLE `host_has_parameter` (
  `host_id` int(10) unsigned NOT NULL DEFAULT '0',
  `parameter_id` int(10) unsigned NOT NULL,
  `value` varchar(32) NOT NULL,
  PRIMARY KEY (`host_id`,`parameter_id`),
  KEY `host_id_2` (`host_id`),
  KEY `parameter_id` (`parameter_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `user_has_parameter`
--
DROP TABLE IF EXISTS `user_has_parameter`;
CREATE TABLE `user_has_parameter` (
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `parameter_id` int(10) unsigned NOT NULL,
  `value` varchar(32) NOT NULL,
  PRIMARY KEY (`user_id`,`parameter_id`),
  KEY `user_id_2` (`user_id`),
  KEY `parameter_id` (`parameter_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `branch_has_parameter`
--
ALTER TABLE `branch_has_parameter`
  ADD CONSTRAINT `branch_has_parameter_ibfk_1` FOREIGN KEY (`branch_id`) REFERENCES `branch` (`id`),
  ADD CONSTRAINT `branch_has_parameter_ibfk_2` FOREIGN KEY (`parameter_id`) REFERENCES `parameter` (`id`);

--
-- Constraints for table `host_has_parameter`
--
ALTER TABLE `host_has_parameter`
  ADD CONSTRAINT `host_has_parameter_ibfk_1` FOREIGN KEY (`host_id`) REFERENCES `host` (`id`),
  ADD CONSTRAINT `host_has_parameter_ibfk_2` FOREIGN KEY (`parameter_id`) REFERENCES `parameter` (`id`);

--
-- Constraints for table `user_has_parameter`
--
ALTER TABLE `user_has_parameter`
  ADD CONSTRAINT `user_has_parameter_ibfk_1` FOREIGN KEY (`parameter_id`) REFERENCES `parameter` (`id`);

-- --------------------------------------------------------

--
-- Structure for view `user`
--
DROP TABLE IF EXISTS `user`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `user` AS select `vic_main`.`uzivatel`.`user_id` AS `user_id`,`vic_main`.`uzivatel`.`jmeno` AS `jmeno`,`vic_main`.`uzivatel`.`prijmeni` AS `prijmeni`,`vic_main`.`uzivatel`.`nick` AS `nick`,`vic_main`.`uzivatel`.`heslo` AS `heslo`,`vic_main`.`uzivatel`.`pohlavi` AS `pohlavi`,`vic_main`.`uzivatel`.`datum_narozeni` AS `datum_narozeni`,`vic_main`.`uzivatel`.`email` AS `email`,`vic_main`.`uzivatel`.`zeme_id` AS `zeme_id`,`vic_main`.`uzivatel`.`ulice` AS `ulice`,`vic_main`.`uzivatel`.`psc` AS `psc`,`vic_main`.`uzivatel`.`telefon` AS `telefon`,`vic_main`.`uzivatel`.`mena_id` AS `mena_id`,`vic_main`.`uzivatel`.`info` AS `info`,`vic_main`.`uzivatel`.`mobil` AS `mobil`,`vic_main`.`uzivatel`.`newsletter` AS `newsletter`,`vic_main`.`uzivatel`.`vyber_status` AS `vyber_status`,`vic_main`.`uzivatel`.`posledni_prihlaseni` AS `posledni_prihlaseni`,`vic_main`.`uzivatel`.`vyprseni_session` AS `vyprseni_session`,`vic_main`.`uzivatel`.`datum_registrace` AS `datum_registrace`,`vic_main`.`uzivatel`.`individualni_max_vklad` AS `individualni_max_vklad`,`vic_main`.`uzivatel`.`zakazany` AS `zakazany`,`vic_main`.`uzivatel`.`lang_id` AS `lang_id`,`vic_main`.`uzivatel`.`misto` AS `misto`,`vic_main`.`uzivatel`.`ucet_status` AS `ucet_status`,`vic_main`.`uzivatel`.`vyhernost` AS `vyhernost`,`vic_main`.`uzivatel`.`block` AS `block`,`vic_main`.`uzivatel`.`block_ip` AS `block_ip`,`vic_main`.`uzivatel`.`vyhernost_game` AS `vyhernost_game`,`vic_main`.`uzivatel`.`bet_stats_win` AS `bet_stats_win`,`vic_main`.`uzivatel`.`bet_stats_lose_acc` AS `bet_stats_lose_acc`,`vic_main`.`uzivatel`.`bet_stats_lose_book` AS `bet_stats_lose_book`,`vic_main`.`uzivatel`.`bet_total` AS `bet_total`,`vic_main`.`uzivatel`.`win_ticket` AS `win_ticket`,`vic_main`.`uzivatel`.`lose_ticket` AS `lose_ticket`,`vic_main`.`uzivatel`.`delete_ticket` AS `delete_ticket`,`vic_main`.`uzivatel`.`num_bet_ticket` AS `num_bet_ticket`,`vic_main`.`uzivatel`.`bet_total2` AS `bet_total2`,`vic_main`.`uzivatel`.`ticket_num` AS `ticket_num`,`vic_main`.`uzivatel`.`finance_rating` AS `finance_rating`,`vic_main`.`uzivatel`.`max_bet` AS `max_bet`,`vic_main`.`uzivatel`.`book_info` AS `book_info`,`vic_main`.`uzivatel`.`self_excluded_until` AS `self_excluded_until`,`vic_main`.`uzivatel`.`osloveni` AS `osloveni`,`vic_main`.`uzivatel`.`e_testovaci` AS `e_testovaci`,`vic_main`.`uzivatel`.`block_play` AS `block_play`,`vic_main`.`uzivatel`.`castka_m` AS `castka_m`,`vic_main`.`uzivatel`.`castka_w` AS `castka_w`,`vic_main`.`uzivatel`.`datum_aktivace` AS `datum_aktivace` from `vic_main`.`uzivatel`;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `contract`
--
ALTER TABLE `contract`
  ADD CONSTRAINT `contract_ibfk_2` FOREIGN KEY (`template_id`) REFERENCES `contract_template` (`id`),
  ADD CONSTRAINT `contract_ibfk_1` FOREIGN KEY (`contract_status_id`) REFERENCES `contract_status` (`id`);

--
-- Constraints for table `contract_parameter`
--
ALTER TABLE `contract_parameter`
  ADD CONSTRAINT `contract_parameter_ibfk_1` FOREIGN KEY (`template_id`) REFERENCES `contract_template` (`id`);

--
-- Constraints for table `contract_parameter_value`
--
ALTER TABLE `contract_parameter_value`
  ADD CONSTRAINT `contract_parameter_value_ibfk_2` FOREIGN KEY (`parameter_id`) REFERENCES `parameter` (`id`),
  ADD CONSTRAINT `contract_parameter_value_ibfk_1` FOREIGN KEY (`contract_id`) REFERENCES `contract` (`id`);

--
-- Constraints for table `host`
--
ALTER TABLE `host`
  ADD CONSTRAINT `host_ibfk_1` FOREIGN KEY (`branch_id`) REFERENCES `branch` (`id`);

SET foreign_key_checks=1;
