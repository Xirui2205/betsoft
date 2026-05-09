start transaction;

CREATE TABLE IF NOT EXISTS `setting_limits` (
  `limit_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `time_from` datetime NOT NULL,
  `time_to` datetime NOT NULL,
  `limit_amount` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `actual_amount` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`limit_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

ALTER TABLE `setting_limits` ADD CONSTRAINT `setting_limits_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `uzivatel` (`user_id`);

commit;
