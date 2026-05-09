START TRANSACTION;

UPDATE `vic_main`.`controller_convert` SET `real_controller` = 'global-cache-frame' 
WHERE `controller_convert`.`c_id` = 66 AND `controller_convert`.`lang_id` = 1; 
UPDATE `vic_main`.`controller_convert` SET `real_controller` = 'global-cache-frame' 
WHERE `controller_convert`.`c_id` = 66 AND `controller_convert`.`lang_id` = 2;
UPDATE `vic_main`.`controller_convert` SET `real_controller` = 'global-cache-frame'
WHERE `controller_convert`.`c_id` = 66 AND `controller_convert`.`lang_id` = 16;
UPDATE `vic_main`.`controller_convert` SET `real_controller` = 'global-cache-frame'
WHERE `controller_convert`.`c_id` = 71 AND `controller_convert`.`lang_id` = 1;
UPDATE `vic_main`.`controller_convert` SET `real_controller` = 'global-cache-frame'
WHERE `controller_convert`.`c_id` = 71 AND `controller_convert`.`lang_id` = 2;
UPDATE `vic_main`.`controller_convert` SET `real_controller` = 'global-cache-frame'
WHERE `controller_convert`.`c_id` = 71 AND `controller_convert`.`lang_id` = 16;
UPDATE `vic_main`.`controller_convert` SET `real_controller` = 'global-cache-frame'
WHERE `controller_convert`.`c_id` = 72 AND `controller_convert`.`lang_id` = 1;
UPDATE `vic_main`.`controller_convert` SET `real_controller` = 'global-cache-frame' 
WHERE `controller_convert`.`c_id` = 72 AND `controller_convert`.`lang_id` = 2; 
UPDATE `vic_main`.`controller_convert` SET `real_controller` = 'global-cache-frame' 
WHERE `controller_convert`.`c_id` = 72 AND `controller_convert`.`lang_id` = 16;

COMMIT;
