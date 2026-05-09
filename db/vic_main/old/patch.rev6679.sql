START TRANSACTION;

INSERT INTO `vic_main`.`controller_convert` (`c_id`, `lang_id`, `parent_id`, `poradi`, `zobrazeno`, `cols`, `left_side`, `right_side`, `title`, `description`, `text`, `req_controller`, `req_action`, `real_controller`, `real_action`, `after_login`, `actionless`) VALUES
(86, 1, 0, 1, 1, 3, '', '', '', '', 'Ajax', 'ajax-menu', 'index', 'Ajax', 'sport-menu', 1, 1),
(86, 2, 0, 1, 1, 3, '', '', '', '', 'Ajax', 'ajax-menu', 'index', 'Ajax', 'sport-menu', 1, 1),
(86, 16, 0, 1, 1, 3, '', '', '', '', 'Ajax', 'ajax-menu', 'index', 'Ajax', 'sport-menu', 1, 1);

COMMIT;
