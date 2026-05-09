INSERT INTO vic_main.`database_patch`(`revision`, `not_reapplicable`, `note`) VALUES (3252, 1, 'newspapers in footer');

CREATE TABLE IF NOT EXISTS `newspapers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `valid_to` date NOT NULL,
  `filename` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

INSERT INTO `vic_main`.`controller_convert` (`c_id`, `lang_id`, `parent_id`, `poradi`, `zobrazeno`, `cols`, `left_side`, `right_side`, `title`, `description`, `keywords`, `text`, `req_controller`, `req_action`, `real_controller`, `real_action`, `after_login`, `actionless`, `nonassoc_params`) VALUES ('102', '1', '', '', '1', '3', NULL, NULL, NULL, NULL, NULL, '', 'global-cache-frame', 'foot', 'global-cache-frame', 'foot', '1', '0', '0');
INSERT INTO `vic_main`.`controller_convert` (`c_id`, `lang_id`, `parent_id`, `poradi`, `zobrazeno`, `cols`, `left_side`, `right_side`, `title`, `description`, `keywords`, `text`, `req_controller`, `req_action`, `real_controller`, `real_action`, `after_login`, `actionless`, `nonassoc_params`) VALUES ('102', '2', '', '', '1', '3', NULL, NULL, NULL, NULL, NULL, '', 'global-cache-frame', 'foot', 'global-cache-frame', 'foot', '1', '0', '0');
INSERT INTO `vic_main`.`controller_convert` (`c_id`, `lang_id`, `parent_id`, `poradi`, `zobrazeno`, `cols`, `left_side`, `right_side`, `title`, `description`, `keywords`, `text`, `req_controller`, `req_action`, `real_controller`, `real_action`, `after_login`, `actionless`, `nonassoc_params`) VALUES ('102', '16', '', '', '1', '3', NULL, NULL, NULL, NULL, NULL, '', 'global-cache-frame', 'foot', 'global-cache-frame', 'foot', '1', '0', '0');