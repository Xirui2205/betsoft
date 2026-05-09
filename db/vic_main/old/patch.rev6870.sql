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
'87',  '1',  '0',  '4',  '1',  '3',  '',  '',  '',  '',  'Storno tiketu','muj-ucet','storno-tiketu',  'Myaccount',  'cancel-ticket',  '0',  '0'
), (
'87',  '2',  '0',  '4',  '1',  '3',  '',  '',  '',  '',  'Ticket cancelation', 'my-detail','ticket-cancelation',  'Myaccount',  'cancel-ticket',  '0',  '0'
), (
'87',  '16',  '0',  '4',  '1',  '3',  '',  '',  '',  '',  'Storno tiketu',  'muj-ucet-sk', 'storno-tiketu',  'Myaccount',  'cancel-ticket',  '0',  '0'
);

COMMIT;
