START TRANSACTION;
INSERT INTO `vic_main`.`controller_convert` (`c_id`, `lang_id`, `parent_id`, `poradi`, `zobrazeno`, `cols`, `left_side`, `right_side`, `title`, `description`, `text`, `req_controller`, `req_action`, `real_controller`, `real_action`, `after_login`, `actionless`) VALUES
(76, 1, 0, 4, 1, 3, '', '', '', '', 'My account', 'tisk-live-tiketu', 'index', 'Myaccount', 'print-live-slip', 1, 0),
(76, 2, 0, 4, 1, 3, '', '', '', '', 'My account', 'print-live-slip', 'index', 'Myaccount', 'print-live-slip', 1, 0),
(76, 16, 0, 4, 1, 3, '', '', '', '', 'My account', 'muj-ucet-sk', 'tisk-live-tiketu', 'Myaccount', 'print-live-slip', 1, 0);
COMMIT;
