
CREATE TABLE `vic_admin`.`betradar_import_log`(
 `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
 `sport` VARCHAR(64) NOT NULL COLLATE utf8_general_ci,
 `region` VARCHAR(128) NOT NULL COLLATE utf8_general_ci,
 `event` VARCHAR(128) NOT NULL COLLATE utf8_general_ci,
 `br_tournament_id` INT(10) UNSIGNED NOT NULL,
 `imported_at` TIMESTAMP NOT NULL
) Engine=MyISAM DEFAULT CHARSET utf8 COLLATE utf8_general_ci;

START TRANSACTION;

INSERT INTO `vic_admin`.`parameter` (
 `id`,
 `name`,
 `value`,
 `is_host`,
 `is_branch`,
 `is_user`,
 `is_admin`,
 `is_editable`,
 `type`,
 `mandatory`,
 `description`
) VALUES
(96, 'betradar.ImportLog.defaultMaxAge', '259200', '0', '0', '0', '1', '1', NULL , '1', 'Výchozí hodnota max.stáří logu betradar importu pro zobrazení (v sekundách)'),
(97, 'betradar.ImportLog.queriedLanguage', 'cs', '0', '0', '0', '0', '1', NULL , '1', 'Kód jazyka pro texty při logování betradar importu (např. "cs")');

UPDATE `vic_admin`.`sekce` SET `controller` = 'betradar-import-log', `action` = 'index' WHERE `sekce`.`sekce_id` =142;

COMMIT;
