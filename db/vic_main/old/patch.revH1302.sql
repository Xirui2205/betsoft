START TRANSACTION;

INSERT INTO `vic_main`.`database_patch`(`revision`, `not_reapplicable`, `note`) VALUES
 ('H1302', 1, 'New translation resources for not cancelled tickets');

INSERT INTO `vic_main`.`preklady`(`lang_id`, `index_pole`, `short_text`, `text`, `translate`) VALUES
 (1, 'not_storno_ticket_count', NULL, 'Nezamítnuté tikety', 0),
 (1, 'not_storno_ticket_amount', NULL, 'Nezamítnuté tikety částka', 0),
 (1, 'point_not_storno_ticket_count', NULL, 'Nezamítnuté bodové tikety', 0),
 (1, 'point_not_storno_ticket_amount', NULL, 'Nezamítnuté bodové tikety částka)', 0);
 
COMMIT;
