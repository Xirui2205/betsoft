-- MySQL dump 10.13  Distrib 5.1.47, for unknown-linux-gnu (x86_64)
--
-- Host: localhost    Database: vic_main
-- ------------------------------------------------------
-- Server version	5.1.47

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
-- Table structure for table `archive_cronjob`
--

DROP TABLE IF EXISTS `archive_cronjob`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `archive_cronjob` (
  `cronjob_id` int(11) NOT NULL AUTO_INCREMENT,
  `type` int(1) NOT NULL,
  `sent` tinyint(1) NOT NULL,
  `attempts` int(2) NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY (`cronjob_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `archive_cronjob_has_param`
--

DROP TABLE IF EXISTS `archive_cronjob_has_param`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `archive_cronjob_has_param` (
  `cronjob_id` int(11) NOT NULL,
  `param_id` int(11) NOT NULL,
  PRIMARY KEY (`cronjob_id`,`param_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `archive_cronjob_param`
--

DROP TABLE IF EXISTS `archive_cronjob_param`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `archive_cronjob_param` (
  `param_id` int(11) NOT NULL AUTO_INCREMENT,
  `param_name` varchar(255) NOT NULL,
  `param_value` text NOT NULL,
  PRIMARY KEY (`param_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
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
  CONSTRAINT `FK_bookmaker_ticket_prove_log_1` FOREIGN KEY (`user_id`) REFERENCES `uzivatel` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_bookmaker_ticket_prove_log_2` FOREIGN KEY (`bookmaker_id`) REFERENCES `bookmaker` (`bookmaker_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='InnoDB free: 105472 kB; (`user_id`) REFER `game/uzivatel`(`u';
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
  `type` int(10) unsigned NOT NULL,
  `sent` tinyint(1) NOT NULL,
  `attempts` int(2) NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY (`cronjob_id`),
  KEY `type` (`type`),
  CONSTRAINT `cronjob_ibfk_1` FOREIGN KEY (`type`) REFERENCES `cronjob_type` (`type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=68 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cronjob_has_param`
--

DROP TABLE IF EXISTS `cronjob_has_param`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cronjob_has_param` (
  `cronjob_id` int(11) NOT NULL,
  `param_id` int(11) NOT NULL,
  PRIMARY KEY (`cronjob_id`,`param_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cronjob_param`
--

DROP TABLE IF EXISTS `cronjob_param`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cronjob_param` (
  `param_id` int(11) NOT NULL AUTO_INCREMENT,
  `param_name` varchar(255) NOT NULL,
  `param_value` text NOT NULL,
  PRIMARY KEY (`param_id`)
) ENGINE=MyISAM AUTO_INCREMENT=160 DEFAULT CHARSET=utf8;
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
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
-- Table structure for table `finacni_transakce`
--

DROP TABLE IF EXISTS `finacni_transakce`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `finacni_transakce` (
  `transakce_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `castka` decimal(11,2) unsigned NOT NULL DEFAULT '0.00',
  `poplatky` decimal(11,2) unsigned NOT NULL DEFAULT '0.00',
  `typ_platby` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '1 vklad 2 vyber',
  `metoda_id` smallint(6) unsigned NOT NULL,
  `metoda_sub_id` int(11) unsigned NOT NULL COMMENT 'o jakou kartu bankovni ucet apod jde',
  `datum` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `mena_id` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`transakce_id`),
  KEY `mena_id` (`mena_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `finacni_transakce_fk` FOREIGN KEY (`mena_id`) REFERENCES `mena` (`mena_id`) ON UPDATE CASCADE,
  CONSTRAINT `finacni_transakce_fk1` FOREIGN KEY (`user_id`) REFERENCES `uzivatel` (`user_id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='InnoDB free: 105472 kB';
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='InnoDB free: 105472 kB';
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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci COMMENT='InnoDB free: 105472 kB; (`event_id`) REFER `game/live_event`';
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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci COMMENT='InnoDB free: 105472 kB; (`event_id`) REFER `game/live_event`';
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
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci COMMENT='InnoDB free: 105472 kB; (`event_id`) REFER `game/live_event`';
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
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci COMMENT='InnoDB free: 105472 kB; (`l_sport_id`) REFER `game/sport`(`s';
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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci COMMENT='InnoDB free: 105472 kB; (`event_id`) REFER `game/live_event`';
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 COMMENT='InnoDB free: 105472 kB';
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
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='InnoDB free: 105472 kB';
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
  `text` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
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
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='InnoDB free: 105472 kB; (`sazka1_id`) REFER `game/sazky`(`sa';
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
  `poradi_sloupec` smallint(5) unsigned
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
  PRIMARY KEY (`sazka_id`),
  KEY `FK_sazky_1` (`bookmaker_id`),
  KEY `FK_sazky_2` (`udalost_id`),
  KEY `FK_sazky_3` (`typ_id`),
  KEY `FK_sazky_4` (`podtyp_id`),
  KEY `FK_sazky_5` (`vysledek`),
  KEY `datumod` (`platna_od`),
  KEY `datumdo` (`platna_do`),
  KEY `betradar_sazka_id` (`betradar_sazka_id`),
  CONSTRAINT `FK_sazky_1` FOREIGN KEY (`bookmaker_id`) REFERENCES `bookmaker` (`bookmaker_id`) ON UPDATE CASCADE,
  CONSTRAINT `FK_sazky_2` FOREIGN KEY (`udalost_id`) REFERENCES `udalost` (`udalost_id`) ON UPDATE CASCADE,
  CONSTRAINT `FK_sazky_3` FOREIGN KEY (`typ_id`) REFERENCES `typ` (`typ_id`) ON UPDATE CASCADE,
  CONSTRAINT `FK_sazky_4` FOREIGN KEY (`podtyp_id`) REFERENCES `podtyp` (`podtyp_id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=646506 DEFAULT CHARSET=latin1 COMMENT='InnoDB free: 105472 kB; (`bookmaker_id`) REFER `game/bookmak';
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
  PRIMARY KEY (`sport_id`)
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
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8;
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
) ENGINE=InnoDB AUTO_INCREMENT=77 DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='InnoDB free: 98304 kB; InnoDB free: 105472 kB';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ticket`
--

DROP TABLE IF EXISTS `ticket`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ticket` (
  `ticket_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
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
  PRIMARY KEY (`ticket_id`),
  KEY `FK_ticket_1` (`user_id`),
  KEY `FK_ticket_2` (`zrusil_bookmaker_id`),
  KEY `cupon` (`cupon_id`),
  CONSTRAINT `FK_ticket_1` FOREIGN KEY (`user_id`) REFERENCES `uzivatel` (`user_id`) ON UPDATE CASCADE,
  CONSTRAINT `FK_ticket_2` FOREIGN KEY (`zrusil_bookmaker_id`) REFERENCES `bookmaker` (`bookmaker_id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='InnoDB free: 105472 kB; (`user_id`) REFER `game/uzivatel`(`u';
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='InnoDB free: 105472 kB; (`ticket_id`) REFER `game/ticket`(`t';
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
  `group_id` int(10) unsigned
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
  `group_t` tinyint(1)
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

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
  PRIMARY KEY (`udalost_id`),
  KEY `FK_udalost_1` (`sport_id`),
  KEY `FK_udalost_2` (`oblast_id`),
  KEY `Index_4` (`platne_od`,`platne_do`),
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
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `Index_2` (`email`,`nick`),
  KEY `FK_zeme` (`zeme_id`),
  KEY `FK_mena` (`mena_id`),
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
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='InnoDB free: 105472 kB; (`user_id`) REFER `game/uzivatel`(`u';
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



-- --------------------------------------------------------

--
-- Table structure for table `offergen_tiskopis`
--

CREATE TABLE IF NOT EXISTS `offergen_tiskopis` (
  `id_tiskopis` int(11) NOT NULL AUTO_INCREMENT,
  `id_typ_tiskopisu` int(11) NOT NULL,
  `parametry` varchar(100) DEFAULT NULL,
  `pozadavek` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `pripraven` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `pocet_stran` int(11) DEFAULT NULL,
  `jazyk` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_tiskopis`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `offergen_tiskopis`
--


-- --------------------------------------------------------

--
-- Table structure for table `offergen_typ_tiskopisu`
--

CREATE TABLE IF NOT EXISTS `offergen_typ_tiskopisu` (
  `id_typ_tiskopisu` int(11) NOT NULL AUTO_INCREMENT,
  `typ_tiskopisu_nazev` varchar(30) NOT NULL DEFAULT '',
  `obnova1` int(11) DEFAULT NULL,
  `obnova2` int(11) DEFAULT NULL,
  `mazat` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_typ_tiskopisu`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `offergen_typ_tiskopisu`
--

--
-- Dumping routines for database 'vic_main'
--
/*!50003 DROP FUNCTION IF EXISTS `fn_curr_rate` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 FUNCTION `fn_curr_rate`(
	ind_datum DATETIME,
	inl_mena_id int(6)
	) RETURNS decimal(10,4)
    DETERMINISTIC
BEGIN
	DECLARE _ret DECIMAL(10,4) DEFAULT 0;
	SELECT
			mk.kurz
		INTO
			_ret
		FROM mena_kurz mk
		WHERE mk.mena_id = inl_mena_id AND
			date(mk.timestamp) = date(ind_datum)
		LIMIT 1;
	IF _ret > 0 THEN
		RETURN _ret;
	END IF;
	SELECT
			mk.kurz
		INTO
			_ret
		FROM mena_kurz mk
		WHERE mk.mena_id = inl_mena_id AND
			date(mk.timestamp) < date(ind_datum)
		ORDER BY mk.timestamp desc
		LIMIT 1;
	IF _ret > 0 THEN
		RETURN _ret;
	END IF;
	SELECT
			mk.kurz
		INTO
			_ret
		FROM kurzmena mk
		WHERE mk.mena_id = inl_mena_id AND
			platny_od <= date(ind_datum) AND  date(ind_datum) <= platny_do
		LIMIT 1;
	RETURN _ret;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `fn_get_const` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 FUNCTION `fn_get_const`(s_const VARCHAR(45)) RETURNS varchar(20) CHARSET utf8
    DETERMINISTIC
BEGIN
  DECLARE _const VARCHAR(20);
  SELECT
    a.s_hodnota INTO _const
  FROM t_cfg a WHERE a.s_nazev = s_const;
  RETURN _const;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `fn_get_money_euro2uzivatel` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 FUNCTION `fn_get_money_euro2uzivatel`(inl_id_user INTEGER(11), ind_castka DECIMAL(10,2)) RETURNS decimal(10,2)
    DETERMINISTIC
BEGIN
  RETURN (SELECT
                b.kurz * ind_castka
            FROM uzivatel a
            JOIN mena_kurz b ON (a.mena_id = b.mena_id)
            WHERE
            	a.user_id = inl_id_user
            ORDER BY b.mena_kurz_id DESC
            LIMIT 1);
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `fn_get_money_uzivatel2euro` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 FUNCTION `fn_get_money_uzivatel2euro`(inl_id_user INTEGER(11), ind_castka DECIMAL(10,2)) RETURNS decimal(10,2)
    DETERMINISTIC
BEGIN
  RETURN (SELECT
                ind_castka / b.kurz
            FROM uzivatel a
            JOIN mena_kurz b ON (a.mena_id = b.mena_id)
            WHERE
            	a.user_id = inl_id_user
            ORDER BY b.mena_kurz_id DESC
            LIMIT 1);
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `fn_get_uniq_hash` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 FUNCTION `fn_get_uniq_hash`() RETURNS char(40) CHARSET utf8
    DETERMINISTIC
BEGIN
  RETURN CONCAT('',MD5(CONCAT(NOW(), RAND() * RAND())));
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `fn_is_bonus_shoda` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 FUNCTION `fn_is_bonus_shoda`(
        inl_id_user INTEGER(11),
        inl_id_campaign INTEGER(11)
    ) RETURNS tinyint(1)
    DETERMINISTIC
BEGIN
  DECLARE _is TINYINT(1) DEFAULT 0;

  SELECT
	1
    INTO
    _is
FROM uzivatel a
JOIN user_tracking b ON (a.user_id = b.`user_id`)
JOIN user_tracking c ON ((c.`xforward` = b.`xforward` OR c.`persistent_id` = b.`persistent_id`) AND c.user_id != a.`user_id`)
JOIN user_campaign_chips d ON (d.`user_id` = c.`user_id`)
WHERE
	a.user_id = inl_id_user AND
    (d.campaign_id = inl_id_campaign OR inl_id_campaign IS NULL) AND
    b.xforward NOT IN  ('', '62.77.88.215') AND
    b.persistent_id != ''
GROUP BY c.`user_id`
LIMIT 1;
  SELECT
	1
    INTO
    _is
FROM uzivatel a
JOIN user_tracking b ON (a.user_id = b.`user_id`)
JOIN user_tracking c ON ((c.`xforward` = b.`xforward` OR c.`persistent_id` = b.`persistent_id`) AND c.user_id != a.`user_id`)
JOIN `campaign_voucher` d ON (d.`user_id` = c.`user_id`)
WHERE
	a.user_id = inl_id_user AND
    (d.camp_id = inl_id_campaign OR inl_id_campaign IS NULL) AND
    b.xforward NOT IN  ('', '62.77.88.215') AND
    b.persistent_id != ''
GROUP BY c.`user_id`
LIMIT 1;


  RETURN _is;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `translate` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 FUNCTION `translate`(val_index_pole VARCHAR(200), val_lang_id SMALLINT(3)) RETURNS text CHARSET utf8
    DETERMINISTIC
BEGIN
  RETURN COALESCE((select text from preklady where lang_id=val_lang_id and index_pole=val_index_pole limit 1),val_index_pole);
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `BalanceMinus` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`127.0.0.1`*/ /*!50003 PROCEDURE `BalanceMinus`(uid int,b decimal(20,4),z decimal(20,2),d decimal(20,4))
    SQL SECURITY INVOKER
BEGIN
update uzivatel_im_data set zustatek=zustatek-b,zetony=zetony-z,dluh=dluh-d where user_id=uid;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `BalancePlus` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `BalancePlus`(uid int,b decimal(20,4),z decimal(20,2),d decimal(20,4))
BEGIN
update uzivatel_im_data set zustatek=zustatek+b,zetony=zetony+z,dluh=dluh+d where user_id=uid;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `BalanceSet` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `BalanceSet`(uid int,b decimal(20,4),z decimal(20,2),d decimal(20,4))
BEGIN
IF b=-1 and z=-1 and d!=-1 then
  update uzivatel_im_data set dluh=d where user_id=uid;
ELSEIF b=-1 and z!=-1 and d=-1 then
  update uzivatel_im_data set zetony=z where user_id=uid;
ELSEIF b!=-1 and z=-1 and d=-1 then
  update uzivatel_im_data set zustatek=b where user_id=uid;
ELSEIF b!=-1 and z!=-1 and d=-1 then
  update uzivatel_im_data set zustatek=b,zetony=z where user_id=uid;
ELSEIF b!=-1 and z=-1 and d!=-1 then
  update uzivatel_im_data set zustatek=b,dluh=d where user_id=uid;
ELSEIF b=-1 and z!=-1 and d!=-1 then
  update uzivatel_im_data set zetony=z,dluh=d where user_id=uid;
ELSEIF b!=-1 and z!=-1 and d != -1 then
  update uzivatel_im_data set zustatek=b,zetony=z,dluh=d where user_id=uid;
END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `betStat` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `betStat`(IN f VARCHAR(20), IN t VARCHAR(20), IN invalidUser VARCHAR(1000), OUT totalTicket INTEGER(11), OUT freeTicket INTEGER(11), OUT totalSum DECIMAL(12,2), OUT clearWin DECIMAL(12,2), OUT winnings DECIMAL(5,2), OUT ticketDay INTEGER(11), OUT betDay INTEGER, OUT activeUser INTEGER)
BEGIN
declare totalSumPaid decimal(12,2) default 0;
declare totalWinPaid decimal(12,2) default 0;
select COALESCE(count(ticket_id),0) into totalTicket  from ticket where
zalozen>=f and zalozen<=t and user_id not IN(invalidUser);
select COALESCE(count(b.user_id),0) into activeUser from (select a.user_id   from ticket a where
a.zalozen>=f and a.zalozen<=t  group by a.user_id) b;
select COALESCE(count(ticket_id),0) into freeTicket  from ticket where
zalozen>=f and zalozen<=t and free_bet_bonus=1;
select COALESCE(SUM(a.castka/e.kurz),0) into totalSum from ticket a
inner join uzivatel b on a.user_id=b.user_id
inner join  kurzmena e on b.mena_id=e.mena_id
where a.zalozen>=f and a.zalozen<=t and b.user_id not IN(invalidUser);
select COALESCE(SUM(a.castka/e.kurz),0) into totalSumPaid from ticket a
inner join uzivatel b on a.user_id=b.user_id
inner join  kurzmena e on b.mena_id=e.mena_id
where a.vyplacen=1 and a.zalozen>=f and a.zalozen<=t and b.user_id not IN(invalidUser);
select COALESCE(SUM(f.castka/e.kurz),0) into totalWinPaid from ticket a
inner join uzivatel b on a.user_id=b.user_id
inner join  kurzmena e on b.mena_id=e.mena_id
inner join vyherci_sazky f on a.ticket_id=f.ticket_id
where a.vyplacen=1 and a.zalozen>=f and a.zalozen<=t and b.user_id not IN(invalidUser);
set clearWin = totalSumPaid-totalWinPaid;
set winnings = ((totalWinPaid-totalSumPaid)/totalSumPaid);
set ticketDay = (totalTicket/30);
set betDay = (totalSum/30);
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `bonusStat` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `bonusStat`(IN f VARCHAR(20), IN t VARCHAR(20), OUT depositBonusSum INTEGER(11))
BEGIN
select COALESCE(SUM(a.bonus/e.kurz),0) into depositBonusSum from deposit_bonus_user a
inner join uzivatel b on a.user_id=b.user_id
inner join  kurzmena e on b.mena_id=e.mena_id
where a.date>=f and a.date<=t;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `depositStat` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `depositStat`(IN f VARCHAR(20), IN t VARCHAR(20), IN f2 INTEGER, IN t2 INTEGER, IN payNoUser VARCHAR(1000), OUT newNum INTEGER, OUT newDepositReg DECIMAL(5,2), OUT newSum DECIMAL(12,2), OUT totalNum INTEGER(11), OUT totalSum DECIMAL(12,2))
BEGIN
select COALESCE(count(acctrans_id),0) into newNum from accTrans a   inner join uzivatel c on a.acctrans_USR=c.user_id
where  c.user_id not in(payNoUser) and a.acctrans_sign=1
and accTrans_T>f2 and accTrans_T<t2 and c.datum_registrace>=f and c.datum_registrace<=t;
select COALESCE(count(acctrans_id),0) into totalNum from accTrans a   inner join uzivatel c on a.acctrans_USR=c.user_id
where  c.user_id not in(payNoUser) and a.acctrans_sign=1
and accTrans_T>f2 and accTrans_T<t2;
call numReg(f,t,@num);
set newDepositReg = COALESCE(((newNum/@num)*100),0);
select COALESCE(SUM((a.acctrans_amount/e.kurz)),0) into newSum from accTrans a   inner join uzivatel c
on a.acctrans_USR=c.user_id inner join  kurzmena e on a.acctrans_CUR=e.mena_id
where  e.platny_od<=now() and e.platny_do>=now() and c.user_id not in(payNoUser) and a.acctrans_sign=1
and accTrans_T>f2 and accTrans_T<t2 and c.datum_registrace>=f and c.datum_registrace<=t;
select COALESCE(SUM((a.acctrans_amount/e.kurz)),0) into totalSum from accTrans a   inner join uzivatel c
on a.acctrans_USR=c.user_id inner join  kurzmena e on a.acctrans_CUR=e.mena_id
where  e.platny_od<=now() and e.platny_do>=now() and c.user_id not in(payNoUser) and a.acctrans_sign=1
and accTrans_T>f2 and accTrans_T<t2;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `fn_sync_preklady_search` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `fn_sync_preklady_search`()
BEGIN
	DELETE FROM preklady_search;
	INSERT INTO preklady_search(preklad_id,lang_id,text) SELECT preklad_id,lang_id,text FROM preklady;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `fn_sync_sazky_search` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = latin1 */ ;
/*!50003 SET character_set_results = latin1 */ ;
/*!50003 SET collation_connection  = latin1_swedish_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `fn_sync_sazky_search`()
BEGIN
	DELETE FROM sazky_search;
	INSERT INTO sazky_search(sazka_id,sazka_text) SELECT sazka_id,`text` FROM sazky;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `new_proc` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `new_proc`()
BEGIN
  DECLARE done INT DEFAULT 0;
  DECLARE a text;
  DECLARE i int;
  DECLARE cur1 CURSOR FOR select index_pole from preklady group by index_pole;
  DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;

  OPEN cur1;
  REPEAT
    FETCH cur1 INTO a;
    IF NOT done THEN

      if exists(select preklad_id from preklady where index_pole=a and preklad_id=0) then

      set i = (select max(x.preklad_id) from preklady x);

       update preklady set preklad_id=(i+1) where index_pole=a;

      end if;

    END IF;
  UNTIL done END REPEAT;
  CLOSE cur1;

END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `new_proc0` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `new_proc0`()
BEGIN
declare v1 int;
declare p VARCHAR(100);
set v1 = 0;
drop table if exists ttt;
create temporary table ttt(
 x varchar(100) not null
);
while v1<15000 do
    set p =substr(fn_get_uniq_hash(),2,10);
    insert into ttt values(p);
    set v1 = v1 + 1;
end while;
select * from ttt;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `numReg` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `numReg`(IN f VARCHAR(20), IN t VARCHAR(20), OUT num INTEGER(11))
BEGIN
 select COALESCE(count(user_id),0) into num from uzivatel where datum_registrace>=f and datum_registrace<=t;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `withdrawStat` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `withdrawStat`(IN f VARCHAR(20), IN t VARCHAR(20), IN f2 INTEGER(11), IN t2 INTEGER(11), OUT totalSum DECIMAL(12,2))
BEGIN
select SUM((a.acctrans_amount/e.kurz)) into totalSum from accTrans a   inner join uzivatel c
on a.acctrans_USR=c.user_id inner join  kurzmena e on a.acctrans_CUR=e.mena_id
where  e.platny_od<=now() and e.platny_do>=now()  and a.acctrans_sign=2
and accTrans_T>f2 and accTrans_T<t2;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `wi_af_user__uzivatel` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `wi_af_user__uzivatel`(IN inl_id_uzivatel INTEGER(11), IN inl_id_af_user INTEGER(11))
BEGIN
	INSERT INTO `t_rel_af_user__uzivatel` (nl_id_uzivatel, nl_id_af_user)
    	VALUES (inl_id_uzivatel, inl_id_af_user);
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `wi_napoveda_menu_move` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `wi_napoveda_menu_move`(IN inl_id_menu_napoveda INTEGER(11), IN is_retezec VARCHAR(255))
BEGIN
	DECLARE _hodnota VARCHAR(80);
	LABEL: WHILE TRUE DO
    	SET is_retezec = TRIM(BOTH ';' FROM is_retezec);
	    IF(COALESCE(is_retezec, '') = '') THEN
        	LEAVE LABEL;
        END IF;

     	SET _hodnota = SUBSTRING_INDEX(is_retezec, ';', 1);
        SET is_retezec = SUBSTR(is_retezec, LENGTH(_hodnota)+1);

        UPDATE napoveda_menu d
    	JOIN napoveda_menu e ON (e.`parent_id` = CAST(_hodnota AS DECIMAL(10, 0))
    		AND e.lang_id = d.lang_id
            AND e.poradi = d.poradi) SET
    		e.`index_pole` = d.`index_pole`,
    		e.`movie` = d.`movie`,
    		e.`poradi` =d.`poradi`,
    		e.`text` = d.`text`,
    		e.`type` = d.`type`,
    		e.`url` = d.`url`,
    		e.`zobrazeno` = d.`zobrazeno`
    	WHERE d.parent_id = inl_id_menu_napoveda;

	END WHILE LABEL;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `wi_uzivatel_poznamka` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `wi_uzivatel_poznamka`(IN inl_id_uzivatel INTEGER(11), IN inl_id_admin_login INTEGER(11), IN is_note VARCHAR(255))
BEGIN
	INSERT INTO uzivatel_poznamka (user_id, text, admin_id, datum)
    	VALUES (inl_id_uzivatel, is_note, inl_id_admin_login, NOW());

    SELECT
    	a.`admin_id` AS nl_id_admin,
        a.`text` AS s_poznamka,
        a.`nl_id` AS nl_id_uzivatel_poznamka
	FROM uzivatel_poznamka a
    WHERE
    	a.`nl_id` = LAST_INSERT_ID();
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `ws_bonus_ip_shoda` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `ws_bonus_ip_shoda`()
BEGIN
	SELECT
	b.`user_id` AS nl_id_user,
    b.`email` AS s_email,
    c.`xforward` AS s_ip,
    COALESCE(`fn_get_money_uzivatel2euro`(a.`user_id`, (f.`acctrans_amount`)), 0) AS nd_vlozil,
    d.`ts` AS dt_ip_shoda,
    e.`title` AS s_bonus,
    a.`chips_added` AS dt_bonus_ziskani,
    g.user_id AS nl_id_user_shoda,
    g.`email` AS s_email_shoda
FROM user_campaign_chips a
JOIN uzivatel b ON (b.user_id = a.user_id)
JOIN user_tracking c ON (c.user_id = b.user_id)
JOIN user_tracking d ON (d.`user_id` != b.`user_id` AND d.`xforward` = c.`xforward`)
JOIN campaign e ON (e.`camp_id` = a.`campaign_id`)
LEFT JOIN `accTrans` f ON (f.`acctrans_USR` = b.`user_id`)
JOIN uzivatel g ON (g.user_id = d.user_id)
WHERE
	c.`xforward` NOT IN ( '62.77.88.215', '') AND
    d.`xforward` NOT IN ( '62.77.88.215', '') AND
    b.`e_testovaci` = 'ne' AND
    g.`e_testovaci` = 'ne'
GROUP BY a.`user_id`, d.`user_id`, f.acctrans_USR;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `ws_bonus_pc_shoda` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `ws_bonus_pc_shoda`()
BEGIN
SELECT
	b.`user_id` AS nl_id_user,
    b.`email` AS s_email,
    c.`xforward` AS s_ip,
    COALESCE(`fn_get_money_uzivatel2euro`(a.`user_id`, (f.`acctrans_amount`)), 0) AS nd_vlozil,
    d.`ts` AS dt_ip_shoda,
    e.`title` AS s_bonus,
    a.`chips_added` AS dt_bonus_ziskani,
    g.user_id AS nl_id_user_shoda,
    g.`email` AS s_email_shoda
FROM user_campaign_chips a
JOIN uzivatel b ON (b.user_id = a.user_id)
JOIN user_tracking c ON (c.user_id = b.user_id)
JOIN user_tracking d ON (d.`user_id` != b.`user_id` AND d.`persistent_id` = c.`persistent_id`)
JOIN campaign e ON (e.`camp_id` = a.`campaign_id`)
LEFT JOIN `accTrans` f ON (f.`acctrans_USR` = b.`user_id`)
JOIN uzivatel g ON (g.user_id = d.user_id)
WHERE
	c.`xforward` NOT IN ( '62.77.88.215', '') AND
    d.`xforward` NOT IN ( '62.77.88.215', '') AND
    b.`e_testovaci` = 'ne' AND
    g.`e_testovaci` = 'ne'
GROUP BY a.`user_id`, d.`user_id`, f.acctrans_USR;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `ws_cis_email_dokument_odpoved` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `ws_cis_email_dokument_odpoved`(IN is_tag VARCHAR(45), IN inl_id_jazyk INTEGER(11))
BEGIN
	SELECT
    	a.`s_name`,
        a.`s_id_preklad`,
        b.`text` AS s_preklad,
        c.`text` AS s_subject
    FROM t_list_email_dokument_odpoved a
    JOIN preklady b ON (a.`s_id_preklad` = b.`index_pole`)
    JOIN preklady c ON (c.`lang_id` = b.`lang_id` AND c.`index_pole` = 'doc_mes_sub')
    WHERE
    	(is_tag IS NULL OR is_tag = b.`index_pole`) AND
        b.`lang_id` = COALESCE(inl_id_jazyk, 2);
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `ws_cis_jazyk` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `ws_cis_jazyk`()
BEGIN
	SELECT
    	a.`iso` AS s_nazev,
        a.`lang_id` AS nl_id_jazyk
    FROM jazyky a
    WHERE a.`zobrazeno` = 1;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `ws_cis_kampan` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `ws_cis_kampan`()
BEGIN
	SELECT
    	a.`camp_id` AS nl_id_kampan,
        a.`title` AS s_nazev
    FROM campaign a;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `ws_cis_poznamka_template` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `ws_cis_poznamka_template`()
BEGIN
	SELECT
    	a.`predmet` AS s_predmet,
        a.`telo` AS s_telo
    FROM `poznamky_template` a;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `ws_cis_zeme` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `ws_cis_zeme`()
BEGIN
	SELECT
    	a.`kod` AS s_nazev,
        a.`zeme_id` AS nl_id_zeme
    FROM zeme a
    WHERE a.`zeme_allow` = 1;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `ws_core_parameters` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `ws_core_parameters`(IN is_proc_name CHAR(64), IN is_db_name CHAR(64))
BEGIN
    DECLARE _paramList VARCHAR(800);
    DECLARE _parameter VARCHAR(100);
    SELECT
        CAST(a.param_list AS CHAR(800))
    INTO
        _paramList
    FROM mysql.proc a
    WHERE a.name = is_proc_name
        AND a.db = is_db_name
    LIMIT 1;
    CREATE TEMPORARY TABLE system_proc.__parameters ( s_parameter VARCHAR(20) ) TYPE=HEAP;
    label: WHILE TRUE DO
        SET _paramList = TRIM(_paramList);
        IF(COALESCE(_paramList, '') = '') THEN
            LEAVE label;
        END IF;
        SET _parameter = SUBSTRING_INDEX(_paramList, ',', 1);
        SET _paramList = SUBSTR(_paramList, LENGTH(_parameter)+2);
        INSERT INTO system_proc.__parameters (s_parameter)
            VALUES (
                TRIM(
                    SUBSTRING_INDEX(
                        SUBSTR(_parameter,
                            LENGTH(
                                SUBSTRING_INDEX(_parameter, ' ', 1))+2), ' ', 1)));
    END WHILE label;
    SELECT
        SUBSTR(s_parameter, 1-(
            LENGTH(s_parameter))) AS s_parameter
    FROM system_proc.__parameters WHERE s_parameter != '';
    DROP TABLE system_proc.__parameters;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `ws_export_email_vision` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `ws_export_email_vision`(IN inl_stranka INTEGER(11), IN inl_offset INTEGER(11), IN inl_id_user INTEGER(11), IN ib_last_day TINYINT(1), IN inl_id_user_od INTEGER(11), IN inl_id_user_do INTEGER(11))
BEGIN
	SET inl_offset = COALESCE(inl_offset, (SELECT COUNT(a.user_id) FROM uzivatel a));
    SET inl_stranka = COALESCE(inl_stranka, 1);
PREPARE STMT FROM "
	SELECT
    (SELECT
	`fn_get_money_uzivatel2euro`(xa.`acctrans_USR`, SUM(xa.`acctrans_amount`))
FROM accTrans xa
WHERE xa.`acctrans_sign` = 1 AND xa.`acctrans_USR` = a.user_id) AS nd_vklad,
    (SELECT
DATE(MAX(xa.dt_last))
FROM `t_log_casgam_activity` xa
WHERE xa.nl_id_user = a.user_id AND xa.b_for_real = 1) AS d_posledni_for_fun,
	a.user_id AS nl_id_user,
	CASE WHEN a.`pohlavi` = 'm' THEN 0 ELSE 1 END AS nl_pohlavi,
    CASE WHEN DATE(a.`datum_narozeni`) = '0000-00-00' THEN NULL ELSE DATE(a.`datum_narozeni`) END AS d_narozeni,
    c.`text` AS s_zeme,
    a.`jmeno` AS s_jmeno,
    a.`prijmeni` AS s_prijmeni,
    a.`email` AS s_email,
    a.`telefon` AS s_telefon,
    CASE WHEN DATE(a.`datum_registrace`) = '0000-00-00' THEN NULL ELSE DATE(a.`datum_registrace`) END  AS d_registrace,
    CASE WHEN DATE(a.`posledni_prihlaseni`) = '0000-00-00' THEN NULL ELSE DATE(a.`posledni_prihlaseni`) END AS d_posledni_prihlaseni,
    (SELECT DATE(FROM_UNIXTIME(xa.`acctrans_T`)) FROM accTrans xa
		WHERE xa.`acctrans_USR` = a.user_id AND xa.`acctrans_sign` = 1
		ORDER BY xa.`acctrans_ID` DESC LIMIT 1) AS d_posledni_vklad,
    COALESCE((SELECT 1 FROM ticket xa
		WHERE xa.`user_id` = a.`user_id` LIMIT 1), 0) AS b_sportovni_sazky,
    CASE WHEN COALESCE((SELECT
			SUM(xa.`pocet`)
	FROM zetony xa
	WHERE xa.`user_id` = a.user_id AND xa.`nakup` = 1), 0) != d.zetony THEN 1 ELSE 0 END b_kasino,
    (SELECT COUNT(*)
		FROM accTrans xa
		WHERE xa.`acctrans_USR` = a.user_id AND xa.`acctrans_sign` = 1) AS nl_vkladu,
    (SELECT DATE(FROM_UNIXTIME(xa.`acctrans_T`)) FROM accTrans xa
		WHERE xa.`acctrans_USR` = a.user_id AND xa.`acctrans_sign` = 2
		ORDER BY xa.`acctrans_ID` DESC LIMIT 1) AS d_posledni_vyber,
    `fn_get_money_uzivatel2euro`(a.user_id, d.zustatek) + d.zetony AS nd_zustatek,
    (SELECT `fn_get_money_uzivatel2euro`(a.user_id, SUM(xa.`castka`)) FROM ticket xa
    	WHERE xa.`user_id` = a.user_id) AS nd_protoceno,
    (SELECT DATE(xa.`zalozen`)
		FROM ticket xa WHERE xa.`user_id` = a.user_id
        ORDER BY xa.`ticket_id` DESC LIMIT 1) AS d_posledni_tiket,
    (SELECT DATE(xa.`zalozen`) FROM ticket xa
		JOIN ticket_kurz xb ON (xa.`ticket_id` = xb.`ticket_id`)
		JOIN sazky xc ON (xc.sazka_id = xb.sazka_id)
		JOIN udalost xd ON (xd.`udalost_id` = xc.`udalost_id`)
		WHERE xa.`user_id` = a.user_id AND xd.`sport_id` = 1001
        ORDER BY xa.`zalozen` DESC LIMIT 1) AS d_vsadil_fotbal,
    (SELECT DATE(xa.`zalozen`) FROM ticket xa
		JOIN ticket_kurz xb ON (xa.`ticket_id` = xb.`ticket_id`)
		JOIN sazky xc ON (xc.sazka_id = xb.sazka_id)
		JOIN udalost xd ON (xd.`udalost_id` = xc.`udalost_id`)
		WHERE xa.`user_id` = a.user_id AND xd.`sport_id` = 1003
        ORDER BY xa.`zalozen` DESC LIMIT 1) AS d_vsadil_tenis,
    (SELECT DATE(xa.`zalozen`) FROM ticket xa
		JOIN ticket_kurz xb ON (xa.`ticket_id` = xb.`ticket_id`)
		JOIN sazky xc ON (xc.sazka_id = xb.sazka_id)
		JOIN udalost xd ON (xd.`udalost_id` = xc.`udalost_id`)
		WHERE xa.`user_id` = a.user_id AND xd.`sport_id` = 1011
        ORDER BY xa.`zalozen` DESC LIMIT 1) AS d_vsadil_hokej,
    (SELECT DATE(xa.`zalozen`) FROM ticket xa
		JOIN ticket_kurz xb ON (xa.`ticket_id` = xb.`ticket_id`)
		JOIN sazky xc ON (xc.sazka_id = xb.sazka_id)
		JOIN udalost xd ON (xd.`udalost_id` = xc.`udalost_id`)
		WHERE xa.`user_id` = a.user_id AND xd.`sport_id` = 1020
        ORDER BY xa.`zalozen` DESC LIMIT 1) AS d_vsadil_hazena,
	(SELECT DATE(xa.`zalozen`) FROM ticket xa
		JOIN ticket_kurz xb ON (xa.`ticket_id` = xb.`ticket_id`)
		JOIN sazky xc ON (xc.sazka_id = xb.sazka_id)
		JOIN udalost xd ON (xd.`udalost_id` = xc.`udalost_id`)
		WHERE xa.`user_id` = a.user_id AND xd.`sport_id` = 1006
        ORDER BY xa.`zalozen` DESC LIMIT 1) AS d_vsadil_basketbal,
    (SELECT DATE(xa.`zalozen`) FROM ticket xa
		JOIN ticket_kurz xb ON (xa.`ticket_id` = xb.`ticket_id`)
		JOIN sazky xc ON (xc.sazka_id = xb.sazka_id)
		JOIN udalost xd ON (xd.`udalost_id` = xc.`udalost_id`)
		WHERE xa.`user_id` = a.user_id AND xd.`sport_id` = 1029
        ORDER BY xa.`zalozen` DESC LIMIT 1) AS d_vsadil_formule,
	(SELECT DATE(xa.`zalozen`) FROM ticket xa
		JOIN ticket_kurz xb ON (xa.`ticket_id` = xb.`ticket_id`)
		JOIN sazky xc ON (xc.sazka_id = xb.sazka_id)
		JOIN udalost xd ON (xd.`udalost_id` = xc.`udalost_id`)
		WHERE xa.`user_id` = a.user_id AND xd.`sport_id` NOT IN (1001, 1003, 1011, 1020, 1006, 1029)
        ORDER BY xa.`zalozen` DESC LIMIT 1) AS d_vsadil_jiny,
    COALESCE((SELECT 1 FROM deposit_bonus_user xa
		WHERE xa.`user_id` = a.user_id AND xa.`accept` = 1 LIMIT 1), 0) AS b_deposit_bonus,
    COALESCE((SELECT 1 FROM bonus_user xa
    	WHERE xa.`user_id` = a.user_id AND xa.`bonus_id` IN (5, 6) AND xa.vybrano = 1
        LIMIT 1), 0) AS b_kasino_bonus,
    CASE WHEN a.zakazany > 0 THEN 1 ELSE 0 END b_zakazny,
    (SELECT DATE(FROM_UNIXTIME(xa.`acctrans_T`)) FROM `accTrans` xa
		WHERE xa.`acctrans_USR` = a.user_id AND xa.`acctrans_sign` = 1
		ORDER BY xa.`acctrans_ID` ASC
		LIMIT 0, 1) AS d_vklad_1,
    (SELECT DATE(FROM_UNIXTIME(xa.`acctrans_T`)) FROM `accTrans` xa
		WHERE xa.`acctrans_USR` = a.user_id AND xa.`acctrans_sign` = 1
		ORDER BY xa.`acctrans_ID` ASC
		LIMIT 1, 1) AS d_vklad_2,
    (SELECT DATE(FROM_UNIXTIME(xa.`acctrans_T`)) FROM `accTrans` xa
		WHERE xa.`acctrans_USR` = a.user_id AND xa.`acctrans_sign` = 1
		ORDER BY xa.`acctrans_ID` ASC
		LIMIT 2, 1) AS d_vklad_3,
    CASE WHEN a.vyhernost >= 100
    	THEN a.vyhernost ELSE NULL END AS nd_vyhernost_sazky_vyssi,
    CASE WHEN a.vyhernost < 100
    	THEN a.vyhernost ELSE NULL END AS nd_vyhernost_sazky_nizsi
FROM uzivatel a
JOIN zeme b ON (b.`zeme_id` = a.`zeme_id`)
JOIN preklady c ON (c.`index_pole` = b.`nazev` AND c.`lang_id` = 2)
JOIN uzivatel_im_data d ON (a.user_id = d.user_id)
WHERE
    (? IS NULL OR a.user_id = ?) AND
    a.e_testovaci = 'ne' AND
    (COALESCE(?, 0) = 0 OR (a.posledni_prihlaseni >= NOW() - INTERVAL 1 DAY)) AND
    (? IS NULL OR a.user_id >= ?) AND
    (? IS NULL OR a.user_id <= ?)
ORDER BY a.user_id ASC
LIMIT ? , ?";
SET @a = inl_offset * (inl_stranka - 1);
SET @b = inl_offset;
SET @c = inl_id_user;
SET @d = ib_last_day;
SET @e = inl_id_user_od;
SET @f = inl_id_user_do;
 EXECUTE STMT USING @c, @c, @d, @e, @e, @f, @f, @a, @b;
 DROP PREPARE STMT;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `ws_hra` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `ws_hra`()
BEGIN
	SELECT
    	a.`table_name` AS s_tabulka
    FROM hry a;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `ws_login_uzivatel` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `ws_login_uzivatel`(IN is_username VARCHAR(20), IN is_password char(32))
BEGIN
    SELECT
        a.jmeno AS s_jmeno,
        a.prijmeni AS s_prijmeni,
        a.nick AS s_nick,
        a.email AS s_email
    FROM uzivatel a
    WHERE a.nick = is_username
        AND a.heslo = is_password;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `ws_platebni_metoda_posledni_pouziti` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `ws_platebni_metoda_posledni_pouziti`()
BEGIN
SELECT
FROM_UNIXTIME(MAX(a.acctrans_T)) AS dt_posledni_pouziti,
b.`mena_text` AS s_mena,
d.text AS s_metoda,
CASE WHEN a.`acctrans_sign` = 1 THEN 'deposit' ELSE 'withdraw' END AS s_smer
FROM accTrans a
JOIN mena b ON (a.`acctrans_CUR` = b.`mena_id`)
JOIN method c ON (c.`met_sign` = a.`acctrans_sign` AND c.`met_namex` = a.`acctrans_met`)
JOIN (SELECT * FROM preklady a
		WHERE
			a.`index_pole` LIKE 'pay_method%' AND
    		a.`lang_id` = 1) d ON (d.index_pole = CONCAT('pay_method_', c.`met_namex`))
JOIN uzivatel e ON (e.user_id = a.`acctrans_USR`)
WHERE e.`e_testovaci` = 'ne'
GROUP BY a.`acctrans_CUR`, a.`acctrans_met`, a.`acctrans_sign`
ORDER BY a.`acctrans_sign` ASC,  1 ASC;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `ws_statistika_aff_muze_vybrat` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `ws_statistika_aff_muze_vybrat`()
BEGIN
SELECT
	SUM(b.castka) AS nd_castka,
    CONCAT(a.`jmeno`, ' ', a.prijmeni) AS s_jmeno,
	a.`af_user_id` AS nl_id_af_user
FROM affiliate.af_user a
JOIN affiliate.`af_payment` b ON (a.`af_user_id` = b.`af_user_id`)
WHERE b.`status` = 0
GROUP BY a.`af_user_id`
HAVING SUM(b.castka) >= 50;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `ws_statistika_aff_platby` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `ws_statistika_aff_platby`()
BEGIN
SELECT
	CASE WHEN a.status = 0 THEN 'non paid' ELSE 'paid' END AS s_status,
	YEAR(a.datum) AS nl_rok,
    MONTH(a.datum) AS nl_mesic,
	SUM(a.castka) AS nd_suma
FROM affiliate.af_payment a
GROUP BY a.`status`, YEAR(a.datum), MONTH(a.datum)
ORDER BY 2 DESC, 3 DESC;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `ws_statistika_bonus_celkovy` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `ws_statistika_bonus_celkovy`()
BEGIN
	SELECT
	SUM(b.bonus_amount) AS nd_amount,
    a.`title` AS s_title
FROM campaign a
JOIN campaign_voucher  b ON (a.`camp_id` = b.`camp_id`)
WHERE a.`camp_type` = 'cg'
GROUP BY a.`camp_type`
UNION
SELECT
	`fn_get_money_uzivatel2euro`(b.user_id, SUM(b.bonus_amount)),
    a.`title`
FROM campaign a
JOIN campaign_voucher  b ON (a.`camp_id` = b.`camp_id`)
WHERE a.`camp_type` = 'sb'
 GROUP BY a.`camp_type`
UNION
 SELECT
	SUM(b.chip_amout),
    a.`title`
FROM campaign a
JOIN user_campaign_chips  b ON (a.`camp_id` = b.`campaign_id`)
WHERE a.`camp_type` = 'cc'
GROUP BY a.`camp_type`
UNION
 SELECT
	`fn_get_money_uzivatel2euro`(b.user_id, SUM(b.chip_amout)),
    a.`title`
FROM campaign a
JOIN user_campaign_chips  b ON (a.`camp_id` = b.`campaign_id`)
WHERE a.`camp_type` = 'om'
GROUP BY a.`camp_type`;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `ws_statistika_bonus_protoceni` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `ws_statistika_bonus_protoceni`(IN inl_id_user INTEGER(11), IN is_nick VARCHAR(20), IN is_email VARCHAR(255), IN ib_protoceno TINYINT(1), IN inl_id_jazyk INTEGER(11), IN inl_id_zeme INTEGER(11), IN id_ziskano_od DATE, IN id_ziskano_do DATE, IN inl_id_kampan INTEGER(11))
BEGIN
SELECT
a.`email` AS s_email,
a.`nick` AS s_nick,
a.`user_id` AS nl_id_user,
`fn_get_money_uzivatel2euro`(a.user_id, (SELECT SUM(acctrans_amount) FROM accTrans WHERE a.user_id = acctrans_USR AND acctrans_sign = 1)) AS nd_vlozeno_eur,
d.`title` AS s_nazev_kampan,
e.`zetony` AS nd_zetony,
`fn_get_money_uzivatel2euro`(a.user_id, e.`zustatek`) AS nd_zustatek_eur,
`fn_get_money_uzivatel2euro`(a.user_id,
(SELECT SUM(castka) FROM ticket
WHERE user_id = a.user_id AND vyplacen = 0)) AS nd_v_tiketech_eur,
CASE WHEN b.`chip_amout` * b.`turn_count` > b.`chip_turned` THEN 0 ELSE 1 END AS b_protoceno,
g.`kod` AS s_kod_zeme,
b.`chips_added` AS dt_ziskano,
`fn_get_money_uzivatel2euro`(a.user_id, b.`chip_turned`) AS nd_turned,
`fn_get_money_uzivatel2euro`(a.user_id, b.`chip_amout` * b.`turn_count`) AS nd_musi_protocit,
h.`alt_text` AS s_jazyk
FROM uzivatel a
JOIN `user_campaign_chips` b ON (a.`user_id` = b.user_id)
JOIN `campaign` d ON (d.`camp_id` = b.`campaign_id`)
JOIN uzivatel_im_data e ON (e.user_id = a.user_id)
JOIN zeme g ON (g.`zeme_id` = a.`zeme_id`)
JOIN jazyky h ON (h.`lang_id` = a.`lang_id`)
WHERE a.`e_testovaci` = 'ne'
	AND (inl_id_user IS NULL OR inl_id_user = a.user_id)
    AND (is_nick IS NULL OR is_nick LIKE a.`nick`)
    AND (is_email IS NULL OR is_email LIKE a.`email`)
    AND (ib_protoceno IS NULL OR CASE WHEN b.`chip_amout` * b.`turn_count` > b.`chip_turned` THEN 0 ELSE 1 END = ib_protoceno)
    AND (inl_id_jazyk IS NULL OR inl_id_jazyk = a.`lang_id`)
    AND (inl_id_zeme IS NULL OR inl_id_zeme = a.`zeme_id`)
    AND (id_ziskano_od IS NULL OR id_ziskano_od <= DATE(b.`chips_added`))
    AND (id_ziskano_do IS NULL OR id_ziskano_do >= DATE(b.`chips_added`))
    AND (inl_id_kampan IS NULL OR inl_id_kampan = d.`camp_id`)
GROUP BY b.`campaign_id`, a.`user_id`
ORDER BY 4 DESC;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `ws_statistika_friendship_bonus` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `ws_statistika_friendship_bonus`()
BEGIN
SELECT
	`fn_get_money_uzivatel2euro`(a.`user_id`, SUM(a.`bonus_castka`)) AS nd_bonus,
    COUNT(a.user_id) AS nl_bonus_pocet,
    b.`user_id` AS nl_id_user,
    b.`nick` AS s_nick,
    `fn_get_money_uzivatel2euro`(a.`user_id`, c.`zustatek`) AS nd_zustatek,
    c.`zetony` AS nd_zetony
FROM ticket_bonus_uzivatel a
JOIN uzivatel b ON (a.`user_id` = b.`user_id`)
JOIN uzivatel_im_data c ON (c.user_id  = b.user_id)
WHERE
	(a.`kod` LIKE 'fsb%' OR a.`kod` LIKE 'fbs%') AND
    b.`e_testovaci` = 'ne'
GROUP BY a.`user_id`;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `ws_statistika_happy_hours` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `ws_statistika_happy_hours`(IN inl_id_user INTEGER(11), IN id_od DATE, IN id_do DATE, IN inl_id_zeme INTEGER(11))
BEGIN
	SELECT
    	a.od AS dt_od,
        a.do AS dt_do,
       SUM(`fn_get_money_uzivatel2euro`(b.user_id, b.amount)) AS nd_amount
    FROM happy_hours a
    JOIN happy_hours_user b ON (a.h_id = b.h_id)
    JOIN uzivatel c ON (c.user_id = b.user_id)
    WHERE
    	(inl_id_user IS NULL OR inl_id_user = b.user_id) AND
        (id_od IS NULL OR id_od <= b.date) AND
        (id_do IS NULL OR id_do >= b.date) AND
        (inl_id_zeme IS NULL OR inl_id_zeme = c.zeme_id)
    GROUP BY a.h_id
    ORDER BY a.h_id DESC;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `ws_statistika_transakce` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `ws_statistika_transakce`()
BEGIN
SELECT
YEAR(FROM_UNIXTIME(a.`acctrans_T`)) AS nl_rok,
MONTH(FROM_UNIXTIME(a.`acctrans_T`)) AS nl_mesic,
CASE WHEN a.acctrans_sign = 1 THEN 'deposit' ELSE 'withdraw' END AS  s_smer,
c.`kod` AS s_zeme,
`fn_get_money_uzivatel2euro`(a.`acctrans_USR`, SUM(a.`acctrans_amount`)) AS nd_castka
FROM accTrans a
JOIN uzivatel b ON (a.`acctrans_USR` = b.`user_id`)
JOIN zeme c ON (c.zeme_id = b.`zeme_id`)
WHERE b.`e_testovaci` = 'ne'
GROUP BY b.`zeme_id`, a.acctrans_sign, 1, 2
ORDER BY 1 DESC, 2 ASC, 4 ASC, 3 ASC;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `ws_statistika_vklady` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `ws_statistika_vklady`()
BEGIN
SELECT
d.text AS s_metoda,
CASE WHEN a.`acctrans_sign` = 1 THEN 'deposit' ELSE 'withdraw' END AS s_smer,
`fn_get_money_uzivatel2euro`(a.`acctrans_USR`, SUM(a.`acctrans_amount`)) AS nd_castka,
COUNT(a.`acctrans_met`) AS nl_pocet
FROM accTrans a
JOIN method c ON (c.`met_sign` = a.`acctrans_sign` AND c.`met_namex` = a.`acctrans_met`)
JOIN (SELECT * FROM preklady a
		WHERE
			a.`index_pole` LIKE 'pay_method%' AND
    		a.`lang_id` = 1) d ON (d.index_pole = CONCAT('pay_method_', c.`met_namex`))
JOIN uzivatel e ON (e.user_id = a.`acctrans_USR`)
WHERE
	e.`e_testovaci` = 'ne' AND
    a.`acctrans_sign` = 1
GROUP BY a.`acctrans_met`;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `ws_statistika_vklady_mesic` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `ws_statistika_vklady_mesic`()
BEGIN
SELECT
YEAR(FROM_UNIXTIME(a.`acctrans_T`)) AS nl_rok,
MONTH(FROM_UNIXTIME(a.`acctrans_T`)) AS nl_mesic,
d.text AS s_metoda,
CASE WHEN a.`acctrans_sign` = 1 THEN 'deposit' ELSE 'withdraw' END AS s_smer,
`fn_get_money_uzivatel2euro`(a.`acctrans_USR`, SUM(a.`acctrans_amount`)) AS nd_castka,
COUNT(a.`acctrans_met`) AS nl_pocet
FROM accTrans a
JOIN method c ON (c.`met_sign` = a.`acctrans_sign` AND c.`met_namex` = a.`acctrans_met`)
JOIN (SELECT * FROM preklady a
		WHERE
			a.`index_pole` LIKE 'pay_method%' AND
    		a.`lang_id` = 1) d ON (d.index_pole = CONCAT('pay_method_', c.`met_namex`))
JOIN uzivatel e ON (e.user_id = a.`acctrans_USR`)
WHERE
	e.`e_testovaci` = 'ne' AND
    a.`acctrans_sign` = 1
GROUP BY YEAR(FROM_UNIXTIME(a.`acctrans_T`)), MONTH(FROM_UNIXTIME(a.`acctrans_T`)), a.`acctrans_met`
ORDER BY 1 DESC, 2 DESC;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `ws_statistika_vyherci_sazky` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `ws_statistika_vyherci_sazky`()
BEGIN
SELECT
	YEAR(a.`datum`) AS nl_rok,
    MONTH(a.datum) AS nl_mesic,
    d.`kod` AS s_zeme,
    `fn_get_money_uzivatel2euro`(a.`user_id`, SUM(a.`castka` - c.`castka`)) AS nd_castka,
    COUNT(c.ticket_id) AS nl_pocet_tiketu
FROM `vyherci_sazky` a
JOIN uzivatel b ON (a.`user_id` = b.`user_id`)
JOIN ticket c ON (a.`ticket_id` = c.`ticket_id`)
JOIN zeme d ON (d.zeme_id = b.`zeme_id`)
GROUP BY b.`zeme_id`, 1, 2;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `ws_test` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `ws_test`()
BEGIN
    DECLARE _vysledek, _cisloPart VARCHAR(45) DEFAULT '';
    DECLARE _done, _sazkaId INT DEFAULT 0;
    DECLARE cur1 CURSOR FOR
        SELECT
            c.sazka_id,
            c.vysledek
        FROM ticket a
        JOIN ticket_kurz b ON (b.ticket_id = a.ticket_id)
        JOIN sazky c ON (c.sazka_id = b.sazka_id)
        JOIN typ d ON (d.typ_id = c.typ_id)
        JOIN podtyp e ON (e.podtyp_id = c.podtyp_id)
        JOIN podtyp_sloupce f ON (f.podtyp_id = e.podtyp_id
            AND f.sloupec_id = b.sloupec_id)
        WHERE a.ticket_id = 100433 AND a.user_id = 20;
    DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET _done = 1;
    CREATE TEMPORARY TABLE __vysledky (
                nl_id_vysledek INT(11),
                nl_id_sazka INT(11)
    ) TYPE=HEAP;
    OPEN cur1;
      REPEAT
        FETCH cur1 INTO _sazkaId, _vysledek;
        label: WHILE TRUE DO
            SET _vysledek = TRIM(BOTH ';' FROM _vysledek);
            IF(COALESCE(_vysledek, '') = '') THEN
                LEAVE label;
            END IF;
            SET _cisloPart = SUBSTRING_INDEX(_vysledek, ';', 1);
            SET _vysledek = SUBSTR(_vysledek, LENGTH(_cisloPart)+1);
            INSERT INTO __vysledky (nl_id_vysledek, nl_id_sazka)
                VALUES (CAST(_cisloPart AS DECIMAL(8,0)), _sazkaId);
        END WHILE label;
      UNTIL _done END REPEAT;
    CLOSE cur1;
    SELECT
        CASE f.nazev WHEN g.nazev THEN 'ano' ELSE 'ne' END AS s_vyhra
    FROM ticket a
    JOIN ticket_kurz b ON (b.ticket_id = a.ticket_id)
    JOIN sazky c ON (c.sazka_id = b.sazka_id)
    JOIN typ d ON (d.typ_id = c.typ_id)
    JOIN podtyp e ON (e.podtyp_id = c.podtyp_id)
    JOIN podtyp_sloupce f ON (f.podtyp_id = e.podtyp_id
        AND f.sloupec_id = b.sloupec_id)
    LEFT JOIN podtyp_sloupce g ON (g.podtyp_id = e.podtyp_id
        AND g.sloupec_id IN (SELECT
                                nl_id_vysledek
                            FROM __vysledky
                            WHERE nl_id_sazka = c.sazka_id
                                AND nl_id_vysledek = f.sloupec_id
                            ))
    WHERE a.ticket_id = 100433
        AND a.user_id = 20;
    DROP TABLE __vysledky;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `ws_uzivatel_dokumenty` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `ws_uzivatel_dokumenty`(IN inl_important INTEGER(11), IN inl_faze INTEGER(11), IN inl_id_admin_login INTEGER(11), IN inl_id_uzivatel INTEGER(11), IN ib_uzivatel_null TINYINT(1), IN inl_stranka INTEGER(11), IN inl_offset INTEGER(11))
BEGIN
	SET inl_stranka = COALESCE(inl_stranka , 1);
    SET inl_offset = COALESCE(inl_offset, 1000);

    SELECT
    	COUNT(a.`doc_id`) AS nl_pocet
	FROM uzivatel_dokumenty a
	JOIN uzivatel b ON (b.`user_id` = a.`user_id`)
	WHERE
    	(inl_important IS NULL OR inl_important = a.`important`) AND
        (inl_faze IS NULL OR inl_faze = a.faze) AND
        (inl_id_admin_login IS NULL OR inl_id_admin_login = a.admin_id) AND
        (inl_id_uzivatel IS NULL OR inl_id_uzivatel = a.`user_id`) AND
        (ib_uzivatel_null IS NULL OR CASE WHEN ib_uzivatel_null = 1 THEN a.`admin_id` ELSE 0 END IS NULL
        	OR CASE WHEN ib_uzivatel_null = 0 THEN 0 ELSE NULL END < a.`admin_id`)
	ORDER BY a.`user_id`;
	PREPARE STMT FROM "
	SELECT
    	a.`admin_id` AS nl_id_admin,
		a.`text` AS s_popis,
        CONCAT(a.doc_id , a.`name`) AS s_foto,
        a.`doc_id` AS nl_id_uzivatel_dokumenty,
        b.`jmeno` AS s_jmeno,
        b.`prijmeni` AS s_prijmeni,
        b.`nick` AS s_nick,
        b.`datum_registrace` AS dt_registrace,
        b.`user_id` AS nl_id_user,
        b.`email` AS s_email,
        b.`lang_id` AS nl_id_jazyk,
        CASE WHEN a.`valid` = '00.00.0000' THEN NULL ELSE a.`valid` END AS d_valid
	FROM uzivatel_dokumenty a
	JOIN uzivatel b ON (b.`user_id` = a.`user_id`)
	WHERE
    	(? IS NULL OR ? = a.`important`) AND
        (? IS NULL OR ? = a.faze) AND
        (? IS NULL OR ? = a.admin_id) AND
        (? IS NULL OR ? = a.`user_id`) AND
        (? IS NULL OR CASE WHEN ? = 1 THEN a.`admin_id` ELSE 0 END IS NULL
        	OR CASE WHEN ? = 0 THEN 0 ELSE NULL END < a.`admin_id`)
	ORDER BY a.`user_id`
    LIMIT ? , ?";

    SET @a = inl_important;
    SET @b = inl_faze;
    SET @c = inl_id_admin_login;
    SET @d = inl_id_uzivatel;
    SET @e = ib_uzivatel_null;
    SET @f = inl_offset * (inl_stranka - 1);
    SET @g = inl_offset;

    EXECUTE STMT USING  @a, @a, @b, @b, @c, @c, @d, @d, @e, @e, @e, @f, @g;
    DROP PREPARE STMT;

END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `ws_uzivatel_poznamka` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `ws_uzivatel_poznamka`(IN inl_id_uzivatel INTEGER(11))
BEGIN
	SELECT
    	a.`datum` AS dt_in,
        a.`nl_id` AS nl_id_uzivatel_poznamka,
        a.`text` AS s_poznamka
    FROM uzivatel_poznamka a
    WHERE
    	a.`user_id` = inl_id_uzivatel
    ORDER BY a.`datum` DESC;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `wu_uzivatel_dokumenty__email` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `wu_uzivatel_dokumenty__email`(IN is_subject VARCHAR(1000), IN is_body TEXT, IN inl_id_dokument INTEGER(11), IN inl_id_admin_login INTEGER(11))
BEGIN
	UPDATE uzivatel_dokumenty SET
    	mail_subj = is_subject,
        mail_body = is_body
    WHERE doc_id = inl_id_dokument
    	AND admin_id = inl_id_admin_login;

	SELECT
    	a.`admin_id` AS nl_id_admin,
        a.`doc_id` AS nl_id_dokument,
        a.`datum` AS dt_in
    FROM uzivatel_dokumenty a
    WHERE
    	a.`doc_id` = inl_id_dokument AND
        a.admin_id = inl_id_admin_login;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `wu_uzivatel_dokumenty__pridelit_uzivatel` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `wu_uzivatel_dokumenty__pridelit_uzivatel`(IN inl_id_admin INTEGER(11), IN is_heslo VARCHAR(40), IN inl_id_uzivatel INTEGER(11), IN inl_id_admin_login INTEGER(11))
BEGIN
	IF inl_id_uzivatel > 0 THEN

    	UPDATE uzivatel_dokumenty SET
            admin_id = inl_id_admin
        WHERE
        	user_id = inl_id_uzivatel AND
            faze = 0 AND
            important = 0;

        IF ROW_COUNT() > 0 THEN

        	SELECT
            	a.`user_id` AS nl_id_uzivatel,
                a.`doc_id` AS nl_id_uzivatel_dokument
            FROM uzivatel_dokumenty a
            WHERE
            	user_id = inl_id_uzivatel AND
                faze = 0 AND
                important = 0;

        END IF;

    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `wu_uzivatel_dokumenty__tmp_important` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `wu_uzivatel_dokumenty__tmp_important`(IN inl_id_admin_login INTEGER(11), IN inl_id_dokument INTEGER(11), IN ib_important INTEGER(11))
BEGIN
	UPDATE uzivatel_dokumenty SET
    	tmp_important = ib_important
    WHERE
    	admin_id = inl_id_admin_login AND
        doc_id = inl_id_dokument;

    SELECT
    	a.doc_id AS nl_id_uzvatel_dokumenty
    FROM uzivatel_dokumenty a
    WHERE
    	a.`admin_id` = inl_id_admin_login AND
        a.`doc_id` = inl_id_dokument;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `wu_uzivatel_dokumenty__valid` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `wu_uzivatel_dokumenty__valid`(IN id_valid DATE, IN inl_id_admin_login INTEGER(11), IN inl_id_dokument INTEGER(11))
BEGIN
	UPDATE uzivatel_dokumenty SET
    	valid = id_valid
    WHERE
    	doc_id = inl_id_dokument AND
    	admin_id = inl_id_admin_login;

    SELECT
    	a.doc_id AS nl_id_uzivatel_dokumenty
    FROM uzivatel_dokumenty a
    WHERE
    	a.`admin_id` = inl_id_admin_login AND
        a.`doc_id` = inl_id_dokument;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

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
/*!50001 VIEW `sazka_pohled` AS select `a`.`sazka_id` AS `sazka_id`,`a`.`proplatil_bookmaker` AS `proplatil_bookmaker`,`a`.`proplacena` AS `proplacena`,`a`.`info` AS `info`,`a`.`betradar_sazka_id` AS `betradar_sazka_id`,`a`.`platna_od` AS `platna_od`,`a`.`platna_do` AS `platna_do`,`a`.`status` AS `status`,`a`.`live` AS `live`,`a`.`bookmaker_id` AS `bookmaker_id`,`a`.`vysledek` AS `vysledek`,`a`.`udalost_id` AS `udalost_id`,`a`.`typ_id` AS `typ_id`,`a`.`podtyp_id` AS `podtyp_id`,`a`.`overena` AS `overena`,`a`.`text` AS `text`,`a`.`ako` AS `ako`,`a`.`jednoducha` AS `jednoducha`,`a`.`risk_limit` AS `risk_limit`,`a`.`risk_limit_balance` AS `risk_limit_balance`,`b`.`poradi` AS `poradi`,`b`.`kurz` AS `kurz`,`b`.`kurz_zmena` AS `kurz_zmena`,`b`.`platny_od` AS `platny_od`,`c`.`nazev` AS `nazev`,`c`.`sloupec_id` AS `sloupec_id`,`c`.`poradi` AS `poradi_sloupec` from (`sazky` `a` join (`sazka_kurz` `b` join `podtyp_sloupce` `c` on((`b`.`sloupec_id` = `c`.`sloupec_id`))) on((`a`.`sazka_id` = `b`.`sazka_id`))) */;
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
/*!50001 VIEW `ticket_pohled` AS select `b`.`sazka_id` AS `sazka_id`,`b`.`live` AS `live`,`b`.`platna_do` AS `platna_do`,`b`.`user_id` AS `user_id`,`b`.`proplacena` AS `proplacena`,`b`.`typ_id` AS `typ_id`,`b`.`text` AS `text`,`b`.`vysledek` AS `vysledek`,`b`.`status` AS `status`,`b`.`udalost_id` AS `udalost_id`,`b`.`kurz` AS `kurz`,`b`.`platny_od` AS `platny_od`,`b`.`sloupec_id` AS `sloupec_id`,`b`.`poradi` AS `poradi`,`b`.`ticket_sazka_zrusena` AS `ticket_sazka_zrusena`,`b`.`ticket_sazka_zrusil_bookmaker_id` AS `ticket_sazka_zrusil_bookmaker_id`,`b`.`ticket_sazka_duvod_zruseni` AS `ticket_sazka_duvod_zruseni`,`b`.`ticket_id` AS `ticket_id`,`b`.`zalozen` AS `zalozen`,`b`.`vyplacen` AS `vyplacen`,`b`.`type` AS `type`,`b`.`win` AS `win`,`b`.`rate` AS `rate`,`b`.`win_real` AS `win_real`,`b`.`rate_real` AS `rate_real`,`b`.`zruseno` AS `zruseno`,`b`.`kurz_zmena` AS `kurz_zmena`,`b`.`zrusil_bookmaker_id` AS `zrusil_bookmaker_id`,`b`.`duvod_zruseni` AS `duvod_zruseni`,`b`.`castka` AS `castka`,`b`.`free_bet_bonus` AS `free_bet_bonus`,`b`.`system` AS `system`,`b`.`stats` AS `stats`,`b`.`stats_user` AS `stats_user`,`b`.`banker` AS `banker`,`b`.`cupon_id` AS `cupon_id`,`b`.`mail` AS `mail`,`b`.`group_count` AS `group_count`,`b`.`group_t` AS `group_t`,`b`.`group_id` AS `group_id` from `ticket_pohled_connect` `b` where (`b`.`platny_od` = (select max(`a`.`platny_od`) AS `maxi_platny_od` from `ticket_pohled_connect` `a` where ((`a`.`zalozen` >= `a`.`platny_od`) and (`a`.`sazka_id` = `b`.`sazka_id`) and (`a`.`ticket_id` = `b`.`ticket_id`)) group by `a`.`ticket_id`,`a`.`sazka_id`)) */;
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
/*!50001 VIEW `ticket_pohled_connect` AS select `a`.`sazka_id` AS `sazka_id`,`a`.`live` AS `live`,`a`.`platna_do` AS `platna_do`,`d`.`stats` AS `stats`,`d`.`stats_user` AS `stats_user`,`d`.`user_id` AS `user_id`,`a`.`proplacena` AS `proplacena`,`a`.`typ_id` AS `typ_id`,`a`.`text` AS `text`,`a`.`vysledek` AS `vysledek`,`a`.`status` AS `status`,`a`.`udalost_id` AS `udalost_id`,`b`.`kurz` AS `kurz`,`b`.`platny_od` AS `platny_od`,`b`.`kurz_zmena` AS `kurz_zmena`,`b`.`sloupec_id` AS `sloupec_id`,`b`.`poradi` AS `poradi`,`c`.`ticket_sazka_zrusena` AS `ticket_sazka_zrusena`,`c`.`ticket_sazka_zrusil_bookmaker_id` AS `ticket_sazka_zrusil_bookmaker_id`,`c`.`ticket_sazka_duvod_zruseni` AS `ticket_sazka_duvod_zruseni`,`c`.`banker` AS `banker`,`c`.`group_id` AS `group_id`,`d`.`ticket_id` AS `ticket_id`,`d`.`type` AS `type`,`d`.`win` AS `win`,`d`.`rate` AS `rate`,`d`.`win_real` AS `win_real`,`d`.`rate_real` AS `rate_real`,`d`.`free_bet_bonus` AS `free_bet_bonus`,`d`.`zalozen` AS `zalozen`,`d`.`vyplacen` AS `vyplacen`,`d`.`zruseno` AS `zruseno`,`d`.`zrusil_bookmaker_id` AS `zrusil_bookmaker_id`,`d`.`duvod_zruseni` AS `duvod_zruseni`,`d`.`castka` AS `castka`,`d`.`system` AS `system`,`d`.`cupon_id` AS `cupon_id`,`d`.`mail` AS `mail`,`d`.`group_count` AS `group_count`,`d`.`group_t` AS `group_t` from (`sazky` `a` join (`sazka_kurz` `b` join (`ticket_kurz` `c` join `ticket` `d` on((`c`.`ticket_id` = `d`.`ticket_id`))) on(((`b`.`sazka_id` = `c`.`sazka_id`) and (`b`.`sloupec_id` = `c`.`sloupec_id`)))) on((`a`.`sazka_id` = `b`.`sazka_id`))) */;
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

-- Dump completed on 2010-07-29 10:06:27
