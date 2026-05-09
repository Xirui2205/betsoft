START TRANSACTION;
UPDATE `vic_admin`.`acl_role_resource_privilege` SET privilege_set =0 WHERE acl_resource_id =1042;
UPDATE `vic_admin`.`acl_role_resource_privilege` SET privilege_set =0 WHERE acl_resource_id =1041;
UPDATE `vic_admin`.`acl_role_resource_privilege` SET privilege_set =0 WHERE acl_resource_id =1109;
COMMIT;
