-- this table must have all the collumns as table coupon_data has
CREATE TABLE IF NOT EXISTS `vic_main`.`coupon_data_archive` (
  `user_id` int(10) unsigned NOT NULL,
  `admin_id` int(10) unsigned NOT NULL DEFAULT '0',
  `coupon_id` int(10) unsigned NOT NULL,
  `data` text NOT NULL,
  `status` smallint(5) unsigned NOT NULL DEFAULT '0',
  `date` datetime NOT NULL,
  `modified` text,
  `live` smallint(6) DEFAULT '0',
  `live_confirm` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`coupon_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;
