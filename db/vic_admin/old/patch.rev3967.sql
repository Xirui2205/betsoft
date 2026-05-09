START TRANSACTION;
INSERT INTO `vic_admin`.`acl_role_resource_privilege` (
`id` ,
`acl_role_id` ,
`acl_resource_id` ,
`privilege_name` ,
`privilege_set`
)
VALUES (
NULL , '3', '1298', 'read', '1'
), (
NULL , '3', '1298', 'delete', '1'
);
COMMIT;
