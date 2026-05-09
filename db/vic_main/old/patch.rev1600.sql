START TRANSACTION;
INSERT INTO `vic_main`.`controller_convert` (
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
`real_action`
)
VALUES
('56', '1', '0', '1', '0', '0', NULL , NULL , NULL , NULL , '', 'deposit-redirect', 'muzo', 'deposit-redirect', 'muzo'),
('56', '2', '0', '1', '0', '0', NULL , NULL , NULL , NULL , '', 'deposit-redirect', 'muzo', 'deposit-redirect', 'muzo'),
('56', '16', '0', '1', '0', '0', NULL , NULL , NULL , NULL , '', 'deposit-redirect', 'muzo', 'deposit-redirect', 'muzo');
COMMIT;
