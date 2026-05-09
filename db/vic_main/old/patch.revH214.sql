INSERT INTO `vic_main`.`database_patch`(`revision`, `not_reapplicable`, `note`) VALUES
 ('H214', 1, 'New table for optimized betradar statistics ID sync (mantis:761)');


CREATE TABLE vic_main.bet_br_id_sync(
 sazka_id INT(10) UNSIGNED NOT NULL PRIMARY KEY,
 betradar_statistic_id BIGINT(20) UNSIGNED NOT NULL
) Engine=InnoDb;
