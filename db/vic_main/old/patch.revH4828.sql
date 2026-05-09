START TRANSACTION;

INSERT INTO `database_patch`(`revision`, `not_reapplicable`, `note`) VALUES
 ('4828', NULL, 'mobilni sazeni - nova stranka (mantis:132)');

INSERT INTO `controller_convert` (`c_id`, `lang_id`, `parent_id`, `poradi`, `zobrazeno`, `cols`, `left_side`, `right_side`, `title`, `description`, `keywords`, `text`, `req_controller`, `req_action`, `real_controller`, `real_action`, `after_login`, `actionless`, `nonassoc_params`) VALUES
(113, 1, 0, 0, 1, 3, NULL, NULL, 'Mobílní sázení', NULL, NULL, '', 'mobilni-sazeni', 'index', 'mobile', 'index', 1, 0, 0);

COMMIT;