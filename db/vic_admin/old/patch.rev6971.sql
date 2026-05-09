-- access for bookmakers Statistics tab in user profile
START TRANSACTION;
SET @res = (SELECT acl_resource_id FROM vic_admin.acl_resource WHERE acl_resource_name='section:76');
REPLACE INTO vic_admin.acl_role_resource_privilege(acl_role_id,acl_resource_id,privilege_name,privilege_set) VALUES
 (3,@res,'read',1),(4,@res,'read',1);
COMMIT;
