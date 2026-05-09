INSERT INTO `vic_main`.`database_patch`(`revision`, `not_reapplicable`, `note`) VALUES
 ('8211', 1, 'Novy system kraticek a smluv (mantis:701)');
 
ALTER TABLE `vic_main`.`uzivatel` ADD `agreement_number` VARCHAR( 15 ) NULL AFTER `client_card_number`;
ALTER TABLE `vic_main`.`client_card_number_feed` ADD `used` DATETIME NULL AFTER `set`;
ALTER TABLE `vic_main`.`client_card_number_feed` ADD `blocked` DATETIME NULL AFTER `used`;

UPDATE `vic_main`.`uzivatel` SET
`agreement_number` = `client_card_number`
WHERE `agreement_number` IS NULL;
