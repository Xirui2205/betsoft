START TRANSACTION;

INSERT INTO `acl_role_resource_privilege` (`id`, `acl_role_id`, `acl_resource_id`, `privilege_name`, `privilege_set`) VALUES 
(2456, 13, 1076, 'read', 1),
(2455, 13, 1227, 'read', 1),
(2454, 13, 1040, 'read', 1);

COMMIT;
