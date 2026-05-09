START TRANSACTION;

INSERT INTO `vic_admin`.`database_patch`(`revision`, `not_reapplicable`, `note`) VALUES
 ('H1777', 1, 'callcenter role edit rights (mantis:1059)');

SELECT acl_resource_id
INTO @odvody_resource_id
FROM `sekce`
WHERE sekce_id =252;

DELETE FROM `acl_role_resource_privilege`
WHERE acl_role_id =15
AND acl_resource_id =@odvody_resource_id;

SELECT acl_resource_id
INTO @cash_resource_id
FROM `sekce`
WHERE sekce_id =296;

INSERT INTO `vic_admin`.`acl_role_resource_privilege` (
`id` ,
`acl_role_id` ,
`acl_resource_id` ,
`privilege_name` ,
`privilege_set`
)
VALUES (
NULL , '15', @cash_resource_id, 'read', '1'
);

SELECT acl_resource_id
INTO @password_resource_id
FROM `sekce`
WHERE sekce_id =234;

INSERT INTO `vic_admin`.`acl_role_resource_privilege` (
`id` ,
`acl_role_id` ,
`acl_resource_id` ,
`privilege_name` ,
`privilege_set`
)
VALUES (
NULL , '15', @password_resource_id, 'read', '1'
);

SELECT acl_resource_id
INTO @notes_resource_id
FROM `sekce`
WHERE sekce_id =229;

INSERT INTO `vic_admin`.`acl_role_resource_privilege` (
`id` ,
`acl_role_id` ,
`acl_resource_id` ,
`privilege_name` ,
`privilege_set`
)
VALUES (
NULL , '15', @notes_resource_id, 'read', '1'
);


SELECT acl_resource_id
INTO @host_resource_id
FROM `sekce`
WHERE sekce_id =181;

INSERT INTO `vic_admin`.`acl_role_resource_privilege` (
`id` ,
`acl_role_id` ,
`acl_resource_id` ,
`privilege_name` ,
`privilege_set`
)
VALUES (
NULL , '15', @host_resource_id, 'read', '1'
);
INSERT INTO `vic_admin`.`acl_role_resource_privilege` (
`id` ,
`acl_role_id` ,
`acl_resource_id` ,
`privilege_name` ,
`privilege_set`
)
VALUES (
NULL , '15', @host_resource_id, 'update', '1'
);

SELECT acl_resource_id
INTO @finance_resource_id
FROM `sekce`
WHERE sekce_id =58;

DELETE FROM `acl_role_resource_privilege`
WHERE acl_role_id =15
AND acl_resource_id =@finance_resource_id;

COMMIT;
