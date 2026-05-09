CREATE TABLE `vic_certman`.`certman_certificate`(
 `id` INT(10) UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
 `filename` VARCHAR(128) NOT NULL,
 `description` TEXT NULL DEFAULT NULL,
 CONSTRAINT `u__certman_certificate__filename` UNIQUE(`filename`)
) Engine=InnoDb DEFAULT CHARSET utf8 COLLATE utf8_general_ci;
