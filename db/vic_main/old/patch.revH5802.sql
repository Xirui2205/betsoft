CREATE TABLE IF NOT EXISTS `report` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_report_type` int(10) unsigned NOT NULL,
  `time` datetime NOT NULL,
  `month` smallint(6) NOT NULL,
  `year` smallint(6) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `report_item`
--

CREATE TABLE IF NOT EXISTS `report_item` (
  `id_report` int(10) unsigned NOT NULL,
  `id_report_item_type` int(11) NOT NULL,
  `value` decimal(10,0) NOT NULL,
  PRIMARY KEY (`id_report`,`id_report_item_type`),
  KEY `id_report_item_type` (`id_report_item_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `report_item_type`
--

CREATE TABLE IF NOT EXISTS `report_item_type` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=15 ;

--
-- Dumping data for table `report_item_type`
--

INSERT INTO `report_item_type` (`id`, `name`) VALUES
(1, 'start_balance'),
(2, 'end_balance'),
(3, 'bonuses'),
(4, 'host_deposit'),
(5, 'ticket_payout'),
(6, 'ticket_create'),
(7, 'bank_deposit'),
(8, 'host_withdraw'),
(9, 'bank_withdraw'),
(10, 'fees'),
(11, 'deposit'),
(12, 'withdraw'),
(13, 'host_ticket_create'),
(14, 'host_ticket_payout');

-- --------------------------------------------------------

--
-- Table structure for table `report_type`
--

CREATE TABLE IF NOT EXISTS `report_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `report_type`
--

INSERT INTO `report_type` (`id`, `name`) VALUES
(1, 'user_accounts'),
(2, 'host_accounts');

INSERT INTO `cronjob_type` (`type_id`, `type_name`) VALUES ('26', 'AccountingReports');

INSERT INTO `cronjob` (
`cronjob_id` ,
`cronjob_type` ,
`result` ,
`attempts` ,
`attempts_max` ,
`exec_at` ,
`executed_at` ,
`exec_constantly_at`
)
VALUES (
'33',  '26', NULL ,  '0',  '0', NULL , NULL ,  '0 5 1 * *'
);


INSERT INTO `preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`, `preklad_id`)
 VALUES ('1', 'start_balance', NULL, 'Počáteční zůstatek', '0', '');
INSERT INTO `preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`, `preklad_id`)
 VALUES ('1', 'end_balance', NULL, 'Konečný zůstatek', '0', '');
INSERT INTO `preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`, `preklad_id`)
 VALUES ('1', 'bonuses', NULL, 'Bonusy', '0', '');
INSERT INTO `preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`, `preklad_id`)
 VALUES ('1', 'host_deposit', NULL, 'Vklad na pobočce', '0', '');
INSERT INTO `preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`, `preklad_id`)
 VALUES ('1', 'ticket_payout', NULL, 'Výplata tiketu', '0', '');
 INSERT INTO `preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`, `preklad_id`)
 VALUES ('1', 'ticket_create', NULL, 'Založení tiketu', '0', '');
 INSERT INTO `preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`, `preklad_id`)
 VALUES ('1', 'bank_deposit', NULL, 'Výběr z konta bankou', '0', '');
 INSERT INTO `preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`, `preklad_id`)
 VALUES ('1', 'host_withdraw', NULL, 'Výběr na pobočce', '0', '');
 INSERT INTO `preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`, `preklad_id`)
 VALUES ('1', 'bank_withdraw', NULL, 'Dobití konta bankou', '0', '');
INSERT INTO `preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`, `preklad_id`)
 VALUES ('1', 'fees', NULL, 'Poplatky', '0', '');
 INSERT INTO `preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`, `preklad_id`)
 VALUES ('1', 'host_ticket_create', NULL, 'Založení tiketu na pobočce', '0', '');
 INSERT INTO `preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`, `preklad_id`)
 VALUES ('1', 'host_ticket_payout', NULL, 'Výplata tiketu na pobočce', '0', '');
 INSERT INTO `preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`, `preklad_id`)
 VALUES ('1', 'host_accounts', NULL, 'Pokladny', '0', '');
  INSERT INTO `preklady` (`lang_id`, `index_pole`, `short_text`, `text`, `translate`, `preklad_id`)
 VALUES ('1', 'user_accounts', NULL, 'Online konta', '0', '');