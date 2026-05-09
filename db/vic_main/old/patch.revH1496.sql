START TRANSACTION;

INSERT INTO `vic_main`.`database_patch`(`revision`, `not_reapplicable`, `note`) VALUES
 ('H1496', 1, 'New table for blocking accounts by ip - (UKO120898)');
 
CREATE TABLE `vic_main`.`uzivatel_block` (
`id` INT( 10 ) UNSIGNED NOT NULL AUTO_INCREMENT,
`user_id` INT( 10 ) UNSIGNED NOT NULL ,
`block_ip` VARCHAR( 16 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`block_time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
PRIMARY KEY ( `id` )
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_czech_ci;
