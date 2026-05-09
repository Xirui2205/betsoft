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
('55', '1', '0', '1', '0', '0', NULL , NULL , NULL , NULL , '', 'deposit-feedback', 'muzo', 'deposit-feedback', 'muzo'),
('55', '2', '0', '1', '0', '0', NULL , NULL , NULL , NULL , '', 'deposit-feedback', 'muzo', 'deposit-feedback', 'muzo'),
('55', '16', '0', '1', '0', '0', NULL , NULL , NULL , NULL , '', 'deposit-feedback', 'muzo', 'deposit-feedback', 'muzo');
COMMIT;
