START TRANSACTION;

UPDATE  `vic_main`.`controller_convert` SET  `actionless` =  '1' WHERE  `controller_convert`.`c_id` =27 AND  `controller_convert`.`lang_id` =1;
UPDATE  `vic_main`.`controller_convert` SET  `actionless` =  '1' WHERE  `controller_convert`.`c_id` =27 AND  `controller_convert`.`lang_id` =2;
UPDATE  `vic_main`.`controller_convert` SET  `actionless` =  '1' WHERE  `controller_convert`.`c_id` =27 AND  `controller_convert`.`lang_id` =16;

COMMIT;
