START TRANSACTION;

INSERT INTO `vic_admin`.`database_patch`(`revision`, `not_reapplicable`, `note`)
VALUES ('H1980', 1, 'new role supervision (mantis:1066)');

INSERT INTO `vic_admin`.`acl_role` (
`acl_role_id` ,
`acl_role_name` ,
`assignable`
)
VALUES (
'18', 'supervision', '1'
);


SELECT acl_resource_id into @cash_overview FROM sekce WHERE sekce_id=296;

INSERT INTO `vic_admin`.`acl_role_resource_privilege` (
`id` ,
`acl_role_id` ,
`acl_resource_id` ,
`privilege_name` ,
`privilege_set`
)
VALUES (
NULL , '18', @cash_overview, 'read', '1'
);

INSERT INTO `vic_admin`.`acl_role_resource_privilege` (
`id` ,
`acl_role_id` ,
`acl_resource_id` ,
`privilege_name` ,
`privilege_set`
)
VALUES (
NULL , '18', '1001', 'read', '1'
);

SELECT acl_resource_id into @a FROM sekce WHERE sekce_id=213;

INSERT INTO `vic_admin`.`acl_role_resource_privilege` (
`id` ,
`acl_role_id` ,
`acl_resource_id` ,
`privilege_name` ,
`privilege_set`
)
VALUES (
NULL , '18', @a, 'read', '1'
);

SELECT acl_resource_id into @b FROM sekce WHERE sekce_id=277;

INSERT INTO `vic_admin`.`acl_role_resource_privilege` (
`id` ,
`acl_role_id` ,
`acl_resource_id` ,
`privilege_name` ,
`privilege_set`
)
VALUES (
NULL , '18', @b, 'read', '1'
);

SELECT acl_resource_id into @c FROM sekce WHERE sekce_id=272;

INSERT INTO `vic_admin`.`acl_role_resource_privilege` (
`id` ,
`acl_role_id` ,
`acl_resource_id` ,
`privilege_name` ,
`privilege_set`
)
VALUES (
NULL , '18', @c, 'read', '1'
);

SELECT acl_resource_id into @d FROM sekce WHERE sekce_id=318;

INSERT INTO `vic_admin`.`acl_role_resource_privilege` (
`id` ,
`acl_role_id` ,
`acl_resource_id` ,
`privilege_name` ,
`privilege_set`
)
VALUES (
NULL , '18', @d, 'read', '1'
);

SELECT acl_resource_id into @e FROM sekce WHERE sekce_id=247;

INSERT INTO `vic_admin`.`acl_role_resource_privilege` (
`id` ,
`acl_role_id` ,
`acl_resource_id` ,
`privilege_name` ,
`privilege_set`
)
VALUES (
NULL , '18', @e, 'read', '1'
);

SELECT acl_resource_id into @f FROM sekce WHERE sekce_id=148;

INSERT INTO `vic_admin`.`acl_role_resource_privilege` (
`id` ,
`acl_role_id` ,
`acl_resource_id` ,
`privilege_name` ,
`privilege_set`
)
VALUES (
NULL , '18', @f, 'read', '1'
);

SELECT acl_resource_id into @f FROM sekce WHERE sekce_id=58;

INSERT INTO `vic_admin`.`acl_role_resource_privilege` (
`id` ,
`acl_role_id` ,
`acl_resource_id` ,
`privilege_name` ,
`privilege_set`
)
VALUES (
NULL , '18', @f, 'read', '1'
);

SELECT acl_resource_id into @h FROM sekce WHERE sekce_id=224;

INSERT INTO `vic_admin`.`acl_role_resource_privilege` (
`id` ,
`acl_role_id` ,
`acl_resource_id` ,
`privilege_name` ,
`privilege_set`
)
VALUES (
NULL , '18', @h, 'read', '1'
);

SELECT acl_resource_id into @i FROM sekce WHERE sekce_id=275;

INSERT INTO `vic_admin`.`acl_role_resource_privilege` (
`id` ,
`acl_role_id` ,
`acl_resource_id` ,
`privilege_name` ,
`privilege_set`
)
VALUES (
NULL , '18', @i, 'read', '1'
);

SELECT acl_resource_id into @j FROM sekce WHERE sekce_id=319;

INSERT INTO `vic_admin`.`acl_role_resource_privilege` (
`id` ,
`acl_role_id` ,
`acl_resource_id` ,
`privilege_name` ,
`privilege_set`
)
VALUES (
NULL , '18', @j, 'read', '1'
);

SELECT acl_resource_id into @j FROM sekce WHERE sekce_id=231;

INSERT INTO `vic_admin`.`acl_role_resource_privilege` (
`id` ,
`acl_role_id` ,
`acl_resource_id` ,
`privilege_name` ,
`privilege_set`
)
VALUES (
NULL , '18', @j, 'read', '1'
);

SELECT acl_resource_id into @k FROM sekce WHERE sekce_id=235;

INSERT INTO `vic_admin`.`acl_role_resource_privilege` (
`id` ,
`acl_role_id` ,
`acl_resource_id` ,
`privilege_name` ,
`privilege_set`
)
VALUES (
NULL , '18', @k, 'read', '1'
);

SELECT acl_resource_id into @l FROM sekce WHERE sekce_id=241;

INSERT INTO `vic_admin`.`acl_role_resource_privilege` (
`id` ,
`acl_role_id` ,
`acl_resource_id` ,
`privilege_name` ,
`privilege_set`
)
VALUES (
NULL , '18', @l, 'read', '1'
);

SELECT acl_resource_id into @m FROM sekce WHERE sekce_id=76;

INSERT INTO `vic_admin`.`acl_role_resource_privilege` (
`id` ,
`acl_role_id` ,
`acl_resource_id` ,
`privilege_name` ,
`privilege_set`
)
VALUES (
NULL , '18', @m, 'read', '1'
);

SELECT acl_resource_id into @n FROM sekce WHERE sekce_id=239;

INSERT INTO `vic_admin`.`acl_role_resource_privilege` (
`id` ,
`acl_role_id` ,
`acl_resource_id` ,
`privilege_name` ,
`privilege_set`
)
VALUES (
NULL , '18', @n, 'read', '1'
);

commit;
