ALTER TABLE `vic_admin`.`log` ADD `ip` CHAR( 39 ) NULL AFTER `id` ,
ADD `admin_id` INT UNSIGNED NULL AFTER `ip`,
ADD `host_id` INT UNSIGNED NULL AFTER `admin_id`,
ADD `user_id` INT UNSIGNED NULL AFTER `host_id`;

ALTER TABLE `vic_admin`.`log` ENGINE = MYISAM;
