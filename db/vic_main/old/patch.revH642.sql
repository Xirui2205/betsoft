START TRANSACTION;

INSERT INTO `vic_main`.`database_patch`(`revision`, `not_reapplicable`, `note`)
VALUES (642, 1, 'adding is_top parameter to branch');


INSERT INTO  `vic_main`.`controller_convert` (
`c_id` ,
`lang_id` ,
`parent_id` ,
`poradi` ,
`zobrazeno` ,
`cols` ,
`left_side` ,
`right_side` ,
`title` ,
`description` ,
`text` ,
`req_controller` ,
`req_action` ,
`real_controller` ,
`real_action` ,
`after_login` ,
`actionless`
)
VALUES
(93,  1, 1, 1, 1, 3, NULL, NULL, NULL, NULL, 'branch - all branches', 'pobocky', 'vsechny-pobocky', 'branch', 'all-branches', '1', '0'),
(93,  2, 1, 1, 1, 3, NULL, NULL, NULL, NULL, 'branch - all branches', 'branches', 'all-branches', 'branch', 'all-branches', '1', '0'),
(93, 16, 1, 1, 1, 3, NULL, NULL, NULL, NULL, 'branch - all branches', 'pobocky', 'vsechny-pobocky', 'branch', 'all-branches', '1', '0');


COMMIT;
