INSERT INTO `vic_main`.`database_patch`(`revision`, `not_reapplicable`, `note`) VALUES
 ('H162', 1, 'Changes for pages metadata management (mantis:618)');

ALTER TABLE `vic_main`.`controller_convert` ADD COLUMN `keywords` TEXT COLLATE utf8_general_ci NULL AFTER `description`;

START TRANSACTION;

UPDATE vic_main.controller_convert c
 JOIN vic_main.static_page p
 ON c.lang_id=p.lang_id
  AND c.real_controller='static-page'
  AND c.real_action COLLATE utf8_general_ci=p.page_name
 SET c.title=p.page_title, c.description=p.page_description, c.keywords=p.page_keywords;

COMMIT;

ALTER TABLE `vic_main`.`static_page` DROP COLUMN `page_title`, DROP COLUMN `page_description`, DROP COLUMN `page_keywords`;
