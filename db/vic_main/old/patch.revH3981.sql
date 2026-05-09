/*
Remove script (for testing this patch):

drop table article_tips;

drop table article;

delete from controller_convert where c_id in (105, 106, 107);

pozn.: vkladani prekladu otestovano zvlast do tabulky na prekladyTEST (na lokalu)
*/

START TRANSACTION;

-- vlozit zaznam do database_patch
INSERT INTO `vic_main`.`database_patch`(`revision`, `not_reapplicable`, `note`)
VALUES ('H3981', 1, 'Articles - main (mantis:105)');

CREATE TABLE IF NOT EXISTS `article_tips` (
  `article_id` int(10) unsigned NOT NULL,
  `sazka_id` int(10) unsigned NOT NULL,
  `sloupec_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`article_id`,`sazka_id`),
  KEY `sazka_id` (`sazka_id`),
  KEY `sloupec_id` (`sloupec_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `article` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `perex` text NOT NULL,
  `content` text NOT NULL,
  `priority` tinyint(1) NOT NULL DEFAULT '0',
  `sazka_alias_inserted` int(11) DEFAULT NULL,
  `sazka_id` int(10) unsigned DEFAULT NULL,
  `image_id` int(10) unsigned DEFAULT NULL,
  `admin_id` int(11) unsigned DEFAULT NULL,
  `public` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `published` datetime DEFAULT NULL,
  `lang_id` int(10) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `image_id` (`image_id`),
  KEY `lang_id` (`lang_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;

ALTER TABLE `article_tips`
  ADD CONSTRAINT `article_tips_ibfk_1` FOREIGN KEY (`article_id`) REFERENCES `article` (`id`)  ON DELETE CASCADE,
  ADD CONSTRAINT `article_tips_ibfk_2` FOREIGN KEY (`sazka_id`) REFERENCES `sazky` (`sazka_id`),
  ADD CONSTRAINT `article_tips_ibfk_3` FOREIGN KEY (`article_id`) REFERENCES `article` (`id`),
  ADD CONSTRAINT `article_tips_ibfk_4` FOREIGN KEY (`sazka_id`) REFERENCES `sazky` (`sazka_id`),
  ADD CONSTRAINT `article_tips_ibfk_5` FOREIGN KEY (`sloupec_id`) REFERENCES `podtyp_sloupce` (`sloupec_id`);

ALTER TABLE `article`
  ADD CONSTRAINT `article_ibfk_2` FOREIGN KEY (`lang_id`) REFERENCES `jazyky` (`lang_id`) ON DELETE NO ACTION,
  ADD CONSTRAINT `article_ibfk_1` FOREIGN KEY (`image_id`) REFERENCES `galerie` (`image_id`) ON DELETE NO ACTION;

INSERT INTO `controller_convert`
(`c_id`, `lang_id`, `parent_id`, `poradi`, `zobrazeno`, `cols`, `left_side`, `right_side`, `title`, `description`, `keywords`, `text`, `req_controller`, `req_action`, `real_controller`, `real_action`, `after_login`, `actionless`, `nonassoc_params`) VALUES
(105,		1,		0,				1,		1,				3,			'',	'',				'Články','Informace o nových sázkových příležitostech. Bookmaker doporučuje, na co vsadit.',	'sázení novinky tipy rady',	'Články',	'clanky',	'index',	'article',	'index',	1,	0,	0),
(106,	1,	105,	1,				1,		3,			'',	'',	NULL,	NULL,	NULL,	'Článek',	'clanek',	'index',	'article',	'read',	1,	1,	1),
(107,	1,	105,	1,				1,		3,			'',	'',	NULL,	NULL,	NULL,	'',	'article',	'articles-hp',	'article',	'articles-hp',	1,	0,	0),

(105,		2,		0,				1,		1,				3,			'',	'',				'Articles','',	'',	'Articles',	'articles',	'index',	'article',	'index',	1,	0,	0),
(106,	2,	105,	1,				1,		3,			'',	'',	NULL,	NULL,	NULL,	'Article',	'article',	'index',	'article',	'read',	1,	1,	1),
(107,	2,	105,	1,				1,		3,			'',	'',	NULL,	NULL,	NULL,	'',	'article',	'articles-hp',	'article',	'articles-hp',	1,	0,	0),

(105,		16,		0,				1,		1,				3,			'',	'',				'Články','',	'',	'Články',	'clanky',	'index',	'article',	'index',	1,	0,	0),
(106,	16,	105,	1,				1,		3,			'',	'',	NULL,	NULL,	NULL,	'Článok',	'clanok',	'index',	'article',	'read',	1,	1,	1),
(107,	16,	105,	1,				1,		3,			'',	'',	NULL,	NULL,	NULL,	'',	'article',	'articles-hp',	'article',	'articles-hp',	1,	0,	0);

-- aktualni maximalni preklad_id
SELECT @idPrekladyMax := MAX(preklad_id) FROM vic_main.preklady;

INSERT INTO `vic_main`.`preklady` (`lang_id`, `index_pole`,
`short_text`, `text`, `translate`, `preklad_id`) VALUES
(1,	'article',		'',	'Článek',		0,	(@idPrekladyMax := @idPrekladyMax+1) ),
(1,	'articles',		'',	'Články',		0,	(@idPrekladyMax := @idPrekladyMax+1) ),
(1,	'article_title',		'',	'Titulek',		0,	(@idPrekladyMax := @idPrekladyMax+1) ),
(1,	'article_perex',	'',	'Perex',		0,	(@idPrekladyMax := @idPrekladyMax+1)),
(1,	'article_published_from',		'',	'Publikováno od',		0,	(@idPrekladyMax := @idPrekladyMax+1)),
(1,	'article_published_to',	'',	'Publikováno do',		0,	(@idPrekladyMax := @idPrekladyMax+1)),
(1,	'article_published',	'',	'Publikovat od',	0,	(@idPrekladyMax := @idPrekladyMax+1)),
(1,	'article_public',	'',	'Publikovat',	0,	(@idPrekladyMax := @idPrekladyMax+1)),


(1,	'article_content',	'',	'Obsah článku',		0,	(@idPrekladyMax := @idPrekladyMax+1)),
(1,	'article_priority',	'',	'Prioritní',		0,	(@idPrekladyMax := @idPrekladyMax+1)),
(1,	'article_priority_set',	'',	'Nastavit prioritu',		0,	(@idPrekladyMax := @idPrekladyMax+1)),
(1,	'article_priority_remove',	'',	'Zrušit prioritu',	0,	(@idPrekladyMax := @idPrekladyMax+1)),
(1,	'article_image', 	'',	'Obrázek', 		0,	(@idPrekladyMax := @idPrekladyMax+1)),
(1,	'article_image_remove', '',	'Odebrat', 		0,	(@idPrekladyMax := @idPrekladyMax+1)),
(1,	'article_image_choose', '',	'Vybrat',		0,	(@idPrekladyMax := @idPrekladyMax+1)),
(1,	'article_image_add', '',	'Přidat nový',		0,	(@idPrekladyMax := @idPrekladyMax+1)),

(1,	'article_insert',	'',	'Přidat článek',		0,	(@idPrekladyMax := @idPrekladyMax+1)),
(1,	'article_update',	'',	'Upravit článek',		0,	(@idPrekladyMax := @idPrekladyMax+1)),
(1,	'article_delete',	'',	'Smazat článek',		0,	(@idPrekladyMax := @idPrekladyMax+1)),
(1,	'article_preview',	'',	'Zobrazit na webu',		0,	(@idPrekladyMax := @idPrekladyMax+1)),
(1,	'manage',	'',	'Spravovat',			0,	(@idPrekladyMax := @idPrekladyMax+1)),
(1,	'close',	'',	'Zavřít',			0,	(@idPrekladyMax := @idPrekladyMax+1)),

(1,	'article_sazka_alias_inserted',	'',	'Alias sázky',		0,	(@idPrekladyMax := @idPrekladyMax+1)),
(1,	'article_sazka_id',		'',	'ID sázky',		0,	(@idPrekladyMax := @idPrekladyMax+1)),
(1,	'article_sazka_tips',		'',	'Tipy',			0,	(@idPrekladyMax := @idPrekladyMax+1)),
(1,	'article_sazka_tips_all',	'',	'Podpůrné sázky',	0,	(@idPrekladyMax := @idPrekladyMax+1)),

(1,	'first_page',	'',	'První stránka',		0,	(@idPrekladyMax := @idPrekladyMax+1)),
(1,	'last_page',	'',	'Poslední stránka',		0,	(@idPrekladyMax := @idPrekladyMax+1)),

(1,	'\'%value%\' is less than %min% characters long',	'',	'\'%value%\' je kratší než %min% znaků',		0,	(@idPrekladyMax := @idPrekladyMax+1)),

(1,	'\'%value%\' is more than %max% characters long',	'',	'\'%value%\' je delší než %max% znaků',		0,	(@idPrekladyMax := @idPrekladyMax+1)),

/* custom hlaska, hodi se pro html nebo delsi text */
(1,	'Vložte minimálně %min%, maximálně %max% znaků.',	'',	'Vložte minimálně %min%, maximálně %max% znaků.',		0,	(@idPrekladyMax := @idPrekladyMax+1)),
(2,	'Vložte minimálně %min%, maximálně %max% znaků.',	'',	'Entered text should be from %min% to %max% characters long.',		0,	(@idPrekladyMax := @idPrekladyMax+1)),

(1,	'search_in_bet_offers',	'',	'Nabídka obsahuje text ...',		0,	(@idPrekladyMax := @idPrekladyMax+1)),

(1,	'username_short',	'',	'Login',		0,	(@idPrekladyMax := @idPrekladyMax+1)),

(1,	'time_range_offer',	'',	'Zvolit ...',		0,	(@idPrekladyMax := @idPrekladyMax+1)),

(1,	'bookmaker_tips',	'',	'Tipy bookmakera',		0,	(@idPrekladyMax := @idPrekladyMax+1)),
(1,	'alias',	'',	'Alias',		0,	(@idPrekladyMax := @idPrekladyMax+1)),
(1,	'sport_articles',	'',	'Sportovní články',		0,	(@idPrekladyMax := @idPrekladyMax+1)),
(1,	'article_archive',	'',	'Archiv',		0,	(@idPrekladyMax := @idPrekladyMax+1)),

(1,	'datetimerange_quick_offer',	'',	'Zvolené období',		0,	(@idPrekladyMax := @idPrekladyMax+1)),
(1,	'bet_category',	'',	'Kategorie',		0,	(@idPrekladyMax := @idPrekladyMax+1)),
(1,	'bet_result',	'',	'Výsledek',		0,	(@idPrekladyMax := @idPrekladyMax+1)),

(1,	'sport_menu_no_item',	'',	'Zvoleným kritériím neodpovídá žádná sázková příležitost.',		0,	(@idPrekladyMax := @idPrekladyMax+1)),
(1,	'timefilter_menu_header',	'',	'Rychlá nabídka',		0,	(@idPrekladyMax := @idPrekladyMax+1)),
(1,	'odds_range_menu_header',	'',	'Nabídka podle kurzů',		0,	(@idPrekladyMax := @idPrekladyMax+1)),
(1,	'from_simple',	'',	'Od',		0,	(@idPrekladyMax := @idPrekladyMax+1)),
(1,	'to_simple',	'',	'Do',		0,	(@idPrekladyMax := @idPrekladyMax+1)),

(1,	'article_publish_now',	'',	'Publikovat nyní',		0,	(@idPrekladyMax := @idPrekladyMax+1));

/* vyuzito pro skryti supertipu v menu */
ALTER TABLE `sport` ADD `hide_in_sportmenu` BOOLEAN NOT NULL DEFAULT FALSE;
UPDATE `vic_main`.`sport` SET `hide_in_sportmenu` = '1' WHERE `sport`.`sport_id` =1056;

COMMIT;
