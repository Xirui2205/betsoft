INSERT INTO `vic_main`.`database_patch`(`revision`, `not_reapplicable`, `note`) VALUES
 ('7674', 1, 'Novy vernostni program doplneni infa o stavu udelovanych bodech pri zalozeni tiketu ci vkladu na pobocce. (mantis:566)');

ALTER TABLE `ticket`
	ADD `point_create` INT NULL ,
	ADD `point_branch_visit` INT NULL;
 
