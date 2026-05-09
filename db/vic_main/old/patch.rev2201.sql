DROP TABLE IF EXISTS `vic_main`.`bankovni_ucty`;
DROP TABLE IF EXISTS `vic_main`.`banks`;

CREATE TABLE `vic_main`.`banks` (
  `bank_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `bank_name` varchar(255) NOT NULL,
  `bank_code` int(7) unsigned NOT NULL,
  PRIMARY KEY (`bank_id`),
  UNIQUE KEY `bank_name` (`bank_name`),
  UNIQUE KEY `bank_code` (`bank_code`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE `vic_main`.`user_bank_accounts` (
  `account_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `account_prefix` int(6) unsigned DEFAULT NULL,
  `account_number` int(9) unsigned NOT NULL,
  `bank_id` int(10) unsigned NOT NULL,
  `is_current` BOOLEAN NOT NULL DEFAULT  '0',
  PRIMARY KEY (`account_id`),
  KEY `user_id` (`user_id`),
  KEY `bank_id` (`bank_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


ALTER TABLE `vic_main`.`user_bank_accounts`
  ADD CONSTRAINT `user_bank_accounts_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `uzivatel` (`user_id`),
  ADD CONSTRAINT `user_bank_accounts_ibfk_1` FOREIGN KEY (`bank_id`) REFERENCES `banks` (`bank_id`);


INSERT INTO `vic_main`.`banks` (`bank_id`, `bank_name`, `bank_code`)
VALUES
(NULL, 'AKCENTA, spořitelní a úvěrní družstvo', '2030'),
(NULL, 'Artesa, spořitelní družstvo', '2220'),
(NULL, 'AXA Bank Europe', '2230'),
(NULL, 'Banco Popolare', '6100'),
(NULL, 'Bank of Tokyo-Mitsubishi UFJ (Holland)', '2020'),
(NULL, 'Citfin, spořitelní družstvo', '2060'),
(NULL, 'BRE Bank (mBank)', '6210'),
(NULL, 'Citibank Europe', '2600'),
(NULL, 'COMMERZBANK', '6200'),
(NULL, 'Česká exportní banka', '8090'),
(NULL, 'Crédit Agricole Corporate and Investment Bank (CALYON)', '5000'),
(NULL, 'Česká národní banka', '0710'),
(NULL, 'Česká spořitelna', '0800'),
(NULL, 'Českomoravská stavební spořitelna', '7960'),
(NULL, 'Českomoravská záruční a rozvojová banka', '4300'),
(NULL, 'Československá obchodní banka (+ Poštovní spořitelna)', '0300'),
(NULL, 'Deutsche Bank', '7910'),
(NULL, 'Evropsko-ruská banka', '2210'),
(NULL, 'Fio banka', '2010'),
(NULL, 'Fortis Bank', '6300'),
(NULL, 'GE Money Bank', '0600'),
(NULL, 'HSBC Bank', '8150'),
(NULL, 'Hypoteční banka', '2100'),
(NULL, 'ING Bank', '3500'),
(NULL, 'J & T Banka', '5800'),
(NULL, 'Komerční banka', '0100'),
(NULL, 'LBBW Bank', '4000'),
(NULL, 'Modrá pyramida stavební spořitelna', '7990'),
(NULL, 'Moravský Peněžní Ústav – spořitelní družstvo', '2070'),
(NULL, 'Oberbank', '8040'),
(NULL, 'Peněžní dům, spořitelní družstvo', '2200'),
(NULL, 'Poštová banka', '2240'),
(NULL, 'PPF banka', '6000'),
(NULL, 'PRIVAT BANK', '8200'),
(NULL, 'Raiffeisen stavební spořitelna', '7950'),
(NULL, 'Raiffeisenbank', '5500'),
(NULL, 'Raiffeisenbank im Stiftland', '8030'),
(NULL, 'Royal Bank of Scotland', '5400'),
(NULL, 'Stavební spořitelna České spořitelny', '8060'),
(NULL, 'UNIBON – spořitelní a úvěrní družstvo', '2040'),
(NULL, 'UniCredit Bank', '2700'),
(NULL, 'Volksbank', '6800'),
(NULL, 'Všeobecná úverová banka', '6700'),
(NULL, 'Waldviertler Sparkasse von 1842', '7940'),
(NULL, 'WPB Capital, spořitelní družstvo', '2050'),
(NULL, 'Wüstenrot hypoteční banka', '7980'),
(NULL, 'Wüstenrot stavební spořitelna', '7970'),
(NULL, 'Záložna CREDITAS, spořitelní družstvo', '2250');
