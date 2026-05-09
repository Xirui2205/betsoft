ALTER TABLE `vic_main`.`controller_convert` ADD COLUMN `after_login` BOOLEAN NOT NULL DEFAULT 1;
START TRANSACTION;
DELETE FROM `vic_main`.`controller_convert` WHERE `c_id` IN (0,46);
INSERT INTO `vic_main`.`controller_convert` (`c_id`, `lang_id`, `parent_id`, `poradi`, `zobrazeno`, `cols`, `left_side`, `right_side`, `title`, `description`, `text`, `req_controller`, `req_action`, `real_controller`, `real_action`, `after_login`) VALUES
(63, 1, 1, 7, 1, 3, NULL, NULL, NULL, NULL, 'Odhlášení', 'odhlasen', 'do', 'logoff', 'do', 0),
(63, 2, 1, 7, 1, 3, NULL, NULL, NULL, NULL, 'Logged off', 'log-off', 'do', 'logoff', 'do', 0),
(63, 16, 1, 7, 1, 3, NULL, NULL, NULL, NULL, 'Odhlášenie', 'odhlasen-sk', 'do', 'logoff', 'do', 0),
(46, 1, 0, 1, 1, 3, NULL, NULL, '404 - Stránka nenalezena', NULL, 'Stránka nenalezena', '404', 'index', 'static-page', 'not-found', 0),
(46, 2, 0, 1, 1, 3, NULL, NULL, '404 - Not found', NULL, 'Page not found', '404', 'index', 'static-page', 'not-found', 0),
(46, 16, 0, 1, 1, 3, NULL, NULL, '404 - Stránka nenalezena', NULL, 'Stránka nenalezena', '404', 'index', 'static-page', 'not-found', 0);
UPDATE `vic_main`.`controller_convert` SET `after_login`=0 WHERE `c_id` IN (36,47,49);
COMMIT;
