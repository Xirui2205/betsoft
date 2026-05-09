INSERT INTO `vic_main`.`database_patch`(`revision`, `not_reapplicable`, `note`)
VALUES ('H2048', 1, 'nacitani oblasti po blocich (mantis:944)');

INSERT INTO `controller_convert` (`c_id`, `lang_id`, `parent_id`, `poradi`, `zobrazeno`, `cols`, `left_side`, `right_side`, `title`, `description`, `keywords`, `text`, `req_controller`, `req_action`, `real_controller`, `real_action`, `after_login`, `actionless`, `nonassoc_params`) VALUES
(96, 1, 0, 1, 1, 3, '', '', '', '', NULL, 'Ajax', 'ajax-menu-all', 'index', 'Ajax', 'sport-menu-all', 1, 1, 1),
(96, 2, 0, 1, 1, 3, '', '', '', '', NULL, 'Ajax', 'ajax-menu-all', 'index', 'Ajax', 'sport-menu-all', 1, 1, 1),
(96, 16, 0, 1, 1, 3, '', '', '', '', NULL, 'Ajax', 'ajax-menu-all', 'index', 'Ajax', 'sport-menu-all', 1, 1, 1);

