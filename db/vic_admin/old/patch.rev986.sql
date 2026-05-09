SET foreign_key_checks=0;

DROP TABLE IF EXISTS contract_parameter;

CREATE TABLE IF NOT EXISTS `contract_parameter` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  `template_id` int(10) unsigned NOT NULL,
  `type` varchar(32) NOT NULL,
  `mandatory` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `template_id` (`template_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS parameter;
CREATE TABLE IF NOT EXISTS `parameter` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  `value` varchar(32) NOT NULL,
  `is_host` tinyint(1) NOT NULL,
  `is_branch` tinyint(1) NOT NULL,
  `is_user` tinyint(1) NOT NULL,
  `type` varchar(32) NOT NULL,
  `mandatory` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=12 ;

--
-- Dumping data for table `parameter`
--

INSERT INTO `parameter` (`id`, `name`, `value`, `is_host`, `is_branch`, `is_user`, `type`, `mandatory`) VALUES
(1, 'branch.clientDeposit', '1', 0, 1, 0, '', 1),
(2, 'branch.clientDepositOnline', '1', 0, 1, 0, '', 1),
(3, 'branch.clientWithdraw', '1', 0, 1, 0, '', 1),
(4, 'branch.branchDeposit', '1', 0, 1, 0, '', 1),
(5, 'branch.branchWithdraw', '1', 0, 1, 0, '', 1),
(6, 'branch.clientBonusProgram', '1', 0, 1, 0, '', 1),
(7, 'branch.cashDeskLimit', '10000', 0, 1, 0, '', 1),
(10, 'duplicateTicketCount', '3', 0, 1, 1, '', 1),
(11, 'user.greeting', 'Dobrý den', 0, 0, 1, '', 1);

SET foreign_key_checks=1;
