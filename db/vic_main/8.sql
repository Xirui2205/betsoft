DROP TABLE IF EXISTS `currency`;
CREATE TABLE `currency` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `iso` char(4) NOT NULL,
  `enable` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `uzivatel`
ADD `currency_id` tinyint(3) unsigned NULL,
ADD FOREIGN KEY (`currency_id`) REFERENCES `currency` (`id`) ON DELETE NO ACTION,
COMMENT='';