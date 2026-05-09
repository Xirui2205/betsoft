START TRANSACTION;

INSERT INTO `vic_admin`.`acl_resource` (`acl_resource_id`, `acl_tree_id`, `acl_resource_name`, `acl_resource_parent_id`, `acl_resource_type_id`) VALUES
(null, 1, 'section:278', 1001, 2);

SET @ar = LAST_INSERT_ID();


INSERT INTO `vic_admin`.`sekce` (`sekce_id`, `acl_resource_id`, `nazev`, `zobrazeno`, `controller`, `action`) VALUES
(278, @ar, 'Bet Rate Graph', 1, 'bet-info', 'rate-over-time');


INSERT INTO `vic_admin`.`acl_role_resource_privilege` (
`id` ,
`acl_role_id` ,
`acl_resource_id` ,
`privilege_name` ,
`privilege_set`
)
VALUES
(NULL , '3', @ar, 'read', '1');

COMMIT;
