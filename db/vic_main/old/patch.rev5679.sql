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
(80,  1, 0, 1, 1, 3, NULL, NULL, NULL, NULL, 'tickets_of_month', 'ajax', 'tickets-of-month', 'ajax', 'tickets-of-month', 0, 0),
(80,  2, 0, 1, 1, 3, NULL, NULL, NULL, NULL, 'tickets_of_month', 'ajax', 'tickets-of-month', 'ajax', 'tickets-of-month', 0, 0),
(80, 16, 0, 1, 1, 3, NULL, NULL, NULL, NULL, 'tickets_of_month', 'ajax', 'tickets-of-month', 'ajax', 'tickets-of-month', 0, 0),

(81,  1, 0, 1, 1, 3, NULL, NULL, NULL, NULL, 'last_minute_tab', 'ajax', 'last-minute-tab', 'ajax', 'last-minute-tab', 0, 0),
(81,  2, 0, 1, 1, 3, NULL, NULL, NULL, NULL, 'last_minute_tab', 'ajax', 'last-minute-tab', 'ajax', 'last-minute-tab', 0, 0),
(81, 16, 0, 1, 1, 3, NULL, NULL, NULL, NULL, 'last_minute_tab', 'ajax', 'last-minute-tab', 'ajax', 'last-minute-tab', 0, 0),

(82,  1, 0, 1, 1, 3, NULL, NULL, NULL, NULL, 'terno_tab', 'ajax', 'terno-tab', 'ajax', 'terno-tab', 0, 0),
(82,  2, 0, 1, 1, 3, NULL, NULL, NULL, NULL, 'terno_tab', 'ajax', 'terno-tab', 'ajax', 'terno-tab', 0, 0),
(82, 16, 0, 1, 1, 3, NULL, NULL, NULL, NULL, 'terno_tab', 'ajax', 'terno-tab', 'ajax', 'terno-tab', 0, 0)
;

COMMIT;
