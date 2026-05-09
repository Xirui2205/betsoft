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
('57', '1', '21', '1', '0', '0', NULL , NULL , NULL , NULL , 'Exchange points', 'deposit-withdraw', 'exchange-points', 'deposit', 'exchange-points'),
('57', '2', '21', '1', '0', '0', NULL , NULL , NULL , NULL , 'Exchange points', 'deposit-withdraw', 'exchange-points', 'deposit', 'exchange-points'),
('57', '16', '21', '1', '0', '0', NULL , NULL , NULL , NULL , 'Exchange points', 'deposit-withdraw', 'exchange-points', 'deposit', 'exchange-points'),
('58', '1', '21', '1', '0', '0', NULL , NULL , NULL , NULL , 'Point transactions', 'deposit-withdraw', 'point-transactions', 'deposit', 'point-transactions'),
('58', '2', '21', '1', '0', '0', NULL , NULL , NULL , NULL , 'Point transactions', 'deposit-withdraw', 'point-transactions', 'deposit', 'point-transactions'),
('58', '16', '21', '1', '0', '0', NULL , NULL , NULL , NULL , 'Point transactions', 'deposit-withdraw', 'point-transactions', 'deposit', 'point-transactions');
COMMIT;
