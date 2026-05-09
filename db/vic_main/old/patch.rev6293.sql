START TRANSACTION;

INSERT INTO `vic_main`.`controller_convert` (`c_id`, `lang_id`, `parent_id`, `poradi`, `zobrazeno`, `cols`, `left_side`, `right_side`, `title`, `description`, `text`, `req_controller`, `req_action`, `real_controller`, `real_action`, `after_login`, `actionless`) VALUES
(83, 1, 17, 1, 1, 3, '', '', '', '', 'Tiket detail', 'muj-ucet', 'anonym-tiket-detail', 'Myaccount', 'anonym-slip', 1, 0),
(83, 2, 17, 1, 1, 3, '', '', '', '', 'Tiket detail', 'my-detail', 'anonym-slip-detail', 'Myaccount', 'anonym-slip', 1, 0),
(83, 16, 17, 1, 1, 3, '', '', '', '', 'Tiket detail', 'muj-ucet-sk', 'anonym-tiket-detail', 'Myaccount', 'anonym-slip', 1, 0);

COMMIT;