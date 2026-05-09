SET foreign_key_checks=0;
UPDATE `vic_admin`.`sekce` SET `controller` = 'branch-main' WHERE `sekce`.`sekce_id` =159;
DELETE FROM `vic_admin`.`role_resource_privilege` WHERE `role_resource_privilege`.`id` = 337;
DELETE FROM `vic_admin`.`role_resource_privilege` WHERE `role_resource_privilege`.`id` = 341;
DELETE FROM `vic_admin`.`role_resource_privilege` WHERE `role_resource_privilege`.`id` = 342;
DELETE FROM `vic_admin`.`role_resource_privilege` WHERE `role_resource_privilege`.`id` = 361;
DELETE FROM `vic_admin`.`role_resource_privilege` WHERE `role_resource_privilege`.`id` = 362;
DELETE FROM `vic_admin`.`role_resource_privilege` WHERE `role_resource_privilege`.`id` = 363;

DELETE FROM `vic_admin`.`role_resource_privilege` WHERE `section_id` IN (163,164,165,166);

DELETE FROM `vic_admin`.`sekce` WHERE `sekce`.`sekce_id` = 163;
DELETE FROM `vic_admin`.`sekce` WHERE `sekce`.`sekce_id` = 164;
DELETE FROM `vic_admin`.`sekce` WHERE `sekce`.`sekce_id` = 165;
DELETE FROM `vic_admin`.`sekce` WHERE `sekce`.`sekce_id` = 166;

REPLACE INTO `sekce` (`sekce_id`, `parent_id`, `nazev`, `zobrazeno`, `poradi`, `controller`, `action`) VALUES
(190, 0, 'Branch main', 1, 0, 'branch-main', 'view'),
(191, 0, 'Branch main', 1, 0, 'branch-main', 'update'),
(192, 0, 'Branch main', 1, 0, 'branch-main', 'insert'),
(193, 0, 'Branch main', 1, 0, 'branch-main', 'index');


REPLACE INTO `role_resource_privilege` (`id`, `role_id`, `section_id`, `privilege_name`, `privilege_set`) VALUES
(391, 2, 190, 'read', 1),
(392, 2, 191, 'update', 1),
(393, 2, 192, 'update', 1),
(394, 2, 193, 'read', 1);

SET foreign_key_checks=1;
