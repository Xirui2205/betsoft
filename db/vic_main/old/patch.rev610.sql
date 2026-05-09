ALTER TABLE `user_tracking` CHANGE `xforward` `xforward` VARCHAR( 200 ) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL;

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

DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER t_sazky_after_insert AFTER INSERT ON sazky
FOR EACH ROW BEGIN
	REPLACE INTO sazky_search(sazka_id, sazka_text)
	VALUES(NEW.sazka_id, NEW.`text`);
END */;;
DELIMITER ;

DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER t_sazky_after_update AFTER UPDATE ON sazky
FOR EACH ROW BEGIN
	UPDATE sazky_search SET sazka_text=NEW.`text` WHERE sazka_id=NEW.sazka_id;
END */;;
DELIMITER ;

DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER t_sazky_after_delete AFTER DELETE ON sazky
FOR EACH ROW BEGIN
	DELETE FROM sazky_search WHERE sazka_id=OLD.sazka_id;
END */;;
DELIMITER ;

DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `fn_sync_sazky_search`()
BEGIN
	DELETE FROM sazky_search;
	INSERT INTO sazky_search(sazka_id,sazka_text) SELECT sazka_id,`text` FROM sazky;
END */;;
DELIMITER ;
