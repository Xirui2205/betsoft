INSERT INTO `vic_main`.`database_patch`(`revision`, `not_reapplicable`, `note`) VALUES
 ('8153', 1, 'Vygenerovani karticek (mantis:743)');
 

TRUNCATE `vic_main`.`client_card_number_feed`;

ALTER TABLE `vic_main`.`client_card_number_feed` ADD `set` INT NULL 
