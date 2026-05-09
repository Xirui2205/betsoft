START TRANSACTION;

INSERT INTO `vic_admin`.`database_patch`(`revision`, `not_reapplicable`, `note`)
VALUES (2469, 1, 'bookmaker note edit -superbm+bm permissions');

INSERT INTO `vic_admin`.`acl_role_resource_privilege` (`acl_role_id`,`acl_resource_id`,`privilege_name`,`privilege_set`)
VALUES
  (3,1236,'read',1),
  (3,1236,'update',1),
  (3,1236,'delete',1),
  (4,1236,'read',1),
  (4,1236,'update',1),
  (4,1236,'delete',1);

COMMIT;
