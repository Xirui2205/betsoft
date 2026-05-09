set @r1=(SELECT acl_resource_id FROM vic_admin.acl_resource WHERE acl_resource_name='section:226');
set @r2=(SELECT acl_resource_id FROM vic_admin.acl_resource WHERE acl_resource_name='section:244');
set @r3=(SELECT acl_resource_id FROM vic_admin.acl_resource WHERE acl_resource_name='section:245');
set @r4=(SELECT acl_resource_id FROM vic_admin.acl_resource WHERE acl_resource_name='section:246');


update vic_admin.acl_role_resource_privilege set privilege_set=0
where acl_role_id=14 and (privilege_name='read' or privilege_name='update' or privilege_name='delete') and (
	acl_resource_id=@r1 or
	acl_resource_id=@r2 or
	acl_resource_id=@r3 or
	acl_resource_id=@r4
);

update vic_admin.acl_role_resource_privilege set privilege_set=0
where acl_role_id=1 and (privilege_name='read' or privilege_name='update' or privilege_name='delete') and (
	acl_resource_id=@r1 or
	acl_resource_id=@r2 or
	acl_resource_id=@r3 or
	acl_resource_id=@r4
);

update vic_admin.acl_role_resource_privilege set privilege_set=0
where acl_role_id=2 and (privilege_name='read' or privilege_name='update' or privilege_name='delete') and (
	acl_resource_id=@r1 or
	acl_resource_id=@r2 or
	acl_resource_id=@r3 or
	acl_resource_id=@r4
);

