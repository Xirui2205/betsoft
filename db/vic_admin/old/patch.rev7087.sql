START TRANSACTION;
UPDATE vic_admin.acl_role_resource_privilege SET privilege_name='update'
 WHERE acl_resource_id=(SELECT acl_resource_id FROM vic_admin.sekce WHERE sekce_id=297) AND privilege_name='write';
COMMIT;
