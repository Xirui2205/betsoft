start transaction;

INSERT INTO `controller_convert` (`c_id`, `lang_id`, `parent_id`, `poradi`, `zobrazeno`, `cols`, `left_side`, `right_side`, `title`, `description`, `keywords`, `text`, `req_controller`, `req_action`, `real_controller`, `real_action`, `after_login`, `actionless`, `nonassoc_params`) VALUES
(103, 1, 16, 4, 1, 3, NULL, NULL, NULL, NULL, NULL, 'Nastavení limitů', 'muj-ucet', 'nastaveni-limitu', 'Myaccount', 'setting-limits', 1, 0, 0),
(103, 2, 16, 4, 1, 3, NULL, NULL, NULL, NULL, NULL, 'Setting limits', 'my-detail', 'setting-limits', 'Myaccount', 'setting-limits', 1, 0, 0),
(103, 16, 16, 4, 1, 3, NULL, NULL, NULL, NULL, NULL, 'Nastavenie limitov', 'muj-ucet-sk', 'nastavenie-limitov', 'Myaccount', 'setting-limits', 1, 0, 0);

commit;
