START TRANSACTION;
UPDATE `vic_admin`.`acl_role_resource_privilege` SET `privilege_set` = '1' WHERE `acl_resource_id`=1146 and acl_role_id=1;
UPDATE `vic_admin`.`acl_role_resource_privilege` SET `privilege_set` = '1' WHERE `acl_resource_id` =1146 and acl_role_id=2;
UPDATE `vic_admin`.`acl_role_resource_privilege` SET `privilege_set` = '1' WHERE `acl_resource_id` =1143 and acl_role_id=1;
UPDATE `vic_admin`.`acl_role_resource_privilege` SET `privilege_set` = '1' WHERE `acl_resource_id` =1143 and acl_role_id=2;
COMMIT;
