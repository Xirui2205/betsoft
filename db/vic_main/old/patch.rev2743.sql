CREATE TABLE `vic_main`.`user_actions` (
  `action_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(10) NOT NULL,
  `action_type` int(3) NOT NULL,
  `valid_from` datetime NOT NULL,
  `valid_until` datetime NOT NULL,
  `action_hash` char(32) NOT NULL,
  `date_created` datetime NOT NULL,
  `done` tinyint(1) NOT NULL DEFAULT '0',
  `date_executed` datetime DEFAULT NULL,
  PRIMARY KEY (`action_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `vic_main`.`archive_user_actions` (
  `action_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(10) NOT NULL,
  `action_type` int(3) NOT NULL,
  `valid_from` datetime NOT NULL,
  `valid_until` datetime NOT NULL,
  `action_hash` char(32) NOT NULL,
  `date_created` datetime NOT NULL,
  `done` tinyint(1) NOT NULL DEFAULT '0',
  `date_executed` datetime DEFAULT NULL,
  PRIMARY KEY (`action_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
