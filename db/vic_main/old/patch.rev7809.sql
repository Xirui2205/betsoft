INSERT INTO `vic_main`.`database_patch`(`revision`, `not_reapplicable`, `note`) VALUES
 ('7809', 1, 'New subtype info for complementary columns (mantis:635)');

CREATE TABLE `vic_main`.`podtyp_sloupec_complement`(
 `sloupec_id` INT(10) UNSIGNED NOT NULL,
 `complement_id` INT(10) UNSIGNED NOT NULL,
 `complement_type` ENUM('OR') NOT NULL,
 PRIMARY KEY(`sloupec_id`, `complement_id`),
 CONSTRAINT `fk__podtyp_sloupec_complement__sloupec_id` FOREIGN KEY (`sloupec_id`) REFERENCES `podtyp_sloupce`(`sloupec_id`),
 CONSTRAINT `fk__podtyp_sloupec_complement__complement_id` FOREIGN KEY (`complement_id`) REFERENCES `podtyp_sloupce`(`sloupec_id`)
) Engine=InnoDB DEFAULT CHARSET utf8 COLLATE utf8_general_ci;

START TRANSACTION;

INSERT INTO `vic_main`.`podtyp_sloupec_complement`(`sloupec_id`, `complement_id`, `complement_type`) VALUES
 (1860, 1857, 'OR'), (1860, 1858, 'OR'),
 (1861, 1857, 'OR'), (1861, 1859, 'OR'),
 (1862, 1858, 'OR'), (1862, 1859, 'OR');

COMMIT;
