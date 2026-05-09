INSERT INTO `vic_main`.`database_patch`(`revision`, `not_reapplicable`, `note`)
VALUES (7960, 0, 'three digit bank codes padded with 0 to 4 digits');

ALTER TABLE `bank` CHANGE `bank_code` `bank_code` VARCHAR( 10 ) NOT NULL;
UPDATE `vic_main`.`bank` SET `bank_code` = CONCAT( '0', bank_code ) WHERE length(bank_code) = 3;
