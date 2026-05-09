SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET FOREIGN_KEY_CHECKS=0;

USE `vic_admin`;
DROP TABLE IF EXISTS `admin`;
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
  PRIMARY KEY (`admin_id`),
  UNIQUE KEY (`username`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;


START TRANSACTION;

INSERT INTO `admin` (`admin_id`, `username`, `passwd`, `first_name`, `surname`, `email`, `phone`, `last_login`, `access`, `key`, `block`, `block_ip`, `ldap_username`) VALUES
(3, 'kusa', '9dc1b933c5f211868e62efa87c87840a', 'Ondra', 'Zach', 'ondrej.tyrychtr@garris.cz', '', NULL, 0, '2;3;4;5;6;7;8;19;1000', 0, '', 'ondrej.tyrychtr'),
(4, 'petr', 'bc81eb90de9413ee069786c90ee72054', 'Petr', 'Stastny', 'petr.stastny@compbet.com', '', NULL, 0, '', 0, '', ''),
(5, 'vojtech', '3cdc412f94df326c04447c61ff7101b0', 'Vojtěch', 'Hercl', 'v.hercl@compbet.com', '', NULL, 0, '', 0, '', ''),
(6, 'petr_b', 'f3d2d0aa40641f9c3a011866f0c84c6b', 'Petr', 'Stastny', 'petr.stastny@compbet.com', '', NULL, 0, '', 0, '', '');
SET FOREIGN_KEY_CHECKS=1;
COMMIT;

CREATE TABLE IF NOT EXISTS `referer` (
  `admin_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `referer_url` varchar(150) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `date` datetime NOT NULL,
  PRIMARY KEY (`admin_id`,`referer_url`,`date`),
  FOREIGN KEY (`admin_id`) REFERENCES `admin` (`admin_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_bin;

INSERT INTO `parameter` (`name`, `value`, `is_host`, `is_branch`, `is_user`, `type`, `mandatory`) VALUES
('global.adminOff', '0', 0, 0, 0, '', 1);

ALTER TABLE `session` DROP `bookmaker_id`, DROP `old_ses_id`, DROP `pobocka_user_id`;

-- update bookmakers' roles to match their new IDs inserted into table admin above in this patch
UPDATE `role` SET `role_name`='user:admin:6' WHERE `role_name`='user:main:3';

-- update user's role names to new format
UPDATE `role` SET `role_name`=CONCAT('user:', SUBSTRING(`role_name`, CHAR_LENGTH('user:admin:') + 1)) WHERE `role_name` LIKE 'user:admin:%';

-- following line shouldn't be necessary, because we converted all particular users from user:main:X to user:admin:Y
--UPDATE `role` SET `role_name`=CONCAT('user:', SUBSTRING(`role_name`, CHAR_LENGTH('user:main:') + 1)) WHERE `role_name` LIKE 'user:main:%';

-- now should be table vic_main.bookmaker obsolete
