CREATE DATABASE  `vic_ut_main` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;

-- MySQL dump 10.13  Distrib 5.1.51, for unknown-linux-gnu (x86_64)
--
-- Host: localhost    Database: vic_main
-- ------------------------------------------------------
-- Server version	5.1.51

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
-- Temporary table structure for view `admin`
--

DROP TABLE IF EXISTS `admin`;
/*!50001 DROP VIEW IF EXISTS `admin`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `admin` (
  `user_id` int(10) unsigned,
  `block` smallint(5) unsigned,
  `block_ip` varchar(1000),
  `jmeno` varchar(50),
  `prijmeni` varchar(60),
  `nick` varchar(20),
  `heslo` varchar(600),
  `pohlavi` enum('m','f'),
  `datum_narozeni` date,
  `email` varchar(100),
  `zeme_id` smallint(5) unsigned,
  `ulice` varchar(100),
  `psc` varchar(15),
  `telefon` varchar(30),
  `mena_id` smallint(5) unsigned,
  `info` text,
  `mobil` varchar(30),
  `newsletter` smallint(5) unsigned,
  `vyber_status` smallint(5) unsigned,
  `posledni_prihlaseni` datetime,
  `vyprseni_session` smallint(5) unsigned,
  `datum_registrace` datetime,
  `individualni_max_vklad` int(10) unsigned,
  `zakazany` smallint(5) unsigned,
  `ucet_status` smallint(5) unsigned,
  `self_excluded_until` timestamp
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `approval_group`
--

DROP TABLE IF EXISTS `approval_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `approval_group` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='skupiny pro schvalovani bookmakerem';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `approval_group_threshold`
--

DROP TABLE IF EXISTS `approval_group_threshold`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `approval_group_threshold` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `approval_group_id` int(10) unsigned NOT NULL,
  `odd_upper_threshold` double(8,2) NOT NULL,
  `stake_lower_threshold` float NOT NULL,
  PRIMARY KEY (`id`),
  KEY `approval_group_id` (`approval_group_id`),
  CONSTRAINT `approval_group_threshold_ibfk_1` FOREIGN KEY (`approval_group_id`) REFERENCES `approval_group` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `archive_cronjob`
--

DROP TABLE IF EXISTS `archive_cronjob`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `archive_cronjob` (
  `cronjob_id` int(11) NOT NULL,
  `cronjob_type` int(10) NOT NULL,
  `result` int(10) DEFAULT NULL,
  `attempts` int(2) NOT NULL,
  `exec_at` datetime NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  PRIMARY KEY (`cronjob_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `archive_cronjob_param`
--

DROP TABLE IF EXISTS `archive_cronjob_param`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `archive_cronjob_param` (
  `param_id` int(11) NOT NULL,
  `cronjob_id` int(11) NOT NULL,
  `param_name` varchar(255) NOT NULL,
  `param_value` text NOT NULL,
  PRIMARY KEY (`param_id`),
  KEY `cronjob_id` (`cronjob_id`),
  CONSTRAINT `archive_cronjob_param_ibfk_1` FOREIGN KEY (`cronjob_id`) REFERENCES `archive_cronjob` (`cronjob_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `bankovni_ucty`
--

DROP TABLE IF EXISTS `bankovni_ucty`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bankovni_ucty` (
  `nl_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `jmeno_banky` varchar(200) DEFAULT NULL,
  `cislo_uctu` int(16) unsigned NOT NULL,
  `kod_banky` smallint(6) unsigned NOT NULL,
  PRIMARY KEY (`nl_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `bankovni_ucty_fk` FOREIGN KEY (`user_id`) REFERENCES `uzivatel` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `bet_free_alias`
--

DROP TABLE IF EXISTS `bet_free_alias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bet_free_alias` (
  `alias` int(10) unsigned NOT NULL,
  PRIMARY KEY (`alias`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `bet_handle_range`
--

DROP TABLE IF EXISTS `bet_handle_range`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bet_handle_range` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `event_id` int(10) unsigned NOT NULL,
  `typ_id` int(10) unsigned NOT NULL,
  `from` int(10) unsigned NOT NULL,
  `to` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `event_id` (`event_id`,`typ_id`,`from`,`to`),
  KEY `typ_id` (`typ_id`),
  CONSTRAINT `bet_handle_range_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `udalost` (`udalost_id`),
  CONSTRAINT `bet_handle_range_ibfk_2` FOREIGN KEY (`typ_id`) REFERENCES `typ` (`typ_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `bet_settings`
--

DROP TABLE IF EXISTS `bet_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bet_settings` (
  `udalost_id` int(10) unsigned NOT NULL DEFAULT '0',
  `typ_id` int(10) unsigned NOT NULL DEFAULT '0',
  `podtyp_id` int(10) unsigned NOT NULL DEFAULT '0',
  `kurz_min` decimal(6,2) NOT NULL DEFAULT '0.00',
  `kurz_max` decimal(6,2) NOT NULL DEFAULT '0.00',
  `vyhernost_min` decimal(3,2) NOT NULL DEFAULT '0.00',
  `vyhernost_max` decimal(3,2) NOT NULL DEFAULT '0.00',
  `sport_id` int(10) unsigned NOT NULL DEFAULT '0',
  `risk_limit` int(10) unsigned NOT NULL DEFAULT '3000',
  PRIMARY KEY (`udalost_id`,`typ_id`,`podtyp_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='InnoDB free: 27648 kB; (`udalost_id`) REFER `game/udalost`(`';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `bet_stats`
--

DROP TABLE IF EXISTS `bet_stats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bet_stats` (
  `bet_total` decimal(18,4) NOT NULL DEFAULT '0.0000',
  `pay_to_user` decimal(18,4) NOT NULL DEFAULT '0.0000',
  `user_lose_acc` decimal(18,4) NOT NULL DEFAULT '0.0000',
  `user_lose_book` decimal(18,4) NOT NULL DEFAULT '0.0000',
  `win_ticket` bigint(20) unsigned NOT NULL DEFAULT '0',
  `lose_ticket` bigint(20) unsigned NOT NULL DEFAULT '0',
  `delete_ticket` bigint(20) unsigned NOT NULL DEFAULT '0',
  `num_bet_ticket` bigint(20) unsigned NOT NULL DEFAULT '0',
  `bet_total2` decimal(18,4) NOT NULL DEFAULT '0.0000',
  `ticket_num` bigint(20) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='InnoDB free: 105472 kB';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `bet_stats_winners`
--

DROP TABLE IF EXISTS `bet_stats_winners`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bet_stats_winners` (
  `ticket_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `amount` decimal(12,2) unsigned NOT NULL DEFAULT '0.00',
  `user` varchar(100) COLLATE utf8_bin NOT NULL DEFAULT '',
  `wl` smallint(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`ticket_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='InnoDB free: 105472 kB';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `book_board`
--

DROP TABLE IF EXISTS `book_board`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `book_board` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bookmaker_id` int(11) DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `subject` varchar(200) CHARACTER SET utf8 DEFAULT NULL,
  `body` text CHARACTER SET utf8,
  `priority` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 COMMENT='InnoDB free: 105472 kB';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `bookmaker`
--

DROP TABLE IF EXISTS `bookmaker`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bookmaker` (
  `bookmaker_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` int(10) unsigned NOT NULL,
  `jmeno` varchar(40) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `prijmeni` varchar(60) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `nick` varchar(20) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `heslo` varchar(80) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `telefon` varchar(50) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `super` smallint(6) NOT NULL DEFAULT '0',
  `zakazany` smallint(6) NOT NULL DEFAULT '0',
  `email` varchar(100) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `ldap_nick` varchar(100) CHARACTER SET utf8 NOT NULL DEFAULT '',
  PRIMARY KEY (`bookmaker_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='InnoDB free: 105472 kB';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `bookmaker_ticket_prove_log`
--

DROP TABLE IF EXISTS `bookmaker_ticket_prove_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bookmaker_ticket_prove_log` (
  `user_id` int(10) unsigned NOT NULL,
  `bookmaker_id` int(10) unsigned NOT NULL,
  `sazky` varchar(500) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `req_amount` decimal(10,2) NOT NULL,
  `prove_amount` decimal(10,2) NOT NULL,
  `status` smallint(5) unsigned NOT NULL,
  `date` datetime NOT NULL,
  KEY `FK_bookmaker_ticket_prove_log_1` (`user_id`),
  KEY `FK_bookmaker_ticket_prove_log_2` (`bookmaker_id`),
  CONSTRAINT `FK_bookmaker_ticket_prove_log_1` FOREIGN KEY (`user_id`) REFERENCES `uzivatel` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='InnoDB free: 105472 kB; (`user_id`) REFER `game/uzivatel`(`u';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `bookmaker_timesheet`
--

DROP TABLE IF EXISTS `bookmaker_timesheet`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bookmaker_timesheet` (
  `bookmaker_id` int(10) unsigned NOT NULL,
  `day` date NOT NULL,
  `arrival` time NOT NULL,
  `departure` time DEFAULT NULL,
  PRIMARY KEY (`bookmaker_id`,`day`),
  KEY `bookmaker_id` (`bookmaker_id`),
  CONSTRAINT `bookmaker_timesheet_ibfk_1` FOREIGN KEY (`bookmaker_id`) REFERENCES `bookmaker` (`bookmaker_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `chyby`
--

DROP TABLE IF EXISTS `chyby`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `chyby` (
  `chyba_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `datum` datetime DEFAULT NULL,
  `radek` smallint(5) unsigned DEFAULT NULL,
  `soubor` varchar(80) NOT NULL DEFAULT '',
  `text` text,
  `status` smallint(5) NOT NULL DEFAULT '0',
  `code` varchar(40) DEFAULT '',
  `message` varchar(240) DEFAULT NULL,
  `poznamka` text,
  `admin_id` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`chyba_id`),
  FULLTEXT KEY `Index_2` (`poznamka`,`message`,`text`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='InnoDB free: 4096 kB';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `content`
--

DROP TABLE IF EXISTS `content`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `content` (
  `location_id` int(3) NOT NULL,
  `image_name` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `help` text NOT NULL,
  PRIMARY KEY (`location_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `content_data`
--

DROP TABLE IF EXISTS `content_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `content_data` (
  `location_id` int(3) NOT NULL,
  `lang_id` int(3) NOT NULL,
  `position_id` int(3) NOT NULL,
  `text` text NOT NULL,
  UNIQUE KEY `location_id` (`location_id`,`lang_id`,`position_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `controller_convert`
--

DROP TABLE IF EXISTS `controller_convert`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `controller_convert` (
  `c_id` mediumint(8) unsigned NOT NULL,
  `lang_id` smallint(3) NOT NULL,
  `parent_id` smallint(3) NOT NULL,
  `poradi` smallint(3) NOT NULL,
  `zobrazeno` smallint(3) NOT NULL,
  `cols` smallint(3) NOT NULL DEFAULT '3',
  `left_side` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `right_side` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `title` varchar(400) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` varchar(600) COLLATE utf8_unicode_ci DEFAULT NULL,
  `text` varchar(600) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `req_controller` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `req_action` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `real_controller` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `real_action` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`c_id`,`lang_id`),
  KEY `c_id` (`c_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `controller_has_content`
--

DROP TABLE IF EXISTS `controller_has_content`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `controller_has_content` (
  `controller_id` int(3) NOT NULL,
  `location_id` int(3) NOT NULL,
  UNIQUE KEY `controller_id` (`controller_id`,`location_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `controller_has_promo`
--

DROP TABLE IF EXISTS `controller_has_promo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `controller_has_promo` (
  `controller_id` int(3) NOT NULL,
  `promo_id` int(3) NOT NULL,
  `lang_id` int(3) NOT NULL,
  `priority` smallint(3) NOT NULL,
  PRIMARY KEY (`controller_id`,`promo_id`,`lang_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `controller_preklady`
--

DROP TABLE IF EXISTS `controller_preklady`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `controller_preklady` (
  `preklad_id` int(11) NOT NULL,
  `c_id` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`preklad_id`,`c_id`),
  KEY `c_id` (`c_id`),
  CONSTRAINT `controller_preklady_fk` FOREIGN KEY (`c_id`) REFERENCES `controller_convert` (`c_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cronjob`
--

DROP TABLE IF EXISTS `cronjob`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cronjob` (
  `cronjob_id` int(11) NOT NULL AUTO_INCREMENT,
  `cronjob_type` int(10) unsigned NOT NULL,
  `result` int(10) DEFAULT NULL,
  `attempts` int(2) NOT NULL DEFAULT '0',
  `exec_at` datetime DEFAULT NULL,
  `executed_at` datetime DEFAULT NULL,
  `exec_constantly_at` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`cronjob_id`),
  KEY `cronjob_type` (`cronjob_type`),
  CONSTRAINT `cronjob_ibfk_1` FOREIGN KEY (`cronjob_type`) REFERENCES `cronjob_type` (`type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cronjob_param`
--

DROP TABLE IF EXISTS `cronjob_param`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cronjob_param` (
  `param_id` int(11) NOT NULL AUTO_INCREMENT,
  `cronjob_id` int(11) NOT NULL,
  `param_name` varchar(255) NOT NULL,
  `param_value` text NOT NULL,
  PRIMARY KEY (`param_id`),
  KEY `cronjob_id` (`cronjob_id`),
  CONSTRAINT `cronjob_param_ibfk_1` FOREIGN KEY (`cronjob_id`) REFERENCES `cronjob` (`cronjob_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cronjob_type`
--

DROP TABLE IF EXISTS `cronjob_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cronjob_type` (
  `type_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type_name` varchar(100) NOT NULL,
  PRIMARY KEY (`type_id`),
  UNIQUE KEY `type_name` (`type_name`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cupon_data`
--

DROP TABLE IF EXISTS `cupon_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cupon_data` (
  `user_id` int(11) NOT NULL,
  `data` text NOT NULL,
  `status` smallint(5) unsigned NOT NULL DEFAULT '0',
  `date` datetime NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `live` smallint(6) DEFAULT '0',
  `live_confirm` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='InnoDB free: 105472 kB';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `emial_templates`
--

DROP TABLE IF EXISTS `emial_templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `emial_templates` (
  `template_id` int(5) NOT NULL AUTO_INCREMENT,
  `template_cs` text NOT NULL,
  `template_en` text NOT NULL,
  `template_sk` text NOT NULL,
  PRIMARY KEY (`template_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `favorite_event`
--

DROP TABLE IF EXISTS `favorite_event`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `favorite_event` (
  `udalost_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`udalost_id`,`user_id`),
  KEY `FK_favorite_event_2` (`user_id`),
  CONSTRAINT `FK_favorite_event_1` FOREIGN KEY (`udalost_id`) REFERENCES `udalost` (`udalost_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_favorite_event_2` FOREIGN KEY (`user_id`) REFERENCES `uzivatel` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='InnoDB free: 105472 kB; (`udalost_id`) REFER `game/udalost`(';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `financial_transaction`
--

DROP TABLE IF EXISTS `financial_transaction`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `financial_transaction` (
  `transaction_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned DEFAULT NULL,
  `value` decimal(11,2) NOT NULL DEFAULT '0.00',
  `type_id` int(10) unsigned NOT NULL DEFAULT '1' COMMENT '1 vklad 2 vyber',
  `time` datetime NOT NULL,
  `mena_id` smallint(5) unsigned NOT NULL,
  `branch_id` int(10) unsigned DEFAULT NULL,
  `note` varchar(256) NOT NULL,
  `ticket_id` bigint(20) unsigned DEFAULT NULL,
  `status` enum('ok','pending','canceled') NOT NULL,
  `okTime` datetime DEFAULT NULL,
  `cancelTime` datetime DEFAULT NULL,
  `balance` decimal(11,2) DEFAULT NULL,
  PRIMARY KEY (`transaction_id`),
  KEY `mena_id` (`mena_id`),
  KEY `user_id` (`user_id`),
  KEY `branch_id` (`branch_id`),
  KEY `ticket_id` (`ticket_id`),
  KEY `type_id` (`type_id`),
  KEY `status` (`status`),
  KEY `time` (`time`),
  CONSTRAINT `finacni_transakce_fk` FOREIGN KEY (`mena_id`) REFERENCES `mena` (`mena_id`) ON UPDATE CASCADE,
  CONSTRAINT `finacni_transakce_fk1` FOREIGN KEY (`user_id`) REFERENCES `uzivatel` (`user_id`) ON UPDATE CASCADE,
  CONSTRAINT `financial_transaction_ibfk_2` FOREIGN KEY (`ticket_id`) REFERENCES `ticket` (`ticket_id`),
  CONSTRAINT `financial_transaction_ibfk_3` FOREIGN KEY (`type_id`) REFERENCES `financial_transaction_type` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `financial_transaction_type`
--

DROP TABLE IF EXISTS `financial_transaction_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `financial_transaction_type` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `note` text NOT NULL,
  `from` varchar(8) DEFAULT NULL,
  `thru` varchar(8) DEFAULT NULL,
  `to` varchar(8) DEFAULT NULL,
  `need_confirm` tinyint(1) NOT NULL,
  `debiting` tinyint(1) NOT NULL,
  `account_type` enum('user','branch','other') NOT NULL,
  `low_limit` decimal(11,2) DEFAULT NULL,
  `high_limit` decimal(11,2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `galerie`
--

DROP TABLE IF EXISTS `galerie`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `galerie` (
  `image_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL DEFAULT '',
  `popis` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`image_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COMMENT='InnoDB free: 105472 kB';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `galerie_alt`
--

DROP TABLE IF EXISTS `galerie_alt`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `galerie_alt` (
  `image_id` int(10) unsigned NOT NULL,
  `lang_id` int(10) unsigned NOT NULL DEFAULT '0',
  `alt` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`image_id`,`lang_id`),
  KEY `FK_galerie_alt_1` (`lang_id`),
  CONSTRAINT `FK_galerie_alt_1` FOREIGN KEY (`lang_id`) REFERENCES `jazyky` (`lang_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_galerie_alt_2` FOREIGN KEY (`image_id`) REFERENCES `galerie` (`image_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='InnoDB free: 105472 kB; (`lang_id`) REFER `game/jazyky`(`lan';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `galerie_menu`
--

DROP TABLE IF EXISTS `galerie_menu`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `galerie_menu` (
  `galerie_menu_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nazev` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`galerie_menu_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='InnoDB free: 105472 kB';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Temporary table structure for view `galerie_pohled`
--

DROP TABLE IF EXISTS `galerie_pohled`;
/*!50001 DROP VIEW IF EXISTS `galerie_pohled`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `galerie_pohled` (
  `image_id` int(10) unsigned,
  `name` varchar(100),
  `popis` varchar(255),
  `galerie_menu_id` int(10) unsigned,
  `alt` varchar(255),
  `lang_id` int(10) unsigned
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `galerie_prirad_menu`
--

DROP TABLE IF EXISTS `galerie_prirad_menu`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `galerie_prirad_menu` (
  `image_id` int(10) unsigned NOT NULL,
  `galerie_menu_id` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`image_id`,`galerie_menu_id`),
  KEY `FK_galerie_prirad_menu_2` (`galerie_menu_id`),
  CONSTRAINT `FK_galerie_prirad_menu_1` FOREIGN KEY (`image_id`) REFERENCES `galerie` (`image_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_galerie_prirad_menu_2` FOREIGN KEY (`galerie_menu_id`) REFERENCES `galerie_menu` (`galerie_menu_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='InnoDB free: 105472 kB; (`image_id`) REFER `game/galerie`(`i';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `hot_bet`
--

DROP TABLE IF EXISTS `hot_bet`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hot_bet` (
  `sazka_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`sazka_id`),
  CONSTRAINT `FK_hot_bet_1` FOREIGN KEY (`sazka_id`) REFERENCES `sazky` (`sazka_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=638675 DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci COMMENT='InnoDB free: 105472 kB; (`sazka_id`) REFER `game/sazky`(`saz';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ip2country`
--

DROP TABLE IF EXISTS `ip2country`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ip2country` (
  `lower_bound` bigint(20) NOT NULL,
  `upper_bound` bigint(20) NOT NULL,
  `iso` char(2) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `iso3` char(3) NOT NULL DEFAULT '',
  `country_name` char(50) NOT NULL DEFAULT '',
  KEY `lower_bound` (`lower_bound`,`upper_bound`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='InnoDB free: 105472 kB';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `isocur`
--

DROP TABLE IF EXISTS `isocur`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `isocur` (
  `isocur_ID` int(11) NOT NULL AUTO_INCREMENT,
  `isocur_CODE` char(3) NOT NULL DEFAULT '',
  `isocur_NUM` int(3) NOT NULL,
  `isocur_name` varchar(255) NOT NULL DEFAULT '',
  `isocur_stav` int(2) NOT NULL,
  PRIMARY KEY (`isocur_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='InnoDB free: 105472 kB';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `jazyky`
--

DROP TABLE IF EXISTS `jazyky`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jazyky` (
  `lang_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `iso` varchar(2) NOT NULL DEFAULT '',
  `pozice` smallint(5) unsigned NOT NULL DEFAULT '0',
  `alt_text` varchar(45) DEFAULT NULL,
  `zobrazeno` smallint(5) unsigned NOT NULL DEFAULT '0',
  `lc_local` varchar(20) NOT NULL DEFAULT '',
  `iso_homepage` varchar(2) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `kontakt` varchar(30) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '' COMMENT 'jmeno virtualni konktaktni osoby pro kazdy jazyk',
  `flag_oblast_id` int(11) NOT NULL,
  PRIMARY KEY (`lang_id`),
  UNIQUE KEY `lang_id` (`lang_id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8 COMMENT='InnoDB free: 105472 kB';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `kombinace_druh`
--

DROP TABLE IF EXISTS `kombinace_druh`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kombinace_druh` (
  `udalost_id_1` int(10) unsigned NOT NULL,
  `typ_id_1` smallint(5) unsigned NOT NULL,
  `udalost_id_2` int(10) unsigned NOT NULL,
  `typ_id_2` smallint(5) unsigned NOT NULL,
  `viditelnost` smallint(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`udalost_id_1`,`typ_id_1`,`udalost_id_2`,`typ_id_2`),
  KEY `FK_kombinace_druh_2` (`udalost_id_2`),
  CONSTRAINT `FK_kombinace_druh_1` FOREIGN KEY (`udalost_id_1`) REFERENCES `udalost` (`udalost_id`) ON UPDATE CASCADE,
  CONSTRAINT `FK_kombinace_druh_2` FOREIGN KEY (`udalost_id_2`) REFERENCES `udalost` (`udalost_id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='InnoDB free: 105472 kB; (`udalost_id_1`) REFER `game/udalost';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `kraj`
--

DROP TABLE IF EXISTS `kraj`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kraj` (
  `kraj_id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `nazev` varchar(20) CHARACTER SET utf8 NOT NULL DEFAULT '',
  PRIMARY KEY (`kraj_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `kurz`
--

DROP TABLE IF EXISTS `kurz`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kurz` (
  `id_kurz` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `platny_od` date NOT NULL DEFAULT '0000-00-00',
  `platny_do` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`id_kurz`),
  KEY `Index_2` (`platny_od`,`platny_do`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='InnoDB free: 105472 kB';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `kurz_mena`
--

DROP TABLE IF EXISTS `kurz_mena`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kurz_mena` (
  `id_kurz` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_mena` smallint(5) unsigned NOT NULL DEFAULT '0',
  `kurz` decimal(20,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id_kurz`,`id_mena`),
  KEY `FK_kurz_mena_2` (`id_mena`),
  CONSTRAINT `FK_kurz_mena_1` FOREIGN KEY (`id_kurz`) REFERENCES `kurz` (`id_kurz`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_kurz_mena_2` FOREIGN KEY (`id_mena`) REFERENCES `mena` (`mena_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='InnoDB free: 105472 kB; (`id_kurz`) REFER `game/kurz`(`id_ku';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Temporary table structure for view `kurzmena`
--

DROP TABLE IF EXISTS `kurzmena`;
/*!50001 DROP VIEW IF EXISTS `kurzmena`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `kurzmena` (
  `id_kurz` int(10) unsigned,
  `platny_od` date,
  `platny_do` date,
  `kurz` decimal(20,2),
  `mena_id` smallint(5) unsigned,
  `mena_text` varchar(20)
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `limity`
--

DROP TABLE IF EXISTS `limity`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `limity` (
  `sport_id` int(11) unsigned NOT NULL,
  `limit_den` int(11) DEFAULT '1000',
  UNIQUE KEY `sport_id` (`sport_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `limity_user`
--

DROP TABLE IF EXISTS `limity_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `limity_user` (
  `user_id` int(11) unsigned NOT NULL,
  `sport_id` int(11) unsigned NOT NULL,
  `vycerpal` int(11) unsigned NOT NULL DEFAULT '0',
  `limit_den_individual` int(11) unsigned DEFAULT NULL COMMENT 'pokud je NULL nema nastaven',
  KEY `user_id` (`user_id`),
  KEY `sport_id` (`sport_id`),
  CONSTRAINT `limity_user_fk` FOREIGN KEY (`user_id`) REFERENCES `uzivatel` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `limity_user_fk1` FOREIGN KEY (`sport_id`) REFERENCES `sport` (`sport_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Temporary table structure for view `live`
--

DROP TABLE IF EXISTS `live`;
/*!50001 DROP VIEW IF EXISTS `live`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `live` (
  `event_id` int(10) unsigned,
  `start_date` datetime,
  `stav` smallint(5) unsigned,
  `no_comb` smallint(5) unsigned,
  `l_sport_id` int(10) unsigned,
  `l_udalost_id` int(10) unsigned,
  `vypnuto` smallint(5) unsigned,
  `home_team` varchar(200),
  `away_team` varchar(200),
  `live_bookmaker_id` smallint(5) unsigned,
  `sazka_id` int(10) unsigned,
  `proplatil_bookmaker` int(11),
  `proplacena` smallint(5) unsigned,
  `platna_od` datetime,
  `platna_do` datetime,
  `status` smallint(5) unsigned,
  `live` smallint(5) unsigned,
  `kurz_zmena` smallint(6),
  `bookmaker_id` int(10) unsigned,
  `vysledek` varchar(250),
  `udalost_id` int(10) unsigned,
  `typ_id` int(10) unsigned,
  `podtyp_id` int(10) unsigned,
  `overena` smallint(5) unsigned,
  `text` varchar(255),
  `jednoducha` smallint(5) unsigned,
  `risk_limit` int(10) unsigned,
  `risk_limit_balance` decimal(10,2) unsigned,
  `close` smallint(5) unsigned,
  `aktualizace` datetime,
  `aktualizace_sazka` datetime,
  `poradi` int(10) unsigned,
  `kurz` decimal(10,2),
  `platny_od` datetime,
  `nazev` varchar(100),
  `sloupec_id` int(10) unsigned,
  `poradi_sloupec` smallint(5) unsigned,
  `betradar_sazka_id` int(11),
  `no_update` smallint(6)
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `live_1001`
--

DROP TABLE IF EXISTS `live_1001`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `live_1001` (
  `event_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `score_home` smallint(5) unsigned NOT NULL DEFAULT '0',
  `score_away` smallint(5) unsigned NOT NULL DEFAULT '0',
  `first_half_home` smallint(5) unsigned NOT NULL DEFAULT '0',
  `first_half_away` smallint(5) unsigned NOT NULL DEFAULT '0',
  `start` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `second_half_home` smallint(5) unsigned NOT NULL DEFAULT '0',
  `second_half_away` smallint(5) unsigned NOT NULL DEFAULT '0',
  `red_card_home` smallint(5) unsigned NOT NULL DEFAULT '0',
  `red_card_away` smallint(5) unsigned NOT NULL DEFAULT '0',
  `yellow_card_home` smallint(5) unsigned NOT NULL DEFAULT '0',
  `yellow_card_away` smallint(5) unsigned NOT NULL DEFAULT '0',
  `note` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`event_id`),
  CONSTRAINT `FK_live_soccer_1` FOREIGN KEY (`event_id`) REFERENCES `live_event` (`event_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci COMMENT='InnoDB free: 105472 kB; (`event_id`) REFER `game/live_event`';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `live_1003`
--

DROP TABLE IF EXISTS `live_1003`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `live_1003` (
  `event_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `start` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `score_home` varchar(2) CHARACTER SET utf8 NOT NULL DEFAULT '0' COMMENT 'sety',
  `score_away` varchar(2) CHARACTER SET utf8 NOT NULL DEFAULT '0' COMMENT 'sety',
  `set_1_home` smallint(5) unsigned NOT NULL DEFAULT '0',
  `set_1_away` smallint(5) unsigned NOT NULL DEFAULT '0',
  `set_2_home` smallint(5) unsigned NOT NULL DEFAULT '0',
  `set_2_away` smallint(5) unsigned NOT NULL DEFAULT '0',
  `set_3_home` smallint(5) unsigned NOT NULL DEFAULT '0',
  `set_3_away` smallint(5) unsigned NOT NULL DEFAULT '0',
  `set_4_home` smallint(5) unsigned NOT NULL DEFAULT '0',
  `set_4_away` smallint(5) unsigned NOT NULL DEFAULT '0',
  `set_5_home` smallint(5) unsigned NOT NULL DEFAULT '0',
  `set_5_away` smallint(5) unsigned NOT NULL DEFAULT '0',
  `court` enum('clay','grass','hardcourt') CHARACTER SET utf8 NOT NULL DEFAULT 'clay',
  `set_num` smallint(5) unsigned NOT NULL DEFAULT '3',
  `first_service` varchar(20) COLLATE utf8_czech_ci NOT NULL DEFAULT '',
  `tiebreak` tinyint(1) NOT NULL,
  `note` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`event_id`),
  CONSTRAINT `FK_live_tenis_1` FOREIGN KEY (`event_id`) REFERENCES `live_event` (`event_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci COMMENT='InnoDB free: 105472 kB; (`event_id`) REFER `game/live_event`';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `live_1006`
--

DROP TABLE IF EXISTS `live_1006`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `live_1006` (
  `event_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `score_home` smallint(5) unsigned NOT NULL DEFAULT '0',
  `ctvrtina_1_home` smallint(5) unsigned NOT NULL DEFAULT '0',
  `ctvrtina_2_home` smallint(5) unsigned NOT NULL DEFAULT '0',
  `ctvrtina_3_home` smallint(5) unsigned NOT NULL DEFAULT '0',
  `ctvrtina_4_home` smallint(5) unsigned NOT NULL DEFAULT '0',
  `score_away` smallint(5) unsigned NOT NULL DEFAULT '0',
  `ctvrtina_1_away` smallint(5) unsigned NOT NULL DEFAULT '0',
  `ctvrtina_2_away` smallint(5) unsigned NOT NULL DEFAULT '0',
  `ctvrtina_3_away` smallint(5) unsigned NOT NULL DEFAULT '0',
  `ctvrtina_4_away` smallint(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`event_id`),
  CONSTRAINT `FK_live_basket_1` FOREIGN KEY (`event_id`) REFERENCES `live_event` (`event_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci COMMENT='InnoDB free: 105472 kB; (`event_id`) REFER `game/live_event`';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `live_1011`
--

DROP TABLE IF EXISTS `live_1011`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `live_1011` (
  `event_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `score_home` smallint(5) unsigned NOT NULL DEFAULT '0',
  `score_away` smallint(5) unsigned NOT NULL DEFAULT '0',
  `tretina_1_home` smallint(5) unsigned NOT NULL DEFAULT '0',
  `tretina_1_away` smallint(5) unsigned NOT NULL DEFAULT '0',
  `tretina_2_home` smallint(5) unsigned NOT NULL DEFAULT '0',
  `tretina_2_away` smallint(5) unsigned NOT NULL DEFAULT '0',
  `tretina_3_home` smallint(5) unsigned NOT NULL DEFAULT '0',
  `tretina_3_away` smallint(5) unsigned NOT NULL DEFAULT '0',
  `minuta` smallint(5) unsigned NOT NULL,
  `note` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`event_id`),
  CONSTRAINT `FK_live_hockey_1` FOREIGN KEY (`event_id`) REFERENCES `live_event` (`event_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci COMMENT='InnoDB free: 105472 kB; (`event_id`) REFER `game/live_event`';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `live_1013`
--

DROP TABLE IF EXISTS `live_1013`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `live_1013` (
  `event_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `start` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `score_home` varchar(2) CHARACTER SET utf8 NOT NULL DEFAULT '0' COMMENT 'sety',
  `score_away` varchar(2) CHARACTER SET utf8 NOT NULL DEFAULT '0' COMMENT 'sety',
  `set_1_home` smallint(5) unsigned NOT NULL DEFAULT '0',
  `set_1_away` smallint(5) unsigned NOT NULL DEFAULT '0',
  `set_2_home` smallint(5) unsigned NOT NULL DEFAULT '0',
  `set_2_away` smallint(5) unsigned NOT NULL DEFAULT '0',
  `set_3_home` smallint(5) unsigned NOT NULL DEFAULT '0',
  `set_3_away` smallint(5) unsigned NOT NULL DEFAULT '0',
  `set_4_home` smallint(5) unsigned NOT NULL DEFAULT '0',
  `set_4_away` smallint(5) unsigned NOT NULL DEFAULT '0',
  `set_5_home` smallint(5) unsigned NOT NULL DEFAULT '0',
  `set_5_away` smallint(5) unsigned NOT NULL DEFAULT '0',
  `service` enum('home','away') COLLATE utf8_bin NOT NULL DEFAULT 'home',
  PRIMARY KEY (`event_id`),
  CONSTRAINT `live_1013_fk` FOREIGN KEY (`event_id`) REFERENCES `live_event` (`event_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='InnoDB free: 105472 kB; (`event_id`) REFER `game/live_event`';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `live_1020`
--

DROP TABLE IF EXISTS `live_1020`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `live_1020` (
  `event_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `score_home` smallint(5) unsigned NOT NULL DEFAULT '0',
  `score_away` smallint(5) unsigned NOT NULL DEFAULT '0',
  `first_half_home` smallint(5) unsigned NOT NULL DEFAULT '0',
  `first_half_away` smallint(5) unsigned NOT NULL DEFAULT '0',
  `start` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `second_half_home` smallint(5) unsigned NOT NULL DEFAULT '0',
  `second_half_away` smallint(5) unsigned NOT NULL DEFAULT '0',
  `red_card_home` smallint(5) unsigned NOT NULL DEFAULT '0',
  `red_card_away` smallint(5) unsigned NOT NULL DEFAULT '0',
  `yellow_card_home` smallint(5) unsigned NOT NULL DEFAULT '0',
  `yellow_card_away` smallint(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`event_id`),
  CONSTRAINT `FK_live_habdball_1` FOREIGN KEY (`event_id`) REFERENCES `live_event` (`event_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci COMMENT='InnoDB free: 105472 kB; (`event_id`) REFER `game/live_event`';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `live_1029`
--

DROP TABLE IF EXISTS `live_1029`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `live_1029` (
  `event_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `start` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `lap` smallint(5) unsigned NOT NULL DEFAULT '0',
  `lap_total` smallint(6) DEFAULT '30',
  PRIMARY KEY (`event_id`),
  CONSTRAINT `FK_live_f1_1` FOREIGN KEY (`event_id`) REFERENCES `live_event` (`event_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci COMMENT='InnoDB free: 105472 kB; (`event_id`) REFER `game/live_event`';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `live_betradar_event`
--

DROP TABLE IF EXISTS `live_betradar_event`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `live_betradar_event` (
  `event_id` int(10) unsigned NOT NULL,
  `betradar_event_id` int(11) NOT NULL,
  `last_act` int(16) DEFAULT NULL COMMENT 'cas posledni aktualizace',
  `msgnr` int(11) DEFAULT '0',
  `no_update` tinyint(4) DEFAULT '0' COMMENT '0 = aktualizuj z betradaru\r\n1 = neaktualizuj z betradaru',
  UNIQUE KEY `betradar_event_id` (`betradar_event_id`),
  KEY `event_id` (`event_id`),
  CONSTRAINT `live_betradar_event_fk` FOREIGN KEY (`event_id`) REFERENCES `live_event` (`event_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='InnoDB free: 105472 kB; (`event_id`) REFER `game/live_event`';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `live_betradar_log`
--

DROP TABLE IF EXISTS `live_betradar_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `live_betradar_log` (
  `log_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `date` datetime DEFAULT NULL,
  `text` text CHARACTER SET utf8,
  PRIMARY KEY (`log_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `live_betradar_score_card`
--

DROP TABLE IF EXISTS `live_betradar_score_card`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `live_betradar_score_card` (
  `betradar_event_id` int(11) NOT NULL,
  `sc_id` int(11) NOT NULL,
  `sc_type` smallint(6) NOT NULL DEFAULT '1' COMMENT '1=score 2=card',
  PRIMARY KEY (`betradar_event_id`,`sc_id`,`sc_type`),
  KEY `betradar_event_id` (`betradar_event_id`),
  CONSTRAINT `live_betradar_score_card_fk` FOREIGN KEY (`betradar_event_id`) REFERENCES `live_betradar_event` (`betradar_event_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='InnoDB free: 105472 kB; (`betradar_event_id`) REFER `game/li';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `live_betradar_setting`
--

DROP TABLE IF EXISTS `live_betradar_setting`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `live_betradar_setting` (
  `setting_id` int(11) NOT NULL DEFAULT '1',
  `last_alive_time` int(16) DEFAULT NULL COMMENT 'posledni datum alive zpravy',
  UNIQUE KEY `setting_id` (`setting_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='InnoDB free: 105472 kB';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `live_chat`
--

DROP TABLE IF EXISTS `live_chat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `live_chat` (
  `chrow_id` int(13) NOT NULL AUTO_INCREMENT,
  `live_event_id` int(8) NOT NULL,
  `user_id` int(8) NOT NULL,
  `nick` varchar(40) NOT NULL DEFAULT '',
  `lang_id` int(5) NOT NULL,
  `ts` datetime NOT NULL,
  `msg` varchar(400) NOT NULL DEFAULT '',
  PRIMARY KEY (`chrow_id`),
  KEY `live_event_id` (`live_event_id`,`lang_id`,`ts`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='InnoDB free: 105472 kB';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `live_event`
--

DROP TABLE IF EXISTS `live_event`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `live_event` (
  `event_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `start_date` datetime NOT NULL,
  `stav` smallint(5) unsigned NOT NULL DEFAULT '0',
  `l_sport_id` int(10) unsigned NOT NULL,
  `l_udalost_id` int(10) unsigned NOT NULL,
  `vypnuto` smallint(5) unsigned NOT NULL DEFAULT '0',
  `home_team` varchar(200) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `away_team` varchar(200) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `live_bookmaker_id` smallint(5) unsigned DEFAULT NULL,
  `minute` smallint(5) unsigned NOT NULL DEFAULT '0',
  `win` decimal(10,2) NOT NULL DEFAULT '30000.00' COMMENT 'max. vyhra v EUR na jeden tiket',
  `aktualizace` datetime NOT NULL,
  `limit_bet` int(11) DEFAULT '20',
  `limit_rate` decimal(5,2) DEFAULT '1.50',
  PRIMARY KEY (`event_id`),
  KEY `FK_live_event_1` (`l_sport_id`),
  KEY `FK_live_event_2` (`l_udalost_id`),
  CONSTRAINT `FK_live_event_1` FOREIGN KEY (`l_sport_id`) REFERENCES `sport` (`sport_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_live_event_2` FOREIGN KEY (`l_udalost_id`) REFERENCES `udalost` (`udalost_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci COMMENT='InnoDB free: 105472 kB; (`l_sport_id`) REFER `game/sport`(`s';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `live_info`
--

DROP TABLE IF EXISTS `live_info`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `live_info` (
  `event_id` int(10) unsigned NOT NULL,
  `time` smallint(5) unsigned NOT NULL,
  `text` varchar(45) COLLATE utf8_czech_ci NOT NULL DEFAULT '' COMMENT 'text z prekladu ciselnik',
  `book_text` varchar(100) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT 'text, ktery pisou bookmakeri',
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`),
  KEY `FK_live_info_1` (`event_id`),
  CONSTRAINT `FK_live_info_1` FOREIGN KEY (`event_id`) REFERENCES `live_event` (`event_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci COMMENT='InnoDB free: 105472 kB; (`event_id`) REFER `game/live_event`';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `live_kombinace`
--

DROP TABLE IF EXISTS `live_kombinace`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `live_kombinace` (
  `event_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sazka_id` int(10) unsigned NOT NULL,
  KEY `FK_live_kombinace_1` (`event_id`),
  KEY `FK_live_kombinace_2` (`sazka_id`),
  CONSTRAINT `FK_live_kombinace_1` FOREIGN KEY (`event_id`) REFERENCES `live_event` (`event_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_live_kombinace_2` FOREIGN KEY (`sazka_id`) REFERENCES `sazky` (`sazka_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci COMMENT='InnoDB free: 105472 kB; (`event_id`) REFER `game/live_event`';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `live_notice`
--

DROP TABLE IF EXISTS `live_notice`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `live_notice` (
  `event_id` int(10) NOT NULL,
  `user_id` int(10) NOT NULL,
  PRIMARY KEY (`event_id`,`user_id`),
  KEY `event_id` (`event_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='InnoDB free: 105472 kB';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `live_sazka`
--

DROP TABLE IF EXISTS `live_sazka`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `live_sazka` (
  `event_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sazka_id` int(10) unsigned NOT NULL,
  `no_comb` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT 'pokud 1 tak nelze kombinovat s ostatnimi live v ramci jedne event_id',
  `aktualizace` datetime NOT NULL,
  `aktualizace_sazka` datetime NOT NULL COMMENT 'cas kdy byla sazka zalozena nebo zavrena nebo znovu otervrena',
  `close` smallint(5) unsigned NOT NULL DEFAULT '0',
  `betradar_sazka_id` int(11) DEFAULT '0' COMMENT 'pokud je nula je to nase vypsana sazka pokud je tam id je to sazka ktera prisla z betradaru',
  `no_update` smallint(6) DEFAULT '0' COMMENT 'zda se ma sazka aktualizovat z betradaru',
  PRIMARY KEY (`event_id`,`sazka_id`),
  KEY `FK_live_sazka_2` (`sazka_id`),
  KEY `event_id` (`event_id`),
  CONSTRAINT `FK_live_sazka_1` FOREIGN KEY (`event_id`) REFERENCES `live_event` (`event_id`) ON UPDATE CASCADE,
  CONSTRAINT `FK_live_sazka_2` FOREIGN KEY (`sazka_id`) REFERENCES `sazky` (`sazka_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci COMMENT='InnoDB free: 105472 kB; (`event_id`) REFER `game/live_event`';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `live_sazka_before_ins_tr` BEFORE INSERT ON `live_sazka` FOR EACH ROW BEGIN
  DECLARE a INT DEFAULT 0;
  DECLARE done INT DEFAULT 0;
  DECLARE cur1 CURSOR FOR select betradar_sazka_id from live_sazka where betradar_sazka_id=NEW.betradar_sazka_id;
  DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;
  OPEN cur1;
  REPEAT
    FETCH cur1 INTO a;
    IF NOT done THEN
      if a<>0 then set NEW.sazka_id=NULL; END IF;
    END IF;
  UNTIL done END REPEAT;
  CLOSE cur1;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `live_template`
--

DROP TABLE IF EXISTS `live_template`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `live_template` (
  `event_id` int(10) unsigned NOT NULL,
  `typ_id` int(10) unsigned NOT NULL,
  `podtyp_id` int(10) unsigned NOT NULL,
  `disable` smallint(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`event_id`,`typ_id`,`podtyp_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci COMMENT='InnoDB free: 105472 kB';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `logy`
--

DROP TABLE IF EXISTS `logy`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `logy` (
  `log_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `datum` datetime DEFAULT '0000-00-00 00:00:00',
  `akce` varchar(240) NOT NULL DEFAULT '',
  `code` varchar(40) NOT NULL DEFAULT '0',
  `text` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `status` smallint(6) NOT NULL DEFAULT '0',
  `admin_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`log_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='InnoDB free: 105472 kB';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mena`
--

DROP TABLE IF EXISTS `mena`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mena` (
  `mena_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `mena_T` int(10) unsigned NOT NULL,
  `mena_TA` int(10) unsigned NOT NULL,
  `mena_ADM` int(10) unsigned NOT NULL,
  `mena_allow` smallint(1) unsigned NOT NULL DEFAULT '1',
  `mena_text` varchar(20) NOT NULL DEFAULT '',
  `mena_isonum` char(3) NOT NULL DEFAULT '0',
  `mena_info` varchar(20) NOT NULL DEFAULT '',
  `zobrazeno` smallint(5) unsigned NOT NULL DEFAULT '0',
  `free_bet_max` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `happy_hour` int(10) unsigned NOT NULL,
  PRIMARY KEY (`mena_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COMMENT='InnoDB free: 105472 kB';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mena_kurz`
--

DROP TABLE IF EXISTS `mena_kurz`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mena_kurz` (
  `mena_kurz_id` int(11) NOT NULL AUTO_INCREMENT,
  `mena_id` smallint(5) NOT NULL,
  `kurz` double(15,4) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`mena_kurz_id`),
  KEY `mena_id` (`mena_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='InnoDB free: 128000 kB; InnoDB free: 105472 kB';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `nastaveni`
--

DROP TABLE IF EXISTS `nastaveni`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `nastaveni` (
  `id` smallint(6) NOT NULL DEFAULT '1',
  `doba_vklad_vyber` int(11) NOT NULL DEFAULT '0',
  `zakazane_ip` text,
  `max_vyhernost_zetony` int(11) NOT NULL DEFAULT '1000',
  `vypnute_stranky` smallint(6) NOT NULL DEFAULT '0',
  `t_pay` smallint(5) unsigned NOT NULL DEFAULT '0',
  `ticket_limit_low` int(10) unsigned NOT NULL DEFAULT '50',
  `ticket_limit_high` int(10) unsigned NOT NULL DEFAULT '100',
  `ticket_limit_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='InnoDB free: 105472 kB';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `novinky`
--

DROP TABLE IF EXISTS `novinky`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `novinky` (
  `novinka_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `platna_od` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `platna_do` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `zobrazeno` smallint(5) unsigned NOT NULL DEFAULT '0',
  `homepage` smallint(5) unsigned NOT NULL DEFAULT '0',
  `sazky` smallint(5) unsigned NOT NULL DEFAULT '0',
  `hp_image` varchar(45) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `promo_image` varchar(45) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `udalost_id` int(10) unsigned NOT NULL,
  `page_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'id stranky na kterou se odkazuje novinka - ma prednost pred udalost_id',
  PRIMARY KEY (`novinka_id`),
  KEY `Index_2` (`platna_od`,`platna_do`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1 COMMENT='InnoDB free: 105472 kB';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `novinky_jazyk`
--

DROP TABLE IF EXISTS `novinky_jazyk`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `novinky_jazyk` (
  `novinka_id` int(10) unsigned NOT NULL,
  `lang_id` int(10) unsigned NOT NULL DEFAULT '0',
  `zobrazeno_jazyk` smallint(5) unsigned NOT NULL DEFAULT '1',
  `anotace` varchar(500) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `data` text CHARACTER SET utf8 NOT NULL,
  `nadpis` varchar(200) CHARACTER SET utf8 DEFAULT NULL,
  `navstevnost` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`novinka_id`,`lang_id`),
  KEY `FK_novinky_jazyk_2` (`lang_id`),
  KEY `Index_3` (`zobrazeno_jazyk`),
  CONSTRAINT `FK_novinky_jazyk_1` FOREIGN KEY (`novinka_id`) REFERENCES `novinky` (`novinka_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_novinky_jazyk_2` FOREIGN KEY (`lang_id`) REFERENCES `jazyky` (`lang_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='InnoDB free: 105472 kB; (`novinka_id`) REFER `game/novinky`(';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `oblast`
--

DROP TABLE IF EXISTS `oblast`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `oblast` (
  `oblast_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nazev` varchar(20) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `betradar_oblast_id` int(10) unsigned DEFAULT NULL,
  `img` varchar(40) COLLATE utf8_unicode_ci DEFAULT '',
  `pozice` smallint(5) unsigned NOT NULL,
  `iso` varchar(3) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`oblast_id`)
) ENGINE=InnoDB AUTO_INCREMENT=577 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='InnoDB free: 105472 kB';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `offergen_tiskopis`
--

DROP TABLE IF EXISTS `offergen_tiskopis`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `offergen_tiskopis` (
  `id_tiskopis` int(11) NOT NULL AUTO_INCREMENT,
  `id_typ_tiskopisu` int(11) NOT NULL,
  `parametry` varchar(100) DEFAULT NULL,
  `pozadavek` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `pripraven` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `pocet_stran` int(11) DEFAULT NULL,
  `jazyk` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_tiskopis`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `offergen_typ_tiskopisu`
--

DROP TABLE IF EXISTS `offergen_typ_tiskopisu`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `offergen_typ_tiskopisu` (
  `id_typ_tiskopisu` int(11) NOT NULL AUTO_INCREMENT,
  `typ_tiskopisu_nazev` varchar(30) NOT NULL DEFAULT '',
  `obnova1` int(11) DEFAULT NULL,
  `obnova2` int(11) DEFAULT NULL,
  `mazat` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_typ_tiskopisu`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `platebni_metody`
--

DROP TABLE IF EXISTS `platebni_metody`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `platebni_metody` (
  `metoda_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `nazev` varchar(80) NOT NULL DEFAULT '',
  `dostupnost_penez` varchar(20) NOT NULL DEFAULT '',
  `vyber` smallint(5) unsigned NOT NULL DEFAULT '0',
  `vklad` smallint(5) unsigned NOT NULL DEFAULT '0',
  `zobrazeno` smallint(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`metoda_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='InnoDB free: 105472 kB';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pobocka`
--

DROP TABLE IF EXISTS `pobocka`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pobocka` (
  `pobocka_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `jmeno` varchar(400) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `ulice` varchar(100) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `PSC` smallint(6) unsigned NOT NULL,
  `mesto` varchar(40) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `umisteni` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `platnost_od` date NOT NULL,
  `email` varchar(600) CHARACTER SET utf8 DEFAULT NULL,
  `telefon_predvolba` smallint(6) unsigned NOT NULL,
  `telefon` int(11) unsigned NOT NULL,
  `bank_predcisly` int(11) unsigned DEFAULT NULL,
  `bank_cislouctu` int(11) unsigned DEFAULT NULL,
  `bank_kod_banky` smallint(6) unsigned DEFAULT NULL,
  `bank_nazev_banky` varchar(400) CHARACTER SET utf8 DEFAULT NULL,
  `bank_nazev_pobocky` varchar(400) CHARACTER SET utf8 DEFAULT NULL,
  `bank_cislo_pobocky` smallint(6) unsigned DEFAULT NULL,
  `bank_mena` varchar(5) CHARACTER SET utf8 DEFAULT NULL,
  `bank_nazev_meny` varchar(20) CHARACTER SET utf8 DEFAULT NULL,
  `provize_predcisly` int(11) DEFAULT NULL,
  `provize_cislouctu` int(11) DEFAULT NULL,
  `provize_kod_banky` smallint(5) DEFAULT NULL,
  `provize_nazev_banky` varchar(400) CHARACTER SET utf8 DEFAULT NULL,
  `provize_nazev_pobocky` varchar(400) CHARACTER SET utf8 DEFAULT NULL,
  `provize_cislo_pobocky` smallint(6) DEFAULT NULL,
  `provize_mena` varchar(5) CHARACTER SET utf8 DEFAULT NULL,
  `provize_nazev_meny` varchar(20) CHARACTER SET utf8 DEFAULT NULL,
  `provize_kontaktni_osoba` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `smlouva_datum` date NOT NULL,
  `smlouva_provoz_po` tinyint(4) DEFAULT '0',
  `smlouva_provoz_ut` tinyint(4) DEFAULT '0',
  `smlouva_provoz_st` tinyint(4) DEFAULT '0',
  `smlouva_provoz_ct` tinyint(4) DEFAULT '0',
  `smlouva_provoz_pa` tinyint(4) DEFAULT '0',
  `smlouva_provoz_so` tinyint(4) DEFAULT '0',
  `smlouva_provoz_ne` tinyint(4) DEFAULT '0',
  `smlouva_prestavka_od` varchar(8) CHARACTER SET utf8 DEFAULT NULL,
  `smlouva_prestavka_do` varchar(8) CHARACTER SET utf8 DEFAULT NULL,
  `smlouva_datum_ucinnosti` date NOT NULL,
  `smlouva_datum_podpisu` date NOT NULL,
  `smlouva_datum_ukonceni` date DEFAULT NULL,
  `smlouva_ulice` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `smlouva_mesto` varchar(40) CHARACTER SET utf8 DEFAULT NULL,
  `smlouva_manipulacni_poplatek` smallint(3) unsigned NOT NULL DEFAULT '0',
  `smlouva_druh_smlouvy` varchar(20) CHARACTER SET utf8 DEFAULT NULL,
  `smlouva_vernostni_program` tinyint(4) unsigned NOT NULL DEFAULT '0',
  `smlouva_vyuctovani` varchar(20) CHARACTER SET utf8 DEFAULT NULL,
  `smlouva_zalohy` varchar(20) CHARACTER SET utf8 DEFAULT NULL,
  `smlouva_reg_manager` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `smlouva_kraj` smallint(6) unsigned NOT NULL,
  `povoleny_naber` tinyint(4) DEFAULT '1' COMMENT '0=nemuze prijimat a vyplacet tikety\r\n1=muze prijimat a vyplacet',
  `pobocka_aktivni` tinyint(4) DEFAULT '1' COMMENT '0=neaktivni pobocka\r\n1=aktivni pobocka',
  `pobocka_muze_prihlasit` tinyint(4) DEFAULT '1' COMMENT '0= nemuze se prihlasit do systemu\r\n1= muze se prihlasit do systemu',
  PRIMARY KEY (`pobocka_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pobocka_uzivatel`
--

DROP TABLE IF EXISTS `pobocka_uzivatel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pobocka_uzivatel` (
  `pobocka_id` int(11) unsigned NOT NULL,
  `pobocka_user_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `jmeno` varchar(20) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `prijmeni` varchar(40) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `email` varchar(60) CHARACTER SET utf8 DEFAULT NULL,
  `nick` varchar(20) CHARACTER SET utf8 DEFAULT NULL,
  `heslo` varchar(40) CHARACTER SET utf8 DEFAULT NULL,
  `zakazany` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT '0=povoleny\r\n1=zakazany neprihlasi se',
  PRIMARY KEY (`pobocka_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `podtyp`
--

DROP TABLE IF EXISTS `podtyp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `podtyp` (
  `podtyp_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `radek_sloupec` smallint(5) unsigned NOT NULL DEFAULT '0',
  `sloupec_pocet_max` smallint(5) unsigned NOT NULL DEFAULT '1',
  `text` varchar(20) CHARACTER SET utf8 DEFAULT NULL,
  `interni_nazev` varchar(50) CHARACTER SET utf8 NOT NULL DEFAULT '',
  PRIMARY KEY (`podtyp_id`)
) ENGINE=InnoDB AUTO_INCREMENT=173 DEFAULT CHARSET=latin1 COMMENT='InnoDB free: 105472 kB';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `podtyp_sloupce`
--

DROP TABLE IF EXISTS `podtyp_sloupce`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `podtyp_sloupce` (
  `sloupec_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `podtyp_id` int(10) unsigned NOT NULL DEFAULT '0',
  `nazev` varchar(100) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `poradi` smallint(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`sloupec_id`),
  KEY `FK_podtyp_sloupce_1` (`podtyp_id`),
  CONSTRAINT `FK_podtyp_sloupce_1` FOREIGN KEY (`podtyp_id`) REFERENCES `podtyp` (`podtyp_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1881 DEFAULT CHARSET=latin1 COMMENT='InnoDB free: 105472 kB; (`podtyp_id`) REFER `game/podtyp`(`p';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `poznamky_template`
--

DROP TABLE IF EXISTS `poznamky_template`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `poznamky_template` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `predmet` varchar(500) CHARACTER SET utf8 DEFAULT NULL,
  `telo` text CHARACTER SET utf8,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1 COMMENT='InnoDB free: 105472 kB';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Temporary table structure for view `prava_bookmaker`
--

DROP TABLE IF EXISTS `prava_bookmaker`;
/*!50001 DROP VIEW IF EXISTS `prava_bookmaker`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `prava_bookmaker` (
  `bookmaker_id` int(11) unsigned,
  `jmeno` varchar(40),
  `prijmeni` varchar(60),
  `povoleny` smallint(5) unsigned,
  `udalost_id` int(10) unsigned,
  `sport_id` int(10) unsigned,
  `nazev` varchar(20)
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `prava_udalost`
--

DROP TABLE IF EXISTS `prava_udalost`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `prava_udalost` (
  `udalost_id` int(10) unsigned NOT NULL DEFAULT '0',
  `bookmaker_id` int(10) unsigned NOT NULL DEFAULT '0',
  `povoleny` smallint(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`udalost_id`,`bookmaker_id`),
  CONSTRAINT `FK_prava_udalost_1` FOREIGN KEY (`udalost_id`) REFERENCES `udalost` (`udalost_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='InnoDB free: 105472 kB; (`udalost_id`) REFER `game/udalost`(';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `preklady`
--

DROP TABLE IF EXISTS `preklady`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `preklady` (
  `lang_id` int(10) unsigned NOT NULL DEFAULT '0',
  `index_pole` varchar(20) NOT NULL DEFAULT '',
  `short_text` varchar(100) DEFAULT NULL,
  `text` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `translate` smallint(6) DEFAULT '0',
  `preklad_id` int(11) unsigned NOT NULL,
  UNIQUE KEY `index_pole_lang_id_unique` (`index_pole`,`lang_id`),
  KEY `FK_preklady_1` (`lang_id`),
  KEY `index_pole` (`index_pole`),
  CONSTRAINT `FK_preklady_1` FOREIGN KEY (`lang_id`) REFERENCES `jazyky` (`lang_id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='InnoDB free: 105472 kB; (`lang_id`) REFER `game/jazyky`(`lan';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `preklady_before_ins_tr` BEFORE INSERT ON `preklady`
 FOR EACH ROW BEGIN
declare i int;
if exists(select preklad_id from preklady where index_pole=NEW.index_pole limit 1) then


      set NEW.preklad_id = (select preklad_id from preklady where index_pole=NEW.index_pole limit 1);

else
       set i = (select max(x.preklad_id) from preklady x);

       if i is null then

              set NEW.preklad_id = 1;

       else

              set NEW.preklad_id =i+1;

       end if;

end if;


END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `t_preklady_after_insert` AFTER INSERT ON `preklady`
 FOR EACH ROW BEGIN
	REPLACE INTO preklady_search(preklad_id, lang_id, text)
	VALUES(NEW.preklad_id, NEW.lang_id, NEW.text);
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `t_preklady_after_update` AFTER UPDATE ON `preklady`
 FOR EACH ROW BEGIN
	UPDATE preklady_search SET text=NEW.text WHERE preklad_id=NEW.preklad_id AND lang_id=NEW.lang_id;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `t_preklady_after_delete` AFTER DELETE ON `preklady`
 FOR EACH ROW BEGIN
	DELETE FROM preklady_search WHERE preklad_id=OLD.preklad_id AND lang_id=OLD.lang_id;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `preklady_search`
--

DROP TABLE IF EXISTS `preklady_search`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `preklady_search` (
  `preklad_id` int(11) NOT NULL,
  `lang_id` int(11) NOT NULL,
  `text` text NOT NULL,
  PRIMARY KEY (`preklad_id`,`lang_id`),
  FULLTEXT KEY `fulltext` (`text`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `prng_9`
--

DROP TABLE IF EXISTS `prng_9`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `prng_9` (
  `seed` int(3) unsigned NOT NULL AUTO_INCREMENT,
  `prn_a` int(3) unsigned NOT NULL,
  `prn_b` int(3) unsigned NOT NULL,
  `prn_c` int(3) unsigned NOT NULL,
  PRIMARY KEY (`seed`),
  UNIQUE KEY `prn_a` (`prn_a`),
  UNIQUE KEY `prn_b` (`prn_b`),
  UNIQUE KEY `prn_c` (`prn_c`)
) ENGINE=MyISAM AUTO_INCREMENT=1000 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `promo`
--

DROP TABLE IF EXISTS `promo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `promo` (
  `promo_id` smallint(5) NOT NULL,
  `img_name` varchar(100) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `url` varchar(200) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `sazka_id` int(10) unsigned NOT NULL DEFAULT '0',
  `menu_id` int(10) unsigned NOT NULL DEFAULT '0',
  `target` varchar(45) COLLATE utf8_bin NOT NULL DEFAULT '_self',
  `lang_id` smallint(5) unsigned NOT NULL,
  `text` varchar(200) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `promo_sec` smallint(5) unsigned NOT NULL DEFAULT '30',
  `icon` varchar(100) COLLATE utf8_bin NOT NULL DEFAULT '',
  `platne_do` datetime DEFAULT NULL,
  `platne_od` datetime DEFAULT NULL,
  `button_text` varchar(200) COLLATE utf8_bin DEFAULT '0',
  `title` varchar(200) CHARACTER SET utf8 DEFAULT NULL,
  PRIMARY KEY (`promo_id`,`lang_id`),
  KEY `FK_promo_hp_1` (`menu_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='InnoDB free: 105472 kB';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `public_inquiry`
--

DROP TABLE IF EXISTS `public_inquiry`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `public_inquiry` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `label` varchar(255) NOT NULL DEFAULT '',
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_active` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='InnoDB free: 105472 kB';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `public_inquiry_option`
--

DROP TABLE IF EXISTS `public_inquiry_option`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `public_inquiry_option` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `question_id` int(10) unsigned NOT NULL,
  `label` varchar(20) NOT NULL DEFAULT '' COMMENT 'message in translation',
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `question_id` (`question_id`),
  CONSTRAINT `public_inquiry_option_ibfk_1` FOREIGN KEY (`question_id`) REFERENCES `public_inquiry_question` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='InnoDB free: 105472 kB; (`question_id`) REFER `game/public_i';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `public_inquiry_question`
--

DROP TABLE IF EXISTS `public_inquiry_question`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `public_inquiry_question` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `inquiry_id` int(11) unsigned NOT NULL,
  `label` varchar(20) NOT NULL DEFAULT '' COMMENT 'foreign to the translation',
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_active` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `inquiry_id` (`inquiry_id`),
  CONSTRAINT `public_inquiry_question_ibfk_1` FOREIGN KEY (`inquiry_id`) REFERENCES `public_inquiry` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='InnoDB free: 105472 kB; (`inquiry_id`) REFER `game/public_in';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `public_inquiry_vote`
--

DROP TABLE IF EXISTS `public_inquiry_vote`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `public_inquiry_vote` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `question_id` int(10) unsigned NOT NULL,
  `option_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned DEFAULT NULL,
  `age` float DEFAULT NULL,
  `gender` enum('m','f') COLLATE utf8_bin DEFAULT NULL,
  `country_id` smallint(5) unsigned DEFAULT NULL,
  `session_id` varchar(33) COLLATE utf8_bin NOT NULL DEFAULT '',
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `question_id` (`question_id`),
  KEY `user_id` (`user_id`),
  KEY `contry_id` (`country_id`),
  KEY `option_id` (`option_id`),
  CONSTRAINT `public_inquiry_vote_ibfk_1` FOREIGN KEY (`question_id`) REFERENCES `public_inquiry_question` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `public_inquiry_vote_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `uzivatel` (`user_id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `public_inquiry_vote_ibfk_5` FOREIGN KEY (`option_id`) REFERENCES `public_inquiry_option` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `public_inquiry_vote_ibfk_6` FOREIGN KEY (`country_id`) REFERENCES `zeme` (`zeme_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='InnoDB free: 105472 kB; (`question_id`) REFER `game/public_i';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `referer`
--

DROP TABLE IF EXISTS `referer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `referer` (
  `user_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `referer_url` varchar(150) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `date` datetime NOT NULL,
  PRIMARY KEY (`user_id`,`referer_url`,`date`),
  CONSTRAINT `FK_referrer_1` FOREIGN KEY (`user_id`) REFERENCES `uzivatel` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='InnoDB free: 105472 kB; (`user_id`) REFER `game/uzivatel`(`u';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `reklama_page`
--

DROP TABLE IF EXISTS `reklama_page`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reklama_page` (
  `page_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(80) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `url` varchar(250) CHARACTER SET utf8 NOT NULL DEFAULT '',
  PRIMARY KEY (`page_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci COMMENT='InnoDB free: 237568 kB';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `reklama_page_bet`
--

DROP TABLE IF EXISTS `reklama_page_bet`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reklama_page_bet` (
  `page_id` smallint(5) unsigned NOT NULL,
  `sazka_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`page_id`,`sazka_id`),
  KEY `FK_reklama_page_bet_2` (`sazka_id`),
  CONSTRAINT `FK_reklama_page_bet_1` FOREIGN KEY (`page_id`) REFERENCES `reklama_page` (`page_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_reklama_page_bet_2` FOREIGN KEY (`sazka_id`) REFERENCES `sazky` (`sazka_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sazka_chat`
--

DROP TABLE IF EXISTS `sazka_chat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sazka_chat` (
  `sazka_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `text` varchar(800) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `type` smallint(5) unsigned NOT NULL DEFAULT '0',
  `book_id` int(10) unsigned NOT NULL,
  `datum` datetime NOT NULL,
  KEY `FK_sazka_chat_1` (`sazka_id`),
  CONSTRAINT `FK_sazka_chat_1` FOREIGN KEY (`sazka_id`) REFERENCES `sazky` (`sazka_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='InnoDB free: 105472 kB; (`sazka_id`) REFER `game/sazky`(`saz';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sazka_kombinace`
--

DROP TABLE IF EXISTS `sazka_kombinace`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sazka_kombinace` (
  `sazka1_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sazka2_id` int(10) unsigned NOT NULL DEFAULT '0',
  `kombinace_ticket` smallint(6) NOT NULL DEFAULT '0',
  `kombinace_show` smallint(5) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`sazka1_id`,`sazka2_id`),
  KEY `FK_sazka_kombinace_2` (`sazka2_id`),
  CONSTRAINT `FK_sazka_kombinace_1` FOREIGN KEY (`sazka1_id`) REFERENCES `sazky` (`sazka_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_sazka_kombinace_2` FOREIGN KEY (`sazka2_id`) REFERENCES `sazky` (`sazka_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=646511 DEFAULT CHARSET=latin1 COMMENT='InnoDB free: 105472 kB; (`sazka1_id`) REFER `game/sazky`(`sa';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sazka_kurz`
--

DROP TABLE IF EXISTS `sazka_kurz`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sazka_kurz` (
  `sazka_id` int(10) unsigned NOT NULL,
  `sloupec_id` int(10) unsigned NOT NULL DEFAULT '0',
  `poradi` int(10) unsigned NOT NULL DEFAULT '0',
  `kurz` decimal(10,2) NOT NULL DEFAULT '0.00',
  `platny_od` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `kurz_zmena` smallint(6) NOT NULL DEFAULT '0',
  PRIMARY KEY (`sazka_id`,`sloupec_id`,`poradi`),
  UNIQUE KEY `Index_3` (`sazka_id`,`sloupec_id`,`platny_od`),
  KEY `FK_sazka_kurz_2` (`sloupec_id`),
  CONSTRAINT `FK_sazka_kurz_1` FOREIGN KEY (`sazka_id`) REFERENCES `sazky` (`sazka_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_sazka_kurz_2` FOREIGN KEY (`sloupec_id`) REFERENCES `podtyp_sloupce` (`sloupec_id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='InnoDB free: 105472 kB; (`sazka_id`) REFER `game/sazky`(`saz';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Temporary table structure for view `sazka_pohled`
--

DROP TABLE IF EXISTS `sazka_pohled`;
/*!50001 DROP VIEW IF EXISTS `sazka_pohled`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `sazka_pohled` (
  `sazka_id` int(10) unsigned,
  `proplatil_bookmaker` int(11),
  `proplacena` smallint(5) unsigned,
  `info` varchar(800),
  `betradar_sazka_id` int(10) unsigned,
  `platna_od` datetime,
  `platna_do` datetime,
  `status` smallint(5) unsigned,
  `live` smallint(5) unsigned,
  `bookmaker_id` int(10) unsigned,
  `vysledek` varchar(250),
  `udalost_id` int(10) unsigned,
  `typ_id` int(10) unsigned,
  `podtyp_id` int(10) unsigned,
  `overena` smallint(5) unsigned,
  `text` varchar(255),
  `ako` tinyint(4) unsigned,
  `jednoducha` smallint(5) unsigned,
  `risk_limit` int(10) unsigned,
  `risk_limit_balance` decimal(10,2) unsigned,
  `poradi` int(10) unsigned,
  `kurz` decimal(10,2),
  `kurz_zmena` smallint(6),
  `platny_od` datetime,
  `nazev` varchar(100),
  `sloupec_id` int(10) unsigned,
  `poradi_sloupec` smallint(5) unsigned,
  `betradar_autoupdate` tinyint(1),
  `score` varchar(128)
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `sazky`
--

DROP TABLE IF EXISTS `sazky`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sazky` (
  `sazka_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `betradar_autoupdate` tinyint(1) NOT NULL DEFAULT '0',
  `platna_od` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `platna_do` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status` smallint(5) unsigned NOT NULL DEFAULT '0',
  `live` smallint(5) unsigned NOT NULL DEFAULT '0',
  `bookmaker_id` int(10) unsigned NOT NULL DEFAULT '0',
  `udalost_id` int(10) unsigned NOT NULL DEFAULT '0',
  `typ_id` int(10) unsigned NOT NULL DEFAULT '0',
  `podtyp_id` int(10) unsigned NOT NULL DEFAULT '0',
  `overena` smallint(5) unsigned NOT NULL DEFAULT '0',
  `text` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `jednoducha` smallint(5) unsigned NOT NULL DEFAULT '0',
  `vysledek` varchar(250) NOT NULL DEFAULT '',
  `proplacena` smallint(5) unsigned NOT NULL DEFAULT '0',
  `proplatil_bookmaker` int(11) DEFAULT '0',
  `betradar_sazka_id` int(10) unsigned DEFAULT NULL,
  `betradar_statistic_id` bigint(20) unsigned DEFAULT NULL,
  `risk_limit` int(10) unsigned NOT NULL DEFAULT '3000',
  `risk_limit_balance` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `info` varchar(800) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `message` varchar(400) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `ako` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT 'AKO sazka',
  `alias` int(11) DEFAULT NULL,
  `parent_id` int(10) unsigned DEFAULT NULL,
  `alias_released` tinyint(1) DEFAULT NULL,
  `score` varchar(128) CHARACTER SET utf8 DEFAULT NULL,
  PRIMARY KEY (`sazka_id`),
  KEY `FK_sazky_1` (`bookmaker_id`),
  KEY `FK_sazky_2` (`udalost_id`),
  KEY `FK_sazky_3` (`typ_id`),
  KEY `FK_sazky_4` (`podtyp_id`),
  KEY `FK_sazky_5` (`vysledek`),
  KEY `datumod` (`platna_od`),
  KEY `datumdo` (`platna_do`),
  KEY `betradar_sazka_id` (`betradar_sazka_id`),
  KEY `alias` (`alias`),
  KEY `parent_id` (`parent_id`),
  CONSTRAINT `FK_sazky_1` FOREIGN KEY (`bookmaker_id`) REFERENCES `bookmaker` (`bookmaker_id`) ON UPDATE CASCADE,
  CONSTRAINT `FK_sazky_2` FOREIGN KEY (`udalost_id`) REFERENCES `udalost` (`udalost_id`) ON UPDATE CASCADE,
  CONSTRAINT `FK_sazky_3` FOREIGN KEY (`typ_id`) REFERENCES `typ` (`typ_id`) ON UPDATE CASCADE,
  CONSTRAINT `FK_sazky_4` FOREIGN KEY (`podtyp_id`) REFERENCES `podtyp` (`podtyp_id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=646511 DEFAULT CHARSET=latin1 COMMENT='InnoDB free: 105472 kB; (`bookmaker_id`) REFER `game/bookmak';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER t_sazky_after_insert AFTER INSERT ON sazky
FOR EACH ROW BEGIN
	REPLACE INTO sazky_search(sazka_id, sazka_text)
	VALUES(NEW.sazka_id, NEW.`text`);
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER t_sazky_after_update AFTER UPDATE ON sazky
FOR EACH ROW BEGIN
	UPDATE sazky_search SET sazka_text=NEW.`text` WHERE sazka_id=NEW.sazka_id;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER t_sazky_after_delete AFTER DELETE ON sazky
FOR EACH ROW BEGIN
	DELETE FROM sazky_search WHERE sazka_id=OLD.sazka_id;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `sazky_result`
--

DROP TABLE IF EXISTS `sazky_result`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sazky_result` (
  `sazka_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ft` varchar(40) COLLATE utf8_bin DEFAULT NULL,
  `ot` varchar(40) COLLATE utf8_bin DEFAULT NULL,
  `ht` varchar(40) COLLATE utf8_bin DEFAULT NULL,
  `1t` varchar(40) COLLATE utf8_bin DEFAULT NULL,
  `2t` varchar(40) COLLATE utf8_bin DEFAULT NULL,
  `3t` varchar(40) COLLATE utf8_bin DEFAULT NULL,
  `ap` varchar(40) CHARACTER SET utf8 DEFAULT NULL,
  PRIMARY KEY (`sazka_id`),
  CONSTRAINT `FK_sazky_result_1` FOREIGN KEY (`sazka_id`) REFERENCES `sazky` (`sazka_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='InnoDB free: 105472 kB; (`sazka_id`) REFER `game/sazky`(`saz';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sazky_search`
--

DROP TABLE IF EXISTS `sazky_search`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sazky_search` (
  `sazka_id` int(10) NOT NULL,
  `sazka_text` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`sazka_id`),
  FULLTEXT KEY `sazka_text` (`sazka_text`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `seo_url`
--

DROP TABLE IF EXISTS `seo_url`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `seo_url` (
  `lang_id` int(10) unsigned NOT NULL DEFAULT '0',
  `type` smallint(5) unsigned NOT NULL COMMENT '1:sport;2:oblast;3:udalost;4:druh;5:hry;6:tymy',
  `url` varchar(200) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `event_id` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`lang_id`,`type`,`event_id`),
  KEY `Index_1` (`event_id`),
  KEY `Index_2` (`lang_id`),
  KEY `Index_3` (`url`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci COMMENT='InnoDB free: 105472 kB';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sport`
--

DROP TABLE IF EXISTS `sport`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sport` (
  `sport_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nazev` varchar(20) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `pozice` smallint(6) NOT NULL,
  `zvyrazneni` smallint(6) NOT NULL DEFAULT '0',
  `zobrazeno` smallint(6) NOT NULL DEFAULT '0',
  `betradar_sport_id` int(10) unsigned DEFAULT NULL,
  `live` smallint(5) unsigned NOT NULL DEFAULT '0',
  `approval_group_id` int(10) unsigned DEFAULT NULL,
  `bet_alias_from` int(10) unsigned DEFAULT NULL,
  `bet_alias_to` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`sport_id`),
  KEY `approval_group_id` (`approval_group_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1044 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='InnoDB free: 105472 kB';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `static_page`
--

DROP TABLE IF EXISTS `static_page`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `static_page` (
  `page_id` int(11) NOT NULL AUTO_INCREMENT,
  `page_name` varchar(64) NOT NULL,
  `lang_id` int(11) NOT NULL,
  `template` varchar(256) NOT NULL,
  `visible` tinyint(1) NOT NULL,
  `page_title` text,
  `page_description` text,
  `page_keywords` text,
  `page_content` text,
  PRIMARY KEY (`page_id`),
  UNIQUE KEY `page_name_lang_id` (`page_name`,`lang_id`),
  KEY `page_name` (`page_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `system_user_bets`
--

DROP TABLE IF EXISTS `system_user_bets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `system_user_bets` (
  `user_id` int(10) unsigned NOT NULL,
  `datum` datetime NOT NULL,
  `bet` varchar(800) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `num` smallint(5) unsigned NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AVG_ROW_LENGTH=29 COMMENT='InnoDB free: 105472 kB';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `t_cfg`
--

DROP TABLE IF EXISTS `t_cfg`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t_cfg` (
  `nl_id` int(11) NOT NULL AUTO_INCREMENT,
  `dt_in` datetime NOT NULL,
  `s_nazev` varchar(45) COLLATE utf8_bin NOT NULL DEFAULT '',
  `s_hodnota` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `s_popis` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `datovy_typ` varchar(10) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`nl_id`),
  UNIQUE KEY `s_nazev` (`s_nazev`),
  UNIQUE KEY `s_nazev_2` (`s_nazev`)
) ENGINE=InnoDB AUTO_INCREMENT=79 DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='InnoDB free: 98304 kB; InnoDB free: 105472 kB';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `team`
--

DROP TABLE IF EXISTS `team`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `team` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `betradar_id` int(10) unsigned DEFAULT NULL,
  `sport_id` int(10) unsigned NOT NULL,
  `name` varchar(128) NOT NULL,
  `short_name` varchar(64) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `betradar_id` (`betradar_id`),
  KEY `sport_id` (`sport_id`),
  CONSTRAINT `team_ibfk_1` FOREIGN KEY (`sport_id`) REFERENCES `sport` (`sport_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `team_has_bet_handle_range`
--

DROP TABLE IF EXISTS `team_has_bet_handle_range`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `team_has_bet_handle_range` (
  `team_id` int(10) unsigned NOT NULL,
  `bet_handle_range_id` int(10) unsigned NOT NULL,
  `number` int(11) NOT NULL,
  PRIMARY KEY (`team_id`,`bet_handle_range_id`),
  KEY `bet_handle_range_id` (`bet_handle_range_id`),
  CONSTRAINT `team_has_bet_handle_range_ibfk_1` FOREIGN KEY (`team_id`) REFERENCES `team` (`id`),
  CONSTRAINT `team_has_bet_handle_range_ibfk_2` FOREIGN KEY (`bet_handle_range_id`) REFERENCES `bet_handle_range` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ticket`
--

DROP TABLE IF EXISTS `ticket`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ticket` (
  `ticket_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `handle` char(9) CHARACTER SET ascii COLLATE ascii_bin DEFAULT NULL,
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `castka` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `zalozen` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `vyplacen` smallint(5) unsigned NOT NULL DEFAULT '0',
  `zruseno` smallint(5) unsigned NOT NULL DEFAULT '0',
  `zrusil_bookmaker_id` int(10) unsigned DEFAULT NULL,
  `duvod_zruseni` varchar(255) DEFAULT NULL,
  `free_bet_bonus` smallint(5) unsigned NOT NULL DEFAULT '0',
  `system` smallint(5) unsigned NOT NULL DEFAULT '0',
  `type` varchar(45) DEFAULT NULL,
  `win` decimal(15,2) unsigned DEFAULT NULL,
  `rate` decimal(15,2) unsigned DEFAULT NULL,
  `rate_real` decimal(15,2) unsigned DEFAULT NULL,
  `win_real` decimal(15,2) unsigned DEFAULT NULL,
  `stats` smallint(5) unsigned NOT NULL DEFAULT '0',
  `stats_user` smallint(5) unsigned NOT NULL DEFAULT '0',
  `cupon_id` varchar(40) NOT NULL DEFAULT '0',
  `vyplacen_date` datetime NOT NULL,
  `vyplacen_bookmaker_id` smallint(5) unsigned NOT NULL DEFAULT '0',
  `mail` smallint(6) DEFAULT '1',
  `group_count` int(10) unsigned DEFAULT NULL,
  `group_t` tinyint(1) DEFAULT '0',
  `tickethash` varchar(32) CHARACTER SET ascii NOT NULL,
  `branch_id` int(10) unsigned NOT NULL,
  `cash` tinyint(1) NOT NULL,
  `collection_time` datetime DEFAULT NULL,
  `collection_branch_id` int(10) unsigned DEFAULT NULL,
  `cancel_time` datetime DEFAULT NULL,
  PRIMARY KEY (`ticket_id`),
  UNIQUE KEY `handle` (`handle`),
  KEY `FK_ticket_1` (`user_id`),
  KEY `FK_ticket_2` (`zrusil_bookmaker_id`),
  KEY `cupon` (`cupon_id`),
  KEY `tickethash` (`tickethash`),
  KEY `branch_id` (`branch_id`),
  KEY `collection_branch_id` (`collection_branch_id`),
  CONSTRAINT `FK_ticket_1` FOREIGN KEY (`user_id`) REFERENCES `uzivatel` (`user_id`) ON UPDATE CASCADE,
  CONSTRAINT `FK_ticket_2` FOREIGN KEY (`zrusil_bookmaker_id`) REFERENCES `bookmaker` (`bookmaker_id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='InnoDB free: 105472 kB; (`user_id`) REFER `game/uzivatel`(`u';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ticket_bonus`
--

DROP TABLE IF EXISTS `ticket_bonus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ticket_bonus` (
  `ticket_bonus_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `nazev` varchar(60) NOT NULL DEFAULT '',
  PRIMARY KEY (`ticket_bonus_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='InnoDB free: 105472 kB';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ticket_bonus_uzivatel`
--

DROP TABLE IF EXISTS `ticket_bonus_uzivatel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ticket_bonus_uzivatel` (
  `ticket_bonus_id` smallint(5) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `kod` varchar(40) NOT NULL DEFAULT '',
  `vybral` smallint(5) unsigned NOT NULL DEFAULT '0',
  `bonus_castka` decimal(10,2) unsigned NOT NULL,
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `poznamka` varchar(100) DEFAULT NULL,
  `rel` int(8) NOT NULL COMMENT 'doplnkova informace, napr. vazba na neco',
  `dt_in` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `FK_ticket_bonus_uzivatel_1` (`ticket_bonus_id`),
  KEY `FK_ticket_bonus_uzivatel_2` (`user_id`),
  CONSTRAINT `FK_ticket_bonus_uzivatel_1` FOREIGN KEY (`ticket_bonus_id`) REFERENCES `ticket_bonus` (`ticket_bonus_id`) ON UPDATE CASCADE,
  CONSTRAINT `FK_ticket_bonus_uzivatel_2` FOREIGN KEY (`user_id`) REFERENCES `uzivatel` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='InnoDB free: 105472 kB; (`ticket_bonus_id`) REFER `game/tick';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ticket_combination`
--

DROP TABLE IF EXISTS `ticket_combination`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ticket_combination` (
  `k` int(10) unsigned NOT NULL COMMENT 'pozadovany pocet _k_ vyher v kombinaci _n_ nad _k_',
  `ticket_id` bigint(20) unsigned NOT NULL,
  `stake` decimal(10,2) NOT NULL,
  PRIMARY KEY (`ticket_id`,`k`),
  KEY `ticket_id` (`ticket_id`),
  CONSTRAINT `ticket_combination_ibfk_1` FOREIGN KEY (`ticket_id`) REFERENCES `ticket` (`ticket_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='data pro kombinace skupin';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ticket_kurz`
--

DROP TABLE IF EXISTS `ticket_kurz`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ticket_kurz` (
  `ticket_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `sazka_id` int(10) unsigned NOT NULL DEFAULT '0',
  `sloupec_id` int(10) unsigned NOT NULL DEFAULT '0',
  `ticket_sazka_zrusena` smallint(6) NOT NULL DEFAULT '0',
  `ticket_sazka_zrusil_bookmaker_id` int(11) DEFAULT NULL,
  `ticket_sazka_duvod_zruseni` varchar(255) DEFAULT NULL,
  `banker` smallint(5) unsigned NOT NULL DEFAULT '0',
  `group_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`ticket_id`,`sazka_id`),
  KEY `FK_ticket_kurz_2` (`sazka_id`,`sloupec_id`),
  CONSTRAINT `FK_ticket_kurz_1` FOREIGN KEY (`ticket_id`) REFERENCES `ticket` (`ticket_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='InnoDB free: 105472 kB; (`ticket_id`) REFER `game/ticket`(`t';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Temporary table structure for view `ticket_pohled`
--

DROP TABLE IF EXISTS `ticket_pohled`;
/*!50001 DROP VIEW IF EXISTS `ticket_pohled`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `ticket_pohled` (
  `sazka_id` int(10) unsigned,
  `live` smallint(5) unsigned,
  `platna_do` datetime,
  `user_id` int(10) unsigned,
  `proplacena` smallint(5) unsigned,
  `typ_id` int(10) unsigned,
  `text` varchar(255),
  `vysledek` varchar(250),
  `status` smallint(5) unsigned,
  `udalost_id` int(10) unsigned,
  `kurz` decimal(10,2),
  `platny_od` datetime,
  `sloupec_id` int(10) unsigned,
  `poradi` int(10) unsigned,
  `ticket_sazka_zrusena` smallint(6),
  `ticket_sazka_zrusil_bookmaker_id` int(11),
  `ticket_sazka_duvod_zruseni` varchar(255),
  `ticket_id` bigint(20) unsigned,
  `ticket_handle` char(9),
  `ticket_branch_id` int(10) unsigned,
  `zalozen` datetime,
  `vyplacen` smallint(5) unsigned,
  `type` varchar(45),
  `win` decimal(15,2) unsigned,
  `rate` decimal(15,2) unsigned,
  `win_real` decimal(15,2) unsigned,
  `rate_real` decimal(15,2) unsigned,
  `zruseno` smallint(5) unsigned,
  `kurz_zmena` smallint(6),
  `zrusil_bookmaker_id` int(10) unsigned,
  `duvod_zruseni` varchar(255),
  `castka` decimal(10,2) unsigned,
  `free_bet_bonus` smallint(5) unsigned,
  `system` smallint(5) unsigned,
  `stats` smallint(5) unsigned,
  `stats_user` smallint(5) unsigned,
  `banker` smallint(5) unsigned,
  `cupon_id` varchar(40),
  `mail` smallint(6),
  `group_count` int(10) unsigned,
  `group_t` tinyint(1),
  `group_id` int(10) unsigned,
  `vyplacen_date` datetime,
  `vyplacen_bookmaker_id` smallint(5) unsigned,
  `cash` tinyint(1)
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `ticket_pohled_connect`
--

DROP TABLE IF EXISTS `ticket_pohled_connect`;
/*!50001 DROP VIEW IF EXISTS `ticket_pohled_connect`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `ticket_pohled_connect` (
  `sazka_id` int(10) unsigned,
  `live` smallint(5) unsigned,
  `platna_do` datetime,
  `stats` smallint(5) unsigned,
  `stats_user` smallint(5) unsigned,
  `user_id` int(10) unsigned,
  `proplacena` smallint(5) unsigned,
  `typ_id` int(10) unsigned,
  `text` varchar(255),
  `vysledek` varchar(250),
  `status` smallint(5) unsigned,
  `udalost_id` int(10) unsigned,
  `kurz` decimal(10,2),
  `platny_od` datetime,
  `kurz_zmena` smallint(6),
  `sloupec_id` int(10) unsigned,
  `poradi` int(10) unsigned,
  `ticket_sazka_zrusena` smallint(6),
  `ticket_sazka_zrusil_bookmaker_id` int(11),
  `ticket_sazka_duvod_zruseni` varchar(255),
  `banker` smallint(5) unsigned,
  `group_id` int(10) unsigned,
  `ticket_id` bigint(20) unsigned,
  `ticket_handle` char(9),
  `ticket_branch_id` int(10) unsigned,
  `type` varchar(45),
  `win` decimal(15,2) unsigned,
  `rate` decimal(15,2) unsigned,
  `win_real` decimal(15,2) unsigned,
  `rate_real` decimal(15,2) unsigned,
  `free_bet_bonus` smallint(5) unsigned,
  `zalozen` datetime,
  `vyplacen` smallint(5) unsigned,
  `zruseno` smallint(5) unsigned,
  `zrusil_bookmaker_id` int(10) unsigned,
  `duvod_zruseni` varchar(255),
  `castka` decimal(10,2) unsigned,
  `system` smallint(5) unsigned,
  `cupon_id` varchar(40),
  `mail` smallint(6),
  `group_count` int(10) unsigned,
  `group_t` tinyint(1),
  `vyplacen_date` datetime,
  `vyplacen_bookmaker_id` smallint(5) unsigned,
  `cash` tinyint(1)
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `tickethash`
--

DROP TABLE IF EXISTS `tickethash`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tickethash` (
  `tickethash` varchar(32) CHARACTER SET ascii NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `tickethash_count` int(10) unsigned NOT NULL,
  PRIMARY KEY (`tickethash`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `tickethash_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `uzivatel` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tickethash_ticket`
--

DROP TABLE IF EXISTS `tickethash_ticket`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tickethash_ticket` (
  `tickethash` varchar(32) CHARACTER SET ascii NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `ticket_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`tickethash`,`user_id`,`ticket_id`),
  KEY `user_id` (`user_id`),
  KEY `ticket_id` (`ticket_id`),
  CONSTRAINT `tickethash_ticket_ibfk_1` FOREIGN KEY (`tickethash`) REFERENCES `tickethash` (`tickethash`),
  CONSTRAINT `tickethash_ticket_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `uzivatel` (`user_id`),
  CONSTRAINT `tickethash_ticket_ibfk_3` FOREIGN KEY (`ticket_id`) REFERENCES `ticket` (`ticket_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `translate_missing`
--

DROP TABLE IF EXISTS `translate_missing`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `translate_missing` (
  `translate_key` varchar(32) NOT NULL,
  `lang_id` int(10) NOT NULL,
  `controller` varchar(100) NOT NULL,
  `action` varchar(100) NOT NULL,
  `status` enum('empty','missing') NOT NULL,
  PRIMARY KEY (`translate_key`,`lang_id`,`controller`,`action`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `translate_pages`
--

DROP TABLE IF EXISTS `translate_pages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `translate_pages` (
  `translate_key` varchar(32) NOT NULL,
  `controller` varchar(100) NOT NULL,
  `action` varchar(100) NOT NULL,
  PRIMARY KEY (`translate_key`,`controller`,`action`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `typ`
--

DROP TABLE IF EXISTS `typ`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `typ` (
  `typ_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nazev` varchar(20) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `zobrazeno` smallint(5) unsigned NOT NULL DEFAULT '0',
  `poradi` smallint(5) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`typ_id`)
) ENGINE=InnoDB AUTO_INCREMENT=115 DEFAULT CHARSET=latin1 COMMENT='InnoDB free: 105472 kB';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `typ_podtyp`
--

DROP TABLE IF EXISTS `typ_podtyp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `typ_podtyp` (
  `typ_id` int(10) unsigned NOT NULL DEFAULT '0',
  `sport_id` int(10) unsigned NOT NULL DEFAULT '0',
  `podtyp_id` int(10) unsigned NOT NULL DEFAULT '0',
  `live_bet` smallint(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`typ_id`,`sport_id`,`podtyp_id`),
  KEY `FK_typ_podtyp_2` (`podtyp_id`),
  KEY `FK_typ_podtyp_3` (`sport_id`),
  CONSTRAINT `FK_typ_podtyp_1` FOREIGN KEY (`typ_id`) REFERENCES `typ` (`typ_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_typ_podtyp_2` FOREIGN KEY (`podtyp_id`) REFERENCES `podtyp` (`podtyp_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_typ_podtyp_3` FOREIGN KEY (`sport_id`) REFERENCES `sport` (`sport_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='InnoDB free: 105472 kB; (`typ_id`) REFER `game/typ`(`typ_id`';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `typ_sport`
--

DROP TABLE IF EXISTS `typ_sport`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `typ_sport` (
  `typ_id` int(10) unsigned NOT NULL DEFAULT '0',
  `sport_id` int(10) unsigned NOT NULL DEFAULT '0',
  `vychozi` smallint(5) unsigned NOT NULL DEFAULT '0',
  `poradi` smallint(5) unsigned NOT NULL DEFAULT '2000',
  `vychozi_mobil` smallint(6) DEFAULT '0',
  PRIMARY KEY (`typ_id`,`sport_id`),
  KEY `FK_typ_sport_1` (`sport_id`),
  CONSTRAINT `FK_typ_sport_1` FOREIGN KEY (`sport_id`) REFERENCES `sport` (`sport_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_typ_sport_2` FOREIGN KEY (`typ_id`) REFERENCES `typ` (`typ_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='InnoDB free: 21504 kB; InnoDB free: 105472 kB; (`sport_id`) ';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `udalost`
--

DROP TABLE IF EXISTS `udalost`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `udalost` (
  `udalost_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sport_id` int(10) unsigned NOT NULL DEFAULT '0',
  `pozice` smallint(5) unsigned NOT NULL DEFAULT '0',
  `zobrazeno` smallint(5) unsigned NOT NULL DEFAULT '0',
  `nazev` varchar(20) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `platne_od` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `platne_do` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `zvyrazneni` smallint(5) unsigned NOT NULL DEFAULT '0',
  `oddeleni` smallint(5) unsigned NOT NULL DEFAULT '0',
  `betradar_udalost_id` int(10) unsigned DEFAULT NULL,
  `oblast_id` int(10) unsigned DEFAULT NULL,
  `approval_group_id` int(10) unsigned DEFAULT NULL,
  `bet_alias_from` int(10) unsigned DEFAULT NULL,
  `bet_alias_to` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`udalost_id`),
  KEY `FK_udalost_1` (`sport_id`),
  KEY `FK_udalost_2` (`oblast_id`),
  KEY `Index_4` (`platne_od`,`platne_do`),
  KEY `approval_group_id` (`approval_group_id`),
  CONSTRAINT `FK_udalost_1` FOREIGN KEY (`sport_id`) REFERENCES `sport` (`sport_id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2942 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='InnoDB free: 105472 kB; (`sport_id`) REFER `game/sport`(`spo';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `udalost_betradar`
--

DROP TABLE IF EXISTS `udalost_betradar`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `udalost_betradar` (
  `udalost_id` int(10) unsigned NOT NULL,
  `betradar_udalost_id` int(11) NOT NULL,
  PRIMARY KEY (`udalost_id`,`betradar_udalost_id`),
  KEY `udalost_id` (`udalost_id`),
  CONSTRAINT `udalost_betradar_fk` FOREIGN KEY (`udalost_id`) REFERENCES `udalost` (`udalost_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='InnoDB free: 84992 kB; InnoDB free: 105472 kB; (`udalost_id`';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_actions`
--

DROP TABLE IF EXISTS `user_actions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_actions` (
  `user_id` int(10) NOT NULL,
  `action_type` int(3) NOT NULL,
  `valid_from` datetime NOT NULL,
  `valid_until` datetime NOT NULL,
  `action_hash` char(32) NOT NULL,
  `done` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`action_hash`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_bet_rating`
--

DROP TABLE IF EXISTS `user_bet_rating`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_bet_rating` (
  `user_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `win` decimal(6,2) NOT NULL,
  `week_bet` decimal(15,2) NOT NULL,
  `month_bet` decimal(15,2) NOT NULL,
  PRIMARY KEY (`user_id`),
  CONSTRAINT `FK_user_bet_rating_1` FOREIGN KEY (`user_id`) REFERENCES `uzivatel` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='InnoDB free: 105472 kB; (`user_id`) REFER `game/uzivatel`(`u';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_tracking`
--

DROP TABLE IF EXISTS `user_tracking`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_tracking` (
  `ut_id` int(12) NOT NULL AUTO_INCREMENT,
  `ts` datetime NOT NULL,
  `persistent_id` char(100) NOT NULL DEFAULT '',
  `session_id` char(40) NOT NULL DEFAULT '',
  `remote_ip` char(15) NOT NULL DEFAULT '',
  `xforward` varchar(200) DEFAULT NULL,
  `useragent` varchar(200) NOT NULL DEFAULT '',
  `user_id` int(11) NOT NULL,
  `nick` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`ut_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='InnoDB free: 105472 kB';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `uzivatel`
--

DROP TABLE IF EXISTS `uzivatel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `uzivatel` (
  `user_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` int(10) unsigned NOT NULL,
  `jmeno` varchar(50) DEFAULT NULL,
  `prijmeni` varchar(60) NOT NULL DEFAULT '',
  `nick` varchar(20) NOT NULL DEFAULT '',
  `heslo` varchar(600) DEFAULT NULL,
  `pohlavi` enum('m','f') NOT NULL DEFAULT 'm',
  `datum_narozeni` date NOT NULL DEFAULT '0000-00-00',
  `email` varchar(100) NOT NULL DEFAULT '',
  `zeme_id` smallint(5) unsigned NOT NULL DEFAULT '0',
  `ulice` varchar(100) NOT NULL DEFAULT '',
  `psc` varchar(15) NOT NULL DEFAULT '0',
  `telefon` varchar(30) DEFAULT NULL,
  `mena_id` smallint(5) unsigned NOT NULL DEFAULT '0',
  `info` text,
  `mobil` varchar(30) DEFAULT NULL,
  `newsletter` smallint(5) unsigned NOT NULL DEFAULT '0',
  `vyber_status` smallint(5) unsigned NOT NULL DEFAULT '0',
  `posledni_prihlaseni` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `vyprseni_session` smallint(5) unsigned NOT NULL DEFAULT '0',
  `datum_registrace` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `individualni_max_vklad` int(10) unsigned NOT NULL DEFAULT '0',
  `zakazany` smallint(5) unsigned NOT NULL DEFAULT '0',
  `lang_id` int(10) unsigned NOT NULL DEFAULT '0',
  `misto` varchar(250) NOT NULL DEFAULT '',
  `ucet_status` smallint(5) unsigned NOT NULL DEFAULT '1',
  `vyhernost` decimal(10,2) unsigned DEFAULT NULL,
  `block` smallint(5) unsigned NOT NULL,
  `block_ip` varchar(1000) NOT NULL DEFAULT '',
  `vyhernost_game` decimal(18,4) NOT NULL,
  `bet_stats_win` decimal(18,4) unsigned NOT NULL DEFAULT '0.0000',
  `bet_stats_lose_acc` decimal(18,4) unsigned NOT NULL DEFAULT '0.0000',
  `bet_stats_lose_book` decimal(18,4) unsigned NOT NULL DEFAULT '0.0000',
  `bet_total` decimal(18,4) unsigned NOT NULL DEFAULT '0.0000',
  `win_ticket` int(10) unsigned NOT NULL DEFAULT '0',
  `lose_ticket` int(10) unsigned NOT NULL DEFAULT '0',
  `delete_ticket` int(10) unsigned NOT NULL DEFAULT '0',
  `num_bet_ticket` int(10) unsigned NOT NULL DEFAULT '0',
  `bet_total2` decimal(18,4) unsigned NOT NULL DEFAULT '0.0000',
  `ticket_num` bigint(20) unsigned NOT NULL,
  `finance_rating` smallint(5) unsigned NOT NULL DEFAULT '6',
  `max_bet` int(10) unsigned NOT NULL DEFAULT '99999999',
  `book_info` varchar(2000) DEFAULT NULL,
  `self_excluded_until` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `osloveni` varchar(60) DEFAULT NULL,
  `e_testovaci` enum('ne','test','naseip','auto','pobocka') NOT NULL DEFAULT 'ne' COMMENT 'zda pouzivame uzivatele pro test',
  `block_play` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'kdyz 1=uzivatel nemuze hrat ani sazet',
  `castka_m` decimal(11,2) NOT NULL DEFAULT '0.00',
  `castka_w` decimal(11,2) NOT NULL DEFAULT '0.00',
  `datum_aktivace` datetime DEFAULT NULL,
  `anonymous` tinyint(1) NOT NULL DEFAULT '0',
  `watched` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1=vsechny sazky apod. musi byt schvalovany bookmakerem',
  `client_card_number` varchar(12) NOT NULL,
  `created_by_admin_id` int(10) unsigned NOT NULL,
  `allowed_by_admin_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `Index_2` (`email`,`nick`),
  KEY `FK_zeme` (`zeme_id`),
  KEY `FK_mena` (`mena_id`),
  KEY `client_card_number` (`client_card_number`),
  KEY `created_by_admin_id` (`created_by_admin_id`),
  KEY `allowed_by_admin_id` (`allowed_by_admin_id`),
  CONSTRAINT `FK_mena` FOREIGN KEY (`mena_id`) REFERENCES `mena` (`mena_id`) ON UPDATE CASCADE,
  CONSTRAINT `FK_zeme` FOREIGN KEY (`zeme_id`) REFERENCES `zeme` (`zeme_id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COMMENT='InnoDB free: 22528 kB; (`mena_id`) REFER `game/mena`(`mena_i';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Temporary table structure for view `uzivatel_data`
--

DROP TABLE IF EXISTS `uzivatel_data`;
/*!50001 DROP VIEW IF EXISTS `uzivatel_data`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `uzivatel_data` (
  `user_id` int(10) unsigned,
  `datum_registrace` datetime,
  `mena_id` smallint(5) unsigned,
  `zakazany` smallint(5) unsigned,
  `ucet_status` smallint(5) unsigned,
  `zustatek` decimal(60,4),
  `zetony` decimal(40,2) unsigned,
  `block_play` tinyint(1) unsigned
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `uzivatel_im_data`
--

DROP TABLE IF EXISTS `uzivatel_im_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `uzivatel_im_data` (
  `user_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `zustatek` decimal(60,4) NOT NULL DEFAULT '0.0000',
  `zetony` decimal(40,2) unsigned NOT NULL DEFAULT '0.00',
  `dluh` decimal(10,4) unsigned NOT NULL DEFAULT '0.0000',
  `zustatek_bonus` decimal(11,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`user_id`),
  CONSTRAINT `FK_uzivatel_im_data_1` FOREIGN KEY (`user_id`) REFERENCES `uzivatel` (`user_id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COMMENT='InnoDB free: 105472 kB; (`user_id`) REFER `game/uzivatel`(`u';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `uzivatel_pobocka`
--

DROP TABLE IF EXISTS `uzivatel_pobocka`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `uzivatel_pobocka` (
  `user_id` int(11) unsigned NOT NULL,
  `pobocka_id` int(11) unsigned NOT NULL,
  KEY `user_id` (`user_id`),
  KEY `pobocka_id` (`pobocka_id`),
  CONSTRAINT `uzivatel_pobocka_fk` FOREIGN KEY (`user_id`) REFERENCES `uzivatel` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `uzivatel_pobocka_fk1` FOREIGN KEY (`pobocka_id`) REFERENCES `pobocka` (`pobocka_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `uzivatel_poznamka`
--

DROP TABLE IF EXISTS `uzivatel_poznamka`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `uzivatel_poznamka` (
  `user_id` int(10) unsigned zerofill NOT NULL DEFAULT '0000000000',
  `text` varchar(2000) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `admin_id` int(10) unsigned NOT NULL,
  `datum` datetime NOT NULL,
  `nl_id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`nl_id`),
  KEY `FK_uzivatel_poznamka_1` (`user_id`),
  CONSTRAINT `FK_uzivatel_poznamka_1` FOREIGN KEY (`user_id`) REFERENCES `uzivatel` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1 COMMENT='InnoDB free: 105472 kB; (`user_id`) REFER `game/uzivatel`(`u';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `uzivatel_smazane_akce`
--

DROP TABLE IF EXISTS `uzivatel_smazane_akce`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `uzivatel_smazane_akce` (
  `user_id` int(10) NOT NULL,
  `od` datetime DEFAULT NULL,
  `do` datetime DEFAULT NULL,
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='InnoDB free: 105472 kB';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `uzivatel_supply`
--

DROP TABLE IF EXISTS `uzivatel_supply`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `uzivatel_supply` (
  `nl_id` int(8) NOT NULL AUTO_INCREMENT,
  `user_id` int(8) NOT NULL COMMENT 'uzivatel',
  `s_data_name` varchar(100) COLLATE utf8_bin NOT NULL DEFAULT '' COMMENT 'nazev parametru',
  `s_data_value` text COLLATE utf8_bin NOT NULL COMMENT 'hodnota parametru',
  `dt_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'cas posledni zmeny',
  PRIMARY KEY (`nl_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='InnoDB free: 464896 kB';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Temporary table structure for view `view_admin_ticket`
--

DROP TABLE IF EXISTS `view_admin_ticket`;
/*!50001 DROP VIEW IF EXISTS `view_admin_ticket`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `view_admin_ticket` (
  `user_id` int(10) unsigned,
  `jmeno` varchar(50),
  `prijmeni` varchar(60),
  `nick` varchar(20),
  `mena_id` smallint(5) unsigned,
  `zustatek` decimal(60,4),
  `kurz` decimal(20,2),
  `platny_od` date,
  `platny_do` date
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `view_typ_id`
--

DROP TABLE IF EXISTS `view_typ_id`;
/*!50001 DROP VIEW IF EXISTS `view_typ_id`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `view_typ_id` (
  `nazev` varchar(20),
  `typ_id` int(10) unsigned,
  `sport_id` int(10) unsigned
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `vracene_sazky`
--

DROP TABLE IF EXISTS `vracene_sazky`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vracene_sazky` (
  `ticket_id` bigint(20) DEFAULT NULL,
  `sazka_id` bigint(20) DEFAULT NULL,
  `bookmaker_id` int(11) DEFAULT NULL,
  `datum` datetime DEFAULT NULL,
  KEY `ticket_id` (`ticket_id`,`sazka_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='InnoDB free: 105472 kB';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `vyherci`
--

DROP TABLE IF EXISTS `vyherci`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vyherci` (
  `sekce_id` smallint(6) NOT NULL,
  `typ_id` smallint(6) NOT NULL,
  `zeme_id` smallint(6) NOT NULL,
  `hra_id` smallint(6) DEFAULT NULL,
  `nick` varchar(255) NOT NULL DEFAULT '',
  `castka` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='InnoDB free: 105472 kB';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `vyherci_sazky`
--

DROP TABLE IF EXISTS `vyherci_sazky`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vyherci_sazky` (
  `user_id` int(10) NOT NULL,
  `ticket_id` bigint(20) NOT NULL,
  `castka` decimal(10,2) NOT NULL,
  `datum` datetime NOT NULL,
  `rate` decimal(11,2) DEFAULT '0.00',
  `full_win` smallint(6) DEFAULT '1',
  PRIMARY KEY (`user_id`,`ticket_id`,`datum`),
  KEY `ticket_id` (`ticket_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='InnoDB free: 105472 kB';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `webservice_session`
--

DROP TABLE IF EXISTS `webservice_session`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `webservice_session` (
  `ses_id` varchar(100) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `time` int(10) unsigned NOT NULL,
  `loginStatus` smallint(6) DEFAULT '0',
  PRIMARY KEY (`ses_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='InnoDB free: 105472 kB';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `week_month_winners`
--

DROP TABLE IF EXISTS `week_month_winners`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `week_month_winners` (
  `date_from` date NOT NULL,
  `date_to` date NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `freeBet_amount` decimal(10,2) NOT NULL,
  `type` enum('w','m') COLLATE utf8_bin NOT NULL COMMENT 'w = vikendovy vitez\r\nm = mesicni vitez',
  `ticket_id` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin MAX_ROWS=8000000 COMMENT='InnoDB free: 105472 kB';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `zeme`
--

DROP TABLE IF EXISTS `zeme`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `zeme` (
  `zeme_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `zeme_T` int(11) NOT NULL,
  `zeme_TA` int(11) NOT NULL,
  `zeme_ADM` int(10) unsigned NOT NULL,
  `zeme_allow` smallint(1) unsigned NOT NULL DEFAULT '1',
  `nazev` varchar(45) NOT NULL DEFAULT '',
  `zobrazeno` smallint(5) unsigned NOT NULL DEFAULT '0',
  `kod` varchar(6) DEFAULT NULL,
  PRIMARY KEY (`zeme_id`)
) ENGINE=InnoDB AUTO_INCREMENT=246 DEFAULT CHARSET=utf8 COMMENT='InnoDB free: 105472 kB';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Final view structure for view `admin`
--

/*!50001 DROP TABLE IF EXISTS `admin`*/;
/*!50001 DROP VIEW IF EXISTS `admin`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `admin` AS select `uzivatel`.`user_id` AS `user_id`,`uzivatel`.`block` AS `block`,`uzivatel`.`block_ip` AS `block_ip`,`uzivatel`.`jmeno` AS `jmeno`,`uzivatel`.`prijmeni` AS `prijmeni`,`uzivatel`.`nick` AS `nick`,`uzivatel`.`heslo` AS `heslo`,`uzivatel`.`pohlavi` AS `pohlavi`,`uzivatel`.`datum_narozeni` AS `datum_narozeni`,`uzivatel`.`email` AS `email`,`uzivatel`.`zeme_id` AS `zeme_id`,`uzivatel`.`ulice` AS `ulice`,`uzivatel`.`psc` AS `psc`,`uzivatel`.`telefon` AS `telefon`,`uzivatel`.`mena_id` AS `mena_id`,`uzivatel`.`info` AS `info`,`uzivatel`.`mobil` AS `mobil`,`uzivatel`.`newsletter` AS `newsletter`,`uzivatel`.`vyber_status` AS `vyber_status`,`uzivatel`.`posledni_prihlaseni` AS `posledni_prihlaseni`,`uzivatel`.`vyprseni_session` AS `vyprseni_session`,`uzivatel`.`datum_registrace` AS `datum_registrace`,`uzivatel`.`individualni_max_vklad` AS `individualni_max_vklad`,`uzivatel`.`zakazany` AS `zakazany`,`uzivatel`.`ucet_status` AS `ucet_status`,`uzivatel`.`self_excluded_until` AS `self_excluded_until` from `uzivatel` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `galerie_pohled`
--

/*!50001 DROP TABLE IF EXISTS `galerie_pohled`*/;
/*!50001 DROP VIEW IF EXISTS `galerie_pohled`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `galerie_pohled` AS select `a`.`image_id` AS `image_id`,`a`.`name` AS `name`,`a`.`popis` AS `popis`,`c`.`galerie_menu_id` AS `galerie_menu_id`,`d`.`alt` AS `alt`,`e`.`lang_id` AS `lang_id` from ((((`galerie` `a` left join `galerie_prirad_menu` `b` on((`a`.`image_id` = `b`.`image_id`))) left join `galerie_menu` `c` on((`c`.`galerie_menu_id` = `b`.`galerie_menu_id`))) left join `galerie_alt` `d` on((`a`.`image_id` = `d`.`image_id`))) left join `jazyky` `e` on((`e`.`lang_id` = `d`.`lang_id`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `kurzmena`
--

/*!50001 DROP TABLE IF EXISTS `kurzmena`*/;
/*!50001 DROP VIEW IF EXISTS `kurzmena`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `kurzmena` AS select `a`.`id_kurz` AS `id_kurz`,`a`.`platny_od` AS `platny_od`,`a`.`platny_do` AS `platny_do`,`b`.`kurz` AS `kurz`,`c`.`mena_id` AS `mena_id`,`c`.`mena_text` AS `mena_text` from (`kurz` `a` join (`kurz_mena` `b` join `mena` `c` on((`b`.`id_mena` = `c`.`mena_id`))) on((`a`.`id_kurz` = `b`.`id_kurz`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `live`
--

/*!50001 DROP TABLE IF EXISTS `live`*/;
/*!50001 DROP VIEW IF EXISTS `live`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `live` AS select `l`.`event_id` AS `event_id`,`l`.`start_date` AS `start_date`,`l`.`stav` AS `stav`,`b`.`no_comb` AS `no_comb`,`l`.`l_sport_id` AS `l_sport_id`,`l`.`l_udalost_id` AS `l_udalost_id`,`l`.`vypnuto` AS `vypnuto`,`l`.`home_team` AS `home_team`,`l`.`away_team` AS `away_team`,`l`.`live_bookmaker_id` AS `live_bookmaker_id`,`p`.`sazka_id` AS `sazka_id`,`p`.`proplatil_bookmaker` AS `proplatil_bookmaker`,`p`.`proplacena` AS `proplacena`,`p`.`platna_od` AS `platna_od`,`p`.`platna_do` AS `platna_do`,`p`.`status` AS `status`,`p`.`live` AS `live`,`p`.`kurz_zmena` AS `kurz_zmena`,`p`.`bookmaker_id` AS `bookmaker_id`,`p`.`vysledek` AS `vysledek`,`p`.`udalost_id` AS `udalost_id`,`p`.`typ_id` AS `typ_id`,`p`.`podtyp_id` AS `podtyp_id`,`p`.`overena` AS `overena`,`p`.`text` AS `text`,`p`.`jednoducha` AS `jednoducha`,`p`.`risk_limit` AS `risk_limit`,`p`.`risk_limit_balance` AS `risk_limit_balance`,`b`.`close` AS `close`,`b`.`aktualizace` AS `aktualizace`,`b`.`aktualizace_sazka` AS `aktualizace_sazka`,`p`.`poradi` AS `poradi`,`p`.`kurz` AS `kurz`,`p`.`platny_od` AS `platny_od`,`p`.`nazev` AS `nazev`,`p`.`sloupec_id` AS `sloupec_id`,`p`.`poradi_sloupec` AS `poradi_sloupec`,`b`.`betradar_sazka_id` AS `betradar_sazka_id`,`b`.`no_update` AS `no_update` from ((`live_event` `l` join `live_sazka` `b` on((`l`.`event_id` = `b`.`event_id`))) join `sazka_pohled` `p` on((`b`.`sazka_id` = `p`.`sazka_id`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `prava_bookmaker`
--

/*!50001 DROP TABLE IF EXISTS `prava_bookmaker`*/;
/*!50001 DROP VIEW IF EXISTS `prava_bookmaker`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `prava_bookmaker` AS select `a`.`bookmaker_id` AS `bookmaker_id`,`a`.`jmeno` AS `jmeno`,`a`.`prijmeni` AS `prijmeni`,`b`.`povoleny` AS `povoleny`,`c`.`udalost_id` AS `udalost_id`,`c`.`sport_id` AS `sport_id`,`c`.`nazev` AS `nazev` from (`bookmaker` `a` join (`prava_udalost` `b` join `udalost` `c` on((`b`.`udalost_id` = `c`.`udalost_id`))) on((`a`.`bookmaker_id` = `b`.`bookmaker_id`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `sazka_pohled`
--

/*!50001 DROP TABLE IF EXISTS `sazka_pohled`*/;
/*!50001 DROP VIEW IF EXISTS `sazka_pohled`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `sazka_pohled` AS select `a`.`sazka_id` AS `sazka_id`,`a`.`proplatil_bookmaker` AS `proplatil_bookmaker`,`a`.`proplacena` AS `proplacena`,`a`.`info` AS `info`,`a`.`betradar_sazka_id` AS `betradar_sazka_id`,`a`.`platna_od` AS `platna_od`,`a`.`platna_do` AS `platna_do`,`a`.`status` AS `status`,`a`.`live` AS `live`,`a`.`bookmaker_id` AS `bookmaker_id`,`a`.`vysledek` AS `vysledek`,`a`.`udalost_id` AS `udalost_id`,`a`.`typ_id` AS `typ_id`,`a`.`podtyp_id` AS `podtyp_id`,`a`.`overena` AS `overena`,`a`.`text` AS `text`,`a`.`ako` AS `ako`,`a`.`jednoducha` AS `jednoducha`,`a`.`risk_limit` AS `risk_limit`,`a`.`risk_limit_balance` AS `risk_limit_balance`,`b`.`poradi` AS `poradi`,`b`.`kurz` AS `kurz`,`b`.`kurz_zmena` AS `kurz_zmena`,`b`.`platny_od` AS `platny_od`,`c`.`nazev` AS `nazev`,`c`.`sloupec_id` AS `sloupec_id`,`c`.`poradi` AS `poradi_sloupec`,`a`.`betradar_autoupdate` AS `betradar_autoupdate`,`a`.`score` AS `score` from (`sazky` `a` join (`sazka_kurz` `b` join `podtyp_sloupce` `c` on((`b`.`sloupec_id` = `c`.`sloupec_id`))) on((`a`.`sazka_id` = `b`.`sazka_id`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `ticket_pohled`
--

/*!50001 DROP TABLE IF EXISTS `ticket_pohled`*/;
/*!50001 DROP VIEW IF EXISTS `ticket_pohled`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `ticket_pohled` AS select `b`.`sazka_id` AS `sazka_id`,`b`.`live` AS `live`,`b`.`platna_do` AS `platna_do`,`b`.`user_id` AS `user_id`,`b`.`proplacena` AS `proplacena`,`b`.`typ_id` AS `typ_id`,`b`.`text` AS `text`,`b`.`vysledek` AS `vysledek`,`b`.`status` AS `status`,`b`.`udalost_id` AS `udalost_id`,`b`.`kurz` AS `kurz`,`b`.`platny_od` AS `platny_od`,`b`.`sloupec_id` AS `sloupec_id`,`b`.`poradi` AS `poradi`,`b`.`ticket_sazka_zrusena` AS `ticket_sazka_zrusena`,`b`.`ticket_sazka_zrusil_bookmaker_id` AS `ticket_sazka_zrusil_bookmaker_id`,`b`.`ticket_sazka_duvod_zruseni` AS `ticket_sazka_duvod_zruseni`,`b`.`ticket_id` AS `ticket_id`,`b`.`ticket_handle` AS `ticket_handle`,`b`.`ticket_branch_id` AS `ticket_branch_id`,`b`.`zalozen` AS `zalozen`,`b`.`vyplacen` AS `vyplacen`,`b`.`type` AS `type`,`b`.`win` AS `win`,`b`.`rate` AS `rate`,`b`.`win_real` AS `win_real`,`b`.`rate_real` AS `rate_real`,`b`.`zruseno` AS `zruseno`,`b`.`kurz_zmena` AS `kurz_zmena`,`b`.`zrusil_bookmaker_id` AS `zrusil_bookmaker_id`,`b`.`duvod_zruseni` AS `duvod_zruseni`,`b`.`castka` AS `castka`,`b`.`free_bet_bonus` AS `free_bet_bonus`,`b`.`system` AS `system`,`b`.`stats` AS `stats`,`b`.`stats_user` AS `stats_user`,`b`.`banker` AS `banker`,`b`.`cupon_id` AS `cupon_id`,`b`.`mail` AS `mail`,`b`.`group_count` AS `group_count`,`b`.`group_t` AS `group_t`,`b`.`group_id` AS `group_id`,`b`.`vyplacen_date` AS `vyplacen_date`,`b`.`vyplacen_bookmaker_id` AS `vyplacen_bookmaker_id`,`b`.`cash` AS `cash` from `ticket_pohled_connect` `b` where (`b`.`platny_od` = (select max(`a`.`platny_od`) AS `maxi_platny_od` from `ticket_pohled_connect` `a` where ((`a`.`zalozen` >= `a`.`platny_od`) and (`a`.`sazka_id` = `b`.`sazka_id`) and (`a`.`ticket_id` = `b`.`ticket_id`)) group by `a`.`ticket_id`,`a`.`sazka_id`)) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `ticket_pohled_connect`
--

/*!50001 DROP TABLE IF EXISTS `ticket_pohled_connect`*/;
/*!50001 DROP VIEW IF EXISTS `ticket_pohled_connect`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `ticket_pohled_connect` AS select `a`.`sazka_id` AS `sazka_id`,`a`.`live` AS `live`,`a`.`platna_do` AS `platna_do`,`d`.`stats` AS `stats`,`d`.`stats_user` AS `stats_user`,`d`.`user_id` AS `user_id`,`a`.`proplacena` AS `proplacena`,`a`.`typ_id` AS `typ_id`,`a`.`text` AS `text`,`a`.`vysledek` AS `vysledek`,`a`.`status` AS `status`,`a`.`udalost_id` AS `udalost_id`,`b`.`kurz` AS `kurz`,`b`.`platny_od` AS `platny_od`,`b`.`kurz_zmena` AS `kurz_zmena`,`b`.`sloupec_id` AS `sloupec_id`,`b`.`poradi` AS `poradi`,`c`.`ticket_sazka_zrusena` AS `ticket_sazka_zrusena`,`c`.`ticket_sazka_zrusil_bookmaker_id` AS `ticket_sazka_zrusil_bookmaker_id`,`c`.`ticket_sazka_duvod_zruseni` AS `ticket_sazka_duvod_zruseni`,`c`.`banker` AS `banker`,`c`.`group_id` AS `group_id`,`d`.`ticket_id` AS `ticket_id`,`d`.`handle` AS `ticket_handle`,`d`.`branch_id` AS `ticket_branch_id`,`d`.`type` AS `type`,`d`.`win` AS `win`,`d`.`rate` AS `rate`,`d`.`win_real` AS `win_real`,`d`.`rate_real` AS `rate_real`,`d`.`free_bet_bonus` AS `free_bet_bonus`,`d`.`zalozen` AS `zalozen`,`d`.`vyplacen` AS `vyplacen`,`d`.`zruseno` AS `zruseno`,`d`.`zrusil_bookmaker_id` AS `zrusil_bookmaker_id`,`d`.`duvod_zruseni` AS `duvod_zruseni`,`d`.`castka` AS `castka`,`d`.`system` AS `system`,`d`.`cupon_id` AS `cupon_id`,`d`.`mail` AS `mail`,`d`.`group_count` AS `group_count`,`d`.`group_t` AS `group_t`,`d`.`vyplacen_date` AS `vyplacen_date`,`d`.`vyplacen_bookmaker_id` AS `vyplacen_bookmaker_id`,`d`.`cash` AS `cash` from (`sazky` `a` join (`sazka_kurz` `b` join (`ticket_kurz` `c` join `ticket` `d` on((`c`.`ticket_id` = `d`.`ticket_id`))) on(((`b`.`sazka_id` = `c`.`sazka_id`) and (`b`.`sloupec_id` = `c`.`sloupec_id`)))) on((`a`.`sazka_id` = `b`.`sazka_id`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `uzivatel_data`
--

/*!50001 DROP TABLE IF EXISTS `uzivatel_data`*/;
/*!50001 DROP VIEW IF EXISTS `uzivatel_data`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `uzivatel_data` AS select `a`.`user_id` AS `user_id`,`a`.`datum_registrace` AS `datum_registrace`,`a`.`mena_id` AS `mena_id`,`a`.`zakazany` AS `zakazany`,`a`.`ucet_status` AS `ucet_status`,`b`.`zustatek` AS `zustatek`,`b`.`zetony` AS `zetony`,`a`.`block_play` AS `block_play` from (`uzivatel` `a` join `uzivatel_im_data` `b` on((`a`.`user_id` = `b`.`user_id`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `view_admin_ticket`
--

/*!50001 DROP TABLE IF EXISTS `view_admin_ticket`*/;
/*!50001 DROP VIEW IF EXISTS `view_admin_ticket`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `view_admin_ticket` AS select `a`.`user_id` AS `user_id`,`a`.`jmeno` AS `jmeno`,`a`.`prijmeni` AS `prijmeni`,`a`.`nick` AS `nick`,`a`.`mena_id` AS `mena_id`,`b`.`zustatek` AS `zustatek`,`c`.`kurz` AS `kurz`,`c`.`platny_od` AS `platny_od`,`c`.`platny_do` AS `platny_do` from ((`uzivatel` `a` join `uzivatel_im_data` `b` on((`a`.`user_id` = `b`.`user_id`))) join `kurzmena` `c` on((`a`.`mena_id` = `c`.`mena_id`))) where ((`c`.`platny_od` <= now()) and (`c`.`platny_do` > now())) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `view_typ_id`
--

/*!50001 DROP TABLE IF EXISTS `view_typ_id`*/;
/*!50001 DROP VIEW IF EXISTS `view_typ_id`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `view_typ_id` AS select `b`.`nazev` AS `nazev`,`b`.`typ_id` AS `typ_id`,`d`.`sport_id` AS `sport_id` from (`typ` `b` join `typ_sport` `d` on((`b`.`typ_id` = `d`.`typ_id`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2010-11-03 11:55:11
