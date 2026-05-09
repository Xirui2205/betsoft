INSERT INTO `vic_admin`.`database_patch`(`revision`, `not_reapplicable`, `note`) VALUES
 ('H2405', 1, 'Added rights to password change section (mantis: 1218)');

select acl_resource_id into @resource_id from sekce where sekce_id=280;

insert into acl_role_resource_privilege(acl_role_id, acl_resource_id, privilege_name, privilege_set) 
values (13, @resource_id, 'read', 1),(15, @resource_id, 'read', 1),(16, @resource_id, 'read', 1), (17, @resource_id, 'read', 1), (18, @resource_id, 'read', 1);
