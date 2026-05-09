INSERT INTO `vic_main`.`database_patch`(`revision`, `not_reapplicable`,
`note`) VALUES
 ('7449', 1 , 'Tickets tips ordering (mantis:499)');

ALTER TABLE `vic_main`.`ticket_kurz` ADD `order` INT NOT NULL DEFAULT '0',
ADD INDEX `i__ticket_kurz__order` ( `order` ) 
