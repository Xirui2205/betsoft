start transaction;
INSERT INTO `vic_admin`.`user_has_parameter`
(`user_id`,`parameter_id`,`value`)
SELECT `vic_main`.`uzivatel`.`user_id`,'80','30' FROM `vic_main`.`uzivatel`
LEFT JOIN `vic_admin`.`user_has_parameter` ON `vic_admin`.`user_has_parameter`.`user_id` = `vic_main`.`uzivatel`.`user_id`
WHERE `anonymous` = 1 AND `vic_admin`.`user_has_parameter`.`user_id` IS NULL;
commit;
