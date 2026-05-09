INSERT INTO `vic_main`.`database_patch`(`revision`, `not_reapplicable`, `note`) VALUES
 ('8097', NULL, 'Vygenerovani cisel karticek (mantis:690)');



CREATE TABLE `client_card_number_feed` (
 `id` varchar(13) NOT NULL,
 `generated` datetime NOT NULL,
 `admin_id` int(10) unsigned DEFAULT NULL,
 `branch_id` int(10) unsigned DEFAULT NULL,
 PRIMARY KEY (`id`),
 KEY `i__client_card_number_feed__admin_id` (`admin_id`),
 KEY `i__client_card_number_feed__branch_id` (`branch_id`)
) ENGINE=InnoDB;
