START TRANSACTION;

DELETE FROM `vic_admin`.`sekce_has_parent` WHERE sekce_id=232;
DELETE FROM `vic_admin`.`sekce` WHERE sekce_id=232;
DELETE FROM `vic_admin`.`acl_role_resource_privilege` WHERE acl_resource_id=1232;
DELETE FROM `vic_admin`.`acl_resource` WHERE acl_resource_id=1232;

COMMIT;
