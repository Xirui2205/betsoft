START TRANSACTION;
INSERT INTO `vic_main`.`controller_convert` (`c_id`, `lang_id`, `parent_id`, `poradi`, `zobrazeno`, `cols`, `left_side`, `right_side`, `title`, `description`, `text`, `req_controller`, `req_action`, `real_controller`, `real_action`, `after_login`) VALUES
(48, 2, 0, 1, 1, 3, NULL, NULL, 'Maxicombinator detail', NULL, 'Maxicombinator detail', 'maxicombinator', 'detail', 'ajax', 'maxicombinator-detail', 1),
(48, 16, 0, 1, 1, 3, NULL, NULL, 'Podrobnosti maxikombinátora', NULL, 'Podrobnosti maxikombinátora', 'maxikombinator', 'detail', 'ajax', 'maxicombinator-detail', 1);
UPDATE `vic_main`.`controller_convert` SET `title`='Podrobnosti maxikombinátoru', `text`='Podrobnosti maxikombinátoru',`req_controller`='maxikombinator' WHERE `c_id`=48 AND `lang_id`=1;
COMMIT;
