CREATE DATABASE `vic_certman` DEFAULT CHARSET utf8 COLLATE utf8_general_ci;

-- credentials for testing
CREATE USER 'certman'@'localhost' IDENTIFIED BY '5A6pHJj8TMLW';
GRANT SELECT, INSERT, UPDATE, DELETE ON `vic\_certman` . * TO 'certman'@'localhost';
FLUSH PRIVILEGES;

CREATE TABLE `vic_certman`.`certman_user` (
 `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
 `username` VARCHAR(32) NOT NULL,
 `passwd` VARCHAR(128) NOT NULL COLLATE ascii_bin,
 `first_name` VARCHAR(32) NOT NULL,
 `surname` VARCHAR(32) NOT NULL,
 `email` VARCHAR(128) NOT NULL,
 `access` SMALLINT(3) NOT NULL DEFAULT 0 COMMENT '0=access granted',
 `role` ENUM ('admin', 'crew') DEFAULT 'crew',
 `created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
 `last_login` TIMESTAMP NULL DEFAULT NULL,
 CONSTRAINT `fk__certman_user__username` UNIQUE (`username`)
) Engine=InnoDB DEFAULT CHARSET utf8 COLLATE utf8_general_ci;

CREATE TABLE `vic_certman`.`certman_log` (
 `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
 `user_id` INT(10) UNSIGNED NULL DEFAULT NULL,
 `created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
 `priority` SMALLINT(3) NOT NULL,
 `certificate` VARCHAR(128) NULL DEFAULT NULL,
 `message` TEXT NOT NULL
) Engine=MyISAM DEFAULT CHARSET utf8 COLLATE utf8_general_ci;
