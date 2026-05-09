INSERT INTO `vic_admin`.`database_patch`(`revision`, `not_reapplicable`, `note`) VALUES
 ('H541', 1, 'Privilege for role accountant section Cash overview (mantis:888)');

INSERT INTO vic_admin.acl_role_resource_privilege(acl_role_id, acl_resource_id, privilege_name, privilege_set) VALUES
 (14, 1325, 'read', 1);
