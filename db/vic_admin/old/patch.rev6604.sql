CREATE TABLE `vic_admin`.`admin_secondary_branch` (
 `admin_id` INT(10) UNSIGNED NOT NULL,
 `branch_id` INT(10) UNSIGNED NOT NULL,
 PRIMARY KEY (`admin_id`, `branch_id`),
 CONSTRAINT `fk__admin_secondary_branch__admin_id` FOREIGN KEY (`admin_id`) REFERENCES `admin`(`admin_id`),
 CONSTRAINT `fk__admin_secondary_branch__branch_id` FOREIGN KEY (`branch_id`) REFERENCES `branch`(`id`)
) Engine=InnoDb DEFAULT CHARSET utf8 COLLATE utf8_general_ci;
