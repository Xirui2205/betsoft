START TRANSACTION;

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
(79,  1, 1, 8, 1, 3, NULL, NULL, NULL, NULL, 'head', 'global-cache-frame', 'head', 'global-cache-frame', 'head', '0', '0'),
(79,  2, 1, 8, 1, 3, NULL, NULL, NULL, NULL, 'head', 'global-cache-frame', 'head', 'global-cache-frame', 'head', '0', '0'),
(79, 16, 1, 8, 1, 3, NULL, NULL, NULL, NULL, 'head', 'global-cache-frame', 'head', 'global-cache-frame', 'head', '0', '0');

COMMIT;
