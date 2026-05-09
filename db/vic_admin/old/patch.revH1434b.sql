START TRANSACTION;

INSERT INTO `vic_admin`.`database_patch`(`revision`, `not_reapplicable`, `note`) VALUES
 ('H1434b', 1, 'Right for roles "sales" to read complete day balancing and transactions section (mantis:984)');

INSERT INTO acl_role_resource_privilege(acl_role_id, acl_resource_id, privilege_name, privilege_set)
 SELECT 16, acl_resource_id, 'read', 1 FROM vic_admin.sekce WHERE sekce_id IN (224, 277);

COMMIT;
