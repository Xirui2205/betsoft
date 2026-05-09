START TRANSACTION;

INSERT INTO `controller_convert`
(`c_id`, `lang_id`, `parent_id`, `poradi`, `zobrazeno`, `cols`, `left_side`, `right_side`, `title`, `description`, `keywords`, `text`, `req_controller`, `req_action`, `real_controller`, `real_action`, `after_login`, `actionless`, `nonassoc_params`) VALUES  (111, 1, 0, 1, 1, 3, '', '', NULL, NULL, NULL, '', 'ajax', 'ticket-pin', 'ajax', 'ticket-pin', 1, 0, 0),
(111, 2, 0, 1, 1, 3, '', '', NULL, NULL, NULL, '', 'ajax', 'ticket-pin', 'ajax', 'ticket-pin', 1, 0, 0),
(111, 16, 0, 1, 1, 3, '', '', NULL, NULL, NULL, '', 'ajax', 'ticket-pin', 'ajax', 'ticket-pin', 1, 0, 0);

COMMIT;
