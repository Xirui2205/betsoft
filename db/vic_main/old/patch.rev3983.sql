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
VALUES (
'74',  '1',  '0',  '4',  '1',  '3',  '',  '',  '',  '',  'My account',  'tisk-tiketu',  'index',  'Myaccount',  'print-slip',  '1',  '0'
), (
'74',  '2',  '0',  '4',  '1',  '3',  '',  '',  '',  '',  'My account',  'print-slip',  'index',  'Myaccount',  'print-slip',  '1',  '0'
), (
'74',  '16',  '0',  '4',  '1',  '3',  '',  '',  '',  '',  'My account',  'muj-ucet-sk',  'tisk-tiketu',  'Myaccount',  'print-slip',  '1',  '0'
);

COMMIT;
