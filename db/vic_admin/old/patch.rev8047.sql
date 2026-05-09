INSERT INTO `vic_admin`.`database_patch`(`revision`, `not_reapplicable`, `note`)
VALUES ('8047', 0, 'uprava razeni udalosti');

UPDATE `vic_admin`.`sekce` SET `controller`='event', `action`='event-selector-popup' WHERE `sekce_id`=300;
	
