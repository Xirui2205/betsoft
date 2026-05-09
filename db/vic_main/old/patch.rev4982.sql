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
'77',  '1',  '0',  '4',  '1',  '3',  '',  '',  '',  '',  'Provider Registration',  'registrace-poskytovatele',  'index',  'provider-registration',  'index',  '0',  '0'
), (
'77',  '2',  '0',  '4',  '1',  '3',  '',  '',  '',  '',  'Provider Registration',  'provider-registration',  'index',  'provider-registration',  'index',  '0',  '0'
), (
'77',  '16',  '0',  '4',  '1',  '3',  '',  '',  '',  '',  'Provider Registration',  'registrace-poskytovatele-sk',  'index',  'provider-registration',  'index',  '0',  '0'
);

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
'78',  '1',  '0',  '4',  '1',  '3',  '',  '',  '',  '',  'Provider Registration',  'registrace-poskytovatele',  'registrovat',  'provider-registration',  'register',  '0',  '0'
), (
'78',  '2',  '0',  '4',  '1',  '3',  '',  '',  '',  '',  'Provider Registration',  'provider-registration',  'register',  'provider-registration',  'register',  '0',  '0'
), (
'78',  '16',  '0',  '4',  '1',  '3',  '',  '',  '',  '',  'Provider Registration',  'registrace-poskytovatele-sk',  'registrovat-sk',  'provider-registration',  'register',  '0',  '0'
);


COMMIT;