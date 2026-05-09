start transaction;
UPDATE `vic_admin`.`sekce` SET `zobrazeno` = '1' WHERE `sekce`.`sekce_id` =194;
UPDATE `vic_admin`.`sekce` SET `zobrazeno` = '1' WHERE `sekce`.`sekce_id` =72;
UPDATE `vic_admin`.`sekce_has_parent` SET `parent_id` = '2',
`sekce_order` = '6' WHERE `sekce_has_parent`.`sekce_id` =72;
UPDATE `vic_admin`.`sekce_has_parent` SET `sekce_order` = '5' WHERE `sekce_has_parent`.`sekce_id` =46;

INSERT INTO `vic_admin`.`acl_role_resource_privilege` (`id`, `acl_role_id`, `acl_resource_id`, `privilege_name`, `privilege_set`) VALUES
(889, 1, 1194, 'delete', 0),
(890, 1, 1194, 'update', 0),
(891, 1, 1194, 'read', 0);
commit;
