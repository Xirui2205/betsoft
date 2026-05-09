START TRANSACTION;

UPDATE `vic_main`.`controller_convert` SET `req_controller` = 'ticket',
`req_action` = 'index',
`real_controller` = 'ticket',
`real_action` = 'index',
`after_login` = '1' WHERE `controller_convert`.`c_id` =83 AND `controller_convert`.`lang_id` =1;

UPDATE `vic_main`.`controller_convert` SET `req_controller` = 'ticket',
`req_action` = 'index',
`real_controller` = 'ticket',
`real_action` = 'index',
`after_login` = '1' WHERE `controller_convert`.`c_id` =83 AND `controller_convert`.`lang_id` =2;

UPDATE `vic_main`.`controller_convert` SET `req_controller` = 'ticket',
`req_action` = 'index',
`real_controller` = 'ticket',
`real_action` = 'index',
`after_login` = '1' WHERE `controller_convert`.`c_id` =83 AND `controller_convert`.`lang_id` =16;


COMMIT;