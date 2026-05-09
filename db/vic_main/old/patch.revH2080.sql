INSERT INTO `vic_main`.`database_patch`(`revision`, `not_reapplicable`, `note`)
VALUES (2080, 1, 'change typ_sport to typ_udalost');



CREATE TABLE IF NOT EXISTS `vic_main`.`typ_udalost` (
  `typ_id` int(10) unsigned NOT NULL DEFAULT '0',
  `udalost_id` int(10) unsigned NOT NULL DEFAULT '0',
  `is_binded` BOOLEAN NOT NULL DEFAULT '0',
  `is_default` smallint(5) unsigned NOT NULL DEFAULT '0',
  `order` smallint(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`typ_id`,`udalost_id`),
  KEY `i__typ_udalost__udalost_id` (`udalost_id`),
  KEY `i__typ__typ_id` (`typ_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='InnoDB free: 21504 kB; InnoDB free: 105472 kB; (`sport_id`) ';

ALTER TABLE `vic_main`.`typ_udalost` ADD CONSTRAINT `typ_udalost_ibfk_1` FOREIGN KEY (`typ_id`) REFERENCES `typ` (`typ_id`);

INSERT INTO `vic_main`.`typ_udalost` (`typ_id`,`udalost_id`,`is_default`,`is_binded`,`order`)
SELECT ts.typ_id, u.udalost_id, ts.vychozi, 1, ts.poradi
FROM `vic_main`.`typ_sport` ts
JOIN `vic_main`.`udalost` u ON ts.sport_id = u.sport_id;

DROP TABLE `vic_main`.`typ_sport`;



CREATE TABLE IF NOT EXISTS `vic_main`.`typ_order_type` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

INSERT INTO `vic_main`.`typ_order_type` (`id`, `name`) VALUES
(1, 'rate_asc'),
(2, 'alias_rate_asc'),
(3, 'treshold_asc');

ALTER TABLE `vic_main`.`typ` CHANGE `order_type` `order_type` VARCHAR( 256 ) NULL DEFAULT NULL;
UPDATE `vic_main`.`typ` SET order_type=1 WHERE order_type='rate_asc';
UPDATE `vic_main`.`typ` SET order_type=2 WHERE order_type='alias_rate_asc';
UPDATE `vic_main`.`typ` SET order_type=3 WHERE order_type='threshold_asc';
ALTER TABLE `vic_main`.`typ` CHANGE `order_type` `order_type_id` INT UNSIGNED NULL DEFAULT '1';
ALTER TABLE `vic_main`.`typ` ADD INDEX `i__typ__order_type` ( `order_type_id` );
ALTER TABLE `vic_main`.`typ_order_type` ADD UNIQUE `u__typ_order_type` ( `name` );

ALTER TABLE `vic_main`.`typ` ADD FOREIGN KEY ( `order_type_id` )
REFERENCES `vic_main`.`typ_order_type` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

CREATE OR REPLACE
  DEFINER = CURRENT_USER 
  SQL SECURITY DEFINER
VIEW `vic_main`.`view_typ_id` AS
  SELECT
    `t`.`nazev` AS `nazev`,
    `t`.`typ_id` AS `typ_id`,
    `te`.`udalost_id` AS `udalost_id`
  FROM `typ` `t`
  JOIN `typ_udalost` `te`
    ON `t`.`typ_id` = `te`.`typ_id`
  WHERE `te`.`is_binded` = 1;