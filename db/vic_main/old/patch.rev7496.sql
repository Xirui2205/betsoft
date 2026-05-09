-- WARNING: ensure yourself that vic_main.udalost_betradar.betradar_udalost_id has unique values
--          values in this table can differ among partivular databases and cannot be proned automatically

INSERT INTO `vic_main`.`database_patch`(`revision`, `not_reapplicable`, `note`) VALUES
 ('7496', 1, 'Making "BetradarId-Udalost ID" relation 1:N');

ALTER TABLE `vic_main`.`udalost_betradar`
 ADD CONSTRAINT `u__udalost_betradar__betradar_udalost_id` UNIQUE INDEX (`betradar_udalost_id`);
