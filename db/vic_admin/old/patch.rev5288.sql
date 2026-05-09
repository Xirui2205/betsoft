start transaction;
INSERT INTO vic_admin.acl_role_resource_privilege (
`id` ,
`acl_role_id` ,
`acl_resource_id` ,
`privilege_name` ,
`privilege_set`
)
SELECT NULL, '16', `acl_resource_id`, 'read', '1'
FROM vic_admin.acl_resource
WHERE acl_resource_name='section:193'
OR acl_resource_name='section:190'
OR acl_resource_name='section:275'
OR acl_resource_name='section:286'
OR acl_resource_name='section:166'
OR acl_resource_name='section:164'
OR acl_resource_name='section:165'
OR acl_resource_name='section:185'
OR acl_resource_name='section:183'
OR acl_resource_name='section:227'
OR acl_resource_name='section:180';

INSERT INTO vic_admin.acl_role_resource_privilege (
`id` ,
`acl_role_id` ,
`acl_resource_id` ,
`privilege_name` ,
`privilege_set`
)
SELECT NULL, '15', `acl_resource_id`, 'read', '1'
FROM vic_admin.acl_resource
WHERE acl_resource_name='section:193'
OR acl_resource_name='section:190'
OR acl_resource_name='section:275'
OR acl_resource_name='section:286'
OR acl_resource_name='section:166'
OR acl_resource_name='section:164'
OR acl_resource_name='section:165'
OR acl_resource_name='section:185'
OR acl_resource_name='section:183'
OR acl_resource_name='section:227'
OR acl_resource_name='section:180';
commit;
