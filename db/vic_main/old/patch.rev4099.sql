START TRANSACTION;
INSERT INTO `vic_main`.`controller_convert` (`c_id`,`lang_id`, `parent_id`, `poradi`, `zobrazeno`,
	`cols`, `left_side`, `right_side`, `title`, `description`, `text`, `req_controller`, `req_action`,
	`real_controller`, `real_action`, `after_login`, `actionless`)
VALUES (
	'75', '1', '16', '2', '1', '3', NULL, NULL, NULL, NULL, 'Live tiket detail',
	'muj-ucet', 'live-slip', 'myaccount', 'live-slip', '0', '0'
), (
	'75', '2', '16', '2', '1', '3', NULL, NULL, NULL, NULL, 'Live tickets detail',
	'my-account', 'live-slip', 'myaccount', 'live-slip', '0', '0'
), (
	'75', '16', '16', '2', '1', '3', NULL, NULL, NULL, NULL, 'Live tiket detail',
	'muj-ucet', 'live-slip', 'myaccount', 'live-slip', '0', '0'
);
COMMIT;
