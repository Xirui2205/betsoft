START TRANSACTION;

INSERT INTO `controller_convert` (`c_id`, `lang_id`, `parent_id`, `poradi`, `zobrazeno`, `cols`, `left_side`, `right_side`, `title`, `description`, `keywords`, `text`, `req_controller`, `req_action`, `real_controller`, `real_action`, `after_login`, `actionless`, `nonassoc_params`) VALUES
(112, 1, 0, 1, 1, 0, NULL, NULL, NULL, NULL, NULL, 'Filter button', 'ajax', 'filter-button', 'ajax', 'filter-button', 1, 0, 0),
(112, 2, 0, 1, 1, 0, NULL, NULL, NULL, NULL, NULL, 'Filter button', 'ajax', 'filter-button', 'ajax', 'filter-button', 1, 0, 0),
(112, 16, 0, 1, 1, 0, NULL, NULL, NULL, NULL, NULL, 'Filter button', 'ajax', 'filter-button', 'ajax', 'filter-button', 1, 0, 0);

COMMIT;
