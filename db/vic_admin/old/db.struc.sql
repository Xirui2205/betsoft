-- MySQL dump 10.11
--
-- Host: localhost    Database: vic_admin
-- ------------------------------------------------------
-- Server version	5.0.51a-24+lenny1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `admin`
--

DROP TABLE IF EXISTS `admin`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `admin` (
  `admin_id` int(10) unsigned NOT NULL auto_increment,
  `nick` varchar(16) character set utf8 collate utf8_bin default NULL,
  `heslo` varchar(60) character set utf8 collate utf8_bin default NULL,
  `jmeno` varchar(50) NOT NULL default '',
  `prijmeni` varchar(60) NOT NULL default '',
  `email` varchar(80) NOT NULL default '',
  `telefon` varchar(20) default '',
  `posledni_prihlaseni` datetime default NULL,
  `superadmin` smallint(5) unsigned NOT NULL default '0',
  `zakazany` smallint(6) NOT NULL default '0',
  `klic` varchar(100) NOT NULL default '',
  `block` smallint(5) unsigned NOT NULL,
  `block_ip` varchar(1000) NOT NULL default '',
  `ldap_nick` varchar(100) NOT NULL default '',
  PRIMARY KEY  (`admin_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COMMENT='InnoDB free: 105472 kB';
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `admin_menu`
--

DROP TABLE IF EXISTS `admin_menu`;
/*!50001 DROP VIEW IF EXISTS `admin_menu`*/;
/*!50001 CREATE TABLE `admin_menu` (
  `admin_id` int(10) unsigned,
  `sekce_id` int(10) unsigned,
  `updatex` smallint(5) unsigned,
  `readx` smallint(5) unsigned,
  `deletex` smallint(5) unsigned
) */;

--
-- Temporary table structure for view `admin_prava_sekce`
--

DROP TABLE IF EXISTS `admin_prava_sekce`;
/*!50001 DROP VIEW IF EXISTS `admin_prava_sekce`*/;
/*!50001 CREATE TABLE `admin_prava_sekce` (
  `admin_id` int(10) unsigned,
  `sekce_id` int(10) unsigned,
  `nazev` varchar(80),
  `parent_id` int(10) unsigned,
  `readx` smallint(5) unsigned,
  `updatex` smallint(5) unsigned,
  `deletex` smallint(5) unsigned,
  `poradi` smallint(5) unsigned,
  `zobrazeno` smallint(5) unsigned
) */;

--
-- Table structure for table `chyby`
--

DROP TABLE IF EXISTS `chyby`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `chyby` (
  `chyba_id` int(10) unsigned NOT NULL auto_increment,
  `datum` datetime NOT NULL default '0000-00-00 00:00:00',
  `radek` smallint(5) unsigned NOT NULL default '0',
  `soubor` varchar(80) NOT NULL default '',
  `text` text,
  `code` varchar(40) default NULL,
  `status` smallint(5) unsigned NOT NULL default '0',
  `message` varchar(240) default NULL,
  `poznamka` text,
  `admin_id` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`chyba_id`),
  FULLTEXT KEY `Index_2` (`poznamka`,`message`,`text`)
) ENGINE=MyISAM AUTO_INCREMENT=1590 DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `clen`
--

DROP TABLE IF EXISTS `clen`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `clen` (
  `uid` int(11) NOT NULL auto_increment,
  `jmeno` varchar(100) collate utf8_bin NOT NULL default '',
  `email` varchar(100) collate utf8_bin NOT NULL default '',
  `odpovednost` text collate utf8_bin NOT NULL,
  `tel` varchar(40) collate utf8_bin default NULL,
  `icq` varchar(20) collate utf8_bin default NULL,
  `znalosti` varchar(800) collate utf8_bin default NULL,
  PRIMARY KEY  (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='InnoDB free: 105472 kB';
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `logy`
--

DROP TABLE IF EXISTS `logy`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `logy` (
  `log_id` int(10) unsigned NOT NULL auto_increment,
  `datum` datetime default '0000-00-00 00:00:00',
  `akce` varchar(240) NOT NULL default '',
  `code` varchar(40) NOT NULL default '0',
  `text` text character set utf8 collate utf8_bin NOT NULL,
  `status` smallint(6) NOT NULL default '0',
  `admin_id` int(11) default NULL,
  PRIMARY KEY  (`log_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='InnoDB free: 105472 kB';
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `prava`
--

DROP TABLE IF EXISTS `prava`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `prava` (
  `sekce_id` int(10) unsigned NOT NULL default '0',
  `admin_id` int(10) unsigned NOT NULL default '0',
  `readx` smallint(5) unsigned default '0',
  `updatex` smallint(5) unsigned default '0',
  `deletex` smallint(5) unsigned default '0',
  PRIMARY KEY  (`sekce_id`,`admin_id`),
  KEY `FK_prava_2` (`admin_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='InnoDB free: 105472 kB';
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `sekce`
--

DROP TABLE IF EXISTS `sekce`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `sekce` (
  `sekce_id` int(10) unsigned NOT NULL auto_increment,
  `parent_id` int(10) unsigned NOT NULL default '0',
  `nazev` varchar(80) NOT NULL default '',
  `zobrazeno` smallint(5) unsigned NOT NULL default '1',
  `poradi` smallint(5) unsigned NOT NULL default '0',
  PRIMARY KEY  (`sekce_id`),
  KEY `parent_index` (`parent_id`)
) ENGINE=InnoDB AUTO_INCREMENT=131 DEFAULT CHARSET=utf8 COMMENT='InnoDB free: 105472 kB';
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `session`
--

DROP TABLE IF EXISTS `session`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `session` (
  `ses_id` varchar(50) NOT NULL default '',
  `ip` varchar(10) NOT NULL default '',
  `prohlizec` varchar(100) NOT NULL default '',
  `data` text,
  `time` bigint(20) unsigned NOT NULL default '0',
  `zprava` varchar(140) default NULL,
  `status` smallint(5) unsigned NOT NULL default '0',
  `start` datetime NOT NULL default '0000-00-00 00:00:00',
  `admin_id` int(11) default '0',
  `bookmaker_id` int(11) NOT NULL default '0',
  `old_ses_id` varchar(40) NOT NULL default '',
  `mobile` smallint(6) default '0',
  `pobocka_user_id` int(11) unsigned default NULL,
  PRIMARY KEY  (`ses_id`),
  KEY `ip` (`ip`),
  KEY `prohlizec` (`prohlizec`),
  KEY `old_ses_id` (`old_ses_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='InnoDB free: 105472 kB';
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `ukol`
--

DROP TABLE IF EXISTS `ukol`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `ukol` (
  `ukol_id` int(11) NOT NULL auto_increment,
  `text` varchar(600) collate utf8_bin NOT NULL default '',
  `zahajeni` date NOT NULL,
  `dokonceni` date default NULL,
  `skutecne_dokonceno` date default NULL,
  `poznamka` text collate utf8_bin NOT NULL,
  `priorita` smallint(6) NOT NULL default '1',
  PRIMARY KEY  (`ukol_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='InnoDB free: 105472 kB';
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `ukol_clen`
--

DROP TABLE IF EXISTS `ukol_clen`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `ukol_clen` (
  `uid` int(11) NOT NULL,
  `ukol_id` int(11) NOT NULL,
  `prace` text collate utf8_bin,
  PRIMARY KEY  (`uid`,`ukol_id`),
  KEY `FK_ukol_clen_1` (`ukol_id`),
  CONSTRAINT `FK_ukol_clen_1` FOREIGN KEY (`ukol_id`) REFERENCES `ukol` (`ukol_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_ukol_clen_2` FOREIGN KEY (`uid`) REFERENCES `clen` (`uid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='InnoDB free: 105472 kB; (`ukol_id`) REFER `admin/ukol`(`ukol';
SET character_set_client = @saved_cs_client;

--
-- Dumping routines for database 'vic_admin'
--
DELIMITER ;;
DELIMITER ;

--
-- Final view structure for view `admin_menu`
--

/*!50001 DROP TABLE `admin_menu`*/;
/*!50001 DROP VIEW IF EXISTS `admin_menu`*/;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `admin_menu` AS select `a`.`admin_id` AS `admin_id`,`b`.`sekce_id` AS `sekce_id`,`b`.`updatex` AS `updatex`,`b`.`readx` AS `readx`,`b`.`deletex` AS `deletex` from (`admin` `a` left join `prava` `b` on((`a`.`admin_id` = `b`.`admin_id`))) */;

--
-- Final view structure for view `admin_prava_sekce`
--

/*!50001 DROP TABLE `admin_prava_sekce`*/;
/*!50001 DROP VIEW IF EXISTS `admin_prava_sekce`*/;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `admin_prava_sekce` AS select `a`.`admin_id` AS `admin_id`,`c`.`sekce_id` AS `sekce_id`,`c`.`nazev` AS `nazev`,`c`.`parent_id` AS `parent_id`,`b`.`readx` AS `readx`,`b`.`updatex` AS `updatex`,`b`.`deletex` AS `deletex`,`c`.`poradi` AS `poradi`,`c`.`zobrazeno` AS `zobrazeno` from (`admin` `a` join (`prava` `b` join `sekce` `c` on((`b`.`sekce_id` = `c`.`sekce_id`))) on((`a`.`admin_id` = `b`.`admin_id`))) */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2010-04-14 17:31:01

-- phpMyAdmin SQL Dump
-- version 3.3.2deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 18, 2010 at 06:12 PM
-- Server version: 5.1.41
-- PHP Version: 5.3.2-1ubuntu4.2

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `vic_admin`
--

-- --------------------------------------------------------

--
-- Table structure for table `contract`
--

CREATE TABLE IF NOT EXISTS `contract` (
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

CREATE TABLE IF NOT EXISTS `contract_parameter` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  `template_id` int(10) unsigned NOT NULL,
  `type` varchar(32) NOT NULL,
  `mandatory` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `template_id` (`template_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `contract_parameter`
--


--
-- Constraints for dumped tables
--


--
-- Dumping data for table `contract_parameter`
--


-- --------------------------------------------------------

--
-- Table structure for table `contract_parameter_value`
--

CREATE TABLE IF NOT EXISTS `contract_parameter_value` (
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

CREATE TABLE IF NOT EXISTS `contract_status` (
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

CREATE TABLE IF NOT EXISTS `contract_template` (
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

CREATE TABLE IF NOT EXISTS `host` (
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

-- --------------------------------------------------------

--
-- Table structure for table `host_has_parameter`
--

CREATE TABLE IF NOT EXISTS `host_has_parameter` (
  `host_id` int(10) unsigned NOT NULL DEFAULT '0',
  `parameter_id` int(10) unsigned NOT NULL,
  `value` varchar(32) NOT NULL,
  PRIMARY KEY (`host_id`,`parameter_id`),
  KEY `host_id_2` (`host_id`),
  KEY `parameter_id` (`parameter_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `host_has_parameter`
--


-- --------------------------------------------------------

--
-- Table structure for table `parameter`
--

CREATE TABLE IF NOT EXISTS `parameter` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  `value` varchar(32) NOT NULL,
  `is_host` tinyint(1) NOT NULL,
  `is_branch` tinyint(1) NOT NULL,
  `is_user` tinyint(1) NOT NULL,
  `validator_class` varchar(32) NOT NULL,
  `validator_params` varchar(32) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=12 ;



-- --------------------------------------------------------

--
-- Table structure for table `user_has_parameter`
--

CREATE TABLE IF NOT EXISTS `user_has_parameter` (
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `parameter_id` int(10) unsigned NOT NULL,
  `value` varchar(32) NOT NULL,
  PRIMARY KEY (`user_id`,`parameter_id`),
  KEY `user_id_2` (`user_id`),
  KEY `parameter_id` (`parameter_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `user_has_parameter`
--


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

