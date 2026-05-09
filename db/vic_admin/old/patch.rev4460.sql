START TRANSACTION;
INSERT INTO `vic_admin`.`acl_role` (
`acl_role_id` ,
`acl_role_name` ,
`assignable`
)
VALUES (
'14', 'accountant', '1'
);

INSERT INTO  `vic_admin`.`admin` (`admin_id`, `username`, `first_name`, `surname`, `passwd`, `phone`, `email`, `access`, `block`, `block_ip`, `key`, `ldap_username`, `last_login`, `branch_id`) VALUES
(NULL, 'ucto1', 'Učetní1', 'Učetní1', 'bd9c163915b01d66e17871fb4afed293', '', 'v.hercl@compbet.com', 0, 0, '', '', '', '2011-04-15 10:40:47', NULL);
SET @u1 = LAST_INSERT_ID();
INSERT INTO `admin` (`admin_id`, `username`, `first_name`, `surname`, `passwd`, `phone`, `email`, `access`, `block`, `block_ip`, `key`, `ldap_username`, `last_login`, `branch_id`) VALUES
(NULL, 'ucto2', 'Učetní2', 'Učetní2', 'bd9c163915b01d66e17871fb4afed293', '', 'v.hercl@compbet.com', 0, 0, '', '', '', '2011-04-15 10:40:47', NULL);
SET @u2 = LAST_INSERT_ID();

INSERT INTO `vic_admin`.`acl_role` (`acl_role_id`, `acl_role_name`, `assignable`) VALUES
(NULL, CONCAT('user:',@u1),1);
SET @uu1 = LAST_INSERT_ID();

INSERT INTO `vic_admin`.`acl_role` (`acl_role_id`, `acl_role_name`, `assignable`) VALUES
(NULL, CONCAT('user:',@u2),1);
SET @uu2 = LAST_INSERT_ID();

INSERT INTO `vic_admin`.`acl_role_has_parent`(`acl_role_id`, `parent_id`, `parent_order`) VALUES
(@uu1, 14, 1),
(@uu2, 14, 1);

INSERT INTO vic_admin.acl_role_resource_privilege (
`id` ,
`acl_role_id` ,
`acl_resource_id` ,
`privilege_name` ,
`privilege_set`
)
SELECT NULL, '14', `acl_resource_id`, 'read', '1'
FROM vic_admin.acl_resource
WHERE acl_resource_name='section:1'
OR acl_resource_name='section:211'
OR acl_resource_name='section:212'
OR acl_resource_name='section:213'
OR acl_resource_name='section:272'
OR acl_resource_name='section:277'
OR acl_resource_name='section:58'
OR acl_resource_name='section:252'
OR acl_resource_name='section:253'
OR acl_resource_name='section:267'
OR acl_resource_name='section:224'
OR acl_resource_name='section:225'
OR acl_resource_name='section:226'
OR acl_resource_name='section:271'
;

COMMIT;
