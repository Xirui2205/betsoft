UPDATE `vic_admin`.`sekce` SET `zobrazeno` = '0' WHERE `sekce`.`sekce_id` =213;
UPDATE `role_resource_privilege` SET role_id =4 WHERE section_id IN ( 194, 196, 197, 198, 199, 200, 201, 202 );
UPDATE `vic_admin`.`sekce` SET `parent_id` = '133' WHERE `sekce`.`sekce_id` =194;
UPDATE `vic_admin`.`role_resource_privilege` SET `privilege_name` = 'insert' WHERE `role_resource_privilege`.`id` =395;
