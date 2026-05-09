INSERT INTO `vic_admin`.`sekce` (`sekce_id`, `parent_id`, `nazev`, `zobrazeno`, `poradi`, `controller`, `action`) 
	VALUES ('224', '58', 'Transactions', '1', '0', 'finance', 'transactions');
	
INSERT INTO `vic_admin`.`sekce` (`sekce_id`, `parent_id`, `nazev`, `zobrazeno`, `poradi`, `controller`, `action`) 
	VALUES ('225', '58', 'Withdraws', '1', '0', 'finance', 'withdraws');
	
INSERT INTO `vic_admin`.`sekce` (`sekce_id`, `parent_id`, `nazev`, `zobrazeno`, `poradi`, `controller`, `action`) 
	VALUES ('226', '58', 'Manual', '1', '0', 'finance', 'manual');
	
INSERT INTO `vic_admin`.`role_resource_privilege` (`id`, `role_id`, `section_id`, `privilege_name`, `privilege_set`) 
	VALUES (NULL, '2', '224', 'read', '1');
	
INSERT INTO `vic_admin`.`role_resource_privilege` (`id`, `role_id`, `section_id`, `privilege_name`, `privilege_set`) 
	VALUES (NULL, '2', '225', 'read', '1');
	
INSERT INTO `vic_admin`.`role_resource_privilege` (`id`, `role_id`, `section_id`, `privilege_name`, `privilege_set`) 
	VALUES (NULL, '2', '226', 'read', '1');
