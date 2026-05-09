CREATE DATABASE `vic_livezilla` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
CREATE USER 'livezilla'@'%' IDENTIFIED BY '***';

GRANT SELECT ,
INSERT ,

UPDATE ,
DELETE ,
CREATE ,
DROP ,
FILE ,
INDEX ,
ALTER ,
CREATE TEMPORARY TABLES ,
CREATE VIEW ,
EVENT,
TRIGGER,
SHOW VIEW ,
CREATE ROUTINE,
ALTER ROUTINE,
EXECUTE ON * . * TO 'livezilla'@'%' IDENTIFIED BY '***' WITH MAX_QUERIES_PER_HOUR 0 MAX_CONNECTIONS_PER_HOUR 0 MAX_UPDATES_PER_HOUR 0 MAX_USER_CONNECTIONS 0 ;

GRANT ALL PRIVILEGES ON `vic_livezilla` . * TO 'livezilla'@'%';


SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

use vic_livezilla;


CREATE TABLE IF NOT EXISTS `alerts` (
  `id` varchar(32) COLLATE utf8_bin NOT NULL DEFAULT '',
  `created` int(10) unsigned NOT NULL DEFAULT '0',
  `receiver_user_id` varchar(32) COLLATE utf8_bin NOT NULL DEFAULT '',
  `receiver_browser_id` varchar(32) COLLATE utf8_bin NOT NULL DEFAULT '',
  `event_action_id` varchar(32) COLLATE utf8_bin NOT NULL DEFAULT '',
  `text` mediumtext COLLATE utf8_bin NOT NULL,
  `displayed` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `accepted` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `receiver_user_id` (`receiver_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;



CREATE TABLE IF NOT EXISTS `chat_archive` (
  `time` int(11) unsigned NOT NULL DEFAULT '0',
  `endtime` int(11) unsigned NOT NULL DEFAULT '0',
  `closed` int(11) unsigned NOT NULL DEFAULT '0',
  `chat_id` varchar(64) COLLATE utf8_bin NOT NULL DEFAULT '',
  `external_id` varchar(32) COLLATE utf8_bin NOT NULL DEFAULT '',
  `fullname` varchar(32) COLLATE utf8_bin NOT NULL DEFAULT '',
  `internal_id` varchar(32) COLLATE utf8_bin NOT NULL DEFAULT '',
  `group_id` varchar(32) COLLATE utf8_bin NOT NULL DEFAULT '',
  `area_code` varchar(100) COLLATE utf8_bin NOT NULL DEFAULT '',
  `html` longtext COLLATE utf8_bin NOT NULL,
  `plain` longtext COLLATE utf8_bin NOT NULL,
  `email` varchar(50) COLLATE utf8_bin NOT NULL DEFAULT '',
  `company` varchar(50) COLLATE utf8_bin NOT NULL DEFAULT '',
  `iso_language` varchar(8) COLLATE utf8_bin NOT NULL DEFAULT '',
  `iso_country` varchar(5) COLLATE utf8_bin NOT NULL DEFAULT '',
  `host` varchar(64) COLLATE utf8_bin NOT NULL DEFAULT '',
  `ip` varchar(15) COLLATE utf8_bin NOT NULL DEFAULT '',
  `gzip` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `transcript_sent` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `transcript_receiver` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  `question` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  `customs` text COLLATE utf8_bin NOT NULL,
  KEY `chat_id` (`chat_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;



CREATE TABLE IF NOT EXISTS `chat_files` (
  `id` varchar(64) COLLATE utf8_bin NOT NULL,
  `created` int(10) unsigned NOT NULL DEFAULT '0',
  `file_name` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  `file_mask` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  `file_id` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  `chat_id` int(10) unsigned NOT NULL DEFAULT '0',
  `visitor_id` varchar(32) COLLATE utf8_bin NOT NULL DEFAULT '',
  `browser_id` varchar(32) COLLATE utf8_bin NOT NULL DEFAULT '',
  `operator_id` varchar(32) COLLATE utf8_bin NOT NULL DEFAULT '',
  `error` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  `permission` tinyint(1) NOT NULL DEFAULT '-1',
  `download` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `closed` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`,`created`),
  KEY `visitor_id` (`visitor_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;



CREATE TABLE IF NOT EXISTS `chat_forwards` (
  `id` varchar(32) COLLATE utf8_bin NOT NULL DEFAULT '',
  `created` int(10) unsigned NOT NULL DEFAULT '0',
  `sender_operator_id` varchar(32) COLLATE utf8_bin NOT NULL DEFAULT '',
  `target_operator_id` varchar(32) COLLATE utf8_bin NOT NULL DEFAULT '',
  `target_group_id` varchar(32) COLLATE utf8_bin NOT NULL DEFAULT '',
  `visitor_id` varchar(32) COLLATE utf8_bin NOT NULL DEFAULT '',
  `browser_id` varchar(32) COLLATE utf8_bin NOT NULL DEFAULT '',
  `chat_id` int(11) unsigned NOT NULL DEFAULT '0',
  `conversation` mediumtext COLLATE utf8_bin NOT NULL,
  `info_text` mediumtext COLLATE utf8_bin NOT NULL,
  `processed` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `received` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `chat_id` (`chat_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;



CREATE TABLE IF NOT EXISTS `chat_posts` (
  `id` varchar(32) COLLATE utf8_bin NOT NULL DEFAULT '',
  `chat_id` varchar(32) COLLATE utf8_bin NOT NULL DEFAULT '',
  `time` int(11) unsigned NOT NULL DEFAULT '0',
  `micro` int(11) unsigned NOT NULL DEFAULT '0',
  `sender` varchar(65) COLLATE utf8_bin NOT NULL DEFAULT '',
  `receiver` varchar(65) COLLATE utf8_bin NOT NULL DEFAULT '',
  `receiver_group` varchar(32) COLLATE utf8_bin NOT NULL DEFAULT '',
  `text` mediumtext COLLATE utf8_bin NOT NULL,
  `translation` mediumtext COLLATE utf8_bin NOT NULL,
  `translation_iso` varchar(10) COLLATE utf8_bin NOT NULL DEFAULT '',
  `received` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `persistent` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;



CREATE TABLE IF NOT EXISTS `chat_requests` (
  `id` varchar(32) COLLATE utf8_bin NOT NULL DEFAULT '',
  `created` int(10) unsigned NOT NULL DEFAULT '0',
  `sender_system_id` varchar(32) COLLATE utf8_bin NOT NULL DEFAULT '',
  `sender_group_id` varchar(32) COLLATE utf8_bin NOT NULL DEFAULT '',
  `receiver_user_id` varchar(32) COLLATE utf8_bin NOT NULL DEFAULT '',
  `receiver_browser_id` varchar(32) COLLATE utf8_bin NOT NULL DEFAULT '',
  `event_action_id` varchar(32) COLLATE utf8_bin NOT NULL DEFAULT '',
  `text` mediumtext COLLATE utf8_bin NOT NULL,
  `displayed` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `accepted` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `declined` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `closed` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `receiver_browser_id` (`receiver_browser_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;



CREATE TABLE IF NOT EXISTS `events` (
  `id` varchar(32) COLLATE utf8_bin NOT NULL DEFAULT '',
  `name` varchar(32) COLLATE utf8_bin NOT NULL DEFAULT '',
  `created` int(10) unsigned NOT NULL DEFAULT '0',
  `creator` varchar(32) COLLATE utf8_bin NOT NULL DEFAULT '',
  `edited` int(10) unsigned NOT NULL DEFAULT '0',
  `editor` varchar(32) COLLATE utf8_bin NOT NULL DEFAULT '',
  `pages_visited` int(10) unsigned NOT NULL DEFAULT '0',
  `time_on_site` int(10) unsigned NOT NULL DEFAULT '0',
  `max_trigger_amount` int(10) unsigned NOT NULL DEFAULT '0',
  `trigger_again_after` int(10) unsigned NOT NULL DEFAULT '0',
  `not_declined` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `not_accepted` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `not_in_chat` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `priority` int(10) unsigned NOT NULL DEFAULT '0',
  `is_active` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `search_phrase` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;



CREATE TABLE IF NOT EXISTS `event_actions` (
  `id` varchar(32) COLLATE utf8_bin NOT NULL DEFAULT '',
  `eid` varchar(32) COLLATE utf8_bin NOT NULL DEFAULT '',
  `type` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `value` mediumtext COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`),
  KEY `event_id` (`eid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;



CREATE TABLE IF NOT EXISTS `event_action_internals` (
  `id` varchar(32) COLLATE utf8_bin NOT NULL DEFAULT '',
  `created` int(10) unsigned NOT NULL DEFAULT '0',
  `trigger_id` varchar(32) COLLATE utf8_bin NOT NULL DEFAULT '',
  `receiver_user_id` varchar(32) COLLATE utf8_bin NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;



CREATE TABLE IF NOT EXISTS `event_action_overlays` (
  `id` varchar(32) COLLATE utf8_bin NOT NULL DEFAULT '',
  `action_id` varchar(32) COLLATE utf8_bin NOT NULL DEFAULT '',
  `position` varchar(2) COLLATE utf8_bin NOT NULL DEFAULT '',
  `speed` tinyint(1) NOT NULL DEFAULT '1',
  `slide` tinyint(1) NOT NULL DEFAULT '1',
  `margin_left` int(11) NOT NULL DEFAULT '0',
  `margin_top` int(11) NOT NULL DEFAULT '0',
  `margin_right` int(11) NOT NULL DEFAULT '0',
  `margin_bottom` int(11) NOT NULL DEFAULT '0',
  `style` varchar(32) COLLATE utf8_bin NOT NULL DEFAULT '',
  `close_on_click` tinyint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `action_id` (`action_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;



CREATE TABLE IF NOT EXISTS `event_action_receivers` (
  `id` varchar(32) COLLATE utf8_bin NOT NULL DEFAULT '',
  `action_id` varchar(32) COLLATE utf8_bin NOT NULL DEFAULT '',
  `receiver_id` varchar(32) COLLATE utf8_bin NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `action_id` (`action_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;



CREATE TABLE IF NOT EXISTS `event_action_senders` (
  `id` varchar(32) COLLATE utf8_bin NOT NULL DEFAULT '',
  `pid` varchar(32) COLLATE utf8_bin NOT NULL DEFAULT '',
  `user_id` varchar(32) COLLATE utf8_bin NOT NULL DEFAULT '',
  `group_id` varchar(32) COLLATE utf8_bin NOT NULL DEFAULT '',
  `priority` tinyint(2) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;



CREATE TABLE IF NOT EXISTS `event_action_website_pushs` (
  `id` varchar(32) COLLATE utf8_bin NOT NULL DEFAULT '',
  `action_id` varchar(32) COLLATE utf8_bin NOT NULL DEFAULT '',
  `target_url` varchar(2056) COLLATE utf8_bin NOT NULL DEFAULT '',
  `ask` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `action_id` (`action_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;



CREATE TABLE IF NOT EXISTS `event_funnels` (
  `eid` varchar(32) COLLATE utf8_bin NOT NULL DEFAULT '',
  `uid` varchar(32) COLLATE utf8_bin NOT NULL DEFAULT '',
  `ind` smallint(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`eid`,`uid`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;



CREATE TABLE IF NOT EXISTS `event_goals` (
  `event_id` varchar(32) COLLATE utf8_bin NOT NULL DEFAULT '',
  `goal_id` int(10) unsigned NOT NULL DEFAULT '0',
  UNIQUE KEY `prim` (`event_id`,`goal_id`),
  KEY `target_id` (`goal_id`),
  KEY `event_id` (`event_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;



CREATE TABLE IF NOT EXISTS `event_triggers` (
  `id` varchar(32) COLLATE utf8_bin NOT NULL DEFAULT '',
  `receiver_user_id` varchar(32) COLLATE utf8_bin NOT NULL DEFAULT '',
  `receiver_browser_id` varchar(32) COLLATE utf8_bin NOT NULL DEFAULT '',
  `action_id` varchar(32) COLLATE utf8_bin NOT NULL DEFAULT '',
  `time` int(10) unsigned NOT NULL DEFAULT '0',
  `triggered` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `receiver_user_id` (`receiver_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;



CREATE TABLE IF NOT EXISTS `event_urls` (
  `id` varchar(32) COLLATE utf8_bin NOT NULL DEFAULT '',
  `eid` varchar(32) COLLATE utf8_bin NOT NULL DEFAULT '',
  `url` varchar(2048) COLLATE utf8_bin NOT NULL DEFAULT '',
  `referrer` varchar(2048) COLLATE utf8_bin NOT NULL DEFAULT '',
  `time_on_site` int(10) unsigned NOT NULL DEFAULT '0',
  `blacklist` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `event_id` (`eid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;



CREATE TABLE IF NOT EXISTS `filters` (
  `creator` varchar(32) COLLATE utf8_bin NOT NULL DEFAULT '',
  `created` int(10) unsigned NOT NULL DEFAULT '0',
  `editor` varchar(32) COLLATE utf8_bin NOT NULL DEFAULT '',
  `edited` int(10) unsigned NOT NULL DEFAULT '0',
  `ip` varchar(15) COLLATE utf8_bin NOT NULL DEFAULT '',
  `expiredate` int(10) NOT NULL DEFAULT '0',
  `visitor_id` varchar(32) COLLATE utf8_bin NOT NULL DEFAULT '',
  `reason` text COLLATE utf8_bin NOT NULL,
  `name` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  `id` varchar(32) COLLATE utf8_bin NOT NULL DEFAULT '',
  `active` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `exertion` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `languages` text COLLATE utf8_bin NOT NULL,
  `activeipaddress` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `activevisitorid` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `activelanguage` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;



CREATE TABLE IF NOT EXISTS `goals` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8_bin NOT NULL,
  `description` text COLLATE utf8_bin NOT NULL,
  `conversion` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `ind` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `title` (`title`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;



CREATE TABLE IF NOT EXISTS `info` (
  `version` varchar(15) COLLATE utf8_bin NOT NULL,
  `chat_id` int(11) unsigned NOT NULL DEFAULT '11700',
  `ticket_id` int(11) unsigned NOT NULL DEFAULT '11700',
  `gtspan` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`version`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;



CREATE TABLE IF NOT EXISTS `operators` (
  `id` varchar(32) COLLATE utf8_bin NOT NULL DEFAULT '',
  `login_id` varchar(32) COLLATE utf8_bin NOT NULL DEFAULT '',
  `first_active` int(10) unsigned NOT NULL DEFAULT '0',
  `last_active` int(10) unsigned NOT NULL DEFAULT '0',
  `password` varchar(32) COLLATE utf8_bin NOT NULL DEFAULT '',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `level` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `ip` varchar(15) COLLATE utf8_bin NOT NULL DEFAULT '',
  `typing` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `visitor_file_sizes` mediumtext COLLATE utf8_bin NOT NULL,
  `last_chat_allocation` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;



CREATE TABLE IF NOT EXISTS `operator_logins` (
  `id` varchar(32) COLLATE utf8_bin NOT NULL DEFAULT '',
  `user_id` varchar(32) COLLATE utf8_bin NOT NULL DEFAULT '',
  `ip` varchar(32) COLLATE utf8_bin NOT NULL DEFAULT '',
  `time` int(11) unsigned NOT NULL DEFAULT '0',
  `password` varchar(32) COLLATE utf8_bin NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;



CREATE TABLE IF NOT EXISTS `operator_status` (
  `time` int(11) unsigned NOT NULL,
  `confirmed` int(11) unsigned NOT NULL,
  `internal_id` varchar(15) COLLATE utf8_bin NOT NULL,
  `status` tinyint(1) unsigned NOT NULL,
  PRIMARY KEY (`time`,`internal_id`,`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;



CREATE TABLE IF NOT EXISTS `predefined` (
  `id` int(11) unsigned NOT NULL,
  `internal_id` varchar(32) COLLATE utf8_bin NOT NULL,
  `group_id` varchar(32) COLLATE utf8_bin NOT NULL,
  `lang_iso` varchar(5) COLLATE utf8_bin NOT NULL,
  `invitation_manual` mediumtext COLLATE utf8_bin NOT NULL,
  `invitation_auto` mediumtext COLLATE utf8_bin NOT NULL,
  `welcome` mediumtext COLLATE utf8_bin NOT NULL,
  `website_push_manual` mediumtext COLLATE utf8_bin NOT NULL,
  `website_push_auto` mediumtext COLLATE utf8_bin NOT NULL,
  `browser_ident` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `is_default` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `auto_welcome` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `editable` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;



CREATE TABLE IF NOT EXISTS `profiles` (
  `id` varchar(32) COLLATE utf8_bin NOT NULL DEFAULT '',
  `edited` int(11) NOT NULL DEFAULT '0',
  `first_name` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  `last_name` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  `email` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  `company` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  `phone` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  `fax` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  `street` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  `zip` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  `department` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  `city` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  `country` varchar(255) COLLATE utf8_bin NOT NULL,
  `gender` tinyint(1) NOT NULL DEFAULT '0',
  `languages` varchar(1024) COLLATE utf8_bin NOT NULL DEFAULT '',
  `comments` longtext COLLATE utf8_bin NOT NULL,
  `public` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;



CREATE TABLE IF NOT EXISTS `profile_pictures` (
  `id` varchar(32) COLLATE utf8_bin NOT NULL DEFAULT '',
  `internal_id` varchar(32) COLLATE utf8_bin NOT NULL,
  `time` int(11) NOT NULL DEFAULT '0',
  `webcam` tinyint(1) NOT NULL DEFAULT '0',
  `data` mediumtext COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;



CREATE TABLE IF NOT EXISTS `ratings` (
  `id` varchar(32) COLLATE utf8_bin NOT NULL,
  `time` int(11) unsigned NOT NULL,
  `user_id` varchar(32) COLLATE utf8_bin NOT NULL,
  `internal_id` varchar(32) COLLATE utf8_bin NOT NULL,
  `fullname` varchar(32) COLLATE utf8_bin NOT NULL,
  `email` varchar(50) COLLATE utf8_bin NOT NULL,
  `company` varchar(50) COLLATE utf8_bin NOT NULL,
  `qualification` tinyint(1) unsigned NOT NULL,
  `politeness` tinyint(1) unsigned NOT NULL,
  `comment` varchar(400) COLLATE utf8_bin NOT NULL,
  `ip` varchar(15) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;



CREATE TABLE IF NOT EXISTS `resources` (
  `id` varchar(32) COLLATE utf8_bin NOT NULL,
  `owner` varchar(15) COLLATE utf8_bin NOT NULL,
  `editor` varchar(15) COLLATE utf8_bin NOT NULL,
  `value` longtext COLLATE utf8_bin NOT NULL,
  `edited` int(11) unsigned NOT NULL,
  `title` varchar(255) COLLATE utf8_bin NOT NULL,
  `created` int(11) unsigned NOT NULL,
  `type` tinyint(1) unsigned NOT NULL,
  `discarded` tinyint(1) unsigned NOT NULL,
  `parentid` varchar(32) COLLATE utf8_bin NOT NULL,
  `rank` int(11) unsigned NOT NULL,
  `size` bigint(20) unsigned NOT NULL,
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;



CREATE TABLE IF NOT EXISTS `stats_aggs` (
  `year` smallint(4) unsigned NOT NULL DEFAULT '0',
  `month` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `day` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `time` int(10) unsigned NOT NULL DEFAULT '0',
  `mtime` int(10) unsigned NOT NULL DEFAULT '0',
  `sessions` int(10) unsigned NOT NULL DEFAULT '0',
  `visitors_unique` int(10) unsigned NOT NULL DEFAULT '0',
  `conversions` int(10) unsigned NOT NULL DEFAULT '0',
  `aggregated` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `chats_forwards` int(10) unsigned NOT NULL DEFAULT '0',
  `chats_posts_internal` int(10) unsigned NOT NULL DEFAULT '0',
  `chats_posts_external` int(10) unsigned NOT NULL DEFAULT '0',
  `avg_time_site` double unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`year`,`month`,`day`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;



CREATE TABLE IF NOT EXISTS `stats_aggs_availabilities` (
  `year` smallint(5) unsigned NOT NULL DEFAULT '0',
  `month` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `day` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `hour` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `user_id` varchar(32) COLLATE utf8_bin NOT NULL DEFAULT '',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `seconds` int(4) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`year`,`month`,`day`,`user_id`,`hour`,`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;



CREATE TABLE IF NOT EXISTS `stats_aggs_browsers` (
  `year` smallint(5) unsigned NOT NULL DEFAULT '0',
  `month` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `day` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `browser` int(10) unsigned NOT NULL DEFAULT '0',
  `amount` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`year`,`month`,`day`,`browser`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;



CREATE TABLE IF NOT EXISTS `stats_aggs_chats` (
  `year` smallint(5) unsigned NOT NULL DEFAULT '0',
  `month` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `day` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `hour` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `user_id` varchar(32) COLLATE utf8_bin NOT NULL DEFAULT '',
  `amount` int(10) unsigned NOT NULL DEFAULT '0',
  `accepted` int(10) unsigned NOT NULL DEFAULT '0',
  `declined` int(10) unsigned NOT NULL DEFAULT '0',
  `avg_duration` double unsigned NOT NULL DEFAULT '0',
  `avg_waiting_time` double unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`year`,`month`,`day`,`user_id`,`hour`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;



CREATE TABLE IF NOT EXISTS `stats_aggs_cities` (
  `year` smallint(5) unsigned NOT NULL DEFAULT '0',
  `month` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `day` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `city` int(10) unsigned NOT NULL DEFAULT '0',
  `amount` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`year`,`month`,`day`,`city`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;



CREATE TABLE IF NOT EXISTS `stats_aggs_countries` (
  `year` smallint(5) unsigned NOT NULL DEFAULT '0',
  `month` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `day` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `country` varchar(2) COLLATE utf8_bin NOT NULL DEFAULT '',
  `amount` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`year`,`month`,`day`,`country`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;



CREATE TABLE IF NOT EXISTS `stats_aggs_crawlers` (
  `year` smallint(5) unsigned NOT NULL DEFAULT '0',
  `month` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `day` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `crawler` int(10) unsigned NOT NULL DEFAULT '0',
  `amount` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`year`,`month`,`day`,`crawler`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;



CREATE TABLE IF NOT EXISTS `stats_aggs_domains` (
  `year` smallint(5) unsigned NOT NULL DEFAULT '0',
  `month` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `day` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `domain` int(10) unsigned NOT NULL DEFAULT '0',
  `amount` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`year`,`month`,`day`,`domain`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;



CREATE TABLE IF NOT EXISTS `stats_aggs_durations` (
  `year` smallint(5) unsigned NOT NULL DEFAULT '0',
  `month` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `day` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `duration` int(10) unsigned NOT NULL DEFAULT '0',
  `amount` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`year`,`month`,`day`,`duration`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;



CREATE TABLE IF NOT EXISTS `stats_aggs_goals` (
  `year` smallint(5) unsigned NOT NULL DEFAULT '0',
  `month` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `day` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `goal` int(10) unsigned NOT NULL DEFAULT '0',
  `amount` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`year`,`month`,`day`,`goal`),
  KEY `target` (`goal`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;



CREATE TABLE IF NOT EXISTS `stats_aggs_isps` (
  `year` smallint(5) unsigned NOT NULL DEFAULT '0',
  `month` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `day` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `isp` int(10) unsigned NOT NULL DEFAULT '0',
  `amount` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`year`,`month`,`day`,`isp`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;



CREATE TABLE IF NOT EXISTS `stats_aggs_languages` (
  `year` smallint(5) unsigned NOT NULL DEFAULT '0',
  `month` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `day` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `language` varchar(5) COLLATE utf8_bin NOT NULL,
  `amount` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`year`,`month`,`day`,`language`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;



CREATE TABLE IF NOT EXISTS `stats_aggs_pages` (
  `year` smallint(5) unsigned NOT NULL DEFAULT '0',
  `month` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `day` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `url` int(10) unsigned NOT NULL DEFAULT '0',
  `amount` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`year`,`month`,`day`,`url`),
  KEY `url_id` (`url`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;



CREATE TABLE IF NOT EXISTS `stats_aggs_pages_entrance` (
  `year` smallint(5) unsigned NOT NULL DEFAULT '0',
  `month` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `day` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `url` int(10) unsigned NOT NULL DEFAULT '0',
  `amount` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`year`,`month`,`day`,`url`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;



CREATE TABLE IF NOT EXISTS `stats_aggs_pages_exit` (
  `year` smallint(5) unsigned NOT NULL DEFAULT '0',
  `month` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `day` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `url` int(10) unsigned NOT NULL DEFAULT '0',
  `amount` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`year`,`month`,`day`,`url`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;



CREATE TABLE IF NOT EXISTS `stats_aggs_queries` (
  `year` smallint(5) unsigned NOT NULL DEFAULT '0',
  `month` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `day` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `query` int(10) unsigned NOT NULL DEFAULT '0',
  `amount` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`year`,`month`,`day`,`query`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;



CREATE TABLE IF NOT EXISTS `stats_aggs_referrers` (
  `year` smallint(5) unsigned NOT NULL DEFAULT '0',
  `month` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `day` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `referrer` int(10) unsigned NOT NULL DEFAULT '0',
  `amount` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`year`,`month`,`day`,`referrer`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;



CREATE TABLE IF NOT EXISTS `stats_aggs_regions` (
  `year` smallint(5) unsigned NOT NULL DEFAULT '0',
  `month` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `day` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `region` int(10) unsigned NOT NULL DEFAULT '0',
  `amount` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`year`,`month`,`day`,`region`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;



CREATE TABLE IF NOT EXISTS `stats_aggs_resolutions` (
  `year` smallint(5) unsigned NOT NULL DEFAULT '0',
  `month` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `day` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `resolution` int(10) unsigned NOT NULL DEFAULT '0',
  `amount` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`year`,`month`,`day`,`resolution`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;



CREATE TABLE IF NOT EXISTS `stats_aggs_search_engines` (
  `year` smallint(5) unsigned NOT NULL DEFAULT '0',
  `month` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `day` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `domain` int(10) unsigned NOT NULL DEFAULT '0',
  `amount` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`year`,`month`,`day`,`domain`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;



CREATE TABLE IF NOT EXISTS `stats_aggs_systems` (
  `year` smallint(5) unsigned NOT NULL DEFAULT '0',
  `month` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `day` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `system` int(10) unsigned NOT NULL DEFAULT '0',
  `amount` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`year`,`month`,`day`,`system`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;



CREATE TABLE IF NOT EXISTS `stats_aggs_visitors` (
  `year` smallint(5) unsigned NOT NULL DEFAULT '0',
  `month` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `day` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `hour` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `visitors_unique` int(10) unsigned NOT NULL DEFAULT '0',
  `page_impressions` int(10) unsigned NOT NULL DEFAULT '0',
  `visitors_recurring` int(10) unsigned NOT NULL DEFAULT '0',
  `bounces` int(10) unsigned NOT NULL DEFAULT '0',
  `search_engine` int(10) unsigned NOT NULL DEFAULT '0',
  `from_referrer` int(10) unsigned NOT NULL DEFAULT '0',
  `browser_instances` int(10) unsigned NOT NULL DEFAULT '0',
  `js` int(10) unsigned NOT NULL DEFAULT '0',
  `on_chat_page` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`year`,`month`,`day`,`hour`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;



CREATE TABLE IF NOT EXISTS `stats_aggs_visits` (
  `year` smallint(5) unsigned NOT NULL DEFAULT '0',
  `month` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `day` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `visits` int(10) unsigned NOT NULL DEFAULT '0',
  `amount` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`year`,`month`,`day`,`visits`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;



CREATE TABLE IF NOT EXISTS `tickets` (
  `id` varchar(32) COLLATE utf8_bin NOT NULL,
  `user_id` varchar(32) COLLATE utf8_bin NOT NULL,
  `target_group_id` varchar(32) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;



CREATE TABLE IF NOT EXISTS `ticket_customs` (
  `ticket_id` varchar(32) COLLATE utf8_bin NOT NULL DEFAULT '',
  `custom_id` varchar(32) COLLATE utf8_bin NOT NULL DEFAULT '',
  `value` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  PRIMARY KEY (`ticket_id`,`custom_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;



CREATE TABLE IF NOT EXISTS `ticket_editors` (
  `ticket_id` varchar(32) COLLATE utf8_bin NOT NULL DEFAULT '',
  `internal_fullname` varchar(32) COLLATE utf8_bin NOT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `time` int(10) unsigned NOT NULL,
  PRIMARY KEY (`ticket_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;



CREATE TABLE IF NOT EXISTS `ticket_messages` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `time` int(11) unsigned NOT NULL,
  `ticket_id` varchar(32) COLLATE utf8_bin NOT NULL,
  `text` mediumtext COLLATE utf8_bin NOT NULL,
  `fullname` varchar(32) COLLATE utf8_bin NOT NULL,
  `email` varchar(50) COLLATE utf8_bin NOT NULL,
  `company` varchar(50) COLLATE utf8_bin NOT NULL,
  `ip` varchar(15) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ticket_id` (`ticket_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;



CREATE TABLE IF NOT EXISTS `visitors` (
  `id` varchar(32) COLLATE utf8_bin NOT NULL DEFAULT '',
  `entrance` int(10) unsigned NOT NULL DEFAULT '0',
  `last_active` int(10) unsigned NOT NULL DEFAULT '0',
  `host` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  `ip` varchar(15) COLLATE utf8_bin NOT NULL DEFAULT '',
  `system` smallint(5) unsigned NOT NULL DEFAULT '0',
  `browser` smallint(5) unsigned NOT NULL DEFAULT '0',
  `visits` smallint(5) unsigned NOT NULL DEFAULT '0',
  `visit_id` varchar(7) COLLATE utf8_bin NOT NULL DEFAULT '',
  `visit_latest` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `visit_last` int(10) unsigned NOT NULL DEFAULT '0',
  `resolution` smallint(5) unsigned NOT NULL DEFAULT '0',
  `language` varchar(5) COLLATE utf8_bin NOT NULL,
  `country` varchar(2) COLLATE utf8_bin NOT NULL DEFAULT '',
  `city` smallint(5) unsigned NOT NULL DEFAULT '0',
  `region` smallint(5) unsigned NOT NULL DEFAULT '0',
  `isp` smallint(5) unsigned NOT NULL DEFAULT '0',
  `timezone` varchar(24) COLLATE utf8_bin NOT NULL DEFAULT '',
  `latitude` double NOT NULL DEFAULT '0',
  `longitude` double NOT NULL DEFAULT '0',
  `geo_result` int(10) unsigned NOT NULL DEFAULT '0',
  `js` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `signature` varchar(32) COLLATE utf8_bin NOT NULL DEFAULT '',
  PRIMARY KEY (`id`,`entrance`),
  UNIQUE KEY `visit_id` (`visit_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;



CREATE TABLE IF NOT EXISTS `visitor_browsers` (
  `id` varchar(32) COLLATE utf8_bin NOT NULL DEFAULT '',
  `visitor_id` varchar(32) COLLATE utf8_bin NOT NULL DEFAULT '',
  `visit_id` varchar(7) COLLATE utf8_bin NOT NULL DEFAULT '',
  `created` int(10) unsigned NOT NULL DEFAULT '0',
  `last_active` int(10) unsigned NOT NULL DEFAULT '0',
  `last_update` varchar(2) COLLATE utf8_bin NOT NULL DEFAULT '',
  `is_chat` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `query` int(10) unsigned NOT NULL DEFAULT '0',
  `fullname` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  `email` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  `company` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  `customs` text COLLATE utf8_bin NOT NULL,
  `url_entrance` int(10) unsigned NOT NULL DEFAULT '0',
  `url_exit` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `visit_id` (`visit_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;



CREATE TABLE IF NOT EXISTS `visitor_browser_urls` (
  `browser_id` varchar(32) COLLATE utf8_bin NOT NULL DEFAULT '',
  `entrance` int(10) unsigned NOT NULL DEFAULT '0',
  `referrer` int(10) unsigned NOT NULL DEFAULT '0',
  `url` int(10) unsigned NOT NULL DEFAULT '0',
  `params` text COLLATE utf8_bin NOT NULL,
  `untouched` text COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`entrance`,`browser_id`),
  KEY `browser_id` (`browser_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;



CREATE TABLE IF NOT EXISTS `visitor_chats` (
  `visitor_id` varchar(32) COLLATE utf8_bin NOT NULL DEFAULT '',
  `browser_id` varchar(32) COLLATE utf8_bin NOT NULL DEFAULT '',
  `visit_id` varchar(32) COLLATE utf8_bin NOT NULL DEFAULT '',
  `chat_id` int(11) unsigned NOT NULL DEFAULT '0',
  `fullname` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  `email` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  `company` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `typing` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `waiting` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `area_code` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  `first_active` int(10) unsigned NOT NULL DEFAULT '0',
  `last_active` int(10) unsigned NOT NULL DEFAULT '0',
  `qpenalty` int(10) unsigned NOT NULL DEFAULT '0',
  `request_operator` varchar(32) COLLATE utf8_bin NOT NULL DEFAULT '',
  `request_group` varchar(32) COLLATE utf8_bin NOT NULL DEFAULT '',
  `question` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  `customs` text COLLATE utf8_bin NOT NULL,
  `allocated` int(11) unsigned NOT NULL DEFAULT '0',
  `internal_active` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `internal_closed` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `internal_declined` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `external_active` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `external_close` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `exit` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`visitor_id`,`browser_id`,`visit_id`,`chat_id`),
  KEY `chat_id` (`chat_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;



CREATE TABLE IF NOT EXISTS `visitor_chat_operators` (
  `chat_id` int(10) unsigned NOT NULL DEFAULT '0',
  `user_id` varchar(32) COLLATE utf8_bin NOT NULL,
  `declined` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `dtime` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`user_id`,`chat_id`),
  KEY `chat_id` (`chat_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;



CREATE TABLE IF NOT EXISTS `visitor_data_area_codes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `area_code` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `area_code` (`area_code`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;



CREATE TABLE IF NOT EXISTS `visitor_data_browsers` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `browser` varchar(255) COLLATE utf8_bin NOT NULL,
  `type` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `browser` (`browser`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;



CREATE TABLE IF NOT EXISTS `visitor_data_cities` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `city` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `city` (`city`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;



CREATE TABLE IF NOT EXISTS `visitor_data_crawlers` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `crawler` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `crawler` (`crawler`),
  UNIQUE KEY `crawler_2` (`crawler`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;



CREATE TABLE IF NOT EXISTS `visitor_data_domains` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `domain` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  `external` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `search` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `domain` (`domain`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;



CREATE TABLE IF NOT EXISTS `visitor_data_isps` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `isp` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `isp` (`isp`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;



CREATE TABLE IF NOT EXISTS `visitor_data_pages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `domain` int(10) unsigned NOT NULL DEFAULT '0',
  `path` int(10) unsigned NOT NULL DEFAULT '0',
  `title` int(10) unsigned NOT NULL DEFAULT '0',
  `area_code` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ` (`domain`,`path`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;



CREATE TABLE IF NOT EXISTS `visitor_data_paths` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `path` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `path` (`path`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;



CREATE TABLE IF NOT EXISTS `visitor_data_queries` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `query` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `query` (`query`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;



CREATE TABLE IF NOT EXISTS `visitor_data_regions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `region` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `region` (`region`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;



CREATE TABLE IF NOT EXISTS `visitor_data_resolutions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `resolution` varchar(32) COLLATE utf8_bin NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `resolution` (`resolution`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;



CREATE TABLE IF NOT EXISTS `visitor_data_systems` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `system` varchar(255) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `os` (`system`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;



CREATE TABLE IF NOT EXISTS `visitor_data_titles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8_bin NOT NULL,
  `confirmed` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `title` (`title`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;



CREATE TABLE IF NOT EXISTS `visitor_goals` (
  `visitor_id` varchar(32) COLLATE utf8_bin NOT NULL DEFAULT '',
  `goal_id` int(10) unsigned NOT NULL DEFAULT '0',
  `time` int(10) unsigned NOT NULL DEFAULT '0',
  `first_visit` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`visitor_id`,`goal_id`),
  KEY `visitor_id` (`visitor_id`),
  KEY `target_id` (`goal_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;



CREATE TABLE IF NOT EXISTS `website_pushs` (
  `id` varchar(32) COLLATE utf8_bin NOT NULL DEFAULT '',
  `created` int(10) unsigned NOT NULL DEFAULT '0',
  `sender_system_id` varchar(32) COLLATE utf8_bin NOT NULL DEFAULT '',
  `receiver_user_id` varchar(32) COLLATE utf8_bin NOT NULL DEFAULT '',
  `receiver_browser_id` varchar(32) COLLATE utf8_bin NOT NULL DEFAULT '',
  `text` mediumtext COLLATE utf8_bin NOT NULL,
  `ask` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `target_url` varchar(2048) COLLATE utf8_bin NOT NULL DEFAULT '',
  `displayed` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `accepted` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `declined` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `receiver_browser_id` (`receiver_browser_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;


ALTER TABLE `alerts`
  ADD CONSTRAINT `alerts_ibfk_1` FOREIGN KEY (`receiver_user_id`) REFERENCES `visitors` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `chat_files`
  ADD CONSTRAINT `chat_files_ibfk_1` FOREIGN KEY (`visitor_id`) REFERENCES `visitors` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `chat_forwards`
  ADD CONSTRAINT `chat_forwards_ibfk_1` FOREIGN KEY (`chat_id`) REFERENCES `visitor_chats` (`chat_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `chat_requests`
  ADD CONSTRAINT `chat_requests_ibfk_1` FOREIGN KEY (`receiver_browser_id`) REFERENCES `visitor_browsers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `event_actions`
  ADD CONSTRAINT `event_actions_ibfk_1` FOREIGN KEY (`eid`) REFERENCES `events` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `event_action_overlays`
  ADD CONSTRAINT `event_action_overlays_ibfk_1` FOREIGN KEY (`action_id`) REFERENCES `event_actions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `event_action_receivers`
  ADD CONSTRAINT `event_action_receivers_ibfk_1` FOREIGN KEY (`action_id`) REFERENCES `event_actions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `event_action_website_pushs`
  ADD CONSTRAINT `event_action_website_pushs_ibfk_1` FOREIGN KEY (`action_id`) REFERENCES `event_actions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `event_funnels`
  ADD CONSTRAINT `event_funnels_ibfk_1` FOREIGN KEY (`uid`) REFERENCES `event_urls` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `event_funnels_ibfk_2` FOREIGN KEY (`eid`) REFERENCES `events` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `event_goals`
  ADD CONSTRAINT `event_goals_ibfk_1` FOREIGN KEY (`goal_id`) REFERENCES `goals` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `event_goals_ibfk_2` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `event_triggers`
  ADD CONSTRAINT `event_triggers_ibfk_1` FOREIGN KEY (`receiver_user_id`) REFERENCES `visitors` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `event_urls`
  ADD CONSTRAINT `event_urls_ibfk_1` FOREIGN KEY (`eid`) REFERENCES `events` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `stats_aggs_goals`
  ADD CONSTRAINT `stats_aggs_goals_ibfk_1` FOREIGN KEY (`goal`) REFERENCES `goals` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `stats_aggs_pages`
  ADD CONSTRAINT `stats_aggs_pages_ibfk_1` FOREIGN KEY (`url`) REFERENCES `visitor_data_pages` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `ticket_customs`
  ADD CONSTRAINT `ticket_customs_ibfk_1` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `ticket_editors`
  ADD CONSTRAINT `ticket_editors_ibfk_1` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `ticket_messages`
  ADD CONSTRAINT `ticket_messages_ibfk_1` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `visitor_browsers`
  ADD CONSTRAINT `visitor_browsers_ibfk_1` FOREIGN KEY (`visit_id`) REFERENCES `visitors` (`visit_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `visitor_browser_urls`
  ADD CONSTRAINT `visitor_browser_urls_ibfk_1` FOREIGN KEY (`browser_id`) REFERENCES `visitor_browsers` (`id`) ON DELETE CASCADE;

ALTER TABLE `visitor_chats`
  ADD CONSTRAINT `visitor_chats_ibfk_1` FOREIGN KEY (`visitor_id`) REFERENCES `visitors` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `visitor_chat_operators`
  ADD CONSTRAINT `visitor_chat_operators_ibfk_1` FOREIGN KEY (`chat_id`) REFERENCES `visitor_chats` (`chat_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `visitor_goals`
  ADD CONSTRAINT `visitor_goals_ibfk_1` FOREIGN KEY (`visitor_id`) REFERENCES `visitors` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `website_pushs`
  ADD CONSTRAINT `website_pushs_ibfk_1` FOREIGN KEY (`receiver_browser_id`) REFERENCES `visitor_browsers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
