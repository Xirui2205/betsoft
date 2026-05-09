START TRANSACTION;


INSERT INTO `vic_main`.`database_patch`(`revision`, `not_reapplicable`,
`note`) VALUES
 ('7411', null, 'added "info", "longitude" and "latitide" cols to vic_admin.branch');


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
(89,  1, 1, 1, 1, 3, NULL, NULL, NULL, NULL, 'AJAX branch detail', 'ajax', 'branch-detail', 'ajax', 'branch-detail', '1', '0'),
(89,  2, 1, 1, 1, 3, NULL, NULL, NULL, NULL, 'AJAX branch detail', 'ajax', 'branch-detail', 'ajax', 'branch-detail', '1', '0'),
(89, 16, 1, 1, 1, 3, NULL, NULL, NULL, NULL, 'AJAX branch detail', 'ajax', 'branch-detail', 'ajax', 'branch-detail', '1', '0');


COMMIT;

