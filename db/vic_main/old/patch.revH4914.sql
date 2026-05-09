CREATE TABLE IF NOT EXISTS `affiliate_banner` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `image_title` varchar(255) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `text` text NOT NULL,
  `target` enum('_blank','_self') NOT NULL,
  `valid_from` datetime DEFAULT NULL,
  `valid_to` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;

CREATE TABLE IF NOT EXISTS `affiliate_partner` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(256) COLLATE utf8_czech_ci NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `admin_id` int(11) NOT NULL,
  `url` varchar(128) COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci AUTO_INCREMENT=9 ;

CREATE TABLE IF NOT EXISTS `affiliate_partner_banner` (
  `partner_id` int(10) NOT NULL,
  `banner_id` int(10) NOT NULL,
  `count_users` varchar(64) NOT NULL,
  PRIMARY KEY (`partner_id`,`banner_id`),
  KEY `fk_partner_id` (`partner_id`),
  KEY `fk_banner_id` (`banner_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `affiliate_partner_banner`
  ADD CONSTRAINT `affiliate_partner_banner_ibfk_1` FOREIGN KEY (`partner_id`) REFERENCES `affiliate_partner` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `affiliate_partner_banner_ibfk_2` FOREIGN KEY (`banner_id`) REFERENCES `affiliate_banner` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

CREATE TABLE IF NOT EXISTS `affiliate_partner_banner_user` (
  `partner_id` int(10) NOT NULL,
  `banner_id` int(10) NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`partner_id`,`banner_id`, `user_id`),
  KEY `fk_partner_id` (`partner_id`),
  KEY `fk_banner_id` (`banner_id`),
  KEY `fk_user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `affiliate_partner_banner_user`
  ADD CONSTRAINT `affiliate_partner_banner_user_ibfk_1` FOREIGN KEY (`partner_id`) REFERENCES `affiliate_partner` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `affiliate_partner_banner_user_ibfk_2` FOREIGN KEY (`banner_id`) REFERENCES `affiliate_banner` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `affiliate_partner_banner_user_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `uzivatel` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;
