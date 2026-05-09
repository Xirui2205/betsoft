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
WHERE acl_resource_name='section:192'
OR acl_resource_name='section:191'
OR acl_resource_name='section:287'
OR acl_resource_name='section:167'
OR acl_resource_name='section:169'
OR acl_resource_name='section:168'
OR acl_resource_name='section:184'
OR acl_resource_name='section:181';
commit;

start transaction;
INSERT INTO vic_admin.acl_role_resource_privilege (
`id` ,
`acl_role_id` ,
`acl_resource_id` ,
`privilege_name` ,
`privilege_set`
)
SELECT NULL, '3', `acl_resource_id`, 'read', '1'
FROM vic_admin.acl_resource
WHERE acl_resource_name='section:40'
OR acl_resource_name='section:227'
OR acl_resource_name='section:231'
OR acl_resource_name='section:234'
OR acl_resource_name='section:229'
OR acl_resource_name='section:235'
OR acl_resource_name='section:241'
OR acl_resource_name='section:239';
commit;

start transaction;
INSERT INTO vic_admin.acl_role_resource_privilege (
`id` ,
`acl_role_id` ,
`acl_resource_id` ,
`privilege_name` ,
`privilege_set`
)
SELECT NULL, '4', `acl_resource_id`, 'read', '1'
FROM vic_admin.acl_resource
WHERE acl_resource_name='section:40'
OR acl_resource_name='section:227'
OR acl_resource_name='section:231'
OR acl_resource_name='section:234'
OR acl_resource_name='section:229'
OR acl_resource_name='section:235'
OR acl_resource_name='section:241'
OR acl_resource_name='section:239';
commit;
