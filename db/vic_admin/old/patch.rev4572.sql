start transaction;
SET @resourceId = (SELECT acl_resource_id FROM vic_admin.acl_resource WHERE acl_resource_name='section:58');
UPDATE vic_admin.acl_role_resource_privilege set `privilege_set` =0 WHERE acl_resource_id= @resourceId and acl_role_id=3;

SET @resourceId = (SELECT acl_resource_id FROM vic_admin.acl_resource WHERE acl_resource_name='section:270');
UPDATE vic_admin.acl_role_resource_privilege set `privilege_set` =0 WHERE acl_resource_id= @resourceId and acl_role_id=3;
commit;

SET @resourceId = (SELECT acl_resource_id FROM vic_admin.acl_resource WHERE acl_resource_name='section:279');
UPDATE vic_admin.acl_role_resource_privilege set `privilege_set` =0 WHERE acl_resource_id= @resourceId and acl_role_id=3;
commit;
