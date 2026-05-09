DROP TABLE `vic_main`.`coupon_data_archive`;
CREATE TABLE IF NOT EXISTS `vic_main`.`coupon_data_archive` (
  `archive_id` INT(11) unsigned NOT NULL AUTO_INCREMENT,
  `archived_at` DATETIME NOT NULL,
  `user_id` INT(10) unsigned NOT NULL,
  `admin_id` INT(10) unsigned NOT NULL DEFAULT 0,
  `coupon_id` INT(10) unsigned NOT NULL,
  `data` TEXT NOT NULL,
  `status` SMALLINT(5) unsigned NOT NULL DEFAULT 0,
  `date` DATETIME NOT NULL,
  `modified` TEXT,
  `live` SMALLINT(6) DEFAULT '0',
  `live_confirm` INT(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`archive_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE utf8_general_ci
COMMENT 'Required columns: archive id, time, columns from coupon_data';
