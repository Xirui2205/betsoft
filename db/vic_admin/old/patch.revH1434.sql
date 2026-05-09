START TRANSACTION;

INSERT INTO `vic_admin`.`database_patch`(`revision`, `not_reapplicable`, `note`) VALUES
 ('H1434', NULL, 'ACL changes for sales role (mantis:984)');

SET @rid1=(SELECT acl_resource_id FROM vic_admin.sekce WHERE sekce_id=281);
SET @rid2=(SELECT acl_resource_id FROM vic_admin.sekce WHERE sekce_id=282);
SET @rid3=(SELECT acl_resource_id FROM vic_admin.sekce WHERE sekce_id=283);
SET @rid4=(SELECT acl_resource_id FROM vic_admin.sekce WHERE sekce_id=284);

REPLACE INTO vic_admin.acl_role_resource_privilege(acl_role_id, acl_resource_id, privilege_name, privilege_set) VALUES
 (16, @rid1, 'read', 1),
 (16, @rid1, 'update', 1),
 (16, @rid1, 'delete', 1), 
 (16, @rid2, 'read', 1),
 (16, @rid2, 'update', 1),
 (16, @rid2, 'delete', 1), 
 (16, @rid3, 'read', 1),
 (16, @rid3, 'update', 1),
 (16, @rid3, 'delete', 1), 
 (16, @rid4, 'read', 1),
 (16, @rid4, 'update', 1),
 (16, @rid4, 'delete', 1); 

COMMIT;
