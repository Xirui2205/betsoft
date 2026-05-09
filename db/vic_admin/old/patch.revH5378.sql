start transaction;
select acl_resource_id from sekce where sekce_id = 285 into @ar;
insert into acl_role_resource_privilege(acl_role_id, acl_resource_id, privilege_name, privilege_set) values(16, @ar, "read", 1);
	commit;