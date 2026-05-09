-- detection of duplicate lang_id/req_controller/req_action triplets
-- SELECT *
-- FROM controller_convert
-- WHERE (lang_id,req_controller,req_action) IN (
--  SELECT lang_id,req_controller,req_action
--  FROM controller_convert
--  GROUP BY lang_id,req_controller,req_action
--  HAVING COUNT(c_id)>1
-- )
-- ORDER BY req_controller,req_action,c_id,lang_id;

UPDATE `vic_main`.`controller_convert` SET `req_action`='vklad' WHERE `c_id`=32 AND `lang_id`=16;
DELETE FROM `vic_main`.`controller_convert` WHERE `c_id`=39;
