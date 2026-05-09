start transaction;
INSERT INTO `vic_admin`.`acl_role` (
`acl_role_id` ,
`acl_role_name` ,
`assignable`
)
VALUES (
'15', 'callcentrum', '1'
), (
'16', 'sales', '1'
);

INSERT INTO vic_admin.acl_role_resource_privilege (
`id` ,
`acl_role_id` ,
`acl_resource_id` ,
`privilege_name` ,
`privilege_set`
)
SELECT NULL, '15', `acl_resource_id`, 'read', '1'
FROM vic_admin.acl_resource
WHERE acl_resource_name='section:1'
OR acl_resource_name='section:148'
OR acl_resource_name='section:270'
OR acl_resource_name='section:159'
OR acl_resource_name='section:272'
OR acl_resource_name='section:211'
OR acl_resource_name='section:58'
OR acl_resource_name='section:252'
OR acl_resource_name='section:40'
OR acl_resource_name='section:247';


INSERT INTO vic_admin.acl_role_resource_privilege (
`id` ,
`acl_role_id` ,
`acl_resource_id` ,
`privilege_name` ,
`privilege_set`
)
SELECT NULL, '16', `acl_resource_id`, 'read', '1'
FROM vic_admin.acl_resource
WHERE acl_resource_name='section:1'
OR acl_resource_name='section:148'
OR acl_resource_name='section:270'
OR acl_resource_name='section:159'
OR acl_resource_name='section:272'
OR acl_resource_name='section:211'
OR acl_resource_name='section:58'
OR acl_resource_name='section:252'
OR acl_resource_name='section:40'
OR acl_resource_name='section:247'
OR acl_resource_name='section:257'
OR acl_resource_name='section:190'
OR acl_resource_name='section:166'
OR acl_resource_name='section:164'
OR acl_resource_name='section:165'
OR acl_resource_name='section:185'
OR acl_resource_name='section:183'
OR acl_resource_name='section:180'
OR acl_resource_name='section:286'
;


INSERT INTO vic_admin.acl_role_resource_privilege (
`id` ,
`acl_role_id` ,
`acl_resource_id` ,
`privilege_name` ,
`privilege_set`
)
SELECT NULL, '15', `acl_resource_id`, 'update', '1'
FROM vic_admin.acl_resource
WHERE acl_resource_name='section:270';

INSERT INTO vic_admin.acl_role_resource_privilege (
`id` ,
`acl_role_id` ,
`acl_resource_id` ,
`privilege_name` ,
`privilege_set`
)
SELECT NULL, '16', `acl_resource_id`, 'update', '1'
FROM vic_admin.acl_resource
WHERE acl_resource_name='section:159'
OR acl_resource_name='section:257'
OR acl_resource_name='section:270'
OR acl_resource_name='section:190'
OR acl_resource_name='section:166'
OR acl_resource_name='section:164'
OR acl_resource_name='section:165'
OR acl_resource_name='section:185'
OR acl_resource_name='section:183'
OR acl_resource_name='section:180'
OR acl_resource_name='section:286'
;

INSERT INTO vic_admin.acl_role_resource_privilege (
`id` ,
`acl_role_id` ,
`acl_resource_id` ,
`privilege_name` ,
`privilege_set`
)
SELECT NULL, '16', `acl_resource_id`, 'delete', '1'
FROM vic_admin.acl_resource
WHERE acl_resource_name='section:159'
OR acl_resource_name='section:257'
OR acl_resource_name='section:190'
OR acl_resource_name='section:166'
OR acl_resource_name='section:164'
OR acl_resource_name='section:165'
OR acl_resource_name='section:185'
OR acl_resource_name='section:183'
OR acl_resource_name='section:180'
OR acl_resource_name='section:286';

commit;
