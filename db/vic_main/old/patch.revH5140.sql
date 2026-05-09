START TRANSACTION;

INSERT INTO `database_patch`(`revision`, `not_reapplicable`, `note`) VALUES
 ('H5140', 1, 'promo - bet alias (mantis:219)');

ALTER TABLE `promo`
ADD `alias` int(11) unsigned NULL AFTER `url`,
COMMENT='';

-- aktualni maximalni preklad_id
SELECT @idPrekladyMax := MAX(preklad_id) FROM preklady;

INSERT INTO `preklady` (`lang_id`, `index_pole`,
`short_text`, `text`, `translate`, `preklad_id`) VALUES
(1,	'load',		'',	'Načíst ...',		0,	(@idPrekladyMax := @idPrekladyMax+1) );

COMMIT;