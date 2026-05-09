-- role admin gains read privileges for branch insert/update sections
START TRANSACTION;
INSERT INTO vic_admin.acl_role_resource_privilege(acl_role_id,acl_resource_id,privilege_name,privilege_set) VALUES
(2,1191,'read',1),
(2,1192,'read',1);
COMMIT;
