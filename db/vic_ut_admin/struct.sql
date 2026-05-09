CREATE DATABASE  `vic_ut_admin` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;

-- MySQL dump 10.13  Distrib 5.1.51, for unknown-linux-gnu (x86_64)
--
-- Host: localhost    Database: vic_admin
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
-- Table structure for table `acl_resource`
--

DROP TABLE IF EXISTS `acl_resource`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `acl_resource` (
  `acl_resource_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `acl_tree_id` smallint(3) unsigned NOT NULL,
  `acl_resource_name` varchar(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  `acl_resource_parent_id` int(10) unsigned DEFAULT NULL,
  `acl_resource_type_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`acl_resource_id`),
  UNIQUE KEY `u__acl_resource__acl_resource_name` (`acl_resource_name`),
  KEY `fk__acl_resource__acl_tree_id` (`acl_tree_id`),
  KEY `fk__acl_resource__acl_resource_parent_id` (`acl_resource_parent_id`),
  KEY `fk__acl_resource__acl_resource_type_id` (`acl_resource_type_id`),
  CONSTRAINT `fk__acl_resource__acl_resource_parent_id` FOREIGN KEY (`acl_resource_parent_id`) REFERENCES `acl_resource` (`acl_resource_id`),
  CONSTRAINT `fk__acl_resource__acl_resource_type_id` FOREIGN KEY (`acl_resource_type_id`) REFERENCES `acl_resource_type` (`acl_resource_type_id`),
  CONSTRAINT `fk__acl_resource__acl_tree_id` FOREIGN KEY (`acl_tree_id`) REFERENCES `acl_tree` (`acl_tree_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1244 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `acl_resource_type`
--

DROP TABLE IF EXISTS `acl_resource_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `acl_resource_type` (
  `acl_resource_type_id` int(10) unsigned NOT NULL,
  `acl_resource_type_name` varchar(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  PRIMARY KEY (`acl_resource_type_id`),
  UNIQUE KEY `u__acl_resource_type__acl_resource_type_name` (`acl_resource_type_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `acl_role`
--

DROP TABLE IF EXISTS `acl_role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `acl_role` (
  `acl_role_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `acl_role_name` varchar(32) NOT NULL,
  `assignable` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`acl_role_id`),
  UNIQUE KEY `role_name` (`acl_role_name`)
) ENGINE=InnoDB AUTO_INCREMENT=1039 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `acl_role_has_parent`
--

DROP TABLE IF EXISTS `acl_role_has_parent`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `acl_role_has_parent` (
  `acl_role_id` int(10) unsigned NOT NULL,
  `parent_id` int(10) unsigned NOT NULL,
  `parent_order` int(10) unsigned NOT NULL,
  PRIMARY KEY (`acl_role_id`,`parent_id`),
  KEY `fk__acl_role_has_parent__parent_id` (`parent_id`),
  CONSTRAINT `fk__acl_role_has_parent__acl_role_id` FOREIGN KEY (`acl_role_id`) REFERENCES `acl_role` (`acl_role_id`),
  CONSTRAINT `fk__acl_role_has_parent__parent_id` FOREIGN KEY (`parent_id`) REFERENCES `acl_role` (`acl_role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `acl_role_resource_privilege`
--

DROP TABLE IF EXISTS `acl_role_resource_privilege`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `acl_role_resource_privilege` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `acl_role_id` int(10) unsigned NOT NULL,
  `acl_resource_id` int(10) unsigned NOT NULL,
  `privilege_name` varchar(32) NOT NULL,
  `privilege_set` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `u__acl_role_resource_privilege__role_resource_privilege` (`acl_role_id`,`acl_resource_id`,`privilege_name`),
  KEY `privilege_name` (`privilege_name`),
  KEY `fk__acl_role_resource_privilege__resource_id` (`acl_resource_id`),
  CONSTRAINT `fk__acl_role_resource_privilege__acl_role_id` FOREIGN KEY (`acl_role_id`) REFERENCES `acl_role` (`acl_role_id`),
  CONSTRAINT `fk__acl_role_resource_privilege__resource_id` FOREIGN KEY (`acl_resource_id`) REFERENCES `acl_resource` (`acl_resource_id`)
) ENGINE=InnoDB AUTO_INCREMENT=520 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `acl_tree`
--

DROP TABLE IF EXISTS `acl_tree`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `acl_tree` (
  `acl_tree_id` smallint(3) unsigned NOT NULL AUTO_INCREMENT,
  `acl_tree_name` varchar(32) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  PRIMARY KEY (`acl_tree_id`),
  UNIQUE KEY `u__acl_tree__acl_tree_name` (`acl_tree_name`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `admin`
--

DROP TABLE IF EXISTS `admin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admin` (
  `admin_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(16) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `first_name` varchar(50) NOT NULL DEFAULT '',
  `surname` varchar(60) NOT NULL DEFAULT '',
  `passwd` varchar(60) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `phone` varchar(20) DEFAULT '',
  `email` varchar(80) NOT NULL DEFAULT '',
  `access` smallint(6) NOT NULL DEFAULT '0' COMMENT '0 = allowed',
  `block` smallint(6) NOT NULL DEFAULT '0',
  `block_ip` varchar(1000) NOT NULL DEFAULT '',
  `key` varchar(100) NOT NULL DEFAULT '',
  `ldap_username` varchar(100) NOT NULL DEFAULT '',
  `last_login` datetime DEFAULT NULL,
  `branch_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`admin_id`),
  UNIQUE KEY `username` (`username`),
  KEY `branch_id` (`branch_id`),
  CONSTRAINT `admin_ibfk_1` FOREIGN KEY (`branch_id`) REFERENCES `branch` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `admin_has_parameter`
--

DROP TABLE IF EXISTS `admin_has_parameter`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admin_has_parameter` (
  `admin_id` int(10) unsigned NOT NULL DEFAULT '0',
  `parameter_id` int(10) unsigned NOT NULL,
  `value` varchar(32) NOT NULL,
  PRIMARY KEY (`admin_id`,`parameter_id`),
  KEY `admin_id_2` (`admin_id`),
  KEY `parameter_id` (`parameter_id`),
  CONSTRAINT `admin_has_parameter_ibfk_1` FOREIGN KEY (`parameter_id`) REFERENCES `parameter` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Temporary table structure for view `admin_menu`
--

DROP TABLE IF EXISTS `admin_menu`;
/*!50001 DROP VIEW IF EXISTS `admin_menu`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `admin_menu` (
  `admin_id` int(10) unsigned,
  `sekce_id` int(10) unsigned,
  `updatex` smallint(5) unsigned,
  `readx` smallint(5) unsigned,
  `deletex` smallint(5) unsigned
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `bank_account`
--

DROP TABLE IF EXISTS `bank_account`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bank_account` (
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
) ENGINE=InnoDB AUTO_INCREMENT=72 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `bank_account_type`
--

DROP TABLE IF EXISTS `bank_account_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bank_account_type` (
  `type_id` int(11) NOT NULL AUTO_INCREMENT,
  `type_name` varchar(255) NOT NULL,
  PRIMARY KEY (`type_id`),
  UNIQUE KEY `type_name` (`type_name`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `branch`
--

DROP TABLE IF EXISTS `branch`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `branch` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type_id` int(10) unsigned NOT NULL,
  `branch_location_id` int(10) unsigned NOT NULL,
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
  `currency_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `type_id` (`type_id`),
  KEY `region_id` (`branch_location_id`),
  KEY `status_id` (`status_id`),
  KEY `currency_id` (`currency_id`),
  CONSTRAINT `branch_ibfk_1` FOREIGN KEY (`type_id`) REFERENCES `branch_type` (`id`),
  CONSTRAINT `branch_ibfk_2` FOREIGN KEY (`status_id`) REFERENCES `branch_status` (`id`),
  CONSTRAINT `branch_ibfk_3` FOREIGN KEY (`branch_location_id`) REFERENCES `branch_location` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `branch_has_bank_account`
--

DROP TABLE IF EXISTS `branch_has_bank_account`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `branch_has_bank_account` (
  `branch_id` int(11) unsigned NOT NULL,
  `bank_account_id` int(11) NOT NULL,
  `bank_account_type` int(11) NOT NULL,
  `is_current` tinyint(1) NOT NULL,
  `time_created` datetime NOT NULL,
  KEY `bank_account_type` (`bank_account_type`),
  KEY `branch_id` (`branch_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `branch_has_parameter`
--

DROP TABLE IF EXISTS `branch_has_parameter`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `branch_has_parameter` (
  `branch_id` int(10) unsigned NOT NULL DEFAULT '0',
  `parameter_id` int(10) unsigned NOT NULL,
  `value` varchar(32) NOT NULL,
  PRIMARY KEY (`branch_id`,`parameter_id`),
  KEY `branch_id_2` (`branch_id`),
  KEY `parameter_id` (`parameter_id`),
  CONSTRAINT `branch_has_parameter_ibfk_1` FOREIGN KEY (`branch_id`) REFERENCES `branch` (`id`),
  CONSTRAINT `branch_has_parameter_ibfk_2` FOREIGN KEY (`parameter_id`) REFERENCES `parameter` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `branch_location`
--

DROP TABLE IF EXISTS `branch_location`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `branch_location` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `branch_param`
--

DROP TABLE IF EXISTS `branch_param`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `branch_param` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL COMMENT 'No dashes( ''-'' ) allowed in param names! Zend dont like ''em.',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `branch_status`
--

DROP TABLE IF EXISTS `branch_status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `branch_status` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `branch_type`
--

DROP TABLE IF EXISTS `branch_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `branch_type` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `chyby`
--

DROP TABLE IF EXISTS `chyby`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `chyby` (
  `chyba_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `datum` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `radek` smallint(5) unsigned NOT NULL DEFAULT '0',
  `soubor` varchar(80) NOT NULL DEFAULT '',
  `text` text,
  `code` varchar(40) DEFAULT NULL,
  `status` smallint(5) unsigned NOT NULL DEFAULT '0',
  `message` varchar(240) DEFAULT NULL,
  `poznamka` text,
  `admin_id` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`chyba_id`),
  FULLTEXT KEY `Index_2` (`poznamka`,`message`,`text`)
) ENGINE=MyISAM AUTO_INCREMENT=1591 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `clen`
--

DROP TABLE IF EXISTS `clen`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `clen` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `jmeno` varchar(100) COLLATE utf8_bin NOT NULL DEFAULT '',
  `email` varchar(100) COLLATE utf8_bin NOT NULL DEFAULT '',
  `odpovednost` text COLLATE utf8_bin NOT NULL,
  `tel` varchar(40) COLLATE utf8_bin DEFAULT NULL,
  `icq` varchar(20) COLLATE utf8_bin DEFAULT NULL,
  `znalosti` varchar(800) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='InnoDB free: 105472 kB';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `contract`
--

DROP TABLE IF EXISTS `contract`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contract` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `date_valid_from` date NOT NULL,
  `date_valid_to` date NOT NULL,
  `date_signed` date DEFAULT NULL,
  `template_id` int(10) unsigned NOT NULL,
  `branch_id` int(10) unsigned NOT NULL,
  `date_canceled` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `template_id` (`template_id`),
  KEY `branch_id` (`branch_id`),
  CONSTRAINT `contract_ibfk_2` FOREIGN KEY (`template_id`) REFERENCES `contract_template` (`id`),
  CONSTRAINT `contract_ibfk_3` FOREIGN KEY (`branch_id`) REFERENCES `branch` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `contract_parameter`
--

DROP TABLE IF EXISTS `contract_parameter`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contract_parameter` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  `template_id` int(10) unsigned NOT NULL,
  `type` varchar(32) NOT NULL,
  `mandatory` tinyint(1) NOT NULL DEFAULT '1',
  `value` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `template_id` (`template_id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `contract_parameter_value`
--

DROP TABLE IF EXISTS `contract_parameter_value`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contract_parameter_value` (
  `contract_id` int(10) unsigned NOT NULL,
  `parameter_id` int(10) unsigned NOT NULL,
  `value` varchar(64) NOT NULL,
  PRIMARY KEY (`contract_id`,`parameter_id`),
  KEY `contract_id` (`contract_id`),
  KEY `parameter_id` (`parameter_id`),
  CONSTRAINT `contract_parameter_value_ibfk_1` FOREIGN KEY (`contract_id`) REFERENCES `contract` (`id`),
  CONSTRAINT `contract_parameter_value_ibfk_2` FOREIGN KEY (`parameter_id`) REFERENCES `parameter` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `contract_template`
--

DROP TABLE IF EXISTS `contract_template`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contract_template` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `host`
--

DROP TABLE IF EXISTS `host`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `host` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` int(10) unsigned NOT NULL,
  `name` varchar(32) NOT NULL,
  `allowed` tinyint(1) NOT NULL DEFAULT '0',
  `version` varchar(16) NOT NULL DEFAULT '0',
  `is_online` tinyint(1) NOT NULL DEFAULT '0',
  `hardware` text,
  `display` varchar(256) CHARACTER SET utf8 COLLATE utf8_hungarian_ci DEFAULT NULL,
  `printer` varchar(256) DEFAULT NULL,
  `ip` varchar(64) DEFAULT NULL,
  `win_sn` varchar(64) DEFAULT NULL,
  `provider_dns` varchar(64) DEFAULT NULL,
  `provider_gateway` varchar(64) DEFAULT NULL,
  `provider_ip` varchar(32) DEFAULT NULL,
  `provider_username` varchar(32) DEFAULT NULL,
  `provider_password` varchar(32) DEFAULT NULL,
  `vic_email` varchar(32) DEFAULT NULL,
  `vic_email_password` varchar(32) DEFAULT NULL,
  `vic_admin_password` varchar(32) DEFAULT NULL,
  `vic_employee_password_1` varchar(32) DEFAULT NULL,
  `vic_employee_password_2` varchar(32) DEFAULT NULL,
  `note` text,
  PRIMARY KEY (`id`),
  KEY `branch_id` (`branch_id`),
  CONSTRAINT `host_ibfk_1` FOREIGN KEY (`branch_id`) REFERENCES `branch` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `host_has_parameter`
--

DROP TABLE IF EXISTS `host_has_parameter`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `host_has_parameter` (
  `host_id` int(10) unsigned NOT NULL DEFAULT '0',
  `parameter_id` int(10) unsigned NOT NULL,
  `value` varchar(32) NOT NULL,
  PRIMARY KEY (`host_id`,`parameter_id`),
  KEY `host_id_2` (`host_id`),
  KEY `parameter_id` (`parameter_id`),
  CONSTRAINT `host_has_parameter_ibfk_1` FOREIGN KEY (`host_id`) REFERENCES `host` (`id`),
  CONSTRAINT `host_has_parameter_ibfk_2` FOREIGN KEY (`parameter_id`) REFERENCES `parameter` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
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
-- Table structure for table `parameter`
--

DROP TABLE IF EXISTS `parameter`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `parameter` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  `value` varchar(32) NOT NULL,
  `is_host` tinyint(1) NOT NULL,
  `is_branch` tinyint(1) NOT NULL,
  `is_user` tinyint(1) NOT NULL,
  `type` varchar(32) NOT NULL,
  `mandatory` tinyint(1) NOT NULL DEFAULT '1',
  `is_admin` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `prava`
--

DROP TABLE IF EXISTS `prava`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `prava` (
  `sekce_id` int(10) unsigned NOT NULL DEFAULT '0',
  `admin_id` int(10) unsigned NOT NULL DEFAULT '0',
  `readx` smallint(5) unsigned DEFAULT '0',
  `updatex` smallint(5) unsigned DEFAULT '0',
  `deletex` smallint(5) unsigned DEFAULT '0',
  PRIMARY KEY (`sekce_id`,`admin_id`),
  KEY `FK_prava_2` (`admin_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='InnoDB free: 105472 kB';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `referer`
--

DROP TABLE IF EXISTS `referer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `referer` (
  `admin_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `referer_url` varchar(150) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `date` datetime NOT NULL,
  PRIMARY KEY (`admin_id`,`referer_url`,`date`),
  CONSTRAINT `referer_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `admin` (`admin_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sekce`
--

DROP TABLE IF EXISTS `sekce`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sekce` (
  `sekce_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `acl_resource_id` int(10) unsigned NOT NULL,
  `nazev` varchar(80) NOT NULL DEFAULT '',
  `zobrazeno` smallint(5) unsigned NOT NULL DEFAULT '1',
  `controller` varchar(64) DEFAULT NULL,
  `action` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`sekce_id`),
  KEY `controller` (`controller`),
  KEY `fk__sekce__acl_resource_id` (`acl_resource_id`),
  CONSTRAINT `fk__sekce__acl_resource_id` FOREIGN KEY (`acl_resource_id`) REFERENCES `acl_resource` (`acl_resource_id`)
) ENGINE=InnoDB AUTO_INCREMENT=244 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sekce_has_parent`
--

DROP TABLE IF EXISTS `sekce_has_parent`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sekce_has_parent` (
  `sekce_id` int(10) unsigned NOT NULL,
  `parent_id` int(10) unsigned DEFAULT NULL,
  `sekce_order` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`sekce_id`),
  UNIQUE KEY `u__sekce_has_parent__sekce_id__parent_id` (`sekce_id`,`parent_id`),
  KEY `fk__sekce_has_parent__parent_id` (`parent_id`),
  CONSTRAINT `fk__sekce_has_parent__parent_id` FOREIGN KEY (`parent_id`) REFERENCES `sekce` (`sekce_id`),
  CONSTRAINT `fk__sekce_has_parent__sekce_id` FOREIGN KEY (`sekce_id`) REFERENCES `sekce` (`sekce_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `session`
--

DROP TABLE IF EXISTS `session`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `session` (
  `ses_id` varchar(50) NOT NULL DEFAULT '',
  `ip` varchar(10) NOT NULL DEFAULT '',
  `prohlizec` varchar(100) NOT NULL DEFAULT '',
  `data` text,
  `time` bigint(20) unsigned NOT NULL DEFAULT '0',
  `zprava` varchar(140) DEFAULT NULL,
  `status` smallint(5) unsigned NOT NULL DEFAULT '0',
  `start` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `admin_id` int(11) DEFAULT '0',
  `mobile` smallint(6) DEFAULT '0',
  PRIMARY KEY (`ses_id`),
  KEY `ip` (`ip`),
  KEY `prohlizec` (`prohlizec`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='InnoDB free: 105472 kB';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ukol`
--

DROP TABLE IF EXISTS `ukol`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ukol` (
  `ukol_id` int(11) NOT NULL AUTO_INCREMENT,
  `text` varchar(600) COLLATE utf8_bin NOT NULL DEFAULT '',
  `zahajeni` date NOT NULL,
  `dokonceni` date DEFAULT NULL,
  `skutecne_dokonceno` date DEFAULT NULL,
  `poznamka` text COLLATE utf8_bin NOT NULL,
  `priorita` smallint(6) NOT NULL DEFAULT '1',
  PRIMARY KEY (`ukol_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='InnoDB free: 105472 kB';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ukol_clen`
--

DROP TABLE IF EXISTS `ukol_clen`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ukol_clen` (
  `uid` int(11) NOT NULL,
  `ukol_id` int(11) NOT NULL,
  `prace` text COLLATE utf8_bin,
  PRIMARY KEY (`uid`,`ukol_id`),
  KEY `FK_ukol_clen_1` (`ukol_id`),
  CONSTRAINT `FK_ukol_clen_1` FOREIGN KEY (`ukol_id`) REFERENCES `ukol` (`ukol_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_ukol_clen_2` FOREIGN KEY (`uid`) REFERENCES `clen` (`uid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='InnoDB free: 105472 kB; (`ukol_id`) REFER `admin/ukol`(`ukol';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Temporary table structure for view `user`
--

DROP TABLE IF EXISTS `user`;
/*!50001 DROP VIEW IF EXISTS `user`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `user` (
  `user_id` int(10) unsigned,
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
  `lang_id` int(10) unsigned,
  `misto` varchar(250),
  `ucet_status` smallint(5) unsigned,
  `vyhernost` decimal(10,2) unsigned,
  `block` smallint(5) unsigned,
  `block_ip` varchar(1000),
  `vyhernost_game` decimal(18,4),
  `bet_stats_win` decimal(18,4) unsigned,
  `bet_stats_lose_acc` decimal(18,4) unsigned,
  `bet_stats_lose_book` decimal(18,4) unsigned,
  `bet_total` decimal(18,4) unsigned,
  `win_ticket` int(10) unsigned,
  `lose_ticket` int(10) unsigned,
  `delete_ticket` int(10) unsigned,
  `num_bet_ticket` int(10) unsigned,
  `bet_total2` decimal(18,4) unsigned,
  `ticket_num` bigint(20) unsigned,
  `finance_rating` smallint(5) unsigned,
  `max_bet` int(10) unsigned,
  `book_info` varchar(2000),
  `self_excluded_until` timestamp,
  `osloveni` varchar(60),
  `e_testovaci` enum('ne','test','naseip','auto','pobocka'),
  `block_play` tinyint(1) unsigned,
  `castka_m` decimal(11,2),
  `castka_w` decimal(11,2),
  `datum_aktivace` datetime
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `user_has_parameter`
--

DROP TABLE IF EXISTS `user_has_parameter`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_has_parameter` (
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `parameter_id` int(10) unsigned NOT NULL,
  `value` varchar(32) NOT NULL,
  PRIMARY KEY (`user_id`,`parameter_id`),
  KEY `user_id_2` (`user_id`),
  KEY `parameter_id` (`parameter_id`),
  CONSTRAINT `user_has_parameter_ibfk_1` FOREIGN KEY (`parameter_id`) REFERENCES `parameter` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Final view structure for view `admin_menu`
--

/*!50001 DROP TABLE IF EXISTS `admin_menu`*/;
/*!50001 DROP VIEW IF EXISTS `admin_menu`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `admin_menu` AS select `a`.`admin_id` AS `admin_id`,`b`.`sekce_id` AS `sekce_id`,`b`.`updatex` AS `updatex`,`b`.`readx` AS `readx`,`b`.`deletex` AS `deletex` from (`admin` `a` left join `prava` `b` on((`a`.`admin_id` = `b`.`admin_id`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `user`
--

/*!50001 DROP TABLE IF EXISTS `user`*/;
/*!50001 DROP VIEW IF EXISTS `user`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `user` AS select `vic_main`.`uzivatel`.`user_id` AS `user_id`,`vic_main`.`uzivatel`.`jmeno` AS `jmeno`,`vic_main`.`uzivatel`.`prijmeni` AS `prijmeni`,`vic_main`.`uzivatel`.`nick` AS `nick`,`vic_main`.`uzivatel`.`heslo` AS `heslo`,`vic_main`.`uzivatel`.`pohlavi` AS `pohlavi`,`vic_main`.`uzivatel`.`datum_narozeni` AS `datum_narozeni`,`vic_main`.`uzivatel`.`email` AS `email`,`vic_main`.`uzivatel`.`zeme_id` AS `zeme_id`,`vic_main`.`uzivatel`.`ulice` AS `ulice`,`vic_main`.`uzivatel`.`psc` AS `psc`,`vic_main`.`uzivatel`.`telefon` AS `telefon`,`vic_main`.`uzivatel`.`mena_id` AS `mena_id`,`vic_main`.`uzivatel`.`info` AS `info`,`vic_main`.`uzivatel`.`mobil` AS `mobil`,`vic_main`.`uzivatel`.`newsletter` AS `newsletter`,`vic_main`.`uzivatel`.`vyber_status` AS `vyber_status`,`vic_main`.`uzivatel`.`posledni_prihlaseni` AS `posledni_prihlaseni`,`vic_main`.`uzivatel`.`vyprseni_session` AS `vyprseni_session`,`vic_main`.`uzivatel`.`datum_registrace` AS `datum_registrace`,`vic_main`.`uzivatel`.`individualni_max_vklad` AS `individualni_max_vklad`,`vic_main`.`uzivatel`.`zakazany` AS `zakazany`,`vic_main`.`uzivatel`.`lang_id` AS `lang_id`,`vic_main`.`uzivatel`.`misto` AS `misto`,`vic_main`.`uzivatel`.`ucet_status` AS `ucet_status`,`vic_main`.`uzivatel`.`vyhernost` AS `vyhernost`,`vic_main`.`uzivatel`.`block` AS `block`,`vic_main`.`uzivatel`.`block_ip` AS `block_ip`,`vic_main`.`uzivatel`.`vyhernost_game` AS `vyhernost_game`,`vic_main`.`uzivatel`.`bet_stats_win` AS `bet_stats_win`,`vic_main`.`uzivatel`.`bet_stats_lose_acc` AS `bet_stats_lose_acc`,`vic_main`.`uzivatel`.`bet_stats_lose_book` AS `bet_stats_lose_book`,`vic_main`.`uzivatel`.`bet_total` AS `bet_total`,`vic_main`.`uzivatel`.`win_ticket` AS `win_ticket`,`vic_main`.`uzivatel`.`lose_ticket` AS `lose_ticket`,`vic_main`.`uzivatel`.`delete_ticket` AS `delete_ticket`,`vic_main`.`uzivatel`.`num_bet_ticket` AS `num_bet_ticket`,`vic_main`.`uzivatel`.`bet_total2` AS `bet_total2`,`vic_main`.`uzivatel`.`ticket_num` AS `ticket_num`,`vic_main`.`uzivatel`.`finance_rating` AS `finance_rating`,`vic_main`.`uzivatel`.`max_bet` AS `max_bet`,`vic_main`.`uzivatel`.`book_info` AS `book_info`,`vic_main`.`uzivatel`.`self_excluded_until` AS `self_excluded_until`,`vic_main`.`uzivatel`.`osloveni` AS `osloveni`,`vic_main`.`uzivatel`.`e_testovaci` AS `e_testovaci`,`vic_main`.`uzivatel`.`block_play` AS `block_play`,`vic_main`.`uzivatel`.`castka_m` AS `castka_m`,`vic_main`.`uzivatel`.`castka_w` AS `castka_w`,`vic_main`.`uzivatel`.`datum_aktivace` AS `datum_aktivace` from `vic_main`.`uzivatel` */;
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

-- Dump completed on 2010-11-03 12:44:21
