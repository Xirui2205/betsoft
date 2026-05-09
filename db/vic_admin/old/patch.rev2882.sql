START TRANSACTION;

INSERT INTO `vic_admin`.`parameter` (`id`, `name`, `value`, `is_host`, `is_branch`, `is_user`, `is_admin`, `type`, `mandatory`, `description`) VALUES
(NULL , 'confirmation.ticketSimple.lowerTreshold.stake', '21', '0', '0', '1', '0', NULL , '1', 'Částka, nad kterou jde simple tiket vždy do schvalovačky'),
(NULL, 'confirmation.ticketCombi.lowerTreshold.stake', '41', '0', '0', '1', '0', NULL, '1', 'Částka, nad kterou jde kombi/maxikombi tiket vždy do schvalovačky.');

INSERT INTO `vic_admin`.`acl_resource` (
`acl_resource_id` ,
`acl_tree_id` ,
`acl_resource_name` ,
`acl_resource_parent_id` ,
`acl_resource_type_id`
)
VALUES (
1299 , '1', 'section:270', '1', '2'
);


INSERT INTO  `vic_admin`.`acl_role_resource_privilege` (
`id` ,
`acl_role_id` ,
`acl_resource_id` ,
`privilege_name` ,
`privilege_set`
)
VALUES (
NULL,  '2',  1299,  'read',  '1'
);


INSERT INTO  `vic_admin`.`sekce` (
`sekce_id` ,
`acl_resource_id` ,
`nazev` ,
`zobrazeno` ,
`controller` ,
`action`
)
VALUES (
'270', 1298,  'Branches/Hosts',  '1',  NULL,  NULL
);

update vic_admin.sekce_has_parent set parent_id=270 where sekce_id IN(257,159,268);

INSERT INTO `vic_admin`.`sekce_has_parent` (
`sekce_id` ,
`parent_id` ,
`sekce_order`
)
VALUES (
'257', '270', '0'
);


commit;
