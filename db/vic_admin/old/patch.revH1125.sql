START TRANSACTION;

INSERT INTO `vic_admin`.`database_patch`(`revision`, `not_reapplicable`, `note`)
VALUES (1125, 1, 'allowing role accountant to read from section 279');



INSERT INTO `vic_admin`.`acl_role_resource_privilege` (`acl_role_id`, `acl_resource_id`, `privilege_name`, `privilege_set`)
VALUES (14, (SELECT `acl_resource_id` FROM `vic_admin`.`acl_resource` WHERE acl_resource_name='section:279'),  'read', 1);



COMMIT;
