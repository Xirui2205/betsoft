ALTER TABLE `vic_main`.`uzivatel`
 ADD COLUMN `entry_bonus_applied_at` DATETIME NULL DEFAULT NULL AFTER `entry_bonus_applied`;

START TRANSACTION;

INSERT INTO `vic_main`.`controller_convert` (`c_id`, `lang_id`, `parent_id`, `poradi`, `zobrazeno`, `cols`, `left_side`, `right_side`, `title`, `description`, `text`, `req_controller`, `req_action`, `real_controller`, `real_action`, `after_login`, `actionless`) VALUES
(85, 1, 0, 1, 1, 3, '', '', '', '', 'Vstupní bonus', 'muj-ucet', 'vstupni-bonus', 'Myaccount', 'entry-bonus', 1, 0),
(85, 2, 0, 1, 1, 3, '', '', '', '', 'Entry bonus', 'my-detail', 'entry-bonus', 'Myaccount', 'entry-bonus', 1, 0),
(85, 16, 0, 1, 1, 3, '', '', '', '', 'Vstupní bonus', 'muj-ucet-sk', 'vstupni-bonus', 'Myaccount', 'entry-bonus', 1, 0);

COMMIT;
