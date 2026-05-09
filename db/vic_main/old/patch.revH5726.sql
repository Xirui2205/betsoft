START TRANSACTION;
CREATE TABLE IF NOT EXISTS `coupon_saved` (
  `alias` int(10) unsigned NOT NULL,
  `saved_coupon_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned DEFAULT NULL,
  `data` text NOT NULL,
  `date` datetime NOT NULL,
  `admin_id` int(10) unsigned NOT NULL,
  `host_id` int(10) unsigned NOT NULL,
  `platny_do` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`saved_coupon_id`),
  UNIQUE KEY `alias` (`alias`),
  KEY `platny_do` (`platny_do`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `coupon_saved_free_alias` (
  `alias` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`alias`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

INSERT INTO `vic_main`.`preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`, `preklad_id`) VALUES
(2, 'coupon_saved', '', 'Coupon stored under ID: {0}', 0, 18369),
(1, 'coupon_saved', '', 'Kupón uložen pod ID: {0}', 0, 18369),
(16, 'coupon_saved', '', 'Kopón uložený pod ID: {0}', 0, 18369),

(2, 'save_coupon', '', 'Save', 0, 18370),
(1, 'save_coupon', '', 'Uložit', 0, 18370),
(16, 'save_coupon', '', 'Uložiť', 0, 18370);
COMMIT;