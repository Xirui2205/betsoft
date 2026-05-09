START TRANSACTION;

CREATE TABLE IF NOT EXISTS `bet_free_alias_new` (
  `alias` int(10) unsigned NOT NULL,
  PRIMARY KEY (`alias`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

ALTER TABLE `sazky` ADD `alias_new` INT( 11 ) NULL DEFAULT NULL COMMENT 'novy petimistny alias';

ALTER TABLE `sport` ADD `bet_alias_from_new` INT( 10 ) UNSIGNED NULL DEFAULT NULL COMMENT 'dolni hranice pro nove aliasy';
ALTER TABLE `sport` ADD `bet_alias_to_new` INT( 10 ) UNSIGNED NULL DEFAULT NULL COMMENT 'horni hranice pro nove aliasy';

COMMIT;
