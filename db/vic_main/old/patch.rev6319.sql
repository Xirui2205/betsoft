START TRANSACTION;

INSERT INTO `vic_main`.`controller_convert` (`c_id`, `lang_id`, `parent_id`, `poradi`, `zobrazeno`, `cols`, `left_side`, `right_side`, `title`, `description`, `text`, `req_controller`, `req_action`, `real_controller`, `real_action`, `after_login`, `actionless`) VALUES
(84, 1, 0, 1, 1, 3, '', '', '', '', 'Ajax', 'ajax-sazky', 'index', 'Ajax', 'sportsbook', 1, 1),
(84, 2, 0, 1, 1, 3, '', '', '', '', 'Ajax', 'ajax-sportsbook', 'index', 'Ajax', 'sportsbook', 1, 1),
(84, 16, 0, 1, 1, 3, '', '', '', '', 'Ajax', 'ajax-sazky', 'index', 'Ajax', 'sportsbook', 1, 1);

COMMIT;
