-- remove privileges for adminstrator on section admin/insert,admin/update,admin/privileges
DELETE FROM vic_admin.acl_role_resource_privilege WHERE acl_role_id=2 AND acl_resource_id IN (
 SELECT acl_resource_id FROM vic_admin.sekce WHERE sekce_id IN (283,284,285)
);
