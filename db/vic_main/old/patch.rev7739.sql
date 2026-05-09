
CREATE TABLE IF NOT EXISTS `sazka_kombinace_archive` (
  `archive_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `archived_on` date NOT NULL,
  `sazka1_id` int(10) NOT NULL,
  `sazka2_id` int(10) NOT NULL DEFAULT '0',
  `kombinace_ticket` int(6) NOT NULL DEFAULT '0',
  `kombinace_show` smallint(5) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`archive_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
