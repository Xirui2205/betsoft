INSERT INTO `vic_main`.`database_patch`(`revision`, `not_reapplicable`, `note`) VALUES
 ('H722', 1, 'Betradar time offset (mantis:907)');

ALTER TABLE `vic_main`.`udalost` ADD COLUMN `betradar_time_offset` INT(5) NULL DEFAULT NULL AFTER `betradar_udalost_id`;
ALTER TABLE `vic_main`.`typ` ADD COLUMN `betradar_time_offset` DECIMAL(4,2) NOT NULL DEFAULT 0;

UPDATE `vic_main`.`typ` SET `betradar_time_offset`=1 WHERE `typ_id` IN (
 89, 139, 140, -- 2P totals
 17, -- 2nd half
 27, -- 2P
 157, 158, 159, -- 2nd half totals
 161 -- 2P double chance
);

UPDATE `vic_main`.`typ` SET `betradar_time_offset`=2 WHERE `typ_id` IN (
 90, 141, 142, -- 3P totals
 28, -- 3P
 162 -- 3P double chance
);
