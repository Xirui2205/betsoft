DELIMITER //

CREATE TABLE IF NOT EXISTS `vic_ut_main`.`user_search` (
  `user_id` int(11) NOT NULL,
  `jmeno` varchar(50) DEFAULT NULL,
  `prijmeni` varchar(50) NOT NULL,
  `nick` varchar(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `ulice` varchar(100) NOT NULL,
  `telefon` varchar(30) DEFAULT NULL,
  `mobil` varchar(30) DEFAULT NULL,
  `info` text,
  `misto` varchar(250) NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8//

ALTER TABLE  `vic_ut_main`.`user_search` ADD FULLTEXT (
`jmeno` ,
`prijmeni` ,
`nick` ,
`email` ,
`ulice` ,
`telefon` ,
`mobil` ,
`info` ,
`misto`
)


DROP TRIGGER IF EXISTS `t_uzivatel_after_insert`//
CREATE TRIGGER `t_uzivatel_after_insert` AFTER INSERT ON `vic_ut_main`.`uzivatel`
 FOR EACH ROW BEGIN
	REPLACE INTO user_search(jmeno, prijmeni, nick, email, ulice, telefon, mobil, info, misto)
	VALUES(NEW.jmeno, NEW.prijmeni, NEW.nick, NEW.email, NEW.ulice, NEW.telefon, NEW.mobil, NEW.info, NEW.misto);
END
//

DROP TRIGGER IF EXISTS `t_uzivatel_after_update`//
CREATE TRIGGER `t_uzivatel_after_update` AFTER UPDATE ON `vic_ut_main`.`uzivatel`
 FOR EACH ROW BEGIN
	UPDATE user_search
		SET jmeno=NEW.jmeno, prijmeni=NEW.prijmeni, nick=NEW.nick, email=NEW.email, ulice=NEW.ulice, telefon=NEW.telefon, mobil=NEW.mobil, info=NEW.info, misto=NEW.misto
		WHERE user_id=NEW.user_id;
END
//

DROP TRIGGER IF EXISTS `t_uzivatel_after_delete`//
CREATE TRIGGER `t_uzivatel_after_delete` AFTER DELETE ON `vic_ut_main`.`uzivatel`
 FOR EACH ROW BEGIN
	DELETE FROM user_search WHERE user_id=OLD.user_id;
END
//

CREATE PROCEDURE `fn_sync_user_search`()
BEGIN
	DELETE FROM user_search;
	INSERT INTO user_search(user_id, jmeno, prijmeni, nick, email, ulice, telefon, mobil, info, misto) SELECT user_id, jmeno, prijmeni, nick, email, ulice, telefon, mobil, info, misto FROM `vic_ut_main`.`uzivatel`;
END
//

DELIMITER ;
