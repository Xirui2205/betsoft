start transaction;
UPDATE `vic_admin`.`parameter` SET `description` = 'Minimální změna kurzu při které se již posílá alert RateChange (v procentech).' WHERE `parameter`.`id` =17;

INSERT INTO `vic_admin`.`acl_role_resource_privilege` (
`id` ,
`acl_role_id` ,
`acl_resource_id` ,
`privilege_name` ,
`privilege_set`
)
VALUES (
NULL , '3', '1215', 'read', '1'
), (
NULL , '3', '1215', 'delete', '1'
), (
NULL , '3', '1216', 'read', '1'
), (
NULL , '3', '1216', 'delete', '1'
);

commit;
