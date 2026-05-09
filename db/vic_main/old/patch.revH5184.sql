CREATE TABLE IF NOT EXISTS `affiliate_partner_ticket_user_provision` (
  `partner_id` int(10) NOT NULL,
  `ticket_id` bigint(20) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`partner_id`,`ticket_id`,`user_id`),
  KEY `fk_partner_id` (`partner_id`),
  KEY `fk_ticket_id` (`ticket_id`),
  KEY `fk_user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `affiliate_partner_ticket_user_provision`
  ADD CONSTRAINT `affiliate_partner_ticket_user_provision_ibfk_1` FOREIGN KEY (`partner_id`) REFERENCES `affiliate_partner` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `affiliate_partner_ticket_user_provision_ibfk_2` FOREIGN KEY (`ticket_id`) REFERENCES `ticket` (`ticket_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `affiliate_partner_ticket_user_provision_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `uzivatel` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;
