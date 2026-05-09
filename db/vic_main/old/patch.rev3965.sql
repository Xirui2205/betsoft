start transaction;
INSERT INTO vic_main.`controller_convert` (`c_id`, `lang_id`, `parent_id`, `poradi`, `zobrazeno`, `cols`, `left_side`, `right_side`, `title`, `description`, `text`, `req_controller`, `req_action`, `real_controller`, `real_action`, `after_login`, `actionless`) VALUES
(73, 1, 0, 19, 0, 3, NULL, NULL, NULL, NULL, '', 'live-login', 'index', 'live-login', 'index', 1, 0),
(73, 2, 0, 19, 0, 3, NULL, NULL, NULL, NULL, '', 'live-login', 'index', 'live-login', 'index', 1, 0),
(73, 16, 0, 19, 0, 3, NULL, NULL, NULL, NULL, '', 'live-login', 'index', 'live-login', 'index', 1, 0);
commit;
