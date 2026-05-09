CREATE TABLE IF NOT EXISTS `host` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` int(10) unsigned NOT NULL,
  `name` varchar(32) NOT NULL,
  `allowed` tinyint(1) NOT NULL DEFAULT '0',
  `version` varchar(16) NOT NULL DEFAULT '0',
  `is_online` tinyint(1) NOT NULL DEFAULT '0',
  `hardware` text,
  `display` varchar(256) CHARACTER SET utf8 COLLATE utf8_hungarian_ci DEFAULT NULL,
  `printer` varchar(256) DEFAULT NULL,
  `ip` varchar(64) DEFAULT NULL,
  `win_sn` varchar(64) DEFAULT NULL,
  `provider_dns` varchar(64) DEFAULT NULL,
  `provider_gateway` varchar(64) DEFAULT NULL,
  `provider_ip` varchar(32) DEFAULT NULL,
  `provider_username` varchar(32) DEFAULT NULL,
  `provider_password` varchar(32) DEFAULT NULL,
  `vic_email` varchar(32) DEFAULT NULL,
  `vic_email_password` varchar(32) DEFAULT NULL,
  `vic_admin_password` varchar(32) DEFAULT NULL,
  `vic_employee_password_1` varchar(32) DEFAULT NULL,
  `vic_employee_password_2` varchar(32) DEFAULT NULL,
  `note` text,
  PRIMARY KEY (`id`),
  KEY `branch_id` (`branch_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `host`
--
ALTER TABLE `host`
  ADD CONSTRAINT `host_ibfk_1` FOREIGN KEY (`branch_id`) REFERENCES `branch` (`id`);
