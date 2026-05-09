INSERT INTO `vic_main`.`database_patch`(`revision`, `not_reapplicable`, `note`)
VALUES ('h2621', 1, 'added menu Favorites');

INSERT INTO `vic_main`.`preklady` (`lang_id` ,`index_pole` ,`short_text` ,`text` ,`translate` ,`preklad_id`) VALUES ('1', 'sport_offer_fav', NULL , 'Oblíbené', '1', '');

DROP TABLE IF EXISTS `vic_main`.`user_favorites`;
CREATE TABLE `vic_main`.`user_favorites` (
  `id` char(40) NOT NULL,
  `type` char(1) NOT NULL,
  `updated` datetime NOT NULL,
  `content` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `type_updated` (`type`,`updated`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `vic_main`.`controller_convert` (`c_id`, `lang_id`, `parent_id`, `poradi`, `zobrazeno`, `cols`, `left_side`, `right_side`, `title`, `description`, `keywords`, `text`, `req_controller`, `req_action`, `real_controller`, `real_action`, `after_login`, `actionless`, `nonassoc_params`) VALUES
(99,	1,	0,	1,	1,	3,	'',	'',	'',	'',	NULL,	'Ajax',	'ajax-menu-fav',	'index',	'Ajax',	'smf',	1,	1,	1),
(99,	2,	0,	1,	1,	3,	'',	'',	'',	'',	NULL,	'Ajax',	'ajax-menu-fav',	'index',	'Ajax',	'smf',	1,	1,	1),
(99,	16,	0,	1,	1,	3,	'',	'',	'',	'',	NULL,	'Ajax',	'ajax-menu-fav',	'index',	'Ajax',	'smf',	1,	1,	1);
